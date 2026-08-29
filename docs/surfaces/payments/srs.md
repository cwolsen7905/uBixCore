# Payments — Software Requirements Specification (SRS)

**Surface:** `payments` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 · **Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4), `content-posts` (S5, for gating context)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`architecture.md`](architecture.md) · platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.9 (FR-PAY)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). The SRS says **what** and **why**; the technical-spec says **how in code**; the architecture doc says **how as a system**. This surface expands platform FR-PAY (platform SRS §5.9); it does not restate the platform ADS §6 payments subsystem, only cites it.

## 1. Purpose

Let a supporter pay a creator — the transactional core of the MVP ("a supporter can pay a creator", charter §7). Covers subscription checkout, one-off tips and gifts, the `transactions` ledger every money event writes to, Stripe webhook ingestion, commission fee derivation, and failed-payment/dunning/refund/dispute handling. This is the **money domain**: precision over convenience everywhere below.

## 2. Scope

**In scope:** hosted Stripe Checkout for subscription purchase; Stripe Billing for recurring lifecycle (renewal, upgrade/downgrade, cancellation); one-off tips and gifts; the `transactions` ledger and `TransactionTypeEnum`; webhook signature verification and idempotent processing; commission `fee` row derivation at charge time; failed-payment dunning; refunds; chargeback/dispute reflection into the ledger.

**Out of scope (this surface):** Stripe Connect Express onboarding, `payout_accounts`, scheduled payouts, tax documents, earnings statements — all in [`payouts`](../payouts/README.md) (FR-FIN). Giving/tithing flows (FR-GIVE, M3+) reuse this surface's charge/ledger mechanics but are specified in their own future surface. In-stream tip **UI** (chat surfacing, per-stream attribution) belongs to [`live-streaming`](../live-streaming/README.md) FR-70/71; this surface owns the charge, the ledger write, and the `PaymentProviderInterface` call that live-streaming's tip button invokes.

## 3. Definitions

| Term | Meaning |
|---|---|
| **Transaction** | A row in the `transactions` ledger recording one money event (`TransactionTypeEnum`: `subscription`, `tip`, `gift`, `tithe`, `fee`, `payout`, `refund`). |
| **Checkout Session** | Stripe-hosted page that collects payment method and completes a subscription or one-off charge; we never see card data. |
| **Billing subscription** | Stripe Billing object driving the recurring lifecycle behind our `subscriptions` row. |
| **Webhook event** | A signed HTTPS callback from Stripe reporting a state change (charge succeeded, invoice paid, dispute created, etc.). |
| **Idempotency key** | A client-generated key on a money-mutating request, or the Stripe event id on a webhook, used to make retried processing a no-op. |
| **Fee row** | A `transactions` row of type `fee`, derived at charge time, representing platform commission. |
| **Dunning** | The retry/notify sequence Stripe Billing runs on a failed recurring charge before the subscription lapses. |

## 4. Personas & primary user stories

- **Supporter.** "As a supporter, I click Subscribe on a tier, I'm taken to a Stripe-hosted checkout, I never type my card into Sowing.me itself, and afterward I immediately see the gated content unlock."
- **Supporter (tipping).** "As a supporter, I send a one-off tip or gift to a creator without an active subscription."
- **Creator.** "As a creator, every dollar a supporter sends me shows up in my ledger promptly and accurately, including the platform's cut, so my dashboard and payout are never a surprise."
- **Platform admin.** "As an admin, I can see every transaction, understand why a charge failed or was refunded, and trust that a Stripe retry never double-records."

## 5. Functional requirements

### 5.1 Subscription checkout (FR-PAY-1x)
- **FR-PAY-10** A supporter starts a subscription by selecting a tier; the API creates a Stripe Checkout Session (subscription mode) and the client redirects to the Stripe-hosted page. No card fields are ever rendered by Sowing.me.
- **FR-PAY-11** On successful checkout, a `subscriptions` row is created/activated (`SubscriptionStatusEnum`) and a `transactions` row of type `subscription` is written once the confirming webhook (not the redirect) is processed.
- **FR-PAY-12** Checkout Session creation is idempotent per (supporter, tier, intent) so a page refresh or double-click does not create duplicate Stripe objects.
- **FR-PAY-13** Upgrade/downgrade and cancellation (FR-FEED-2) call Stripe Billing to change or end the underlying subscription; the ledger and `subscriptions.status` follow from the resulting webhook, not the API call's synchronous response.

### 5.2 Tips & gifts (FR-PAY-2x)
- **FR-PAY-20** A supporter can send a one-off tip or gift to a creator/org without needing an active subscription, via Stripe Checkout (payment mode).
- **FR-PAY-21** A tip may carry an optional message; a gift may target a specific post or campaign context via a `related_id`/`related_type` reference on the ledger row.
- **FR-PAY-22** In-stream tips (`live-streaming` FR-70) call the same tip-charge path; this surface is agnostic to the caller and does not special-case the live surface beyond the `related_type=stream` relation.

### 5.3 Transactions ledger (FR-PAY-3x)
- **FR-PAY-30** Every money event — subscription charge, tip, gift, fee, refund — writes exactly one `transactions` row using `TransactionTypeEnum`, minor-unit integer `amount`, ISO currency code, `provider_ref` (Stripe object id), and a relation to the creator/org/post/stream it belongs to.
- **FR-PAY-31** The ledger is **append-only** from the application's perspective: corrections are new rows (e.g. a `refund` row), never mutation of a prior row's amount.
- **FR-PAY-32** Stripe is the source of truth for money **movement**; the ledger is our record of **what happened and why**. Reconciliation (architecture §pipeline) detects drift between the two.
- **FR-PAY-33** Commission is a derived `fee` row written at charge time alongside the originating `subscription`/`tip`/`gift` row, referencing it (FR-FIN-2 sets the rate).

