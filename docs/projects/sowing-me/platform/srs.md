# Sowing.me — Platform SRS (Software Requirements Specification)

**Altitude:** Platform (whole product) · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`technical-spec.md`](technical-spec.md) (TDS — how in code) · [`architecture.md`](architecture.md) (ADS — how as a system) · [`README.md`](README.md)
**Upstream:** project [`../charter.md`](../charter.md) (scope/phases/governance) · [`../brief.md`](../brief.md) (inventory) · [`../mvp-roadmap.md`](../mvp-roadmap.md) (work matrix)

> This SRS specifies the **entire** Sowing.me product, not any single surface. It is the breadth reference: every persona and capability domain, tagged by milestone. Depth lives in per-surface SRSs under [`docs/surfaces/`](../../../surfaces/). The charter owns scope/phasing; this doc owns the requirement set the architecture must satisfy so features can be added later without rework.

## 1. Product definition

Sowing.me is a **membership and monetisation platform for Christian content creators** — a Patreon-class product (creator pages, subscription tiers, gated content, tips, payouts, discovery, community) **rebuilt for a faith audience** and extended with capabilities Patreon does not have: church/organization accounts, tithing & giving, prayer requests, and devotional/Scripture-native content. It is the first and flagship product on uBix Core.

**Tagline:** *Planting. Believing. Thriving.*

**We are not** "just the livestream part of Patreon" and not "generic Patreon." The full membership economy is in scope over time; the faith-native domains are first-class, not bolt-ons; the trust & safety posture is values-aligned by design.

## 2. What "all of Patreon" means here (capability parity + our extensions)

| Patreon capability | Sowing.me | Milestone |
|---|---|---|
| Creator page / profile | Creator profile & page (`/c/{slug}`) | M1 |
| Membership tiers | Tiers with benefits, free tier | M1 |
| Posts (text/image/audio/video), gated by tier | Content & posts, tier-gated; **+ devotional/Scripture content types, series/collections, reading plans** | M1 (base), M3+ (faith types) |
| Media hosting | Media storage & delivery (S3 + signed URLs + CDN) | M1 |
| Tips / one-off support | Tips & gifts; **+ in-stream tips** | M2 |
| Subscriptions billing | Payments (Stripe Checkout + Billing) | M2 |
| Creator payouts | Payouts (Stripe Connect Express) + commission + tax docs | M2 |
| Explore / discovery | Discovery & explore, search, **faith topic/denomination categories** | M2 |
| Member feed | Supporter feed + subscription management | M2 |
| Creator analytics | Creator dashboard & analytics | M2 |
| Comments / reactions | Comments, reactions, community | M3 |
| Direct messages | Messaging / DMs | M3 |
| Notifications | Notifications (email + in-app) | M3 |
| Affiliate/referral | Affiliate & referral program (**+ church revenue-share**) | M3 |
| Live video | Live streaming (see surface) | M3 |
| — (no equivalent) | **Church / organization accounts** (multi-creator ministries) | M3+ |
| — | **Giving & tithing** (recurring + one-off, campaign goals) | M3+ |
| — | **Prayer requests & walls** | M3+ |
| Group chat / community | Community spaces / groups | Future |
| Mobile apps | Native apps | Future |
| API | Public API & webhooks | Future |
| Multi-currency / i18n | Localization | Future |

Parity is the floor; the extension rows are the reason the platform exists.

## 3. Personas

| Persona | Description | Primary needs |
|---|---|---|
| **Creator** | Pastor, worship leader, teacher, author, podcaster, Christian artist | Page, tiers, publish content, go live, see earnings, get paid, message supporters |
| **Supporter / Patron** | A believer supporting a creator/ministry | Discover, subscribe/tip/give, consume gated content, manage subscriptions, community |
| **Church / Organization admin** | A ministry running a shared account with multiple creators | Org page, multiple contributors, giving/tithing, campaigns, consolidated payouts, referral revenue |
| **Affiliate** | Creator or church referring others | Referral links/banners, track referrals & revenue share |
| **Platform admin / moderator** | Us | Moderate creators/content, manage affiliates & orgs, platform metrics, handle payouts & disputes, enforce content policy |
| **Visitor / Seeker** | Not signed in | Discover creators, see public content & previews, clear path to join |

