# Sowing.me — Live Streaming Plan

**Status:** Planning · v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Phase:** Post-MVP (depends on `creator-profile`, `subscription-tiers`, `media-storage`, `payments` surfaces)

## 1. What this document is

A technology-selection and architecture plan for adding **creator live streaming** to Sowing.me: a creator goes live from the browser (no OBS required), supporters watch on the creator's page (optionally gated by tier), and the stream is optionally **restreamed** to YouTube/Facebook/etc. and **recorded** to VOD. It picks an open-source stack that runs in our existing Kubernetes cluster, and lays out the phases. It is not an SRS — when the surface is picked up it gets `docs/surfaces/live-streaming/{srs,technical-spec}.md` per the folder conventions.

## 2. Requirements

### Must
- **Browser ingest via WebRTC** (getUserMedia → WHIP). Camera + mic, screen share later. No desktop software needed for the "pastor with a laptop" persona.
- **Optional RTMP/SRT ingest** for creators who already use OBS / StreamYard / church AV hardware. Same stream key, either path.
- **Playback in the browser** for supporters, scaling to hundreds–thousands of concurrent viewers per stream at low ops cost.
- **Tier gating**: only entitled supporters can watch a subscriber-only stream.
- **Restream** to at least YouTube Live and Facebook Live via RTMP(S) push.
- **Record to VOD** so the stream becomes a post in the creator's library afterwards (ties into `media-storage`).
- **Runs in our k8s** (Rancher-managed, nginx ingress, nodes `kube-001..004`), deploys via GitLab CI like everything else, no per-minute SaaS bill.

### Should
- Sub-3s latency for the default path; sub-second is nice for a live chat/Q&A feel but not required for sermons/worship.
- Live chat alongside the stream (can be a plain WebSocket feature, independent of the media server).
- Adaptive bitrate (ABR) so mobile viewers on poor connections still get a picture.

### Won't (for now)
- Multi-guest / co-host video calls (SFU rooms). Note the migration path in §4 if this becomes a requirement.
- Native mobile broadcast apps.
- DRM.

## 3. Landscape — open-source candidates

All of these are containerised and run in Kubernetes. The distinction that matters is **broadcast server (1→many, protocol conversion)** vs **SFU (many↔many rooms)**.

| System | Type | Browser ingest | Playback | Restream out | Record | ABR / transcode | Ops weight | Notes |
|---|---|---|---|---|---|---|---|---|
| **MediaMTX** (Go, single binary) | Broadcast / protocol router | WHIP | WHEP, HLS/LL-HLS, RTSP, SRT | Yes — per-path `runOnReady` hook (ffmpeg push) or path forwarding | Yes, native (fMP4/MPEG-TS segments to disk) | No built-in transcoder → pair with ffmpeg sidecar for ABR ladder | **Very light** (~tens of MB RAM idle), single config file, HTTP control API + auth hooks | Best fit for "browser in, HLS out, ffmpeg does the rest". Auth via external HTTP hook lets our PHP API authorise publish/read. |
| **OvenMediaEngine** (C++) | Broadcast, low-latency focus | WebRTC (its own signalling + WHIP), RTMP, SRT | WebRTC (sub-second), LL-HLS, HLS | Yes — "Push Publishing" REST API to RTMP/SRT targets | Yes, via REST API | **Yes, built-in** transcoder + ABR ladders | Medium; more moving parts, its own player lib (OvenPlayer) | Strongest all-in-one if we want ABR + sub-second without ffmpeg glue. Heavier and less "Unix-y" than MediaMTX. |
| **SRS** (C++) | Broadcast | WHIP | WHEP, HLS, HTTP-FLV | Yes (forward) | Yes (DVR) | Transcode via ffmpeg config | Medium | Solid, big China-side community; docs are patchier for our use. |
| **LiveKit** (Go) | **SFU** (rooms) + Egress/Ingress services | Native SDK, WHIP (Ingress) | Native SDK (WebRTC only) | Via **Egress** service (RTMP push, HLS, MP4 to S3) — needs Redis + headless Chrome for composite | Via Egress | Simulcast (WebRTC-native) | **Heaviest**: server + Redis + Ingress + Egress, plus TURN | Right answer *only if* we need multi-guest rooms. Overkill for 1→many. |
| **Janus** / **mediasoup** | SFU building blocks | WebRTC | WebRTC | You build it | You build it | Simulcast | High (bespoke glue code) | Not considered further — too much custom code. |
| **Broadcast Box** (Pion) | Minimal WHIP→WHEP relay | WHIP | WHEP only | No | No | Simulcast passthrough | Trivial | Great demo, too thin for a product. |
| **Owncast** | Full single-streamer product | RTMP only | HLS | No | Partial | ffmpeg ladder | Light | No WebRTC ingest, single-tenant — wrong shape. |

