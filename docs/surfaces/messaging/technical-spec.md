# Messaging — Technical Specification

**Surface:** `messaging` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) §5.4 (WebSocket pattern reference) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (TDS §5/§6 `EntitlementService`, §12 per-surface template) · [`migrations.md`](../../standards/migrations.md) · [`pagination.md`](../../standards/pagination.md)

> **How in code.** This spec documents only the delta over the platform TDS — layering, DataType/Payload/DTO/Repository, PHP-DI, and Slim 4 conventions are inherited, not restated.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Messaging domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Services) | `conversations`, `messages`, blocks, rate-limit, broadcast fan-out |
| `MessagingService` | `php/Ubix/Service/Messaging/MessagingService.php` | Send/read, block enforcement, `EntitlementService` calls for broadcast, calls `NotifierInterface` for new-message alerts |
| Real-time transport | `Service/Messaging/MessagingSocketService.php` + WS endpoint (§6) | Live delivery to open conversations |
| API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/ConversationController.php`, `MessageController.php`, `MessageReportController.php` | Supporter/creator conversation + message CRUD, block, report |
| Admin | `app/SowingMeAdminApi/` → existing moderation-queue controller | Message reports surfaced alongside content/live-stream reports |
| Frontend | `app/SowingMeJs/src/routes/messages/` + `js/Ubix/src/lib/components/messaging/` | Inbox, thread view, composer, broadcast composer |

## 2. Data model (new migrations)

Exact table names reused from platform TDS §3 (`conversations`, `messages`) — no parallel names invented. Reports reuse the platform's shared `reports`/`moderation_actions`/`audit_logs` tables (TDS §3) rather than a messaging-specific reports table. All tables `InnoDB`, snake_case, `created_at`/`updated_at`.

### 2.1 `conversations`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` | the creator side |
| `supporter_user_id` | BIGINT FK → `users.id` | the supporter side |
| `kind` | ENUM(`direct`,`broadcast_recipient`) | `broadcast_recipient` marks a thread that received at least one broadcast; still a normal 1:1 thread otherwise (SRS FR-MSG-1.2/Q2) |
| `last_message_at` | DATETIME NULL | denormalised for cursor ordering (FR-MSG-1.3) |
| `blocked_by_user_id` | BIGINT NULL FK → `users.id` | set when either party blocks the other (FR-MSG-3.1); NULL = not blocked |

`UNIQUE (creator_id, supporter_user_id)` — one conversation per creator/supporter pair (FR-MSG-1.1 re-use rule).

### 2.2 `messages`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `conversation_id` | BIGINT FK → `conversations.id` | |
| `sender_user_id` | BIGINT FK → `users.id` | |
| `kind` | ENUM(`message`,`broadcast`,`system`) | `broadcast` = creator-authored, fanned out (FR-MSG-1.2) |
| `body` | TEXT | length-bounded at the Payload layer (FR-MSG-1.5) |
| `read_at` | DATETIME NULL | recipient's read state (FR-MSG-1.6) |
| `broadcast_batch_id` | CHAR(36) NULL | groups the fan-out copies of one creator broadcast for analytics/audit; NULL for `direct` messages |
| `created_at` | DATETIME | |

### 2.3 `message_rate_limits` (or Memcache — see §5)
Rate-limit counters are implemented as short-TTL Memcache keys (`memcache-keys.md`), **not** a table, consistent with how live-streaming and notifications treat rate-limit state as ephemeral counters rather than durable rows. No migration needed for this.

DataTypes: `ConversationKindEnum`, `MessageKindEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers.

## 3. Broadcast fan-out (FR-MSG-1.2)

`MessagingService::broadcast(creatorId, tierId, body)`:

1. Calls `EntitlementService` to resolve the set of users currently entitled to `tierId` or above — the same resolver live-streaming's go-live notification uses (its technical-spec §4.3/§8), and the same one `notifications`' recipient resolution uses (that surface's technical-spec §4).
2. For each entitled supporter: find-or-create the `(creator_id, supporter_user_id)` conversation, insert one `messages` row with `kind=broadcast` and a shared `broadcast_batch_id`.
3. Enqueues a `NotificationEvent` (type `new_post`-shaped is wrong here — messaging defines its own notification trigger, delivered through the same `NotifierInterface` seam per that surface's technical-spec §3) per recipient for the new-message alert (FR-MSG-2.2), respecting each recipient's `notification_prefs`.
4. Runs as a request-scoped loop bounded by subscriber count; if a creator's tier audience grows large enough to need async offload, it moves behind a job per platform TDS §9 without changing the API contract — flagged as a scale watch-point, not built ahead of need (YAGNI, per `pagination.md`'s own stated philosophy).

## 4. API surface (`SowingMeApi`)

Session auth + ownership middleware (a user only ever reads conversations/messages they participate in).

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/conversations` | Cursor-paginated inbox, ordered by `last_message_at` | FR-MSG-1.1, FR-MSG-1.3 |
| POST | `/conversations` | Start (or fetch existing) 1:1 conversation with a counterpart | FR-MSG-1.1 |
| GET | `/conversations/{id}/messages` | Cursor-paginated message list | FR-MSG-1.4 |
| POST | `/conversations/{id}/messages` | Send a message (blocked by `blocked_by_user_id` and rate-limit checks) | FR-MSG-1.1, FR-MSG-3.1, FR-MSG-3.2 |
| POST | `/conversations/{id}/read` | Mark conversation read | FR-MSG-1.6 |
| POST | `/conversations/{id}/block` | Block the other participant | FR-MSG-3.1 |
| POST | `/conversations/{id}/report` \| `/messages/{id}/report` | Report to trust-safety (`reports` row) | FR-MSG-3.3 |
| POST | `/creator/broadcasts` | Creator sends a broadcast to a tier | FR-MSG-1.2 |

