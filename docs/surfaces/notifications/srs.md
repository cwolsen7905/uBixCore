# Notifications — Software Requirements Specification (SRS)

**Surface:** `notifications` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3 (post-MVP) · **Prerequisites:** none for the seam itself; individual trigger types depend on their owning surface (`content-posts` M1 built, `subscription-tiers`/`payments`/`payouts` M2 built, [`live-streaming`](../live-streaming/README.md) M3 not yet built, prayer requests FR-PRAY M3+ not yet built)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md)
**Upstream:** platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.14 (FR-NOTIF-1, FR-NOTIF-2) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) §4.2 · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This SRS decomposes the two platform-altitude requirements — **FR-NOTIF-1** (channels/types) and **FR-NOTIF-2** (prefs/digest/rate-limit) — into surface-level requirements, numbered as `FR-NOTIF-1.x` / `FR-NOTIF-2.x` so every requirement here traces to its platform parent without inventing a parallel ID scheme.

## 1. Purpose

Give every Sowing.me surface exactly one way to tell a user something happened: a new post from a followed creator, a creator going live, a creator gaining a subscriber, a payout landing, a tip/gift received, or a response to a prayer request. This surface owns delivery (email + in-app), the user's channel/frequency preferences, and the fan-out mechanics (async, rate-limited, deduplicated) so no other surface hand-rolls its own emailing or in-app badge logic.

## 2. Scope

**In scope:** `NotifierInterface` fan-out to email (Symfony Mailer) and in-app; the six notification types named in the platform SRS (new post, went-live, new subscriber, payout, tip/gift, prayer response); `notification_prefs` per user per type per channel; digesting (hourly/daily rollup); rate-limiting and dedup; in-app notification list + unread count + read/read-all; async job-driven fan-out.

**Out of scope (this surface):** push notifications / SMS (future channel, seam reserved — see [`technical-spec.md`](technical-spec.md) §4); the business logic that decides *when* a payout or tip happened (owned by `payments`/`payouts`); the content of a prayer response (owned by the future prayer-requests surface) — this surface only delivers the notice once told.

## 3. Context — why a single seam

The platform TDS names `NotifierInterface` as one of five shared external seams (`technical-spec.md` §5) precisely so posts, subscriptions, payments, live-streaming, and prayer requests don't each grow their own mailer call and in-app badge table. Live-streaming's `went-live` notification (its FR-32/FR-33) is written against this surface rather than re-specifying delivery — this SRS is what makes that citation resolvable.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Notification** | A single row in `notifications`: one event, one recipient, one or more delivered channels. |
| **Channel** | `email` or `in_app` (seam reserved for `push`/`sms` later). |
| **Notification type** | One of `new_post`, `went_live`, `new_subscriber`, `payout`, `tip_gift`, `prayer_response`. |
| **Preference** | A row in `notification_prefs`: this user's channel + mode for this type. |
| **Digest** | A rolled-up email combining multiple pending notifications of digestible types into one message on a schedule, instead of one email per event. |
| **Dedup key** | The `(user_id, type, related_type, related_id)` tuple that makes re-firing the same event a no-op. |
| **Fan-out** | The async process of resolving "who should be notified" (often via `EntitlementService`, e.g. entitled supporters of a creator) and enqueuing one notification per recipient. |

## 5. Personas & primary user stories

- **Supporter.** "As a subscriber, I want to know when my creator posts, goes live, or responds to my prayer request — by email if I'm not in the app, or just a badge if I am — without getting spammed every time something happens."
- **Creator.** "As a creator, I want to know immediately when I get a new subscriber, a tip, or a payout lands, so I can say thanks and track my income."
- **Any user.** "As a user, I want to control which notifications I get and how (immediate vs. daily digest vs. off), per type."
- **Another surface (system actor).** "As the live-streaming/messaging/comments surface, I want one call that fans a notification out to the right entitled users without me writing email or in-app-badge code."

## 6. Functional requirements

### 6.1 Channels & types (parent: platform FR-NOTIF-1)

