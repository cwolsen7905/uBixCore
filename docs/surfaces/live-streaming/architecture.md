# Live Streaming — Architecture & Design (SDD)

**Surface:** `live-streaming` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`technical-spec.md`](technical-spec.md) (how in code) · plan [`../../projects/sowing-me/live-streaming-plan.md`](../../projects/sowing-me/live-streaming-plan.md)

> **How as a system.** System/deployment design, the network model, sequence flows, capacity, failure modes, and the technology decision with alternatives. Authored against the SDD role in [`web-development-delivery-framework.md`](../../standards/web-development-delivery-framework.md).

## 1. System context

```
┌ Creator ─────────────┐        ┌ Supporters ───────────────────────────┐
│ Browser (WHIP)       │        │ Browser: WHEP (few) / LL-HLS (many)   │
│ OR OBS/AV (RTMP/SRT) │        │ + chat (WS) + tip (HTTPS)             │
└──────────┬───────────┘        └───────▲───────────────▲───────────────┘
           │ media (UDP/TCP)            │ HTTP (HLS)     │ WS/HTTPS
           ▼                            │                │
   ┌───────────────────────── k3s cluster (ws-<env>) ───────────────────────┐
   │  ┌ MediaMTX (StatefulSet, node-pinned) ┐   ┌ SowingMeApi (Slim) ┐      │
   │  │ WHIP/RTMP/SRT in · WHEP/LL-HLS out   │──▶│ auth hook          │      │
   │  │ ffmpeg: restream + record            │◀──│ lifecycle events   │      │
   │  └───────┬───────────────┬──────────────┘   │ streams/keys/tokens│      │
   │          │ PVC (segments) │ RTMP push        │ entitlement · tips │      │
   │          ▼                ▼                   │ chat (WS)          │      │
   │   media-storage      YouTube/Facebook         └─────────┬──────────┘      │
   │   (S3) → VOD post          (external)                   │ MariaDB         │
   └────────────────────────────────────────────────────────┴─────────────────┘
                    ▲ nginx ingress (HTTPS: signalling + HLS)      ▲ CDN (HLS, later)
```

The **API owns all policy** (who may publish, who may watch, tier gating, tips, notifications); the **media server owns only pixels** and asks the API for every decision via the auth hook. This keeps streaming logic in the same PHP domain as the rest of Sowing.me and makes the media server replaceable.

## 2. Technology decision

**Primary: MediaMTX + ffmpeg. Named fallback: OvenMediaEngine. LiveKit only if multi-guest rooms become a requirement.**

### 2.1 Alternatives considered
| System | Type | Browser ingest | Playback | Restream | Record | ABR/transcode | Ops weight | Verdict |
|---|---|---|---|---|---|---|---|---|
| **MediaMTX** (Go) | Broadcast / protocol router | WHIP | WHEP, LL-HLS, RTSP, SRT | ffmpeg via hooks | Native | No (pair ffmpeg) | Very light (single binary + YAML) | **Chosen** |
| **OvenMediaEngine** (C++) | Broadcast, low-latency | WebRTC/WHIP, RTMP, SRT | WebRTC sub-second, LL-HLS | REST push | REST | **Built-in** | Medium | **Fallback** if ABR/transcode pain |
| SRS (C++) | Broadcast | WHIP | WHEP, HLS, HTTP-FLV | forward | DVR | ffmpeg | Medium | Viable; thinner docs for us |
| LiveKit (Go) | **SFU (rooms)** + Ingress/Egress | SDK/WHIP | SDK (WebRTC) | Egress (Redis + headless Chrome) | Egress | simulcast | Heavy | Only for multi-guest |
| Janus / mediasoup | SFU toolkit | WebRTC | WebRTC | build it | build it | simulcast | High (bespoke) | Too much glue |
| Broadcast Box (Pion) | WHIP→WHEP relay | WHIP | WHEP only | — | — | passthrough | Trivial | Too thin for product |
| Owncast | Single-streamer product | RTMP only | HLS | — | partial | ffmpeg | Light | No WebRTC in, single-tenant |

