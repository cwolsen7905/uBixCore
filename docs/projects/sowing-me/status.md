# Sowing.me — Working Status

## 2026-09-01 — M0 complete

### Work this session
- M0-06: `creators` + `creator_slug_history` migration (`20260901000000_…`) authored per creator-profile TDS §2 and applied to the unit-test DB via `migrate:up --target=test`. Reserved nullable `organization_id`/`payout_account_id` (no FKs) per ADR-007. No `users` change (registration TDS §2.1: roles fix is behavior-only, lands with M1).
- M0-07 closed by the ci-parity gate work (blocking + green since 2026-08-28).
- **M0 is done** — next: M1-01 (auth hardening) or M1-02/03 (creator onboarding + public page), which now have their schema.

Rolling session journal. Newest block first. Decisions recorded here the session they're made; roadmap rows flip in the same commit.

## 2026-09-01 — M0-03 / M0-04 closed: auth + registration docs synced to built code

### Work this session
- Verified `docs/surfaces/authentication/` and `docs/surfaces/registration/` (SRS + TDS) line-by-line against the code actually on `dev`, and bumped both surfaces to **v0.2**:
  - Controllers now depend on `UserService` / `EmailConfirmationTokenService` (MR !89 service-layer refactor); repos use the `query(Options)` reader pattern and `void` writers — the planned `PasswordResetToken*` stack is specified to match.
  - `AccountAuthenticationMiddleware` and `DuplicateProspectSqlRepository` no longer exist (M0-01 sweep) — the auth status re-check moves to `SessionAuthenticationMiddleware`; registration anti-abuse is a fresh build, not a repurposing.
  - Corrected the frontend picture: there is no SPA `signup` route today — the supporter form lives on the Latte marketing site (`templates/sowing-me-web-v1/signup.latte`) posting to `/register`.
- Flipped roadmap rows **M0-03** and **M0-04** to Done (same commit).

### Decisions
- Requirements were already right; only realisation details had drifted. SRS content unchanged except the registration §3 context correction — no FR renumbering.
- Doc-drift rule reaffirmed: a code refactor that changes how an FR is realised re-versions the TDS even when no FR changes.

### Next
- M0-05 (`creator-profile` docs verification) is the last Docs-state M0 row; M0-06 (creators migration) awaits the M0-02 Q2 sign-off; M0-07 needs one green `dev` pipeline to tick.
- M1-01 (auth hardening) and M1-02 (creator onboarding) are now fully specced and unblocked.

## 2026-08-27 — Full surface set authored (21 surfaces)

### Work this session
- Authored the complete surface set under `docs/surfaces/` — **20 new surfaces** + the existing live-streaming = 21, each with `README.md` + `srs.md` (SRS) + `technical-spec.md` (TDS), and an `architecture.md` (ADS) where warranted (`payments`, `media-storage`; `live-streaming` already had one). Written by 9 domain-grouped parallel agents, each reading the platform trio + live-streaming as the template.
- Surfaces: authentication, registration, creator-profile, subscription-tiers, content-posts, media-storage, payments, payouts, supporter-feed, explore, creator-dashboard, admin-console, trust-safety, notifications, comments-community, messaging, affiliates, organizations, giving-tithing, prayer-requests (+ live-streaming).
- Added the [surface index](../../surfaces/README.md) and the cross-cutting ERD [`docs/data-models/core-entities.md`](../../data-models/core-entities.md) (satisfies M0-02). Wired both into the project README + roadmap; marked the `SRS + tech spec` roadmap rows `Docs`.

### Consistency sweep (done)
- 66 surface docs, **0 broken links** in the new docs (16 broken links exist only in pre-ported neptune architecture/standards docs referencing un-ported neptune surfaces — `i18n`, `chat-room`, `auth-rewrite`, etc.; pre-existing, not from this work).
- No parallel money tables — all money is `transactions` ledger rows (ADR-004) in minor units. Gating everywhere routes through the single `EntitlementService` (ADR-008; referenced in 33 docs). Milestones tagged correctly per surface.
- Reused-not-reinvented: affiliates builds on the `AttributionLog` seed; admin-console/affiliates build on existing `SowingMeAdminApi` skeleton routes; payments repurposes `Transaction`/`BillingTransaction`.

### Findings to action during build
- **Bug:** `AuthController::register` hardcodes `roles: 'user'` instead of the selected role — documented as a required fix in the registration surface (SRS §8 / TDS §3).
- **Nuance:** `payouts` (M2) references `owner_type/owner_id` while ADR-007 introduces that at M3+ (creator-FK until then) — reconcile when payouts is built (noted in `core-entities.md` §2).
- Open questions still owned by the user: PQ1 (org shared-login vs linked-user; default linked-user), PQ2 (tithing legal/receipt distinction — jurisdiction-dependent), PQ3 (content-policy authorship/doctrinal breadth — product/leadership).

### Next
- Nothing is committed yet — review, then commit the documentation foundation.
- Build order per roadmap: M0 cleanup → M1 (creator can publish) starting with authentication + creator-profile + subscription-tiers + content-posts + media-storage.

## 2026-08-27 — Platform documentation trio (whole product)

