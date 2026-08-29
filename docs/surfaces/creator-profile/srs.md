# Creator Profile — Software Requirements Specification (SRS)

**Surface:** `creator-profile` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M1 (build-ready) · **Prerequisites:** none (this surface is a prerequisite of `subscription-tiers`, `content-posts`, `live-streaming`)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md) · parent [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.3 (FR-PROF)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD) and the platform TDS §12 per-surface template. The SRS says **what** and **why**; the technical-spec says **how in code**. This surface has no separate architecture doc — it inherits the platform ADS (see `README.md`).

## 1. Purpose

Give every creator a distinct, addressable identity — the `creators` entity — with a public page at `/c/{slug}` that a visitor can land on to learn who the creator is, see their tiers and recent public content, and subscribe. This is the platform's front door: discovery (FR-DISC), tiers (FR-MEM), content (FR-CONT), and live streaming (FR-LIVE) all render *onto* this page. Without it nothing else on the roadmap has anywhere to attach.

## 2. Scope

**In scope:** the `creators` entity (1:1 with `users`, charter Q2); profile fields (bio, avatar, banner, category, faith topic/denomination, external links); slug assignment, uniqueness, and change-with-redirect history; the org-affiliation stub (`organization_id`, ADR-007); the payout-account stub (`payout_account_id`); the public page and its composition of tiers/posts/streams from sibling surfaces; creator self-service edit flows; the creator-onboarding profile step (FR-ONB-2).

**Out of scope (this surface):** tier definition/pricing (owned by `subscription-tiers`), post authoring/gating (owned by `content-posts`), media upload pipeline (owned by `media-storage` — avatar/banner are plain URLs here until that surface lands), payout onboarding/KYC (owned by `payouts`), live-stream scheduling/playback (owned by `live-streaming`), organization accounts themselves (owned by a future `organizations` surface, M3+), discovery/search ranking (owned by `explore`).

## 3. Context — parent requirements this surface realises

| Platform FR (SRS §5.3) | Statement | Realised by |
|---|---|---|
| FR-PROF-1 | Public creator page at `/c/{slug}` with bio, avatar/banner, category, external links | §6.3 |
| FR-PROF-2 | Creator is a distinct entity 1:1 with a user; slug unique, editable with redirect history | §6.1, §6.2 |
| FR-PROF-3 | A page lists tiers, recent public posts, upcoming/live streams, and a subscribe CTA | §6.3, §6.6 |

Also partially realises **FR-ONB-2** (creator onboarding wizard's "profile" step) and reserves the schema for **FR-FAITH-2** (org affiliation) and **FR-FIN** (payout linkage) per the platform's extensibility contract (ADS §9) — this surface does not build orgs or payouts, only the seam.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Creator** | A `creators` row: the public-facing identity a user operates, distinct from the `users` account (charter Q2). |
| **Slug** | The unique, URL-safe identifier in `/c/{slug}`; human-editable, historied. |
| **Slug history** | A record of a creator's retired slugs, each 301-redirecting to the current one. |
| **Owner polymorphism** | ADR-007's mechanism for a creator optionally belonging to an organization; at M1 this is a nullable `organization_id` FK on `creators`, promoted to `owner_type/owner_id` when orgs (M3+) land. |
| **Payout account stub** | A nullable `payout_account_id` reserved on `creators` so the M2 `payouts` surface attaches without altering this table. |

## 5. Personas & primary user stories

- **Creator.** "As a creator, during onboarding I set up my page — bio, photo, category — pick my slug, and can change it later without breaking old links people already shared."
- **Visitor / Seeker.** "As a visitor who followed a link, I land on a creator's page, understand who they are and what they offer, see their tiers and some free content, and can subscribe."
- **Supporter.** "As a subscriber, I see the creator's page reflects what I already get access to, plus what upgrading would unlock."
- **Platform admin.** "As an admin, I can suspend a creator's page without deleting their account or content."

## 6. Functional requirements

### 6.1 Creator entity (FR-10x)
- **FR-101** A `creators` row is created 1:1 with a `users` row (unique `user_id`); a user without a `creators` row is not a creator (mirrors FR-PROF-2, FR-IAM-2's `creator` role).
- **FR-102** Profile fields: `display_name`, `bio` (long text), `avatar_url`, `banner_url`, `category`, `faith_topic`/`denomination`, `external_links` (ordered list of label+URL pairs, capped count).
- **FR-103** A creator has a lifecycle `status` (`draft` → `active`, plus `suspended`) independent of the underlying user's account status (FR-IAM-4); a `suspended` creator's public page and API responses degrade to a "unavailable" state without deleting data.
- **FR-104** `organization_id` exists on `creators`, nullable, unset at M1 (no UI to set it — org accounts are a future surface); reserved per ADR-007.
- **FR-105** `payout_account_id` exists on `creators`, nullable, unset until the `payouts` surface (M2) issues one; reserved so payouts attach without a schema change.

### 6.2 Slug & redirect history (FR-20x)
- **FR-201** Slug is unique, URL-safe (`[a-z0-9-]`, length-bounded), assigned at creator creation (from a suggested slug, editable before first publish).
- **FR-202** A creator may change their slug after publish; the previous slug is retired into `creator_slug_history` and continues to resolve via a server-side 301 redirect to the current slug — no dead links.
- **FR-203** A retired slug cannot be reused by a different creator (uniqueness spans both the live table and the history table).
- **FR-204** Slug changes are rate-limited (e.g. once per rolling 24h) to prevent redirect-chain abuse and discovery-index churn.

### 6.3 Public creator page (FR-30x)
- **FR-301** `GET /c/{slug}` (frontend route) resolves to the public profile: bio, avatar/banner, category, faith topic/denomination, external links.
- **FR-302** The page lists the creator's tiers (name, price, short benefits) sourced from `subscription-tiers`, each with a subscribe CTA.
- **FR-303** The page lists the creator's recent public posts, sourced from `content-posts`; until that surface ships, this section is omitted, not an error (graceful degradation, mirrors platform NFR-AVAIL).
- **FR-304** The page shows upcoming/live streams, sourced from `live-streaming`; until that surface ships (M3), this section is omitted.
- **FR-305** A `suspended` or `draft` creator's page returns a not-found/unavailable response rather than partial content.
- **FR-306** Page composition is a single server-side read (no client-side stitching of gated data) — visibility of each section still resolves through its owning surface's rules (e.g. tier gating stays `EntitlementService`'s job when `content-posts` lands).

### 6.4 Creator edit flows (FR-40x)
- **FR-401** An authenticated creator can view and edit their own profile fields (§6.1) via a self-service form; ownership enforced server-side (a creator can only edit their own row).
- **FR-402** The creator-onboarding wizard's profile step (FR-ONB-2) creates the `creators` row and captures the initial profile fields and slug in one flow; the wizard's later steps (first tier, payout account) hand off to `subscription-tiers` and `payouts` respectively.
- **FR-403** Edits are validated the same way regardless of entry point (onboarding vs. later self-service edit) — one Payload, one Service method.
- **FR-404** A creator can preview their own `draft` page before publishing (`status: draft → active`).

### 6.5 Admin (FR-50x)
- **FR-501** An admin can suspend/reinstate a creator's page (`status`), independent of user account actions, surfaced through `admin-console` (not built at M1 — reserve the service method; expose via the existing session-authenticated admin role in the interim if `admin-console` isn't ready).

