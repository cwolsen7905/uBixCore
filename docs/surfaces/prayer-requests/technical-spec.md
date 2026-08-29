# Prayer Requests — Technical Design Specification (TDS)

**Surface:** `prayer-requests` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · Platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams — this doc documents deltas only) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (inherited — see [`README.md`](README.md))

> **How in code.** Follows uBix Core patterns in [`../../architecture/complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`../../architecture/complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`../../standards/migrations.md`](../../standards/migrations.md). Entitlement/visibility resolution reuses the **same** `EntitlementService` used by posts and live streams (Platform TDS §6, ADR-008) — this surface adds one new visibility value (`private`) and its resolution rule, not a parallel gating mechanism.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Prayer domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers, Services) | `prayer_requests`, `prayers`, visibility resolution, moderation/notification event emission |
| Prayer API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Request CRUD, pray/respond, report submission |
| Moderation hand-off | Emits into `trust-safety`'s report shape (mirrors `live_stream_reports`) | This surface does not own the queue UI |
| Notification hand-off | Emits via `NotifierInterface` (Platform TDS §5) | This surface does not own delivery/preferences |
| Prayer wall UI | `app/SowingMeJs/` — component on `/c/{slug}` and `/o/{slug}` | Submit form, request list, pray button, response composer |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`, soft-delete (posts-like, per FR-12). FKs to existing `creators`, `organizations`, `tiers`, `users`.

### 2.1 `prayer_requests`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `submitter_user_id` | BIGINT FK → `users.id` | |
| `owner_type` | ENUM(`creator`,`organization`) | community it was submitted to, per ADR-007 owner shape |
| `owner_id` | BIGINT | |
| `body` | TEXT | |
| `visibility` | ENUM(`public`,`subscribers`,`tier`,`private`) | `public`/`subscribers`/`tier` mirror `posts.visibility` exactly (Platform TDS §3); `private` is new to this surface (FR-11) |
| `min_tier_id` | BIGINT FK → `tiers.id` NULL | required when `visibility=tier`, same convention as `live_streams.min_tier_id` |
| `status` | ENUM(`open`,`answered`,`archived`) | FR-13 |
| `deleted_at` | DATETIME NULL | soft-delete (FR-12) |

### 2.2 `prayers`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `prayer_request_id` | BIGINT FK → `prayer_requests.id` | |
| `user_id` | BIGINT FK → `users.id` | |
| `kind` | ENUM(`prayed`,`response`) | FR-20/22 |
| `body` | TEXT NULL | required when `kind=response`, null when `kind=prayed` |
| `deleted_by_user_id` | BIGINT FK → `users.id` NULL | moderation removal (FR-41) |
| UNIQUE | (`prayer_request_id`,`user_id`,`kind`) WHERE `kind='prayed'` | one prayed-count per user per request (FR-20); a user may still leave multiple text responses, so the uniqueness constraint applies only to `kind='prayed'` |

### 2.3 `prayer_request_reports`

Same shape as `live_stream_reports` (`docs/surfaces/live-streaming/technical-spec.md` §2.8), generalized to this surface: `id, prayer_request_id, prayer_id NULL, reporter_user_id, reason, status(open|actioned|dismissed), created_at`. This is the row this surface hands to `trust-safety`'s queue (FR-40); the queue UI/workflow itself is out of scope here.

DataTypes: introduce `PrayerRequestVisibilityEnum`, `PrayerRequestStatusEnum`, `PrayerKindEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers, per the framework.

## 3. Entitlement resolution (reuses the platform's single source of truth)

One service method, following the exact shape of the live-streaming surface's `resolveEntitlement` (`docs/surfaces/live-streaming/technical-spec.md` §8), extended with the `private` case:

- `visibility=public` → allowed for everyone.
- `visibility=subscribers` → allowed if the user has an active subscription to any of the owner's tiers.
- `visibility=tier` → allowed if active subscription to `min_tier_id` or higher.
- `visibility=private` → allowed **only** if the user is the submitter, the community owner (creator, or an `org_admin`/`contributor` per [`../organizations/technical-spec.md`](../organizations/technical-spec.md) §2.2 when `owner_type=organization`), or a platform moderator.
- else → denied.

