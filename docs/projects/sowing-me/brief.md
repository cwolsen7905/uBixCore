# Sowing.me — Project Brief

**Status:** Active · v0.1 · 2026-08-27 · Owner: Christopher W. Olsen

## 1. What this document is

The orientation doc for anyone (human or agent) picking up Sowing.me work. It says what the product is, what is *actually* in the repository today, and the direction of travel. It does not hold the plan — that's [`charter.md`](charter.md) and [`mvp-roadmap.md`](mvp-roadmap.md).

## 2. Vision

A purpose-built creator platform for faith-based creators: creators publish free and subscriber-only content, supporters subscribe / tip / buy, the platform takes a commission, and creators + churches earn referral revenue through an affiliate programme. See [`docs/pitch-deck.md`](../../pitch-deck.md) for positioning, market, and business model.

**Tagline:** *Planting. Believing. Thriving.*

**Primary personas**

| Persona | Needs |
|---|---|
| **Creator** (pastor, worship leader, podcaster, teacher, author) | Page, tiers, post content, see earnings, message supporters, get paid out |
| **Supporter** | Discover creators, subscribe/tip, consume exclusive content, manage subscriptions |
| **Affiliate** (creator or church/org) | Referral links + banners, track referrals and payouts |
| **Admin** (us) | Moderate creators/content, manage affiliates, see platform metrics, handle payouts |

## 3. Where the stack lives today (inventory, 2026-08-27)

Everything below was verified against the working tree, not the pitch deck.

### 3.1 Apps (`app/`)

| App | Kind | State |
|---|---|---|
| `SowingMeWeb` | Slim 4 + Latte, public marketing site | **Working.** Routes: `/`, `/signup`, `/for-creators`, `/how-it-works`, `/pricing`, `/about`, `/faq`, `/testimonials` → `templates/sowing-me-web-v1/*.latte`. Static copy. |
| `SowingMeApi` | Slim 4 JSON API for the product | **Auth only.** `POST/GET /auth` (login / validate session), `POST /logout`, `POST /register`, `GET /confirm-email`, CORS `OPTIONS`. PHP-session based. Registration issues a 64-hex confirmation token and emails a link (`APP_URL/confirm-email?token=`). |
| `SowingMeAdminApi` | Slim 4 JSON API for admin | **Skeleton.** `GET/POST /auth`, `GET /affiliates`, `GET /affiliate/{id}` — affiliate endpoints wired to controllers that don't exist yet in `php/Ubix/Controller/` (only `SowingMeApi/` and `SowingMeWeb/` controllers exist). Treat as stub. |
| `SowingMeJs` | SvelteKit 2 / Svelte 5 / Tailwind product frontend | **UI shell.** Real-ish pages: `login` (17 lines, delegates to `LoginPage.svelte`), `confirm-email` (281), `creator/dashboard` (448), `creator/library` (298), `explore` (392), `settings` (484). Stubs (≤8 lines): `/`, `affiliates`, `affiliates/banners`, `broadcasting/models`, `broadcasting/fanclubs`. The `broadcasting/*` routes are neptune leftovers with no Sowing.me meaning. Has a theme system (`THEME_GUIDE.md`, light/dark via CSS vars), `CreatorSidebar`, `Sidebar`, `ThemeToggle`. |
| `SowingMeAdminJs` | SvelteKit admin frontend | **UI shell / neptune leftover.** `login`, `settings`, `explore`, `affiliates`, `broadcasting/models`. |
| `UbixCli` | Console | Working — `bin/ubix`, migrations, build/deploy, code review. |

Each app carries `dev-/staging-/main-/sandbox-*.yaml` K8s manifests.

### 3.2 Domain layer (`php/Ubix/`)

