# Trust & Safety — Software Requirements Specification (SRS)

**Surface:** `trust-safety` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 (base) / M3 (VOD flag propagation, doctrinal-breadth policy) · **Parent:** platform FR-TRUST ([`srs.md`](../../projects/sowing-me/platform/srs.md) §5.13) + FR-FAITH-1 (§4)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md) · platform [`srs.md`](../../projects/sowing-me/platform/srs.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface expands platform FR-TRUST-1..3 and FR-FAITH-1 into buildable requirements. It does not restate the platform SRS/TDS/ADS — see those for the domain model, layering, and system design this surface inherits.

## 1. Purpose

Give Sowing.me a trust & safety posture that is **values-aligned by design**, not a copy of a secular creator platform's moderation stack with the labels changed. Concretely: a published, faith-grounded content policy and ToS/privacy; a way for anyone to report content or a creator; a moderation queue admins act on; and an audit trail of every action taken. This is the mechanism that makes "faith-native trust & safety" true in the product, not just in the pitch — the platform SRS is explicit that this is a first-class differentiator from generic Patreon, not a bolt-on (platform SRS §1, §4).

## 2. Scope

**In scope:** the published content policy and ToS/privacy (mechanism: publish, version, surface to users — not the policy's doctrinal text, see §11 PQ3); the report-content/creator flow; the moderation queue and moderation actions (hide, remove, suspend); the `reports`, `moderation_actions`, and `audit_logs` tables; age/sensitivity flags on content and their propagation to VOD; content-policy enforcement points across posts, live streams, comments, and profiles.

**Out of scope (this surface):** the admin-console screens that host the queue entry point and execute the underlying suspend mechanism — those are [`admin-console`](../admin-console/README.md) (FR-ADMIN-22, FR-ADMIN-60/61); this surface defines the moderation *rules and actions*, admin-console provides the *operator UI and account-status plumbing* they call. Automated/ML content scanning (human-reviewed queue only at M2/M3). The actual doctrinal text of the content policy (product/leadership task, PQ3).

## 3. Why this isn't generic Patreon trust & safety

Patreon's trust & safety is denomination-agnostic and optimized for the broadest possible creator base. Sowing.me's is not: the content policy is grounded in the platform's explicit faith identity (platform SRS §1: "the faith-native domains are first-class, not bolt-ons"), which means moderation decisions can and do reference faith-specific standards (e.g. what "sensitive" content means in a devotional/Scripture context) rather than a generic community-standards checklist. This is the values-aligned posture that is a stated reason the platform exists (platform SRS §4). Engineering's job is to make that posture **enforceable and auditable**, not to author it.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Report** | A user-submitted flag against a piece of content or a creator, stored in `reports`. |
| **Moderation action** | A decision taken against reported (or proactively found) content/creator — hide, remove, or suspend — stored in `moderation_actions`, distinct from the account-status mechanism `admin-console` executes. |
| **Audit log** | Immutable record of who took what moderation action, when, and why — `audit_logs`, per [`sensitive-data-access.md`](../../standards/sensitive-data-access.md) conventions for auditable actions. |
| **Content policy** | The published, versioned statement of what is and isn't permitted on the platform, grounded in its faith posture (FR-FAITH-1). |
| **Age/sensitivity flag** | A tag on a post, stream, or resulting VOD indicating it requires an interstitial or age gate before viewing. |

## 5. Personas

| Persona | Needs |
|---|---|
| **Visitor / Supporter / Creator** | See the published content policy and ToS/privacy; report content or a creator they believe violates policy; see age/sensitivity interstitials where flagged. |
| **Platform admin / moderator** | Work a moderation queue, take an action (hide/remove/suspend) against reported content or a creator, have every action recorded. |
| **Product / leadership** (non-engineering) | Author and evolve the content policy's actual text and doctrinal stance (PQ3) — this surface's mechanism serves whatever they decide. |

## 6. Functional requirements

### 6.1 Published content policy & ToS/privacy (FR-TRUST-1x / FR-FAITH-1)
- **FR-TRUST-10** A published, versioned content policy is reachable from every public surface (footer link minimum); its current version is recorded and timestamped.
- **FR-TRUST-11** Published ToS and privacy policy, same publish/version mechanism as FR-TRUST-10.
- **FR-TRUST-12** The content policy is explicitly **faith-grounded** (FR-FAITH-1) — its authorship and doctrinal breadth (denominational-neutral vs. a specific stance) is a product/leadership decision (§11 PQ3); this surface only guarantees the policy, whatever its text, is published, versioned, and linkable from every enforcement point (report reason list, moderation action reason, interstitials).
- **FR-TRUST-13** A policy version change is itself audit-logged (who published, when) even though authorship is outside engineering's scope.

### 6.2 Report flow (FR-TRUST-2x)
- **FR-TRUST-20** Any authenticated user can report a post, a live stream (mirrors live-streaming FR-91), a comment, or a creator/organization profile, selecting a reason from a policy-linked reason list.
- **FR-TRUST-21** A report writes a `reports` row: reporter, entity type + id, reason, status (`open`/`actioned`/`dismissed`), timestamp.
- **FR-TRUST-22** Duplicate reports against the same entity by the same reporter within a short window are deduped (no queue spam), without silently dropping distinct reasons.
- **FR-TRUST-23** A reporter is not shown the outcome of their report beyond an acknowledgement (protects moderation decisions from being gamed); admins see full detail in the queue.

### 6.3 Moderation queue & actions (FR-TRUST-3x)
- **FR-TRUST-30** Open `reports` populate a moderation queue (surfaced via admin-console's entry point, FR-ADMIN-60); this surface owns the queue's underlying data and the actions available from it.
- **FR-TRUST-31** Available moderation actions: **hide** (content invisible to non-owners, reversible), **remove** (content deleted/unpublished, reversible only by republish), **suspend** (creator/organization account status change — invokes the same mechanism as admin-console FR-ADMIN-22, per FR-ADMIN-61).
- **FR-TRUST-32** Every moderation action writes a `moderation_actions` row: actor, target entity type + id, action taken, linked `report_id` (nullable — proactive actions have none), reason, timestamp.
- **FR-TRUST-33** Every moderation action additionally writes an `audit_logs` row (platform ADS §5: "money movement and moderation actions are audit-logged").
- **FR-TRUST-34** A report's status transitions to `actioned` or `dismissed` when a moderator resolves it; a dismissal still requires a recorded reason.
- **FR-TRUST-35** Hide/remove enforcement is server-side at every read path the content would otherwise appear on (feed, creator page, explore, direct link) — never a client-side filter, consistent with platform FR-CONT-3's gating principle.

### 6.4 Age/sensitivity flags (FR-TRUST-4x)
- **FR-TRUST-40** A post or live stream can carry an age/sensitivity flag, settable by the creator at creation/edit and overridable by a moderation action.
- **FR-TRUST-41** A flagged post/stream renders an interstitial (confirm-to-view) before content is shown, regardless of tier entitlement — the flag is a content-policy gate layered on top of, not a replacement for, `EntitlementService` tier gating.
- **FR-TRUST-42** When a flagged live stream ends and produces a VOD (live-streaming FR-61), the flag **carries through** to the resulting post — mirrors live-streaming FR-93 exactly; this surface defines the flag semantics live-streaming's VOD pipeline consumes.
- **FR-TRUST-43** A force-stopped or policy-removed live stream's resulting VOD is hidden/unpublished (mirrors live-streaming FR-92) via the same hide/remove action as FR-TRUST-31, applied to the VOD post.

### 6.5 Content-policy enforcement points (FR-TRUST-5x)
- **FR-TRUST-50** Enforcement points at M2: posts, creator/organization profiles, comments (when FR-COMM ships). At M3: live streams (delegates stream-specific mechanics to live-streaming, reuses this surface's report/action/audit tables and flag semantics).
- **FR-TRUST-51** Every enforcement point uses the **same** `reports`/`moderation_actions` tables and the same action set (hide/remove/suspend) — no per-surface parallel moderation schema (mirrors the "reuse names exactly" rule this surface exists to establish for others).

## 7. Non-functional requirements

- **NFR-TRUST-1 Auditability.** Every moderation action and every content-policy version publish writes an immutable, queryable audit record — never a Monolog-only trail, per [`sensitive-data-access.md`](../../standards/sensitive-data-access.md) conventions (this surface's `audit_logs` table is the moderation-specific analogue of that standard's `Pii_Access_Audits`).
- **NFR-TRUST-2 Server-side enforcement.** Hide/remove/age-gate decisions are enforced at every read path server-side; never trust a client to hide what a moderation action removed (platform FR-CONT-3 principle extended to moderation).
- **NFR-TRUST-3 Non-repudiation.** `moderation_actions`/`audit_logs` rows are append-only; a reversal (e.g. un-hiding) is a new row, not an edit or delete of the original.
- **NFR-TRUST-4 Pagination.** The moderation queue and report list are offset-paginated per [`pagination.md`](../../standards/pagination.md) (bounded, admin-scale data — same rule as admin-console).
- **NFR-TRUST-5 Standards.** PHP follows DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via `bin/ubix migrate:*`; JS follows `complete-js-guide.md`.
- **NFR-TRUST-6 Privacy.** Reporter identity is visible only to admins (FR-TRUST-23), consistent with platform NFR-PRIV.

## 8. External interfaces (summary — detail in technical-spec)

- **Public surfaces** (`SowingMeJs`, `SowingMeWeb`): content-policy/ToS/privacy pages, report action on posts/streams/comments/profiles, age/sensitivity interstitial component.
- **`SowingMeApi`**: report submission, policy version fetch, interstitial-gating check (alongside `EntitlementService`).
- **`SowingMeAdminApi`**: moderation queue read (shared with admin-console's entry point), moderation action endpoints, content-policy publish endpoint.

## 9. Constraints & assumptions

- Reuses existing entities: `posts`, `creators`, `organizations`, `users`; depends on `content-posts`/`profile` surfaces existing for enforcement points to attach to, and on `live-streaming` (M3) for FR-TRUST-42/43's VOD propagation.
- `reports`/`moderation_actions`/`audit_logs` are new tables owned by this surface (platform TDS §3 already reserves their names — see [`technical-spec.md`](technical-spec.md) §2).
- The moderation-queue **UI** lives in `SowingMeAdminJs` (admin-console surface); this surface supplies the data and action endpoints it calls.

## 10. Acceptance criteria (surface DoD)

1. The published content policy and ToS/privacy are reachable from a public footer link and record a version + timestamp.
2. A supporter reports a post; the report appears in the moderation queue with reason and reporter (admin-visible only).
3. An admin hides the reported post via a moderation action; the post disappears from feed/creator page/explore server-side; the action and its audit-log row are recorded.
4. An admin suspends the creator (invoking the admin-console suspend mechanism); the suspension and its audit trail are recorded once, not duplicated across two audit tables.
5. A creator flags a post as sensitive; a viewer sees an interstitial before content renders, independent of their tier entitlement.
6. (M3) A flagged live stream ends; its VOD carries the same flag; a force-stopped stream's VOD is hidden.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| PQ3 | Content-policy **authorship and doctrinal breadth** — denominational-neutral vs. a specific doctrinal stance. **Product/leadership decision, not engineering.** | Mechanism ships regardless; policy text is a placeholder until resolved | FR-TRUST-10/12 (publish step), production launch of the report reason list |
| Q1 | Are moderation actions ever automatable (e.g. auto-hide above N reports) at M2, or human-review-only? | Human-review-only at M2/M3; automation is a future revision | FR-TRUST-30 |
| Q2 | Does "remove" hard-delete or soft-delete content? | Soft-delete (reversible only by an explicit republish action, not undo) | FR-TRUST-31, NFR-TRUST-3 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to milestones **M2** (base: policy publish, report flow, queue, hide/remove/suspend, audit) and **M3** (VOD flag propagation, live-stream enforcement point). Parent requirements: platform FR-TRUST-1 (§6.1), FR-TRUST-2 (§6.2–6.3), FR-TRUST-3 (§6.4), FR-FAITH-1 (§6.1, §3).

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
