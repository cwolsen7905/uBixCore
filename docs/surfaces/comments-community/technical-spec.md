# Comments & Community — Technical Specification

**Surface:** `comments-community` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (TDS §5/§6 `EntitlementService`, §12 per-surface template) · [`migrations.md`](../../standards/migrations.md) · [`pagination.md`](../../standards/pagination.md)

> **How in code.** This spec documents only the delta over the platform TDS — layering, DataType/Payload/DTO/Repository, PHP-DI, and Slim 4 conventions are inherited, not restated.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Comments domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Services) | `comments`, `reactions`, moderation actions |
| `CommentService` | `php/Ubix/Service/Comment/CommentService.php` | Entitlement check delegation, create/reply/delete/hide/ban, calls `NotifierInterface` |
| API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/CommentController.php`, `ReactionController.php` | Comment/reaction CRUD, report |
| Admin | `app/SowingMeAdminApi/` → existing moderation-queue controller | Comment reports surfaced alongside content/live-stream/message reports |
| Frontend | `app/SowingMeJs/src/lib/components/comments/` | Comment thread, reaction picker, creator moderation controls inline on a post |

## 2. Data model (new migrations)

Exact table names reused from platform TDS §3 (`comments`, `reactions`) — no parallel names invented. Reports/moderation reuse the platform's shared `reports`/`moderation_actions`/`audit_logs` tables (TDS §3), same as [`messaging`](../messaging/technical-spec.md) §2/§5 and live-streaming's admin pattern. All tables `InnoDB`, snake_case, `created_at`/`updated_at`, soft-delete for `comments` (hide is a flag, not a row delete, per FR-COMM-1.6's retained-for-audit rule).

### 2.1 `comments`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `post_id` | BIGINT FK → `posts.id` | |
| `user_id` | BIGINT FK → `users.id` | author |
| `parent_comment_id` | BIGINT NULL FK → `comments.id` | single-level reply (FR-COMM-1.3); a reply's own `parent_comment_id` is rejected at the Payload layer if it would create a second level |
| `body` | TEXT | length-bounded at the Payload layer |
| `hidden_at` | DATETIME NULL | set by moderation (FR-COMM-1.6); hidden comments excluded from list reads, retained in storage |
| `deleted_at` | DATETIME NULL | soft-delete |
| `created_at` / `updated_at` | DATETIME | |

### 2.2 `reactions`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | |
| `reactable_type` | ENUM(`post`,`comment`) | polymorphic target (FR-COMM-1.2) |
| `reactable_id` | BIGINT | |
| `kind` | ENUM(`like`,`amen`,`pray`) | additive set (SRS §11 Q1 default); new values are enum additions, not schema changes |
| `created_at` | DATETIME | |

`UNIQUE (user_id, reactable_type, reactable_id, kind)` — one reaction of a given kind per user per target.

### 2.3 `comment_bans`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` | ban scope is per-creator (FR-COMM-1.7, SRS §11 Q2) |
| `user_id` | BIGINT FK → `users.id` | banned user |
| `created_by` | BIGINT FK → `users.id` | creator or admin who issued the ban |
| `created_at` | DATETIME | |

`UNIQUE (creator_id, user_id)`. `CommentService::create()` checks this table before entitlement even runs — a ban is a harder gate than tier entitlement.