## 4. Faith-native differentiators (requirements, not flavour text)

- **FR-FAITH-1** Content policy and moderation are **values-aligned**: an explicit, published content policy grounded in the platform's faith posture; moderation tooling enforces it (M2 base, evolves).
- **FR-FAITH-2** First-class **church / organization accounts**: an org owns a page, has multiple creator-contributors, and can receive giving and consolidated payouts (M3+).
- **FR-FAITH-3** **Giving & tithing** flows distinct from memberships: recurring or one-off gifts to a creator/org/campaign, with goals and progress (M3+).
- **FR-FAITH-4** **Prayer requests & walls**: supporters submit prayer requests; creators/community can pray/respond; privacy controls (M3+).
- **FR-FAITH-5** **Devotional/Scripture-native content**: content types for devotionals, reading plans, sermon series/collections, Scripture references (M3+; base posts M1).
- **FR-FAITH-6** **Church affiliate revenue-share**: churches earn referral revenue for creators/supporters they bring (M3).

These requirements shape the M0/M1 data model even though they ship later (see [`architecture.md`](architecture.md) §extensibility): `creators` vs `organizations`, a generic `transactions` ledger that already distinguishes subscription/tip/gift/tithe/payout/fee, and post `type` + visibility from day one.

## 5. Functional requirements by domain

Platform altitude — a representative set per domain; each surface SRS expands its own. Milestone in brackets.

### 5.1 Identity & Access (FR-IAM) [M0/M1]
- FR-IAM-1 Email/password registration with email confirmation (exists), login, logout, session management.
- FR-IAM-2 Roles: `supporter`, `creator`, `org_admin`, `admin`; a user may hold several. Server-side role + ownership checks on every protected route.
- FR-IAM-3 Password reset; account lockout after repeated failures (fields exist); secure cookie policy (`SameSite=Lax`, `Secure`).
- FR-IAM-4 Account status lifecycle: `pending`→`active`, plus `suspended`/`inactive`.

### 5.2 Onboarding & Registration (FR-ONB) [M1]
- FR-ONB-1 Supporter sign-up (fast path).
- FR-ONB-2 Creator onboarding wizard: identity → profile → first tier → payout account.
- FR-ONB-3 Organization onboarding (M3+): create org, invite contributors, verify ministry.

### 5.3 Creator Profile & Page (FR-PROF) [M1]
- FR-PROF-1 Public creator page at `/c/{slug}` with bio, avatar/banner, category, external links.
- FR-PROF-2 Creator is a distinct entity 1:1 with a user (charter Q2); slug unique, editable with redirect history.
- FR-PROF-3 A page lists tiers, recent public posts, upcoming/live streams, and a subscribe CTA.

### 5.4 Memberships & Tiers (FR-MEM) [M1]
- FR-MEM-1 Per-creator tiers: name, price, billing interval, benefits, description; an implicit free tier.
- FR-MEM-2 Supporters subscribe to exactly one active tier per creator (upgrade/downgrade supported).
- FR-MEM-3 Tier ordering defines gating precedence (higher tier sees lower-tier content).

### 5.5 Content & Posts (FR-CONT) [M1 base; M3+ faith types]
- FR-CONT-1 Posts of type text / image / audio / video / external-embed; visibility public / subscribers / specific-tier.
- FR-CONT-2 Collections/series group posts (e.g. a sermon series); post ordering.
- FR-CONT-3 Gating resolved **server-side** on every read; never trust client visibility.
- FR-CONT-4 Faith content types: devotional, reading plan, Scripture reference metadata (M3+).
- FR-CONT-5 Drafts, scheduled publish, edit history.

