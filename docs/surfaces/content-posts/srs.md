# Content & Posts — Software Requirements Specification (SRS)

**Surface:** `content-posts` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M1 (base types) · faith content types M3+ (additive, no schema change)
**Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4); consumes `media-storage` (S6) for uploaded media
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md)
**Upstream:** platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.5 (FR-CONT) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) §4.1 (S5) · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface realises platform SRS domain **FR-CONT** (§5.5) at implementation depth. It does not restate the platform SRS/TDS/ADS — see those for the cross-cutting domain model, `EntitlementService`, and the extensibility contract (ADR-008, NFR-EXT).

## 1. Purpose

Let a creator publish content — text, images, audio, and external-embed video — to their page, gate it by subscription tier, group it into series/collections, and manage it through drafts, scheduled publish, and edit history. This is the platform's baseline content surface; Patreon-parity posts at M1, with the data model shaped so devotional/Scripture-native content types (platform FR-FAITH-5) arrive at M3+ as **new `PostTypeEnum` values**, never new tables.

## 2. Scope

**In scope:** `posts` CRUD (creator-owned), `PostTypeEnum` (`text`, `image`, `audio`, `video_embed` at M1), `PostVisibilityEnum` (`public`, `subscribers`, `tier`) with `min_tier_id` gating, `collections` (series grouping + ordering), drafts, scheduled publish, edit history, server-side entitlement-gated reads, cursor-paginated listing.

**Out of scope (this surface):** the faith content types themselves (`devotional`, `reading_plan`, Scripture-reference metadata — platform FR-CONT-4, M3+; this surface only guarantees the enum/schema seam), comments/reactions (`comments-community`, M3), notifications on new-post (`notifications`, M3), discovery/search ranking (`explore`, M2), the media upload pipeline and signed-URL minting itself (`media-storage`, this surface only consumes it via `post_media`).

## 3. Context — inherited from the platform

| Platform requirement | This surface's realisation |
|---|---|
| FR-CONT-1 Posts of type text/image/audio/video, visibility public/subscribers/specific-tier | §6.1–6.2 below |
| FR-CONT-2 Collections/series group posts; post ordering | §6.3 |
| FR-CONT-3 Gating resolved server-side on every read; never trust client visibility | §6.4, ADR-008 |
| FR-CONT-4 Faith content types: devotional, reading plan, Scripture reference metadata (M3+) | §6.5 — schema seam only, no M1 build |
| FR-CONT-5 Drafts, scheduled publish, edit history | §6.6 |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Post** | A single unit of content owned by one creator, with a `type`, `visibility`, lifecycle status, and zero-or-more attached media. |
| **Collection** | A creator-defined, ordered grouping of posts (e.g. a sermon series or devotional plan). |
| **Gating** | Server-side determination, via `EntitlementService`, of whether a given viewer may read a given post's body/media. |
| **Draft** | A post not yet published; visible only to its creator. |
| **Edit history** | An immutable record of prior versions of a post's editable fields, kept for creator review and moderation audit. |

## 5. Personas & primary user stories

- **Creator.** "As a creator, I write a post, attach a photo, mark it visible to my `$10` tier and above, save it as a draft, and schedule it to publish Sunday morning. Later I fix a typo and can see what changed."
- **Supporter.** "As a subscriber to a permitted tier, I read the full post and see its media. If I'm not subscribed to a high-enough tier, I see a teaser and a subscribe CTA — never the gated body."
- **Visitor.** "As a visitor, I see a creator's public posts on their page, and a paywall on anything gated."
- **Admin.** "As an admin, I can locate and hide a post that violates content policy (`admin-console`, out of scope here beyond the hook)."

## 6. Functional requirements

### 6.1 Post types (FR-CONT-6)
- **FR-CONT-6** `posts.type` is a `PostTypeEnum` with values `text`, `image`, `audio`, `video_embed` at M1. `video_embed` stores an external URL (YouTube/Vimeo) per charter Q4 — no video file upload at M1. A post may combine a text body with zero-or-more `post_media` rows (e.g. text + several images) regardless of its declared `type`; `type` classifies the post's primary content for feed/UI rendering, not an exclusivity constraint.

### 6.2 Visibility & tier gating (FR-CONT-7, realises platform FR-CONT-1's gating half)
- **FR-CONT-7** `posts.visibility` is a `PostVisibilityEnum` with exactly the values `public`, `subscribers`, `tier` (reused verbatim from the platform TDS §3 ERD — no parallel enum). `visibility=tier` requires `min_tier_id` (FK → `tiers.id`); tier ordering (platform FR-MEM-3) defines who satisfies it — a subscriber at `min_tier_id` or a higher-ordered tier is entitled.
- **FR-CONT-8** `visibility=public` posts are readable by anyone, including signed-out visitors. `visibility=subscribers` posts require an active subscription to **any** of the creator's paid tiers. `visibility=tier` requires an active subscription to `min_tier_id` or higher.

### 6.3 Collections / series (FR-CONT-9, realises platform FR-CONT-2)
- **FR-CONT-9** A creator can group posts into a `collections` row (title, description) and assign posts to it with an explicit `position` for ordering (e.g. "Part 1", "Part 2" of a sermon series).
- **FR-CONT-10** A post belongs to at most one collection at M1. A collection's own visibility is derived from its posts (no separate collection-level gate) — a collection page shows only the posts the viewer is entitled to read.
- **FR-CONT-11** Reordering posts within a collection updates `position` only; it never re-parents posts across creators (ownership is immutable after creation).

