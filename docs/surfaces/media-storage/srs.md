# Media Storage & Delivery — Software Requirements Specification (SRS)

**Surface:** `media-storage` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M1 · **Prerequisites:** none of its own (S3-compatible endpoint per charter §10); consumed by `content-posts` (S5) and, at M3, `live-streaming`'s VOD path
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`architecture.md`](architecture.md) · [`README.md`](README.md)
**Upstream:** platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.6 (FR-MED) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) §4.1 (S6), §4 Q4 · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface realises platform SRS domain **FR-MED** (§5.6) at implementation depth and is the sole implementer of the platform TDS §5 `MediaStorageInterface` seam. It does not restate the platform SRS/TDS/ADS.

## 1. Purpose

Give every Sowing.me surface that handles a file (images and audio at M1 per charter Q4; live-streaming VOD recordings at M3) one shared, S3-compatible upload-and-delivery pipeline: direct-to-bucket presigned uploads (no file bytes touch an app pod), type/size/checksum validation, async image derivatives, signed expiring URLs for gated media minted only after an `EntitlementService` check, a CDN in front of public/cacheable media, and per-creator quotas.

## 2. Scope

**In scope:** `MediaStorageInterface` (S3-compatible implementation), presigned direct-to-bucket upload issuance and confirmation, type/size/checksum validation, `media_assets` lifecycle, async image-derivative generation (thumbnails), signed expiring read URLs, CDN delivery for public media, per-creator storage quotas.

