# Payments — Technical Specification

**Surface:** `payments` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`architecture.md`](architecture.md) (system design) · platform [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§3 domain model, §7 payments & ledger design)

> **How in code.** This spec is the contract between the SRS and the implementation. It follows the uBix Core patterns in [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md). It documents only the deltas beyond the platform TDS — the layering (§1), API conventions (§4), and payments/ledger design (§7) there apply unchanged.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Payment provider seam | `php/Ubix/Service/Payment/PaymentProviderInterface` + `StripePaymentProvider` | Checkout Session creation, Billing operations, webhook signature verification, refund/dispute calls — the only code that imports the Stripe SDK |
| Ledger domain | `php/Ubix/Model/Transaction`, `Repository/TransactionRepository`, `DataTransferObject/Transaction*` | `transactions` reads/writes, options-DTO queries |
| Enums | `php/Ubix/Enum/TransactionTypeEnum`, `SubscriptionStatusEnum` + `DataType/Enum/*` wrappers | Type-safe domain scalars for ledger + subscription state |
| Payments API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/PaymentController`, `WebhookController` | Checkout/tip endpoints, webhook receiver |
| Admin | `app/SowingMeAdminApi/` → `Controller/SowingMeAdminApi/TransactionController` | Ledger views, refund initiation |
| Frontend | `app/SowingMeJs/` subscribe/tip components + `js/Ubix/` | Redirect-to-Checkout CTAs, post-checkout return page |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. FKs to existing `users`, and to `creators`/`tiers`/`subscriptions` once `creator-profile`/`subscription-tiers` land.

### 2.1 `transactions` (the ledger — platform TDS §3)
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `type` | ENUM(`subscription`,`tip`,`gift`,`tithe`,`fee`,`payout`,`refund`) | `TransactionTypeEnum` |
| `user_id` | BIGINT FK → `users.id` NULL | payer; NULL for platform-internal rows (e.g. a `fee` row references its parent, not a payer directly) |
| `creator_id` | BIGINT FK → `creators.id` NULL | payee context; NULL for org-owned money once `organizations` lands (owner polymorphism, ADR-007) |
| `related_type` | ENUM(`post`,`stream`,`campaign`) NULL | what the money was for, beyond creator/subscription |
| `related_id` | BIGINT NULL | id within `related_type` |
| `parent_transaction_id` | BIGINT FK → `transactions.id` NULL | links a `fee`/`refund` row to the charge it derives from |
| `amount` | BIGINT | **minor units**, signed (refunds negative relative to the charge they reference) |
| `currency` | CHAR(3) | ISO 4217, e.g. `usd` |
| `provider` | ENUM(`stripe`) | seam for future processors without a schema change |
| `provider_ref` | VARCHAR(255) | Stripe object id (`pi_...`, `ch_...`, `re_...`) |
| `provider_event_id` | VARCHAR(255) NULL UNIQUE | Stripe webhook event id that caused this row — the idempotency key (FR-PAY-41) |
| `message` | VARCHAR(500) NULL | optional tip/gift message (FR-PAY-21) |
| `created_at` | DATETIME | |

`provider_event_id` carries a unique index: a second webhook delivery for the same event attempting an insert fails the unique constraint, which the webhook handler treats as "already processed" (FR-PAY-41).

### 2.2 `subscriptions`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | supporter |
| `creator_id` | BIGINT FK → `creators.id` | |
| `tier_id` | BIGINT FK → `tiers.id` | |
| `status` | ENUM(`incomplete`,`active`,`past_due`,`canceled`,`unpaid`) | `SubscriptionStatusEnum`; mirrors Stripe Billing subscription statuses |
| `provider` | ENUM(`stripe`) | |
| `provider_subscription_id` | VARCHAR(255) UNIQUE | Stripe Billing subscription id |
| `provider_customer_id` | VARCHAR(255) | Stripe Customer id |
| `current_period_end` | DATETIME NULL | for display/renewal countdown |
| `canceled_at` | DATETIME NULL | |
| `created_at` / `updated_at` | DATETIME | |

### 2.3 `webhook_events` (idempotency ledger for the receiver itself)
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `provider` | ENUM(`stripe`) | |
| `event_id` | VARCHAR(255) UNIQUE | Stripe event id |
| `type` | VARCHAR(100) | e.g. `checkout.session.completed`, `invoice.paid` |
| `status` | ENUM(`received`,`processed`,`ignored`,`failed`) | |
| `payload` | JSON | raw verified payload, for replay/debugging |
| `received_at` / `processed_at` | DATETIME | |

Two independent idempotency backstops exist deliberately (`webhook_events.event_id` unique, and `transactions.provider_event_id` unique) — the first stops re-processing at the door, the second stops a duplicate ledger write even if a bug lets processing re-run (defence in depth, matches live-streaming's fail-closed posture).

DataTypes: introduce `TransactionTypeEnum`, `SubscriptionStatusEnum`, `PaymentProviderEnum`, `WebhookEventStatusEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers per the framework.

## 3. Subscription & webhook state machines

### 3.1 Subscription status
```
(checkout started) ──Stripe webhook: checkout.session.completed──► incomplete/active
active ──invoice.payment_failed──► past_due ──dunning exhausted──► canceled/unpaid
active ──customer.subscription.deleted (user/admin cancel)──► canceled
past_due ──invoice.paid (retry succeeds)──► active
```
All transitions are driven by webhooks (§5), never by the synchronous API response to a checkout/cancel call (FR-PAY-11/13).

