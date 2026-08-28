# Live Streaming — Software Requirements Specification (SRS)

**Surface:** `live-streaming` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3 (post-MVP) · **Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4), `media-storage` (S6), `notifications` (M3-02)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`architecture.md`](architecture.md) · project plan [`../../projects/sowing-me/live-streaming-plan.md`](../../projects/sowing-me/live-streaming-plan.md)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). The SRS says **what** and **why**; the technical-spec says **how in code**; the architecture doc says **how as a system**. These three plus this surface's roadmap rows stay in sync — a requirement change here re-versions all three (see CLAUDE.md → *Surface documentation*).

## 1. Purpose

Let a Sowing.me creator broadcast **live video** to their supporters directly from a web browser (no desktop software), gate who can watch by subscription tier, optionally **restream** the same broadcast to external platforms (YouTube/Facebook), and automatically turn the finished broadcast into a **replay (VOD) post** in their library. This is the platform's real-time analogue of a tier-gated post.

## 2. Scope

**In scope:** browser + encoder ingest, tier-gated playback, go-live notifications, scheduling, live chat with moderation, in-stream tips, simulcast/restream, recording→VOD, viewer analytics, admin moderation of live content.

**Out of scope (this surface):** multi-guest / co-host video rooms (interviews, panels — an SFU concern, see [`architecture.md`](architecture.md) §"When to revisit"); native mobile broadcast apps; DRM; pay-per-view one-off ticketed streams unattached to a tier (candidate for a later revision).

## 3. Context — what Patreon does, and our position

Patreon's livestream feature is the closest reference. It informs these requirements; where we differ is deliberate.

| Patreon behaviour | Our stance |
|---|---|
| Livestreams restricted to selected membership tiers; creator picks which tiers see it | **Adopt** (FR-20). Reuse the same tier-visibility model as `content-posts`. |
| Creator schedules a stream; patrons see it upcoming and get notified | **Adopt** (FR-30, FR-31). |
| Patrons notified (email + in-app) when the creator goes live | **Adopt** (FR-32). |
| Live chat during the stream; creator/mods can moderate | **Adopt** (FR-40..FR-43). |
| Replay saved automatically and posted to the feed, still gated | **Adopt** (FR-60, FR-61). |
| Broadcast only from within Patreon's app/OBS | **Extend**: browser-native WHIP ingest *and* RTMP/SRT for OBS/church AV. No creator is forced into desktop software. |
| No native multi-platform simulcast | **Extend**: restream to YouTube/Facebook/custom RTMP is a first-class feature (FR-50). A differentiator for churches already streaming to YouTube. |
| Tips exist platform-wide but not as a live "super-chat" | **Extend**: in-stream tips surfaced live in chat (FR-70), tying real-time engagement to monetisation. |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Stream** | A single live broadcast instance owned by one creator, with a lifecycle (scheduled → live → ended). |
| **Stream key** | Secret that authorises publishing to a stream's ingest path. Per-stream, rotatable. |
| **WHIP** | WebRTC-HTTP Ingestion Protocol — how the browser publishes video to the media server. |
| **WHEP** | WebRTC-HTTP Egress Protocol — low-latency browser playback. |
| **LL-HLS** | Low-Latency HTTP Live Streaming — segmented, cache/CDN-friendly playback for the mass audience. |
| **Restream / simulcast** | Pushing the live broadcast onward to an external RTMP(S) destination. |
| **VOD** | Video-on-demand replay recorded from the live broadcast. |
| **Entitlement** | Server-side determination that a given viewer may watch a given stream (public, or subscribed to a permitted tier). |

## 5. Personas & primary user stories

- **Creator (pastor, worship leader, teacher).** "As a creator, I click *Go Live* in my browser, my supporters get notified, only my paying tiers can watch, it also goes out to my church YouTube, and when I stop, the replay is automatically posted for supporters who missed it."
- **Supporter.** "As a subscriber, I get notified my creator is live, I open their page, I watch with a chat, I can tip during the stream, and I can rewatch later."
- **Non-subscriber / visitor.** "As a visitor, I see the creator is live, I see a paywall (or a public preview if the creator allowed one), and a clear path to subscribe."
- **Admin (us).** "As an admin, I can see active streams, kill an abusive one, and review reports."

## 6. Functional requirements

