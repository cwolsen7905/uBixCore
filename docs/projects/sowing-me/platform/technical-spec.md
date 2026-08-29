# Sowing.me — Platform TDS (Technical Design Spec)

**Altitude:** Platform (whole product) · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (SRS — what/why) · [`architecture.md`](architecture.md) (ADS — how as a system) · [`README.md`](README.md)
**Framework references:** [`complete-php-guide.md`](../../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../../architecture/complete-js-guide.md) · [`monorepo.md`](../../../architecture/monorepo.md) · standards in [`docs/standards/`](../../../README.md)

> **How we build any surface.** This is the shared engineering design every surface inherits: layering, the cross-cutting domain model, API conventions, the payments/media/notifications seams, and the per-surface doc template. A surface TDS specifies only what is new; it must not restate or contradict this.

## 1. Layering (per `complete-php-guide.md`)

Every request flows through the same layers; no layer is skipped:

```
HTTP (Slim 4 route) → Middleware (auth/role/ownership, CORS, error) →
Controller → Payload (validated input, DataType-typed) →
Service (domain logic) → Repository (DTO options in, DTO/Model out) → PDO/MariaDB
                                   ↳ external seams: PaymentProvider, MediaStorage, Mailer, Notifier
Response ← DTO → serialised envelope
```

- **DataType** wrappers (`php/Ubix/DataType/*`) for every domain scalar (no bare strings/ints crossing boundaries).
- **Payload** objects validate + coerce request input; controllers never read `$request` fields raw.
- **DTO** objects are the repository option-inputs and the response contracts.
- **Repository** is the only place SQL lives; options-DTO pattern for queries; returns Models/DTOs.
- **Service** holds domain logic and orchestrates repositories + external seams.
- **PHP-DI** wires everything; no `new` for services/repositories in controllers.

## 2. Apps & where code lives (per `monorepo.md`)

| App | Role | Exposure |
|---|---|---|
| `SowingMeApi` | Single product API for supporter + creator flows (role-gated) | public |
| `SowingMeAdminApi` | Admin/moderation API | internal |
| `SowingMeWeb` | Latte marketing site (static-ish) | public |
| `SowingMeJs` | SvelteKit product SPA | public |
| `SowingMeAdminJs` | SvelteKit admin SPA | internal |
| `SowingMeStream` (planned) | MediaMTX media server (live surface) | public (media) |
| `UbixCli` | Console: migrations, build/deploy, code review | — |

Shared PHP domain in `php/Ubix/`; shared Svelte in `js/Ubix/`. New surfaces add Controllers/Services/Repositories/DTOs/DataTypes under `php/Ubix/`, routes under the owning app, and UI under `SowingMeJs`/`SowingMeAdminJs`.

## 3. Cross-cutting domain model (ERD at platform altitude)

Tables added via `bin/ubix migrate:*` ([`migrations.md`](../../../standards/migrations.md)). Existing: `users`, `email_confirmation_tokens`. The **shape below is fixed in M0/M1 even where features ship later**, so post-MVP domains don't force a rework (NFR-EXT).

```
users ──1:1── creators ──1:N── tiers
  │              │   │            │
  │              │   └─1:N── posts ──1:N── post_media
  │              │            │  └─N:1── collections
  │              │            └─ visibility{public|subscribers|tier}, min_tier_id
  │              └─N:1── organizations (church/ministry)  [M3+]
  │
supporters (role) ──subscriptions(user × tier, status, provider ids)
  │
transactions (LEDGER: type{subscription|tip|gift|tithe|payout|fee|refund}, provider_ref, amount, currency, related_id)
  ├── tips / gifts (user → creator/org)
  ├── givings / tithes (user → creator/org/campaign)      [M3+]
  └── payouts (creator/org, payout_account, period)
payout_accounts (Stripe Connect id, status)
affiliates ── referrals ── attribution_logs               [M3, seed exists]
notifications ── notification_prefs                        [M3]
conversations ── messages                                   [M3]
comments / reactions (on posts)                             [M3]
prayer_requests ── prayers                                  [M3+]
reports ── moderation_actions ── audit_logs                 [M2/M3]
live_streams (+ live_* surface tables)                      [M3]
```

**Design commitments made now for later:**
- `creators` and `organizations` are **separate entities** (a creator may belong to an org); pages/tiers/payouts attach to either via a polymorphic `owner_type/owner_id` or an org FK on creator. (ADS §4 picks the concrete shape.)
- The **`transactions` ledger is generic from day one** — its `type` enum already includes `gift`/`tithe`/`payout`/`fee`/`refund` so giving/tithing (M3+) is data, not schema change.
- `posts.type` and `posts.visibility` exist at M1 so devotional/Scripture types (M3+) are new enum values, not new tables.
- Money is stored in **minor units (INT) + currency code**, never floats.

New enums live in `php/Ubix/Enum/` with matching `DataType/Enum/*` wrappers (e.g. `RoleEnum`, `PostTypeEnum`, `PostVisibilityEnum`, `TransactionTypeEnum`, `SubscriptionStatusEnum`).

## 4. API conventions

- **Envelope & errors:** consistent JSON envelope; typed error model (validation errors carry field paths). Follow the app's existing controller/response pattern.
- **Auth:** PHP session cookie (charter §5); every protected route runs auth + **role** + **ownership** middleware. Token auth deferred until a mobile/public-API need (seam reserved — ADS §security).
- **Pagination** ([`pagination.md`](../../../standards/pagination.md)): **cursor** for feeds/explore/comments; **offset** for admin tables.
- **Idempotency** on money-mutating and webhook endpoints (idempotency keys; webhook dedup by provider event id).
- **Validation** via Payload objects; reject unknown fields; DataType coercion at the boundary.
- **Versioning:** additive-first; breaking changes are new routes.