Sources: [MediaMTX WebRTC docs](https://mediamtx.org/docs/publish/webrtc-servers), [OvenMediaEngine](https://github.com/OvenMediaLabs/OvenMediaEngine), [MediaMTX WHIP/WHEP field latency measurements](https://www.adaptnxt.com/blogs/mediamtx-whip-whep-latency-benchmarks-4-deployments), [Browser WHIP publishing with MediaMTX](https://dev.to/masonwritescode/ship-a-go-live-button-browser-whip-publishing-with-mediamtx-no-sdks-4kfp), [SRS vs OME vs NMS](https://www.pistack.xyz/posts/2026-06-04-srs-ovenmediaengine-node-media-server-self-hosted-streaming-guide/), [Owncast / MediaMTX / nginx-rtmp guide](https://www.pistack.xyz/posts/self-hosted-live-streaming-owncast-mediamtx-nginx-rtmp-guide-2026/), [LiveKit self-hosted alternatives](https://selfhostyourself.com/alternative-to/livekit-cloud).

## 4. Decision

**Primary: MediaMTX + ffmpeg**, with **OvenMediaEngine as the named fallback** if we hit the ABR/transcode wall and don't want to own the ffmpeg ladder ourselves.

Why MediaMTX:
1. **Fits the shape exactly** — one creator publishes, many watch. WHIP in from the browser, RTMP/SRT in from OBS, all on the same path name.
2. **Auth is an HTTP hook** (`authMethod: http`) → MediaMTX POSTs `{action: publish|read, path, query, ip, user, pass}` to **our Slim API**, which checks the stream key / supporter entitlement and returns 200/4xx. Tier gating, stream keys and viewer auth all stay in PHP where the domain lives. No JWT plumbing in a third system.
3. **Restream and record are config, not code** — `runOnReady: ffmpeg -i rtsp://localhost:8554/$MTX_PATH -c copy -f flv rtmps://a.rtmp.youtube.com/live2/KEY` per destination; `record: yes` with `recordPath` onto a PVC / S3 sync.
4. **Two-tier playback** keeps cost sane: **WHEP** (sub-second) for a bounded number of viewers, **LL-HLS** (2–4s) for everyone else, both served by the same process; HLS segments are plain HTTP so they can sit behind nginx/CDN caching.
5. **Ops**: single Go binary, single YAML, ~30 MB image. A StatefulSet with 1 replica handles a lot of concurrent creators before we need to think about sharding.

Known trade-offs (why OME stays on the bench):
- MediaMTX does **not** transcode. ABR ladder = an ffmpeg sidecar/Job per live stream re-publishing `_720p`, `_480p` variants plus a master playlist. That is a couple hundred lines of tooling we own. OME does this natively.
- WHEP viewer count per MediaMTX pod is CPU-bound on packetisation; we route mass audience to HLS anyway, so this is acceptable.
- If **multi-guest rooms** become a real requirement, none of the broadcast servers fit; that is a LiveKit project and it can *coexist* — LiveKit Egress can push RTMP into MediaMTX, so playback/gating/restream/recording built on MediaMTX is not thrown away.

## 5. Target architecture

```
Creator browser ──WHIP (HTTPS+ICE/UDP)──┐
OBS / church encoder ─RTMP/SRT──────────┤
                                        ▼
                     ┌──────────────────────────────────────┐
                     │ mediamtx (StatefulSet, ns ws-<env>)  │
                     │  path: live/<streamKey>              │
                     │  auth hook ─► SowingMeApi /internal/stream/auth
                     │  runOnReady ─► ffmpeg restream jobs  │
                     │  record ─► PVC ─► S3 (media-storage) │
                     └───────┬──────────────────┬───────────┘
                             │ WHEP             │ LL-HLS (HTTP)
                             ▼                  ▼
                  Supporters (low-latency)   Supporters via nginx ingress (+ CDN later)

SowingMeApi (Slim): stream keys, go-live state, entitlement checks, restream targets, VOD post creation
SowingMeJs (SvelteKit): creator "Go live" studio page (getUserMedia + WHIP), viewer player (WHEP → HLS fallback via hls.js)
```

### 5.1 Kubernetes specifics

- **Namespace/manifests**: `app/SowingMeStream/{dev,staging,main}-*.yaml` following the existing per-app pattern (Rancher `field.cattle.io/ports` annotations, nginx `ingressClassName`).
- **Workload**: `StatefulSet` (1 replica initially) with `hostNetwork: true` **or** a `hostPort` range for WebRTC media — ICE needs the UDP port range (`8189` mux port is enough with MediaMTX's `webrtcICEUDPMuxAddress`, so **one UDP port**, not a range) reachable directly on the node; nginx ingress can't proxy UDP. Pin to a node (`kube-00N`) with a `nodeSelector` and advertise that node's public IP via `webrtcAdditionalHosts`.
- **Ingress (TCP/HTTP only)**: `stream.sowing.me` → MediaMTX ports `8889` (WHIP/WHEP HTTP signalling) and `8888` (HLS). TLS terminated at ingress; nginx annotations for large body/long timeouts on the HLS path; enable proxy caching for `*.m4s` segments.
- **RTMP/SRT ingest**: `1935/tcp` and `8890/udp` via `hostPort` on the same node (or a `LoadBalancer` service if MetalLB is available on the cluster — confirm, open question §8).
- **TURN**: needed for creators behind symmetric NAT. Start without it (most home/church networks work with the UDP mux port); add `coturn` as a second Deployment if publish failures show up. Never rely on public STUN alone in prod.
- **Storage**: recording PVC (RWO, same node) + a sidecar/CronJob syncing finished recordings to S3 (AWS SDK already in `composer.json`).
- **Config**: `mediamtx.yml` as a ConfigMap; secrets (restream keys) come from the API at runtime, not baked in.
- **Observability**: MediaMTX exposes Prometheus metrics (`:9998`) and a control API (`:9997`) — keep both ClusterIP-only.

### 5.2 Data model (new migrations via `bin/ubix migrate:*`)

- `live_streams` — `id, creator_id, title, description, tier_visibility (public|subscribers|tier_id), stream_key (hash), status (idle|live|ended), started_at, ended_at, vod_post_id, created_at, updated_at`
- `live_stream_restream_targets` — `id, live_stream_id|creator_id, platform (youtube|facebook|custom), rtmp_url (encrypted), stream_key (encrypted), enabled`
- `live_stream_viewer_sessions` (optional, phase 3) — for concurrency metrics / earnings attribution.

Stream keys and restream secrets are **sensitive data** — follow `docs/standards/sensitive-data-access.md`.

### 5.3 API surface (SowingMeApi)

| Route | Purpose |
|---|---|
| `POST /creator/streams` | create stream (returns WHIP URL + stream key) |
| `POST /creator/streams/{id}/key/rotate` | rotate key |
| `GET /streams/{id}/playback` | returns WHEP/HLS URLs + short-lived read token for entitled viewers |
| `POST /internal/stream/auth` | MediaMTX auth hook (cluster-internal, shared secret header) — `publish` checks stream key; `read` validates the read token + entitlement |
| `POST /internal/stream/event` | MediaMTX `runOnReady`/`runOnNotReady` → flips `status`, fans out notifications, kicks off restream ffmpeg |
| `GET/PUT /creator/restream-targets` | manage restream destinations |

### 5.4 Frontend (SowingMeJs)

- **Creator studio** `/creator/live`: device picker, preview, "Go live" → `RTCPeerConnection` + `fetch(WHIP_URL, {method:'POST', body: sdp})`. No SDK required; a ~100-line `whip.ts` client in `js/Ubix/`. Show live viewer count, restream status, end-stream → "publish as post".
- **Viewer** on creator page: try WHEP first; fall back to LL-HLS via `hls.js` (Safari native). Gated by the playback endpoint.
- Repurpose/delete the neptune `broadcasting/*` route leftovers as part of this surface.

## 6. Phases

| Phase | Deliverable | Exit criterion |
|---|---|---|
| **0 — Spike** (1–2 days) | MediaMTX on `ws-dev`, WHIP from a browser on a laptop, WHEP + HLS playback, one ffmpeg restream to a test YouTube channel. No app code. | Latency and NAT behaviour measured from an outside network; decision MediaMTX vs OME confirmed in `status.md`. |
| **1 — Go live (MVP of the surface)** | Stream/key model, auth hook, creator studio page, public/subscriber-gated playback (HLS + WHEP), recording to PVC. | A creator can go live from the browser and an entitled supporter can watch; unentitled gets 403. |
| **2 — Restream + VOD** | Restream targets UI + ffmpeg jobs, recording → S3 → auto-created library post. | Stream appears on YouTube simultaneously; recording is a post within minutes of ending. |
| **3 — Scale & polish** | ABR ladder (ffmpeg sidecar or switch to OME), CDN in front of HLS, coturn, live chat, viewer metrics, "live now" notifications. | 1k concurrent viewers on one stream without touching the media pod. |

## 7. Risks

- **UDP reachability in the cluster** (hostNetwork/hostPort, node public IP) — the single biggest unknown; the spike exists to retire it.
- **CPU on transcode**: ffmpeg ABR per stream is ~1–2 cores at 720p. Cap concurrent live streams per node or reserve a node.
- **Egress bandwidth**: HLS to 1k viewers at 2.5 Mbps ≈ 2.5 Gbps. A CDN (or at least a caching nginx tier) is mandatory before any real audience.
- **Single-pod SPOF**: acceptable for launch; sharding by creator across pods is straightforward later because the API hands out the WHIP/playback URLs.

## 8. Open questions

1. Is MetalLB / a `LoadBalancer` implementation available on the cluster, or do we commit to `hostPort` + node pinning?
2. Which node has the public IP / port-forwarding for UDP `8189`?
3. Object storage for recordings — S3 proper or something in-cluster (MinIO)? (`media-storage` surface decision.)
4. Do subscriber-only streams need recording gated the same way (probably yes — inherit `tier_visibility` on the VOD post)?
5. Is co-hosting (interviews, panel discussions) a foreseeable product ask? If yes within ~12 months, evaluate LiveKit earlier (Phase 3).