### 2.2 Why MediaMTX
1. **Shape match** — 1 creator → many viewers is exactly protocol-router territory, not an SFU.
2. **Auth as an HTTP hook** — publish/read decisions go to `SowingMeApi`; tier gating, keys, tokens stay in PHP. No parallel JWT/identity system.
3. **Restream & record are config** — ffmpeg push per target; native recording to a PVC.
4. **Two-tier playback** — WHEP (sub-second, bounded) + LL-HLS (mass, HTTP-cacheable) from one process.
5. **Ops** — ~30 MB Go image, one YAML, Prometheus metrics + control API. One node handles many creators before sharding.

### 2.3 Accepted trade-offs
- **No transcode.** ABR ladder = an ffmpeg sidecar/Job per stream (`_720p`/`_480p` + master playlist) — code we own. If that becomes a burden, switch playback/transcoding to **OvenMediaEngine** (built-in ABR) while keeping the same API-owns-policy design. Decision gate: Phase 3.
- **WHEP viewer count is CPU-bound** per pod → route the crowd to HLS; WHEP is for interactive minorities.
- **Single media pod = SPOF** at launch (accepted; see §5).

Sources in the plan doc's landscape section.

## 3. Deployment topology (k3s)

- **App:** `app/SowingMeStream/{dev,staging,main}-*.yaml`, following the per-app manifest pattern (Rancher `field.cattle.io/ports`, nginx `ingressClassName`, nodes `kube-001..004`).
- **Workload:** `StatefulSet`, 1 replica initially, `nodeSelector` pinned to one node (the one with public UDP reachability), `mediamtx.yml` as ConfigMap, internal secret via Secret.
- **Ingress (TCP/HTTP only):** `stream.<env>.ubixsys.com` → `:8889` (WHIP/WHEP signalling) + `:8888` (HLS). TLS at ingress; large-body/long-timeout annotations on HLS; proxy-cache `*.m4s`/`*.mp4` segments.
- **Storage:** recording PVC (RWO, same node) + sidecar/CronJob → `media-storage` (S3) then delete local.
- **Observability:** `:9997` control API and `:9998` Prometheus stay ClusterIP-only; NetworkPolicy limits `/internal/*` callers to the media pod.

## 4. Network model — the load-bearing risk

nginx ingress proxies TCP/HTTP only; **WebRTC media is UDP** and cannot traverse it. MediaMTX's **ICE UDP mux** collapses media to a **single UDP port** (`:8189`), so we need exactly one UDP port publicly reachable on the pinned node — not a wide range.

Options (SRS Q1):
- **A — `hostPort`/`hostNetwork` + node pin (default):** expose `8189/udp` (and `1935/tcp`, `8890/udp` for OBS/SRT) on one node; advertise that node's public IP/DNS via `webrtcAdditionalHosts`. Simple, no cluster add-ons. Cost: media tied to one node.
- **B — `LoadBalancer` (if MetalLB exists):** cleaner, node-independent. Confirm availability first.

**TURN:** start without it (home/church NATs usually work with the UDP mux port + STUN). If publish failures appear from symmetric NATs, add `coturn` as a second Deployment and hand its creds to clients from the API. Never rely on public STUN alone in production.

## 5. Scaling & capacity

- **Ingest** is cheap (one publisher per stream). **Egress** dominates.
- **Bandwidth math:** 1,000 viewers × 2.5 Mbps ≈ **2.5 Gbps** per popular stream. This must be **CDN/cache-served HLS**, never straight off the pod (NFR-2/5). Put a caching tier (nginx proxy_cache, then a CDN) in front of `:8888` before any real audience.
- **Transcode CPU:** ffmpeg 720p ABR ≈ 1–2 cores/stream. Cap concurrent live streams per node or reserve a node.
- **Sharding path:** because the API mints per-stream WHIP/playback URLs, adding a second media pod and routing creators across pods needs **no client change** — the API just points a new stream at a different pod's host. This is how the SPOF is retired (NFR-3).

## 6. Key sequences

