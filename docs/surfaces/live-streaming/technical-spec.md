# Live Streaming — Technical Specification

**Surface:** `live-streaming` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`architecture.md`](architecture.md) (system design) · plan [`../../projects/sowing-me/live-streaming-plan.md`](../../projects/sowing-me/live-streaming-plan.md)

> **How in code.** This spec is the contract between the SRS and the implementation. It follows the uBix Core patterns in [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Media server (**MediaMTX**) | `app/SowingMeStream/` k8s manifests + `mediamtx.yml` ConfigMap | WHIP/RTMP/SRT ingest; WHEP/LL-HLS egress; auth hook; lifecycle hooks; restream & record via ffmpeg |
| Stream domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers, Services) | Streams, keys, entitlement, tokens, restream targets, events, analytics |
| Stream API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Creator + viewer + internal (media-server) endpoints |
| Admin | `app/SowingMeAdminApi/` | Live list, force-stop, reports |
| Chat | `Service/LiveChat/` + WS endpoint (see §7) | Live messages, reactions, moderation |
| Creator studio | `app/SowingMeJs/` route `/creator/live` + `js/Ubix/` `whip.ts` | Capture, WHIP publish, controls, stats |
| Viewer player | `app/SowingMeJs/` on creator page + `js/Ubix/` player component | WHEP→HLS playback, paywall, chat, tips |

Neptune leftovers `broadcasting/*` (SvelteKit routes, admin) are **repurposed** into this surface, not deleted as the charter originally said.

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`, soft-delete where posts-like. FKs to existing `creators`, `tiers`, `subscriptions`, `posts`, `transactions`.

### 2.1 `live_streams`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` | |
| `title` | VARCHAR(200) | |
| `description` | TEXT NULL | |
| `visibility` | ENUM(`public`,`subscribers`,`tier`) | mirrors post visibility |
| `min_tier_id` | BIGINT FK → `tiers.id` NULL | required when `visibility=tier` |
| `preview_seconds` | INT NULL | public preview window (FR-23); NULL = none |
| `status` | ENUM(`scheduled`,`live`,`ended`,`errored`) | state machine §3 |
| `scheduled_start_at` | DATETIME NULL | FR-30 |
| `started_at` / `ended_at` | DATETIME NULL | |
| `record_enabled` | TINYINT(1) default 1 | FR-60 |
| `vod_post_id` | BIGINT FK → `posts.id` NULL | set on publish (FR-61) |
| `ingest_path` | VARCHAR(64) UNIQUE | opaque path segment used by the media server |
| `peak_viewers` / `unique_viewers` | INT | denormalised analytics (FR-80) |

`ingest_path` is an opaque random id (not the numeric PK, not guessable). The stream **key** is stored separately, hashed.

### 2.2 `live_stream_keys`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `live_stream_id` | BIGINT FK | |
| `key_hash` | CHAR(64) | SHA-256 of the key; plaintext shown once at issue |
| `active` | TINYINT(1) | rotation deactivates old rows (FR-12) |
| `created_at` | DATETIME | |

### 2.3 `live_stream_restream_targets`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK | targets are reusable across a creator's streams |
| `platform` | ENUM(`youtube`,`facebook`,`custom`) | |
| `rtmp_url` | VARBINARY | **encrypted** (FR-51) |
| `stream_key_enc` | VARBINARY | **encrypted** |
| `label` | VARCHAR(100) | |
| `enabled` | TINYINT(1) | default per-target; per-stream override in join table if needed |

### 2.4 `live_stream_restream_runs`
Per-broadcast, per-target status for FR-52: `id, live_stream_id, target_id, status(connecting|live|failed|ended), error, started_at, ended_at`.

### 2.5 `live_stream_chat_messages`
`id, live_stream_id, user_id, kind(message|reaction|tip|system), body, tip_transaction_id NULL, deleted_by NULL, created_at`. Tips reference `transactions` (FR-70/71).

### 2.6 `live_stream_chat_bans`
`id, live_stream_id, user_id, until DATETIME NULL, created_by`. (FR-42.)

### 2.7 `live_stream_viewer_sessions` (analytics, phase 3)
`id, live_stream_id, user_id NULL, joined_at, left_at, watch_seconds` — feeds FR-80; can start as periodic snapshots rather than per-session rows.

### 2.8 `live_stream_reports`
`id, live_stream_id, reporter_user_id, reason, status(open|actioned|dismissed), created_at`. (FR-91.)

DataTypes: introduce `StreamVisibilityEnum`, `StreamStatusEnum`, `RestreamPlatformEnum`, `ChatMessageKindEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers per the framework.

## 3. Stream lifecycle state machine

```
scheduled ──creator go-live (ingest auth OK)──► live
   │                                             │
   │ (no ingest by grace period)                 │ ingest stops
   ▼                                             ▼
 (stays scheduled / creator cancels)          ended ──record processed──► VOD post created
                                               │
              ingest error / force-stop ───────┴────────► errored / ended (VOD may still publish)
