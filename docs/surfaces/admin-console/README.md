# Surface: Admin Console

Internal operations surface for Sowing.me: user/creator/organization oversight, transaction & ledger views, payout oversight and dispute handling, affiliate & organization management, and the entry point into the moderation queue (rules and actions themselves belong to [`trust-safety`](../trust-safety/README.md)). Realises platform **FR-ADMIN**, roadmap **S11**. Milestone **M2**.

`SowingMeAdminApi` + `SowingMeAdminJs` today are a **skeleton**: `GET/POST /auth`, `GET /affiliates`, `GET /affiliate/{id}` are wired to `Ubix\Controller\InternalAdminApi\{AuthController,AffiliateController}`, which **do not exist** under `php/Ubix/Controller/` (only `SowingMeApi/` and `SowingMeWeb/` controllers exist today — see [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3). `SowingMeAdminJs` is a UI shell (`login`, `settings`, `explore`, `affiliates`, `broadcasting/models` — neptune leftovers). This surface's job is to turn that skeleton into the real admin console.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-ADMIN.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model deltas, API, controllers to build, frontend routes.

This surface inherits the **Platform ADS** ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)) in full — internal-exposure app boundary (ADS §5), audit logging of money/moderation actions (ADS §5, §6 moderation subsystem), and offset-pagination admin tables (platform TDS §4) are already specified there. It introduces no new system-design decision, so it has no `architecture.md` of its own.

## Status

Draft v0.1 (2026-08-27). **Build-ready** — the skeleton routes and app scaffolding exist; the controllers, services, repositories, and admin SPA screens they call do not. This surface specifies what to build.

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S11) · [`brief.md`](../../projects/sowing-me/brief.md) §3
Related surface: [`trust-safety`](../trust-safety/README.md) (owns moderation rules, `reports`/`moderation_actions`; this surface only provides the admin-side entry point and enforces status changes it decides)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md) and [`authentication`](../authentication/README.md).