**Out of scope (this surface):** what a media asset is *attached to* (`post_media` belongs to `content-posts`; live recording muxing belongs to `live-streaming`), video transcoding/ABR (no uploaded video at M1 — charter Q4; live-streaming's own ffmpeg pipeline is a separate surface concern), virus scanning beyond MIME/magic-byte type validation (flagged as an open question, §11).

## 3. Context — what this surface must serve

Two consumers exist or are planned, and this surface's contract must not be redesigned when the second lands:

| Consumer | What it needs | When |
|---|---|---|
| `content-posts` | Upload images/audio for a post; signed URL per `post_media` row, gated identically to the post | M1 |
| `live-streaming` (VOD path) | Take a recorded broadcast (already muxed to MP4 by that surface), store it via this pipeline, and hand back a `media_asset_id` that becomes a `posts`/`post_media` row inheriting the stream's tier gating | M3 |

Per the platform TDS §8 and the live-streaming technical-spec §5 ("Recording ... hands it to the `media-storage` upload pipeline"), the live surface calls this surface's upload/confirm contract from a server-side job — it does not reinvent storage.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Media asset** | One stored object (an uploaded image/audio file, or a live-streaming recording) tracked end-to-end: pending → uploaded → validated → (derivatives) → ready, or failed. |
| **Presigned upload** | A time-limited URL + form fields the client (or a server-side job) uses to `PUT`/`POST` bytes directly to the bucket, bypassing app pods. |
| **Signed read URL** | A time-limited, single-object URL minted only after an entitlement check, for gated (non-public) media. |
| **Derivative** | A generated variant of an original asset (image thumbnail sizes at M1). |
| **Quota** | A per-creator cap on total stored bytes. |

## 5. Personas & primary user stories

- **Creator.** "As a creator, I attach a photo to my post; the upload goes straight from my browser to storage, I see a progress bar, and a thumbnail appears once it's processed."
- **Supporter.** "As an entitled subscriber, images/audio in a gated post load for me; I never see a link that works for someone who isn't subscribed, and it doesn't stay valid forever if I copy it."
- **Platform (system).** "As the live-streaming surface, when a broadcast ends I hand the recorded file to media-storage and get back an asset id I can attach to a VOD post — I don't implement my own S3 client."
- **Admin.** "As an admin, I can see how much storage a creator is using against their quota (surfaced via `creator-dashboard`/`admin-console`, out of scope here beyond the data)."

## 6. Functional requirements

### 6.1 Upload (FR-MED-1, expanded)
- **FR-MED-1** Uploads are **direct-to-bucket**: the client requests a presigned upload from `SowingMeApi`, then `PUT`s bytes straight to the S3-compatible bucket. No file bytes are proxied through an app pod.
- **FR-MED-4** The presign request declares content type and byte size up front; the server rejects unsupported types and oversize requests **before** issuing a presigned URL (fail fast, no wasted upload).
- **FR-MED-5** After the client confirms upload completion, the server performs a `HEAD`/metadata check against the bucket: actual size matches the declared size (within tolerance), actual content type matches (magic-byte sniff, not just the client-supplied MIME string), and a checksum (SHA-256) is computed/verified. A mismatch marks the asset `failed` and it is never attached to content.
- **FR-MED-6** Supported types at M1 (charter Q4 — images + audio; video by external embed, not upload): images (`image/jpeg`, `image/png`, `image/webp`), audio (`audio/mpeg`, `audio/mp4`, `audio/wav`). The type allowlist is server-side and centrally defined, not per-endpoint.

### 6.2 Validation & quotas (FR-MED-7)
- **FR-MED-7** Each creator has a storage quota (bytes). A presign request is rejected if it would exceed the creator's remaining quota; the check happens before the presigned URL is issued (not after upload, to avoid wasted bandwidth).
- **FR-MED-8** Quota consumption is recomputed from confirmed (`validated`+) asset sizes, not from pending/failed uploads, so an abandoned or rejected upload never permanently consumes quota.

### 6.3 Delivery — signed URLs & CDN (FR-MED-2, expanded)
- **FR-MED-2** Gated media is served exclusively via **short-lived, single-asset signed URLs**, minted only after the same `EntitlementService` check that gates the owning post (or stream). A signed URL is never longer-lived than necessary for immediate playback/display and is never cached at a shared CDN edge.
- **FR-MED-9** Public/cacheable media (public-visibility posts' images/audio) is served through a **CDN** in front of the bucket, using stable public URLs — no per-request signing overhead for content anyone may see.
- **FR-MED-10** An asset's original visibility classification (public vs. gated) is derived from what it is attached to (a post's/stream's visibility), not stored redundantly as its own independent flag that could drift out of sync — the delivery decision is made at read time from the owning resource's current visibility.

### 6.4 Derivatives (FR-MED-3, expanded)
- **FR-MED-3** Image uploads get async-generated derivatives (thumbnail sizes) after the original validates successfully; audio and (future) video are delivered as-is at M1 — no transcoding.
- **FR-MED-11** Derivative generation runs as a job (platform TDS §9), never inline in the upload-confirmation request; the original is usable (full-size) immediately on validation, with derivatives appearing shortly after.
- **FR-MED-12** A derivative failure does not fail the original asset — the original remains usable without its thumbnail if generation errors, logged for retry/investigation.

### 6.5 Serving other surfaces (FR-MED-13)
- **FR-MED-13** The upload-confirm and signed-URL-mint operations are usable by a server-side caller (e.g. the live-streaming VOD job), not only by an end-user's browser presign request — the interface is the same `MediaStorageInterface` either way; only who calls it differs.

## 7. Non-functional requirements

- **NFR-MED-1 Security.** Presigned upload URLs are short-lived and single-use in intent (scoped to one object key); signed read URLs are short-lived (target: on the order of a minute — matches the live-streaming surface's read-token precedent) and re-derived per read, never reused across viewers. No credentials are ever exposed to the client (platform NFR-SEC).
- **NFR-MED-2 Privacy.** Media tied to a gated post/stream is never reachable via a guessable or long-lived URL; bucket keys are opaque (not sequential/predictable).
- **NFR-MED-3 Performance.** Presign issuance and signed-URL minting complete within the platform's p95 < 300 ms target (excluding the client's own upload/download transfer time).
- **NFR-MED-4 Scale.** Delivery bandwidth for public media is CDN-offloaded, never served directly off an app pod (platform NFR-SCALE) — mirrors the live-streaming surface's egress principle.
- **NFR-MED-5 Cost.** Self-hosted S3-compatible storage on existing infra (charter §10); no per-request SaaS media processing service required for M1's image-only derivatives.
- **NFR-MED-6 Extensibility.** Adding a new supported MIME type or a new derivative variant is a config/allowlist change, not a schema change; the same interface must serve live-streaming's VOD recordings at M3 without a contract change (platform NFR-EXT).
- **NFR-MED-7 Standards.** DataType/Payload/DTO/Repository, PHPStan max, custom sniffs, strict PHPUnit; every table via `bin/ubix migrate:*`; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **Upload widget** (SvelteKit, `SowingMeJs`): file picker, client-side type/size pre-check (UX only — server re-validates), direct `PUT` to presigned URL, progress, confirm call.
- **`SowingMeApi`**: presign, confirm, asset status, delete.
- **S3-compatible bucket**: object storage, per [`architecture.md`](architecture.md).
- **CDN**: public media delivery, per [`architecture.md`](architecture.md).
- **Internal consumer**: `live-streaming`'s VOD job calls the same upload/confirm contract server-side (no HTTP hop required — see [`architecture.md`](architecture.md) §3).

## 9. Constraints & assumptions

- S3-compatible storage endpoint available before build starts (charter §10 dependency).
- AWS SDK is already a `composer.json` dependency (brief §3.2) — no new vendor dependency to add.
- No card data or other PCI-scope data ever passes through this surface — irrelevant to media, noted only for completeness.
- Video upload is explicitly out of scope at M1 (charter Q4); this surface's contract must not preclude adding it later without a redesign (NFR-MED-6).

## 10. Acceptance criteria (surface DoD)

1. A creator uploads a JPEG directly to the bucket via a presigned URL; the server confirms it, validates type/size/checksum, and a thumbnail appears within a short delay.
2. An oversize or wrong-type upload is rejected at presign time, before any bytes are sent.
3. A signed URL for a gated image is only mintable after the requesting viewer passes `EntitlementService`; the URL expires quickly and is not the same URL served to a different viewer.
4. A public post's image loads via a stable CDN URL, not a per-request signed URL.
5. A creator who exceeds their quota is blocked from further presign requests with a clear error.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | S3-compatible provider — AWS S3 proper, or in-cluster MinIO (mirrors live-streaming SRS Q2)? | In-cluster MinIO for dev/staging, S3 for `main` (see [`architecture.md`](architecture.md) §2) | build |
| Q2 | Virus/malware scanning beyond MIME/magic-byte checks — needed at M1? | Not at M1 (type/size/checksum only); revisit if user-generated-content risk grows | FR-MED-5 |
| Q3 | Default per-creator quota size and whether it varies by tier level (creator's own subscription plan, not supporter tiers)? | Flat default quota for all creators at M1; per-plan tiering is a product task | FR-MED-7 |
| Q4 | Signed-URL TTL exact value | 60 seconds (matches live-streaming's `readToken` precedent) | FR-MED-2/NFR-MED-1 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability". Platform FR-MED-1..3 are the parent requirements this surface's FR-MED-4..13 realise at implementation depth.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
