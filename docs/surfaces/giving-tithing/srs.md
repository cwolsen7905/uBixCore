# Giving & Tithing — Software Requirements Specification (SRS)

**Surface:** `giving-tithing` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3+ (post-MVP) · **Prerequisites:** `payments` (S7), `payouts` (S10) — unbuilt; `organizations` ([`../organizations/README.md`](../organizations/README.md)) for org/campaign attribution — unbuilt
**Companion docs:** [`technical-spec.md`](technical-spec.md) · Platform [`srs.md`](../../projects/sowing-me/platform/srs.md) §5.20 (FR-GIVE) / §4 (FR-FAITH-3) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (no independent ADS — see [`README.md`](README.md))

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface expands **Platform FR-GIVE** (Platform SRS §5.20) and **FR-FAITH-3** (§4). It does not restate the payments seam or ledger design — see Platform TDS §7 and ADR-004.

## 1. Purpose

Let a supporter give a **recurring or one-off gift or tithe** to a creator, an organization, or a specific campaign — a monetisation flow distinct from subscribing to a membership tier. Giving is not gated content access; it is support given without expectation of a benefit tier, the platform's second faith-native differentiator (Platform SRS §2: "— → Giving & tithing (recurring + one-off, campaign goals)").

## 2. Scope

**In scope:** one-off and recurring gifts/tithes to a creator/organization/campaign; campaigns with a goal and progress; giving statements/receipts; ledger integration via the existing `transactions` table's `gift`/`tithe` types.

**Out of scope (this surface):** membership tier subscriptions (Platform FR-MEM, separate surface); in-stream tips during a live broadcast (`live-streaming` FR-70, a distinct `tip` ledger type); the org entity and its consolidated-giving rollup (see [`../organizations/`](../organizations/README.md) — this surface only produces the ledger rows the org surface aggregates); a new payments processor or checkout flow (reuses `PaymentProviderInterface`, Platform TDS §5).

## 3. Context — why giving is not a subscription

| Generic-platform assumption | Our stance |
|---|---|
| Money flow = subscribe to a tier for benefits | **Extend.** Giving/tithing is a first-class flow with no tier or benefit attached — a supporter gives because they choose to, not to unlock content (FR-GIVE-1). |
| One-off "tip" is the only non-subscription money path | **Distinguish.** A gift/tithe is different in kind from a tip: it can recur on its own schedule (independent of any tier), can target a campaign with a goal, and (pending PQ2) may carry different receipt/reporting treatment. |
| Money always attaches to a single creator | **Extend.** A gift/tithe can target a creator, an organization, or a campaign (which itself belongs to a creator or org via the same owner shape as ADR-007). |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Gift** | A `transactions` row with `type=gift` — support given without a faith-specific designation. |
| **Tithe** | A `transactions` row with `type=tithe` — the giver designates the gift as a tithe at the point of giving. Same ledger mechanics as a gift; the distinction is the giver's label and (pending PQ2) receipt treatment, not a different money path. |
| **Giving plan** | A recurring giving schedule (amount, interval, target) independent of any membership tier — distinct from a tier `subscription`. |
| **Campaign** | A time-boxed or goal-boxed giving target (e.g. "Build fund: $50,000") owned by a creator or organization, with visible progress. |
| **Giving statement** | A supporter-facing summary of their giving over a period, derived from the `transactions` ledger — not a separately stored ledger. |

## 5. Personas & primary user stories

- **Supporter.** "As a supporter, I give $50 a month to my church as a tithe, separately from any membership I hold with a creator, and I can see my giving history."
- **Creator / church admin.** "As a creator, I run a campaign toward a mission trip with a visible goal and progress bar, and I can see my giving total alongside my subscription/tip earnings."
- **Platform admin.** "As an admin, I can see gift/tithe transactions in the same ledger view as every other money event — no separate reconciliation system."

## 6. Functional requirements

### 6.1 One-off & recurring giving (realises FR-GIVE-1)
- **FR-10** A supporter can give a one-off gift or tithe to a creator, an organization, or a campaign, in minor units + currency (Platform TDS §3).
- **FR-11** A supporter can set up a **recurring giving plan** (amount, interval) to a creator/organization/campaign, independent of any tier subscription. A giving plan uses `PaymentProviderInterface`'s recurring-billing capability (Stripe Billing) the same way tier subscriptions do, but is tracked as its own schedule, not a `subscriptions` row.
- **FR-12** At the point of giving, the supporter designates the transaction as a **gift** or a **tithe** — this sets `transactions.type` accordingly (Platform ADR-004); no other mechanical difference in this surface's MVP.
- **FR-13** A supporter can view, pause, or cancel their recurring giving plans.

