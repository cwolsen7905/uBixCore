# Authentication — Software Requirements Specification (SRS)

**Surface:** `authentication` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M0/M1 · **Refines:** Platform [`srs.md`](../../projects/sowing-me/platform/srs.md) §5.1 FR-IAM-1..4
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md)
**Upstream:** [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (Platform SRS) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) (S1) · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3.2

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface hardens the existing `users` table + `AuthController` in `SowingMeApi`; it does not replace them.

## 1. Purpose

Every other surface depends on knowing *who is asking* and *whether they're allowed*. This surface specifies the login/logout/session lifecycle, account lockout, password reset, the role model, account-status lifecycle, and the cookie/CORS policy that everything else in Sowing.me is built on top of. It is the platform's realisation of FR-IAM.

## 2. Scope

**In scope:** email/password login, session-cookie issuance and validation, logout, account lockout after repeated failed logins, password reset (request + confirm), multi-role model (`supporter`, `creator`, `org_admin`, `admin`), account status lifecycle (`pending`/`active`/`suspended`/`inactive`), secure cookie policy, CORS policy for the product SPA.

**Out of scope (this surface):** account creation / email confirmation (owned by [`registration`](../registration/README.md)); token/JWT auth (reserved seam per ADR-002, not built); social/OAuth login; multi-factor authentication (future).

## 3. Context — what exists today

Per [`brief.md`](../../projects/sowing-me/brief.md) §3.2/§3.3: `SowingMeApi` already exposes `POST/GET /auth` (authenticate / validate session), `POST /logout`, and CORS `OPTIONS`, all PHP-session based (`php/Ubix/Controller/SowingMeApi/AuthController.php`). The `users` table already carries `status`, `roles`, `failed_login_attempts`, and `last_failed_login` columns — the lockout *fields* exist, the lockout *logic* does not. This SRS specifies that missing logic plus password reset and the role/status contracts every other surface relies on.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Session** | Server-side PHP session, keyed by a session cookie, holding `$_SESSION['user']` (id, displayName, email, roles, firstName, lastName). |
| **Lockout** | A temporary block on login attempts for an account after N consecutive failures, tracked via `failed_login_attempts` / `last_failed_login`. |
| **Role** | One of `supporter`, `creator`, `org_admin`, `admin` (platform FR-IAM-2). A user may hold several simultaneously (`users.roles`). |
| **Account status** | One of `pending`, `active`, `suspended`, `inactive` (`UserStatus` enum, `php/Ubix/Enum/User/UserStatus.php`). |
| **Ownership check** | Per-request authorization confirming the authenticated user owns/controls the specific resource being acted on (distinct from role check). |

## 5. Personas & primary user stories

- **Supporter/Creator/Org admin/Admin (any authenticated user).** "As a user, I log in with my email and password, stay logged in across requests via a secure cookie, and can log out explicitly."
- **Any user who forgot their password.** "As a user, I can request a password reset email and set a new password from a time-limited link."
- **Any user under attack.** "As a user, if someone repeatedly guesses my password wrong, my account temporarily locks so a brute-force attempt can't succeed."
- **Platform admin.** "As an admin, I can suspend or reinstate an account and the change takes effect on the user's very next request."
- **Any protected route/controller.** "As a route handler, I can trust that only an authenticated user with the right role — and, where relevant, the right ownership — reaches my logic."

## 6. Functional requirements

### 6.1 Login & session lifecycle (FR-1x)
- **FR-10** A user authenticates with email + password via `POST /auth`; on success the server starts a PHP session and stores `id`, `displayName`, `email`, `roles`, `firstName`, `lastName` in `$_SESSION['user']`. *(Realises platform FR-IAM-1.)*
- **FR-11** `GET /auth` validates the current session and returns the same user shape, or an empty/unauthenticated result if no valid session exists.
- **FR-12** `POST /logout` clears `$_SESSION` and destroys the session server-side; the client cookie is invalidated.
- **FR-13** Login fails (401) if the email is unknown, the password does not verify, or the account status is not `active` — the same generic error message is returned for unknown-email and wrong-password cases (no user enumeration).
- **FR-14** A session has a server-side idle/absolute timeout (default: 30 days absolute, per session GC config); expired sessions are treated as unauthenticated.