### 6.1 Ingest — creator going live (FR-1x)
- **FR-10** A creator can start a broadcast from a supported browser using camera + microphone via WHIP, with no software install.
- **FR-11** A creator can instead publish via RTMP or SRT (OBS, StreamYard, hardware encoder) to the same stream using its stream key.
- **FR-12** Each stream has a rotatable, per-stream **stream key**; publishing without a valid key is rejected at the media server (server-side, via the API auth hook).
- **FR-13** The creator studio shows a local preview, device pickers (camera/mic), and a mute/camera-off toggle before and during broadcast.
- **FR-14** Screen sharing is supported as an alternative or additional source (post-MVP within the surface).
- **FR-15** Starting a broadcast transitions the stream to `live` and is idempotent (a reconnect within a grace window resumes the same stream, does not create a new one).

### 6.2 Playback & entitlement (FR-2x)
- **FR-20** A stream has a **visibility**: `public`, `subscribers` (any paid tier), or a specific `tier` (that tier and above). This mirrors `content-posts` visibility.
- **FR-21** Every playback request is authorised **server-side** against the viewer's entitlement; visibility is never enforced only in the client.
- **FR-22** Entitled viewers watch in-browser. Default path is LL-HLS; a low-latency WHEP path is offered where viewer scale allows (see NFR-1).
- **FR-23** A creator may enable a **public preview** (first N minutes, or a low-res teaser) for non-entitled viewers, after which a subscribe paywall is shown.
- **FR-24** Non-entitled viewers see a paywall with a direct subscribe CTA for a permitted tier.
- **FR-25** The live player shows live viewer count and elapsed time.

### 6.3 Scheduling & notifications (FR-3x)
- **FR-30** A creator can schedule a stream (title, description, planned start, visibility) that appears as "upcoming" on their page.
- **FR-31** Supporters can see upcoming streams and (optionally) opt in to a reminder.
- **FR-32** When a creator goes live, entitled supporters are notified via the `notifications` surface (email + in-app); respects each supporter's notification preferences.
- **FR-33** Notification fan-out is rate-limited and de-duplicated (one "went live" notification per stream per supporter).

### 6.4 Live chat, reactions & moderation (FR-4x)
- **FR-40** Entitled viewers can post chat messages during a live stream; visitors with only preview access cannot chat.
- **FR-41** Viewers can send lightweight reactions (emoji) that render live.
- **FR-42** The creator and designated moderators can delete messages, timeout, and ban a viewer from the chat for the stream.
- **FR-43** Chat is retained with the VOD (replayable or exportable) per the recording's visibility; chat content is subject to the platform content policy.

