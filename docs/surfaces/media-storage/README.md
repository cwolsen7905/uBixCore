# Surface: Media Storage & Delivery

The shared upload-and-delivery pipeline for Sowing.me: S3-compatible presigned direct-to-bucket uploads, type/size/checksum validation, async image derivatives, signed expiring URLs for gated media (minted only after an `EntitlementService` check), and CDN delivery for public media. MVP surface (roadmap **S6**, milestone **M1**) — and the seam `live-streaming`'s VOD path reuses at M3 rather than inventing its own storage.

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs realising platform FR-MED, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: `MediaStorageInterface` contract, data model, API, derivative job, quota enforcement.
3. [`architecture.md`](architecture.md) — **how as a system**: bucket topology, the S3-provider decision (MinIO dev/staging, S3 prod), the two upload paths (browser-presigned vs. server-direct), signed-URL + CDN flow, failure modes.

## Relationship to other surfaces

- **[`../content-posts/README.md`](../content-posts/README.md)** — the primary M1 consumer. Owns `post_media` (the attachment/ordering); this surface owns the bucket object, validation, derivatives, and URL minting behind each `post_media.media_asset_id`.
- **[`../live-streaming/README.md`](../live-streaming/README.md)** — its recording→VOD path (technical-spec §5 "Recording") calls this surface's server-side `putObjectDirect()` upload path (architecture.md §3) to store a finished broadcast, then attaches the resulting `media_asset_id` to a `posts`/`post_media` row exactly as `content-posts` does. No separate storage pipeline is built for live video.
- **`subscription-tiers`** (S4) — indirectly: gating decisions this surface's callers make before requesting a signed URL depend on tier data, but this surface itself never resolves entitlement — it only consumes the caller's already-resolved decision.

## Status

Draft v0.1 (2026-08-27). No prerequisites of its own beyond an available S3-compatible endpoint (charter §10); `content-posts` depends on this surface, not the reverse.

## Keeping these in sync

The three docs move together — a requirement change in `srs.md` updates the traceability in `technical-spec.md`, any topology impact in `architecture.md`, and re-versions all three via their Document control tables. Roadmap status flips in the same commit as the code.
