# Surface: Messaging

Supporter↔creator direct messages and creator broadcast-to-tier messages for Sowing.me, with abuse controls (block, rate-limit, report → trust-safety). Post-MVP (roadmap **M3-03**).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: `conversations`/`messages` schema, API, real-time transport, `EntitlementService` gating.

This surface has no `architecture.md` of its own — it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md) (§9 extensibility row: "Comments/community/messaging → `EntitlementService` for gating; new tables, no core change"). Real-time transport follows the pattern documented in [`complete-js-guide.md`](../../architecture/complete-js-guide.md) §5.4 — see `technical-spec.md` §6 for the delta, not a restatement.

## Status
Draft v0.1 (2026-08-27). **Prerequisites unbuilt:** [`notifications`](../notifications/README.md) (new-message alerts), the trust-safety moderation queue (`reports`/`moderation_actions`/`audit_logs`, platform TDS §3, M2/M3, partially built via `admin-console`). `EntitlementService` and `subscription-tiers` (broadcast gating) are built (M1/M2).

## Keeping these in sync
The two docs move together, per `ubixcore/CLAUDE.md` → *Surface documentation*. A requirement change in `srs.md` updates the traceability in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