### Work this session
- Established the **platform-altitude** engineering trio under `docs/projects/sowing-me/platform/`: `srs.md` (Platform SRS — full Patreon-class scope + faith-native domains, milestone-tagged, personas, NFRs), `technical-spec.md` (Platform TDS — layering, cross-cutting domain/ledger model, API conventions, external seams, per-surface template), `architecture.md` (Platform ADS — C4 context/container views, data & security architecture, subsystems, **extensibility contract**, ADR index), and a `README.md`.
- Reframed the whole product explicitly: **not "just Patreon's livestream," not "generic Patreon"** — a Patreon-class membership platform *rebuilt for Christian creators* with faith-native domains (church/org accounts, giving & tithing, prayer, devotional/Scripture content) as first-class, milestone-tagged capabilities.
- Updated `ubixcore/CLAUDE.md` → "Product & surface documentation": two altitudes (platform trio + surface trios), SRS/TDS/ADS ↔ `srs.md`/`technical-spec.md`/`architecture.md` mapping, sync rules. Wired the platform trio into the project README + brief.

### Decisions
- Kept the neptune-inherited `technical-spec.md` filename (referenced repo-wide) rather than renaming to `tds.md`; the SRS/TDS/ADS vocabulary maps onto `srs.md`/`technical-spec.md`/`architecture.md`.
- **Foundation-first commitments so post-MVP domains need no rework (ADS §9 extensibility contract, ADRs):** generic `transactions` ledger from M0 (types cover subscription/tip/gift/tithe/fee/payout/refund); `creators` vs `organizations` with owner polymorphism (ADR-007); `posts.type`/`visibility` from M1; central `EntitlementService` as the one gating authority (ADR-008); token-auth seam reserved behind session middleware; money in minor-units + currency.

### Next
- Slice M0/M1 surfaces (creator-profile, subscription-tiers, content-posts, media-storage) into per-surface SRS+TDS under `docs/surfaces/`, each citing the platform FR-IDs, before their first migrations.
- Resolve platform open questions: church-account model (shared vs linked-user), tithing legal/receipt distinction, content-policy authorship (SRS §8 PQ1–PQ3).

## 2026-08-27 — Live streaming: full surface spec

### Work this session
- Promoted live streaming from a single plan doc to a **full surface**: `docs/surfaces/live-streaming/` with `srs.md` (Patreon-informed FR/NFR + acceptance criteria), `technical-spec.md` (data model, API, MediaMTX config, clients, entitlement), `architecture.md` (SDD: topology, UDP/network model, sequences, capacity, decision + alternatives), and a surface `README.md`.
- Slimmed `live-streaming-plan.md` to an orientation/decision pointer into the surface (no duplicated detail).
- Documented the **surface-doc trio convention + sync rules** in `ubixcore/CLAUDE.md` (new "Surface documentation" section) — SRS=what/why, tech-spec=how-in-code+traceability, architecture=how-as-a-system; the three re-version together.
- Reconciled the charter: live streaming **moved from out-of-scope to post-MVP (M3-06)**; `broadcasting/*` stubs are now *repurposed*, not deleted. Repointed roadmap M3-06 + README to the surface.

### Decisions
- Primary stack: **MediaMTX + ffmpeg** (WHIP browser ingest + RTMP/SRT; WHEP + LL-HLS playback; auth delegated to `SowingMeApi` so tier gating stays in PHP; restream/record via config). **OvenMediaEngine** = named fallback for built-in ABR; **LiveKit** only if multi-guest rooms are ever required.
- API owns all policy (publish/read authorised server-side via the media-server hook); the media server owns only pixels — keeps it replaceable.
- Post-MVP: depends on `creator-profile`, `subscription-tiers`, `media-storage`, `notifications`. **Phase 0 (media-server spike)** is the sole exception with no app deps and can start now.

### Next
- Answer the Phase-0 gating questions (SRS §11 / architecture §4): cluster MetalLB/LoadBalancer vs `hostPort`+node-pin, which node has public UDP reachability, S3 vs MinIO for recordings.
- If greenlit: stand up MediaMTX on `ws-dev` and prove browser WHIP from an external network (the spike).

## 2026-08-27 — Project docs bootstrapped

### Work this session
- Ported `docs/architecture/` + `docs/standards/` from project-neptune (parity commit).
- Inventoried the actual Sowing.me build state (see `brief.md` §3): auth/register/confirm-email API, 2 tables, Latte marketing site, SvelteKit shell pages, admin API stubs. No payments, no content model, no creator entity.
- Wrote `README.md`, `brief.md`, `charter.md` (v0.1), `mvp-roadmap.md`, this file. Added the project row to `docs/projects/README.md`.

### Decisions
- Pitch deck is marketing, not build status; the roadmap is authoritative.
- Follow neptune's project convention (Tier 2 full tracking) from day one.
- Defaults adopted pending answers (charter §12): Stripe; separate `creators` entity; 10% commission; images/audio upload + video embed at MVP; keep Latte marketing site.

### Next
- M0-01 (delete neptune leftovers) and M0-02 (core-entities data model) are the first two rows — both unblock everything after.
- Set milestone dates after M0 to calibrate velocity.
