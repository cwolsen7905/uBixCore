# Creator Dashboard — Technical Design Spec (TDS)

**Surface:** `creator-dashboard` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · Platform [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (system design — this surface has none of its own)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) · [`pagination.md`](../../standards/pagination.md) · [`memcache-keys.md`](../../standards/memcache-keys.md)

> **How in code.** Cites the [Platform TDS](../../projects/sowing-me/platform/technical-spec.md) for anything shared and documents only deltas. Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Dashboard domain | `php/Ubix/` (Repositories, DTOs, DataTypes, Controllers, Services) | Earnings aggregation, subscriber/churn queries, post-metrics aggregation, payout-status read |
| View-count aggregation job | `Console/Command/` + k8s CronJob (Platform TDS §9) | Rolls raw `post_view_events` into denormalised `posts.view_count`/`posts.unique_viewer_count` |
| Dashboard API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Creator-only (auth + ownership), reads across `transactions`/`subscriptions`/`payout_accounts`/`posts`/`live_streams` |
| Creator dashboard page | `app/SowingMeJs/src/routes/creator/dashboard/+page.svelte` (448 lines, existing shell) | Made real: earnings/subscriber/churn/payout tiles replace placeholder `setupSteps` and stub handlers |
| Creator library page | `app/SowingMeJs/src/routes/creator/library/+page.svelte` (298 lines, existing shell) | Gains a performance overlay/column on the post list already owned by `content-posts` |

## 2. Data model (new migration)

This surface owns **no** money or subscription tables — it reads `transactions`, `subscriptions`, `payout_accounts` (Platform TDS §3, `payouts` S10) as-is. It adds the minimal new storage needed for post performance, since no view-tracking exists anywhere in the platform yet (SRS §9).

### 2.1 `post_view_events`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `post_id` | BIGINT FK → `posts.id` | |
| `user_id` | BIGINT FK → `users.id` NULL | null for anonymous views of public posts |
| `viewed_at` | DATETIME | |

Append-only, high-volume, short-lived (aggregated then eligible for pruning — pruning policy is an ops task, not specified here). Raw events are written on every entitled read of a post (fire-and-forget; a write failure here must never block the content read).

### 2.2 `posts` (new columns, additive to `content-posts`, S5)
| Column | Type | Notes |
|---|---|---|
| `view_count` | INT default 0 | denormalised raw view count, updated by the aggregation job (FR-30) |
| `unique_viewer_count` | INT default 0 | denormalised deduplicated count, updated by the aggregation job |

No new enums. `live_streams` performance data (FR-31) is read from the live-streaming surface's own tables (`live_streams.peak_viewers`/`unique_viewers`, live-streaming TDS §2.1) once that surface ships — no schema here depends on it.

## 3. API surface (`SowingMeApi`)

All routes require session auth + **ownership** middleware (Platform TDS §6) — a creator only ever sees their own figures.

### 3.1 Earnings
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/creator/dashboard/earnings?period=` | Summary: total + per-type (subscription/tip/gift), gross and net-of-commission | None — a single aggregate object | FR-10,12 |
| GET | `/creator/dashboard/transactions?period=&limit=&offset=&sort=&order=` | Detailed ledger rows for this creator | **Offset** → `{ items, limit, offset, total }` | FR-11 |

Offset chosen per `pagination.md` §2 decision rule: a bounded, table-shaped, creator-scoped ledger view where a total/page-number is meaningful — the same reasoning `supporter-feed`'s billing history uses on the supporter side of the same ledger.

### 3.2 Subscribers & churn
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/creator/dashboard/subscribers?limit=&offset=&sort=&order=&search=` | Subscriber list (supporter name, tier, since, status) | **Offset** (admin-table-shaped) | FR-20,22 |
| GET | `/creator/dashboard/churn?period=` | Churn rate for the period | None — a single aggregate object | FR-21 |

### 3.3 Post performance
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/creator/dashboard/posts/performance?limit=&offset=&sort=&order=` | Per-post `view_count`/`unique_viewer_count`, joined to `content-posts`' post list | **Offset** (bounded, sortable table) | FR-30 |

### 3.4 Stream performance (conditional — FR-31/32)
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/creator/dashboard/streams/performance?limit=&offset=&sort=&order=` | Per-stream metrics, delegating the read to `live-streaming`'s analytics endpoint (`GET /creator/streams/{id}/analytics`, live-streaming TDS §4.1) | **Offset** | FR-31 |

Until `live-streaming` ships, this endpoint returns `{ items: [], limit, offset, total: 0 }` — a valid empty offset page, not a 404 or feature-flag error — so the frontend's empty state (FR-32) is just "zero rows," no special-casing.