DataTypes: `ReactableTypeEnum`, `ReactionKindEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers.

## 3. Entitlement gating (no parallel rule)

`CommentController`/`ReactionController` call the **same** `EntitlementService.resolve(user, post)` platform TDS §6 defines for reading a post, before allowing a comment/reaction read or write — never a comments-specific gating function. This is the identical call shape live-streaming's playback/chat and messaging's broadcast fan-out use, applied here to `posts`:

```
allowed = EntitlementService.resolve(user, post)
if !allowed → 403 (no comment thread rendered, matches FR-CONT-3's "gating resolved server-side on every read")
if allowed && CommentBanRepository.isBanned(creator_id, user_id) → 403 on write only (read still allowed)
```

## 4. API surface (`SowingMeApi`)

Session auth + entitlement check on every route (no anonymous comment access beyond what public-visibility posts already allow).

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/posts/{id}/comments` | Cursor-paginated comment list (entitlement-checked) | FR-COMM-1.1, FR-COMM-1.4 |
| POST | `/posts/{id}/comments` | Create a comment (or a single-level reply via `parentCommentId`) | FR-COMM-1.1, FR-COMM-1.3 |
| DELETE | `/comments/{id}` | Author self-delete, or creator/admin delete | FR-COMM-1.6 |
| POST | `/comments/{id}/hide` | Creator/admin hide (soft, audit-retained) | FR-COMM-1.6 |
| POST | `/comments/{id}/report` | Report to trust-safety | FR-COMM-1.8 |
| POST | `/reactions` | React to a post or comment (`reactableType`,`reactableId`,`kind`) | FR-COMM-1.2 |
| DELETE | `/reactions/{id}` | Remove own reaction | FR-COMM-1.2 |
| POST | `/creator/comment-bans` | Creator bans a user from their content | FR-COMM-1.7 |
| DELETE | `/creator/comment-bans/{id}` | Lift a ban | FR-COMM-1.7 |

The list endpoint returns the canonical cursor envelope `{ "items": [...], "nextCursor": "..." }` (`pagination.md` §4) — no bespoke shape.

### 4.1 Admin (`SowingMeAdminApi`)
Comment reports appear in the existing shared moderation queue (platform TDS §3 `reports`/`moderation_actions`) alongside content, live-stream, and message reports — the same pattern established across all three other surfaces, not a fourth parallel queue.

## 5. Moderation & audit

- **Hide/delete (FR-COMM-1.6):** `hidden_at`/`deleted_at` set by the post's owning creator (ownership-checked against `posts.creator_id`) or a platform admin; both actions write a `moderation_actions` row distinguishing `actor_role=creator|admin` (FR-COMM-1.9).
- **Ban (FR-COMM-1.7):** `comment_bans` row scoped to `creator_id`; checked before entitlement on every write (§3); does not retroactively hide the banned user's prior comments (SRS explicit).
- **Report (FR-COMM-1.8):** writes a `reports` row (`reportable_type='comment'`, `reportable_id`, `reporter_user_id`, `reason`) into the same table content/live-stream/message reports use; any resulting action is audit-logged per `sensitive-data-access.md`, identically to messaging's report flow (`messaging/technical-spec.md` §5).

## 6. Notifications integration

`CommentService` enqueues a `NotificationEvent` (via `NotifierInterface`, the `notifications` surface's seam) to: the post's creator on a new top-level comment; a parent comment's author on a reply. Delivery/preferences/digesting/dedup are entirely owned by the `notifications` surface — this surface only calls the seam (mirrors messaging's FR-MSG-2.2 pattern exactly).

## 7. Frontend (SvelteKit)

- `js/Ubix/src/lib/components/comments/CommentThread.svelte` — renders under a post, entitlement-aware (renders a paywall/nothing if the parent post already rendered gated, rather than duplicating the check client-side — entitlement is enforced server-side per platform §10 "the SPA renders paywalls but never decides access").
- `ReactionPicker.svelte` — small fixed reaction set (SRS §11 Q1).
- Creator moderation controls (delete/hide/ban) render inline for the post's owning creator, calling the moderation endpoints above.

## 8. Security & privacy

- Every comment/reaction read and write is entitlement-checked server-side (NFR-COMM-SEC); the client never decides visibility.
- Comment content is user-generated content; hidden/deleted comments are retained per the platform's moderation-audit retention policy, not exposed to non-moderators (NFR-COMM-PRIV).
- Moderation and report actions are audit-logged (`sensitive-data-access.md`), distinguishing creator vs. admin actor.

## 9. Testing

- **Unit:** entitlement-delegation branch (comment/reaction gating matches post gating exactly — regression-tested against `EntitlementService`'s existing matrix), single-level-reply rejection, ban-check-before-entitlement ordering, reaction uniqueness constraint. Non-container per the migration-test pattern.
- **Integration:** cursor pagination correctness on the comment list, report → `reports`/`moderation_actions`/`audit_logs` chain, hide vs. delete visibility difference in list reads.
- **E2E (staging):** an entitled supporter comments/reacts on a gated post; a non-entitled visitor gets no comment thread; a creator hides a comment and it disappears for others; a banned user's comment attempt is rejected; a report appears in the admin queue.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite. Every table via migration.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-COMM-1.1/1.2 | `EntitlementService.resolve(user, post)` reused (§3), `POST /reactions` |
| FR-COMM-1.3 | `comments.parent_comment_id` single-level constraint |
| FR-COMM-1.4 | `GET /posts/{id}/comments` cursor contract (`pagination.md` §4) |
| FR-COMM-1.5 | comment DTO fields (author, timestamp, parent) |
| FR-COMM-1.6 | `comments.hidden_at`/`deleted_at`, `DELETE /comments/{id}`, `POST /comments/{id}/hide` |
| FR-COMM-1.7 | `comment_bans`, `POST`/`DELETE /creator/comment-bans[/{id}]` |
| FR-COMM-1.8/1.9 | `reports`/`moderation_actions`/`audit_logs` (shared, platform TDS §3) |
| FR-COMM-2 (reference) | Owned by [`live-streaming` technical-spec §7](../live-streaming/technical-spec.md#7-live-chat-fr-4x) — not realised here |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
