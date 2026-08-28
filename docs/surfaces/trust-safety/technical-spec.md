# Trust & Safety — Technical Specification

**Surface:** `trust-safety` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams — this spec documents only deltas) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)

> **How in code.** Follows [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType/Payload/DTO/Repository), [`complete-js-guide.md`](../../architecture/complete-js-guide.md), and [`pagination.md`](../../standards/pagination.md) (offset, for the moderation queue and report list). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md). This is the surface that realises the moderation subsystem the platform ADS already names (ADS §6: "reports → queue → actions → audit; content policy enforcement (faith-aligned)").

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Report domain | `php/Ubix/` Models/Repositories/DTOs/DataTypes | `reports` CRUD (create/list/status transition) |
| Moderation domain | `php/Ubix/` | `moderation_actions` creation, hide/remove/suspend orchestration |
| Audit writer | `php/Ubix/Service/AuditLogService` | Single insert path to `audit_logs` for every moderation action and policy publish — mirrors the shared `PiiAccessAudit` writer pattern from `sensitive-data-access.md` |
| Content-policy service | `php/Ubix/Service/ContentPolicyService` | Publish/version the policy + ToS/privacy documents; serves the current version to every surface |
| Enforcement seam | `EntitlementService` (existing, platform TDS §5/§6) + new `ModerationGateService` | `ModerationGateService.isVisible(entity)` layered in front of/alongside entitlement checks so hidden/removed content is excluded at every read path (FR-TRUST-35) |
| Public API | `app/SowingMeApi/` → `Controller/SowingMeApi/*` | Report submission, policy fetch, interstitial-gating check |
| Admin API | `app/SowingMeAdminApi/` → `Controller/InternalAdminApi/*` | Moderation queue read, action endpoints, policy publish — shares its app boundary with [`admin-console`](../admin-console/technical-spec.md), which hosts the queue's UI entry point |
| Frontend components | `js/Ubix/` | `ContentPolicyLink`, `ReportButton`/`ReportDialog`, `SensitivityInterstitial` |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at` (append-only — no `updated_at`/soft-delete on the audit-shaped tables, matching the event-table pattern in `sensitive-data-access.md` §"the audit table"). Names reused **exactly** from platform TDS §3 — no parallel naming.

### 2.1 `reports`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `reporter_user_id` | BIGINT FK → `users.id` | |
| `entity_type` | ENUM(`post`,`live_stream`,`comment`,`creator`,`organization`) | discrete, indexable — never a JSON blob, per `sensitive-data-access.md` §6.5 convention |
| `entity_id` | BIGINT | composite-indexed with `entity_type` |
| `reason` | VARCHAR(100) | drawn from a policy-linked reason list (FR-TRUST-20) |
| `detail` | TEXT NULL | free-text elaboration |
| `status` | ENUM(`open`,`actioned`,`dismissed`) default `open` | FR-TRUST-21,34 |
| `resolution_reason` | VARCHAR NULL | required when status leaves `open` (FR-TRUST-34) |
| `created_at` | DATETIME | |

Indexes: `idx_entity_type_entity_id`, `idx_status_created_at` (queue ordering), `idx_reporter_user_id` (dedup window check, FR-TRUST-22).

### 2.2 `moderation_actions`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `admin_id` | BIGINT FK → `users.id` | acting admin (always known — route is role-gated) |
| `report_id` | BIGINT FK → `reports.id` NULL | nullable — proactive actions have none (FR-TRUST-32) |
| `entity_type` | ENUM(`post`,`live_stream`,`comment`,`creator`,`organization`) | same enum as `reports` |
| `entity_id` | BIGINT | composite-indexed with `entity_type` |
| `action` | ENUM(`hide`,`remove`,`suspend`,`unhide`,`restore`) | FR-TRUST-31; `unhide`/`restore` are reversal rows, never edits (NFR-TRUST-3) |
| `reason` | VARCHAR NOT NULL | required on every action, including dismissal-adjacent ones |
| `created_at` | DATETIME | |

`action=suspend` invokes the **same** account-status mechanism `admin-console`'s `AdminDirectoryService.suspend()` exposes (FR-TRUST-31/FR-ADMIN-61) — this table records that a suspend happened *for a moderation reason*; the status mutation itself is admin-console's `AccountStatusEnum` transition, not duplicated here.

### 2.3 `audit_logs`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT PK | event-table shape, no capacity ceiling — mirrors `Pii_Access_Audits` (`sensitive-data-access.md`) |
| `admin_id` | BIGINT FK → `users.id` | |
| `action_type` | ENUM(`moderation_action`,`content_policy_publish`) | discrete, extend additively only |
| `reference_id` | BIGINT | `moderation_actions.id` or the policy-version row id |
| `entity_type` | ENUM(`post`,`live_stream`,`comment`,`creator`,`organization`,`policy_document`) | |
| `entity_id` | BIGINT NULL | null for policy publishes |
| `created_at` | DATETIME | append-only, immutable |

One `audit_logs` row per `moderation_actions` row (written in the same transaction by `AuditLogService`) — never a moderation action without its audit row.

### 2.4 `content_policy_versions`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `document_type` | ENUM(`content_policy`,`tos`,`privacy`) | FR-TRUST-10,11 |
| `version_label` | VARCHAR(20) | e.g. `0.1` |
| `body` | LONGTEXT | the document text (authorship out of scope — PQ3) |
| `published_by_admin_id` | BIGINT FK → `users.id` | |
| `published_at` | DATETIME | |

`ContentPolicyService.current(documentType)` always returns the latest row by `published_at`; older rows are retained for the "what did the policy say when this action was taken" audit question.

### 2.5 Age/sensitivity flags
Added as columns on the existing `posts` table (and `live_streams`, per live-streaming technical-spec §2.1) rather than a new table — a flag is a property of the content, not a separate entity:
- `posts.sensitivity_flag` ENUM(`none`,`sensitive`) default `none` — set by creator, overridable by a `moderation_actions` row.
- On VOD creation (live-streaming FR-61/`vod_post_id`), the resulting post's `sensitivity_flag` is copied from `live_streams.sensitivity_flag` (FR-TRUST-42) — a straight column copy in the same job that creates the VOD post, no new table.

New enums/DataTypes: `ReportEntityTypeEnum`, `ReportStatusEnum`, `ModerationActionEnum`, `AuditActionTypeEnum`, `ContentPolicyDocumentTypeEnum`, `SensitivityFlagEnum` — all under `php/Ubix/Enum/` + matching `DataType/Enum/*`.

## 3. API surface

### 3.1 Public (`SowingMeApi`)
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/policy/{documentType}` | Current published version of `content_policy`/`tos`/`privacy` | FR-TRUST-10,11 |
| POST | `/reports` | Submit a report against `entityType`/`entityId` with a reason | FR-TRUST-20,21 |
| GET | `/posts/{id}` (existing content endpoint, delta) | Response includes `sensitivityFlag`; client renders the interstitial before content when set | FR-TRUST-40,41 |

### 3.2 Admin (`SowingMeAdminApi`)
| Method | Path | Purpose | FR |
|---|---|---|---|
| GET | `/reports` | Offset-paginated moderation queue (also the endpoint admin-console's entry point calls — same route, one owner: this surface) | FR-TRUST-30, FR-ADMIN-60 |
| GET | `/reports/{id}` | Report detail including reporter identity (admin-only visibility) | FR-TRUST-23 |
| POST | `/moderation-actions` | Take an action: `{ entityType, entityId, action, reportId?, reason }` | FR-TRUST-31,32,33 |
| POST | `/reports/{id}/dismiss` | Dismiss with required `resolution_reason` | FR-TRUST-34 |
| POST | `/policy/{documentType}/publish` | Publish a new policy/ToS/privacy version | FR-TRUST-13 |

`POST /moderation-actions` with `action=suspend` calls `admin-console`'s `AdminDirectoryService.suspend()` internally (cross-service call within the same app boundary, not a duplicated status mutation) so account status and moderation-action history stay one source of truth each.

## 4. Enforcement mechanics

- `ModerationGateService.isVisible(entityType, entityId): bool` — checks for a live `moderation_actions` row of type `hide`/`remove` not yet reversed by `unhide`/`restore`; called by every read path (feed, creator page, explore, direct fetch by id, live playback resolver) **alongside** `EntitlementService`, not instead of it (FR-TRUST-35, mirrors platform FR-CONT-3's server-side gating principle).
- Interstitial gating: `sensitivity_flag=sensitive` requires client confirmation before the player/renderer requests the actual content; the API still requires the confirmation flag on the content-fetch request server-side (never a client-only skip) so a direct API call can't bypass it.
- Live-stream force-stop (live-streaming FR-90) and its VOD-hide (FR-92) are implemented as a `moderation_actions` row of `action=remove` against the stream, whose reversal-free semantics are exactly this surface's model — live-streaming's admin force-stop endpoint calls into this surface's action-taking path rather than inventing its own hide/remove concept.

## 5. Frontend

| Component | Where | Purpose |
|---|---|---|
| `ContentPolicyLink` | `js/Ubix/` | Footer link + policy page, fetches `/policy/{documentType}` |
| `ReportButton` / `ReportDialog` | `js/Ubix/` | Attached to post/stream/comment/profile UI; posts to `/reports` |
| `SensitivityInterstitial` | `js/Ubix/` | Confirm-to-view gate, reused by `SowingMeJs` post/stream views |
| Moderation queue table | `SowingMeAdminJs` (owned by [`admin-console`](../admin-console/technical-spec.md) §5, calling this surface's `/reports` and `/moderation-actions`) | Operator UI — not duplicated here |

## 6. Requirement traceability

| FR | Realised by |
|---|---|
| FR-TRUST-10/11/13 | `content_policy_versions`, `ContentPolicyService`, `GET/POST /policy/*` |
| FR-TRUST-12 | `ContentPolicyDocumentTypeEnum`, policy body left as a placeholder pending PQ3 |
| FR-TRUST-20/21/22/23 | `reports` table, `POST /reports`, dedup window in the report-create service |
| FR-TRUST-30/32/33 | `GET /reports`, `moderation_actions`, `AuditLogService` |
| FR-TRUST-31 | `ModerationActionEnum`, cross-call into admin-console's `AdminDirectoryService.suspend()` |
| FR-TRUST-34 | `POST /reports/{id}/dismiss`, `resolution_reason` |
| FR-TRUST-35 | `ModerationGateService.isVisible()` on every read path |
| FR-TRUST-40/41 | `posts.sensitivity_flag`, `SensitivityInterstitial` |
| FR-TRUST-42/43 | VOD-creation column copy (live-streaming `vod_post_id` job), `action=remove` on force-stop |
| FR-FAITH-1 | `content_policy_versions.body` (authorship: PQ3), enforcement path applies whatever the published text says |

## 7. Testing

- **Unit:** report dedup-window logic, `ModerationGateService.isVisible()` matrix (hide/remove/unhide/restore sequences), `ContentPolicyService.current()` version selection, Payload validation for report/action/dismiss/publish endpoints.
- **Integration:** offset-paginated `reports` queries; `moderation_actions`→`audit_logs` same-transaction write; VOD-creation flag copy; cross-service suspend call into admin-console's service.
- **E2E (staging):** submit a report → appears in queue → admin hides it → content disappears from feed/page/explore → admin un-hides → content reappears; admin suspends a creator via a moderation action → creator status changes once, one audit row; (M3) flagged live stream ends → VOD carries the flag.
- Gates: `phpunit` (strict), `phpstan` (max), `phpcs` (custom sniffs, including `DemandCanonicalPagination` for `/reports`), JS suite.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