## 5. Shared external seams (interfaces, not vendors)

Each is a PHP interface in `php/Ubix/Service/*` so the vendor is swappable and testable:

- **`PaymentProviderInterface`** — Stripe implementation (Checkout, Billing, Connect, webhooks). The ledger (`transactions`) is ours; Stripe is source of truth for movement. One place verifies webhook signatures.
- **`MediaStorageInterface`** — S3-compatible; presigned upload + signed read URLs; used by content, media, and live-VOD.
- **`MailerInterface`** — Symfony Mailer (wired). Transactional email.
- **`NotifierInterface`** — fan-out to email + in-app (M3); `SlackService` exists but stays inert (no creds) for ops alerts only.
- **`EntitlementService`** — single source of truth answering "may user X access resource Y?" across posts, media, live, comments, messaging. Every gated read calls it; never client-side gating.

## 6. Access control & entitlement

- Roles: `supporter`, `creator`, `org_admin`, `admin` (multi-hold). Middleware resolves role; services resolve **ownership** (this creator owns this post) and **entitlement** (this supporter is subscribed to a permitted tier).
- `EntitlementService.resolve(user, resource)` centralises tier-gating logic so posts, media URLs, live playback, and comments enforce identically (mirrors the live surface's resolver — that is the reference implementation).
- Gated media is served via short-lived signed URLs minted only after an entitlement check.

## 7. Payments & ledger design (the money seam)

- **No card data touches us.** Subscriptions via hosted Stripe Checkout; recurring lifecycle via Stripe Billing; creator payouts via Stripe Connect Express (KYC at Stripe).
- Every money event writes a `transactions` row (subscription charge, tip, gift, tithe, fee, payout, refund) with `provider_ref`, minor-unit amount, currency, and a relation to creator/org/post/stream.
- Webhooks are the truth channel: signature-verified, idempotent by event id, reconciled against the ledger. Commission is a `fee` row derived at charge time.
- Disputes/refunds reflect back into the ledger and creator balance.

## 8. Media & content pipeline

- Uploads: presigned direct-to-bucket; server records `post_media` with type/size/checksum; type/size validation; image derivatives generated async.
- Delivery: public/cacheable via CDN; gated via signed expiring URLs post-entitlement.
- Video at MVP is external embed (charter Q4); native VOD arrives through the live-streaming surface's record→media-storage path.

## 9. Async work, jobs & scheduling

- Long/async tasks (image derivatives, notification fan-out, VOD muxing, payout scheduling, digests) run as jobs/CronJobs (`Console/Command/` + k8s CronJob) — not inline in HTTP requests.
- Notification fan-out is rate-limited and deduped (FR-NOTIF-2).

## 10. Frontend design (per `complete-js-guide.md`)

- SvelteKit 2 / Svelte 5 / Tailwind; shared components in `js/Ubix/`; theme system (`THEME_GUIDE.md`, light/dark CSS vars).
- Auth via session cookie; SPA calls `SowingMeApi`. Entitlement is enforced server-side — the SPA renders paywalls but never decides access.
- Reuse `CreatorSidebar`/`Sidebar`/`ThemeToggle`; neptune leftover routes (`broadcasting/*`, admin `explore`) are repurposed/removed per the owning surface.

## 11. Testing & quality gates

- **Unit** (non-container per migration-test pattern): DataTypes, Payload validation, `EntitlementService` matrices, ledger derivations, state machines, webhook handlers.
- **Integration:** repository queries against a test schema; provider webhook shapes; auth/role/ownership middleware.
- **E2E (staging):** the charter §7 flow (creator→tier→post→subscribe→gated view→earnings→payout).
- Gates on every merge: `phpunit` (strict), `phpstan` (max), `phpcs` (custom sniffs), JS suite. Every table via migration; every surface has SRS+TDS before its first migration.

## 12. Per-surface doc template (what a surface TDS must contain)

When a surface is picked up, its `technical-spec.md` provides: component map · new tables (migrations) · new enums/DataTypes · API endpoints (with auth/role) · service/entitlement additions · frontend components · external-seam usage · `## Requirement traceability` (each `FR-*` → realiser) · testing · Document control. It cites this platform TDS for anything shared and only documents deltas.

## 13. Requirement traceability (domain → realiser, excerpt)

| Domain (SRS §5) | Realised by |
|---|---|
| FR-IAM | session middleware, `RoleEnum`, `users` + auth controllers |
| FR-MEM/FR-CONT | `tiers`/`posts`/`post_media`/`collections`, `EntitlementService`, gated reads |
| FR-PAY/FR-FIN | `PaymentProviderInterface` (Stripe), `transactions` ledger, webhook controller, Connect payouts |
| FR-MED | `MediaStorageInterface`, presigned uploads, signed URLs |
| FR-NOTIF | `NotifierInterface`, jobs, `notification_prefs` |
| FR-LIVE | `docs/surfaces/live-streaming/technical-spec.md` |
| FR-ORG/FR-GIVE/FR-PRAY | reserved schema (§3) + future surface TDSs |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial platform TDS — layering, cross-cutting domain model, seams, per-surface template. |
