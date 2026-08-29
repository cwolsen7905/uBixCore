# Payouts — Technical Specification

**Surface:** `payouts` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · platform [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§3 domain model, §7 payments & ledger design) · [`payments/architecture.md`](../payments/architecture.md) (Stripe topology, webhook pipeline, PCI boundary — this surface inherits it in full)

> **How in code.** This spec is the contract between the SRS and the implementation. It follows the uBix Core patterns in [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md). It documents only the deltas beyond the platform TDS and the `payments` technical-spec — the `PaymentProviderInterface` seam, the `transactions` ledger shape, and webhook conventions there apply unchanged; this surface adds Connect-specific methods to the same interface rather than a new one.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Payment provider seam (extended) | `php/Ubix/Service/Payment/PaymentProviderInterface` — Connect methods added | Create Connect Express account/onboarding link, read account status, create transfer, fetch tax document links. Same interface the `payments` surface implements against; no second Stripe-facing interface. |
| Payout account domain | `php/Ubix/Model/PayoutAccount`, `Repository/PayoutAccountRepository`, `DataTransferObject/PayoutAccount*` | `payout_accounts` reads/writes |
| Payout run domain | `php/Ubix/Model/PayoutRun`, `Repository/PayoutRunRepository`, `Service/PayoutService` | Balance computation, run execution, idempotency |
| Enums | `php/Ubix/Enum/PayoutAccountStatusEnum`, `PayoutRunStatusEnum` + `DataType/Enum/*` wrappers | Type-safe domain scalars |
| Payouts API | `app/SowingMeApi/` → `Controller/SowingMeApi/PayoutController` | Onboarding, balance, history endpoints |
| Console command | `app/UbixCli/Console/Command/PayoutRunCommand` | Scheduled payout execution, invoked by k8s CronJob |
| Admin | `app/SowingMeAdminApi/` → `Controller/SowingMeAdminApi/PayoutController` | Payout oversight, run history |
| Frontend | `app/SowingMeJs/` creator/org dashboard components + `js/Ubix/` | Onboarding CTA, balance view, payout history, earnings statement |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. FKs to existing `creators` and (once it lands, M3+) `organizations`, following owner polymorphism (ADR-007: org FK on `creators` at M1, `owner_type`/`owner_id` when orgs land — this surface's tables are written against the same convention the platform ADS commits to, so promoting them later is the same one-time migration as everywhere else, not a payouts-specific rework).

### 2.1 `payout_accounts`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `owner_type` | ENUM(`creator`,`organization`) | serves both per ADR-007/FR-FIN-13; `organization` inert until org accounts land (M3+) |
| `owner_id` | BIGINT | FK resolved by `owner_type` (creator id or, later, organization id) |
| `provider` | ENUM(`stripe`) | |
| `provider_account_id` | VARCHAR(255) UNIQUE | Stripe Connect Express account id (`acct_...`) |
| `status` | ENUM(`pending`,`enabled`,`restricted`,`disabled`) | `PayoutAccountStatusEnum`; driven by Stripe `account.updated` webhook, mirrors platform TDS §7's "Stripe is source of truth for movement" applied to account state |
| `payouts_enabled` | TINYINT(1) | Stripe's own `payouts_enabled` flag, mirrored for a fast query without a live API call |
| `commission_bps_override` | SMALLINT NULL | reserved per-owner override (SRS Q3); NULL = use platform default |
| `created_at` / `updated_at` | DATETIME | |

### 2.2 `payout_runs`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `period_start` / `period_end` | DATETIME | the window this run covers |
| `status` | ENUM(`running`,`completed`,`completed_with_errors`) | `PayoutRunStatusEnum` |
| `started_at` / `completed_at` | DATETIME | |
| `triggered_by` | ENUM(`schedule`,`admin`) | |

### 2.3 `payout_run_items`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `payout_run_id` | BIGINT FK → `payout_runs.id` | |
| `payout_account_id` | BIGINT FK → `payout_accounts.id` | |
| `amount` | BIGINT | minor units, the net amount transferred |
| `currency` | CHAR(3) | ISO 4217 |
| `status` | ENUM(`transferred`,`skipped`,`failed`) | `skipped` = below threshold or account not enabled; `failed` = Stripe transfer error, does not block other items (FR-FIN-33) |
| `error` | VARCHAR(500) NULL | |
| `transaction_id` | BIGINT FK → `transactions.id` NULL | the `payout`-type ledger row this item produced, once transferred |

`payout_run_items` carries a **unique constraint on `(payout_account_id, payout_run_id)`** and the run itself is keyed by `(period_start, period_end)` with a unique index — together these make FR-FIN-32's idempotency structural: re-running the same period either resumes a `running` run or is rejected as already `completed`, and a given owner cannot get two items in one run.

DataTypes: introduce `PayoutAccountStatusEnum`, `PayoutRunStatusEnum`, `PayoutRunItemStatusEnum`, `OwnerTypeEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers. `OwnerTypeEnum` is the same enum the platform ADS §4 owner-polymorphism convention uses elsewhere — it is not invented fresh for this surface.

## 3. Balance computation

Payout-eligible balance for an owner, computed live from `transactions` (never from a stored running total, per NFR-FIN-3):

```
eligible_balance =
    SUM(amount WHERE type IN (subscription, tip, gift, tithe) AND creator/org = owner)
  − SUM(amount WHERE type = fee AND parent_transaction.creator/org = owner)
  − SUM(amount WHERE type = payout AND creator/org = owner)          -- already paid out
  − SUM(amount WHERE the originating transaction is currently disputed)  -- held, FR-PAY-53
```

This is a `PayoutService` query, not a cached column — matching the payments surface's ledger-integrity posture (FR-PAY-32/NFR-PAY-3) so a balance is always auditable back to individual `transactions` rows.

## 4. Payout run flow

```
CronJob → UbixCli: bin/ubix payout:run --period=<window>
PayoutRunCommand:
  1. INSERT payout_runs(period_start, period_end, status=running, triggered_by=schedule)
     -- unique (period_start, period_end) rejects a concurrent/duplicate run for the same window
  2. for each payout_account WHERE status=enabled AND payouts_enabled=1:
       balance = PayoutService.eligibleBalance(owner)
       if balance <= threshold: INSERT payout_run_items(status=skipped); continue
       try:
         transfer = PaymentProviderInterface.createTransfer(account, balance)
         INSERT transactions(type=payout, provider_ref=transfer.id, amount=-balance, ...)
         INSERT payout_run_items(status=transferred, transaction_id=...)
       catch ProviderException:
         INSERT payout_run_items(status=failed, error=...)   -- FR-FIN-33: other owners unaffected
  3. UPDATE payout_runs SET status = (any item failed ? completed_with_errors : completed)
```
`createTransfer` is a Stripe Connect transfer to the connected account's Stripe balance; Stripe's own payout-to-bank schedule (per the connected account's settings) takes it from there (SRS Q1) — this command's job ends at the transfer, matching the platform's "Stripe is source of truth for movement" principle.

## 5. API surface (`SowingMeApi`)

All authenticated routes use existing session auth + role/ownership middleware (owner-agnostic — a creator or an org admin hits the same endpoints per FR-FIN-13).

### 5.1 Creator / org
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/payouts/account` | Create Connect Express account + onboarding link → returns `{ onboardingUrl }` | FR-FIN-10 |
| GET | `/payouts/account` | Current `payout_accounts` status + `payouts_enabled` | FR-FIN-11,12 |
| GET | `/payouts/balance` | Live eligible balance (§3) | FR-FIN-34 |
| GET | `/payouts/history` | Past `payout_run_items` for this owner, cursor-paginated | FR-FIN-34 |
| GET | `/payouts/statements/{period}` | Earnings statement (gross/commission/net) for a period | FR-FIN-41 |
| GET | `/payouts/tax-documents` | Links/status of Stripe-generated tax documents | FR-FIN-40 |

### 5.2 Webhook additions (same endpoint as `payments`, `POST /webhooks/stripe`)
| Stripe event | Effect |
|---|---|
| `account.updated` | Sync `payout_accounts.status`/`payouts_enabled` from the connected account's current capabilities |
| `transfer.failed` | Mark the corresponding `payout_run_items` row `failed` if not already recorded synchronously |

No new webhook route — Connect events arrive at the same `WebhookController` the payments surface owns, dispatched by event type per its existing table.

### 5.3 Admin (`SowingMeAdminApi`)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/payout-runs` | Run history, offset-paginated | FR-ADMIN-2 |
| GET | `/payout-runs/{id}` | Run detail (all items, statuses, errors) | FR-FIN-33 |
| POST | `/payout-runs` | Manually trigger a run for a given period (admin override of schedule) | FR-FIN-30 |
| GET | `/payout-accounts` | All owners' account status | FR-ADMIN-2 |

## 6. Deployment (CronJob)

Follows the platform's async-work convention (platform TDS §9): a `PayoutRunCommand` console command wrapped in a k8s CronJob manifest alongside `SowingMeApi`'s per-env manifests, scheduled per SRS Q1's default (weekly). The job calls the same in-cluster `SowingMeApi`/shared `php/Ubix/` code path as the API — there is no separate payout service process, keeping the "modular monolith, split by exposure" posture (platform ADR-001) intact.

## 7. Security & secrets

- No bank account numbers, tax IDs, or identity documents ever reach our systems — Stripe Connect Express collects all of it directly (NFR-FIN-1, extends ADR-003).
- `provider_account_id` is an opaque Stripe id, not sensitive on its own, but `payout_accounts` rows are still access-controlled to the owning creator/org and platform admins only.
- Commission-rate configuration changes are admin-only and audit-logged (actor, old rate, new rate, effective time) per `sensitive-data-access.md`.
- Payout run execution runs in-cluster only (CronJob), never triggerable by an unauthenticated or supporter-facing endpoint; the admin manual-trigger endpoint is role-gated to `admin`.

## 8. Testing

- **Unit:** balance-computation query (matrix over subscription/tip/gift/tithe/fee/payout/disputed-hold combinations), payout-run idempotency (duplicate period rejected), per-item failure isolation, `PayoutAccountStatusEnum`/`PayoutRunStatusEnum` DataTypes. Non-container per the migration-test pattern.
- **Integration:** `PayoutRunCommand` against a test schema with seeded transactions; Connect account creation/status against Stripe test mode; webhook `account.updated`/`transfer.failed` handling.
- **E2E (staging):** creator completes Connect Express test onboarding → accrues test transactions → manually triggered payout run transfers the correct net amount → history/statement reflect it; a second run for the same period pays nothing further; an org completes the same flow.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 9. Requirement traceability

| FR | Realised by |
|---|---|
| FR-FIN-10/11/12 | `POST /payouts/account`, `account.updated` handler, `payout_accounts.status` |
| FR-FIN-13 | `owner_type`/`owner_id` on `payout_accounts`, owner-agnostic service layer |
| FR-FIN-20/21/22 | `commission_bps_override`, `fee` rows read (not recomputed) from `payments` surface |
| FR-FIN-30/31/32/33 | `PayoutRunCommand`, `payout_runs`/`payout_run_items`, unique-period + unique-item constraints |
| FR-FIN-34 | `GET /payouts/balance`, `GET /payouts/history`, §3 live computation |
| FR-FIN-40/41 | `GET /payouts/tax-documents`, `GET /payouts/statements/{period}` |

Full table maintained as the surface is sliced; each new endpoint/table cites its FR.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