```

Transitions are driven by MediaMTX lifecycle hooks (§5), not the client. A brief **reconnect grace window** (e.g. 30–60 s) keeps `live` across a publisher blip (FR-15) before flipping to `ended`.

## 4. API surface (`SowingMeApi`)

All authenticated creator/viewer routes use existing session auth + role/ownership middleware. Payloads use the DataType/Payload validation system; responses are DTOs.

### 4.1 Creator
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/creator/streams` | Create/schedule a stream → returns `{ id, ingestPath, whipUrl, streamKey (once), rtmpUrl }` | FR-10,11,30 |
| GET | `/creator/streams` | List own streams (cursor paginated) | — |
| PATCH | `/creator/streams/{id}` | Edit title/description/visibility/schedule/record flag | FR-20,30,60 |
| POST | `/creator/streams/{id}/key/rotate` | Rotate stream key | FR-12 |
| POST | `/creator/streams/{id}/end` | Explicit stop (also stoppable by ceasing ingest) | FR-15 |
| GET/PUT/DELETE | `/creator/restream-targets[/{id}]` | Manage restream destinations (write-only secrets) | FR-50,51 |
| GET | `/creator/streams/{id}/analytics` | Post-stream metrics | FR-80 |

### 4.2 Viewer
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/streams/{id}` | Public metadata (title, creator, live?, scheduled time, visibility, preview?) | FR-25,30 |
| GET | `/streams/{id}/playback` | **Entitlement-checked.** Returns `{ hlsUrl, whepUrl?, readToken, previewSecondsRemaining? }` or 403 with subscribe CTA | FR-21,22,23,24 |
| POST | `/streams/{id}/tips` | In-stream tip (delegates to payments) | FR-70 |
| POST | `/streams/{id}/reminders` | Opt into upcoming-stream reminder | FR-31 |
| POST | `/streams/{id}/reports` | Report a stream | FR-91 |

### 4.3 Internal — media-server integration (cluster-only, shared-secret header, network-policy restricted)
| Method | Path | Purpose |
|---|---|---|
| POST | `/internal/stream/auth` | MediaMTX auth hook. Body `{ action: publish\|read, path, query, ip, user, pass }`. **publish**: resolve `ingest_path`→stream, verify `pass` against active `key_hash`, allow only if `status∈{scheduled,live}`. **read**: verify the short-lived `readToken` in `query` and re-check entitlement. Returns 200 or 4xx. |
| POST | `/internal/stream/event` | MediaMTX `runOnReady`/`runOnNotReady`/`runOnRead` lifecycle → flip `status`, fan out go-live notifications (FR-32), start/stop restream runs, enqueue VOD processing on end. |

### 4.4 Admin (`SowingMeAdminApi`)
`GET /live-streams` (currently live), `POST /live-streams/{id}/force-stop` (FR-90 → calls MediaMTX control API to kick the path + hides VOD), `GET /live-stream-reports`, `POST /live-stream-reports/{id}/action`.

## 5. Media server (MediaMTX) configuration

`mediamtx.yml` (ConfigMap). Path-scoped so a single server hosts all creators.

```yaml
# auth delegated to our API — the domain lives in PHP, not here
authMethod: http
authHTTPAddress: http://sowing-me-api.ws-<env>.svc.cluster.local/internal/stream/auth
authHTTPExclude: []            # authorise both publish and read

webrtc: yes
webrtcAddress: :8889           # WHIP/WHEP HTTP signalling (behind ingress)
webrtcICEUDPMuxAddress: :8189  # single UDP media port (see architecture §network)
webrtcAdditionalHosts: [ "<node-public-ip-or-dns>" ]

rtmp: yes                      # :1935  OBS/hardware ingest
srt: yes                       # :8890/udp
hls: yes
hlsVariant: lowLatency         # LL-HLS
hlsAddress: :8888

pathDefaults:
  # per-path event hooks call our API; MediaMTX substitutes $MTX_PATH etc.
  runOnReady: >
    curl -s -X POST -H "X-Internal: $INTERNAL_SECRET"
    http://sowing-me-api.../internal/stream/event
    -d 'event=ready&path=$MTX_PATH'
  runOnNotReady: >
    curl -s -X POST -H "X-Internal: $INTERNAL_SECRET"
    http://sowing-me-api.../internal/stream/event
    -d 'event=notReady&path=$MTX_PATH'
  record: no                   # toggled per-stream by the event handler / API-templated config

paths:
  ~^live/.*$:                  # regex: all live paths
    source: publisher
