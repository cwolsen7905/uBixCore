# Surface: Explore

Public discovery for Sowing.me — featured/trending creators, categories and faith topics/denominations, search creators by name/category, and creator cards with entry-tier price and a subscribe CTA. Build-ready for **M2** (roadmap **S12**).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, caching, frontend.

No `architecture.md` — this surface has no system-level topology of its own; it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md) in full.

## Status
Draft v0.1 (2026-08-27). Build-ready: depends on `creator-profile` (S3) and `subscription-tiers` (S4).

**On the existing shell:** `app/SowingMeJs/src/routes/explore/+page.svelte` (392 lines) is the consumer-facing shell this surface makes real — it already has the right shape (topic filter chips, featured/popular/new creator rails) with hardcoded placeholder data and `ui-avatars.com` images. It carries **no** neptune leftovers itself. The **`SowingMeAdminJs`** app has a separate `explore` route that *is* a neptune leftover (per `docs/projects/sowing-me/brief.md` §5) — that one is an admin-console (S11) concern, unrelated to this surface, and is out of scope here.

## Keeping these in sync
The two docs move together — a requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
