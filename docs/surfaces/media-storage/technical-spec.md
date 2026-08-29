# Media Storage & Delivery — Technical Specification

**Surface:** `media-storage` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`architecture.md`](architecture.md) (system design) · [`README.md`](README.md)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) · platform [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (TDS §5 `MediaStorageInterface`, §8 media pipeline, §12 per-surface template)

> **How in code.** This spec is the concrete implementation of the platform TDS §5 `MediaStorageInterface` seam. Every table lands via `bin/ubix migrate:*` per [`../../standards/migrations.md`](../../standards/migrations.md). Topology, network flow, and the S3-provider decision are in [`architecture.md`](architecture.md); this doc covers data model, API, and code layout.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| `MediaStorageInterface` | `php/Ubix/Service/Media/MediaStorageInterface.php` | Vendor-agnostic contract: presign, confirm, signed read URL, delete |
| S3-compatible implementation | `php/Ubix/Service/Media/S3MediaStorageService.php` | AWS SDK v3-backed implementation (already a dependency — brief §3.2) |
| Media domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers) | `media_assets`, `media_derivatives`, `media_quotas` |
| Media API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Presign, confirm, status, delete |
| Derivative job | `Console/Command/` + k8s CronJob/Job | Async thumbnail generation (FR-MED-11) |
| Upload widget | `app/SowingMeJs/` shared component in `js/Ubix/` | File picker, direct `PUT`, progress, confirm call |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. FK to existing `creators`.

### 2.1 `media_assets`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | referenced by `content-posts`' `post_media.media_asset_id` and, at M3, by `live-streaming`'s VOD attach step |
| `creator_id` | BIGINT FK → `creators.id` | uploader/owner; quota accrues here |
| `kind` | ENUM(`image`,`audio`) | `MediaKindEnum` — **additive**: M3 live-streaming VOD adds a `video` value, no schema change (SRS NFR-MED-6) |
| `mime_type` | VARCHAR(100) | server-verified via magic-byte sniff, not trusted from the client (FR-MED-5) |
| `bucket_key` | VARCHAR(255) UNIQUE | opaque, random — never sequential/predictable (SRS NFR-MED-2) |
| `byte_size` | BIGINT | declared at presign, verified at confirm |
| `checksum_sha256` | CHAR(64) NULL | populated at confirm (FR-MED-5) |
| `status` | ENUM(`pending`,`uploaded`,`validated`,`failed`,`deleted`) | `MediaAssetStatusEnum`; lifecycle §3 |
| `failure_reason` | VARCHAR(255) NULL | set when `status=failed` |

### 2.2 `media_derivatives`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `media_asset_id` | BIGINT FK → `media_assets.id` | |
| `variant` | ENUM(`thumb_sm`,`thumb_md`,`thumb_lg`) | `MediaDerivativeVariantEnum` — image-only at M1 (FR-MED-3) |
| `bucket_key` | VARCHAR(255) UNIQUE | |
| `width` / `height` | INT | pixel dimensions |
| `status` | ENUM(`pending`,`ready`,`failed`) | a `failed` derivative never fails the parent asset (FR-MED-12) |

### 2.3 `media_quotas`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` UNIQUE | one row per creator |
| `max_bytes` | BIGINT | default flat quota at M1 (SRS Q3) |
| `used_bytes` | BIGINT | recomputed from `validated`+ assets only (FR-MED-8), not from `pending`/`failed` |

DataTypes: `MediaKindEnum`, `MediaAssetStatusEnum`, `MediaDerivativeVariantEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers.

## 3. Asset lifecycle state machine

```
pending ──client PUTs bytes to presigned URL──► uploaded ──server confirm: type/size/checksum OK──► validated ──derivative job──► validated (+ derivatives ready)
   │                                                │                                    │
   │ (presign expires, never uploaded)              │ confirm fails validation           │ creator/system deletes
   ▼                                                ▼                                    ▼
 (row stays pending; excluded from quota,       failed (excluded from quota,          deleted (bucket object removed;
  garbage-collected by a periodic job)           never attachable to content)          quota decremented)
