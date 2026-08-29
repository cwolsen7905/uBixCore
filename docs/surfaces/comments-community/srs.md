# Comments & Community — Software Requirements Specification (SRS)

**Surface:** `comments-community` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3 (post-MVP) · **Prerequisites:** `content-posts` (M1, built), `EntitlementService` (M1, built), trust-safety moderation queue (`reports`/`moderation_actions`/`audit_logs`, M2/M3), [`notifications`](../notifications/README.md) (M3, spec complete)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md)
**Upstream:** platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.15 (FR-COMM-1, FR-COMM-2) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) §4.2 · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This SRS decomposes the platform-altitude **FR-COMM-1** (comments/reactions/moderation) into surface-level requirements, numbered `FR-COMM-1.x`; **FR-COMM-2** (live chat / community future) is addressed in §2/§7 by reference rather than decomposed here, since its live-chat half is owned by [`live-streaming`](../live-streaming/README.md) and its community-groups half is explicitly future.

## 1. Purpose

Let supporters comment on and react to a creator's posts — gated by the same tier entitlement that gates the post itself — and give creators moderation tools (delete/hide/ban) so their comment sections stay safe, escalating to the platform's shared trust-safety pipeline when needed.

## 2. Scope

**In scope:** comments and reactions on posts, gated by post visibility/tier; creator (and platform admin) moderation: delete/hide a comment, ban a user from commenting on the creator's content; report a comment to trust-safety; cursor-paginated comment lists.

**Out of scope (this surface):** **live chat** during a broadcast — that is [`live-streaming`](../live-streaming/README.md)'s FR-4x (its own tables `live_stream_chat_messages`/`live_stream_chat_bans`, its own WS room); this surface neither re-specifies nor depends on it. **Community spaces/groups** — platform SRS §2/§5.15 marks these **Future**; noted here as a named seam (§7) but not designed. Nested/threaded replies beyond one level (candidate for a later revision). Comments on anything other than a post (e.g. a live-stream VOD's comments are just that VOD's `posts` row per live-streaming FR-61 — this surface's post-comments apply to it unchanged, no special case needed).

## 3. Context — three communication surfaces, one entitlement rule

Comments (this doc), messaging ([`../messaging/srs.md`](../messaging/srs.md)), and live chat ([`../live-streaming/srs.md`](../live-streaming/srs.md) FR-4x) are three distinct Sowing.me surfaces that all resolve access through the same `EntitlementService` and escalate abuse through the same `reports`/`moderation_actions`/`audit_logs` tables (platform TDS §3). This SRS reuses that pattern rather than inventing a comments-specific gating or moderation model.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Comment** | A `comments` row attached to a post, authored by an entitled user. |
| **Reaction** | A `reactions` row: a lightweight emoji/like on a post or a comment. |
| **Entitlement** | Server-side determination that the commenting/reacting user may view the underlying post (same rule as viewing it — platform TDS §6). |
| **Moderation action** | A creator (or admin) hiding/deleting a comment, or banning a user from commenting on that creator's content; recorded in `moderation_actions` (platform TDS §3). |
| **Report** | A user escalating a comment into the shared trust-safety queue. |

## 5. Personas & primary user stories

- **Supporter.** "As a subscriber, I want to comment on and react to a creator's gated post, the same way I would on any social platform, without a non-subscriber being able to see or comment on it."
- **Creator.** "As a creator, I want to moderate my own comment sections — delete a comment, hide it, or ban a repeat offender — without needing an admin every time."
- **Platform admin/moderator.** "As an admin, I want an escalation path when a creator's own moderation isn't enough, using the same queue I already use for content and live-stream reports."

## 6. Functional requirements

### 6.1 Comments & reactions (parent: platform FR-COMM-1)