- **FR-NOTIF-1.1** `NotifierInterface` (platform TDS §5) fans a notification out to **email** (Symfony Mailer, already wired per brief §3.2) and **in-app** (a `notifications` row surfaced in-product); a caller may request either or both channels, subject to the recipient's preference (§6.2).
- **FR-NOTIF-1.2** Supported notification types at M3: `new_post`, `went_live`, `new_subscriber`, `payout`, `tip_gift`, `prayer_response`. New types are additive enum values, not schema changes (mirrors the platform's `posts.type` extensibility pattern, TDS §3).
- **FR-NOTIF-1.3** Each notification carries a `related_type`/`related_id` (e.g. `post`/`123`, `live_stream`/`45`) so the in-app UI can deep-link to the source.
- **FR-NOTIF-1.4** Each type has one transactional email template (Latte, per `SowingMeWeb`'s existing template convention) and one in-app rendering; a template renders from the notification's structured payload, never free-form HTML passed by the caller.
- **FR-NOTIF-1.5** In-app notifications are listed with unread count; a user can mark one or all read.

### 6.2 Preferences, digesting, rate-limit/dedup (parent: platform FR-NOTIF-2)

- **FR-NOTIF-2.1** `notification_prefs` holds one row per `(user_id, type, channel)`; `mode` is `immediate`, `digest_hourly`, `digest_daily`, or `off`. A user without an explicit row gets the type's documented default (email: digest_daily; in-app: immediate).
- **FR-NOTIF-2.2** Digest mode batches a user's pending notifications of digestible types into a single rollup email on the hourly/daily schedule; `went_live` and `prayer_response` are **not digestible** (time-sensitive) and always send immediate email if the channel is on.
- **FR-NOTIF-2.3** Fan-out is rate-limited per `(user_id, type)` window (default: no more than N notifications of the same type per hour) so a noisy source (e.g. rapid post edits) cannot flood a user; excess events are dropped, not queued.
- **FR-NOTIF-2.4** Fan-out is deduplicated by dedup key: re-delivering the same `(user_id, type, related_type, related_id)` is a no-op (e.g. a retried "went live" event for the same stream never double-notifies a supporter — mirrors live-streaming FR-33 exactly).
- **FR-NOTIF-2.5** Fan-out runs as an async job (platform TDS §9), never inline in the triggering HTTP request; a slow or failing notification never blocks the caller's response.

### 6.3 The fan-out seam (surface-owned addition)

- **FR-NOTIF-3.1** Any surface triggers a notification by calling `NotifierInterface` (or enqueuing a `NotificationEvent`) with a type, a recipient resolution (a user id, or a resolver like "entitled supporters of creator X"), and a structured payload — never by calling Symfony Mailer or writing to a notification table directly.
- **FR-NOTIF-3.2** Recipient resolution for creator-broadcast-shaped events (`new_post`, `went_live`) calls `EntitlementService` to resolve the entitled audience, exactly as live-streaming's go-live flow does (technical-spec.md §4.3 `/internal/stream/event` → notify).
- **FR-NOTIF-3.3** The in-app notification list is exposed via a cursor-paginated API endpoint (`pagination.md` §4 — unbounded, append-only per user).

## 7. Non-functional requirements

- **NFR-NOTIF-PERF** Fan-out and delivery are fully async (jobs/CronJobs per platform TDS §9); enqueue latency from trigger to job pickup ≤ a few seconds under normal load.
- **NFR-NOTIF-REL** Delivery is best-effort at-least-once with idempotent dedup (FR-NOTIF-2.4) so a retried job never double-sends; a failed email delivery is retried with backoff and does not lose the in-app row.
- **NFR-NOTIF-PRIV** Notification payloads avoid embedding sensitive PII beyond what's needed to render the message; email addresses/preferences are PII per `sensitive-data-access.md`.
- **NFR-NOTIF-EXT** Adding a notification type or a channel (push/SMS) is an additive enum + a new `NotifierInterface` implementation, never a change to the caller contract (platform ADS §9 extensibility row).
- **NFR-STD** DataType/Payload/DTO/Repository, PHP-DI, Slim 4; PHPStan max; custom sniffs; strict PHPUnit; every table via `bin/ubix migrate:*`; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **`SowingMeApi`**: in-app notification list (cursor), mark-read, `notification_prefs` CRUD, internal trigger entry points used by other surfaces' services (in-process call, not HTTP — see technical-spec §4).
- **Product SPA (`SowingMeJs`)**: notification bell/list component, unread badge, preferences page.
- **Jobs/CronJobs**: fan-out worker, digest roll-up scheduler.

## 9. Constraints & assumptions

- Symfony Mailer is already wired (brief §3.2); this surface adds templates and the queueing/dedup/rate-limit layer around it, not the mailer itself.
- `EntitlementService` (platform TDS §6) must exist to resolve "entitled supporters" recipient sets — it does, for posts/tiers today; live-streaming's entitlement resolver (its technical-spec §8) is the reference this surface's recipient resolution follows for `went_live` once that surface ships.
- Trigger call sites in `content-posts`, `payments`/`payouts` land as those surfaces are updated to call `NotifierInterface`; this SRS does not re-specify those surfaces.

## 10. Acceptance criteria (surface DoD)

1. A `new_post` event from a creator fans out (async) to entitled subscribers per their preference (immediate email, digest, or in-app only); a second identical event (e.g. a retried job) does not double-notify.
2. A user sets `payout` email to `off` and `in_app` to `immediate`; a subsequent payout produces only an in-app row.
3. A user on `digest_daily` for `new_post` receives one rollup email per day summarizing the day's posts, not one email per post.
4. Rate-limiting is observable: firing more than the configured cap of the same type/user within a window drops the excess without erroring the caller.
5. The in-app notification list is cursor-paginated, shows unread count, and supports mark-read / mark-all-read.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Exact rate-limit caps per type (§6.2 FR-NOTIF-2.3)? | Product-tunable config, generous defaults (e.g. 20/hour/type) | FR-NOTIF-2.3 |
| Q2 | Push/SMS channel timeline? | Not in this surface; seam reserved (NFR-NOTIF-EXT) | future |
| Q3 | Does `prayer_response` need privacy scoping (e.g. only the requester is notified, never the whole tier)? | Yes — single-recipient by default; revisit with FR-PRAY | prayer-requests surface |

## 12. Traceability

Each FR maps to schema/endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **M3-02**. Live-streaming's FR-32/FR-33 and any future messaging/comments/prayer notification triggers cite this SRS as their delivery mechanism rather than re-specifying it.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
