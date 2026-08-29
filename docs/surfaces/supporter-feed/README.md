# Surface: Supporter Feed

Logged-in supporter home for Sowing.me — an aggregated feed of posts from subscribed creators, subscription management (view/upgrade/downgrade/cancel), billing history, and saved/bookmarked posts. Build-ready for **M2** (roadmap **S8**).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, entitlement filtering, caching, frontend.

No `architecture.md` — this surface has no system-level topology of its own; it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md) in full (stateless `SowingMeApi` + `SowingMeJs`, MariaDB, Memcached).

## Status
Draft v0.1 (2026-08-27). Build-ready: `creator-profile` (S3), `subscription-tiers` (S4), `content-posts` (S5), and `payments` (S7) are prerequisite surfaces this one reads from and delegates to — see `technical-spec.md` §1.

## Keeping these in sync
The two docs move together — a requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