### 3.5 Payout status
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/creator/dashboard/payout-status` | `payout_accounts.status` + most recent/pending `payout`-type `transactions` rows | FR-40,41 |

## 4. Aggregation logic

- **Earnings summary (FR-10/12):** `SUM(transactions.amount)` grouped by `type IN ('subscription','tip','gift')` for the period, plus `SUM` of `type='fee'` rows in the same period as the commission deduction; net = gross − fee. Money stays in minor units through the aggregation; conversion to a display string happens only at serialization (Platform TDS §3).
- **Churn (FR-21):** `cancelled_in_period / active_at_period_start`, both counts read from `subscriptions` (start-of-period snapshot via `status` + timestamp columns already on that table — no new snapshot table needed at M2 scale).
- **Post views (FR-30):** raw inserts to `post_view_events` on read; the aggregation CronJob (Platform TDS §9) periodically rolls counts into `posts.view_count` (every row) and `posts.unique_viewer_count` (`COUNT(DISTINCT user_id)`, treating anonymous `user_id IS NULL` rows as always-unique-per-event). This keeps the hot content-read path fast (fire-and-forget insert) and keeps expensive `DISTINCT` counting off the request path.

## 5. Caching (Memcached hot reads)

Per `memcache-keys.md`: `NEPTUNE_` prefix, `SCREAMING_SNAKE_CASE`, explicit TTL, read-through, cache failures non-fatal.

| Key | Holds | TTL | Invalidation |
|---|---|---|---|
| `NEPTUNE_DASHBOARD_EARNINGS_SUMMARY_<CREATOR_ID>_<PERIOD>` | Earnings summary aggregate | 5 min ± jitter | Time-based; earnings are inherently a few minutes stale between webhook and cache anyway |
| `NEPTUNE_DASHBOARD_SUBSCRIBER_COUNT_<CREATOR_ID>` | Active subscriber count only (not the full list) | 5 min ± jitter | Time-based |

`<CREATOR_ID>` is numeric, safe to interpolate directly; `<PERIOD>` is a controlled enum value (e.g. `THIS_MONTH`, `ALL_TIME`), also safe (`memcache-keys.md` §2.6 — never interpolate a raw external value). Offset-paginated tables (transactions detail, subscriber list, post/stream performance) follow `pagination.md` §7's client-side-prefetch-first guidance and are **not** server-cached at M2.

## 6. Frontend (SvelteKit)

- `app/SowingMeJs/src/routes/creator/dashboard/+page.svelte`: replaces the placeholder `setupSteps` checklist and `console.log` stub handlers (`handleEditPage`, `handleViewPage`, `handlePageDetails`, `handleSharePage`) with real earnings-summary tiles, subscriber/churn tiles, and a payout-status card; existing `nav-tabs` (`Home`/`Collections`/`Shop`/`Membership`/`Recommendations`) are reused as the tab shell, with dashboard content living under the relevant tab(s) rather than a new nav pattern.
- `app/SowingMeJs/src/routes/creator/library/+page.svelte`: the existing `Posts`/`Collections`/`Drafts` tabs (owned by `content-posts`) gain a performance column/overlay (view count) sourced from `/creator/dashboard/posts/performance`; the `handleCreatePost`/`handleSearch`/`toggleFilter` stubs remain `content-posts`' responsibility to wire up — this surface only adds the metrics column.
- Offset-paginated tables (`/creator/dashboard/transactions`, `/subscribers`, `/posts/performance`, `/streams/performance`) use the shared `DataTable` server-driven mode (`pagination.md` §3 frontend), matching `supporter-feed`'s billing history and `explore`'s admin-table conventions elsewhere in the platform.
- Stream-performance tile/table renders its documented empty state (FR-32) when the endpoint returns zero rows — no separate "not available yet" branch in the frontend, keeping the UI forward-compatible with M3.

## 7. Testing

- **Unit:** earnings aggregation (gross/net/fee math over a seeded `transactions` set), churn computation, view-count aggregation job (raw events → denormalised columns), Payload validation for the offset endpoints.
- **Integration:** `/creator/dashboard/*` ownership middleware (a creator cannot read another creator's dashboard), offset pagination correctness (`items`/`limit`/`offset`/`total`) across all four table endpoints, empty-page behaviour of `/streams/performance` pre-M3.
- **E2E (staging):** creator with seeded subscriptions/tips/gifts/posts sees correct earnings, subscriber count, churn, post view counts, and payout status matching a stubbed `payout_accounts` row.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs incl. `DemandCanonicalPagination`), JS suite.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-10/12 | `GET /creator/dashboard/earnings`, ledger aggregation §4 |
| FR-11 | `GET /creator/dashboard/transactions`, offset pagination |
| FR-20/22 | `GET /creator/dashboard/subscribers`, reads `subscriptions` as-is |
| FR-21 | `GET /creator/dashboard/churn`, churn formula §4 |
| FR-30 | `post_view_events`, aggregation CronJob, `posts.view_count`/`unique_viewer_count`, `GET /creator/dashboard/posts/performance` |
| FR-31 | `GET /creator/dashboard/streams/performance` delegating to live-streaming TDS §4.1 |
| FR-32 | Empty offset page contract §3.4, frontend empty-state reuse |
| FR-40/41 | `GET /creator/dashboard/payout-status`, reads `payout_accounts` + `transactions(type=payout)` |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
