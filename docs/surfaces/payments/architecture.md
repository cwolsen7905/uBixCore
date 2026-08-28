# Payments — Architecture & Design (SDD)

**Surface:** `payments` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`technical-spec.md`](technical-spec.md) (how in code) · platform [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md) (§6 payments subsystem, §5 security architecture, ADR-003, ADR-004)

> **How as a system, payment-specific only.** The platform ADS §6 already establishes the payments subsystem's shape (`PaymentProviderInterface` + webhook controller + ledger + Connect payouts; single signature-verification point; idempotent webhooks; commission as derived `fee` rows) and its ADRs (003: Stripe/no-card-data, 004: generic ledger). This document does **not** repeat that — it adds the Stripe integration topology, the Checkout/Billing flow diagrams, the webhook ingestion + idempotency + reconciliation pipeline, the PCI boundary, and refund/dispute handling that a surface-level ADS earns by being the money surface. Authored against the SDD role in [`web-development-delivery-framework.md`](../../standards/web-development-delivery-framework.md).

## 1. System context

```
┌ Supporter browser ─────┐                    ┌ Stripe ─────────────────────────┐
│ subscribe/tip CTA      │──HTTPS redirect───▶│ Hosted Checkout (subscription /  │
│                        │◀─return URL────────│ payment mode) · Billing engine   │
└───────────┬────────────┘                    └───────────────┬──────────────────┘
            │ session HTTPS                                    │ signed webhook (HTTPS)
            ▼                                                  ▼
   ┌─────────────────────────── SowingMeApi (Slim, k3s) ───────────────────────────┐
   │  PaymentController          WebhookController              TransactionRepo    │
   │  (create Checkout Session)  (verify sig · idempotent       (ledger writes)    │
   │                              dispatch · fee derivation)                       │
   └───────────────────────────────────┬──────────────────────────────────────────┘
                                        │ MariaDB
                                        ▼
                          transactions · subscriptions · webhook_events
```

The **PCI boundary is drawn at the browser-to-Stripe redirect**: from the moment the supporter's browser is sent to Stripe's hosted page until it returns, Sowing.me has no code in the data path that could see a card number. `SowingMeApi` only ever talks to Stripe's server-side API (create session, read objects, verify webhooks) — never a client-provided card field.

## 2. Checkout & Billing flows

### 2.1 Subscription checkout (sequence)
```
Supporter → API: POST /payments/checkout/subscription {tierId}
API: resolve tier → price; find-or-create Stripe Customer for user
API → Stripe: Checkout Session (mode=subscription, price, customer, success/cancel URLs)
Stripe → API: session { url }
API → Supporter: { checkoutUrl }
Supporter browser → Stripe hosted Checkout (card entry happens here, only here)
Stripe → Supporter browser: redirect to success URL (no payment confirmation yet — a hint only)
Stripe → API (async, out-of-band): webhook checkout.session.completed
API: verify signature → idempotency check (event_id) → activate subscriptions row
   → write transactions(subscription) + transactions(fee, parent=subscription row)
Supporter's next page load: EntitlementService sees active subscription → gated content unlocks
```
**The success-URL redirect is not a confirmation.** The UI may show an optimistic "processing" state, but entitlement and the ledger only ever change on the verified webhook — this is what makes FR-PAY-11 correct under network partitions, closed tabs, and Stripe-side delays.

### 2.2 Recurring lifecycle (Billing)
Stripe Billing owns the schedule: renewal attempts, retry cadence on failure (dunning), and prorated upgrade/downgrade math. `SowingMeApi` never computes a renewal date or a proration itself — `subscriptions.current_period_end` and `.status` are **mirrors** of what Billing reports via webhook, not independently derived.

### 2.3 One-off tip/gift checkout
Same shape as §2.1 in `mode=payment` instead of `mode=subscription`; the completing webhook writes `transactions(tip|gift)` + linked `fee` directly (no `subscriptions` row involved). `live-streaming`'s in-stream tip button (FR-70) calls `POST /payments/checkout/tip` with `relatedType=stream` — it does not open its own Stripe session or duplicate this flow.

## 3. Webhook ingestion, idempotency & reconciliation pipeline

```
Stripe ──HTTPS POST /webhooks/stripe (Stripe-Signature header)──▶ SowingMeApi
                                                                     │
                                          ┌──────────────────────────┴───────────────────────────┐
                                          │ 1. Verify signature against webhook signing secret     │
                                          │    (constant-time compare; reject before JSON parse)   │
                                          │ 2. INSERT webhook_events(event_id, type, status=received) │
                                          │    → unique-violation on event_id ⇒ ack 200, stop       │
                                          │ 3. Dispatch by event.type (technical-spec §5 table)      │
                                          │ 4. Handler writes transactions row(s) with               │
                                          │    provider_event_id = event_id (unique) — a re-entrant   │
                                          │    handler bug still can't double-write the ledger        │
                                          │ 5. Update subscriptions.status if applicable              │
                                          │ 6. webhook_events.status = processed → 200                │
                                          │    (exception ⇒ status=failed, 5xx → Stripe retries)       │
                                          └────────────────────────────────────────────────────────┘
```

