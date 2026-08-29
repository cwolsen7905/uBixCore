# Subscription Tiers — Software Requirements Specification (SRS)

**Surface:** `subscription-tiers` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M1 (build-ready) · **Prerequisites:** `creator-profile` (S3)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md) · parent [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.4 (FR-MEM)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD) and the platform TDS §12 per-surface template. The SRS says **what** and **why**; the technical-spec says **how in code**. This surface has no separate architecture doc — it inherits the platform ADS (see `README.md`).

## 1. Purpose

Let a creator define what a supporter gets for money: named tiers with a price, a billing interval, and a list of benefits, ordered so that a higher tier's supporter sees everything a lower tier's supporter sees plus more. This surface owns the tier *definition* and the `subscriptions` record shape that ties a supporter to a tier; it deliberately does **not** own the act of paying — that's the `payments` surface's job, using Stripe Checkout/Billing to create and keep the `subscriptions` row this surface defines up to date.

## 2. Scope

**In scope:** `tiers` (name, price, billing interval, benefits, description, ordering); the implicit free tier concept; the `tier_benefits` model; the `subscriptions` table (user × tier, status, provider ids — schema only); tier ordering as the gating-precedence input consumed by `EntitlementService`; creator-facing tier management (create/edit/reorder/archive); supporter-facing read of their own subscription state; upgrade/downgrade *semantics* (which tier wins, what happens to entitlement immediately).

**Out of scope (this surface, owned by `payments`):** the Checkout/Billing session that creates a `subscriptions` row's paid state, webhook handling, the `transactions` ledger, proration/refunds, dunning. This surface's write endpoints for `subscriptions` are limited to the fields it owns (see §6.4); anything that moves money is a `payments` concern referenced, not duplicated, here.

## 3. Context — parent requirements this surface realises

| Platform FR (SRS §5.4) | Statement | Realised by |
|---|---|---|
| FR-MEM-1 | Per-creator tiers: name, price, billing interval, benefits, description; an implicit free tier | §6.1, §6.2 |
| FR-MEM-2 | Supporters subscribe to exactly one active tier per creator (upgrade/downgrade supported) | §6.3, §6.4 |
| FR-MEM-3 | Tier ordering defines gating precedence (higher tier sees lower-tier content) | §6.5 |

Also feeds **FR-CONT-1** (`content-posts`' `visibility: tier` needs a tier to point at) and **FR-DISC-3** (`explore`'s "entry price" card data, M2) without either surface needing to touch this one's schema.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Tier** | A `tiers` row: one paid membership level a creator offers, with a price and an ordered position. |
| **Implicit free tier** | The zero-cost level every creator has automatically; not a `tiers` row — the absence of an active paid subscription. |
| **Benefit** | One bullet describing what a tier includes; a `tier_benefits` row, free text at M1. |
| **Ordering / position** | An integer on `tiers` that ranks tiers within a creator; higher position = higher tier = sees more. This is the sole input to gating precedence (FR-MEM-3). |
| **Subscription** | A `subscriptions` row: one supporter's membership to one creator's tier, with a lifecycle `status`. |
| **Entitlement** | `EntitlementService`'s determination that a supporter's active subscription's tier position is ≥ a resource's required tier position (defined in the platform TDS §6; this surface supplies the position comparison, not the service itself). |

## 5. Personas & primary user stories

- **Creator.** "As a creator, I create a $5/month tier and a $15/month tier, list what each includes, and put the $15 tier above the $5 tier so its members also see $5-tier content."
- **Supporter.** "As a supporter, I see a creator's tiers on their page, pick one, and after I pay (elsewhere) I see the content it unlocks. Later I upgrade and immediately see more; if I downgrade, I keep access until the current period ends."
- **Platform (EntitlementService).** "Given a supporter and a creator, tell me the highest tier position they're actively entitled to, so posts/streams/comments can gate consistently."

## 6. Functional requirements