### 6.2 Account lockout (FR-2x)
- **FR-20** Each failed login increments `users.failed_login_attempts` and sets `users.last_failed_login`; a successful login resets `failed_login_attempts` to 0.
- **FR-21** After **5** consecutive failed attempts, the account is locked for **15 minutes** from `last_failed_login`; login attempts during the lockout window return 401 with a lockout-specific message (distinct from bad-credentials, since the account is already known at that point) but do **not** further increment the counter.
- **FR-22** The lockout window elapsing does not require an admin action — the next attempt after the window is evaluated normally (and resets the counter on success).
- **FR-23** Lockout state and thresholds are configuration (not hardcoded per environment) so they can be tuned without a schema change.
- **FR-24** Lockout events are logged (`sensitive-data-access.md` posture) for abuse monitoring; repeated lockouts on the same account are visible to admin (`admin-console` surface, out of scope here beyond logging).

### 6.3 Password reset (FR-3x)
- **FR-30** A user can request a password reset by email via a new endpoint; the response is identical whether or not the email exists (no user enumeration).
- **FR-31** A password-reset request issues a single-use, time-limited (1 hour) random token, stored hashed, and emails a reset link (`APP_URL/reset-password?token=...`) via the existing `EmailService` — mirrors the confirmation-token pattern in [`registration`](../registration/README.md).
- **FR-32** Submitting a valid, unexpired, unused token with a new password (+ confirmation) updates `password_hash`, marks the token used, invalidates any other outstanding reset tokens for that user, and resets `failed_login_attempts`.
- **FR-33** An expired or already-used token returns a clear error directing the user to request a new one; requesting a new reset supersedes prior outstanding tokens.
- **FR-34** A successful password reset does not, by itself, log the user in (distinct from email confirmation's auto-login, since the actor here isn't necessarily proven to be the account owner beyond the token).

### 6.4 Role model (FR-4x)
- **FR-40** Roles are `supporter`, `creator`, `org_admin`, `admin`; a user may hold multiple roles concurrently. *(Realises platform FR-IAM-2.)*
- **FR-41** Every protected route runs server-side role middleware; a request lacking a required role is rejected (403) regardless of client-side UI state.
- **FR-42** Ownership checks are separate from and layered on top of role checks (e.g. "is a creator" is a role check; "is *this* creator's post" is an ownership check) — role middleware never substitutes for an ownership check.
- **FR-43** New accounts from supporter/creator/org-admin onboarding (see [`registration`](../registration/README.md)) are assigned the appropriate default role at creation time; `admin` is never self-assignable via any public endpoint.

