# Explore — Software Requirements Specification (SRS)

**Surface:** `explore` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 (roadmap **S12**) · **Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · project [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (parent FR-DISC domain, §5.7)

> Authored against the Platform SRS/TDS/ADS trio. The SRS says **what** and **why**; the technical-spec says **how in code**. No `architecture.md` — this surface inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md).

## 1. Purpose

Let a **visitor** — signed in or not — find creators worth supporting: featured and trending creators, browse by category or faith topic/denomination, search by name, and see enough on a creator card (entry tier price, subscribe CTA) to decide whether to click through to `/c/{slug}`. This is the platform's top-of-funnel surface; it must work for anonymous visitors.

## 2. Scope

**In scope:** public explore page (featured/trending rails), category and faith-topic/denomination browsing, creator name/category search, creator cards with entry price + subscribe CTA.

**Out of scope (this surface, explicit per Platform SRS FR-DISC-2):** full-text **content** search (searching post bodies) — creator discovery only, deferred to a later revision. Also out of scope: the creator page itself (`creator-profile`, S3) and tier purchase flow (`subscription-tiers`/`payments`) — explore links to them, it doesn't own them.

## 3. Definitions

| Term | Meaning |
|---|---|
| **Featured creator** | Admin-curated placement, independent of any algorithm. |
| **Trending creator** | Computed placement based on recent subscription growth over a rolling window. |
| **Category** | A content-genre taxonomy value (e.g. Music, Podcasts, Bible Study) — same axis as the existing explore shell's topic chips. |
| **Faith topic** | A ministry/content-focus taxonomy value distinct from category (e.g. devotional, apologetics, worship) — the faith-native discovery axis (Platform SRS §4, FR-FAITH-5 lineage). |
| **Denomination** | A church-tradition taxonomy value a creator may optionally declare (e.g. Baptist, Catholic, non-denominational). |
| **Entry tier price** | The lowest-priced paid tier a creator offers (excludes the implicit free tier), shown on the creator card. |

## 4. Personas & primary user stories

- **Visitor / Seeker.** "As someone who isn't signed in yet, I want to browse creators by what they teach and what tradition they come from, find one I like, and see clearly what it costs to join before I commit."
- **Supporter (already signed in).** "As a supporter, I want to find *more* creators to support beyond the ones I already follow."

## 5. Functional requirements

Parent: Platform SRS FR-DISC-1/2/3 (§5.7).

### 5.1 Featured & trending (FR-DISC-1)
- **FR-10** The explore page shows a **featured creators** rail: admin-curated, ordered by an explicit admin-set position, independent of any computed signal.
- **FR-11** The explore page shows a **trending creators** rail: creators with the highest new-subscription growth over a rolling window (e.g. 7 days), computed asynchronously (Platform TDS §9), not per-request.
- **FR-12** Featured and trending are each a bounded rail (a fixed page size, e.g. up to 20) shown on the explore landing page, not deep-pageable lists.

