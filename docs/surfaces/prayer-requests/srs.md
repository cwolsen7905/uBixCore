# Prayer Requests — Software Requirements Specification (SRS)

**Surface:** `prayer-requests` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3+ (post-MVP) · **Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4) — unbuilt; `trust-safety` ([`../trust-safety/`](../trust-safety)) — unbuilt; `notifications` (Platform FR-NOTIF) — unbuilt
**Companion docs:** [`technical-spec.md`](technical-spec.md) · Platform [`srs.md`](../../projects/sowing-me/platform/srs.md) §5.21 (FR-PRAY) / §4 (FR-FAITH-4) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (no independent ADS — see [`README.md`](README.md))

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface expands **Platform FR-PRAY** (Platform SRS §5.21) and **FR-FAITH-4** (§4). It does not restate the entitlement/gating design — see Platform TDS §6 and ADR-008.

## 1. Purpose

Let a supporter submit a **prayer request** to a creator or organization's community, with a visibility they control, and let the community **pray for it or respond** to it. This is the platform's third faith-native differentiator (Platform SRS §2: "— → Prayer requests & walls") — a community/pastoral-care capability with no Patreon equivalent.

## 2. Scope

**In scope:** submitting a prayer request with visibility (public / private / tier-scoped); pray/respond interactions and prayer counts; privacy controls; moderation hand-off; notifications on responses.

**Out of scope (this surface):** the moderation queue UI/workflow itself (owned by `trust-safety`; this surface only produces reports into it); the notification delivery mechanism itself (owned by `notifications`; this surface only emits events into it); general community comments/reactions on posts (Platform FR-COMM, a separate surface) — a prayer request is its own object, not a post comment thread, even though the interaction shape (respond, moderate) is similar.

## 3. Context — why this exists

| Generic-platform assumption | Our stance |
|---|---|
| Community interaction = comments/reactions on content | **Extend.** A prayer request is a distinct object with its own visibility model and interaction verbs ("pray" and "respond"), not a comment thread bolted onto a post (FR-PRAY-1). |
| Visibility is public or gated-by-content-owner only | **Extend.** A prayer request's visibility is chosen by its **submitter**, including a fully private option visible only to the request owner (a creator/org) and moderators — a stronger privacy bar than any post's (FR-40). |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Prayer request** | A `prayer_requests` row: a supporter's request, with a visibility and an owning creator/organization community it was submitted to. |
| **Prayer / response** | A `prayers` row: a community member either marking that they prayed (a lightweight count-only interaction) or writing a text response. |
| **Visibility** | `public` (anyone), `subscribers` (any paid tier), `tier` (a specific tier and above) — mirrors `posts.visibility`/`live_streams.visibility` exactly (Platform TDS §3) — or `private` (submitter + community owner/moderators only), unique to this surface. |
| **Prayer count** | The number of `prayers` rows of kind `prayed` against a request — a lightweight, low-friction interaction distinct from writing a response. |

## 5. Personas & primary user stories

- **Supporter.** "As a supporter, I submit a prayer request to my creator's community, choose whether it's public or just visible to fellow subscribers, and see how many people prayed for it."
- **Supporter (responder).** "As a fellow supporter, I see prayer requests I'm entitled to view, tap 'I prayed', or leave an encouraging response."
- **Creator / org admin.** "As a creator, I see prayer requests submitted to my community, can respond personally, and moderate anything inappropriate."
- **Platform admin / moderator.** "As a moderator, I review reported prayer requests/responses through the same queue I use for every other content type."

## 6. Functional requirements

### 6.1 Submitting a request (realises FR-PRAY-1)
- **FR-10** A supporter can submit a prayer request to a creator's or organization's community: body text + a chosen visibility.
- **FR-11** Visibility options are `public`, `subscribers`, `tier` (mirroring `posts.visibility`/`min_tier_id`, Platform TDS §3), or **`private`** (visible only to the submitter and the community owner/moderators — never to other supporters).
- **FR-12** A submitter can edit or delete their own request at any time; deletion removes it from all views (soft-delete, consistent with posts-like tables).
- **FR-13** A request has a status: `open` or `answered` (submitter or owner may mark it answered) or `archived`.

### 6.2 Pray & respond interactions (realises FR-PRAY-1)
- **FR-20** An entitled viewer can mark that they **prayed** for a request — a lightweight, one-tap interaction, one per user per request, contributing to the prayer count (FR-21).
- **FR-21** The **prayer count** (distinct "prayed" interactions) is visible on every request the viewer is entitled to see.
- **FR-22** An entitled viewer can leave a text **response** to a request, subject to the same content policy as any other community content (Platform FR-TRUST-1).
- **FR-23** A private request accepts prayers/responses only from the community owner (creator/org) and designated moderators, never from other supporters.

