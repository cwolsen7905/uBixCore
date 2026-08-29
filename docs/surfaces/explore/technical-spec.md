# Explore — Technical Design Spec (TDS)

**Surface:** `explore` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · Platform [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (system design — this surface has none of its own)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) · [`pagination.md`](../../standards/pagination.md) · [`memcache-keys.md`](../../standards/memcache-keys.md)

> **How in code.** Cites the [Platform TDS](../../projects/sowing-me/platform/technical-spec.md) for anything shared and documents only deltas. Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Explore domain | `php/Ubix/` (Repositories, DTOs, DataTypes, Controllers, Services) | Featured/trending queries, taxonomy lookups, creator search |
| Explore API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Public (unauthenticated-allowed) endpoints |
| Trending job | `Console/Command/` + k8s CronJob (Platform TDS §9) | Recomputes `creators.trending_score` on a schedule |
| Explore page | `app/SowingMeJs/src/routes/explore/+page.svelte` (392 lines, existing shell — see README) | Made real: rails + filters + search wired to the API, placeholder arrays and `ui-avatars.com` images removed |

## 2. Data model (new migration)

Additive columns on the existing `creators` table (owned by `creator-profile`, S3) plus new small lookup tables and join tables owned by this surface.

### 2.1 `creators` (new columns)
| Column | Type | Notes |
|---|---|---|
| `category_id` | BIGINT FK → `categories.id` NULL | FR-23, single category |
| `denomination_id` | BIGINT FK → `denominations.id` NULL | FR-23, optional |
| `featured` | TINYINT(1) default 0 | FR-10, admin-curated |
| `featured_position` | INT NULL | explicit ordering when `featured=1` |
| `trending_score` | INT default 0 | FR-11, written only by the trending job, never by request-path code |

### 2.2 `categories`
`id, slug, name, position` — small, platform-curated (FR-22).

### 2.3 `denominations`
`id, slug, name, position` — small, platform-curated, optional per creator (FR-23).

### 2.4 `faith_topics`
`id, slug, name, position` — small, platform-curated (FR-21).

### 2.5 `creator_faith_topics` (join)
`creator_id, faith_topic_id` — many-to-many (FR-23: zero or more topics per creator), composite PK.

New enums: none required — categories/faith topics/denominations are reference-data tables, not DataType enums, because they are platform-curated content (addable via admin/CLI) rather than fixed code-level values.

## 3. API surface (`SowingMeApi`)

All routes are **public** — no session required (SRS NFR-1). Where a viewer *is* signed in, the Subscribe CTA in the response still just links through; no per-viewer personalization at read time.