```

**Restream (FR-50)** is an ffmpeg process per enabled target, launched by the `/internal/stream/event` handler (or `runOnReady` templated with the target list) — copy codec, no re-encode:
```
ffmpeg -i rtsp://localhost:8554/$MTX_PATH -c copy -f flv rtmps://<target-url>/<target-key>
```
**Recording (FR-60/62)**: MediaMTX `record: yes` with `recordPath` to a PVC as fMP4 segments; a sidecar/Job muxes to a single MP4 on `notReady` and hands it to the `media-storage` upload pipeline → creates the VOD post (FR-61).

## 6. Ingest & playback clients (SvelteKit)

### 6.1 WHIP publish (`js/Ubix/whip.ts`, ~100 lines, no SDK)
```
const pc = new RTCPeerConnection({ iceServers });        // STUN/TURN from API
stream.getTracks().forEach(t => pc.addTrack(t, stream));  // getUserMedia
const offer = await pc.createOffer(); await pc.setLocalDescription(offer);
const res = await fetch(whipUrl, {                          // whipUrl+streamKey from API
  method:'POST', headers:{'Content-Type':'application/sdp', Authorization:`Bearer ${streamKey}`},
  body: offer.sdp });
await pc.setRemoteDescription({ type:'answer', sdp: await res.text() });
// DELETE whipUrl (with Location from POST) to stop.
```
Studio page (`/creator/live`) wraps this with device pickers, preview `<video>`, live stats (poll `/creator/streams/{id}/analytics` or the WS), restream status, and an **End & publish** action.

### 6.2 Viewer player
`GET /streams/{id}/playback` → if `whepUrl` present and viewer opted into low-latency, negotiate WHEP (same offer/answer shape as WHIP but receive-only); otherwise LL-HLS via native (Safari) or `hls.js`. On 403 render the paywall with the subscribe CTA for a permitted tier. Preview mode plays the public segment then swaps to the paywall at `previewSecondsRemaining`.

## 7. Live chat (FR-4x)

Lightweight WebSocket service (`Service/LiveChat/`), one room per `live_stream_id`. Connect is entitlement-checked (same check as playback). Messages persisted to `live_stream_chat_messages`; reactions may be ephemeral-broadcast + sampled to DB. Moderation actions (delete/timeout/ban) are creator/mod-only and enforced server-side (`live_stream_chat_bans`). Tips arrive as `kind=tip` system-authored messages after the payment succeeds. Decision Q5 (reuse vs new WS infra) in SRS §11.

## 8. Entitlement resolution (single source of truth)

One service method `resolveEntitlement(user, stream): {allowed, reason, previewAllowed}` used by **playback, chat connect, and the media-server read hook** so the rule is enforced identically everywhere (NFR-4/FR-21):
- `visibility=public` → allowed.
- `visibility=subscribers` → allowed if the user has an active subscription to any of the creator's tiers.
- `visibility=tier` → allowed if active subscription to `min_tier_id` or higher.
- else → `previewAllowed` if `preview_seconds` set (time-boxed via signed token), otherwise denied.

Read tokens are short-lived (e.g. 60 s), signed, single-stream, and re-validated by the read hook — so a token leak can't grant a different stream or outlast the check.

## 9. Security & secrets

- Stream keys: generated server-side, returned once, stored as `key_hash`; rotation deactivates old rows. Never logged.
- Restream secrets: encrypted at rest (app encryption service), write-only from the UI, decrypted only when launching ffmpeg in-cluster. Audit-logged per `sensitive-data-access.md`.
- Internal endpoints: cluster-internal DNS only, shared-secret header, k8s NetworkPolicy limiting callers to the media pod.
- Media control/metrics APIs (`:9997`/`:9998`) stay ClusterIP-only.

## 10. Testing

- **Unit:** entitlement resolver (matrix over visibility × subscription state), key hashing/rotation, state-machine transitions, auth-hook controller (publish/read decisions), payload validation. Non-container per the migration-test pattern.
- **Integration:** `/internal/stream/auth` and `/internal/stream/event` against a stubbed MediaMTX request shape; restream-run bookkeeping.
- **E2E (spike/staging):** browser WHIP publish → entitled HLS playback → 403 for non-entitled → restream to a test YouTube → VOD post created. Matches SRS §10.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 11. Requirement traceability (excerpt)

| FR | Realised by |
|---|---|
| FR-10/11/12 | `POST /creator/streams`, `live_stream_keys`, `/internal/stream/auth` publish branch, `whip.ts` |
| FR-20/21/24 | `visibility`/`min_tier_id`, entitlement service §8, `/streams/{id}/playback` |
| FR-32 | `/internal/stream/event` ready → notifications fan-out |
| FR-50/52/53 | `restream_targets`/`restream_runs`, ffmpeg per target |
| FR-60/61 | MediaMTX record → media-storage → `vod_post_id` |
| FR-70/71 | `/streams/{id}/tips`, `chat_messages.kind=tip`, `transactions` |
| FR-90/92 | Admin force-stop → MediaMTX control API + VOD hide |

Full table maintained as the surface is sliced; each new endpoint/table cites its FR.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
