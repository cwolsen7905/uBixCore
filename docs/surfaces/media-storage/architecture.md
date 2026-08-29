# Media Storage & Delivery — Architecture & Design (SDD)

**Surface:** `media-storage` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`technical-spec.md`](technical-spec.md) (how in code) · [`README.md`](README.md)

> **How as a system.** Upload/delivery topology, the S3-provider decision, and the signed-URL + CDN flow. Authored against the SDD role in [`../../standards/web-development-delivery-framework.md`](../../standards/web-development-delivery-framework.md). This surface earns a short ADS beyond the platform ADS ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md) §6 "Media subsystem") because it fixes the concrete bucket topology, CDN placement, and dual upload path (browser-presigned vs. server-direct) that `content-posts` and `live-streaming` both depend on.

## 1. System context

```
┌ Creator browser ─────────┐              ┌ Any viewer (public or entitled) ─────┐
│ 1. request presign        │              │ 3a. public media → CDN URL           │
│ 2. PUT bytes direct       │              │ 3b. gated media → signed URL (60s)   │
└─────────┬─────────────────┘              └───────────────▲───────────────▲──────┘
          │ HTTPS PUT (bucket)                              │ HTTPS         │ HTTPS
          ▼                                                 │               │
   ┌───────────────────── k3s cluster (ws-<env>) ────────────────────────────────┐
   │  ┌ SowingMeApi (Slim) ┐        ┌ S3-compatible bucket ┐                     │
   │  │ presign/confirm     │──put──▶│ originals             │◀────cache-miss────┤ CDN
   │  │ MediaQuotaService   │        │ derivatives (thumbs)  │                     │
   │  │ EntitlementService  │◀─read──│                        │                     │
   │  │ signedReadUrl/       │       └───────────┬────────────┘                     │
   │  │ publicUrl selection  │                   │                                  │
   │  └──────────┬───────────┘                   │                                  │
   │             │ direct put (no presign)        │                                  │
   │  ┌ live-streaming VOD job (M3) ┐             │                                  │
   │  │ mux → putObjectDirect()      │─────────────┘                                  │
   │  └───────────────────────────────┘                                              │
   └──────────────────────────────────────────────────────────────────────────────────┘
```

Same principle as the live-streaming ADS: **the API owns all policy** (who may upload, who may read, quota, entitlement); the bucket and CDN own only bytes. Nothing outside `S3MediaStorageService` (technical-spec §5) imports the storage vendor's SDK.

## 2. Technology decision — S3-compatible provider

**Primary: in-cluster MinIO for `dev`/`staging`. AWS S3 for `main`/production traffic, same `MediaStorageInterface` either way.**

| Option | Ops weight | Cost | CDN fit | Verdict |
|---|---|---|---|---|
| **AWS S3** | None (managed) | Pay-per-GB + egress | Native CloudFront, or any CDN via origin pull | **Chosen for `main`** — AWS SDK v3 is already a dependency (brief §3.2); zero new infra to run |
| **In-cluster MinIO** | Low (one StatefulSet, S3-compatible API) | Existing k3s capacity only | Works behind any CDN doing origin-pull over HTTPS | **Chosen for `dev`/`staging`** — no AWS spend for iteration; identical API surface so `S3MediaStorageService` needs no branching, only endpoint/credential config per environment |

Because `MediaStorageInterface` is vendor-agnostic and the AWS SDK v3 S3 client itself speaks the S3 API against any compatible endpoint, **one implementation class serves both environments** — only the endpoint URL, region, and credentials differ per env config (k8s Secret/uBixVault, per root `CLAUDE.md` family tooling). This mirrors the live-streaming ADS's SRS Q2 default ("inherit `media-storage` decision") — this is that decision.

**Rejected:** a bespoke object-storage service (Ceph/RGW) — no operational need at Sowing.me's scale to run our own cluster storage beyond MinIO's simplicity; revisit only if egress cost or object count outgrows a managed S3 bucket.

## 3. Two upload paths, one interface

| Path | Caller | Mechanism | Used by |
|---|---|---|---|
| **Presigned, browser-direct** | End-user browser | `presignUpload()` → client `PUT`s bytes straight to the bucket, bypassing the app pod entirely | `content-posts` creator uploads (M1) |
| **Direct, server-side** | A PHP job/console command running in-cluster | `putObjectDirect()` → the AWS SDK uploads a local file (already on a PVC/local disk) using the same credentials, no presigning needed since the caller *is* trusted server code | `live-streaming`'s VOD mux job (M3, technical-spec §4.3) |