**Single verification point.** Only `WebhookController` ever checks `Stripe-Signature`; no other code path is trusted to have already validated a payload (platform ADS §6). **Two-layer idempotency** (technical-spec §2.3) means a retried delivery is inert at the `webhook_events` insert, and even a handler that somehow re-runs cannot re-insert a `transactions` row for the same `provider_event_id`.

**Reconciliation.** A scheduled job (Console command + k8s CronJob, per platform TDS §9) periodically lists recent Stripe charges/invoices via the Stripe API and compares against `transactions.provider_ref`/`provider_event_id` for the same window, flagging:
- a Stripe-side charge with no corresponding ledger row (webhook missed/failed — replay it from Stripe's event log), and
- a ledger row with no matching Stripe object (should never happen; treated as a data-integrity incident).

This job does not correct data automatically — it raises an alert for admin review, because the money domain never self-heals silently.

## 4. Refund & dispute handling

```
Admin → AdminApi: POST /transactions/{id}/refund
AdminApi → Stripe: create refund on the original charge
Stripe → API (webhook): charge.refunded
API: write transactions(refund, parent_transaction_id = original) — original row untouched
Creator balance (payouts surface) recomputes from ledger, excluding refunded amount
```
Disputes are Stripe-initiated only (a cardholder disputes with their bank): `charge.dispute.created` writes a ledger flag and holds the disputed amount out of payout-eligible balance immediately, before the dispute resolves; `charge.dispute.closed` (won/lost) writes the resolving row. Both are visible in the admin console (FR-PAY-53) so a human, not an automated payout, is in the loop for anything a supporter has formally contested.

## 5. The PCI boundary, explicit

| Component | Sees card data? | Why |
|---|---|---|
| Supporter browser, pre-redirect | No | No card field ever rendered by `SowingMeJs` |
| Stripe hosted Checkout page | Yes (Stripe's PCI Level 1 scope) | By design — this is the entire point of hosted Checkout |
| `SowingMeApi` | No | Only calls Stripe server APIs with object ids/prices, never card fields |
| `transactions` / `webhook_events` tables | No | Store `provider_ref`/`provider_event_id` (opaque Stripe ids), amounts, currency — never PAN/CVV |
| Logs | No | Webhook payloads stored in `webhook_events.payload` are Stripe's event JSON, which itself never includes full card numbers (Stripe redacts to last4 + brand) |

This is why the surface has no PCI-SAQ-A-EP-style requirements to design for: SAQ A (the lightest tier) applies, because card data never transits or is stored on our infrastructure.

## 6. Failure modes

| Failure | Behaviour | Mitigation |
|---|---|---|
| Webhook delivery lost entirely (network issue before it reaches us) | No ledger row written; Stripe still has the charge | Reconciliation job (§3) detects the gap; admin can trigger a manual replay from Stripe's dashboard/API |
| Webhook handler throws mid-processing | `webhook_events.status=failed`; 5xx returned | Stripe's automatic retry redelivers; idempotency guards make replay safe |
| Duplicate webhook delivery (Stripe's own retry) | Second delivery no-ops at the `webhook_events` unique insert | By design — FR-PAY-41 |
| Stripe API unreachable when creating a Checkout Session | Supporter sees an error, no session created | No partial state — the DB write only happens on the confirming webhook, so there's nothing to roll back |
| Admin refunds a transaction, then the same charge is separately disputed | Both a `refund` and a dispute-flag row exist referencing the same parent | Reconciliation/admin view surfaces both; Stripe itself treats a disputed-then-refunded charge consistently, we just mirror it |

Fail-closed posture matches `live-streaming`'s: if a webhook can't be verified, it's rejected, not "processed cautiously."

## 7. Phasing

| Phase | Deliverable | Exit |
|---|---|---|
| **0 — Foundation** | Stripe SDK dependency added; `transactions`/`subscriptions`/`webhook_events` migrations; `PaymentProviderInterface` + `StripePaymentProvider` skeleton | Test-mode Stripe account wired; webhook signature verification works end-to-end against Stripe's CLI forwarder |
| **1 — Subscriptions** | Checkout Session creation, `checkout.session.completed`/`invoice.*`/`customer.subscription.*` handlers, fee derivation | Full subscribe → gated content flow on staging with test cards |
| **2 — Tips & gifts** | Tip/gift Checkout path, `related_type`/`related_id`, live-streaming tip integration | Tip recorded and attributed correctly, including from a live stream |
| **3 — Dunning, refunds, disputes** | Failed-charge notification, admin refund action, dispute webhook handling, reconciliation CronJob | Simulated failure/refund/dispute all reflected correctly on staging |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial architecture/SDD — Stripe topology, checkout/billing flows, webhook pipeline, PCI boundary, refund/dispute handling. |