### 3.2 Webhook processing
```
receive → verify signature → upsert webhook_events(event_id) [unique violation ⇒ already seen, ack 200, stop]
        → dispatch by event.type → write transactions row(s) [provider_event_id unique guards re-entry]
        → update subscriptions.status if applicable → mark webhook_events.status=processed → 200
        → on any handler exception: webhook_events.status=failed, return 5xx so Stripe retries
```

## 4. API surface (`SowingMeApi`)

All authenticated routes use existing session auth + role/ownership middleware. Payloads use the DataType/Payload validation system; responses are DTOs.

### 4.1 Supporter
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/payments/checkout/subscription` | Create a Stripe Checkout Session (subscription mode) for `{tierId}` → returns `{ checkoutUrl }` | FR-PAY-10,12 |
| POST | `/payments/checkout/tip` | Create a Checkout Session (payment mode) for `{creatorId, amount, currency, message?, relatedType?, relatedId?}` | FR-PAY-20,21,22 |
| POST | `/payments/subscriptions/{id}/cancel` | Cancel at period end via Stripe Billing | FR-PAY-13 |
| GET | `/payments/subscriptions` | Supporter's own subscriptions + status (feeds FR-FEED-2) | — |
| GET | `/payments/transactions` | Supporter's own transaction history | — |

### 4.2 Creator
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/creator/transactions` | Own ledger, cursor-paginated (feeds FR-DASH-1) | FR-PAY-30 |

### 4.3 Webhook (public, unauthenticated by session — authenticated by Stripe signature)
| Method | Path | Purpose |
|---|---|---|
| POST | `/webhooks/stripe` | Single ingestion point for all Stripe event types this surface handles. Verifies `Stripe-Signature`; rejects on failure (FR-PAY-40). |

### 4.4 Admin (`SowingMeAdminApi`)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/transactions` | Ledger view, offset-paginated, filterable by type/creator/date | FR-ADMIN-2 |
| POST | `/transactions/{id}/refund` | Admin-initiated refund (calls Stripe, writes `refund` row) | FR-PAY-52 |
| GET | `/disputes` | Disputed/flagged transactions | FR-PAY-53 |

## 5. Webhook event handling (Stripe event types → effect)

| Stripe event | Effect |
|---|---|
| `checkout.session.completed` (subscription mode) | Activate `subscriptions` row; write `transactions(subscription)` + linked `fee` row |
| `checkout.session.completed` (payment mode) | Write `transactions(tip\|gift)` + linked `fee` row |
| `invoice.paid` | Recurring charge succeeded; write `transactions(subscription)` + `fee`; `subscriptions.status = active` |
| `invoice.payment_failed` | `subscriptions.status = past_due`; trigger dunning notification (FR-PAY-50) |
| `customer.subscription.updated` | Sync `status`/`current_period_end` |
| `customer.subscription.deleted` | `subscriptions.status = canceled`; `canceled_at` set |
| `charge.refunded` | Write `refund` transactions row referencing the original via `parent_transaction_id` |
| `charge.dispute.created` / `.closed` | Write ledger flag row; surface in admin `disputes` (FR-PAY-53) |

Commission (`fee`) rows are computed by the handler at the moment it processes the originating charge event — not by a separate job — using the rate from `payouts` surface's commission configuration (FR-FIN-2), so a `fee` row's rate always reflects what was in effect at charge time even if the rate changes later.

## 6. Security & secrets

- Stripe secret key and webhook signing secret live in k8s Secrets / uBixVault, never in code or logs.
- `PaymentProviderInterface` is the only caller of the Stripe SDK; no controller, service, or frontend code talks to Stripe directly except the client-side redirect to the hosted Checkout URL.
- No card data (PAN/CVV) is ever received, logged, or stored by any Sowing.me system (ADR-003) — enforced structurally by using hosted Checkout exclusively; there is no card-collecting form to audit.
- Webhook endpoint verifies `Stripe-Signature` before touching the database; a failed verification is logged and rejected, never parsed as JSON first.
- Refund/dispute admin actions are audit-logged (actor, transaction, reason) per `sensitive-data-access.md`.

## 7. Testing

- **Unit:** `TransactionTypeEnum`/`SubscriptionStatusEnum` DataTypes, Payload validation, fee-derivation calculation, webhook event-type dispatch table, idempotency-guard behavior (duplicate `event_id`/`provider_event_id`). Non-container per the migration-test pattern.
- **Integration:** repository queries against a test schema; `StripePaymentProvider` against Stripe's test-mode API (or a recorded fixture); webhook signature verification with valid/invalid/replayed signatures.
- **E2E (staging):** subscribe with a Stripe test card → gated content unlocks; tip flow; simulated failed-charge → dunning → lapse; refund → ledger reflects it; simulated dispute → admin console shows it.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 8. Requirement traceability

| FR | Realised by |
|---|---|
| FR-PAY-10/11/12 | `POST /payments/checkout/subscription`, `checkout.session.completed` handler, `subscriptions` |
| FR-PAY-13 | `POST /payments/subscriptions/{id}/cancel`, `customer.subscription.*` handlers |
| FR-PAY-20/21/22 | `POST /payments/checkout/tip`, `related_type`/`related_id`, shared with `live-streaming` FR-70 |
| FR-PAY-30/31/32/33 | `transactions` schema §2.1, `parent_transaction_id`, fee derivation §5 |
| FR-PAY-40/41/42/43 | `POST /webhooks/stripe`, `webhook_events`, dispatch table §5 |
| FR-PAY-50/51 | `invoice.payment_failed` handler, `EntitlementService` live re-check |
| FR-PAY-52/53/54 | `POST /transactions/{id}/refund`, `charge.refunded`/`charge.dispute.*` handlers |

Full table maintained as the surface is sliced; each new endpoint/table cites its FR.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
