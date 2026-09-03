# Pagination Standards

**Status:** Approved
**Audience:** VS Media Development Department
**Last Updated:** 2026-06-25 (added §7 Performance & prefetching — client-side prefetch/cache first, shared-Memcache cache-aside second, no queue needed; renumbered Quick reference to §8)

This document defines how uBix Core paginates list endpoints. uBix Core sanctions **exactly two** pagination patterns, each canonical for a specific problem shape. Anything else — hand-rolled `LIMIT`/`OFFSET`, ad-hoc cursors, returning unbounded arrays from a list endpoint — is non-conforming and is rejected by machine code review (§5).

This is the uBix Core "one way to do a thing" principle applied per problem shape: there are two distinct problems (bounded browsing vs. unbounded feeds), so there are two sanctioned patterns — and exactly one sanctioned *implementation* of each.

---

## 1. Scope

Applies to every uBix Core API endpoint that returns a **collection** that can grow without bound, and to the framework seams and frontend components that consume them. A genuinely small, fixed lookup (e.g. a status enum, a department list of a dozen rows) does **not** need pagination and should not adopt either pattern — load it whole. "Could this realistically reach thousands of rows?" is the trigger.

---

## 2. The two patterns & the decision rule

| | **Offset pagination** (page-based) | **Cursor pagination** (keyset) |
|---|---|---|
| **Use when** | A bounded, browsable dataset where the user wants page numbers, jump-to-page, and a total count | An unbounded, append-only / continuously-growing feed consumed by infinite scroll, where totals and jump-to-page are meaningless |
| **Canonical consumers** | Admin tables — Users, Affiliates, Performers, all M3 list surfaces | The home activity stream; fanclub post feeds |
| **UI** | Numbered pager + "Showing X–Y of Z" | "Load more" / infinite scroll |
| **Strengths** | Total count; jump to any page | Stable under concurrent inserts; efficient at any depth |
| **Trade-offs** | Deep offsets degrade (scan-and-discard); a row inserted mid-browse shifts pages | No total count; cannot jump to an arbitrary page |

**Decision rule (human judgement — pick one before building the endpoint):**

1. Does the surface show **page numbers / a total count** and is the dataset **bounded** (admin-scale)? → **Offset.**
2. Is it an **infinite-scroll feed** of an unbounded, append-only collection where a total is meaningless? → **Cursor.**
3. Unsure? Default to **Offset** — it's correct for every admin/back-office table, which is the common case. Reach for Cursor only for genuine feeds.

MCR cannot judge whether a feed *should* be infinite-scroll — that's a design decision recorded here and in the endpoint's spec. MCR enforces that whichever you pick is implemented with the sanctioned seam (§5).

---

## 3. Offset pagination — contract

**Request.** The request payload extends `AbstractPaginatedRequestPayload`, which carries:

| Field | DataType | Rule |
|---|---|---|
| `limit` | `Limit` | Page size. Default `25`; hard-capped (e.g. `100`) so a client can't request the whole table. |
| `offset` | `Offset` | `>= 0`. Rows to skip. |
| `sort` | `SortBy` | The column key to sort by, **validated against a per-endpoint allowlist** of sortable columns (never a raw client string in SQL → no injection, no sort on un-indexed columns). |
| `order` | `SortDirection` (enum DataType) | `asc` \| `desc`. |

**Response.** The response payload extends `AbstractPaginatedResponsePayload`, which wraps the existing `PagedObjects` DTO (`{ objects, offset, total }`) and serialises to the **one** canonical wire shape:

```json
{ "items": [ … ], "limit": 25, "offset": 50, "total": 1287 }
```

The frontend derives page numbers from `offset` / `limit` / `total` (`page = offset/limit + 1`, `pages = ceil(total/limit)`). There is no `totalPages`/`totalCount` wire field — that was a pre-standard shape and is being migrated out (§6).

**Repository.** A reader `search()` / `list()` method accepts the options DTO (limit/offset/sort/order) and returns a `PagedObjects`. Raw `LIMIT` / `OFFSET` / `COUNT(*)` SQL lives **only** inside the one sanctioned paginate helper, built from the validated options — never assembled ad-hoc in a controller or from client input.

