# Surface: Creator Profile

The `creators` entity, the public creator page (`/c/{slug}`), slug + slug-history, creator edit flows, and the org-affiliation stub. Realises platform **FR-PROF**. Milestone **M1** — build-ready.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-PROF.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, service/entitlement touches, frontend components.

No `architecture.md` for this surface — it inherits the platform ADS in full (modular monolith, session auth, ledger/media/entitlement seams, owner polymorphism per ADR-007). Nothing here changes system topology, so a separate ADS would only restate [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md).

## Status

Draft v0.2 (2026-09-01). **Schema landed; app layer build-ready for M1.** `creators` and `creator_slug_history` exist as of `sql/migrations/20260901000000_create_creators_and_slug_history.sql` (M0-06, applied to the test DB); the PHP domain (Model/Repository/Service/Controller) and both frontend routes are still to build (M1-02/M1-03). The `organization_id` and `payout_account_id` columns are reserved (nullable, no FK constraint yet) so the M3+ `organizations` entity and the M2 `payouts` surface attach without a `creators` schema rework (NFR-EXT).

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) (§5.3 FR-PROF) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§3 domain model, §12 per-surface template) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (§4 owner polymorphism, ADR-007, ADR-008)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S3, Q2 creator-as-entity) · [`brief.md`](../../projects/sowing-me/brief.md) (§3 inventory)
Related surfaces: [`subscription-tiers`](../subscription-tiers/README.md) (tiers rendered on the public page) · `content-posts` (recent public posts on the page; not yet authored) · `payouts` (payout account the profile stub links to; not yet authored) · [`live-streaming`](../live-streaming/README.md) (upcoming/live streams on the page)
Standards: [`database.md`](../../standards/database.md) · [`migrations.md`](../../standards/migrations.md) · [`pagination.md`](../../standards/pagination.md)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md).
