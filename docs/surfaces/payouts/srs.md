# Payouts — Software Requirements Specification (SRS)

**Surface:** `payouts` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 · **Prerequisites:** `creator-profile` (S3), [`payments`](../payments/README.md) (S7 — the `transactions` ledger this surface pays out against)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.10 (FR-FIN)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). The SRS says **what** and **why**; the technical-spec says **how in code**. This surface expands platform FR-FIN (platform SRS §5.10). System design is inherited from the platform ADS §6 and the [`payments` architecture.md](../payments/architecture.md) — see this surface's [`README.md`](README.md) for why there is no separate `architecture.md`.

## 1. Purpose

Get money a creator (or organization, per ADR-007) has earned on the ledger **into their bank account**: Stripe Connect Express onboarding, the platform's commission cut, a scheduled payout run, balance/pending visibility, and the tax documentation a paid creator needs at year end. Closes the loop the [`payments`](../payments/README.md) surface opens — payments records what came in; this surface gets it out.

## 2. Scope

**In scope:** Stripe Connect Express onboarding (KYC at Stripe); `payout_accounts`; platform commission (default 10% beta, configurable) as `fee` rows (written by the payments surface, consumed here for payout math); scheduled payouts via a Console command + k8s CronJob; balance and pending-balance views for creators and orgs; tax documentation (1099-style) surfaced from Stripe; earnings statements. Serves **both** creators and organizations (ADR-007 owner polymorphism).

**Out of scope (this surface):** the checkout/webhook/ledger-write mechanics that produce the `transactions` rows this surface reads — see [`payments`](../payments/README.md). Consolidated multi-creator org payouts beyond the single-owner case land with FR-ORG (M3+); this surface's schema does not block that (owner polymorphism, ADR-007).

## 3. Definitions

| Term | Meaning |
|---|---|
| **Connect Express account** | A Stripe-managed connected account; Stripe collects KYC/identity/bank details directly from the creator via a Stripe-hosted onboarding flow. |
| **Payout account** | Our `payout_accounts` row linking a `creator`/`organization` to its Stripe Connect Express account id and status. |
| **Commission** | The platform's percentage cut of a charge, recorded as a `fee` transactions row (payments surface FR-PAY-33) at the rate in effect when the charge happened. |
| **Payout-eligible balance** | Sum of a creator/org's ledger rows not yet paid out and not held for dispute (payments surface FR-PAY-53). |
| **Payout run** | A scheduled batch operation that transfers each eligible creator/org's balance via Stripe Connect and records a `payout` transaction. |

## 4. Personas & primary user stories

- **Creator.** "As a creator, I connect my bank account once through a short Stripe flow, I can see what I've earned and what's pending, and money shows up on a predictable schedule minus the platform's cut."
- **Organization admin.** "As a church/ministry admin, our organization — not an individual creator — receives the consolidated payout for our page."
- **Platform admin.** "As an admin, I can see payout status across all creators/orgs, and understand exactly what commission was taken and why."

## 5. Functional requirements

### 5.1 Connect Express onboarding (FR-FIN-1x)
- **FR-FIN-10** A creator or org admin can start Stripe Connect Express onboarding from their dashboard; the flow is Stripe-hosted (KYC, identity, bank account entry all happen at Stripe — never on our systems).
- **FR-FIN-11** A `payout_accounts` row is created on onboarding start and updated to `enabled` only once Stripe reports the account can receive payouts (via webhook, not the redirect return).
- **FR-FIN-12** A creator/org with an incomplete or restricted Connect account sees a clear status and a link back into Stripe's hosted flow to finish/fix it; no payout is attempted for a non-enabled account.
- **FR-FIN-13** Onboarding serves both `creator` and `organization` owners identically at the API/service level (owner polymorphism, ADR-007) — the UI may present different copy, but the payout mechanics do not branch by owner type.

### 5.2 Commission (FR-FIN-2x)
- **FR-FIN-20** Platform commission defaults to **10%** in beta and is configurable (per-platform default, with room for a future per-creator override) without a code deploy.
- **FR-FIN-21** Commission is computed and recorded as a `fee` transactions row at charge time by the payments surface (FR-PAY-33); this surface reads that row, it does not recompute commission at payout time.
- **FR-FIN-22** A change to the commission rate applies only to charges from the moment of the change forward; historical `fee` rows are never retroactively altered (ledger append-only, FR-PAY-31).