```

`pending`/`failed` rows never count toward `media_quotas.used_bytes` (FR-MED-8). Only `content-posts` (or `live-streaming`'s VOD attach step) may reference a `validated` asset — attaching a `pending`/`uploaded`/`failed` asset is rejected by that consumer's Payload validation.

## 4. API surface (`SowingMeApi`)

All routes use existing session auth + role/ownership middleware (the requesting creator must own the asset). Payloads use the DataType/Payload validation system.

### 4.1 Creator (end-user upload path)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/creator/media/uploads` | Request a presigned upload: body `{ mimeType, byteSize }` → validates type/size/quota **before** presigning (FR-MED-4,7), returns `{ mediaAssetId, uploadUrl, fields, expiresAt }` | FR-MED-1,4,6,7 |
| POST | `/creator/media/uploads/{id}/confirm` | Client signals upload complete → server `HEAD`s the object, verifies size/type/checksum, transitions `uploaded`→`validated` or `failed`, enqueues derivative job if image | FR-MED-5,11 |
| GET | `/creator/media/{id}` | Asset status (for polling derivative readiness) | — |
| DELETE | `/creator/media/{id}` | Soft-delete (marks `deleted`, removes bucket object, decrements quota); rejected if still attached to a published post (ownership check delegated to the calling surface) | FR-MED-8 |
| GET | `/creator/media/quota` | Current usage vs. `max_bytes` | FR-MED-7,8 |

### 4.2 Internal — read-URL minting (called by consuming surfaces, not exposed as a public route)
`MediaStorageInterface::signedReadUrl(mediaAssetId, ttlSeconds): string` is a **PHP service call**, not an HTTP endpoint — `content-posts` and `live-streaming` run in the same monolith (platform ADR-001) and call it in-process, after their own `EntitlementService.resolve` check succeeds (FR-MED-2). No internal HTTP hop exists for this because, unlike the live-streaming media server (a separate Go process), every caller here is PHP code in `php/Ubix/`.

