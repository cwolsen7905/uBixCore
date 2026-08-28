# Surface: Content & Posts

Creator posts for Sowing.me — text/image/audio/external-embed-video content, tier-gated visibility, series/collections, drafts + scheduled publish + edit history. The data model is shaped so devotional/Scripture-native content types (platform FR-FAITH-5) arrive at M3+ as additive `PostTypeEnum` values, never new tables. MVP surface (roadmap **S5**, milestone **M1**).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs realising platform FR-CONT, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, entitlement consumption, media attachment flow, the faith-type extensibility proof.

No `architecture.md` — this surface adds no system design beyond the platform ADS ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)); it is a plain layered CRUD surface consuming the platform's `EntitlementService` and `MediaStorageInterface` seams.

## Relationship to other surfaces

- **[`../media-storage/README.md`](../media-storage/README.md)** — owns the uploaded bucket object, validation, derivatives, and signed URLs behind every `post_media` row. This surface never talks to S3 directly.
- **`subscription-tiers`** (S4) — supplies `tiers`/`min_tier_id` ordering that `EntitlementService` resolves against.
- **`comments-community`** (M3, FR-COMM) — will gate comments on a post using the same `EntitlementService.resolve` call this surface uses for the post body; no new gating logic when it lands.
- **[`../live-streaming/README.md`](../live-streaming/README.md)** — its VOD replay lands as a `posts` row (`vod_post_id`) inheriting the stream's visibility; that surface is a producer into this one's table, not the reverse.

## Status

Draft v0.1 (2026-08-27). Prerequisites: `creator-profile` (S3), `subscription-tiers` (S4) — see roadmap M1-04/M1-05.

## Keeping these in sync

The two docs move together — a requirement change in `srs.md` updates the traceability in `technical-spec.md` and re-versions both via their Document control tables. Roadmap status flips in the same commit as the code.
