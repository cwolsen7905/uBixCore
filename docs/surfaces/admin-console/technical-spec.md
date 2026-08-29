# Admin Console — Technical Specification

**Surface:** `admin-console` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams — this spec documents only deltas) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)

> **How in code.** Follows [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType/Payload/DTO/Repository), [`complete-js-guide.md`](../../architecture/complete-js-guide.md), and [`pagination.md`](../../standards/pagination.md) (offset, exclusively — this is the canonical admin-table surface pagination.md §2 points to). Every new table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Admin API app | `app/SowingMeAdminApi/` (Slim 4, scaffolded — `Routes.php`, `Dependencies.php`, `Middleware.php` exist) | Wires controllers below; today only stub routes exist |
| Admin controllers | `php/Ubix/Controller/InternalAdminApi/*` (**to be built** — referenced by `Routes.php` but absent) | `AuthController`, `AffiliateController` (both stubs today), plus new `UserAdminController`, `CreatorAdminController`, `OrganizationAdminController`, `TransactionAdminController`, `PayoutAdminController`, `DisputeAdminController`, `ReportQueueController` |
| Admin services | `php/Ubix/Service/*` | `AdminDirectoryService` (status changes + directory reads), `LedgerReadService`, `PayoutOversightService`, `DisputeService` — orchestrate repositories, never SQL directly |
| Repositories | `php/Ubix/Repository/*` | Read (and status-write) repositories over `users`, `creators`, `organizations`, `transactions`, `payout_accounts`, `affiliates`, `attribution_logs`, `reports` — options-DTO pattern, offset-paginated `search()`/`list()` per `pagination.md` §3 |
| Audit writer | `php/Ubix/Service/PiiAccessAudit` (shared seam, `sensitive-data-access.md`) + `AuditLogService` (writes `audit_logs` for status changes / money actions) | Single insert path for both audit trails; admin controllers call it, never hand-roll `INSERT` |
| Admin SPA | `app/SowingMeAdminJs/` (SvelteKit shell exists: `login`, `settings`, `explore`, `affiliates`, `broadcasting/models`) | Directory tables, detail pages, ledger/payout/dispute views, affiliate/org management, moderation-queue landing |

`SowingMeAdminJs`'s `explore` and `broadcasting/models` routes are neptune leftovers with no Sowing.me admin-console equivalent — replaced by the routes in §5, not repurposed (unlike live-streaming's `broadcasting/*`, which had a direct analogue).

## 2. Data model

This surface **adds no new core entity tables** — it is a consumer of tables owned by their domain surfaces (per platform TDS §3), reused **exactly** by name: `users`, `creators`, `organizations`, `transactions`, `payout_accounts`, `affiliates`, `attribution_logs`. `reports`/`moderation_actions`/`audit_logs` are owned and specified by [`trust-safety`](../trust-safety/technical-spec.md); this surface only reads `reports` (queue entry point, FR-ADMIN-60) and writes `audit_logs` (status changes) via the shared writer.

**Prerequisite columns** (owned by the surface that creates each table, called out here because admin-console is the first consumer that requires them):
- `users.status` — already specified by platform FR-IAM-4 (`pending`/`active`/`suspended`/`inactive`).
- `creators.status` and `organizations.status` — same `AccountStatusEnum` values; must exist before FR-ADMIN-22 ships. If the owning surface (memberships/organizations) has not yet added these columns, this surface's first migration adds them additively (`ALTER TABLE ... ADD COLUMN status ...`) rather than inventing a parallel status concept.

**New DataTypes/Enums** (`php/Ubix/Enum/` + `DataType/Enum/*`): `AccountStatusEnum` (shared — reused by users/creators/organizations, not redefined per entity), `DisputeReviewStatusEnum` (`open`,`reviewed`), `PayoutStatusEnum` (`scheduled`,`in_flight`,`completed`,`failed`) — the latter may already exist from the payments/payouts surface; this surface reuses it rather than redeclaring.

No table here duplicates a name from platform TDS §3 — see the rule in the task brief: reuse `users`, `creators`, `organizations`, `transactions`, `reports`, `moderation_actions`, `audit_logs` exactly.

## 3. Access control

- Every route: session auth (`admin` role) + role middleware, per platform TDS §6.
- Every route returning subject PII (user/creator/org/affiliate detail, transaction detail) additionally carries a `permissionKey` + `AdminFunctionAccessMiddleware`, per [`sensitive-data-access.md`](../../standards/sensitive-data-access.md) — the same rule the standard's first implementation (`GET /customers`) established; this surface is `entity_type ∈ {user, creator, organization, affiliate}` in `Pii_Access_Audits` terms (extend the enum additively if `affiliate`/`organization` aren't already values).
- `AdminDirectoryService` is the single place status-change authorization + cascade logic lives; controllers never mutate status directly.

## 4. API surface (`SowingMeAdminApi`)

Extends the existing skeleton in `app/SowingMeAdminApi/src/Routes.php` (currently only `/auth`, `/affiliates`, `/affiliate/{id}`).

### 4.1 Auth (build out the stub)
| Method | Path | Purpose | FR |
|---|---|---|---|
| POST | `/auth` | Admin login (already routed to `AuthController::authenticate`, controller absent) | FR-ADMIN-11 |
| GET | `/auth` | Session validate (already routed to `AuthController::validate`, controller absent) | FR-ADMIN-11 |

