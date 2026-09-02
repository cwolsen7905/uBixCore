# Surface: Authentication

Login, logout, session lifecycle, account lockout, password reset, role model, and account status for Sowing.me. Hardens what already exists in `SowingMeApi` (`POST/GET /auth`, `POST /logout`, PHP-session auth) rather than building something new. Milestone **M0/M1**, refines platform **FR-IAM**.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-IAM.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model deltas, API, middleware, security mechanics.

This surface inherits the **Platform ADS** ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)) in full — it introduces no system-design decision the platform ADS doesn't already cover (session auth per ADR-002, security architecture §5), so it has no `architecture.md` of its own.

## Status

Draft v0.2 (2026-09-01). **Build-ready** — `users` table, `email_confirmation_tokens`, and `AuthController` already exist; this surface specifies the hardening work (lockout logic, password reset, role model, cookie policy) needed before M1 exits.

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S1) · [`brief.md`](../../projects/sowing-me/brief.md)
Related surface: [`registration`](../registration/README.md) (account creation feeds this surface's login/status lifecycle)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md).
