# Giving & Tithing — Technical Design Specification (TDS)

**Surface:** `giving-tithing` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · Platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams/ledger design — this doc documents deltas only) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (inherited — see [`README.md`](README.md))

> **How in code.** Follows uBix Core patterns in [`../../architecture/complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`../../architecture/complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`../../standards/migrations.md`](../../standards/migrations.md).
>
> **The whole point of this surface (Platform ADR-004): giving/tithing flows through the existing generic `transactions` ledger's `gift`/`tithe` `TransactionTypeEnum` values. This surface adds no parallel money table.** New tables here are supporting metadata (campaigns, giving plans) that *relate to* ledger rows; they never duplicate amount/currency/status data the ledger already owns.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Giving domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers, Services) | Campaigns, giving plans, statement generation, ledger writes |
| Giving API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Gift/tithe creation, giving-plan CRUD, campaign CRUD, statements |
| Webhook handling | Reuses `payments` surface's webhook controller (Platform TDS §7) | Recurring giving-plan charge events land as `transactions` rows same as tier subscription charges |
| Giving UI | `app/SowingMeJs/` — give flow, campaign page, giving history | Presents amounts/currency, never computes gating or totals client-side |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. FKs to existing `creators`, `organizations`, `transactions`, `users`. **No table in this surface stores an amount, currency, or transaction status independent of `transactions`.**

### 2.1 `campaigns`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `owner_type` | ENUM(`creator`,`organization`) | per ADR-007 owner shape |
| `owner_id` | BIGINT | FK resolved by `owner_type` |
| `title` | VARCHAR(200) | |
| `description` | TEXT NULL | |
| `goal_amount` | INT | **minor units**, matches `transactions.amount` convention (Platform TDS §3) |
| `currency` | CHAR(3) | ISO 4217, matches `transactions.currency` |
| `starts_at` / `ends_at` | DATETIME NULL | optional time-boxing |
| `status` | ENUM(`active`,`closed`,`archived`) | FR-23 |

`goal_amount`/`currency` are the campaign's *target*, not a running total — progress (FR-21) is always a live `SUM(transactions.amount) WHERE transactions.type IN ('gift','tithe') AND transactions.related_id = campaigns.id AND transactions.related_type = 'campaign'`, never a cached column that could drift.

### 2.2 `giving_plans`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | the giver |
| `owner_type` | ENUM(`creator`,`organization`) | recurring target |
| `owner_id` | BIGINT | |
| `campaign_id` | BIGINT FK → `campaigns.id` NULL | optional attribution (FR-22) |
| `giving_type` | ENUM(`gift`,`tithe`) | giver's designation at setup (FR-12); each resulting charge's `transactions.type` is copied from this |
| `amount` | INT | minor units, the *schedule's* amount — each resulting `transactions` row carries its own `amount` copied at charge time, so a later plan edit never rewrites history |
| `currency` | CHAR(3) | |
| `interval` | ENUM(`weekly`,`monthly`,`quarterly`,`annually`) | FR-11 |
| `provider_ref` | VARCHAR(191) | Stripe Billing subscription id for the recurring schedule — **not** a row in the platform's `subscriptions` (tier) table; a giving plan is never a tier subscription |
| `status` | ENUM(`active`,`paused`,`canceled`) | FR-13 |

### 2.3 No `giving_statements` table

Giving statements (FR-30) are generated **on demand** from a repository query over `transactions` filtered by `user_id` (as giver), `type IN ('gift','tithe')`, and a date range — optionally joined to `campaigns`/`creators`/`organizations` for display. This is a deliberate non-decision: persisting a statement snapshot is deferred until/unless PQ2 resolves toward a legally-mandated receipt document that must be immutable once issued (in which case a `giving_receipts` table storing a rendered document reference, not duplicate financial data, would be added in a follow-up revision).

