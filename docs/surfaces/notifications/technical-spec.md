# Notifications — Technical Specification

**Surface:** `notifications` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md)
**Framework references:** [`complete-php-guide.md`](../../architecture/complete-php-guide.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (TDS §5 `NotifierInterface`, §9 async jobs, §12 per-surface template) · [`migrations.md`](../../standards/migrations.md) · [`pagination.md`](../../standards/pagination.md)

> **How in code.** This spec is the contract between `srs.md` and the implementation. It documents only the delta over the platform TDS — layering, DataType/Payload/DTO/Repository, PHP-DI, and Slim 4 conventions are inherited, not restated.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Notification domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Services) | `notifications`, `notification_prefs`, dedup/rate-limit logic |
| `NotifierInterface` | `php/Ubix/Service/Notification/NotifierInterface.php` | The one fan-out seam every surface calls (platform TDS §5) |
| `EmailNotifierChannel` | `php/Ubix/Service/Notification/EmailNotifierChannel.php` | Wraps existing `MailerInterface` (Symfony Mailer); renders Latte templates |
| `InAppNotifierChannel` | `php/Ubix/Service/Notification/InAppNotifierChannel.php` | Writes `notifications` rows |
| `NotificationService` | `php/Ubix/Service/Notification/NotificationService.php` | Resolves recipients (calls `EntitlementService` where needed), applies prefs/rate-limit/dedup, dispatches to channels |
| Fan-out job | `php/Ubix/Console/Command/Notification/DispatchNotificationsCommand.php` + k8s CronJob/queue worker | Async processing per platform TDS §9 |
| Digest job | `php/Ubix/Console/Command/Notification/SendDigestsCommand.php` + k8s CronJob (hourly + daily) | Rolls up digestible pending notifications into one email |
| API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/NotificationController.php`, `NotificationPreferenceController.php` | In-app list, mark-read, prefs CRUD |
| Frontend | `app/SowingMeJs/src/routes/**` + `js/Ubix/src/lib/components/notifications/` | Bell/list, unread badge, preferences page |

## 2. Data model (new migrations)

Exact table names reused from platform TDS §3 (`notifications`, `notification_prefs`) — no parallel names invented. All tables `InnoDB`, snake_case, `created_at`/`updated_at`.

### 2.1 `notifications`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | recipient |
| `type` | ENUM(`new_post`,`went_live`,`new_subscriber`,`payout`,`tip_gift`,`prayer_response`) | additive; new value ≠ schema change |
| `related_type` | VARCHAR(32) NULL | e.g. `post`, `live_stream`, `subscription`, `transaction`, `prayer_request` |
| `related_id` | BIGINT NULL | |
| `payload` | JSON | structured data for template rendering (FR-NOTIF-1.4) |
| `channels_sent` | SET(`email`,`in_app`) | which channels actually delivered |
| `dedup_key` | CHAR(64) | SHA-256 of `(user_id,type,related_type,related_id)`; **UNIQUE** |
| `read_at` | DATETIME NULL | in-app read state (FR-NOTIF-1.5) |
| `created_at` | DATETIME | |

The `UNIQUE` on `dedup_key` is the dedup mechanism (FR-NOTIF-2.4): a re-fired event is an `INSERT ... ON DUPLICATE KEY` no-op at the repository layer, not an application-level check-then-insert race.

### 2.2 `notification_prefs`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | |
| `type` | ENUM(same as `notifications.type`) | |
| `channel` | ENUM(`email`,`in_app`) | |
| `mode` | ENUM(`immediate`,`digest_hourly`,`digest_daily`,`off`) | |
| `updated_at` | DATETIME | |

`UNIQUE (user_id, type, channel)`. A missing row means "use the type's documented default" (FR-NOTIF-2.1) — resolved in `NotificationService`, not by pre-seeding every row for every user.

DataTypes: `NotificationTypeEnum`, `NotificationChannelEnum`, `NotificationModeEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers, per the framework (mirrors live-streaming's `StreamVisibilityEnum` pattern).

## 3. `NotifierInterface` (the seam)

```php
interface NotifierInterface
{
    /** Enqueues fan-out; does not deliver synchronously (FR-NOTIF-2.5). */
    public function notify(NotificationEvent $event): void;
}
```

`NotificationEvent` (DTO): `type` (`NotificationTypeEnum`), `recipients` (a closed set of user ids **or** an `EntitlementRecipientResolver` reference — e.g. "entitled supporters of creator 123"), `relatedType`/`relatedId`, `payload` (array, template-shaped). A caller (post publish, live-streaming's `/internal/stream/event` handler, payments/payouts service, future prayer-response service) builds an `NotificationEvent` and hands it to `NotifierInterface::notify()` — it never touches `MailerInterface` or the `notifications` table itself (FR-NOTIF-3.1).

`notify()` enqueues a job payload; it does not resolve recipients or render templates inline, keeping the caller's request fast (NFR-NOTIF-PERF).

## 4. Fan-out job (async)

`DispatchNotificationsCommand` (queue-consumer pattern, platform TDS §9 "not inline in HTTP requests"):

1. Dequeue a `NotificationEvent`.
2. **Resolve recipients.** A closed id list is used as-is; an entitlement-shaped resolver calls `EntitlementService` — for `new_post`/`went_live` this is "supporters entitled to this creator's content", the same call live-streaming's go-live flow makes (its technical-spec §4.3, §8).
3. For each recipient: look up `notification_prefs` (default per FR-NOTIF-2.1); skip a channel whose mode is `off`; route `digest_hourly`/`digest_daily` recipients to a **pending-digest** queue instead of sending now (unless the type is non-digestible per FR-NOTIF-2.2 — `went_live`, `prayer_response`); apply the per-`(user_id,type)` rate-limit counter (Memcache, per `memcache-keys.md`) — drop if over cap (FR-NOTIF-2.3); insert the `notifications` row with `dedup_key` (`INSERT ... ON DUPLICATE KEY UPDATE id=id` — a no-op on collision, satisfying FR-NOTIF-2.4 idempotently even under job retry); dispatch to `EmailNotifierChannel`/`InAppNotifierChannel` per the resolved immediate channels.

`SendDigestsCommand` (k8s CronJob, hourly + daily schedules) reads each user's pending-digest queue for the elapsed window, renders one rollup email per user via `EmailNotifierChannel`, and clears the queue — this is the only place multiple events become one email.

## 5. API surface (`SowingMeApi`)

Session auth + ownership middleware (a user only ever sees their own notifications/prefs).

| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/notifications` | Cursor-paginated in-app list (`pagination.md` §4 — unbounded, own-user feed) | FR-NOTIF-1.5, FR-NOTIF-3.3 |
| POST | `/notifications/{id}/read` | Mark one read | FR-NOTIF-1.5 |
| POST | `/notifications/read-all` | Mark all read | FR-NOTIF-1.5 |
| GET | `/notification-prefs` | Current user's full pref set (defaults filled in for display) | FR-NOTIF-2.1 |
| PUT | `/notification-prefs` | Upsert one or more `(type, channel) → mode` rows | FR-NOTIF-2.1 |