### 5.2 Categories & faith topics/denominations (FR-DISC-1)
- **FR-20** Explore exposes a fixed set of **categories** as filter chips (mirrors the existing shell's `topics` array); selecting one filters creator listings to that category.
- **FR-21** Explore separately exposes **faith topics** and **denominations** as their own filter facets, distinct from category (Platform SRS §4 — faith-native axes are first-class, not folded into the generic category list).
- **FR-22** Category, faith-topic, and denomination values are small, platform-curated lookups (not user-generated) — no pagination needed for the lookup lists themselves (`pagination.md` §1).
- **FR-23** A creator may belong to at most one category, opt into zero or more faith topics, and declare at most one denomination (denomination is optional — many creators are non-denominational or decline to state).

### 5.3 Search (FR-DISC-2)
- **FR-30** A visitor can search creators by **name** (display name / slug) and/or filter by category, faith topic, or denomination, combinable.
- **FR-31** Content search (searching post text/bodies) is explicitly **out of scope** for this surface (Platform SRS FR-DISC-2 note); a later revision may add it.
- **FR-32** Search results are an unbounded, browsable list — paginated the same way as featured/trending listings beyond the initial rail (§6, cursor).

### 5.4 Creator cards & subscribe CTA (FR-DISC-3)
- **FR-40** Every creator card (featured, trending, category browse, search result) shows: display name, avatar, category, **entry tier price** (lowest paid tier, or "Free" if the creator has no paid tier), and a **Subscribe** CTA.
- **FR-41** The Subscribe CTA on a card without a signed-in session routes to sign-up/login first (Platform SRS FR-ONB-1), then to the creator's page tier picker — it does not attempt a purchase inline on the explore page.
- **FR-42** Price is always the **current** entry tier price at read time (no stale/cached price shown as final — see technical-spec §5 for the caching/staleness trade-off actually applied).

## 6. Non-functional requirements

- **NFR-1 Public access.** Every explore endpoint works for an anonymous visitor; no auth required to browse or search (Platform SRS Persona: Visitor/Seeker).
- **NFR-2 Performance.** Explore is a hot, high-traffic entry point — reads target Platform NFR-PERF (p95 < 300 ms excl. third-party); featured/trending/category-lookup are Memcached (`memcache-keys.md`) — see technical-spec §5.
- **NFR-3 Pagination.** Listings beyond the fixed-size rails (search results, "see all" category browse) use **cursor** pagination (`pagination.md` §2 — explore is named explicitly as a canonical cursor consumer).
- **NFR-4 Standards.** DataType/Payload/DTO/Repository, PHP-DI, Slim 4 (`complete-php-guide.md`); SvelteKit per `complete-js-guide.md`; every table via the migration runner.
- **NFR-5 Accessibility.** WCAG 2.1 AA (Platform NFR-A11Y) for filter chips, search, and creator cards.

## 7. External interfaces (summary — detail in technical-spec)

- **Explore page** (SvelteKit, `SowingMeJs`, public route `/explore`): featured/trending rails, category/faith-topic/denomination filter chips, search box, creator cards.
- **`SowingMeApi`**: featured/trending reads, category/faith-topic/denomination lookups, creator search, creator-card DTO (includes entry tier price).

## 8. Constraints & assumptions

- Requires `creators` and `tiers` to exist (`creator-profile`/`subscription-tiers`). This surface's migration **adds columns to the existing `creators` table** (`featured`, `trending_score`, `category_id`, `denomination_id`) and adds new small lookup tables — it does not fork creator ownership.
- Trending computation runs as a scheduled job (Platform TDS §9 — async work, not inline in the request path).
- No payment/subscription mutation happens on this surface; the Subscribe CTA hands off to `subscription-tiers`/`payments`.

## 9. Acceptance criteria (surface DoD)

1. An anonymous visitor loads `/explore` and sees featured and trending rails without signing in.
2. Selecting a category filters the creator listing to that category; selecting a faith topic or denomination filters independently and combinably.
3. Searching a creator's name returns them even filtered by category/topic/denomination simultaneously.
4. Every creator card shows a correct current entry tier price (or "Free") and a working Subscribe CTA that routes through sign-up when the visitor isn't authenticated.
5. Search results and "see all" listings page via cursor (`items`/`nextCursor`), never offset.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 10. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Trending window length and the growth metric (raw new subs vs. % growth)? | 7-day rolling window, raw new active-subscription count | FR-11 |
| Q2 | Can a creator belong to more than one category? | No — one category (mirrors existing single-select shell); faith topics remain multi-select | FR-23 |
| Q3 | Who curates featured placements — admin console (S11) UI or a manual DB/CLI step at M2? | CLI/manual at M2 (`bin/ubix`); admin UI is an admin-console (S11) fast-follow | FR-10 |

## 11. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **S12**. Changes to any FR update the traceability table and re-version both companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