DataTypes: introduce `CampaignStatusEnum`, `GivingPlanIntervalEnum`, `GivingPlanStatusEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers. **`TransactionTypeEnum` already has `gift`/`tithe`** (Platform TDS §3/ADR-004) — this surface adds no new ledger enum values.

## 3. API surface (`SowingMeApi`)

All routes use existing session auth + role/ownership middleware. Payloads use DataType/Payload validation (`givingType` is a required field on every giving-creation payload — `gift` or `tithe`); responses are DTOs.

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/giving` | One-off gift/tithe: `{ ownerType, ownerId, campaignId?, givingType, amount, currency }` → delegates to `PaymentProviderInterface` checkout, writes `transactions` row on webhook confirmation | FR-10,12,22,40 |
| POST | `/giving/plans` | Create a recurring giving plan → Stripe Billing subscription via `PaymentProviderInterface` | FR-11,12 |
| GET | `/giving/plans` | List the current user's giving plans | FR-13 |
| PATCH | `/giving/plans/{id}` | Pause/resume/change amount (future charges only) | FR-13 |
| DELETE | `/giving/plans/{id}` | Cancel | FR-13 |
| POST/GET/PATCH | `/campaigns[/{id}]` | Campaign CRUD (creator/org-admin only for write) | FR-20,23 |
| GET | `/campaigns/{id}` | Public campaign page data incl. live progress query | FR-21 |
| GET | `/giving/statements?from=&to=` | On-demand statement generation for the current user | FR-30,31 |

### Webhook integration

Recurring giving-plan charge events arrive on the **existing** `payments` surface webhook endpoint (Platform TDS §7) — this surface does not stand up a second webhook receiver. The handler distinguishes a giving-plan charge from a tier-subscription charge by `provider_ref` lookup against `giving_plans` vs `subscriptions`, then writes a `transactions` row with `type` copied from the plan's `giving_type`, signature-verified and idempotent by provider event id (Platform TDS §7).

## 4. Service & entitlement additions

- **`GivingService`**: creates one-off gifts/tithes and giving plans, resolves campaign attribution, computes live campaign progress and on-demand statements — all as read/derive operations over `transactions`, never a write path that competes with the ledger.
- **No `EntitlementService` change.** Giving/tithing does not gate content; it is a money event, not an access decision (Platform TDS §6 is unaffected by this surface).
- **Commission handling**: if product enables commission on giving (SRS Q1, default: none), it is a derived `fee` row exactly as subscription commission is (Platform TDS §7) — same mechanism, no new code path.

## 5. Frontend components (`SowingMeJs`)

- Give flow (modal or page): amount entry, gift/tithe toggle, optional campaign selector, one-off vs recurring choice.
- `/campaigns/{id}` campaign page: goal, live progress bar, recent givers (respecting any anonymity preference — a future refinement).
- `/settings/giving`: manage recurring giving plans, download statements.
- Creator/org dashboard addition: giving total surfaced alongside subscription/tip earnings (Platform FR-DASH), sourced from the same ledger query pattern as [`../organizations/technical-spec.md`](../organizations/technical-spec.md) §4's consolidated giving view.

## 6. Requirement traceability

| FR | Realised by |
|---|---|
| FR-10/12 | `POST /giving`, `TransactionTypeEnum::gift`/`tithe` (Platform ADR-004, no new enum) |
| FR-11/13 | `giving_plans`, `/giving/plans*`, Stripe Billing via `PaymentProviderInterface` |
| FR-20/21/23 | `campaigns`, live `SUM(transactions...)` progress query |
| FR-22 | `giving_plans.campaign_id` / one-off `campaignId` payload field |
| FR-30/31/32 | On-demand statement query (§2.3) — no persisted statement table pending PQ2 |
| FR-40/41 | Reuse of the `payments` surface webhook + ledger write path; no new money table |

Platform trace: FR-GIVE-1 (Platform SRS §5.20), FR-FAITH-3 (§4), ADR-004.

## 7. Testing

- **Unit:** giving-plan → `transactions` type mapping, campaign progress query correctness (matrix over gift/tithe mix), statement query date-range boundaries, payload validation (`givingType` required, valid owner references).
- **Integration:** webhook handler distinguishing a giving-plan charge from a tier-subscription charge on the shared endpoint; campaign progress query against a test schema with mixed transaction types.
- **E2E (staging):** creator creates a campaign → supporter gives a one-off gift attributed to it → progress updates → supporter sets up a recurring tithe to an organization → scheduled charge produces a `type=tithe` row → supporter downloads a statement matching both.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