### 6.1 Tier definition (FR-10x)
- **FR-101** A creator can create a tier: `name` (required), `price` (minor units + currency), `billing_interval` (`month`|`year`), `description` (optional long text), an ordered list of `tier_benefits` (free-text bullets).
- **FR-102** Every creator implicitly has a free tier (position 0); it is not a stored row — the frontend renders a synthetic "Free" card alongside the creator's real tiers so the two are presented uniformly, but no `tiers` row, no `subscriptions` row, and no price/benefits editing exist for it.
- **FR-103** A creator can edit, reorder, and archive (soft-hide from new subscribers) a tier; archiving does not remove existing subscribers' access.
- **FR-104** Price and currency are stored in minor units + an ISO currency code, never a float (platform TDS §3).
- **FR-105** A tier's `position` is unique per creator and strictly orders tiers; reordering renumbers positions transactionally (no gaps required, but no ties).

### 6.2 Benefits model (FR-20x)
- **FR-201** Benefits are an ordered, per-tier list of short descriptions (`tier_benefits`); a creator adds/removes/reorders them independent of editing the tier's other fields.
- **FR-202** Benefits are descriptive only at M1 (no structured "this benefit = access to X post" linkage) — gating is by tier position (§6.5), not by individual benefit.

### 6.3 Subscriptions — schema & ownership boundary (FR-30x)
- **FR-301** A `subscriptions` row associates one `user_id` with one `tier_id` (and, denormalised, its `creator_id`), a `status` (`SubscriptionStatusEnum`: `active`, `past_due`, `canceled`, `expired`), and provider identifiers (`provider_subscription_id`, `provider_customer_id`) written by the `payments` surface.
- **FR-302** A supporter has at most one non-`canceled`/non-`expired` `subscriptions` row per creator (exactly one active paid tier per creator, per FR-MEM-2); this surface enforces the uniqueness constraint even though `payments` is what inserts/updates the row during checkout.
- **FR-303** This surface exposes read-only endpoints for a supporter's own subscriptions and for a creator's subscriber list/count; it does **not** expose an endpoint that creates a `subscriptions` row with `status=active` directly — that transition only happens via the `payments` surface's webhook-driven write (cross-reference, not duplicated here).
- **FR-304** Cancelling is a `payments`-surface action (it talks to Stripe Billing); this surface only documents the resulting `status` values it must render correctly.