### 3.1 Rails & lookups
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/explore/featured` | Featured creators, `featured_position` order | Fixed-size, no pagination (FR-12) | FR-10 |
| GET | `/explore/trending` | Trending creators, `trending_score` desc | Fixed-size, no pagination (FR-12) | FR-11 |
| GET | `/explore/categories` | Category lookup | None — small fixed lookup (`pagination.md` §1) | FR-20,22 |
| GET | `/explore/faith-topics` | Faith topic lookup | None | FR-21,22 |
| GET | `/explore/denominations` | Denomination lookup | None | FR-21,22 |

### 3.2 Search / browse
| Method | Path | Purpose | Pagination | Key FRs |
|---|---|---|---|---|
| GET | `/explore/creators?q=&category=&faithTopic=&denomination=&after=&limit=` | Search/filter creators, any combination of filters | **Cursor** (`after`, `limit`) → `{ items, nextCursor }` | FR-30,31,32 |

`q` matches against `creators.display_name`/`slug` (indexed prefix/fulltext match — exact mechanism is a repository concern, not a new table); content-body search is explicitly not implemented (FR-31).

## 4. Creator card DTO

Every rail/search response item is the same shape (FR-40):

```json
{
  "id": 123,
  "slug": "sarah-johnson",
  "displayName": "Sarah Johnson",
  "avatarUrl": "https://cdn.sowing.me/...",
  "category": { "slug": "worship", "name": "Worship Music" },
  "entryTier": { "priceMinorUnits": 500, "currency": "USD" },
  "hasFreeTier": true
}
```

`entryTier` is derived from `MIN(tiers.price)` among the creator's non-free, active tiers (Platform TDS §3 — money stored in minor units + currency, never floats); `null` if the creator has only a free tier, in which case the card shows "Free" (FR-40).

## 5. Caching (Memcached hot reads)

Per `memcache-keys.md`: `NEPTUNE_` prefix, `SCREAMING_SNAKE_CASE`, explicit TTL, read-through, cache failures non-fatal.

| Key | Holds | TTL | Invalidation |
|---|---|---|---|
| `NEPTUNE_EXPLORE_FEATURED_CREATORS` | Serialised featured rail | 5 min ± jitter | Time-based; a version-key bust (`NEPTUNE_EXPLORE_VERSION`) is deferred (`memcache-keys.md` §3) until admin curation needs sub-TTL propagation |
| `NEPTUNE_EXPLORE_TRENDING_CREATORS` | Serialised trending rail | 5 min ± jitter | Time-based; also naturally refreshed each time the trending job runs |
| `NEPTUNE_EXPLORE_CATEGORIES` / `NEPTUNE_EXPLORE_FAITH_TOPICS` / `NEPTUNE_EXPLORE_DENOMINATIONS` | Lookup lists | 24 h (webservice-result-cache convention, `memcache-keys.md` §4) | Time-based; these change rarely |

This is the accepted staleness trade-off for FR-42 ("current" price): a card can show a price up to the rail's TTL old. Search results (§3.2, cursor, per-query) are **not** cached at M2 — same measure-first stance as `supporter-feed`'s billing history.

## 6. Frontend (SvelteKit)

- `app/SowingMeJs/src/routes/explore/+page.svelte`: replace the hardcoded `recommendedCreators`/`popularCreators`/`newCreators` arrays and `ui-avatars.com` placeholder images with calls to `/explore/featured`, `/explore/trending`, and `/explore/categories`; the existing `topics` chip array becomes the `/explore/categories` response, with new chip rows for faith topics and denominations (FR-21) added alongside it, not merged into it.
- Search box wires to `/explore/creators?q=` with the shared infinite-scroll cursor component (`pagination.md` §4 frontend, same component `supporter-feed` uses).
- Creator cards render the DTO in §4; the Subscribe CTA links to `/c/{slug}` (creator-profile) with the tier picker open, routing through login/sign-up first when unauthenticated (FR-41).
- The `SowingMeAdminJs` `explore` route is **not** touched by this surface (README — it's a separate neptune-leftover concern under `admin-console`, S11).

## 7. Testing

- **Unit:** entry-tier-price derivation (creators with only a free tier, one paid tier, multiple paid tiers), category/faith-topic/denomination lookup serialization, Payload validation for the cursor search endpoint.
- **Integration:** trending job scoring against a seeded window of subscription rows; combined filter (`category` + `faithTopic` + `q`) query correctness; cursor pagination correctness (`nextCursor` null at exhaustion).
- **E2E (staging):** anonymous visitor loads `/explore`, filters by a faith topic, searches a name, clicks Subscribe, lands in sign-up then the creator's tier picker.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs incl. `DemandCanonicalPagination`), JS suite.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-10 | `creators.featured`/`featured_position`, `GET /explore/featured` |
| FR-11 | `creators.trending_score`, trending CronJob, `GET /explore/trending` |
| FR-12 | Fixed-size rail responses, no pagination fields |
| FR-20/22 | `categories` table, `GET /explore/categories` |
| FR-21 | `faith_topics`/`denominations`/`creator_faith_topics`, `GET /explore/faith-topics`, `GET /explore/denominations` |
| FR-23 | `creators.category_id`/`denomination_id`, `creator_faith_topics` join |
| FR-30/32 | `GET /explore/creators` cursor search |
| FR-31 | Explicitly no post-body search endpoint |
| FR-40/42 | Creator card DTO §4, entry-tier derivation |
| FR-41 | Frontend Subscribe CTA routing, no server change |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
