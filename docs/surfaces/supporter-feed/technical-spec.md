# Supporter Feed — Technical Design Spec (TDS)

**Surface:** `supporter-feed` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · Platform [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (system design — this surface has none of its own)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) · [`pagination.md`](../../standards/pagination.md) · [`memcache-keys.md`](../../standards/memcache-keys.md)

> **How in code.** This spec cites the [Platform TDS](../../projects/sowing-me/platform/technical-spec.md) for anything shared (layering, apps, `EntitlementService`, envelope/error model, DataType/Payload/DTO/Repository) and documents only deltas. Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Feed domain | `php/Ubix/` (Repositories, DTOs, DataTypes, Controllers, Services) | Feed aggregation query, saved-post CRUD |
| Feed API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Feed, subscriptions (read + delegate), billing history, saved posts |
| Supporter home | `app/SowingMeJs/` route `/` (currently an 8-line stub — becomes the real feed) | Infinite-scroll feed, empty state |
| Subscriptions page | `app/SowingMeJs/` route `/settings` (existing shell, 484 lines) gains a subscriptions/billing tab, or a new `/subscriptions` route if `settings` doesn't already own account-level tabs | View/upgrade/downgrade/cancel, billing history table, saved posts |

This surface adds **no new domain entities** beyond `saved_posts` (§2) — it is primarily a read/aggregation layer over `posts`, `tiers`, `subscriptions`, `transactions` (Platform TDS §3) plus a thin delegation layer over `subscription-tiers` (S4) and `payments` (S7) mutation logic.

## 2. Data model (new migration)

### 2.1 `saved_posts`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | the supporter |
| `post_id` | BIGINT FK → `posts.id` | |
| `created_at` | DATETIME | |

Unique key `(user_id, post_id)` — saving twice is idempotent (toggle unsave rather than error). No new enum. No FK to `creators` needed — reached via `posts.creator_id`.

No other new tables. Feed, subscription list, and billing history are **queries**, not new storage, over existing tables (Platform TDS §3): `posts`/`post_media` (visibility, `min_tier_id`), `tiers`, `subscriptions` (user × tier, status), `transactions` (ledger).

## 3. API surface (`SowingMeApi`)

All routes require session auth; ownership is implicit (the authenticated user is always the subject — a supporter can only ever see/act on their own subscriptions, billing, and saved posts).

### 3.1 Feed
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/feed` | Aggregated posts from subscribed creators, entitlement-filtered | Cursor (`after`, `limit`) → `{ items, nextCursor }` | FR-10,11,12,13,14 |

Query shape (keyset per `pagination.md` §4, ordered `(posts.published_at DESC, posts.id DESC)`):
```sql
SELECT p.* FROM posts p
JOIN subscriptions s ON s.creator_id = p.creator_id AND s.user_id = :userId AND s.status = 'active'
WHERE p.visibility IN ('public','subscribers')
   OR (p.visibility = 'tier' AND s.tier_id >= p.min_tier_id)   -- resolved via EntitlementService, not raw SQL tier math
  AND (p.published_at, p.id) < (:afterPublishedAt, :afterId)
ORDER BY p.published_at DESC, p.id DESC
LIMIT :limitPlusOne
```
The tier-comparison clause above is illustrative only — actual gating is never inlined SQL tier math; every row returned by the base query is still passed through `EntitlementService.resolve(user, post)` before serialisation (FR-12), matching the live-streaming surface's reference pattern (its playback/chat/read-hook all call one resolver — Platform TDS §6).

### 3.2 Subscription management (delegating)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/subscriptions` | List the caller's subscriptions (creator, tier, price, status, renewal date) — loaded whole, no pagination (FR-23) | FR-20 |
| POST | `/subscriptions/{id}/change-tier` | Upgrade/downgrade — **delegates to `subscription-tiers` (S4) tier-change logic and `payments` (S7) `PaymentProviderInterface`**; this controller validates ownership + calls the shared service, it does not write Stripe state itself | FR-21 |
| POST | `/subscriptions/{id}/cancel` | Cancel — **delegates to `payments` (S7)** cancellation flow | FR-22 |

### 3.3 Billing history
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/billing/transactions` | The caller's `transactions` ledger rows (subscription charge, tip, gift) | Offset (`limit`, `offset`, `sort`, `order`) → `{ items, limit, offset, total }` | FR-30,31,32 |

Offset chosen per `pagination.md` §2 decision rule: bounded per-supporter dataset, table-shaped, a total/"showing X–Y of Z" is meaningful here — unlike the feed, which is an unbounded append-only stream.

### 3.4 Saved posts
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/saved-posts` | The caller's saved posts, re-checked for entitlement | Cursor → `{ items, nextCursor }` | FR-40,41 |
| POST | `/saved-posts` | Save a post (`{ postId }`) — idempotent | — | FR-40 |
| DELETE | `/saved-posts/{postId}` | Unsave | — | FR-40 |

