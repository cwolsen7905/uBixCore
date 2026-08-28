# Registration — Software Requirements Specification (SRS)

**Surface:** `registration` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M1 (supporter + creator paths); org path M3+ · **Refines:** Platform [`srs.md`](../../projects/sowing-me/platform/srs.md) §5.2 FR-ONB-1..3
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md)
**Upstream:** [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (Platform SRS) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) (S2) · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3.2

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface hardens the existing `POST /register` / `GET /confirm-email` in `SowingMeApi` and adds the creator onboarding wizard on top of it; it does not replace the existing registration mechanics.

## 1. Purpose

Get a supporter or creator from "nothing" to an authenticated, `active` account with the minimum friction appropriate to each path, and start a creator toward a publishable page. This surface is the platform's realisation of FR-ONB; it hands off to [`authentication`](../authentication/README.md) for everything post-account-creation (login, session, roles, status transitions).

## 2. Scope

**In scope:** supporter fast sign-up (email/password only), creator onboarding wizard (identity → profile → first tier → payout account), email confirmation (existing, documented here as the shared completion step for both paths), resend-confirmation + expiry handling, duplicate-prospect anti-abuse.

**Out of scope (this surface):** organization onboarding beyond a stub (full flow is M3+, platform FR-ONB-3/FR-ORG); the actual creator-profile page, tier CRUD, and payout-account mechanics (owned by their respective future surfaces — this surface only sequences the wizard and hands each step to its owner); login/session/lockout (owned by [`authentication`](../authentication/README.md)).

## 3. Context — what exists today