### 5.6 Media Storage & Delivery (FR-MED) [M1]
- FR-MED-1 Direct-to-bucket presigned uploads to S3-compatible storage; type/size limits; virus/type validation.
- FR-MED-2 Signed, expiring URLs for gated media; CDN in front for public/cacheable media.
- FR-MED-3 Image derivatives (thumbnails); audio/video handled by embed initially, native VOD via live-streaming surface.

### 5.7 Discovery & Explore (FR-DISC) [M2]
- FR-DISC-1 Public explore page: featured/trending creators, categories, faith topics/denominations.
- FR-DISC-2 Search creators by name/category; (content search later).
- FR-DISC-3 Creator cards show tier entry price and a subscribe CTA.

### 5.8 Supporter Feed & Subscription Management (FR-FEED) [M2]
- FR-FEED-1 Logged-in home aggregates posts from subscribed creators (cursor-paginated).
- FR-FEED-2 Manage subscriptions: view, upgrade/downgrade, cancel, billing history.
- FR-FEED-3 Saved/bookmarked posts (nice-to-have).

### 5.9 Payments (FR-PAY) [M2]
- FR-PAY-1 Hosted Stripe Checkout for subscriptions; Billing for recurring lifecycle; **no card data on our systems** (PCI scope stays at processor).
- FR-PAY-2 One-off tips and gifts; in-stream tips (live surface).
- FR-PAY-3 Webhook handling with signature verification; our `transactions` ledger records every money event; Stripe is source of truth for movement.
- FR-PAY-4 Failed-payment dunning, refunds, chargeback/dispute reflection.

### 5.10 Payouts & Creator Finance (FR-FIN) [M2]
- FR-FIN-1 Stripe Connect Express onboarding for creators/orgs; KYC handled by Stripe.
- FR-FIN-2 Platform commission (default 10% beta, configurable); scheduled payouts; balance & pending views.
- FR-FIN-3 Tax documentation (1099-style) surfaced from Stripe; earnings statements.

### 5.11 Creator Dashboard & Analytics (FR-DASH) [M2]
- FR-DASH-1 Earnings (subscriptions, tips, gifts), subscriber count & list, churn.
- FR-DASH-2 Post/stream performance metrics; payout status.

### 5.12 Admin Console (FR-ADMIN) [M2]
- FR-ADMIN-1 User/creator/org list with status changes (suspend, reinstate).
- FR-ADMIN-2 Transaction/ledger views; payout oversight; dispute handling.
- FR-ADMIN-3 Moderation queue; content takedown; affiliate & org management.

### 5.13 Trust, Safety & Content Policy (FR-TRUST) [M2/M3]
- FR-TRUST-1 Published, faith-aligned content policy + ToS/privacy.
- FR-TRUST-2 Report content/creator; moderation queue; hide/remove; suspend.
- FR-TRUST-3 Age/sensitivity flags; audit logging of moderation actions (`sensitive-data-access.md`).

### 5.14 Notifications (FR-NOTIF) [M3]
- FR-NOTIF-1 Email + in-app notifications: new post, went-live, new subscriber, payout, tip/gift, prayer response.
- FR-NOTIF-2 Per-user notification preferences; digesting; rate-limiting/dedup.

### 5.15 Comments, Reactions & Community (FR-COMM) [M3]
- FR-COMM-1 Comments and reactions on posts (entitlement-gated); creator moderation.
- FR-COMM-2 Live chat (live surface). Community spaces/groups: future.

### 5.16 Messaging / DMs (FR-MSG) [M3]
- FR-MSG-1 Supporter↔creator direct messages; creator broadcast messages to a tier; abuse controls.

### 5.17 Affiliate & Referral Program (FR-AFF) [M3]
- FR-AFF-1 Referral links + banners; attribution tracking (`AttributionLog` exists as a seed); revenue-share payouts.
- FR-AFF-2 Church/org affiliate revenue-share (FR-FAITH-6).

