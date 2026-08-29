# Content & Posts — Technical Specification

**Surface:** `content-posts` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) · platform [`../../projects/sowing-me/platform/technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (TDS §3 domain model, §12 per-surface template)

> **How in code.** This spec documents only the deltas this surface adds to the platform TDS — layering, cross-cutting domain model, API conventions, and the `EntitlementService`/`MediaStorageInterface` seams are inherited, not restated. Every table lands via `bin/ubix migrate:*` per [`../../standards/migrations.md`](../../standards/migrations.md). No `architecture.md` — this surface adds no system design beyond the platform ADS.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Post domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers, Services) | `posts`, `post_media`, `collections`, post edit history, entitlement-gated reads |
| Post API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Creator (write/manage) + public/supporter (gated read) endpoints |
| Publish scheduler job | `Console/Command/` + k8s CronJob | Flips `scheduled` → `published` at `publish_at` (platform TDS §9) |
| Creator studio / library | `app/SowingMeJs/` route `creator/library` + `js/Ubix/` shared components | Compose, attach media, gate, schedule, edit-history view |
| Public post view | `app/SowingMeJs/` on creator page | Renders gated/teaser posts per API response |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`, soft-delete (`deleted_at`) on `posts` and `collections`. FKs to existing `creators`, `tiers` (platform TDS §3).

### 2.1 `posts`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` | immutable after creation (SRS FR-CONT-11) |
| `collection_id` | BIGINT FK → `collections.id` NULL | SRS FR-CONT-10 |
| `type` | ENUM(`text`,`image`,`audio`,`video_embed`) | `PostTypeEnum`; **additive** — M3+ adds `devotional`,`reading_plan` (SRS FR-CONT-14) |
| `visibility` | ENUM(`public`,`subscribers`,`tier`) | `PostVisibilityEnum` — reused verbatim from platform TDS §3 |
| `min_tier_id` | BIGINT FK → `tiers.id` NULL | required when `visibility=tier`; enforced at Payload validation, not just DB |
| `title` | VARCHAR(200) | |
| `body` | MEDIUMTEXT NULL | markdown/rich text; NULL permitted for pure-media posts |
| `excerpt` | VARCHAR(500) NULL | author-supplied teaser override (SRS Q2); auto-truncated from `body` when NULL |
| `external_video_url` | VARCHAR(500) NULL | set when `type=video_embed`; host-allowlist validated (SRS Q3) |
| `scripture_reference` | JSON NULL | **reserved, unused at M1** — additive seam for FR-CONT-4 (SRS §6.5); no reader/writer code ships until M3+ |
| `position` | INT NULL | ordering within `collection_id` (SRS FR-CONT-9) |
| `status` | ENUM(`draft`,`scheduled`,`published`,`archived`) | `PostStatusEnum`; state machine §3 |
| `publish_at` | DATETIME NULL | set when `status=scheduled` (SRS FR-CONT-16) |
| `published_at` | DATETIME NULL | set once, on first transition into `published` |

### 2.2 `post_media`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `post_id` | BIGINT FK → `posts.id` | |
| `media_asset_id` | BIGINT FK → `media_assets.id` | owned by [`../media-storage/technical-spec.md`](../media-storage/technical-spec.md) §2.1 — this surface never writes bucket keys directly |
| `position` | INT | ordering of multiple media within one post |
| `alt_text` | VARCHAR(300) NULL | accessibility (platform NFR-A11Y) |

### 2.3 `collections`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` | |
| `title` | VARCHAR(200) | |
| `description` | TEXT NULL | |

### 2.4 `post_edit_history`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `post_id` | BIGINT FK → `posts.id` | |
| `edited_by_user_id` | BIGINT FK → `users.id` | |
| `prior_title` / `prior_body` / `prior_type` / `prior_visibility` / `prior_min_tier_id` | snapshot columns | only the fields captured at FR-CONT-17; nullable if unchanged in that edit |
| `created_at` | DATETIME | the edit timestamp |

Append-only; never updated or deleted (SRS NFR-CONT-5).

DataTypes: `PostTypeEnum`, `PostVisibilityEnum` (reused exactly from platform TDS §3), `PostStatusEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers per the framework.

## 3. Post lifecycle state machine

```
draft ──creator sets publish_at──► scheduled ──job at publish_at──► published ──creator unpublish──► archived
  │                                                                     ▲
  └──creator publishes immediately─────────────────────────────────────┘
```

`draft` and `scheduled` posts are readable only by the owning creator (ownership check, not entitlement — SRS FR-CONT-15). `archived` posts are excluded from all listing endpoints but retained (soft-delete semantics, not a hard delete) for the creator's own history.

## 4. API surface (`SowingMeApi`)

All authenticated creator routes use existing session auth + role/ownership middleware. Payloads use the DataType/Payload validation system; responses are DTOs.

### 4.1 Creator
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/creator/posts` | Create a post (`draft` by default) | FR-CONT-6,7 |
| GET | `/creator/posts` | List own posts, all statuses (offset-paginated admin-style list per pagination standard §3) | FR-CONT-18 |
| PATCH | `/creator/posts/{id}` | Edit fields; writes `post_edit_history` first if `status=published` | FR-CONT-17 |
| POST | `/creator/posts/{id}/schedule` | Set `publish_at`, transition to `scheduled` | FR-CONT-16 |
| POST | `/creator/posts/{id}/publish` | Immediate publish | FR-CONT-15 |
| POST | `/creator/posts/{id}/archive` | Unpublish | FR-CONT-15 |
| DELETE | `/creator/posts/{id}` | Soft-delete a `draft` (published posts archive, never hard-delete) | — |
| GET | `/creator/posts/{id}/history` | Edit history list | FR-CONT-17 |
| GET/POST/PATCH/DELETE | `/creator/collections[/{id}]` | Collection CRUD | FR-CONT-9 |
| PATCH | `/creator/collections/{id}/reorder` | Bulk `position` update for member posts | FR-CONT-11 |

### 4.2 Viewer (public / supporter)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/creators/{slug}/posts` | Cursor-paginated post list on a creator's page; each row entitlement-checked | FR-CONT-12,13,18 |
| GET | `/collections/{id}/posts` | Cursor-paginated posts in a collection, ordered by `position` | FR-CONT-9,12 |
| GET | `/posts/{id}` | Single post, entitlement-checked; 200 with full body/media if entitled, 200 with teaser-only DTO if not (never a bare 403 — a gated post's existence and teaser are public) | FR-CONT-12 |

## 5. Entitlement resolution (reuses the platform seam)

This surface adds **no new gating logic** — it is a consumer of `EntitlementService.resolve(user, resource)` (platform TDS §6, ADR-008), passing a `post` resource carrying `visibility`/`min_tier_id`. The resolution rule mirrors the live-streaming surface's reference implementation exactly:

- `visibility=public` → allowed.
- `visibility=subscribers` → allowed if the user has an active subscription to any of the creator's tiers.
- `visibility=tier` → allowed if active subscription to `min_tier_id` or higher (tier ordering per platform FR-MEM-3).
- else → response DTO omits `body`/`post_media`, includes `excerpt` and the permitted tier(s) to unlock it.

The controller never inspects `visibility` itself to decide what to return — every gated field access routes through the resolver, so posts, media, and (later) comments enforce identically (SRS FR-CONT-12).

## 6. Media attachment flow

1. Creator uploads via `media-storage`'s presigned-upload flow (`POST /creator/media/uploads` — [`../media-storage/technical-spec.md`](../media-storage/technical-spec.md) §4.1), receiving a `media_asset_id` once validated.
2. Creator attaches it to a post: `POST /creator/posts/{id}/media { mediaAssetId, position, altText }` → writes `post_media`.
3. On a gated read, the response's media entries carry a **signed, expiring URL** minted by `media-storage` only after the same entitlement check that gated the post body (never a bare bucket key or a pre-signed URL good for longer than the read).

This surface owns `post_media` (the attachment/order/alt-text) and the post-level gating decision; `media-storage` owns the bucket object, validation, derivatives, and URL signing.

## 7. Faith content types — extensibility proof (M3+, not built at M1)

To satisfy platform NFR-EXT/ADR (no rework), M1 ships:
- `PostTypeEnum` as an open, additive enum (new `DataType/Enum/PostTypeEnum` cases `devotional`, `reading_plan` are a one-line addition, not a new column).
- `posts.scripture_reference` as a reserved, unused, nullable `JSON` column — present in the M1 migration so M3+ needs no `ALTER TABLE` to add it, only application code to read/write it.
- No M1 endpoint, Payload rule, or UI reads/writes `scripture_reference` or the reserved enum values — they are schema-only until the faith-types surface work is picked up.

## 8. Testing

- **Unit:** `PostTypeEnum`/`PostVisibilityEnum`/`PostStatusEnum` DataTypes, Payload validation (`min_tier_id` required when `visibility=tier`, `external_video_url` host allowlist), entitlement-resolution consumption (matrix over visibility × subscription state — reuses the live-streaming surface's resolver test pattern), state-machine transitions, edit-history write-before-update ordering. Non-container per the migration-test pattern.
- **Integration:** repository queries against a test schema (cursor pagination correctness — keyset stability under concurrent inserts per `pagination.md` §4); collection reorder transactionality.
- **E2E (staging):** creator drafts → schedules → publishes → gated post correctly hidden/shown per tier → edit produces history row.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 9. Requirement traceability

| FR | Realised by |
|---|---|
| FR-CONT-6 | `posts.type` (`PostTypeEnum`), `post_media` for multi-media posts |
| FR-CONT-7/8 | `posts.visibility`/`min_tier_id`, `EntitlementService.resolve` (§5) |
| FR-CONT-9/10/11 | `collections`, `posts.collection_id`/`position`, `PATCH /creator/collections/{id}/reorder` |
| FR-CONT-12/13 | Per-post entitlement check in `GET /posts/{id}` and list endpoints (§5) |
| FR-CONT-14 | `scripture_reference` reserved column + additive `PostTypeEnum` seam (§7) |
| FR-CONT-15/16 | `posts.status` state machine (§3), publish-scheduler job (§1) |
| FR-CONT-17 | `post_edit_history` (§2.4) |
| FR-CONT-18 | Cursor pagination on `GET /creators/{slug}/posts`, `GET /collections/{id}/posts` |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