- **FR-COMM-1.1** A user may comment on a post only if they are entitled to view that post — the identical rule `EntitlementService.resolve(user, resource)` already applies to the post's content (platform TDS §6), never a separate or looser check.
- **FR-COMM-1.2** A user may react (emoji/like) to a post or to a comment, subject to the same entitlement check as commenting.
- **FR-COMM-1.3** Comments support a single level of reply (a comment may reference one parent comment); deeper nesting is out of scope (§2).
- **FR-COMM-1.4** Comment lists (per post) are cursor-paginated, ordered newest-first or by a creator-configurable order (`pagination.md` §4 — unbounded, append-only per post).
- **FR-COMM-1.5** A comment's author, creation time, and (if applicable) parent are always shown; a creator's own comments on their post are visually distinguishable (product-level, not a data requirement beyond a boolean the API can compute from `post.creator_id == comment.user_id`'s underlying creator/user relationship).

### 6.2 Creator moderation (parent: platform FR-COMM-1)

- **FR-COMM-1.6** The post's owning creator (and platform admins) may **delete** or **hide** any comment on that post; a hidden comment is retained for audit but not rendered to other users.
- **FR-COMM-1.7** The post's owning creator (and platform admins) may **ban** a user from commenting on that creator's content; a banned user's existing comments are unaffected unless separately deleted/hidden.
- **FR-COMM-1.8** Any user may **report** a comment; a report creates a `reports` row visible in the shared admin moderation queue (platform TDS §3), and any resulting action is recorded in `moderation_actions` and audit-logged (`sensitive-data-access.md`) — mirrors live-streaming FR-91/FR-92 and messaging FR-MSG-3.3 exactly; no parallel moderation model is introduced.
- **FR-COMM-1.9** Moderation actions (delete/hide/ban) are themselves audit-logged, distinguishing creator-initiated from admin-initiated actions (platform FR-TRUST-3).

### 6.3 Live chat & community — reference, not duplication (parent: platform FR-COMM-2)

- **FR-COMM-2 (reference)** Live chat during a broadcast is specified entirely by [`live-streaming` SRS §6.4](../live-streaming/srs.md#64-live-chat-reactions--moderation-fr-4x) (FR-40..FR-43); this surface neither re-specifies it nor shares a table with it — the two are gated identically (via `EntitlementService`) but are separate features on separate content types (a post vs. a broadcast).
- Community spaces/groups remain **FUTURE** per platform SRS §2/§5.15 — not specified in this document; the `EntitlementService` gating pattern established here is expected to extend to them when they are picked up (platform ADS §9 extensibility row).

## 7. Non-functional requirements

- **NFR-COMM-SEC** Server-side entitlement check on every comment/reaction read and write — never trust a client's belief that it may comment (mirrors platform FR-CONT-3 "gating resolved server-side on every read").
- **NFR-COMM-PERF** Cursor pagination bounds comment-list query cost regardless of a post's total comment volume (platform NFR-PERF).
- **NFR-COMM-PRIV** Comment content is user-generated content; moderation and report actions are audit-logged (`sensitive-data-access.md`); a hidden/deleted comment's content is retained only as long as the platform's moderation-audit retention policy requires, not indefinitely exposed.
- **NFR-COMM-EXT** A future community-groups feature must be addable without changing the `comments`/`reactions` schema — it is expected to introduce its own tables and reuse `EntitlementService`, per platform ADS §9.
- **NFR-STD** DataType/Payload/DTO/Repository, PHP-DI, Slim 4; PHPStan max; custom sniffs; strict PHPUnit; every table via `bin/ubix migrate:*`; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **`SowingMeApi`**: comment CRUD (cursor-paginated), reactions, report.
- **`SowingMeAdminApi`**: comment reports queue (shared with content/live-stream/message reports).
- **Product SPA (`SowingMeJs`)**: comment thread component under a post, reaction picker, creator moderation controls (delete/hide/ban) inline on the post/comment.
- **`notifications` surface**: called for new-comment alerts to a post's creator (and optionally to a parent comment's author for a reply).

## 9. Constraints & assumptions

- No dedicated roadmap row exists yet for `comments-community` specifically (`mvp-roadmap.md` groups M3 rows M3-01..M3-06 around affiliates/notifications/messaging/orgs/analytics/live-streaming); this surface is scoped from platform SRS §5.15 and should be added as its own roadmap row when picked up for implementation.
- Reuses the shared trust-safety tables (`reports`/`moderation_actions`/`audit_logs`) rather than introducing comment-specific moderation tables.
- Reaction types (which emoji/like set) are a product decision, not fixed by this SRS; the schema (technical-spec §2) treats reaction `kind` as an enum so the set can grow additively.

## 10. Acceptance criteria (surface DoD)

1. An entitled supporter can comment on and react to a tier-gated post; a non-entitled visitor cannot see the post's comments at all (403/hidden, server-side).
2. A creator can delete or hide a comment on their own post; the comment disappears from other users' view immediately.
3. A creator can ban a user from commenting on their content; that user's subsequent comment attempts on that creator's posts are rejected.
4. A report on a comment appears in the same admin moderation queue used for content and live-stream reports, with an audit trail entry.
5. Comment lists are cursor-paginated and remain fast regardless of comment volume on a popular post.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Reaction type set (like-only vs. multi-emoji)? | Small fixed enum (e.g. `like`,`amen`,`pray`) reflecting faith-native tone per charter §4.2 differentiators | technical-spec reaction enum |
| Q2 | Does a creator ban apply per-post, per-creator (all their content), or platform-wide? | Per-creator (all their content) — matches FR-COMM-1.7 as written | data model |
| Q3 | Roadmap row assignment for this surface | Add as a new M3 row alongside M3-01..M3-06 when scheduled | planning, not spec |

## 12. Traceability

Each FR maps to schema/endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability". Live chat's FRs remain owned by [`live-streaming` SRS §6.4](../live-streaming/srs.md#64-live-chat-reactions--moderation-fr-4x), cited here rather than restated.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