### 5.3 Scheduled payouts (FR-FIN-3x)
- **FR-FIN-30** Payouts run on a schedule (e.g. weekly) via a Console command (`bin/ubix payout:run` or similar) invoked by a k8s CronJob — never inline in an HTTP request.
- **FR-FIN-31** Each run computes payout-eligible balance per creator/org (gross charges minus commission `fee` rows, minus prior payouts, excluding disputed/held amounts) and, for any account above a minimum threshold with an `enabled` Connect account, transfers the balance and writes a `payout` transactions row.
- **FR-FIN-32** A payout run is idempotent per (owner, period) — a re-run (e.g. after a crash) does not double-pay an owner already paid for that period.
- **FR-FIN-33** A payout transfer failure (e.g. Stripe-side account restriction discovered mid-run) is recorded and does not block other owners' payouts in the same run.
- **FR-FIN-34** A creator/org can view payout history (dates, amounts, status) and the current pending balance at any time; balances are always computed live from the ledger, never from a cached total alone (mirrors FR-PAY's integrity requirement).

### 5.4 Tax documentation & earnings statements (FR-FIN-4x)
- **FR-FIN-40** Tax documents (1099-K/1099-NEC-style, per Stripe's own thresholds and rules) are generated and delivered by Stripe Connect; this surface surfaces a link/status, it does not generate tax forms itself.
- **FR-FIN-41** A creator/org can download or view an earnings statement (period, gross, commission, net paid) reconstructed from the ledger for any past period.

## 6. Non-functional requirements

- **NFR-FIN-1 No KYC/bank data on our systems.** Bank account numbers, tax ID, and identity documents are collected and stored exclusively by Stripe Connect (ADR-003 extended to payouts).
- **NFR-FIN-2 Idempotency.** A payout run is safe to re-run; no owner is ever paid twice for the same period (FR-FIN-32).
- **NFR-FIN-3 Integrity.** Payout-eligible balance is always independently reconstructable from `transactions` alone (subscription/tip/gift/tithe minus fee minus prior payout, excluding disputed holds) — matches payments surface NFR-PAY-3.
- **NFR-FIN-4 Auditability.** Every payout run, transfer, and commission-rate change is logged with actor (system/admin) and audit-logged per `sensitive-data-access.md`.
- **NFR-FIN-5 Async.** Payout execution never runs inline in a web request; it is a scheduled Console command/CronJob (platform TDS §9).
- **NFR-FIN-6 Standards.** DataType/Payload/DTO/Repository, PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner.

## 7. External interfaces (summary — detail in technical-spec)

- **`SowingMeJs`**: creator/org "get paid" onboarding CTA (redirects to Stripe-hosted Connect Express), balance/pending view, payout history, earnings statement download, tax-document link.
- **`SowingMeApi`**: Connect account creation/status, balance endpoints, payout history read.
- **`UbixCli`**: scheduled payout run command.
- **`SowingMeAdminApi`**: payout oversight, run history, per-owner status (feeds FR-ADMIN-2).
- **Stripe Connect**: Express onboarding, transfers, tax document generation.

## 8. Constraints & assumptions

- Depends on the `payments` surface's `transactions` ledger and `fee`-row mechanics already existing — this surface adds no new ledger-write path beyond `payout` rows.
- Serves creators and organizations per ADR-007 (org FK on `creators` at M1, promoted to `owner_type/owner_id` when orgs land at M3+) — this surface's `payout_accounts` schema is written against that polymorphism from the start so organization payouts (FR-ORG-2) are additive, not a rework.
- Stripe Connect Express (not Standard or Custom) — creators interact with Stripe's own hosted onboarding UI, minimizing our compliance surface.
- Stripe test-mode Connect available before this surface's first slice (charter §10).

## 9. Acceptance criteria (surface DoD)

1. A creator completes Stripe Connect Express onboarding (test mode); `payout_accounts.status` becomes `enabled` only after the confirming webhook, not the redirect.
2. A creator with subscription/tip transactions accrued sees a correct pending balance (gross minus commission) before any payout has run.
3. A scheduled payout run (triggered manually in a test) transfers the correct net amount, writes a `payout` transactions row, and re-running the same period's job does not pay twice.
4. A disputed transaction's amount is excluded from payout-eligible balance until the dispute resolves.
5. An organization (not just an individual creator) can complete onboarding and receive a payout through the same mechanics.
6. An earnings statement for a past period reconstructs correctly from the ledger alone.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`); all schema via migrations.

## 10. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Payout cadence at MVP — weekly or Stripe's own default payout schedule to the connected account? | Weekly platform-triggered transfer to Connect balance; Stripe's own payout-to-bank schedule applies after that | FR-FIN-30 |
| Q2 | Minimum payout threshold? | No minimum at MVP; revisit if Stripe transfer fees make small payouts uneconomical | FR-FIN-31 |
| Q3 | Per-creator commission override needed at MVP, or platform-wide only? | Platform-wide only at MVP; schema reserves a per-owner override column | FR-FIN-20 |

## 11. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to platform FR-FIN (platform SRS §5.10) and roadmap row **S10**. Changes to any FR update the traceability table and re-version the companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