### 6.1 Go live (browser)
```
Creator → API: POST /creator/streams               (returns whipUrl + streamKey)
Creator → MediaMTX: WHIP POST (SDP, Bearer streamKey)
MediaMTX → API: /internal/stream/auth {publish, path, pass}
API → MediaMTX: 200 (key matches, status∈{scheduled,live})
MediaMTX: publisher connected → runOnReady
MediaMTX → API: /internal/stream/event {ready}
API: status=live; notify entitled supporters (FR-32); launch enabled restream ffmpeg
```
### 6.2 Viewer joins (gated)
```
Viewer → API: GET /streams/{id}/playback
API: resolveEntitlement(user,stream)
  allowed → {hlsUrl, whepUrl?, readToken(60s)}
  denied  → 403 + subscribe CTA (or preview token if preview_seconds)
Viewer → MediaMTX: WHEP/HLS with readToken in query
MediaMTX → API: /internal/stream/auth {read, token} → 200/403 (re-check)
```
### 6.3 End → VOD
```
Publisher stops → grace window → runOnNotReady
MediaMTX → API: /internal/stream/event {notReady}
API: status=ended; stop restream runs; enqueue VOD job
Sidecar: mux fMP4 segments → MP4 → media-storage (S3) → API creates gated VOD post (vod_post_id)
```

## 7. Failure modes

| Failure | Behaviour | Mitigation |
|---|---|---|
| Publisher network blip | `live` held during grace window | reconnect resumes same stream (FR-15) |
| One restream target down | primary + other targets unaffected | independent ffmpeg per target; status surfaced (FR-52/53) |
| Media pod crash | active streams drop | StatefulSet reschedule; creators re-go-live; recordings on PVC survive if node intact |
| API unreachable by hook | media server denies (fail-closed) | correct default: no policy → no stream |
| S3 upload fails post-stream | VOD job retries; segments retained on PVC until success | alerting on job failure |
| Symmetric NAT publisher | WHIP fails to connect | add coturn (§4) |

Fail-closed on the auth hook is deliberate: if policy can't be evaluated, nobody publishes or watches.

## 8. Security model (recap)

Server-side authorisation on **both** publish and read; stream keys hashed + rotatable; restream secrets encrypted + write-only + audit-logged (`sensitive-data-access.md`); internal endpoints shared-secret + NetworkPolicy; control/metrics ports never exposed; TLS at ingress; readTokens short-lived, signed, single-stream. Full detail in [`technical-spec.md`](technical-spec.md) §9.

## 9. Phasing (maps to plan & roadmap M3-06)

| Phase | Deliverable | Exit |
|---|---|---|
| **0 Spike** | MediaMTX on `ws-dev`; browser WHIP from an external network; WHEP + HLS playback; one ffmpeg restream to test YouTube. **No app code.** | UDP/NAT behaviour measured; MediaMTX-vs-OME confirmed in `status.md`. Retires §4 risk. |
| **1 Go live** | stream/key model, auth hook, studio page, gated HLS(+WHEP) playback, record→PVC | creator goes live from browser; entitled watch; non-entitled 403 |
| **2 Restream + VOD** | restream targets UI + ffmpeg; record→S3→auto VOD post | simultaneous YouTube; replay posted minutes after end |
| **3 Scale & polish** | ABR ladder (ffmpeg or switch to OME), CDN for HLS, coturn, chat, tips-in-chat, analytics, live-now notifications | 1k concurrent on one stream without touching the media pod |

**Phase 0 is the only part that can start today** — it has zero dependency on `creators`/`tiers`/`media-storage` and buys down the biggest unknown.

## 10. When to revisit the decision
- **Multi-guest / co-host** rooms become a real ask → evaluate **LiveKit** (SFU); it can coexist — LiveKit Egress can push RTMP into MediaMTX so gating/restream/VOD are reused.
- **ABR ffmpeg tooling** proves heavy to own → switch transcode/playback to **OvenMediaEngine**, keep the API-owns-policy seam.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial architecture/SDD; absorbed landscape analysis + decision. |
