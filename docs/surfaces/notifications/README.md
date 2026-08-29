# Surface: Notifications

The single fan-out seam every other Sowing.me surface calls to reach a user: email (Symfony Mailer) + in-app, per-user preferences, digesting, rate-limiting and dedup. Post-MVP (roadmap **M3-02**).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: `notifications`/`notification_prefs` schema, `NotifierInterface`, async jobs, API.

This surface has no `architecture.md` of its own — it inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md) (see ADS §6 "Notification subsystem", §9 extensibility row). Nothing here needs its own system-level design beyond what the platform ADS/TDS already commit to (async jobs, `NotifierInterface`).

## Status
Draft v0.1 (2026-08-27). **Complete spec; several trigger sources are unbuilt.** `new_post` and `new_subscriber` triggers exist today (content-posts, subscription-tiers). `payout` and `tip_gift` triggers depend on `payments`/`payouts` (M2, built). `went_live` depends on [`live-streaming`](../live-streaming/README.md) (M3, not yet built). `prayer_response` depends on prayer requests (FR-PRAY, M3+, not yet built). This surface's own tables, `NotifierInterface`, jobs, and API can be built now; the unbuilt triggers simply have no caller until their surface lands.

## Keeping these in sync
The two docs move together, per `ubixcore/CLAUDE.md` → *Surface documentation*. A requirement change in `srs.md` updates the traceability in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
