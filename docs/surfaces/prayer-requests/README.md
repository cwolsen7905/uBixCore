# Prayer Requests — Surface Overview (README)

**Surface:** `prayer-requests` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3+ (post-MVP, faith-native) · **Prerequisites (unbuilt):** `creator-profile` (S3), `subscription-tiers` (S4), `trust-safety` ([`../trust-safety/`](../trust-safety)), `notifications` (Platform FR-NOTIF, no surface folder yet)

Supporters submit prayer requests to a creator or organization's community; the community can pray/respond, with public / private / tier-scoped visibility enforced the same way as gated content. Realises Platform SRS **FR-PRAY** ([`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.21) and **FR-FAITH-4** (§4).

## Read order

1. [`srs.md`](srs.md) — requirements: what & why, numbered FRs/NFRs, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — how in code: `prayer_requests`/`prayers` data model, `EntitlementService` reuse for visibility scoping, moderation and notification hand-offs.

## No architecture.md

This surface has no independent ADS. It inherits the Platform ADS — [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md), specifically the extensibility contract row "Prayer requests/walls → standalone tables + `EntitlementService` reuse for scoping" (§9) and **ADR-008** (central `EntitlementService` as the single gating authority).

## Status

Draft v0.1 (2026-08-27). **Prerequisites unbuilt:** `creator-profile` and `subscription-tiers` (needed for tier-scoped visibility) have no migrations yet; **moderation** depends on the `trust-safety` surface's report/action queue pattern ([`../trust-safety/`](../trust-safety) — currently only a placeholder directory, no docs yet); **notifications on responses** depend on the `notifications` surface (Platform FR-NOTIF, M3, no `docs/surfaces/notifications/` folder yet — the dependency is noted here so it is not lost).

## Keeping these in sync

`srs.md` and `technical-spec.md` move together and cite the Platform SRS/TDS/ADS for anything shared. A requirement change here re-versions both docs' Document control tables.