### 5.4 Webhooks (FR-PAY-4x)
- **FR-PAY-40** All Stripe webhook events arrive at a single endpoint that verifies the Stripe signature before any processing; unverified requests are rejected (401/400), never queued.
- **FR-PAY-41** Webhook processing is idempotent by Stripe event id — a redelivered event (Stripe's own retry policy resends on non-2xx) produces no duplicate ledger rows or state transitions.
- **FR-PAY-42** The webhook handler is the **only** place that transitions `subscriptions.status` and writes `transactions` rows for events it owns; no other code path writes these directly from a client-driven request.
- **FR-PAY-43** Unrecognized or not-yet-handled event types are logged and acknowledged (2xx) without side effects, so Stripe does not retry them indefinitely.

### 5.5 Dunning, refunds & disputes (FR-PAY-5x)
- **FR-PAY-50** A failed recurring charge triggers Stripe Billing's dunning sequence; each retry/notice webhook updates `subscriptions.status` (e.g. `past_due`) and the supporter is notified (`NotifierInterface`, FR-NOTIF).
- **FR-PAY-51** Exhausted dunning transitions the subscription to a lapsed/canceled status and revokes entitlement on the next `EntitlementService` check (no separate revocation job needed — entitlement is always resolved live).
- **FR-PAY-52** A refund (full or partial), whether admin-initiated or Stripe-initiated, writes a `refund` transactions row referencing the original charge and is reflected in creator balance calculations.
- **FR-PAY-53** A dispute/chargeback webhook writes a ledger event, flags the transaction, and surfaces in the admin console (FR-ADMIN-2) for review; disputed funds are held out of payout-eligible balance (payouts surface).
- **FR-PAY-54** Refund/dispute processing never deletes or mutates the original transaction row (FR-PAY-31).

## 6. Non-functional requirements

- **NFR-PAY-1 PCI.** No card data (PAN, CVV, full track data) touches our systems at any point — Checkout is hosted end-to-end. This is a hard constraint, not a target (ADR-003).
- **NFR-PAY-2 Idempotency.** Every money-mutating endpoint and the webhook endpoint are safe to retry; duplicate side effects are structurally impossible, not merely unlikely.
- **NFR-PAY-3 Integrity.** The ledger sum for a creator/org must always be independently reconstructable from `transactions` rows alone; no derived balance is stored as the sole record.
- **NFR-PAY-4 Latency.** Checkout Session creation and the tip endpoint respond within platform NFR-PERF (p95 < 300 ms excluding the Stripe API round-trip itself).
- **NFR-PAY-5 Auditability.** Every transaction, webhook receipt, and admin-initiated refund is logged with actor and reason; money movement is audit-logged per `sensitive-data-access.md`.
- **NFR-PAY-6 Currency & precision.** Money is minor units (INT) + ISO currency code everywhere — never a float, never a string with implied decimals.
- **NFR-PAY-7 Standards.** DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner.

## 7. External interfaces (summary — detail in technical-spec)

- **`SowingMeJs`**: subscribe/tip/gift call-to-actions that redirect to Stripe Checkout; post-checkout return page; subscription management calls to Billing-backed endpoints.
- **`SowingMeApi`**: checkout session creation, tip/gift endpoints, webhook receiver, ledger read endpoints for the creator dashboard.
- **`SowingMeAdminApi`**: transaction/ledger views, refund initiation, dispute review (FR-ADMIN-2).
- **Stripe**: Checkout, Billing, webhooks. No other processor in scope.
- **`live-streaming`**: calls this surface's tip endpoint; does not implement its own charge path.

## 8. Constraints & assumptions

- No Stripe dependency exists in `composer.json` yet (brief §3.4) — added as part of this surface's first slice.
- `BillingTransaction` repository and `Transaction` model are neptune carry-overs (brief §3.2); they are **repurposed** into the `transactions` ledger shape defined in platform TDS §3, not kept as separate parallel structures.
- Reuses `creators`, `tiers`, `subscriptions` (subscription-tiers surface) which must exist first.
- Stripe test-mode keys available before this surface's first slice (charter §10).

## 9. Acceptance criteria (surface DoD)

1. A supporter subscribes to a tier via Stripe test Checkout; a `subscriptions` row activates and a `transactions` row (type `subscription`) plus a linked `fee` row appear only after the webhook confirms, never before.
2. A supporter sends a tip without a subscription; a `tip` transaction is recorded and visible on the creator's dashboard.
3. A duplicated webhook delivery (same Stripe event id, redelivered) produces no duplicate ledger rows.
4. A simulated failed recurring charge drives the subscription through dunning to a lapsed status, and entitlement is revoked on the next check.
5. An admin-initiated refund writes a `refund` row and the creator's reconstructed balance reflects it; the original transaction row is unchanged.
6. A simulated dispute webhook flags the transaction and appears in the admin console; disputed funds are excluded from payout-eligible balance.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`); all schema via migrations.

## 10. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Do gifts need a distinct UX from tips at MVP, or is `gift` purely a ledger `type` value on the same flow? | Same flow, ledger-only distinction at MVP | FR-PAY-21 |
| Q2 | Multi-currency at MVP? | Single currency (USD) at MVP; currency column reserved for later (platform NFR-EXT) | schema only, no blocker |
| Q3 | Partial refunds in scope for MVP or full-refund-only? | Full refund only at MVP; partial reserved in schema | FR-PAY-52 |

## 11. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to platform FR-PAY (platform SRS §5.9) and roadmap row **S7**. Changes to any FR update the traceability table and re-version the companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
