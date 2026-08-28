# Sowing.me — MVP Roadmap / Work Matrix

**Status:** Active · updated 2026-08-27

Status vocabulary per row: `Todo` · `Docs` (SRS/spec in progress) · `Build` · `Review` · `Done` · `Dropped`. Flip the cell in the same commit as the work. Effort legend in charter §4.4.

## M0 — Foundation cleanup

| ID | Task | Effort | Status | Notes |
|---|---|---|---|---|
| M0-01 | Remove neptune leftovers: `broadcasting/*` routes in both JS apps, `AttributionLog`, `Transaction` model (replace with Sowing.me ledger later), `DuplicateProspect` repo, admin `explore` page | S | Todo | Verify nothing imports them; delete tests alongside |
| M0-02 | Decide Q2 (creator entity) + write `docs/data-models/core-entities.md` (ERD for users/creators/tiers/subscriptions/posts/transactions) | S | Todo | Charter §5 proposal is the starting point |
| M0-03 | SRS + tech spec for `authentication` (`docs/surfaces/authentication/`) — current session flow, lockout, password reset, roles | S | Todo | |
| M0-04 | SRS + tech spec for `registration` — supporter vs creator sign-up, onboarding wizard | S | Todo | Email confirmation already built; document it |
| M0-05 | SRS + tech spec for `creator-profile` | S | Todo | |
| M0-06 | Migration: `creators` table + `users.roles` cleanup | S | Todo | `bin/ubix migrate:*`; after M0-02 |
| M0-07 | CI: confirm phpunit/phpstan/phpcs + vitest run green on `dev` for all SowingMe apps | S | Todo | Baseline before build |

## M1 — Creator can publish

| ID | Task | Effort | Status | Notes |
|---|---|---|---|---|
| M1-01 | Auth hardening: lockout logic, password reset (token table reuse), secure cookie flags, role middleware | M | Todo | S1 |
| M1-02 | Creator onboarding wizard (JS) + `POST /creators` (API) | M | Todo | S2/S3 |
| M1-03 | Public creator page `/c/{slug}` (SSR in SvelteKit, `GET /creators/{slug}`) | M | Todo | S3 |
| M1-04 | Tiers: spec, migration, CRUD API, creator UI | M | Todo | S4 |
| M1-05 | Posts: spec, migration, CRUD API, visibility rules, creator library page becomes real | L | Todo | S5; existing `creator/library` shell |
| M1-06 | Media: spec, presigned S3 upload, `post_media`, limits | M | Todo | S6; Q4 |
| M1-07 | Creator dashboard (post mgmt half) wired to real data | M | Todo | S9; existing `creator/dashboard` shell |
| M1-08 | Tests + PHPStan/PHPCS clean for all M1 code | — | Todo | Rolling, per row |

## M2 — Supporter can pay (MVP / private beta)

| ID | Task | Effort | Status | Notes |
|---|---|---|---|---|
| M2-01 | `payments` spec: PaymentProviderInterface, ledger schema, webhook contract | S | Todo | S7; Q1, Q3 |
| M2-02 | Stripe Checkout + Billing: subscribe to tier, cancel, webhook → `subscriptions` + `transactions` | L | Todo | S7 |
| M2-03 | Tips (one-off Checkout) | S | Todo | S7 |
| M2-04 | Server-side tier gating on post reads | S | Todo | S5/S8 |
| M2-05 | Supporter feed + subscription management page | M | Todo | S8 |
| M2-06 | Creator earnings dashboard | M | Todo | S9 |
| M2-07 | Stripe Connect Express onboarding + payouts + commission | L | Todo | S10 |
| M2-08 | Admin: real `SowingMeAdminApi` controllers (users, creators, transactions), suspend/hide, `SowingMeAdminJs` pages | L | Todo | S11 |
| M2-09 | Explore: categories, search, cursor pagination | M | Todo | S12; existing `explore` shell |
| M2-10 | PII audit logging for billing/payout views | S | Todo | sensitive-data-access standard |
| M2-11 | Staging end-to-end beta test per charter §7 | S | Todo | Gate for M2 |
| M2-12 | Content policy + ToS/privacy pages on `SowingMeWeb` | S | Todo | Q6; product task |

## M3 — Growth loops (post-MVP, not yet sliced)

| ID | Task | Effort | Status | Notes |
|---|---|---|---|---|
| M3-01 | Affiliates: data model, referral links, banners page, admin list (existing stubs) | L | Todo | |
| M3-02 | Notifications (email + in-app) | M | Todo | |
| M3-03 | Messaging | L | Todo | |
| M3-04 | Organisation / church accounts | L | Todo | |
| M3-05 | Analytics dashboard | M | Todo | |
