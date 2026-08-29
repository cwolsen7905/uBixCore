# Organizations — Technical Design Specification (TDS)

**Surface:** `organizations` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · Platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams — this doc documents deltas only) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (this surface inherits the Platform ADS; see [`README.md`](README.md))

> **How in code.** Follows uBix Core patterns in [`../../architecture/complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`../../architecture/complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`../../standards/migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Organization domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers, Services) | `organizations`, `org_members`, ministry verification, owner-polymorphism resolution |
| Org API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Org CRUD, member management, verification submission |
| Admin | `app/SowingMeAdminApi/` | Org list, verification review, suspend/reinstate |
| Org page & dashboard | `app/SowingMeJs/` route `/o/{slug}` + `/org/dashboard` | Page, member management UI, consolidated giving view |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. This surface also **promotes** the owner-polymorphism mechanism per **ADR-007** (Platform ADS §4/§10): M1 shipped `creators.organization_id` as a simple FK placeholder; this surface is where it is promoted to the generic `owner_type`/`owner_id` pair on every table that can belong to either a `creators` row or an `organizations` row.

### 2.1 `organizations`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `slug` | VARCHAR(100) UNIQUE | shared uniqueness namespace with `creators.slug` (FR-12) |
| `name` | VARCHAR(200) | |
| `bio` | TEXT NULL | |
| `logo_media_id` / `banner_media_id` | BIGINT FK → media (nullable) | reuses `MediaStorageInterface` pipeline (Platform TDS §5) |
| `category` | VARCHAR(100) NULL | denomination/category, mirrors `creators.category` |
| `verification_status` | ENUM(`pending`,`verified`,`rejected`) default `pending` | FR-50/51 |
| `status` | ENUM(`active`,`suspended`,`inactive`) | mirrors `users`/`creators` lifecycle (Platform FR-IAM-4) |
| `payout_account_id` | BIGINT FK → `payout_accounts.id` NULL | one consolidated payout account (FR-31) |

### 2.2 `org_members`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `organization_id` | BIGINT FK → `organizations.id` | |
| `user_id` | BIGINT FK → `users.id` | linked-user model (SRS §9, PQ1) |
| `role` | ENUM(`org_admin`,`contributor`,`staff`) | FR-20 |
| `status` | ENUM(`pending`,`active`,`removed`) | invite lifecycle (FR-21/22) |
| `invited_by_user_id` | BIGINT FK → `users.id` NULL | |
| UNIQUE | (`organization_id`,`user_id`) | a user holds one role per org at a time |

### 2.3 `org_verifications`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `organization_id` | BIGINT FK | |
| `legal_name` | VARCHAR(200) | |
| `registration_number` | VARCHAR(100) NULL | EIN/equivalent, optional (Q2, self-attestation at launch) |
| `contact_email` | VARCHAR(255) | |
| `status` | ENUM(`submitted`,`approved`,`rejected`) | |
| `reviewed_by_user_id` | BIGINT FK → `users.id` NULL | admin who actioned it |
| `reviewed_at` | DATETIME NULL | |

### 2.4 Owner-polymorphism promotion (ADR-007)

Adds nullable `owner_type ENUM('creator','organization')` and `owner_id BIGINT` to `tiers`, `posts`, `payout_accounts`, and (where `transactions` needs org-level attribution) a resolvable owner via the existing `related_id`/`related_type`-style relation already implied by the ledger (Platform TDS §3). Migration is additive and backward-compatible:

1. Add nullable `owner_type`/`owner_id` columns (no default change to existing behaviour).
2. Backfill: for every existing `creators`-owned row, set `owner_type='creator'`, `owner_id=creators.id` (derived from the current `creator_id` FK).
3. Application code reads `owner_type`/`owner_id` going forward; the legacy `creator_id`/`organization_id` FK columns are kept as generated/derived for one release for read compatibility, then dropped in a follow-up migration once all call sites are confirmed on the polymorphic form.
4. New rows (an org-owned tier/post/payout) are written directly with `owner_type='organization'`.

This satisfies Platform NFR-EXT: no existing `creators`-owned tier/post/payout requires a breaking change.

DataTypes: introduce `OrgMemberRoleEnum`, `OrgVerificationStatusEnum`, `OwnerTypeEnum` (`creator`|`organization`) under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers, per the framework (Platform TDS §3).

## 3. API surface (`SowingMeApi`)

All routes use existing session auth + role/ownership middleware, extended to resolve `org_admin`/`contributor`/`staff` membership via `org_members` (Platform TDS §6). Payloads use DataType/Payload validation; responses are DTOs.

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/organizations` | Create an org (creator becomes `org_admin`); starts `pending` verification | FR-10,50 |
| GET | `/organizations/{slug}` | Public org page data | FR-10,11 |
| PATCH | `/organizations/{id}` | Edit page fields (org_admin only) | FR-10 |
| POST | `/organizations/{id}/members` | Invite a member by email + role | FR-21 |
| PATCH | `/organizations/{id}/members/{memberId}` | Change role / accept invite | FR-20,22 |
| DELETE | `/organizations/{id}/members/{memberId}` | Remove member (detach, no content delete) | FR-22 |
| POST | `/organizations/{id}/verification` | Submit `org_verifications` row | FR-51 |
| GET | `/organizations/{id}/giving` | Consolidated giving view (ledger query, org-scoped) | FR-30,32 |
| GET | `/organizations/{id}/analytics` | Aggregate + per-contributor analytics | FR-40 |

### Admin (`SowingMeAdminApi`)

`GET /organizations`, `GET/POST /organizations/{id}/verification/review` (approve/reject, FR-51), `POST /organizations/{id}/suspend` / `/reinstate` (FR-60/61) — mirrors the creator suspend/reinstate pattern (Platform FR-ADMIN-1).

## 4. Service & entitlement additions

- **`OrganizationService`**: creates orgs, manages membership lifecycle, resolves owner-polymorphism for tiers/posts/payouts it owns.
- **Ownership middleware** (Platform TDS §6) extended: an action against an org-owned resource resolves ownership via `org_members.role` for the acting user, in addition to the existing single-creator ownership check. `EntitlementService` itself is unchanged — tier-gating logic for org-owned posts/tiers works identically because `owner_type/owner_id` resolves to the same `tiers`/`posts` rows the resolver already reads (Platform TDS §6, ADR-008).
- **Ledger queries** for consolidated giving (FR-30) are read-only aggregations over `transactions` scoped by `owner_type='organization' AND owner_id=?` (plus contributor attribution) — no new money table, per **ADR-004**.

## 5. Frontend components (`SowingMeJs`)

- `/o/{slug}` org page (mirrors `/c/{slug}` creator page shape, Platform FR-PROF).
- `/org/dashboard`: member list + invite/role management, verification submission form, consolidated giving/payout view, analytics.
- Reuses `CreatorSidebar`/`Sidebar` shell components (Platform TDS §10) with an org-scoped nav variant.

## 6. Migration ordering & impact

This surface's migrations must land **after** `creators`, `tiers`, `posts`, `transactions`, and `payout_accounts` exist (SRS §9 prerequisites) because the ADR-007 promotion (§2.4) alters those tables. It is the first surface to exercise the "promote owner polymorphism" seam the Platform ADS reserved (§9 extensibility contract row: "Church/organization accounts → `organizations` entity + owner polymorphism").

## 7. Requirement traceability

| FR | Realised by |
|---|---|
| FR-10/11/12 | `organizations` table, `/organizations/{slug}` |
| FR-20/21/22/23 | `org_members`, invite/role/remove endpoints |
| FR-30/32 | `owner_type/owner_id` promotion (§2.4), `/organizations/{id}/giving` ledger query, ADR-004 |
| FR-31 | `organizations.payout_account_id`, Stripe Connect (Platform FR-FIN-1) |
| FR-40 | `/organizations/{id}/analytics` |
| FR-50/51/52 | `org_verifications`, admin review endpoint |
| FR-60/61 | Admin suspend/reinstate endpoints |

Platform trace: FR-ORG-1/2 (Platform SRS §5.19), FR-FAITH-2 (§4).

## 8. Testing

- **Unit:** `org_members` role resolution, ownership-middleware extension (matrix over role × action), owner-polymorphism backfill logic, verification state machine, payload validation.
- **Integration:** repository queries against a test schema for the consolidated giving view (org-scoped `transactions` aggregation); admin verification review flow.
- **E2E (staging):** create org → invite contributor → contributor publishes → supporter gives → consolidated giving view reflects it → admin approves verification → payout scheduled.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
