# Sowing.me — Project Charter

**Status:** Active · v0.1 (draft for review) · 2026-08-27 · Sponsor/Lead: Christopher W. Olsen

## 1. Executive summary

Build Sowing.me from its current foundation (auth + registration + marketing site + UI shell) to a launchable MVP where a supporter can pay a creator, then iterate toward the full feature set in the [pitch deck](../../pitch-deck.md). Work is sliced into phases with a milestone gate each; the [roadmap](mvp-roadmap.md) is the work matrix.

## 2. Background

See [`brief.md`](brief.md) §3 for the verified inventory. Short version: the platform framework (uBix Core, forked from project-neptune) is mature; the *product* is ~5% built. There is no data model beyond `users`, no payments, no content.

### 2.1 Constraints

- Solo developer + AI agents; no PMO. The delivery framework's roles collapse to one person — the artefacts (charter / SRS / tech spec / roadmap / status) are kept, the ceremonies are not.
- Must follow uBix Core standards (PHPStan max, custom sniffs, strict PHPUnit, migrations runner, DataType/Payload pattern).
- Infra: existing k3s/GitLab CI pipeline with `dev` / `staging` / `main` environments and per-app manifests.
- Handling money and PII → PCI scope must stay at the processor (hosted checkout / tokenised cards), never raw card data in our DB.

## 3. Problem statement

Faith-based creators have no purpose-built monetisation platform; the pitch deck makes the case. Engineering problem: turn a scaffold into a product with a coherent data model, a payments seam, and the four persona-facing surfaces (creator, supporter, affiliate, admin) — without accumulating the un-planned drift already visible in the repo (stub routes, neptune leftovers, marketing claims ahead of code).

## 4. Scope

### 4.1 In scope (MVP — M1/M2)

| # | Surface | Summary |
|---|---|---|
| S1 | `authentication` | Harden what exists: session/cookie policy, lockout (fields exist, logic TBD), password reset, role model (`user` / `creator` / `admin`). |
| S2 | `registration` | Supporter + creator sign-up flows, email confirmation (exists), creator onboarding wizard. |
| S3 | `creator-profile` | `creators` entity, public creator page (`/c/{slug}`), bio, avatar/banner, links. |
| S4 | `subscription-tiers` | Per-creator tiers (price, description, benefits), free tier. |
| S5 | `content-posts` | Posts (text, image, video-link, audio) with visibility = public / tier-gated. |
| S6 | `media-storage` | Upload pipeline → S3-compatible storage (AWS SDK already in deps), signed URLs, size/type limits. |
| S7 | `payments` | `PaymentProvider` seam; Stripe Checkout + Billing for subscriptions and one-off tips; webhooks; `transactions` ledger. |
| S8 | `supporter-feed` | Logged-in supporter home: posts from subscribed creators; subscription management. |
| S9 | `creator-dashboard` | Earnings, subscriber count/list, post management (existing dashboard/library pages become real). |
| S10 | `payouts` | Stripe Connect Express onboarding + scheduled payouts; platform commission. |
| S11 | `admin-console` | `SowingMeAdminApi` + `SowingMeAdminJs`: user/creator list + status changes, transaction view, basic moderation (suspend, hide post). |
| S12 | `explore` | Public discovery page (existing shell), category tags, search by name. |

### 4.2 Post-MVP (M3+)

`affiliates` (referral links, banners, revenue share — admin API stubs exist), `notifications` (email digests, in-app), `messaging` (creator ↔ supporter DMs), `live-streaming` (creator live video — WHIP browser ingest → MediaMTX, tier-gated playback, restream, VOD; **repurposes** the neptune `broadcasting/*` stubs; full spec in [`docs/surfaces/live-streaming/`](../../surfaces/live-streaming/README.md), roadmap M3-06), `organisations` (church accounts, tithing), `prayer-requests`, analytics dashboard, premium creator features, mobile apps, whitelabel.

### 4.3 Out of scope

Native mobile apps; anything requiring us to store card numbers. (Live streaming was previously out of scope; it moved to **post-MVP / M3** on 2026-08-27 — see `docs/surfaces/live-streaming/`. The `broadcasting/*` stubs are now **repurposed** by that surface rather than deleted.)

### 4.4 Effort legend

S ≤ 2 days · M ≤ 1 week · L ≤ 3 weeks · XL > 3 weeks (solo + agents).

## 5. Architecture posture