### 4.3 Internal — server-side upload (live-streaming VOD, M3)
`MediaStorageInterface::presignUpload()` / `confirmUpload()` are the same two calls the live-streaming VOD sidecar/Job uses server-side (no presigned client URL needed there — it uploads directly via the AWS SDK using the same interface's non-presigned put-object path, see [`architecture.md`](architecture.md) §3). This is FR-MED-13: one interface, two callers (browser via presign, server via direct put).

## 5. `MediaStorageInterface` contract

```php
interface MediaStorageInterface
{
    public function presignUpload(CreatorId $creatorId, MimeType $mimeType, ByteSize $byteSize): PresignedUpload;
    public function confirmUpload(MediaAssetId $mediaAssetId): MediaAsset; // validates, transitions status
    public function putObjectDirect(CreatorId $creatorId, MimeType $mimeType, string $localPath): MediaAsset; // server-side caller path (FR-MED-13)
    public function signedReadUrl(MediaAssetId $mediaAssetId, int $ttlSeconds): SignedUrl;
    public function publicUrl(MediaAssetId $mediaAssetId): CdnUrl; // only for assets attached to public-visibility content
    public function delete(MediaAssetId $mediaAssetId): void;
}
```

`PresignedUpload`, `MediaAsset`, `SignedUrl`, `CdnUrl` are DTOs per `complete-php-guide.md`. The S3-compatible implementation (`S3MediaStorageService`) is the only class importing the AWS SDK; every consumer (`content-posts`, `live-streaming`) depends on the interface, wired via PHP-DI — the platform's swappable-vendor rule (platform TDS §5).

## 6. Delivery decision (signed URL vs. CDN)

The **calling surface**, not this one, knows a resource's current visibility (a post's `visibility`, a stream's `visibility`). The contract is:

- Caller resolves entitlement first (`EntitlementService.resolve`).
- If the owning resource is `visibility=public` **and** the viewer is any viewer (entitlement trivially passes) → caller requests `publicUrl()`, a stable CDN-fronted URL, cacheable.
- If gated (`subscribers`/`tier`) and the viewer is entitled → caller requests `signedReadUrl()` with a short TTL (SRS Q4 default 60 s), never cached at a shared edge.
- If not entitled → caller never calls this interface at all; it returns a teaser DTO with no media URL (mirrors `content-posts` technical-spec §5).

This keeps FR-MED-10 (visibility derived from the owning resource, not duplicated) true by construction — `media_assets` itself carries no visibility column.

## 7. Derivative generation (FR-MED-3/11/12)

- On `confirmUpload()` success for `kind=image`, the confirm handler enqueues a job (`Console/Command/Media/GenerateDerivatives`) rather than generating inline (platform TDS §9).
- The job reads the original, produces `thumb_sm`/`thumb_md`/`thumb_lg` (fixed max-dimension presets), writes each to its own `bucket_key`, and inserts `media_derivatives` rows with `status=ready`.
- A failure writes `status=failed` on the derivative row only; `media_assets.status` is untouched — the original remains fully usable (FR-MED-12).
- Audio and (M3) video get no derivative rows at M1 — delivered as the original asset only.

## 8. Quota enforcement (FR-MED-7/8)

`MediaQuotaService.reserve(creatorId, byteSize)` is called inside `presignUpload()` before a URL is issued: it reads `media_quotas.used_bytes` + `max_bytes`, rejects with a typed error if the request would exceed it. `used_bytes` is incremented only on successful `confirmUpload()` (transition to `validated`) and decremented on `delete()` — never on `pending`/`failed`, so quota cannot be exhausted by abandoned uploads (FR-MED-8).

## 9. Security & secrets

- Presigned upload URLs: short-lived, scoped to one `bucket_key`, generated per request — never reused.
- Signed read URLs: short-lived (default 60 s, SRS Q4), single-asset, re-derived on every read — never persisted or cached client-side beyond immediate use.
- Bucket credentials live in k8s Secrets/uBixVault (family tooling per root `CLAUDE.md`); only `S3MediaStorageService` reads them — no other class touches storage credentials.
- `bucket_key` values are opaque random ids, never derived from `media_assets.id` or any guessable sequence (SRS NFR-MED-2).

## 10. Testing

- **Unit:** `MediaKindEnum`/`MediaAssetStatusEnum`/`MediaDerivativeVariantEnum` DataTypes, Payload validation (type allowlist, size ceiling), quota reservation/decrement logic, lifecycle state-machine transitions, `signedReadUrl`/`publicUrl` selection logic (§6) as a pure function independent of any real S3 call. Non-container per the migration-test pattern.
- **Integration:** presign→confirm flow against a test bucket (MinIO in CI, per [`architecture.md`](architecture.md) §2); checksum/type mismatch produces `failed`; derivative job idempotency (re-running does not duplicate `media_derivatives` rows).
- **E2E (staging):** browser uploads an image via presigned URL → thumbnail appears → gated post's image is unreachable to a non-entitled viewer's signed-URL request.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 11. Requirement traceability

| FR | Realised by |
|---|---|
| FR-MED-1/4/6 | `POST /creator/media/uploads` presign path, type/size allowlist Payload validation |
| FR-MED-5 | `POST /creator/media/uploads/{id}/confirm`, checksum/magic-byte verification |
| FR-MED-7/8 | `media_quotas`, `MediaQuotaService.reserve` (§8) |
| FR-MED-2/9/10 | `signedReadUrl()`/`publicUrl()` (§6), no visibility column on `media_assets` |
| FR-MED-3/11/12 | `media_derivatives`, `Console/Command/Media/GenerateDerivatives` job (§7) |
| FR-MED-13 | `putObjectDirect()` server-side path, shared by `live-streaming`'s VOD job (§4.3) |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
