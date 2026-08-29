# Surface: Affiliates

Referral links & banners, cookie/link attribution tracking, referral records, and revenue-share payouts (via the existing `transactions` ledger + `payouts` surface) for Sowing.me creators — plus first-class **church/organization affiliate revenue-share** (a church earns for creators and supporters it brings). Realises platform **FR-AFF** and **FR-FAITH-6**. Post-MVP (roadmap **M3-01**).

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-AFF and FR-FAITH-6, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model (`affiliates`, `referrals`, repurposed `attribution_logs`), API, revenue-share ledger writes, frontend.

This surface has **no `architecture.md` of its own** — it inherits the platform ADS in full ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)), in particular §4 (data architecture / ADR-007 owner polymorphism) and §9 (extensibility contract, "Affiliate revenue-share" row: `attribution_logs` seed + ledger `fee`/`payout` types). If this surface ever needs system design beyond what the platform ADS already covers, an `architecture.md` is added then, following the `live-streaming` precedent.

## Status

Draft v0.1 (2026-08-27). **Prerequisites unbuilt:** `creator-profile`, `payments` (`transactions` ledger), `payouts` (`payout_accounts`). Two pieces already exist as neptune carry-overs and are **repurposed, not kept as-is**:

- `SowingMeAdminApi` route stubs `GET /affiliates` / `GET /affiliate/{id}` (`app/SowingMeAdminApi/src/Routes.php`), wired to a controller that doesn't exist yet — this surface creates it.
- The `attribution_logs` table/model (`Ubix\Model\AttributionLog`), shaped for a different ("MP code") attribution domain — this surface's migration replaces its columns with the shape in `technical-spec.md` §2.3.
- The `SowingMeJs` `affiliates` and `affiliates/banners` routes are ≤8-line stubs (the banners stub currently iframes a legacy `vscash`-domain tool) — this surface makes them real.
- The `AffiliateStatusEnum`/`AffiliateRateTypeEnum`/`AffiliateSiteTypeEnum` DataType wrappers exist but their backing enum classes don't — this surface creates them.

Church/organization affiliate flows (FR-AFF-17x) additionally depend on the `organizations` entity reaching a usable state (roadmap **M3-04**, `organisations` surface) — see `srs.md` §11 Q5.

## Companion docs

Platform: [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (§5.17 FR-AFF, §4 FR-FAITH-6) · [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§3 domain model — `affiliates`/`referrals`/`attribution_logs`, §7 ledger design) · [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md) (§4, §9 extensibility contract, ADR-004, ADR-007)
Project: [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) (§4.2 post-MVP inventory) · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) (§3.1/§3.2 existing stubs) · [`../../projects/sowing-me/mvp-roadmap.md`](../../projects/sowing-me/mvp-roadmap.md) (M3-01)
Related surfaces: [`payments`](../payments/README.md) (`transactions` ledger this surface writes `fee`/`payout` rows into) · [`payouts`](../payouts/README.md) (scheduled payout run that sweeps affiliate revenue share) · [`live-streaming`](../live-streaming/README.md) (precedent for a surface with its own `architecture.md` and an internal-hook API pattern this surface's `/internal/affiliate/conversion` follows)
Standards: [`../../standards/database.md`](../../standards/database.md) · [`../../standards/migrations.md`](../../standards/migrations.md) · [`../../standards/pagination.md`](../../standards/pagination.md) · [`../../standards/sensitive-data-access.md`](../../standards/sensitive-data-access.md)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md) and [`payments`](../payments/README.md). Because this surface has no `architecture.md`, any system-level impact instead flags a revision to the platform `architecture.md` (per its §9 rule: a new surface may not require altering the ledger spine, auth model, or app topology without an ADR).
