# Registration — Technical Specification

**Surface:** `registration` · **Status:** Draft v0.2 · 2026-09-01 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md)
**Cites:** [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (Platform TDS — layering, API conventions, domain model §3, cited not restated) · [`../authentication/technical-spec.md`](../authentication/technical-spec.md) (role assignment, session start on auto-login) · [`../../architecture/complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`../../standards/migrations.md`](../../standards/migrations.md)

> **How in code.** This surface hardens the existing `SowingMeApi` registration stack (`AuthController::register`, `EmailConfirmationController`) and adds the creator onboarding wizard's sequencing layer on top. It documents only the deltas: role-on-create fix, resend endpoint, wizard-progress tracking, and duplicate-prospect anti-abuse wiring. Everything else (layering, DataType/Payload/DTO/Repository, PHP-DI) is per the platform TDS §1.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| `AuthController::register` | `php/Ubix/Controller/SowingMeApi/AuthController.php` (existing, hardened) | Creates `users` row, issues confirmation token, sends email; fixed to persist selected role (§3). Persistence via `UserService`/`EmailConfirmationTokenService` — controllers never touch repositories (house standard since MR !89) |
| `EmailConfirmationController` | `php/Ubix/Controller/SowingMeApi/EmailConfirmationController.php` (existing) | `confirmEmail` unchanged; gains a sibling `resendConfirmation` action |
| `RegistrationRequestPayload` | `php/Ubix/Payload/Request/RegistrationRequestPayload.php` (existing) | Extended with a `role` field (`supporter`\|`creator`) for FR-20/21 |
| `EmailConfirmationToken` model/repo/service | `php/Ubix/Model/EmailConfirmationToken.php`, `php/Ubix/Repository/EmailConfirmationToken/*`, `php/Ubix/Service/EmailConfirmationTokenService.php` (existing) | Token issuance/lookup/mark-used via the service facade; reader uses `query(EmailConfirmationTokenOptions)`, writers return `void`; resend supersedes prior tokens (§4) |
| `CreatorOnboardingController` (new) | `php/Ubix/Controller/SowingMeApi/CreatorOnboardingController.php` | Wizard step endpoints: get progress, advance step, hand off to owning surfaces |
| `DuplicateRegistrationProspect` repo (new) | `php/Ubix/Repository/DuplicateRegistrationProspect/*` | Registration-attempt duplicate tracking (§6); the neptune-era `DuplicateProspect` repo was removed in M0-01 — this is a fresh build, not a repurposing |
| Registration / wizard pages | `app/SowingMeJs/src/routes/signup` (supporter), `creator/onboarding/*` (new) | Forms calling the endpoints below |

## 2. Data model (new migrations)

Per [`migrations.md`](../../standards/migrations.md), no edits to `sql/sowingme.sql`; new schema lands via `bin/ubix migrate:*`.

### 2.1 `users` — no new migration, behavior only
No column changes. `roles` (existing `VARCHAR`) is populated at creation with the registrant's selected role (`supporter` or `creator`) instead of the current hardcoded `'user'` literal — a bug-fix-as-part-of-hardening, not a schema change.

### 2.2 `email_confirmation_tokens` — no new migration, behavior only
No column changes. Resend (FR-42) reuses the existing `createToken`/token-shape mechanics; superseded tokens are marked used (via the existing `markTokenAsUsed`) rather than deleted, so audit history is preserved.

### 2.3 `creator_onboarding_progress` (new)
Tracks wizard position per the SRS §10 Q2 default (derive-where-possible, persist only where no natural entity exists yet):

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id`, UNIQUE | one row per creator-track registrant |
| `current_step` | ENUM(`identity`,`profile`,`tier`,`payout`,`complete`) | derived-first: recomputed from entity presence (`creators` row exists → ≥`profile`; a tier row exists → ≥`tier`; a payout account exists → ≥`payout`) whenever read; this column is a cache, not the source of truth |
| `created_at` / `updated_at` | DATETIME | |

DataType: `OnboardingStepEnum` under `php/Ubix/Enum/` with a matching `DataType/Enum/*` wrapper, per the platform TDS §3 enum convention. This table is intentionally thin — it exists only to answer "resume where?" cheaply; FR-22/23/24's actual schema (`creators`, `tiers`, payout account) belongs to their owning future surfaces and is not defined here (platform TDS §12 per-surface delta rule).

### 2.4 `duplicate_registration_prospects` (new)
A registration-scoped table. (The neptune-era `DuplicateProspect` repo that inspired the pattern was removed in M0-01 — nothing is shared or repurposed; this is a fresh table + repo + service following current house standards.)

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `email` | VARCHAR(255) | attempted registration email |
| `ip_address` | VARCHAR(45) NULL | IPv4/IPv6 |
| `matched_user_id` | BIGINT FK → `users.id` NULL | set if it collided with an existing account |
| `flag_reason` | ENUM(`duplicate_email`,`velocity`,`other`) | |
| `created_at` | DATETIME | |

## 3. Role-on-create fix

`RegistrationRequestPayload` gains a required `role` field constrained to `supporter`\|`creator` (org path out of scope per SRS FR-30). `AuthController::register` replaces the current:

```php
roles: 'user',
```

with:

```php
roles: $payload->role->value, // 'supporter' | 'creator' — selected at Step 1 (FR-21)
```

This is the only change to the existing `register` method's user-creation call; all other logic (duplicate checks, token issuance, email send) is unchanged.

## 4. API surface (`SowingMeApi`)

All payloads use the existing DataType/Payload validation system; responses are DTOs, matching the existing controllers' conventions.

### 4.1 Shared / supporter path
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/register` | Create account (existing, extended with `role`) | FR-10,11,12,20,21 |
| GET | `/confirm-email` | Confirm email, auto-login (existing) | FR-40,41 |
| POST | `/confirm-email/resend` | Issue a fresh token, supersede prior ones, rate-limited | FR-42,43,44 |

### 4.2 Creator onboarding wizard
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/creator/onboarding` | Current step (derived per §2.3) + which entities exist | FR-25,26 |
| POST | `/creator/onboarding/profile` | Sequencing hand-off to `creator-profile` surface's create endpoint (this surface validates auth + email-confirmed gate, then delegates) | FR-22,27 |
| POST | `/creator/onboarding/tier` | Sequencing hand-off to `subscription-tiers` surface's create endpoint | FR-23,27 |
| POST | `/creator/onboarding/payout` | Sequencing hand-off to `payouts` surface's Connect-onboarding-start endpoint | FR-24,27 |

The three hand-off endpoints are thin: verify session + `status=active` (email confirmed, FR-27) + role=`creator`, then invoke the owning surface's service. As those surfaces land, their controllers absorb their own step directly and this surface's wizard controller becomes pure orchestration (no domain logic duplicated here) — consistent with platform TDS §12's rule that a surface documents only its own deltas.

### 4.3 Organization onboarding — stub
No endpoints at M1. Reserved path: `POST /organizations` (FR-30) — full spec deferred to the future `organizations` surface per platform FR-ORG-1.

## 5. Resend & expiry handling

`resendConfirmation` (new action on `EmailConfirmationController`, or a small new controller — implementation choice, same DI shape either way):

1. Look up user by email (payload `email`); if not found or already `active`, return the same generic 200 acknowledgement as a found-and-pending user (FR-44, no enumeration).
2. If found and `pending`: check last token's `created_at` against the rate-limit window (FR-43, config-driven like `authentication`'s lockout thresholds — e.g. `REGISTRATION_RESEND_COOLDOWN_SECONDS`); if within cooldown, return the same generic 200 (silently no-op) rather than an error that would leak timing signal.
3. Otherwise: mark any outstanding unused tokens for the user as used (supersession, FR-42), generate a new token via the same `bin2hex(random_bytes(32))` + `EmailConfirmationToken` + `EmailConfirmationTokenService::createToken` mechanics as `register`, send via `EmailService::sendRegistrationConfirmation` (reused, not duplicated).

## 6. Anti-abuse — duplicate prospects

At the top of `register` (before user creation), a lightweight check queries `duplicate_registration_prospects` for recent rows matching the incoming email or IP within a short window (config-driven):

- A match against an existing **active** user's email is already handled by the existing `emailExists` 409 path — no change.
- A match against **recent prospect rows** (same email attempted `pending` multiple times, or high velocity from one IP) writes a flagged row but **does not block** registration (FR-52) — it proceeds normally. This keeps false-positive risk resolved toward "allow," per SRS FR-52.
- Every registration attempt (successful or not) writes one `duplicate_registration_prospects` row when a matching signal exists, feeding a future `admin-console` review queue; attempts with no matching signal do not write a row (keeps the table to genuinely flagged activity).

## 7. Frontend (SvelteKit)

- `app/SowingMeJs/src/routes/signup` (new — no such SPA route exists today; the current supporter sign-up form lives on the Latte marketing site, `templates/sowing-me-web-v1/signup.latte`, posting straight to `/register`) — becomes the real supporter fast sign-up form in the product SPA; a role toggle/link routes into the creator path instead of a separate top-level route, per SRS Q1 default. The Latte form stays as a marketing-site entry point until this route lands.
- `app/SowingMeJs/src/routes/creator/onboarding/{identity,profile,tier,payout}` (new) — wizard shell with a step indicator; `GET /creator/onboarding` on mount decides which step to render; each step's form posts to its hand-off endpoint (§4.2) then re-fetches progress.
- `app/SowingMeJs/src/routes/confirm-email` (existing, 281 lines) — gains a "resend" affordance calling `/confirm-email/resend` when a token is expired/invalid, instead of a dead end.
- Reuses `js/Ubix/` shared components (`ThemeToggle`, `CreatorSidebar` for the wizard shell) per `complete-js-guide.md`.

## 8. Security & secrets

- No change to confirmation-token generation/strength (32 random bytes, `bin2hex`); resend reuses the same mechanism rather than a weaker one.
- Rate-limit and anti-abuse config values are environment-driven (no hardcoded thresholds in code), matching `authentication`'s lockout-config pattern.
- `duplicate_registration_prospects.ip_address` and `email` are PII; access is admin-only (future `admin-console` surface) and logged per `sensitive-data-access.md`.
- Wizard hand-off endpoints perform their own auth/status/role checks independently of the surfaces they delegate to — a missing check in a not-yet-built downstream surface must not be exploitable as an open registration bypass.

## 9. Testing

- **Unit:** role-on-create payload validation (rejects anything outside `supporter`\|`creator`), resend cooldown/enumeration matrix, wizard-step derivation logic (entity-presence → step), duplicate-prospect flag matching. Non-container, per the migration-test pattern.
- **Integration:** full `register` → `confirm-email` cycle for both roles against a test schema; resend supersedes prior token (old token then fails); wizard progress reflects entity creation as steps are (mock-)completed.
- **E2E (staging):** supporter sign-up → confirm → land active; creator sign-up → confirm → wizard resumes after a simulated reload at each step.
- Gates: `phpunit` (strict), `phpstan` (max), `phpcs` (custom sniffs), JS suite — per platform TDS §11.

## 10. Requirement traceability

| FR | Realised by |
|---|---|
| FR-10..13 | `AuthController::register` (existing, unchanged path for supporters) |
| FR-20/21 | `RegistrationRequestPayload.role`, role-on-create fix (§3) |
| FR-22..24 | `/creator/onboarding/{profile,tier,payout}` hand-off endpoints |
| FR-25/26 | `creator_onboarding_progress`, `GET /creator/onboarding` |
| FR-27 | Hand-off endpoints' shared auth/status/role gate |
| FR-30 | Reserved — no code this milestone |
| FR-40/41 | `EmailConfirmationController::confirmEmail` (existing) |
| FR-42..44 | `POST /confirm-email/resend` |
| FR-50..52 | `duplicate_registration_prospects`, register-time check (§6) |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
| 0.2 | 2026-09-01 | Synced to built code (M0-04 close-out): service-layer dependency (MR !89), `DuplicateProspect` repo removal (M0-01) — §2.4/§6 now a fresh build; §7 corrected — the supporter sign-up form lives on the Latte marketing site, the SPA `signup` route is new. |
