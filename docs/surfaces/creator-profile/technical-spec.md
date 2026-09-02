# Creator Profile — Technical Specification

**Surface:** `creator-profile` · **Status:** Draft v0.2 · 2026-09-01 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams — this doc documents only deltas)

> **How in code.** Follows [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Creator domain | `php/Ubix/` (Model, Repository, DTOs, DataTypes, Controller, Service) | `creators`, slug history, profile CRUD, page composition. Controllers depend on `CreatorProfileService`, never repositories; SQL repositories follow the `query(CreatorOptions)` reader pattern with `void` writers (house standards, enforced by the standards test suite since MR !89) |
| Creator API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/CreatorController` | Public profile read, authenticated profile write, slug management |
| Admin API | `app/SowingMeAdminApi/` | Suspend/reinstate (stub until `admin-console`) |
| Public page | `app/SowingMeJs/` route `/c/[slug]` | Renders composed profile |
| Profile editor | `app/SowingMeJs/` route `/creator/dashboard/profile` (existing dashboard becomes real) | Self-service edit form, slug change |

## 2. Data model

**As built** — landed 2026-09-01 as `sql/migrations/20260901000000_create_creators_and_slug_history.sql` (roadmap M0-06). All tables `InnoDB`, snake_case, `created_at`/`updated_at`. Integer key types follow `users.id` (`INT(10) UNSIGNED`), not the platform-TDS default of BIGINT — the FK requires the referenced type.

### 2.1 `creators`
| Column | Type | Notes |
|---|---|---|
| `id` | INT(10) UNSIGNED PK | matches `users.id` type |
| `user_id` | INT(10) UNSIGNED FK → `users.id`, UNIQUE | 1:1 (SRS FR-101) |
| `slug` | VARCHAR(64) UNIQUE | FR-201 |
| `display_name` | VARCHAR(120) | |
| `bio` | TEXT NULL | |
| `avatar_url` | VARCHAR(500) NULL | plain URL at M1 (SRS §9); `media-storage` upgrades the value, not the column |
| `banner_url` | VARCHAR(500) NULL | ditto |
| `category` | ENUM (`CreatorCategoryEnum`) | starter set: `pastor`,`worship`,`teacher`,`podcaster`,`author`,`artist`,`other` |
| `faith_topic` | VARCHAR(120) NULL | free text at M1; denomination/topic taxonomy is an `explore` (M2) concern |
| `external_links` | JSON NULL | `[{label, url}]`, app-layer cap (e.g. 5) |
| `organization_id` | BIGINT UNSIGNED NULL | **reserved**, no FK constraint yet — added when `organizations` (M3+) exists (ADR-007) |
| `payout_account_id` | BIGINT UNSIGNED NULL | **reserved**, no FK constraint yet — added when `payouts` (M2) creates `payout_accounts` |
| `status` | ENUM (`CreatorStatusEnum`: `draft`,`active`,`suspended`) | FR-103 |
| `published_at` | DATETIME NULL | set on first `draft`→`active` transition |

Indexes: UNIQUE `slug`; UNIQUE `user_id`; index on `status` (page-availability checks).

### 2.2 `creator_slug_history`
| Column | Type | Notes |
|---|---|---|
| `id` | INT(10) UNSIGNED PK | |
| `creator_id` | INT(10) UNSIGNED FK → `creators.id` | |
| `old_slug` | VARCHAR(64) UNIQUE | unique across this table (FR-203); app-layer also checks against live `creators.slug` |
| `retired_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP (as built) |

DataTypes: `php/Ubix/Enum/Creator/CreatorCategory`, `CreatorStatus` (house enum naming — no `Enum` suffix, per `Ubix\Enum\User\UserStatus`) + matching `DataType/Enum/*` wrappers per the framework; a `CreatorSlug` DataType (format/length validation, shared by both tables' writes).

## 3. Slug redirect resolution

```
GET /c/{slug}
  → creators.slug = {slug} AND status = 'active'?  → render profile
  → else creator_slug_history.old_slug = {slug}?    → 301 to /c/{current slug}
  → else                                             → 404
```
Resolution order and the 301 (not 302, so it's cacheable/SEO-correct) are enforced in the Service, not the controller, so the same rule applies to any future consumer (e.g. an API client resolving a slug).

## 4. API surface (`SowingMeApi`)

Payloads use the DataType/Payload validation system; responses are DTOs. Public read has no auth; write routes require session auth + ownership middleware (a creator can only write their own row).

### 4.1 Public
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/creators/{slug}` | Composed public profile: bio/avatar/banner/category/faith_topic/external_links + tiers summary + recent public posts (if `content-posts` exists) + upcoming/live (if `live-streaming` exists) + subscribe CTA data | FR-301..306 |

### 4.2 Creator (authenticated, ownership-checked)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/creator/profile` | Create the `creators` row (onboarding step) | FR-101, FR-402 |
| GET | `/creator/profile` | Read own profile (including `draft`) | FR-401 |
| PATCH | `/creator/profile` | Edit profile fields | FR-401, FR-403 |
| PATCH | `/creator/profile/status` | `draft` → `active` publish transition | FR-404 |
| POST | `/creator/profile/slug` | Change slug; writes `creator_slug_history`; rate-limited (FR-204) | FR-202..204 |
| GET | `/creator/profile/slug-history` | List own retired slugs | FR-202 |

### 4.3 Admin (`SowingMeAdminApi`)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| PATCH | `/creators/{id}/status` | Suspend/reinstate | FR-501 |

## 5. Page composition (service, not controller, stitches the sections)

`CreatorProfileService::composePublicProfile(slug)`:
1. Resolve creator by slug (or redirect/404 per §3); if `status != active`, 404.
2. Load own fields (bio, avatar, category, links).
3. Call `TierService::listPublicTiers(creatorId)` (from `subscription-tiers`) for the tiers section.
4. If `content-posts`' `PostService` is registered in the container, call it for recent public posts; otherwise omit the section (SRS FR-303, NFR-2). Same pattern for `live-streaming`'s stream listing (FR-304).
5. Assemble one response DTO; no partial-failure surfaces as an HTTP error — a missing optional section is just an absent key.

This "call if present, omit if absent" composition is what lets `content-posts` and `live-streaming` land later without this surface's contract changing — the response DTO's optional fields are already shaped for it.

## 6. Frontend components

- `/c/[slug]` (public, SvelteKit): profile header (avatar/banner/bio/category/links), tiers grid (from `subscription-tiers`' shared component), recent-posts list (conditional), upcoming/live banner (conditional), subscribe CTA. Reuses `ThemeToggle`.
- `/creator/dashboard/profile` (existing dashboard page becomes real): edit form bound to the PATCH endpoints; slug-change control shows the redirect-history list and the current rate-limit state.
- Onboarding wizard step (new — no wizard shell exists in `SowingMeJs` today; the wizard is specified in the registration TDS §4.2): profile step calls `POST /creator/profile`, then hands off to the `subscription-tiers` step.

## 7. External-seam usage

None new. Avatar/banner are plain URLs (no `MediaStorageInterface` call yet — reserved for `media-storage`). No payment, mailer, or notifier calls originate in this surface.

## 8. Service/entitlement additions

None — this surface has no gated content of its own. It only *consumes* `subscription-tiers` and (later) `content-posts`/`live-streaming` read services; `EntitlementService` is not called here because the public profile itself has no tier-gated fields.

## 9. Testing

- **Unit:** slug uniqueness/format validation, slug-history redirect resolution (including the "retired slug can't be reclaimed" rule), status-transition guard (`draft`→`active`→`suspended`), Payload validation for profile fields (external-links cap, category enum).
- **Integration:** repository queries against a test schema (slug lookup + history fallback in one path); ownership middleware rejecting cross-creator writes; page composition with 0/1/2 of the optional sibling services present.
- **E2E (staging):** onboarding → profile created → page live at `/c/{slug}` → slug changed → old link redirects → admin suspends → page 404s for visitors.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 10. Requirement traceability

| FR | Realised by |
|---|---|
| FR-101/102/103 | `creators` table, `CreatorProfileController`/`Service` |
| FR-104/105 | `creators.organization_id`/`payout_account_id` reserved columns |
| FR-201/202/203/204 | `creator_slug_history`, slug-change endpoint + rate limit |
| FR-301..306 | `GET /creators/{slug}`, `CreatorProfileService::composePublicProfile` |
| FR-401/403 | `PATCH /creator/profile`, ownership middleware |
| FR-402 | `POST /creator/profile` (onboarding step) |
| FR-404 | `PATCH /creator/profile/status` |
| FR-501 | `PATCH /creators/{id}/status` (admin) |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
| 0.2 | 2026-09-01 | Synced to as-built state (M0-05 close-out): §2 reflects the landed M0-06 migration (`INT(10) UNSIGNED` keys, `BIGINT UNSIGNED` reserved columns, `retired_at` default); enum naming to house convention; service-layer/repository standards noted; onboarding-shell claim corrected. |
