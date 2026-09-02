# Authentication — Technical Specification

**Surface:** `authentication` · **Status:** Draft v0.2 · 2026-09-01 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md)
**Cites:** [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (Platform TDS — layering, API conventions, access control §6, cited not restated) · [`../../architecture/complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`../../standards/migrations.md`](../../standards/migrations.md)

> **How in code.** This surface hardens the existing `SowingMeApi` auth stack (`AuthController`, `UserSqlRepository`, `EmailConfirmationToken*`). It documents only the deltas: new password-reset schema/endpoints, lockout logic, session/cookie/CORS hardening, and the role/status contracts other surfaces depend on. Everything else (layering, DataType/Payload/DTO/Repository, PHP-DI) is per the platform TDS §1.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| `AuthController` | `php/Ubix/Controller/SowingMeApi/AuthController.php` (existing, hardened) | `authenticate`, `validateSession`, `logout`, CORS `options`; add lockout checks. Depends on `UserService`/`EmailConfirmationTokenService` — controllers never touch repositories (house standard, enforced by the standards test suite since MR !89) |
| `PasswordResetController` (new) | `php/Ubix/Controller/SowingMeApi/PasswordResetController.php` | Request + confirm reset endpoints |
| `UserService` / `EmailConfirmationTokenService` | `php/Ubix/Service/*Service.php` (existing) | Service layer between controllers and repositories; lockout counter updates and reset-token invalidation live here |
| `User` model / `UserSqlRepository` | `php/Ubix/Model/User.php`, `php/Ubix/Repository/User/*` (existing) | Reads/writes `status`, `roles`, `failed_login_attempts`, `last_failed_login`, `password_hash`; reader methods go through the private `query(UserOptions)` pattern |
| `PasswordResetToken` (new) | `php/Ubix/Model/PasswordResetToken.php`, `php/Ubix/Repository/PasswordResetToken/*`, `php/Ubix/Service/PasswordResetTokenService.php` | Mirrors `EmailConfirmationToken` shape/lifecycle (model + Reader/Writer interfaces + SQL repo + service facade) |
| `RoleAuthorizationMiddleware` (new) | `php/Ubix/Middleware/RoleAuthorizationMiddleware.php` | Resolves session roles, rejects (403) routes missing a required role |
| `SessionAuthenticationMiddleware` | `php/Ubix/Middleware/SessionAuthenticationMiddleware.php` (existing) | Confirms an active session before role/ownership checks run |
| ~~`AccountAuthenticationMiddleware`~~ | *(removed in M0-01)* | The neptune-era middleware no longer exists; its status-gating duty moves into `SessionAuthenticationMiddleware` (§5) |
| `EmailService` | `php/Ubix/Service/EmailService.php` (existing) | Add `sendPasswordReset(...)`, mirroring `sendRegistrationConfirmation(...)` |
| Login / reset pages | `app/SowingMeJs/src/routes/login`, `forgot-password` (new), `reset-password` (new) | SvelteKit forms calling the endpoints below |

## 2. Data model (new migrations)

Per [`migrations.md`](../../standards/migrations.md), no edits to `sql/sowingme.sql`; new schema lands via `bin/ubix migrate:*`.

### 2.1 `users` — no new migration, behavior only
No column changes. `status` (existing `UserStatus` enum: `pending`/`active`/`suspended`/`inactive`), `roles` (existing `VARCHAR`, comma-delimited multi-value — e.g. `"creator,supporter"` — matching platform TDS §3 `RoleEnum` values `supporter`/`creator`/`org_admin`/`admin`), `failed_login_attempts` (INT), `last_failed_login` (DATETIME NULL) are all consumed as-is. This surface's only schema contribution is the lockout *logic* around these existing columns (SRS FR-2x).

### 2.2 `password_reset_tokens` (new)
Mirrors `email_confirmation_tokens` exactly in shape, per platform TDS §3 (reuse exact patterns, not parallel inventions):

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | |
| `token_hash` | CHAR(64) | SHA-256 of the raw token; plaintext only ever appears in the emailed URL, never stored |
| `expires_at` | DATETIME | now + 1 hour (FR-31) |
| `used_at` | DATETIME NULL | set on successful confirm (FR-32) |
| `created_at` | DATETIME | |

DataType: `PasswordResetTokenId` under `php/Ubix/DataType/Int/`, following the `UserId` pattern.

Repository standards (as now enforced by the standards test suite): `PasswordResetTokenSqlRepository` implements `PasswordResetTokenReaderInterface` + `PasswordResetTokenWriterInterface`, reader methods go through a private `query(PasswordResetTokenOptions)` (new DTO under `DataTransferObject/SqlRepository/`), and writer methods return `void` — `createToken()` stamps the new id via `Model::setId()` — matching `EmailConfirmationTokenSqlRepository` post-MR !89.

**Note on hashing vs. the existing pattern:** `EmailConfirmationToken` currently stores the raw 64-hex token directly (per `EmailConfirmationTokenSqlRepository`); this surface stores `password_reset_tokens.token_hash` (SHA-256) instead, since a reset token is a stronger authorization bearer (it changes a credential, not just a status flag). Lookup hashes the incoming token before querying, mirroring `live_stream_keys.key_hash` in the live-streaming surface.

## 3. Account lockout logic

Implemented in the service layer — `UserService`, or a dedicated `LoginAttemptService` wrapping it (controllers depend on services, never repositories; house standard) — invoked from `AuthController::authenticate` and evaluated in this order once the user row is found:

```
if user.status !== ACTIVE            → 401 (status-specific message, FR-51)
if user.failed_login_attempts >= 5
   and user.last_failed_login within 15 min → 401 (locked; do not increment, FR-21)
if password_verify fails             → increment failed_login_attempts,
                                        set last_failed_login = now, 401 (generic message)
else                                  → reset failed_login_attempts = 0,
                                        set last_login = now, issue session
```

Thresholds (`5` attempts, `15` minutes) live in app config (env-driven), not hardcoded, per SRS FR-23 — e.g. `AUTH_LOCKOUT_THRESHOLD`, `AUTH_LOCKOUT_MINUTES` consumed by a small `LockoutPolicy` value object injected via PHP-DI.

## 4. API surface (`SowingMeApi`)

All payloads use the existing DataType/Payload validation system; responses are DTOs, matching `AuthenticationRequestPayload`/`AuthenticationResponsePayload` conventions.

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/auth` | Login (existing, hardened with lockout + status checks) | FR-10,13,20-22,51 |
| GET | `/auth` | Validate session (existing) | FR-11 |
| POST | `/logout` | End session (existing) | FR-12 |
| OPTIONS | `/auth` | CORS preflight (existing, hardened to configured origin only) | FR-60-62 |
| POST | `/auth/password-reset/request` | Issue reset token + email link; always 200 regardless of email existence | FR-30,31 |
| POST | `/auth/password-reset/confirm` | Validate token, set new `password_hash`, invalidate other outstanding tokens for the user, reset lockout counter | FR-32,33,34 |

`RegistrationRequestPayload` already exists for `/register` (owned by [`registration`](../registration/README.md)); this surface adds `PasswordResetRequestPayload` (`email`) and `PasswordResetConfirmPayload` (`token`, `password`, `confirmPassword`) under `php/Ubix/Payload/Request/`.

## 5. Session, cookie & status-change interaction

- Session cookie attributes set at session-start (`session_set_cookie_params` before `session_start()`, or ini equivalent in the app bootstrap): `Secure`, `SameSite=Lax`, `HttpOnly`, `path` scoped to the API route prefix — realises FR-60.
- On an admin-initiated status change to `suspended`/`inactive` (owned by the future `admin-console` surface), this surface's contract is: the *next* request through `SessionAuthenticationMiddleware` re-checks `users.status` (not just session presence) and terminates the session if no longer `active` — closing the gap noted in SRS Q2 as a best-effort, not a push-based kill.
- CORS: `options()` and all real responses read the request `Origin`, compare against an allow-list (config, not `*`), and only echo it back + set `Access-Control-Allow-Credentials: true` when it matches — realises FR-61/62. This replaces the current `AuthController::options()` behavior of echoing any origin verbatim.

## 6. Role & ownership middleware

- `RoleAuthorizationMiddleware` reads `$_SESSION['user']['roles']` (already populated by `authenticate`/`confirmEmail`), parses the multi-value field, and rejects (403) unless the route's declared required role is present — mirrors platform TDS §6 "Middleware resolves role; services resolve ownership."
- Ownership checks stay in the owning surface's Service layer (e.g. "is this creator's post" lives in `content-posts`), never in this middleware — this surface only proves *who* and *what roles*, per SRS FR-42.
- Route wiring (Slim 4 group middleware) declares required roles per route group in the owning app's routes file, consistent with the existing `SessionAuthenticationMiddleware` registration.

## 7. Frontend (SvelteKit)

- `app/SowingMeJs/src/routes/login` (existing) — no structural change beyond surfacing lockout/status-specific error messages returned by `/auth`.
- `app/SowingMeJs/src/routes/forgot-password` (new) — email form → `POST /auth/password-reset/request`; always shows a neutral "check your email" confirmation regardless of response (mirrors server-side non-enumeration).
- `app/SowingMeJs/src/routes/reset-password` (new) — reads `?token=` from the URL (same pattern as `confirm-email`), new-password + confirm form → `POST /auth/password-reset/confirm`.
- Reuses `js/Ubix/` shared components (`ThemeToggle`, form primitives) per `complete-js-guide.md`.

## 8. Security & secrets

- Password hashing: `password_hash`/`password_verify`, PHP default algorithm — no change from existing behavior.
- Reset tokens: 32 random bytes (`bin2hex(random_bytes(32))`, matching the existing confirmation-token generation), hashed (SHA-256) at rest, single-use, 1-hour expiry, and superseded (all prior outstanding tokens for the user invalidated) on a new request — per `sensitive-data-access.md`.
- Lockout and reset events are logged via the existing Monolog `Logger` injection, at `info`/`warning` level, with email/user id but never password or full token (only prefixes, matching the existing `substr($token, 0, 10)` pattern in `EmailConfirmationController`).
- No card data or unrelated PII touches this surface.

## 9. Testing

- **Unit:** lockout threshold/window matrix (just-under, at, just-over threshold; window boundary), password-reset token expiry/used-at state machine, role-middleware allow/deny matrix, CORS origin allow-list matching. Non-container, per the migration-test pattern.
- **Integration:** `POST /auth` end-to-end for each account status; full password-reset request→confirm→login cycle against a test schema; `PasswordResetTokenSqlRepository` queries.
- **E2E (staging):** login → protected route → logout; forgot-password → email link → reset → login with new password; role-gated route rejects wrong role.
- Gates: `phpunit` (strict), `phpstan` (max), `phpcs` (custom sniffs), JS suite — per platform TDS §11.

## 10. Requirement traceability

| FR | Realised by |
|---|---|
| FR-10/11/12 | `AuthController::authenticate/validateSession/logout` (existing) |
| FR-13/51 | `AuthController::authenticate` status + credential branches |
| FR-14 | Session GC / cookie lifetime config (app bootstrap) |
| FR-20..24 | `LockoutPolicy` + `users.failed_login_attempts`/`last_failed_login` |
| FR-30..34 | `PasswordResetController`, `password_reset_tokens`, `EmailService::sendPasswordReset` |
| FR-40..43 | `users.roles`, `RoleAuthorizationMiddleware`, onboarding default-role assignment (see `registration`) |
| FR-50..52 | `UserStatus` enum, `SessionAuthenticationMiddleware` status re-check (§5) |
| FR-60..62 | Session cookie params, `AuthController::options` origin allow-list |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
| 0.2 | 2026-09-01 | Synced to built code (M0-03 close-out): controllers now depend on `UserService`/`EmailConfirmationTokenService` (MR !89); `AccountAuthenticationMiddleware` was removed in M0-01 — status re-check reassigned to `SessionAuthenticationMiddleware`; repository reader/writer standards spelled out for the password-reset repo. |
