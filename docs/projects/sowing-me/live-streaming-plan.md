# Sowing.me — Live Streaming Plan (orientation)

**Status:** Planning · v0.2 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3 (post-MVP) · Roadmap row **M3-06**

> This orientation doc has **graduated into a full surface**. The authoritative detail now lives in [`docs/surfaces/live-streaming/`](../../surfaces/live-streaming/README.md):
> - [`srs.md`](../../surfaces/live-streaming/srs.md) — requirements (what/why, Patreon-informed)
> - [`technical-spec.md`](../../surfaces/live-streaming/technical-spec.md) — how in code
> - [`architecture.md`](../../surfaces/live-streaming/architecture.md) — how as a system + the technology decision
>
> This page stays as the one-paragraph orientation and phase summary; do not re-document detail here — update the surface docs.

## What it is
Let a creator broadcast **live video** from the browser (no OBS required) to their supporters, gated by subscription tier, optionally **restreamed** to YouTube/Facebook, and automatically saved as a **replay (VOD) post**. The real-time analogue of a tier-gated post. Patreon's tier-gated livestreams are the reference; browser-native ingest, native simulcast, and in-stream tips are our extensions (see `srs.md` §3).

## Decision (one line)
**MediaMTX + ffmpeg** in the existing k3s cluster — WHIP browser ingest (+ RTMP/SRT), WHEP + LL-HLS playback, auth delegated to `SowingMeApi` so tier gating stays in PHP. **OvenMediaEngine** is the named fallback for built-in ABR; **LiveKit** only if multi-guest rooms are ever needed. Rationale + alternatives table: [`architecture.md`](../../surfaces/live-streaming/architecture.md) §2.

## Prerequisites
Depends on `creator-profile`, `subscription-tiers`, `media-storage`, and `notifications` — none built yet. **Phase 0 (media-server spike)** is the exception: no app dependencies, retires the UDP/NAT risk, can start today.

## Phases
| Phase | Deliverable | Startable now? |
|---|---|---|
| 0 — Spike | MediaMTX on `ws-dev`, browser WHIP from outside, WHEP+HLS playback, one restream to test YouTube. No app code. | **Yes** |
| 1 — Go live | stream/key model, auth hook, studio page, gated playback, record→PVC | after prereqs |
| 2 — Restream + VOD | restream targets, record→S3→auto VOD post | after 1 |
| 3 — Scale & polish | ABR, CDN, coturn, chat, tips-in-chat, analytics, live-now notifications | after 2 |

Open questions that gate Phase 0: cluster LoadBalancer/MetalLB vs `hostPort`, and which node has public UDP reachability — see `architecture.md` §4 and `srs.md` §11.