Both paths converge on the same `media_assets` row and lifecycle (technical-spec §3) — a VOD recording is not a special case in the data model, only in which method populates it. This is the concrete mechanism behind FR-MED-13 and the reason this surface does not need a second table or a second interface when live-streaming lands.

## 4. Signed-URL + CDN delivery flow

### 4.1 Public media (cacheable)
```
Viewer → SowingMeJs renders <img src="publicUrl">
publicUrl → CDN edge (cache-miss) → bucket origin → cached at edge → served
```
No app-pod involvement per request once cached. `publicUrl()` (technical-spec §5) returns a stable, deterministic CDN-fronted URL derived from `bucket_key` — safe to cache indefinitely at the edge since public content's URL never needs to expire or vary per viewer.

### 4.2 Gated media (signed, non-cached)
```
Viewer → SowingMeApi: GET /posts/{id}  (content-posts surface)
SowingMeApi: EntitlementService.resolve(user, post) → allowed
SowingMeApi → media-storage: signedReadUrl(mediaAssetId, ttl=60s)
Response includes the signed URL; viewer's browser fetches it directly from the bucket (not proxied through the app)
Bucket: validates the signature/expiry itself (S3 presigned-GET semantics) — no additional app-side check needed per byte served
```
The signed URL bypasses the CDN by construction (a distinct, non-cacheable path/query per request, per-viewer) — mirrors the live-streaming architecture's `readToken` re-validation pattern, but simpler: S3's own presigned-URL signature verification replaces a custom auth-hook round trip, since there is no separate media-server process to ask (unlike MediaMTX in live-streaming).

## 5. Deployment topology (k3s)

- **`dev`/`staging`:** MinIO `StatefulSet` in the `ws-<env>` namespace, `ClusterIP` service; `SowingMeApi` reaches it over cluster DNS. No public ingress to MinIO itself — all reads/writes go through presigned URLs the API mints, which MinIO's S3-compatible signing accepts directly from the client's browser via a `NodePort`/ingress route scoped only to the bucket's S3 API port.
- **`main`:** AWS S3 bucket (per-environment bucket name), no cluster-hosted storage; `SowingMeApi` pods carry IAM credentials via k8s Secret/uBixVault.
- **CDN:** origin-pull CDN (provider TBD — any CDN capable of HTTPS origin-pull works, since the origin is a standard S3-compatible HTTPS endpoint) fronts `publicUrl()`'s bucket path only; gated paths are never registered as CDN origins.
- **CI gates:** `phpunit`, `phpstan`, `phpcs`, JS build/test before deploy, per platform ADS §3 — MinIO runs as a CI service container for the presign→confirm integration tests (technical-spec §10).

## 6. Failure modes

| Failure | Behaviour | Mitigation |
|---|---|---|
| Presign issued but client never uploads | Asset row stays `pending` forever | Periodic GC job expires `pending` rows past presign TTL; never counted toward quota (FR-MED-8) |
| Confirm's type/checksum check fails | Asset marked `failed`, bucket object orphaned | Periodic GC job deletes `failed` assets' bucket objects |
| Derivative job crashes mid-run | Original asset unaffected (`validated`); derivative row `failed` or missing | Job is idempotent/retryable; original remains servable without a thumbnail (FR-MED-12) |
| Bucket unreachable | Presign/confirm/signed-URL calls fail fast with a typed error | `content-posts` degrades gracefully — a post publishes with pending/missing media rather than blocking the whole post (platform NFR-AVAIL: no subsystem SPOF for core browsing) |
| CDN cache serves a stale public asset after replacement | Old bytes visible briefly | Public assets are treated as immutable per `bucket_key` (a re-upload gets a new key, never overwrites) — no invalidation needed |

Fail-closed on entitlement is inherited from `EntitlementService` (platform ADR-008): if a caller cannot resolve entitlement, no `signedReadUrl()` call is made at all — this surface never receives an ambiguous request to adjudicate.

## 7. Capacity notes

- Ingest bandwidth is creator-upload-driven (low volume, image/audio only at M1) — no special provisioning beyond normal API pod scaling.
- Egress is delivery-driven and CDN-absorbed for public content (platform NFR-SCALE); gated content's signed-URL egress is per-viewer but still served directly from the bucket, not proxied through an app pod, so it scales independently of `SowingMeApi` pod count.
- No transcoding CPU cost at M1 (images/audio only); the live-streaming surface's own ffmpeg pipeline (its architecture §5) remains outside this surface's capacity envelope even after it starts calling `putObjectDirect()`.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial architecture/SDD. |