### 6.4 Server-side gating on every read (FR-CONT-12, realises platform FR-CONT-3)
- **FR-CONT-12** Every endpoint that returns a post's `body` or `post_media` calls `EntitlementService.resolve(user, post)` (platform TDS §6, ADR-008) before including gated content in the response. A non-entitled viewer's response omits `body` and `post_media` entirely (not merely hidden client-side) and instead carries a teaser (title, excerpt if the creator opted in, and the permitted tier to unlock it).
- **FR-CONT-13** List endpoints (creator page, collection page) apply the same per-post entitlement check to every row before serialisation — gating is never a single check on a whole page, since a page can mix public and gated posts from the same creator.

### 6.5 Faith content types — the extensibility seam (FR-CONT-14, realises platform FR-CONT-4)
- **FR-CONT-14** `PostTypeEnum` and `posts.visibility`/`min_tier_id` are designed so that `devotional`, `reading_plan`, and Scripture-reference metadata (platform FR-FAITH-5) land at M3+ as **additive `PostTypeEnum` values** plus an additive nullable metadata column (e.g. `scripture_reference` JSON) — never a parallel table, never a schema rework of `posts`. This surface's M1 build does not implement those types; it only guarantees the seam holds (see [`technical-spec.md`](technical-spec.md) §7).

### 6.6 Drafts, scheduling, edit history (FR-CONT-15, realises platform FR-CONT-5)
- **FR-CONT-15** A post has a lifecycle `status`: `draft` → `scheduled` → `published`, plus `archived` (creator-initiated unpublish). Only the owning creator can read a `draft`/`scheduled` post; entitlement gating applies only once `published`.
- **FR-CONT-16** A `scheduled` post carries a `publish_at` timestamp; a job flips it to `published` at that time (platform TDS §9 — async work, not inline in a request).
- **FR-CONT-17** Every edit to a published post's `body`, `type`, `visibility`, or `min_tier_id` writes an immutable edit-history row capturing the prior values, the editor, and the timestamp, before applying the change.

### 6.7 Listing & pagination (FR-CONT-18)
- **FR-CONT-18** Creator page and collection post listings are **cursor-paginated** (platform TDS §4, [`../../standards/pagination.md`](../../standards/pagination.md) §4 — unbounded, append-only feeds). The creator's own draft/management list (`creator-dashboard`) may use offset pagination per the standard's admin-table rule; the supporter-facing feed always uses cursor.

## 7. Non-functional requirements

- **NFR-CONT-1 Security.** Gating is enforced exclusively server-side (FR-CONT-12); the SPA renders paywalls but never decides access (platform TDS §10).
- **NFR-CONT-2 Performance.** Post listing p95 < 300 ms excluding media-storage signed-URL minting (platform NFR-PERF).
- **NFR-CONT-3 Extensibility.** Adding `devotional`/`reading_plan` at M3+ must not require a `posts` schema migration beyond additive columns/enum values (platform NFR-EXT).
- **NFR-CONT-4 Standards.** DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via `bin/ubix migrate:*`.
- **NFR-CONT-5 Auditability.** Edit history (FR-CONT-17) is retained indefinitely at M1 (no purge job) — it is a small, per-post table, not an event-table-scale audit log.

## 8. External interfaces (summary — detail in technical-spec)

- **Creator studio / library** (SvelteKit, `SowingMeJs`): compose, attach media, set visibility/tier, assign to collection, schedule, view edit history, existing `creator/library` shell becomes real (roadmap M1-05).
- **Public creator page & post view** (SvelteKit): renders gated/teaser posts per the entitlement response.
- **`SowingMeApi`**: post CRUD, collection CRUD, publish/schedule, entitlement-gated reads.
- **`media-storage`**: `post_media` rows link to media assets minted by that surface; this surface never talks to S3 directly.

## 9. Constraints & assumptions

- Reuses entities that must exist first: `creators`, `tiers`, `subscriptions` (or at minimum the tier model — subscriptions can be simulated at M1 per roadmap M1-05 "viewable by creator only, no payments" until M2 wires Stripe).
- `EntitlementService` is a platform seam (ADR-008); this surface is a consumer, not the owner.
- No content search at M1 (platform FR-DISC-2, M2+).

## 10. Acceptance criteria (surface DoD)

1. A creator creates a `text` post, attaches an image via `media-storage`, sets `visibility=tier`, and it does not appear (body/media) to a non-entitled viewer's read.
2. A creator schedules a post for a future time; it does not appear publicly until the scheduled job publishes it.
3. A creator edits a published post; the prior version is retrievable from edit history.
4. A creator groups three posts into a collection with explicit ordering; the collection page renders them in order, gated per-post.
5. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Does a post support multiple collections at M1, or exactly one? | Exactly one (FR-CONT-10) | schema |
| Q2 | Is a teaser/excerpt author-supplied or auto-truncated? | Auto-truncated from `body`, author override optional field | FR-CONT-12 |
| Q3 | Does `video_embed` validate the URL host allowlist (YouTube/Vimeo only)? | Yes, allowlist at Payload validation | FR-CONT-6 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability". Platform FR-CONT-1..5 are the parent requirements this surface's FR-CONT-6..18 realise at implementation depth.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