- **Models:** `User`, `EmailConfirmationToken`, plus neptune carry-overs (`AttributionLog`, `Country`, `State`, `Transaction`, `MachineCodeReview*`).
- **Repositories:** `User`, `EmailConfirmationToken`, `BillingTransaction`, `Country`, `State`, `DuplicateProspect`, `SchemaMigration`.
- **Controllers:** `SowingMeApi/AuthController`, `SowingMeApi/EmailConfirmationController`, `SowingMeWeb/SowingMeWebController`.
- **Services:** mailer (Symfony Mailer), AWS SDK present (no S3 usage yet), migration subsystem.

### 3.3 Database (`sql/`)

Baseline `sql/sowingme.sql` has exactly two tables:

- `users` — `id, display_name, password_hash, email, first_name, last_name, status(active|inactive|suspended|pending), roles(varchar), failed_login_attempts, last_failed_login, last_login, created_at, updated_at`
- `email_confirmation_tokens`

Migrations: `sql/migrations/00000000000000_init_schema_migrations.sql` only. **All new schema goes through `bin/ubix migrate:*`** per [`docs/standards/migrations.md`](../../standards/migrations.md).

### 3.4 What does *not* exist yet (despite the pitch deck)

No creator profiles/pages, no subscription tiers, no content/posts model, no media storage, no payments (no Stripe/PayPal dependency in `composer.json` or `package.json`), no payouts, no affiliate data model, no messaging, no analytics, no moderation tooling, no notifications. The pitch deck's "✅ Payment processing integration / Affiliate system / Creator dashboard / Subscriber management" are **not built**.

## 4. Where it's going

### 4.1 Stack (fixed)

PHP 8.3 / Slim 4 / PHP-DI / Latte for APIs and the marketing site; SvelteKit 2 + Svelte 5 + Tailwind for the product and admin UIs; MariaDB via the migration runner; K8s via GitLab CI. Shared PHP framework lives in `php/Ubix/`, shared Svelte in `js/Ubix/` (npm package name is still `vsm`). Follow [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository pattern) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md).

### 4.2 Product shape (MVP)

The MVP is "a creator can get paid by a supporter": creator onboarding → public creator page with tiers → supporter subscribes (card) → gated content → creator sees earnings → payout. Everything else (affiliates, messaging, orgs/tithing, prayer requests, mobile apps) is post-MVP. Phasing is in the charter.

### 4.3 Surfaces

Each product surface gets `docs/surfaces/<slug>/srs.md` + `technical-spec.md` as it is picked up. Planned surface list (rows in the roadmap): `authentication`, `registration`, `creator-profile`, `subscription-tiers`, `content-posts`, `media-storage`, `payments`, `payouts`, `supporter-feed`, `explore`, `creator-dashboard`, `admin-console`, `affiliates`, `notifications`, `messaging`.

## 5. What we're not touching

- `project-neptune` app code — ubixcore is a fork of neptune's *framework and tooling*, not its apps. Neptune leftovers in ubixcore (`broadcasting/*` routes, `AttributionLog`, `Transaction`, admin `explore` page) are to be **deleted or repurposed**, not maintained.
- The marketing site's copy/design — out of scope for engineering rows; content changes are their own small tasks.

## 6. Operating principles

1. **Docs before code for each surface** — an SRS + tech spec (even short) precedes the first migration. Keeps the plan and the build in sync.
2. **Migrations only** — no edits to `sql/sowingme.sql` baseline; the runner is the forward schema source.
3. **Payments are a boundary** — one `PaymentProvider` seam (Stripe first); nothing else in the codebase talks to the processor.
4. **Sensitive data is gated + audited** — per [`sensitive-data-access.md`](../../standards/sensitive-data-access.md); creator payout details and supporter billing are the PII hot-spots.
5. **Dead neptune code is retired as we go**, not deferred.
6. **Roadmap status flips land with the code** (same commit).

## 7. Open questions owned by this brief

See charter §12. The two that block the MVP schema: payment processor choice (Stripe assumed) and whether "creator" is a role on `users` or a separate `creators` entity (recommended: separate entity, 1:1 with a user, so orgs/churches can later own multiple).

## 8. Document control

| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial brief from repo inventory. |