### 6.2 Campaigns (realises FR-GIVE-1)
- **FR-20** A creator or organization can create a **campaign**: title, description, goal amount + currency, optional end date, owned via the same owner shape as tiers/posts (ADR-007).
- **FR-21** A campaign page shows current progress toward its goal, derived live from the `transactions` ledger (sum of gift/tithe rows attributed to the campaign) — never a value that can drift from the ledger.
- **FR-22** A gift or tithe may optionally be attributed to a specific campaign at the point of giving.
- **FR-23** A creator/org can close or archive a campaign; closing does not affect already-recorded ledger rows.

### 6.3 Giving statements & receipts (realises FR-GIVE-1)
- **FR-30** A supporter can view/download a giving statement for a period (e.g. calendar year), listing their gift/tithe transactions with amounts, dates, and recipient (creator/org/campaign).
- **FR-31** Statements are derived on demand from the `transactions` ledger — there is no separate persisted "giving" record that could disagree with the ledger.
- **FR-32** Whether a tithe requires a distinct receipt format (e.g. a nonprofit-style donation receipt) for tax/legal purposes is **explicitly open** — see PQ2 (§9). The MVP statement format is the same for gifts and tithes until resolved.

### 6.4 Ledger integration (no new money table)
- **FR-40** Every gift/tithe writes exactly one `transactions` row via the existing money seam (webhook-verified, idempotent — Platform TDS §7); giving/tithing is **data** (a `type` value and optional campaign relation), not a schema change, per Platform ADR-004 and the extensibility contract (Platform ADS §9).
- **FR-41** Platform commission on gifts/tithes (if any is applied — a product decision, default: none, unlike subscription commission) is derived the same way as any other `fee` row.

## 7. Non-functional requirements

- **NFR-1 Ledger fidelity.** No campaign-progress or giving-statement figure may be stored as a value independent of the `transactions` ledger; all are derived queries (Platform ADR-004, NFR consistency).
- **NFR-2 Idempotency.** Recurring giving-plan charges and one-off gifts follow the same webhook signature verification + idempotency-key discipline as any other money-mutating endpoint (Platform TDS §4/§7).
- **NFR-3 Compliance.** Tithing/giving receipt treatment is jurisdiction-dependent and unresolved (PQ2); do not hard-code a specific jurisdiction's tax-receipt language against this spec (Platform NFR-COMPLY).
- **NFR-4 Privacy.** Giving history and statements are PII-adjacent (financial) and permission-gated + audit-logged like any other transaction data (Platform NFR-PRIV).
- **NFR-5 Standards.** DataType/Payload/DTO/Repository, PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **Giving UI** (SvelteKit, `SowingMeJs`): give-once / set-up-recurring flow, campaign page with progress, giving history + statement download.
- **`SowingMeApi`**: gift/tithe creation, giving-plan CRUD, campaign CRUD, statement generation.
- **`PaymentProviderInterface`** (Stripe): reused as-is for both one-off charges and recurring giving-plan billing (Platform TDS §5) — no new provider integration.

## 9. Constraints & assumptions

- Reuses existing entities that must exist first: `transactions` (with `gift`/`tithe` types already reserved per Platform ADR-004), `payout_accounts`, `creators`, and (for org/campaign attribution) `organizations`.
- **PQ2 (Platform SRS §8, carried forward): is tithing legally/operationally distinct from a tip/gift in our jurisdiction (receipts/statements)?** **Flagged as explicitly open and jurisdiction-dependent** — no default is adopted here beyond "same ledger mechanics, same statement format as a gift, until product/legal specifies otherwise" (FR-32). Any jurisdiction-specific nonprofit/charitable-receipt behavior is a follow-on scope addition, not part of this surface's MVP.
- No card data touches our systems; giving uses the same hosted Stripe flow as the rest of the platform (Platform FR-PAY-1).

## 10. Acceptance criteria (surface DoD)

1. A supporter gives a one-off gift to a creator; a `transactions` row with `type=gift` is created and appears in the creator's earnings alongside subscriptions/tips.
2. A supporter sets up a recurring monthly tithe to an organization; recurring charges produce `type=tithe` rows on schedule.
3. A creator creates a campaign with a $X goal; the campaign page's progress reflects the live sum of attributed gift/tithe rows.
4. A supporter downloads a giving statement for a period and it matches the sum of their `transactions` rows for that period.
5. No new money table exists anywhere in the schema for this surface — verified by migration review.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| PQ2 | Is tithing legally/operationally distinct from a tip in our jurisdiction (receipts/statements)? | Same treatment as a gift; flagged open, jurisdiction-dependent | FR-30/32, statement format |
| Q1 | Does the platform take commission on gifts/tithes, or only on tier subscriptions? | No commission on gifts/tithes at launch (default) | FR-41 |
| Q2 | Can a giving plan target a campaign that later closes mid-cycle? | Plan continues, attribution falls back to the campaign's owner (creator/org) once closed | FR-13/22/23 |

## 12. Traceability

Each FR maps to endpoints/tables in [`technical-spec.md`](technical-spec.md) §"Requirement traceability", and to Platform SRS **FR-GIVE-1** and **FR-FAITH-3**. Changes to any FR update the traceability table and re-version both docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