### 6.6 Cross-surface composition (informative — no new FRs)
The public page is the composition point for four surfaces: this one (identity/bio), `subscription-tiers` (tiers list), `content-posts` (recent posts, M1 but a separate slice), and `live-streaming` (upcoming/live, M3). Each contributes its section via its own service; `creator-profile` never re-implements another surface's gating or data shape — it composes read-only summaries.

## 7. Non-functional requirements

- **NFR-1 Performance.** Public page read is a single aggregated request; p95 < 300 ms excluding third-party calls (platform NFR-PERF); sibling-surface calls that aren't ready yet must not slow or error the response — they're conditionally composed, not synchronously required.
- **NFR-2 Availability.** A missing/degraded sibling surface (posts, streams) degrades that section only, never the whole page (platform NFR-AVAIL).
- **NFR-3 Uniqueness & integrity.** Slug uniqueness enforced at the database level across `creators` and `creator_slug_history` combined (not just application-level checks).
- **NFR-4 Security.** Profile edits are ownership-checked server-side; `organization_id`/`payout_account_id` are not client-writable (system-assigned only, by the surfaces that own them).
- **NFR-5 A11Y.** Public page meets WCAG 2.1 AA (platform NFR-A11Y): alt text for avatar/banner, semantic heading structure, keyboard-navigable external links.
- **NFR-6 Standards.** DataType/Payload/DTO/Repository, PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner (platform NFR-STD).
- **NFR-7 Extensibility.** Adding `organizations` (M3+) or `payouts` (M2) must not require a `creators` schema rework beyond populating the already-reserved columns (platform NFR-EXT, ADR-007).

## 8. External interfaces (summary — detail in technical-spec)

- **Public page** (SvelteKit, `/c/[slug]`): reads the composed profile; renders subscribe CTAs that hand off to `subscription-tiers`/`payments`.
- **Creator profile editor** (SvelteKit, under `/creator/dashboard`): self-service edit form; slug-change control with a redirect-history notice.
- **`SowingMeApi`**: public profile read, authenticated profile write, slug management.
- **`SowingMeAdminApi`**: suspend/reinstate (stubbed until `admin-console`).

## 9. Constraints & assumptions

- Depends on `users` (exists) only; does not depend on any unbuilt surface to ship its own scope, though its page composition *reads from* them opportunistically once they exist.
- Avatar/banner are plain external URLs at M1 (no upload) — `media-storage` upgrades this to signed, hosted media without changing the column names (a URL is a URL; the value's origin changes).
- `organization_id` and `payout_account_id` are schema-only at M1; no creator-facing UI to set either.

## 10. Acceptance criteria (surface DoD)

1. A new creator completes onboarding, gets a unique slug, and their public page renders at `/c/{slug}`.
2. Changing a slug preserves the old one as a working 301 redirect; the old slug cannot be claimed by another creator.
3. A `suspended` creator's page is unavailable to visitors without deleting any data.
4. The public page renders correctly with zero, one, or all of tiers/posts/streams present (graceful composition).
5. A creator cannot edit another creator's profile (ownership enforced server-side, tested).
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Is `display_name` distinct from the `users` display name, or always mirrored? | Distinct — creator identity may differ from account name | §6.1 |
| Q2 | Slug suggestion algorithm (from display name vs. manual only)? | Slugify display name, editable before first publish | §6.2 |
| Q3 | Category/faith-topic — free text or a fixed enum now (ahead of `explore`'s M2 taxonomy)? | Fixed enum now (`CreatorCategoryEnum`), small starter set, additive later | §6.1, `explore` |
| Q4 | Does `draft` status block the slug from resolving at all, or show a "coming soon" page? | Not-found (404) until `active` | §6.3 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability". Parent platform FRs are FR-PROF-1..3 (SRS §3 above); this surface also seeds the schema consumed by FR-FAITH-2 (org) and FR-FIN (payout) per the platform ADS extensibility contract.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
