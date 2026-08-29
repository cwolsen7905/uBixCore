# Supporter Feed — Software Requirements Specification (SRS)

**Surface:** `supporter-feed` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 (roadmap **S8**) · **Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4), `content-posts` (S5), `payments` (S7)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · project [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (parent FR-FEED domain, §5.8)

> Authored against the Platform SRS/TDS/ADS trio. The SRS says **what** and **why**; the technical-spec says **how in code**. This surface has no system-level topology of its own, so there is no `architecture.md` — it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md).

## 1. Purpose

Give a signed-in supporter one place to see what their subscribed creators have posted, and to manage the subscriptions and billing that make that access possible. This is the platform's "home" surface for the supporter persona — the counterpart to the creator dashboard.

## 2. Scope

**In scope:** aggregated post feed from subscribed creators, entitlement-filtered and cursor-paginated; subscription management (view active subscriptions, upgrade/downgrade tier, cancel); billing history; saved/bookmarked posts (nice-to-have).

**Out of scope (this surface):** the payment/billing mutation logic itself (owned by `payments`, S7, and `subscription-tiers`, S4 — this surface's UI and controllers delegate to them); content authoring/CRUD (owned by `content-posts`, S5); public discovery (owned by `explore`).

## 3. Context — reuse, not reinvention

The feed and subscription-management screens read and mutate entities this surface does not own:

| Concern | Owned by | This surface's role |
|---|---|---|
| Post content, visibility, `min_tier_id` | `content-posts` (S5) — `posts`/`post_media` | Reads, filters via `EntitlementService` |
| Tier definitions, pricing | `subscription-tiers` (S4) — `tiers` | Reads (to show current tier, upgrade/downgrade options) |
| Subscription lifecycle mutation (create/change/cancel with Stripe) | `payments` (S7) — `PaymentProviderInterface`, `subscriptions` | Delegates; this surface's controller is a thin caller |
| Money ledger | Platform — `transactions` | Reads (billing history) |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Feed** | The cursor-paginated, reverse-chronological list of posts from creators the signed-in supporter is subscribed to. |
| **Entitlement filter** | The `EntitlementService` check applied per post so only posts the supporter's active tier permits appear. |
| **Saved post** | A supporter-curated bookmark of a post, independent of feed position. |

## 5. Personas & primary user stories

- **Supporter.** "As a subscriber, when I sign in I want to see new posts from everyone I support in one feed, without visiting each creator's page."
- **Supporter (billing).** "As a subscriber, I want to see what I'm paying each creator, upgrade or downgrade my tier, cancel if I need to, and see my billing history."
- **Supporter (saving).** "As a subscriber, I want to bookmark a post to come back to later."

## 6. Functional requirements

Parent: Platform SRS FR-FEED-1/2/3 (§5.8). This surface expands each into buildable requirements.

### 6.1 Feed (FR-FEED-1)
- **FR-10** A signed-in supporter's home aggregates posts from every creator they hold an active subscription to (any tier, including the free tier), ordered reverse-chronologically.
- **FR-11** The feed is **cursor-paginated** infinite scroll (`docs/standards/pagination.md` §4) — no total count, no page numbers.
- **FR-12** Every post in the feed has already passed `EntitlementService.resolve(user, post)`; a post the supporter's tier does not permit (e.g. the creator raised a post to a higher tier after the supporter's subscription) never appears — entitlement is re-checked on every read, not cached into the feed row.
- **FR-13** An empty feed (no subscriptions, or all subscribed creators have no recent posts) shows a clear empty state with a path to `explore`.
- **FR-14** The feed excludes posts from creators whose subscription has lapsed (`status` no longer active) as of the read.

### 6.2 Subscription management (FR-FEED-2)
- **FR-20** A supporter can view all their subscriptions (creator, tier, price, billing interval, status, next renewal/cancellation date).
- **FR-21** A supporter can request an upgrade or downgrade to a different tier of a creator they already subscribe to; the mutation is **delegated to the `subscription-tiers` (S4) tier-change flow and the `payments` (S7) `PaymentProviderInterface`** — this surface does not talk to Stripe directly.
- **FR-22** A supporter can cancel a subscription; cancellation is delegated the same way (FR-21) and takes effect per the provider's proration/period-end rules (owned by `payments`).
- **FR-23** Subscription list is small per supporter (bounded by how many creators one person supports) and does **not** require pagination (`pagination.md` §1 — small fixed lookup); load it whole.