### 5.18 Live Streaming (FR-LIVE) [M3]
- Full surface: [`docs/surfaces/live-streaming/`](../../../surfaces/live-streaming/README.md). WHIP browser ingest → MediaMTX → tier-gated playback, restream, VOD.

### 5.19 Church / Organization Accounts (FR-ORG) [M3+]
- FR-ORG-1 Org entity with page, multiple creator-contributors, roles.
- FR-ORG-2 Consolidated giving & payouts; org-level analytics.

### 5.20 Giving & Tithing (FR-GIVE) [M3+]
- FR-GIVE-1 Recurring or one-off gifts to creator/org/campaign; goals & progress; giving statements.

### 5.21 Prayer Requests & Walls (FR-PRAY) [M3+]
- FR-PRAY-1 Submit prayer requests (public/private/tier-scoped); pray/respond; moderation.

### 5.22 Future domains
- FR-API (public API + webhooks), FR-I18N (localization/multi-currency), native mobile apps, community groups. Called out so the architecture reserves seams (see ADS §extensibility).

## 6. Non-functional requirements (platform-wide)

- **NFR-SEC** Server-side authorization on every protected action; least privilege; secrets encrypted + audit-logged (`sensitive-data-access.md`); no card data on our systems (PCI at processor).
- **NFR-PRIV** PII (email, payout, giving) permission-gated + audit-logged; data-subject deletion/export path (design for it now).
- **NFR-PERF** Feed/explore reads cursor-paginated; typical API p95 < 300 ms excluding third-party calls.
- **NFR-SCALE** Horizontal per-app scaling; stateless app pods; heavy media/live offloaded to their subsystems + CDN.
- **NFR-AVAIL** No single-app SPOF for core browsing/paying; graceful degradation when a subsystem (live, media) is down.
- **NFR-A11Y** WCAG 2.1 AA target for supporter/creator UIs.
- **NFR-STD** PHPStan max, custom sniffs, strict PHPUnit; DataType/Payload/DTO/Repository; every table via the migration runner; JS per `complete-js-guide.md`.
- **NFR-OBS** Structured logging, metrics, audit trail for money & moderation.
- **NFR-COST** Self-hosted on existing k3s/GitLab CI; per-transaction cost is the processor's; media/live egress CDN-offloaded.
- **NFR-EXT** Adding a future domain must not require reworking core auth, the ledger, the media pipeline, or the app topology (ADS §extensibility is the contract).
- **NFR-COMPLY** Payments/KYC via Stripe Connect; data protection (GDPR-style export/delete); ministry/nonprofit giving considerations noted for FR-GIVE.

## 7. MVP definition (from charter, restated)

MVP = **a supporter can pay a creator**: creator onboarding → public page with tiers → supporter subscribes (Stripe test) → gated content → creator sees earnings → payout (simulated). Domains M1+M2. Everything else is post-MVP but its **data & architecture seams exist from M0**.

## 8. Assumptions, constraints, open questions

- Constraints: solo dev + agents; uBix Core standards; existing k3s/GitLab infra; no raw card data. (Charter §2.1.)
- Open questions inherited from charter §12 (processor=Stripe, creator entity, commission, media types, marketing site, content policy) — resolved defaults adopted. Platform-specific:
  - PQ1: Are church/org accounts a *shared login* or *linked-user* model? (Default: org entity with linked creator-users.)
  - PQ2: Is tithing legally/operationally distinct from a tip in our jurisdiction (receipts/statements)? (Flag for FR-GIVE.)
  - PQ3: Content-policy authorship & doctrinal breadth (denominational neutrality vs stance). (Product/leadership task.)

## 9. Traceability

Each domain's FRs are realised in [`technical-spec.md`](technical-spec.md) (domain model + API) and deployed per [`architecture.md`](architecture.md). Surface SRSs cite these platform FR-IDs as their parent. Roadmap milestones M0–M3 gate delivery.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial platform SRS — full product breadth, faith-native domains, Patreon parity map. |