This method is called by **every** read path — the request list query, a single-request fetch, the pray/respond endpoints, and the report endpoint — so the rule is enforced identically everywhere (Platform ADR-008, mirroring the live-streaming surface's reference implementation). No visibility check is ever performed client-side.

## 4. API surface (`SowingMeApi`)

All routes use existing session auth + role/ownership middleware; entitlement is additionally checked per §3 on every read/write. Payloads use DataType/Payload validation; responses are DTOs.

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/prayer-requests` | Submit a request: `{ ownerType, ownerId, body, visibility, minTierId? }` | FR-10,11 |
| GET | `/prayer-requests?ownerType=&ownerId=` | List requests entitled to the current viewer (cursor-paginated per `pagination.md`) | FR-21, entitlement §3 |
| PATCH | `/prayer-requests/{id}` | Submitter edits body/visibility/status; owner/moderator edits status only | FR-12,13,30 |
| DELETE | `/prayer-requests/{id}` | Submitter soft-deletes own request | FR-12 |
| POST | `/prayer-requests/{id}/prayers` | Mark "prayed" (`kind=prayed`) | FR-20 |
| POST | `/prayer-requests/{id}/responses` | Leave a text response (`kind=response`) | FR-22 |
| DELETE | `/prayer-requests/{id}/prayers/{prayerId}` | Owner/moderator removes a response (soft, sets `deleted_by_user_id`) | FR-41 |
| POST | `/prayer-requests/{id}/reports` | Report a request | FR-40 |
| POST | `/prayer-requests/{id}/responses/{responseId}/reports` | Report a response | FR-40 |

### Admin / `trust-safety` hand-off

`GET /prayer-request-reports`, `POST /prayer-request-reports/{id}/action` — same admin action shape as `live_stream_reports` (mirrors `docs/surfaces/live-streaming/technical-spec.md` §4.4); implemented here only as far as producing/consuming the report row, with the queue UI itself owned by the `trust-safety` surface once it exists.

## 5. Notification hand-off

On a successful `POST /prayer-requests/{id}/responses`, the service calls `NotifierInterface` (Platform TDS §5) with a `prayer_response` event addressed to the request's `submitter_user_id`, respecting that user's notification preferences and the platform's fan-out rate-limit/dedup rules (Platform FR-NOTIF-2). A `prayed` (count-only) interaction does **not** call `NotifierInterface` by default (SRS FR-51). Until the `notifications` surface ships, this call target is a stub that logs the event — the call site and event shape are fixed now so wiring the real notifier later is additive.

## 6. Frontend components (`SowingMeJs`)

- Prayer wall section on `/c/{slug}` and `/o/{slug}`: submit form (body + visibility picker, including a `private` option with a plain-language explanation of who can see it), paginated request list respecting entitlement, pray button (distinct affordance from a post "like"), response composer.
- `/settings/prayer-requests` (or folded into account settings): a supporter's own submitted requests, editable/deletable.
- Report action available on every visible request/response, following the same UI pattern as content reporting elsewhere on the platform.

## 7. Requirement traceability

| FR | Realised by |
|---|---|
| FR-10/11/12/13 | `prayer_requests` table, `POST/PATCH/DELETE /prayer-requests` |
| FR-20/21 | `prayers` (`kind=prayed`), unique constraint, count query |
| FR-22/23 | `prayers` (`kind=response`), entitlement §3 `private` branch |
| FR-30/31 | `visibility` mutability rule (§3), no retroactive disclosure |
| FR-40/41 | `prayer_request_reports`, moderation removal (`deleted_by_user_id`) |
| FR-50/51 | `NotifierInterface` call on response only (§5) |

Platform trace: FR-PRAY-1 (Platform SRS §5.21), FR-FAITH-4 (§4), ADR-008.

## 8. Testing

- **Unit:** entitlement resolver matrix over visibility × role (public/subscribers/tier/private × submitter/owner/moderator/other-supporter/visitor) — the same testing pattern as the live-streaming resolver (`docs/surfaces/live-streaming/technical-spec.md` §10); unique-prayed-per-user constraint; payload validation (visibility requires `minTierId` only when `tier`).
- **Integration:** repository queries against a test schema for the entitled request list; report submission producing the shape `trust-safety` expects; notification event emission (against the stub `NotifierInterface`).
- **E2E (staging, once prerequisites land):** supporter submits a `tier`-visibility request → only tier-entitled supporters see it → a response triggers a notification → a reported response reaches the moderation queue → moderator removes it.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