## 4. Request/response payloads

Per Platform TDS §1/§4: Payload objects validate input, DataType-typed; reject unknown fields. Cursor endpoints extend `AbstractCursorRequestPayload`/`AbstractCursorResponsePayload`; the billing-history endpoint extends `AbstractPaginatedRequestPayload`/`AbstractPaginatedResponsePayload` (`pagination.md` §3/§5 — MCR's `DemandCanonicalPagination` sniff enforces this). No hand-rolled `LIMIT`/`OFFSET` or ad-hoc cursor.

## 5. Caching (Memcached hot reads)

Per `memcache-keys.md`: `NEPTUNE_` prefix, `SCREAMING_SNAKE_CASE`, explicit TTL, cache failures non-fatal (read-through, never write-through).

| Key | Holds | TTL | Invalidation |
|---|---|---|---|
| `NEPTUNE_FEED_FIRST_PAGE_<USER_ID>` | Serialised first page of `/feed` (most-requested page; deeper cursor pages are not cached) | 60 s ± jitter (`memcache-keys.md` §4.1) | Time-based only at M2; a version-key bust (`NEPTUNE_FEED_VERSION`) is deferred until a surface needs sub-TTL propagation (e.g. "see my new post immediately") |
| `NEPTUNE_SUBSCRIPTIONS_LIST_<USER_ID>` | The small FR-23 subscriptions list | 5 min | Busted on any subscription mutation (change-tier/cancel) by direct delete-on-write, since those are low-frequency, high-value-of-freshness writes |

`<USER_ID>` is a numeric id — safe to interpolate directly (`memcache-keys.md` §2.6). Billing history (offset, per `pagination.md` §7.2) is **not** cached at M2 — admin-scale query cost guidance applies (measure first); revisit only if profiling shows it's slow.

## 6. Frontend (SvelteKit)

- **Supporter home** (`app/SowingMeJs/src/routes/+page.svelte`, currently a stub): infinite-scroll list using the shared cursor list component (Platform TDS §10, `pagination.md` §4 frontend note), intersection-observer "load more", entitlement-aware post cards (gated posts never leak content client-side — the API simply omits them).
- **Subscriptions/billing**: a tab or route reusing `Sidebar`/theme system; subscription cards with upgrade/downgrade/cancel actions calling the delegating endpoints (§3.2); billing history uses the shared `DataTable` server-driven mode (`pagination.md` §3 frontend) since it's offset-paginated.
- **Saved posts**: a tab in the same area using the shared infinite-scroll component (§3.4), same as the main feed.
- Auth via session cookie; entitlement is server-enforced — the SPA renders paywalls/locks, never decides access (Platform TDS §10).

## 7. Testing

- **Unit:** feed query row-to-`EntitlementService` filtering (matrix over visibility × subscription state, reusing the live-streaming surface's resolver-matrix pattern), saved-post toggle idempotency, Payload validation for both pagination bases.
- **Integration:** `/feed` cursor correctness (keyset ordering, `nextCursor` null at exhaustion), `/billing/transactions` offset correctness (`items`/`limit`/`offset`/`total`), delegation calls to `subscription-tiers`/`payments` services (mocked at the seam, not re-tested here — those surfaces own their own test suites).
- **E2E (staging):** subscribe to two creators → see interleaved feed → creator raises a post's tier → post drops out of feed → cancel a subscription → creator's posts stop appearing after the effective date.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs incl. `DemandCanonicalPagination`), JS suite.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-10/11 | `GET /feed`, cursor query §3.1, infinite-scroll home |
| FR-12/14 | `EntitlementService.resolve` per row (Platform TDS §6), `subscriptions.status = active` join |
| FR-13 | Empty-state UI, no server change |
| FR-20 | `GET /subscriptions` |
| FR-21 | `POST /subscriptions/{id}/change-tier` → delegates to `subscription-tiers`/`payments` |
| FR-22 | `POST /subscriptions/{id}/cancel` → delegates to `payments` |
| FR-23 | Unpaginated `GET /subscriptions` (small fixed lookup, `pagination.md` §1) |
| FR-30/32 | `GET /billing/transactions` reading `transactions`, minor-units display formatting only |
| FR-31 | Offset pagination per `pagination.md` §2/§3 |
| FR-40/41/42 | `saved_posts` table, `GET/POST/DELETE /saved-posts` |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
