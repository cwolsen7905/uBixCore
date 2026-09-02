# Surface: Registration

Account creation for Sowing.me: supporter fast sign-up, creator onboarding wizard, organization onboarding (stub), and email confirmation. Hardens/extends what already exists in `SowingMeApi` (`POST /register`, `GET /confirm-email`) rather than building something new. Milestone **M1** (supporter + creator paths build-ready; org path M3+ stub), refines platform **FR-ONB**.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-ONB.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model deltas, API, wizard steps, anti-abuse.

This surface inherits the **Platform ADS** ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)) in full — it introduces no system-design decision the platform ADS doesn't already cover, so it has no `architecture.md` of its own.

## Status

Draft v0.2 (2026-09-01). **Build-ready for supporter + creator paths.** `users`, `email_confirmation_tokens`, `AuthController::register`, `EmailConfirmationController`, `UserService`, and `EmailConfirmationTokenService` already exist. (The neptune-era `DuplicateProspectSqlRepository` was removed in M0-01; the anti-abuse table in the TDS is a fresh build.) Organization onboarding (FR-ONB-3) is a stub pending the `organizations` entity (platform FR-ORG, M3+).

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S2) · [`brief.md`](../../projects/sowing-me/brief.md)
Related surfaces: [`authentication`](../authentication/README.md) (login/session/role assignment this surface feeds) · `creator-profile`, `subscription-tiers`, `payouts` (not yet authored — steps of the creator onboarding wizard)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md).