- **Apps:** `SowingMeApi` is the single product API for both supporter and creator flows (role-gated), `SowingMeAdminApi` for admin, `SowingMeWeb` stays a static-ish marketing site, `SowingMeJs` the product SPA, `SowingMeAdminJs` the admin SPA.
- **Domain model (proposed):** `users` (identity) ← 1:1 → `creators` (public profile, slug, payout account) → `tiers` → `subscriptions` (supporter × tier, status, provider ids) ; `posts` (creator, visibility, tier_id?) → `post_media` ; `transactions` (ledger: subscription charge / tip / payout / fee, provider ref) ; `tips`. Full ERD lands in `docs/surfaces/*/technical-spec.md` per surface and in `docs/data-models/` for cross-cutting shapes.
- **Auth:** keep PHP sessions for now (cookie, `SameSite=Lax`, secure), revisit token auth only if a mobile client appears.
- **Payments:** Stripe (Checkout + Billing + Connect Express). One interface `PaymentProviderInterface` in `php/Ubix/Service/Payment/`; webhook controller verifies signatures; the ledger is ours, Stripe is the source of truth for money movement.
- **Media:** S3-compatible bucket, direct-to-bucket presigned uploads, CDN in front later.
- **Access control:** route middleware checks role + ownership; tier gating resolved server-side on every post read; never trust client-side visibility.
- **PII:** payout details, emails, billing → permission-gated + audit-logged per the standard.
- **Pagination:** offset for admin tables, cursor for feeds/explore ([`pagination.md`](../../standards/pagination.md)).

## 6. Phasing & milestones

| Milestone | Definition of done |
|---|---|
| **M0 — Foundation cleanup** | Neptune leftovers removed; role model decided; `creators` migration; surface docs for S1–S3; CI green. |
| **M1 — Creator can publish** | Creator onboarding → public page with tiers → posts with gating (viewable by creator only, no payments). S1–S6, S9 (post mgmt half). |
| **M2 — Supporter can pay (MVP)** | Stripe subscriptions + tips live on staging with test keys; supporter feed; earnings dashboard; payouts via Connect; admin console basics. S7, S8, S9 (earnings), S10, S11, S12. → **launchable private beta**. |
| **M3 — Growth loops** | Affiliates, notifications, explore improvements, org accounts. |

Phase order = milestone order; each phase's rows are in the roadmap. No calendar dates yet — set after M0 velocity is known (record in `status.md`).

## 7. Success criteria

- M2: an end-to-end test on staging: new creator → tier → post → new supporter subscribes with a Stripe test card → sees gated post → creator sees the transaction → payout simulated.
- Standards gates green on every merge (`phpunit`, `phpstan`, `phpcs`, JS suite).
- Every table added via migration; every surface has an SRS + tech spec before its first migration.

## 8. Risks & mitigations

| Risk | Mitigation |
|---|---|
| Scope creep from the pitch deck's feature list | Roadmap is the only source of "in scope"; deck stays marketing. |
| Payments/PCI complexity | Hosted Stripe Checkout + Connect Express; no card data on our side. |
| Solo-dev bandwidth | Small slices (S/M), agents do surface docs + tests; milestone gates keep it shippable. |
| Content moderation / policy (faith-aligned) | Admin suspend/hide from M2; written content policy is a product task, tracked in roadmap. |
| Neptune-fork drift | Keep `docs/architecture` + `docs/standards` in parity; app code is ours. |

## 9. Governance

One owner. Decisions go in `status.md` the session they're made; roadmap rows flip in the same commit as the code. Charter re-versioned when scope changes.

## 10. Dependencies & assumptions

- Migration runner (`docs/projects/migrations/`) is usable on dev/staging — it is.
- Stripe account (test mode) available before S7 starts.
- S3-compatible storage endpoint available before S6.
- Domain, transactional email sender (Symfony Mailer already wired).

## 11. Cross-references

[`brief.md`](brief.md) · [`mvp-roadmap.md`](mvp-roadmap.md) · [`status.md`](status.md) · [`docs/pitch-deck.md`](../../pitch-deck.md) · [`docs/standards/web-development-delivery-framework.md`](../../standards/web-development-delivery-framework.md) (Charter / SRS / SDD structure this folder is authored against).

## 12. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Payment processor | Stripe | S7, S10 |
| Q2 | `creator` as role vs entity | Separate `creators` table, 1:1 user | M0 |
| Q3 | Commission rate / tiering | 10% flat for beta | S7 |
| Q4 | Media types at MVP (video hosting is expensive) | Images + audio upload; video by external embed (YouTube/Vimeo) | S5, S6 |
| Q5 | Keep `SowingMeWeb` (Latte) or fold marketing into `SowingMeJs`? | Keep Latte site | — |
| Q6 | Content policy text / moderation rules | Product task, write before beta | S11 |

## Document control

| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial charter. |
| 0.2 | 2026-08-27 | Live streaming moved from out-of-scope to post-MVP (M3-06); surface docs added. |