### 6.5 Restream / simulcast (FR-5x)
- **FR-50** A creator can configure external RTMP(S) restream destinations (YouTube, Facebook, custom URL + key) and enable/disable each per stream.
- **FR-51** Restream credentials are stored encrypted and never returned in plaintext to the client after entry (write-only from the UI's perspective).
- **FR-52** Restream starts automatically when the stream goes live (for enabled destinations) and its per-destination status (connecting / live / failed) is visible to the creator.
- **FR-53** A restream failure to one destination must not interrupt the primary broadcast or other destinations.

### 6.6 Recording & VOD (FR-6x)
- **FR-60** Each broadcast is recorded by default (creator can opt out per stream).
- **FR-61** When a broadcast ends, the recording is processed and published as a **replay post** in the creator's library, inheriting the stream's visibility/tier gating.
- **FR-62** Recordings are stored via the `media-storage` pipeline (object storage + signed URLs), not on the media pod long-term.
- **FR-63** A creator can trim, retitle, change visibility, or delete a replay like any post.

### 6.7 In-stream monetisation (FR-7x)
- **FR-70** An entitled viewer can send a **tip** during a live stream; the tip is surfaced live in chat (amount + optional message), using the existing `payments` tip flow.
- **FR-71** In-stream tips appear in the creator's earnings/dashboard attributed to the stream.

### 6.8 Analytics (FR-8x)
- **FR-80** The creator dashboard shows per-stream metrics: peak concurrent viewers, unique viewers, average watch time, chat volume, tips total.
- **FR-81** Live viewer count is available in near-real-time during the broadcast.

### 6.9 Admin, safety & compliance (FR-9x)
- **FR-90** An admin can list currently-live streams across the platform and force-stop any stream (kills ingest + playback + restream).
- **FR-91** Viewers can report a live stream; reports reach the admin moderation queue.
- **FR-92** Force-stopping or a policy takedown also unpublishes/hides the resulting VOD.
- **FR-93** Age/sensitivity flags on a stream carry through to its VOD.

## 7. Non-functional requirements

- **NFR-1 Latency.** Default (LL-HLS) glass-to-glass ≤ ~4 s; optional WHEP path ≤ ~1 s for interactive Q&A. Chat delivery ≤ 1 s.
- **NFR-2 Scale.** A single stream must serve **1,000 concurrent viewers** via the HLS path without adding load to the media/ingest pod (i.e. HLS is cache/CDN-served). Support ≥ 10 concurrent live creators per media node initially.
- **NFR-3 Availability.** Single media pod is acceptable at launch (SPOF documented); the design must allow sharding creators across pods later without changing client contracts (the API hands out per-stream URLs).
- **NFR-4 Security.** Stream keys and restream secrets are sensitive data (`docs/standards/sensitive-data-access.md`): encrypted at rest, permission-gated, audit-logged, never logged in plaintext, rotatable. Publish and playback are both authorised server-side.
- **NFR-5 Cost.** No per-minute SaaS media bill — self-hosted in the existing k3s cluster. Egress to viewers must be CDN/cache-offloadable before any real audience (bandwidth is the dominant cost — see [`architecture.md`](architecture.md) §capacity).
- **NFR-6 Browser support.** Ingest: current Chrome/Edge/Firefox (getUserMedia + WebRTC); Safari best-effort. Playback: all evergreen browsers + iOS Safari (native HLS) + `hls.js` elsewhere.
- **NFR-7 Accessibility.** Player keyboard-navigable, captions supported where available, chat screen-reader friendly.
- **NFR-8 Privacy.** Viewer identities in chat follow the platform display-name model; viewer analytics are aggregate; recordings of subscriber-only streams inherit the same gating.
- **NFR-9 Standards.** PHP follows DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner; JS follows `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **Creator studio** (SvelteKit): device selection, go-live, live stats, restream status, end-and-publish. WHIP client, no third-party SDK.
- **Viewer player** (SvelteKit): WHEP-with-HLS-fallback player, paywall, chat, tip button.
- **Media server** (MediaMTX): WHIP/RTMP/SRT in; WHEP/LL-HLS out; HTTP auth hook + lifecycle webhooks into `SowingMeApi`; ffmpeg for restream/record.
- **`SowingMeApi`**: stream CRUD, key management, entitlement/playback tokens, media-server auth hook + event webhooks, restream targets, chat (WebSocket), tips, analytics.
- **`SowingMeAdminApi`**: live stream list, force-stop, reports queue.

## 9. Constraints & assumptions

- Reuses existing entities that must exist first: `creators`, `tiers`, `subscriptions`, `posts`/`post_media`, `transactions`, `notifications`.
- Runs on the existing Rancher/k3s cluster (nodes `kube-001..004`), nginx ingress, GitLab CI, MariaDB via migration runner.
- WebRTC media needs UDP reachability on a node — the primary infra risk (see [`architecture.md`](architecture.md)).
- No card data touches our systems; tips go through the same hosted Stripe flow as the rest of the platform.

## 10. Acceptance criteria (surface DoD)

1. A creator goes live from a browser on an external network; an entitled supporter watches within NFR-1 latency; a non-entitled visitor gets a paywall (403 server-side).
2. The same broadcast appears simultaneously on a test YouTube channel via restream.
3. Ending the broadcast produces a gated replay post in the creator's library within minutes.
4. Live chat works with creator moderation; an in-stream tip is recorded against the stream.
5. An admin can force-stop a live stream and its VOD is hidden.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Cluster has MetalLB/LoadBalancer, or commit to `hostPort` + node pinning for UDP? | `hostPort` + pin to one node | architecture, ingest |
| Q2 | Object storage for recordings — S3 proper or in-cluster MinIO? | Inherit `media-storage` decision | recording/VOD |
| Q3 | WHEP low-latency path in the surface MVP, or HLS-only first? | HLS-only first; WHEP as fast-follow | playback |
| Q4 | Public preview: time-boxed (first N min) or low-res teaser? | Time-boxed, creator-set minutes | FR-23 |
| Q5 | Chat: build on existing WS infra or add one? Retain chat with VOD by default? | New lightweight WS service; retain chat | FR-4x |
| Q6 | Co-hosting / multi-guest foreseeable within ~12 months? If yes, evaluate LiveKit earlier. | Not in this surface | scope of architecture |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **M3-06** (sub-rows added when the surface is sliced). Changes to any FR update the traceability table and re-version the companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