**Frontend.** The shared `DataTable` component's **server-driven mode**: the page passes `total` / `offset` / `limit` and handler callbacks for page / sort / search change; the page re-fetches on each. Identical look to the client-side mode. Small lists keep the client-side mode (load-whole + in-browser filter); only large/slow surfaces opt into server mode.

---

## 4. Cursor pagination — contract

> **Implementation status:** the contract below is **defined and sanctioned now** so feeds don't diverge, but the seam is **built when the first uBix Core-native feed surface ships** (the home stream and fanclubs are legacy iframes today). Building it with zero consumers would violate YAGNI; documenting it now prevents an ad-hoc third pattern when those surfaces are ported.

**Request.** Extends `AbstractCursorRequestPayload`:

| Field | DataType | Rule |
|---|---|---|
| `after` | `Cursor` (opaque) | Nullable. Omitted/`null` on the first page. An opaque, server-encoded token — clients must treat it as opaque and never construct or parse it. |
| `limit` | `Limit` | Page size, same `Limit` DataType + cap as offset. |

**Response.** Extends `AbstractCursorResponsePayload`, wrapping a new `CursorPage` DTO (`{ objects, nextCursor }`), serialising to:

```json
{ "items": [ … ], "nextCursor": "eyJpZCI6OTI0fQ" }
```

`nextCursor` is `null` when the feed is exhausted. No `total`, no `offset`.

**Cursor mechanics.** The cursor encodes the **keyset** of the last row returned — for a feed ordered by `(created_at DESC, id DESC)` that is `{ created_at, id }`, base64-encoded. The next query is keyset, not offset:

```sql
WHERE (created_at, id) < (:afterCreatedAt, :afterId)
ORDER BY created_at DESC, id DESC
LIMIT :limitPlusOne
```

Fetch `limit + 1` rows to determine whether a `nextCursor` exists (if the extra row came back, there's more; drop it and emit its predecessor's keyset as the cursor). This requires a composite index on the sort key + tie-breaker (e.g. `(created_at, id)`) so the keyset comparison is an index range scan, not a filesort. (Composite/covering-index guidance in `database.md` §6.3 is tracked as benchmark item SB-29.)

**Frontend.** A shared infinite-scroll list component (intersection-observer "load more"); built alongside the seam in the same slice.

---

## 5. Machine code review enforcement

The custom PHPCS sniff `Ubix.ProjectNeptune.DemandCanonicalPagination` (`php/Ubix/Sniffs/ProjectNeptune/DemandCanonicalPaginationSniff.php`) enforces that a payload exposing pagination fields does so through a sanctioned base, never a hand-rolled third shape:

- **Request payloads** (`Ubix\Payload\Request\*`) that declare a public `limit` / `offset` / `after` field **must** extend `AbstractPaginatedRequestPayload` or `AbstractCursorRequestPayload`. ✅ **enforced**
- **Response payloads** (`Ubix\Payload\Response\*`) that declare a public `items` field (the canonical collection-envelope key) **must** extend `AbstractPaginatedResponsePayload` or `AbstractCursorResponsePayload`. ✅ **enforced**

Abstract bases are exempt (they ARE the seam); concrete payloads inherit the fields rather than redeclaring them, so only a from-scratch reimplementation trips the rule. The parent is matched on its short name against both the full `Abstract*` name and the `UseAlias`-stripped alias actually written in `extends`.

**Not yet enforced (deferred):**

- **No raw `LIMIT` / `OFFSET`** string literals in repository SQL except inside the sanctioned paginate / keyset helpers — deferred until the ~20 list repositories still carrying pre-standard raw `LIMIT` clauses (plus legitimate `LIMIT 1` lookups) are migrated onto `getPage`.
- **List/search reader methods return `PagedObjects` / `CursorPage`** (not a bare unbounded `array`) — candidate for a future sniff/reflective rule.