Both list endpoints return the canonical cursor envelope `{ "items": [...], "nextCursor": "..." }` (`pagination.md` §4) — no bespoke shape.

### 4.1 Admin (`SowingMeAdminApi`)
Message reports appear in the existing moderation queue endpoints (platform TDS §3 `reports`/`moderation_actions`) alongside content and live-stream reports — no separate admin surface is built for messaging specifically, following the same shared-queue pattern live-streaming's FR-91 established.

## 5. Abuse controls implementation

- **Block (FR-MSG-3.1):** `conversations.blocked_by_user_id`; `MessagingService::send()` rejects with 403 if the sender is not the blocker and a block is set. The blocked party is told the send failed, never told by whom or that a block specifically caused it (SRS acceptance criterion 3).
- **Rate-limit (FR-MSG-3.2):** Memcache counter keyed `msg:rl:{sender_user_id}:{window}` (per-sender) and `msg:rl:{sender_user_id}:{recipient_user_id}:{window}` (per-pair), incremented on send, checked before insert; broadcast sends increment a separate `msg:rl:broadcast:{creator_id}:{window}` counter with a higher cap. Exceeding either returns 429.
- **Report (FR-MSG-3.3):** writes a `reports` row (`reportable_type='conversation'|'message'`, `reportable_id`, `reporter_user_id`, `reason`) — the same table content reports and live-stream reports use (platform TDS §3), so the admin moderation queue is one UI, not three. A resulting `moderation_actions` row and `audit_logs` entry follow the platform's existing trust-safety flow (`sensitive-data-access.md`).

## 6. Real-time transport (WebSocket — delta only)

Sowing.me has no existing WebSocket infrastructure of its own; `complete-js-guide.md` §5.4 documents project-neptune's F4F-domain **browser-direct WebSocket** pattern (same-origin path-routing through the main domain's nginx, native `WebSocket`, exponential-backoff reconnect 500 ms → 30 s, per-connection state reconstructable from client state on reconnect — that guide's §11's "WS/SSE reconnect-to-different-pod" rule). This surface **follows that pattern's shape**, not its specific F4F-domain routing (SRS §9 constraint):

- A lightweight WS endpoint lives on `SowingMeApi` (or a small sidecar service if connection volume warrants separating it later — not needed at M3 scale), one logical "room" per `conversation_id`, mirroring live-streaming's `Service/LiveChat/` one-room-per-`live_stream_id` design (that surface's technical-spec §7) — the same architectural shape, applied to a different table.
- Connect is entitlement/ownership-checked: only the two conversation participants may join a conversation's room.
- On send, `MessagingService` persists the `messages` row first (durability, NFR-MSG-AVAIL), then pushes the frame to any connected participants' sockets.
- Client: `js/Ubix/src/lib/components/messaging/` reuses the same reconnect-loop shape as `complete-js-guide.md`'s reference `<ChatPanel>` (native `WebSocket`, exponential backoff, `$effect` teardown) — copied as a pattern, not imported as a dependency, since the F4F chat-room transport is a different app's infrastructure.
- A dropped connection falls back to the next `GET /conversations/{id}/messages` poll/reload picking up any missed messages — no message is ever WS-only (NFR-MSG-AVAIL).

## 7. External seam usage

- **`EntitlementService`** — broadcast recipient resolution (§3); same call shape as live-streaming/notifications.
- **`NotifierInterface`** (via the `notifications` surface) — new-message alerts (FR-MSG-2.2); this surface is a caller, it does not re-implement delivery.
- **Trust-safety tables** (`reports`/`moderation_actions`/`audit_logs`, platform TDS §3) — reused as-is for reports (§5).

## 8. Security & privacy

- Every route enforces participant ownership — a user cannot read a conversation they are not part of, admin routes excepted (per platform TDS §6 role+ownership).
- Message bodies are user-generated content; access is participant-scoped; moderation access is audit-logged (`sensitive-data-access.md`).
- Broadcast sends are entitlement-checked at send time, not cached from an earlier read, so a just-cancelled subscriber is correctly excluded (NFR-MSG-ENT).

## 9. Testing

- **Unit:** block-enforcement branch, rate-limit counter logic (per-sender/per-pair/broadcast), broadcast fan-out recipient resolution against a stubbed `EntitlementService`, message-length Payload validation. Non-container per the migration-test pattern.
- **Integration:** conversation find-or-create uniqueness, cursor pagination correctness on both list endpoints, report → `reports`/`moderation_actions`/`audit_logs` chain.
- **E2E (staging):** supporter↔creator DM round-trip with live WS delivery; a blocked sender's send is rejected; a creator broadcast reaches only currently-entitled Tier B+ supporters; a report appears in the admin queue.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite. Every table via migration.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-MSG-1.1 | `conversations` find-or-create, `POST /conversations` |
| FR-MSG-1.2 | `MessagingService::broadcast()`, `EntitlementService`, `messages.kind=broadcast` |
| FR-MSG-1.3/1.4 | `GET /conversations`, `GET /conversations/{id}/messages` cursor contracts |
| FR-MSG-1.5 | `messages.body` Payload length validation |
| FR-MSG-1.6 | `messages.read_at`, `POST /conversations/{id}/read` |
| FR-MSG-2.1/2.2 | §6 WS transport, `NotifierInterface` call for offline recipients |
| FR-MSG-3.1 | `conversations.blocked_by_user_id`, `POST /conversations/{id}/block` |
| FR-MSG-3.2 | Memcache rate-limit counters (§5) |
| FR-MSG-3.3/3.4 | `reports`/`moderation_actions`/`audit_logs`, admin moderation queue |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
