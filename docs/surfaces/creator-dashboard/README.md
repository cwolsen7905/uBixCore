# Surface: Creator Dashboard

Creator-facing earnings and performance home for Sowing.me — earnings from subscriptions/tips/gifts, subscriber count/list and churn, post/stream performance, and payout status. Build-ready for **M2** (roadmap **S9**, earnings half — post-management half shipped at M1).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, caching, frontend.

No `architecture.md` — this surface has no system-level topology of its own; it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md) in full.

## Status
Draft v0.1 (2026-08-27). Build-ready: depends on `payments` (S7), `payouts` (S10), `content-posts` (S5). Reads `live_streams` performance data from `live-streaming` (FR-LIVE, M3) — that section of the dashboard renders empty until that surface ships; see `technical-spec.md` §3.4.

**On the existing shells:** `app/SowingMeJs/src/routes/creator/dashboard/+page.svelte` (448 lines) and `app/SowingMeJs/src/routes/creator/library/+page.svelte` (298 lines) are the shells this surface makes real. Both currently render hardcoded placeholder data and `console.log` stubs for every action (`handleEditPage`, `handleCreatePost`, etc.) — no neptune leftovers, just unfinished Sowing.me UI.

## Keeping these in sync
The two docs move together — a requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