Per [`brief.md`](../../projects/sowing-me/brief.md) §3.2/§3.3: `POST /register` (`AuthController::register`) already creates a `users` row with `status = PENDING`, `roles = 'user'`, issues a 64-hex confirmation token (`bin2hex(random_bytes(32))`), and emails `APP_URL/confirm-email?token=...` via `EmailService`. `GET /confirm-email` (`EmailConfirmationController::confirmEmail`) validates the token (not found / already used / expired), flips `users.status` to `ACTIVE`, marks the token used, and auto-logs-in the user. `DuplicateProspectSqlRepository` exists as an anti-abuse seed (currently used for a different domain's duplicate-submission tracking; this surface repurposes the pattern for duplicate-registration-attempt tracking, not the same rows). This SRS specifies: hardening the existing path for two distinct personas (supporter vs. creator), the creator wizard sequencing, resend/expiry, and anti-abuse.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Fast sign-up** | Supporter registration requiring only email + password (+ display name), no additional steps before the account is usable (pending email confirmation). |
| **Onboarding wizard** | The multi-step creator registration flow: identity → profile → first tier → payout account. |
| **Confirmation token** | The existing 64-hex, 24-hour-expiry, single-use token in `email_confirmation_tokens` proving control of the registered email. |
| **Prospect** | An in-progress registration attempt, before or independent of a completed `users` row — the anti-abuse surface's unit of tracking. |
| **Duplicate prospect** | A registration attempt that collides (by email, or a fuzzy signal) with a prior attempt in a way that suggests abuse (spam sign-ups, retry storms) rather than a genuine new user. |

## 5. Personas & primary user stories

- **Visitor becoming a Supporter.** "As a visitor, I sign up with just my email and password, confirm my email, and I'm in — I can browse and subscribe."
- **Visitor becoming a Creator.** "As a visitor, I sign up, then walk through setting up my identity, my public profile, my first membership tier, and my payout account, so I'm ready to publish and get paid."
- **Church/ministry becoming an Organization (M3+).** "As a ministry admin, I'll eventually create an org account, invite contributors, and verify my ministry — not yet."
- **Any registrant.** "As someone who registered, if my confirmation email didn't arrive or expired, I can get a new one without starting over."
- **Platform (anti-abuse).** "As the platform, repeated near-identical registration attempts from the same source are flagged rather than silently creating noise accounts."

## 6. Functional requirements

### 6.1 Supporter fast sign-up (FR-1x)
- **FR-10** A visitor registers with email, password, password confirmation, first name, last name (existing `RegistrationRequestPayload` shape) via `POST /register`; no additional fields required for the supporter path. *(Realises platform FR-ONB-1.)*
- **FR-11** On success the account is created with `status = pending`, default role `supporter` (see §8 on the existing `roles = 'user'` default, which this surface corrects), and a confirmation email is sent — matching the existing flow.
- **FR-12** Duplicate email or duplicate display name at registration returns a field-scoped 409 error (existing behavior in `AuthController::register`), never silently overwriting an existing account.
- **FR-13** The supporter path has no wizard — one form, one submit, one confirmation email.

### 6.2 Creator onboarding wizard (FR-2x)
- **FR-20** A visitor chooses the creator path at (or immediately after) sign-up; the same `POST /register` account-creation step applies, but the account is additionally flagged/routed into the wizard rather than landing directly on the supporter home. *(Realises platform FR-ONB-2.)*
- **FR-21** **Step 1 — Identity.** Same as supporter sign-up (email, password, name) plus role selection = `creator`; produces the `users` row + confirmation email, identical mechanics to FR-10/11.
- **FR-22** **Step 2 — Profile.** Creator display name/slug, bio, avatar/banner, category — creates the `creators` entity 1:1 with the user (owned by the future `creator-profile` surface; this surface sequences the step and hands off the payload).
- **FR-23** **Step 3 — First tier.** Creator defines at least the implicit free tier, optionally a first paid tier (owned by the future `subscription-tiers` surface; this surface sequences the step, does not define tier schema).
- **FR-24** **Step 4 — Payout account.** Creator starts Stripe Connect Express onboarding (owned by the future `payouts` surface; this surface sequences the step and records that it was started/completed, not the payout mechanics).
- **FR-25** The wizard is resumable: a creator who abandons partway can return and continue from the last completed step (progress persisted server-side, not only in client state).
- **FR-26** A creator account can publish (later surfaces) only once at least identity + profile + one tier exist; payout-account completion gates receiving money, not publishing (mirrors charter M1 "viewable by creator only, no payments" vs. M2 payments).
- **FR-27** Email confirmation (§6.4) is required before the wizard's later steps unlock, exactly as it gates the supporter path — there is one confirmation mechanism shared by both personas, not two.

### 6.3 Organization onboarding (FR-3x) — M3+ stub
- **FR-30** *(Stub, not built this milestone.)* An org admin will create an org entity, invite creator-contributors, and go through a ministry-verification step. *(Realises platform FR-ONB-3 / FR-ORG-1, M3+.)* This surface reserves the persona and the hand-off point (an org-admin role exists per [`authentication`](../authentication/README.md) FR-40) but specifies no schema or endpoint here — full spec lands with the `organizations` surface.

### 6.4 Email confirmation (FR-4x)
- **FR-40** `GET /confirm-email?token=...` validates the token (not found → 400; already used → 400; expired → 400 with "request a new one"; valid → flips `status` to `active`, marks token used, auto-logs-in) — existing behavior, documented as shared infrastructure for both supporter and creator paths.
- **FR-41** A confirmation token is single-use and expires 24 hours after issuance (existing `EmailConfirmationToken` shape) — no change to the token's own lifecycle, only to what happens around it.
- **FR-42** **Resend.** A registrant with a `pending` account can request a new confirmation email; this invalidates (or simply supersedes — see technical-spec) prior outstanding tokens for that user and issues a fresh one, reusing the same generation/send mechanics as initial registration.
- **FR-43** Resend requests are rate-limited per account (e.g. no more than one every 60 seconds) to prevent email-bombing a target address.
- **FR-44** Requesting a resend for an email that is already `active` or does not exist returns the same generic acknowledgement (no user enumeration), matching the non-enumeration posture used elsewhere ([`authentication`](../authentication/README.md) FR-30).

### 6.5 Anti-abuse — duplicate prospects (FR-5x)
- **FR-50** Registration attempts are checked against recent attempts (by email, and where available IP/fingerprint signal) using the `DuplicateProspect` pattern; a flagged attempt does not block registration outright at M1 but is recorded for review (`admin-console` surface, future) rather than silently ignored.
- **FR-51** A high-confidence duplicate/abuse signal (e.g. many registrations from the same source in a short window) may throttle further attempts from that source, distinct from the per-account lockout owned by [`authentication`](../authentication/README.md).
- **FR-52** Anti-abuse checks never block a legitimate first-time registration; false-positive risk is resolved toward allowing registration and flagging for review, not rejecting outright, at this milestone.

## 7. Non-functional requirements

- **NFR-1 Security.** No user enumeration on resend or org paths, consistent with [`authentication`](../authentication/README.md) NFR-1; confirmation tokens remain single-use, time-limited, unguessable (32 random bytes). *(Platform NFR-SEC.)*
- **NFR-2 Privacy.** Registration PII (email, name) handled per `sensitive-data-access.md`; wizard progress data (partial creator profile) is only readable by its owner + admin. *(Platform NFR-PRIV.)*
- **NFR-3 Standards.** DataType/Payload/DTO/Repository pattern for all new code (wizard-progress tracking, resend endpoint); PHPStan max; custom sniffs; strict PHPUnit; every table via `bin/ubix migrate:*`. *(Platform NFR-STD.)*
- **NFR-4 Observability.** Registration attempts, confirmations, resends, and anti-abuse flags are logged with enough context to investigate spam without leaking passwords/tokens. *(Platform NFR-OBS.)*
- **NFR-5 Extensibility.** The wizard's step sequencing must not hardcode assumptions that block the future `organizations` path from reusing the identity step (FR-21) — role selection at Step 1 is the seam. *(Platform NFR-EXT.)*

## 8. Constraints & assumptions

- Builds on the existing `users` + `email_confirmation_tokens` tables and `AuthController::register`/`EmailConfirmationController::confirmEmail` — no redefinition of the confirmation-token lifecycle.
- **Correction carried from `authentication`:** the existing `AuthController::register` hardcodes `roles: 'user'` on creation; this surface's Step 1 (FR-21) replaces that with the role the registrant actually selected (`supporter` or `creator`), matching platform FR-IAM-2's role set — flagged explicitly so it isn't missed as a "no change needed" item.
- Creator wizard steps 2–4 (FR-22..24) are *sequenced* here but *specified* in their owning future surfaces (`creator-profile`, `subscription-tiers`, `payouts`); this SRS does not invent schema for those domains, per platform TDS §12's per-surface delta rule.
- Organization onboarding (FR-30) is scope-reserved, not scope-included, at M1.

## 9. Acceptance criteria (surface DoD)

1. A visitor completes supporter fast sign-up, receives a confirmation email, confirms, and lands `active` with role `supporter`.
2. A visitor starts the creator wizard, completes identity + email confirmation, and can resume the wizard from Step 2 after closing the browser.
3. A registrant whose confirmation email expired can request a resend and successfully confirm with the new token; the old token no longer works.
4. Resend is rate-limited (a second immediate resend request is rejected/no-ops) and gives no signal about account existence for a non-existent email.
5. A burst of near-identical registration attempts from one source is recorded as flagged prospects without blocking the first legitimate one.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`); all new schema via migrations.

## 10. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Is creator-path role selection presented before or after Step 1's form (separate landing choice vs. a field in the same form)? | A field/toggle in the same Step 1 form | FR-20,21 |
| Q2 | Wizard progress persistence — new `creator_onboarding_progress` table, or derive "current step" from which owning entities exist yet (`creators` row exists → step 2 done, etc.)? | Derive from existing-entity presence where possible; only add a progress table if a step has no natural entity yet | FR-25 |
| Q3 | Resend rate-limit window (60s assumed) and lifetime cap (e.g. max 5/day)? | 60s cooldown, no hard daily cap at M1 | FR-43 |
| Q4 | Anti-abuse signal beyond email (IP, device fingerprint) — available at M1 infra? | Email + IP only at M1; fingerprinting deferred | FR-50 |

## 11. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to platform FR-ONB-1..3. Roadmap milestone: M1 (org path M3+), charter surface **S2**.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