### 4.2 Directory & status changes
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/users` | Offset-paginated user list (search/sort) | FR-ADMIN-20 |
| GET | `/users/{userId}` | User detail (PII-gated) | FR-ADMIN-21 |
| POST | `/users/{userId}/suspend` | Suspend, with required `reason` | FR-ADMIN-22,24 |
| POST | `/users/{userId}/reinstate` | Reinstate | FR-ADMIN-22 |
| GET | `/creators` | Offset-paginated creator list | FR-ADMIN-20 |
| GET | `/creators/{creatorId}` | Creator detail (PII-gated) | FR-ADMIN-21 |
| POST | `/creators/{creatorId}/suspend` \| `/reinstate` | Same mechanism as users | FR-ADMIN-22 |
| GET | `/organizations` | Offset-paginated org list | FR-ADMIN-20,52 |
| GET | `/organizations/{orgId}` | Org detail (PII-gated) | FR-ADMIN-21,52 |
| POST | `/organizations/{orgId}/suspend` \| `/reinstate` | Same mechanism | FR-ADMIN-22,52 |

### 4.3 Transactions / ledger
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/transactions` | Offset-paginated ledger, filter by `type`/date range/creator/org/supporter | FR-ADMIN-30 |
| GET | `/transactions/{id}` | Transaction detail incl. linked refund/dispute rows (PII-gated) | FR-ADMIN-31 |

No `POST`/`PATCH`/`DELETE` on `/transactions*` — read-only per FR-ADMIN-32.

### 4.4 Payouts & disputes
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/payouts` | Offset-paginated payout list, filter by status/creator/org | FR-ADMIN-40 |
| GET | `/payouts/{id}` | Payout detail incl. Stripe-surfaced failure reason | FR-ADMIN-41 |
| GET | `/disputes` | Offset-paginated dispute list | FR-ADMIN-42 |
| POST | `/disputes/{id}/review` | Mark reviewed + note | FR-ADMIN-42,43 |

### 4.5 Affiliates (build out the stub)
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/affiliates` | Already routed to `AffiliateController::list`, controller absent — becomes offset-paginated real list | FR-ADMIN-50 |
| GET | `/affiliate/{affiliateId}` | Already routed to `AffiliateController::get`, controller absent — becomes real detail (PII-gated) | FR-ADMIN-50 |
| POST | `/affiliate/{affiliateId}/status` | Approve/suspend | FR-ADMIN-51 |

### 4.6 Moderation queue entry point
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/reports` | Offset-paginated open-report count + list (reads the `reports` table trust-safety owns) | FR-ADMIN-60 |

No `POST` here — taking a moderation action is a trust-safety endpoint (see [`trust-safety/technical-spec.md`](../trust-safety/technical-spec.md)); this surface only links out.

## 5. Frontend (`SowingMeAdminJs`)

| Route | Purpose |
|---|---|
| `/users`, `/creators`, `/organizations` | Server-driven `DataTable` (shared component, offset mode per `pagination.md` §3) + detail pages with suspend/reinstate actions |
| `/transactions` | Ledger table with type/date/entity filters; read-only detail drill-in |
| `/payouts`, `/disputes` | Oversight tables; dispute detail with review action |
| `/affiliates`, `/affiliate/{id}` | Replaces the existing shell's `affiliates` route with a real server-driven table + detail/status actions |
| `/reports` | Moderation queue landing — count + list, links into trust-safety's moderation UI |

Reuses `Sidebar.svelte` (exists) and the shared `DataTable` component per platform TDS §10 and `design-system-handoff.md` conventions; no new design system introduced.

## 6. External seam usage

- **`PaymentProviderInterface`** (Stripe) — read-only: surfacing payout failure reasons and dispute state comes from data already reflected into `transactions`/`payout_accounts` by the payments surface's webhook handler; this surface does not call Stripe directly except where a future "open in Stripe dashboard" deep link is added (no new seam).
- **Audit seam** — `PiiAccessAudit` (PII reads) and `AuditLogService` (status/dispute/payout-note writes) are the only two write paths this surface uses; no controller writes to `Pii_Access_Audits` or `audit_logs` directly.

## 7. Requirement traceability

| FR | Realised by |
|---|---|
| FR-ADMIN-10/11 | Admin role+permission middleware, `AuthController` (built out from stub) |
| FR-ADMIN-20/21 | `UserAdminController`/`CreatorAdminController`/`OrganizationAdminController` + offset-paginated repositories |
| FR-ADMIN-22/23/24 | `AdminDirectoryService.suspend()/reinstate()`, `AuditLogService`, `AccountStatusEnum` |
| FR-ADMIN-30/31/32 | `TransactionAdminController`, `LedgerReadService`, read-only `transactions` repository |
| FR-ADMIN-40/41 | `PayoutAdminController`, `PayoutOversightService`, `payout_accounts` |
| FR-ADMIN-42/43 | `DisputeAdminController`, `DisputeService`, `AuditLogService` |
| FR-ADMIN-50/51 | `AffiliateController` (built out from stub), `affiliates`/`attribution_logs` |
| FR-ADMIN-52 | `OrganizationAdminController` (reserved fields per Q3) |
| FR-ADMIN-60/61 | `ReportQueueController` (read-only), shared suspend mechanism invoked by trust-safety |

## 8. Testing

- **Unit:** `AdminDirectoryService` status-transition matrix (active↔suspended, invalid transitions rejected), Payload validation for suspend/reinstate/review actions, `AccountStatusEnum`/`DisputeReviewStatusEnum` DataTypes.
- **Integration:** offset-paginated repository queries (limit/offset/sort/search against a test schema, per `pagination.md` §3); permission-gating middleware on every PII route; audit-writer call assertions (one row per subject, correct `entity_type`).
- **E2E (staging):** admin logs in → suspends a creator → creator page reflects it → reinstates; admin filters ledger and opens a transaction; admin reviews a dispute; admin opens the report queue and navigates to a trust-safety action.
- Gates: `phpunit` (strict), `phpstan` (max), `phpcs` (custom sniffs, including `DemandCanonicalPagination`), JS suite.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
