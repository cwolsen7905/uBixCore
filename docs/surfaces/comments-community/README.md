# Surface: Comments & Community

Comments and reactions on posts (entitlement-gated) with creator moderation, for Sowing.me. Post-MVP (roadmap slice of **M3**; not yet its own numbered roadmap row — see `srs.md` §9).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: `comments`/`reactions` schema, API, `EntitlementService` gating, moderation.

This surface has no `architecture.md` of its own — it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md) (§9 extensibility row: "Comments/community/messaging → `EntitlementService` for gating; new tables, no core change"). Live chat during a broadcast is a **different surface's** concern — see [`../live-streaming/README.md`](../live-streaming/README.md) FR-4x; this surface does not duplicate it.

## Status
Draft v0.1 (2026-08-27). **Prerequisites unbuilt:** the trust-safety moderation queue (`reports`/`moderation_actions`/`audit_logs`, platform TDS §3, M2/M3, partially built via `admin-console`), [`notifications`](../notifications/README.md) (new-comment alerts, spec complete). `EntitlementService` and `content-posts` are built (M1). Community spaces/groups are **FUTURE** per platform SRS §5.15/§2 — noted, not specified here.

## Keeping these in sync
The two docs move together, per `ubixcore/CLAUDE.md` → *Surface documentation*. A requirement change in `srs.md` updates the traceability in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