### 6.3 Privacy controls (realises FR-PRAY-1)
- **FR-30** The submitter chooses visibility at submission time and can tighten it later (e.g. `public` → `private`) but a tightening never retroactively reveals who could see it before the change; it only affects future access checks.
- **FR-31** A `private` request's identity (submitter) is never exposed beyond the community owner/moderators, even in aggregate admin views, except where already visible via existing account admin tooling.

### 6.4 Moderation (realises FR-PRAY-1, hands off to `trust-safety`)
- **FR-40** Any viewer can report a prayer request or a response; the report is submitted into the `trust-safety` surface's moderation queue using the same report shape as other content types (mirrors `live_stream_reports`, `docs/surfaces/live-streaming/technical-spec.md` §2.8).
- **FR-41** A community owner (creator/org) or platform moderator can hide/remove a request or response; removal is server-side enforced identically to any other content-policy action (Platform FR-TRUST-2).

### 6.5 Notifications (realises FR-PRAY-1, hands off to `notifications`)
- **FR-50** The submitter of a prayer request is notified (per their notification preferences) when someone responds to their request, via the `notifications` surface's `NotifierInterface` fan-out (Platform TDS §5, FR-NOTIF-1 already lists "prayer response" as a notification event).
- **FR-51** A "someone prayed" (count-only) interaction does **not** generate a notification by default (to avoid notification fatigue on a low-friction action); only text responses do. This default is revisitable by product.

## 7. Non-functional requirements

- **NFR-1 Server-side gating.** Visibility (including `private`) is resolved server-side on every read via the same entitlement mechanism as posts/streams; the client never decides access (Platform NFR-SEC, ADR-008).
- **NFR-2 Privacy.** `private` requests carry a stricter access rule than any existing content type — reviewed explicitly in the entitlement resolver, not assumed to fall out of the standard tier-visibility matrix (Platform NFR-PRIV).
- **NFR-3 Abuse resistance.** Prayer/response submission is rate-limited per user per request to prevent spam (an addition beyond the base entitlement check).
- **NFR-4 Accessibility.** Request/response UI meets WCAG 2.1 AA (Platform NFR-A11Y), including the pray action being screen-reader operable and distinguishable from a "like".
- **NFR-5 Standards.** DataType/Payload/DTO/Repository, PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **Prayer wall UI** (SvelteKit, `SowingMeJs`): on a creator/org page — submit form, list of entitled-visible requests, pray button, response composer.
- **`SowingMeApi`**: request CRUD, pray/respond endpoints, report submission (delegates to `trust-safety`).
- **`SowingMeAdminApi`** / `trust-safety` queue: moderation actions on reported requests/responses.
- **`NotifierInterface`**: response-received notification event (delegates to `notifications`).

## 9. Constraints & assumptions

- Reuses existing entities that must exist first: `creators`, `tiers`, `organizations` (for org-community requests), and the entitlement resolver pattern already proven by `posts`/`live_streams` (Platform TDS §6, `docs/surfaces/live-streaming/technical-spec.md` §8).
- Moderation and notifications are **hand-offs**, not owned here: this surface produces the report/notification events; `trust-safety` and `notifications` own the queue/delivery mechanics respectively. Until those surfaces exist, this surface's moderation/notification endpoints are specified but cannot be exercised end-to-end (see `README.md` Status).

## 10. Acceptance criteria (surface DoD)

1. A supporter submits a `private` prayer request; only the community owner can see it — verified server-side, not just hidden in the UI.
2. A supporter submits a `tier`-visibility request; only supporters at that tier or above see it, mirroring post-visibility behavior exactly.
3. Multiple entitled viewers mark "prayed"; the prayer count reflects distinct users, not clicks.
4. A response triggers a notification event to the submitter (exercised against a stub `NotifierInterface` until `notifications` ships).
5. A reported request/response reaches the moderation queue shape `trust-safety` expects (exercised against a stub queue until `trust-safety` ships).
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Should a submitter be able to see who prayed (not just the count)? | No — aggregate count only at launch, to keep the low-friction interaction light | FR-20/21 |
| Q2 | Can a request be submitted anonymously even to the community owner? | No — submitter is always known to the owner/moderators for pastoral-care and moderation purposes; only *other supporters* can be kept blind (via `private`) | FR-11/31 |
| Q3 | Retention/export of prayer content on account deletion? | Follows the platform-wide data-subject deletion path once built (Platform NFR-PRIV) | data lifecycle |

## 12. Traceability

Each FR maps to endpoints/tables in [`technical-spec.md`](technical-spec.md) §"Requirement traceability", and to Platform SRS **FR-PRAY-1** and **FR-FAITH-4**. Changes to any FR update the traceability table and re-version both docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