Response envelope for the list endpoint follows the cursor contract exactly: `{ "items": [...], "nextCursor": "..." }` (`pagination.md` §4) — no bespoke shape.

No dedicated internal HTTP endpoint is needed for other surfaces to trigger a notification: `NotifierInterface` is called in-process (PHP-DI injected) by the triggering surface's own service, the same way `MailerInterface`/`PaymentProviderInterface` are consumed elsewhere (platform TDS §5). Live-streaming's `/internal/stream/event` handler is the one exception that already crosses an HTTP boundary (media server → API) before it reaches this seam — that boundary belongs to live-streaming's technical-spec, not this one.

## 6. Frontend (SvelteKit)

- `js/Ubix/src/lib/components/notifications/NotificationBell.svelte` — unread badge, polls or (future) subscribes to the in-app list; opens a dropdown/list.
- `app/SowingMeJs/src/routes/settings/notifications/` — preferences page: a matrix of type × channel × mode, backed by `GET`/`PUT /notification-prefs`.
- Per `complete-js-guide.md`, side-effecting connections (a future live-update mechanism for the bell) would live in an `$effect` block with teardown; at M3 the bell polls the cursor endpoint rather than opening a persistent connection — no WebSocket is introduced by this surface (messaging/live-chat are the surfaces that need real-time transport).

## 7. External seam usage

- **`MailerInterface`** (Symfony Mailer, wired) — used by `EmailNotifierChannel`; this surface adds templates, not the mailer.
- **`EntitlementService`** — used by `NotificationService` to resolve broadcast-shaped recipient sets (FR-NOTIF-3.2).
- **Async jobs** (platform TDS §9) — `DispatchNotificationsCommand`, `SendDigestsCommand`.

## 8. Security & privacy

- `notification_prefs` and `notifications.payload` may carry PII-adjacent context (e.g. a supporter's display name in a "new subscriber" notice to a creator); treated per `sensitive-data-access.md` — no plaintext email addresses logged, access is always scoped to the owning user via ownership middleware.
- The dedup unique index is also a defence against a malicious or buggy caller trying to spam a user via repeated identical events.

## 9. Testing

- **Unit:** dedup-key derivation and the `UNIQUE`-constraint no-op path, preference-default resolution, rate-limit counter logic, digest windowing, `NotificationEvent` → channel routing matrix (immediate/digest/off × email/in_app). Non-container per the migration-test pattern.
- **Integration:** `DispatchNotificationsCommand` against a stubbed `EntitlementService` recipient resolver; `SendDigestsCommand` roll-up correctness across a synthetic multi-event window.
- **E2E (staging):** a `new_post` publish fans out to a subscribed test user's inbox and email; a repeated fan-out of the same event does not double-send; a digest user receives one rollup, not N emails.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite. Every table via migration.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-NOTIF-1.1 | `NotifierInterface`, `EmailNotifierChannel`, `InAppNotifierChannel` |
| FR-NOTIF-1.2/1.3 | `notifications.type`/`related_type`/`related_id`, `NotificationTypeEnum` |
| FR-NOTIF-1.4 | Latte email templates per type, in-app payload rendering |
| FR-NOTIF-1.5 | `notifications.read_at`, `GET /notifications`, `POST /notifications/{id}/read`, `POST /notifications/read-all` |
| FR-NOTIF-2.1 | `notification_prefs`, `GET`/`PUT /notification-prefs` |
| FR-NOTIF-2.2 | `SendDigestsCommand`, non-digestible type list |
| FR-NOTIF-2.3 | Memcache rate-limit counter in `DispatchNotificationsCommand` |
| FR-NOTIF-2.4 | `notifications.dedup_key` UNIQUE + `INSERT ... ON DUPLICATE KEY` |
| FR-NOTIF-2.5 | `DispatchNotificationsCommand` as an async job (platform TDS §9) |
| FR-NOTIF-3.1/3.2 | `NotifierInterface::notify()`, `EntitlementService` recipient resolution |
| FR-NOTIF-3.3 | `GET /notifications` cursor contract (`pagination.md` §4) |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