### 6.4 Upgrade/downgrade semantics (FR-40x)
- **FR-401** A supporter may hold only one tier per creator; switching tiers is a **replace**, not an additional subscription (FR-302's uniqueness constraint is what makes this true by construction).
- **FR-402** **Upgrade** (moving to a higher `position`): entitlement takes effect immediately once `payments` confirms the new tier's charge; the previous tier's row is superseded, not kept in parallel.
- **FR-403** **Downgrade** (moving to a lower `position`) or **cancel**: the supporter keeps their **current** tier's entitlement through the end of the paid period already purchased (`current_period_end`), then drops to the new tier/free at renewal — this surface defines the field (`current_period_end`) and the rule; `payments` is what schedules the actual transition via Stripe Billing.
- **FR-404** Tier ordering changes (a creator reordering existing tiers, FR-105) never retroactively change an existing subscriber's *access* mid-period — a subscriber's entitlement is pinned to their subscribed `tier_id`, and `position` is read fresh at each gating check, so a reorder changes future precedence comparisons only, not what a subscriber already paid for.

### 6.5 Gating precedence for `EntitlementService` (FR-50x)
- **FR-501** `EntitlementService` resolves a supporter's entitlement to a creator's gated resource by comparing the supporter's active-subscription tier's `position` against the resource's required tier's `position`; a supporter with an active subscription at position ≥ the requirement is entitled (mirrors the live-streaming surface's identical precedence rule, per platform TDS §6, so a future consumer needs no new rule).
- **FR-502** A supporter with **no** active subscription to a creator has implicit free-tier (position 0) entitlement — sufficient for `visibility: public`, insufficient for `visibility: subscribers` or `visibility: tier`.
- **FR-503** `visibility: subscribers` (any paid tier) is satisfied by any active subscription with position ≥ 1, regardless of which tier; `visibility: tier` requires position ≥ the specific tier's position.
- **FR-504** This surface supplies the position-comparison rule as a contribution to the platform's single `EntitlementService`; it does not stand up a competing gating mechanism.

## 7. Non-functional requirements

- **NFR-1 Money integrity.** Price stored in minor units + currency; never a float (platform NFR-STD, TDS §3).
- **NFR-2 Consistency.** Exactly-one-active-subscription-per-creator is a database constraint, not just an application check (defence in depth, mirrors platform NFR-SEC's server-side posture).
- **NFR-3 Performance.** Tier list and entitlement lookups are cheap, indexed reads (creator's tiers, supporter's active subscription) — no N+1 across a feed of many creators; platform NFR-PERF (p95 < 300 ms) applies.
- **NFR-4 Boundary discipline.** No code in this surface calls a payment provider directly; only `payments`' `PaymentProviderInterface` implementation writes `provider_subscription_id`/`status` transitions (platform NFR-EXT — keeps the money seam singular).
- **NFR-5 Standards.** DataType/Payload/DTO/Repository, PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner.
- **NFR-6 Pagination.** Creator subscriber lists are bounded/admin-scale → offset pagination per `pagination.md`; no infinite-scroll need here.

## 8. External interfaces (summary — detail in technical-spec)

- **Creator tier manager** (SvelteKit, under `/creator/dashboard`): create/edit/reorder/archive tiers and benefits.
- **Public tiers section** (rendered inside `creator-profile`'s `/c/[slug]` page): reads this surface's public tier list.
- **Supporter subscription view** (SvelteKit, under supporter settings): read-only list of own subscriptions/status; upgrade/downgrade/cancel controls hand off to `payments`.
- **`SowingMeApi`**: tier CRUD, subscriber list, own-subscriptions read.
- **`payments` surface**: the only writer of `subscriptions.status`/provider fields once checkout/billing exist.

## 9. Constraints & assumptions

- Depends on `creator-profile` (a tier belongs to a `creators` row).
- Assumes `payments` is a separate, later-built surface; until it ships, `subscriptions` rows can only be seeded manually (e.g. for the M1 "creator can publish, viewable by creator only, no payments" milestone per charter M1) or left empty — the free tier still works with zero paid subscribers.
- Currency is single-currency at M1 (platform FR-I18N is Future); the `currency` column exists now so multi-currency is additive later.

## 10. Acceptance criteria (surface DoD)

1. A creator creates two tiers with distinct prices and ordering; both render correctly on the public page in position order.
2. A `tiers` row cannot share a `position` with another of the same creator (constraint verified).
3. A supporter cannot hold two non-canceled `subscriptions` rows for the same creator (constraint verified).
4. `EntitlementService`'s position comparison correctly grants/denies against `public`/`subscribers`/`tier` visibility values in a unit-test matrix (FR-501..503).
5. Reordering a creator's tiers does not change the computed entitlement of an existing subscriber tested before and after the reorder (FR-404).
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Maximum tiers per creator? | Soft cap (e.g. 5) enforced in Service, not schema | §6.1 |
| Q2 | Does archiving a tier block new subscriptions but keep existing ones, or also block renewals? | Blocks new only; renewals continue until the supporter cancels/downgrades | FR-103 |
| Q3 | Free-tier "subscription" tracking — do we need a row at all for feed/notification purposes (who follows for free)? | No row at M1; revisit with `supporter-feed` (S8) | FR-102 |
| Q4 | Grace period for `past_due` before entitlement is revoked? | Owned by `payments` (dunning, FR-PAY-4); this surface just renders whatever `status` it's given | FR-301 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability". Parent platform FRs are FR-MEM-1..3 (SRS §3 above); this surface also supplies the tier-position input consumed by FR-CONT (`content-posts`, via `EntitlementService`) and FR-LIVE (`live-streaming`, which already documents the identical precedence pattern).

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