### 6.5 Account status lifecycle (FR-5x)
- **FR-50** Account status is one of `pending` (registered, email unconfirmed), `active` (confirmed, normal), `suspended` (admin action, login blocked), `inactive` (deactivated, login blocked). *(Realises platform FR-IAM-4.)*
- **FR-51** Only `active` accounts may authenticate; `pending`/`suspended`/`inactive` all fail login with a status-appropriate message (`pending` → "confirm your email"; `suspended`/`inactive` → generic "account not active", no detail leaked beyond what's necessary).
- **FR-52** Transition `pending`→`active` happens via email confirmation ([`registration`](../registration/README.md) FR-40 in that surface); `active`↔`suspended`/`inactive` happens via admin action (`admin-console` surface) and immediately affects new authentication and, ideally, terminates existing sessions for that user (session invalidation on status change — see technical-spec §5).

### 6.6 Cookie & CORS policy (FR-6x)
- **FR-60** The session cookie is issued with `Secure`, `SameSite=Lax`, `HttpOnly`, and a path scoped to the API. *(Realises platform FR-IAM-3 cookie policy, ADS §5.)*
- **FR-61** CORS responses (`OPTIONS` preflight and actual responses) allow only the configured product SPA origin(s), with `Access-Control-Allow-Credentials: true` required for the cookie to be sent cross-origin between `SowingMeJs` and `SowingMeApi`.
- **FR-62** No wildcard (`*`) origin is used when credentials are allowed (browsers reject this combination anyway; it must not be attempted even in dev configs that get promoted).

## 7. Non-functional requirements

- **NFR-1 Security.** Password hashing via `password_hash`/`password_verify` (PHP default algorithm, currently bcrypt); no plaintext password ever logged (mirrors existing `AuthController` which already avoids logging raw passwords). Server-side authorization on every protected action; least privilege. *(Platform NFR-SEC.)*
- **NFR-2 Privacy.** Emails and other PII in session/log output are handled per `sensitive-data-access.md`; lockout/reset logging never includes the password or full token, only prefixes/ids. *(Platform NFR-PRIV.)*
- **NFR-3 Availability.** Auth must not be a single point of failure for the rest of the platform beyond the shared DB/session store already assumed by the platform ADS (stateless app pods, external session store). *(Platform NFR-AVAIL/SCALE.)*
- **NFR-4 Standards.** DataType/Payload/DTO/Repository pattern for all new code (password-reset token repository mirrors `EmailConfirmationToken`); PHPStan max; custom sniffs; strict PHPUnit; every table via `bin/ubix migrate:*`. *(Platform NFR-STD.)*
- **NFR-5 Observability.** Login failures, lockouts, password-reset requests/completions, and status transitions are logged with enough context to investigate abuse without leaking secrets. *(Platform NFR-OBS.)*
- **NFR-6 Extensibility.** Token/JWT auth remains a reserved seam behind the existing auth middleware (ADR-002); this surface must not make adding it later require touching domain code. *(Platform NFR-EXT.)*

## 8. External interfaces (summary — detail in technical-spec)

- **`SowingMeApi`**: `POST/GET /auth`, `POST /logout` (existing); `POST /auth/password-reset/request`, `POST /auth/password-reset/confirm` (new).
- **Product SPA (`SowingMeJs`)**: login page (existing), forgot-password + reset-password pages (new), session-aware routing/guards.
- **`EmailService`**: password-reset email (new template, same mechanism as the existing registration-confirmation email).

## 9. Constraints & assumptions

- Builds on the existing `users` table columns (`status`, `roles`, `failed_login_attempts`, `last_failed_login`) — no redefinition of those columns, only logic around them.
- Session store and cookie mechanics follow charter §5 and platform ADS §5 (PHP session cookie now; token/JWT deferred).
- `roles` is currently a `varchar` on `users` (brief §3.3); this surface treats it as a comma-delimited (or equivalent) multi-value field per platform FR-IAM-2 rather than introducing a join table — technical-spec §2 confirms the concrete representation.

## 10. Acceptance criteria (surface DoD)

1. A user with correct credentials and `active` status logs in and receives a session cookie meeting FR-60; `GET /auth` reflects the session; `POST /logout` ends it.
2. Five consecutive wrong-password attempts lock the account for 15 minutes; a sixth attempt within the window is rejected without incrementing the counter further; an attempt after the window is evaluated normally.
3. A password-reset request + confirm flow changes the password and the user can log in with the new password but is not auto-logged-in by the reset itself.
4. A `pending`, `suspended`, or `inactive` account cannot log in even with correct credentials, and each gets an appropriate (non-leaking) message.
5. A route requiring the `creator` role rejects a `supporter`-only user (403) even with a valid session.
6. CORS preflight/response only allows the configured SPA origin, with credentials enabled and no wildcard.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`); all new schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Exact lockout threshold/duration (5 attempts / 15 min assumed) — product-tunable? | Config value, defaults as stated in FR-21 | FR-21 |
| Q2 | Does a status change to `suspended`/`inactive` need to kill *existing* sessions immediately, or is next-login-blocked sufficient for M1? | Best-effort session invalidation at status-change time; hard guarantee deferred | FR-52 |
| Q3 | Session absolute lifetime (30 days assumed) — does the product want "remember me" vs. shorter default? | 30-day absolute, no remember-me toggle at M1 | FR-14 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to platform FR-IAM-1..4. Roadmap milestone: M0/M1, charter surface **S1**.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