What MCR does **not** decide: *which* pattern an endpoint should use (§2 decision rule is human judgement). It enforces only that you used a sanctioned seam and didn't introduce a third way. Run via `php bin/ubix code:review` like every other rule.

---

## 6. Rollout

- **Offset seam:** built (Phase 2) and **fully consumed by Admin Users** (Phase 2c, done) — DataTypes + `AbstractPaginatedSqlRepository::getPage` + the request/response payload bases, the `AdminUserAccount` repo/service/controller, the shared `DataTable` server-driven mode, and the URL-driven + cached + prefetched Users page (§7.1). The reference implementation for every future offset consumer.
- **Existing endpoints migrated (Phase 4, done):** `Affiliate` and `Performer` search moved off their pre-standard `{ …, totalCount, currentPage, totalPages }` shape onto the canonical `{ items, limit, offset, total }` (§3), so there is one wire shape. Their structured multi-field filter forms were kept (DataTable server mode models a single search box; multi-filter surfaces keep a tailored form).
- **Sniff (`DemandCanonicalPagination`), Phase 3, done:** the payload-base rules in §5 are now machine-enforced repo-wide (zero violations after the Phase 4 migration unblocked it). The raw-`LIMIT` rule remains deferred per §5.
- **Cursor seam:** built when the home stream or fanclubs are ported to uBix Core-native (§4).

---

## 7. Performance & prefetching

Making page-to-page navigation faster is a **synchronous, on-demand** concern — it does **not** require a message queue or background workers. (Those are only for *speculative* pre-warming, §7.3, which is out of scope for admin surfaces.) Apply in order of value:

### 7.1 Client-side prefetch + cache (default — do this first)

The biggest perceived-latency win, and it needs no backend infrastructure:

- Make the pager **URL-driven** (`?offset=…&sort=…&order=…&search=…`) so pages are shareable / bookmarkable and the framework can preload them.
- **Prefetch the next page** on idle, or on hover of "Next" (SvelteKit `preloadData`, or a background `fetch`), so the click is instant.
- **Cache visited pages** in a client-side `Map` keyed by `(offset, sort, order, search)` and serve back-navigation from it; invalidate the map whenever the sort or any filter changes.

### 7.2 Server-side cache-aside (only when a surface measurably needs it)

Populated **at request time** (cache-aside / read-through — NOT a background job):

- Cache the **`COUNT(*)` total** per filter-set with a short TTL. In offset pagination the count is usually the expensive, slow-changing half, so caching just the total lets only the cheap `LIMIT`/`OFFSET` slice run per page.
- Optionally cache the page payload per params with a short TTL.
- **Use the shared cache (Memcache via `SimpleCache`), never pod-local memory** — uBix Core runs ≥5 pods, so an in-process cache would be inconsistent across them.

### 7.3 What actually needs background jobs (and why you usually don't)

Speculative **pre-warming** (computing pages nobody has requested, on a schedule) or materialised / denormalised tables are the only cases that need a queue or worker — rarely worth it for internal admin.

### 7.4 Measure first

At admin scale (hundreds–low-thousands of rows) a `LIMIT 25` query on an indexed sort column and its `COUNT` are already fast, so the bottleneck is the round-trip, not the database. That's why **client-side prefetch (§7.1) is the high-ROI move** and server-side caching (§7.2) is reserved for a surface that profiling shows is genuinely slow. Cursor/keyset pagination (§4) is inherently more prefetch-friendly than deep offset (no rows are scanned-and-discarded to reach a page).

---

## 8. Quick reference

- Bounded admin table, page numbers, totals → **Offset** → `AbstractPaginatedRequestPayload` → `PagedObjects` → `{ items, limit, offset, total }` → `DataTable` server mode.
- Unbounded infinite-scroll feed → **Cursor** → `AbstractCursorRequestPayload` → `CursorPage` → `{ items, nextCursor }` → infinite-scroll list.
- Small fixed lookup → no pagination; load whole.
- Never hand-roll `LIMIT`/`OFFSET` or a bespoke cursor — MCR will reject it.