### 6.3 Billing history (FR-FEED-2)
- **FR-30** A supporter can view their billing history: every `transactions` ledger row where they are the paying party (subscription charges, tips, gifts), with date, creator, type, and amount.
- **FR-31** Billing history is a bounded, browsable table the supporter may want to page through and see a total for — it uses **offset pagination** (`pagination.md` §2 decision rule: bounded, table-like, not an unbounded feed), not cursor.
- **FR-32** Amounts display in the transaction's stored currency, converted from minor units for display only; the ledger itself stays minor-units (Platform TDS §3).

### 6.4 Saved / bookmarked posts (FR-FEED-3, nice-to-have)
- **FR-40** A supporter can save/unsave a post from the feed or a creator's page.
- **FR-41** A supporter can view their saved posts as a **cursor-paginated** list (unbounded over time, feed-shaped) — same entitlement re-check as FR-12 applies (a saved post the supporter no longer has access to shows as gated, not silently omitted, so the bookmark isn't lost).
- **FR-42** Saving is a nice-to-have (SRS FR-FEED-3): shippable after FR-10..FR-32 without blocking M2.

## 7. Non-functional requirements

- **NFR-1 Performance.** Feed reads target platform NFR-PERF (p95 < 300 ms excl. third-party); hot first-page reads are Memcached (`docs/standards/memcache-keys.md`) — see technical-spec §5.
- **NFR-2 Correctness of gating.** Entitlement is resolved server-side on every feed/saved-post read (FR-12/FR-41); the SPA never decides visibility (Platform TDS §6, ADS §5).
- **NFR-3 Consistency.** Subscription-management mutations never duplicate state already owned by `payments`/`subscription-tiers` — this surface reads their tables/services, it does not fork them.
- **NFR-4 Standards.** DataType/Payload/DTO/Repository, PHP-DI, Slim 4 (`complete-php-guide.md`); SvelteKit per `complete-js-guide.md`; every table via the migration runner.
- **NFR-5 Accessibility.** WCAG 2.1 AA (Platform NFR-A11Y) for the feed, subscription list, and billing table.

## 8. External interfaces (summary — detail in technical-spec)

- **Supporter home** (SvelteKit, `SowingMeJs`): infinite-scroll feed, empty state, link to `explore`.
- **Subscriptions page**: current subscriptions, upgrade/downgrade/cancel actions (delegating), billing history table, saved posts tab.
- **`SowingMeApi`**: feed read, subscription read/mutate-delegate, billing history read, saved-post CRUD.

## 9. Constraints & assumptions

- Requires `creators`, `tiers`, `subscriptions`, `posts`/`post_media`, `transactions` to already exist (Platform TDS §3) and `content-posts`/`subscription-tiers`/`payments` surfaces to be built first for their owned mutation logic.
- No new money-mutating logic is introduced by this surface — it is a read/aggregation and delegation surface.

## 10. Acceptance criteria (surface DoD)

1. A supporter subscribed to two creators sees both creators' recent posts, interleaved reverse-chronologically, in one infinite-scroll feed.
2. A post above the supporter's tier is never returned by the feed endpoint, even if the creator recently changed the post's `min_tier_id`.
3. A supporter can upgrade a tier and see the change reflected in their subscriptions list once the delegated `payments` flow confirms it.
4. A supporter can cancel a subscription and see it marked cancelled with an end date; its posts stop appearing in the feed after the effective date.
5. Billing history shows every ledger row for the supporter with correct offset pagination (`items`/`limit`/`offset`/`total`).
6. A saved post that later becomes ungated-for-this-supporter still appears in the saved list, shown as locked rather than disappearing.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Does the feed include the supporter's own creator posts if they are also a creator? | No — feed is supporter-only; a creator sees their own posts in `creator-dashboard`/`creator-library` | FR-10 |
| Q2 | Should a lapsed-subscription creator's older (already-seen) posts stay visible read-only, or disappear entirely? | Disappear (FR-14); revisit if churn-retention data suggests otherwise | FR-14 |
| Q3 | Saved posts cap per supporter? | None at M2; revisit if abuse/storage signals appear | FR-40 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **S8**. Changes to any FR update the traceability table and re-version both companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
