# Messaging — Software Requirements Specification (SRS)

**Surface:** `messaging` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3 (post-MVP) · **Prerequisites:** `subscription-tiers` (M2, built), `EntitlementService` (M1, built), [`notifications`](../notifications/README.md) (M3, spec complete — see that surface's status), trust-safety moderation queue (`reports`/`moderation_actions`/`audit_logs`, M2/M3)
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md)
**Upstream:** platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.16 (FR-MSG-1) · [`../../projects/sowing-me/charter.md`](../../projects/sowing-me/charter.md) §4.2 · [`../../projects/sowing-me/brief.md`](../../projects/sowing-me/brief.md) §3

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This SRS decomposes the platform-altitude **FR-MSG-1** into surface-level requirements, numbered `FR-MSG-1.x` so every requirement here traces to its platform parent without inventing a parallel ID scheme.

## 1. Purpose

Let a supporter and a creator exchange direct messages, and let a creator send one message to every supporter of a given tier (a broadcast), while keeping the surface safe: recipients can block a sender, senders are rate-limited, and either party can report a conversation into the platform's trust-safety queue.

## 2. Scope

**In scope:** 1:1 supporter↔creator conversations; creator broadcast to a tier (fan-out write, not a real conversation thread per recipient); text messages; cursor-paginated conversation and message lists; block; per-user/per-window rate-limiting; report-to-trust-safety; real-time delivery via WebSocket with a polling fallback.

**Out of scope (this surface):** group/community chat (platform SRS §5.15 "community spaces/groups: future"); message attachments (images/audio — candidate for a later revision, follows the `media-storage` pipeline when added); supporter↔supporter messaging (creator is always one side of a conversation); live-stream chat (owned by [`live-streaming`](../live-streaming/README.md) — a different, ephemeral-per-broadcast concern, not this surface's `conversations`/`messages`).

## 3. Context — relationship to comments and live chat

Sowing.me has three person-to-person communication surfaces at M3: **messaging** (this doc, private/broadcast, persistent), **comments-community** (public, attached to a post), and **live-streaming**'s live chat (public, attached to a broadcast, ephemeral-ish). All three call the same `EntitlementService` for gating and the same trust-safety pipeline (`reports`/`moderation_actions`/`audit_logs`) for abuse — this SRS does not duplicate live chat's moderation model, it reuses the pattern.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Conversation** | A `conversations` row: exactly one supporter and one creator (1:1), or a creator-owned broadcast conversation representing "this creator's messages to tier X". |
| **Message** | A `messages` row belonging to a conversation, authored by one participant. |
| **Broadcast** | A creator-authored message fanned out to every supporter currently entitled to a chosen tier; recipients see it in a conversation with that creator, they do not reply to the whole tier. |
| **Block** | A supporter or creator preventing the other party from starting or continuing a conversation with them. |
| **Report** | Escalating a conversation/message into the shared trust-safety queue (`reports` → `moderation_actions` → `audit_logs`, platform TDS §3). |

## 5. Personas & primary user stories

- **Supporter.** "As a subscriber, I want to message my creator directly, and I want to be able to block or report someone who abuses that."
- **Creator.** "As a creator, I want to reply to individual supporters and also send one update to everyone on my $10 tier, without writing 500 individual messages."
- **Platform admin/moderator.** "As an admin, I want abusive conversations reported into the same moderation queue as everything else, with an audit trail."

## 6. Functional requirements

### 6.1 Conversations & messages (parent: platform FR-MSG-1)

- **FR-MSG-1.1** A supporter or a creator can start a 1:1 conversation with the other (respecting block state, §6.3). A user with an existing conversation to a given counterpart re-uses it rather than creating a duplicate.
- **FR-MSG-1.2** A creator can broadcast a message to a chosen tier; the message is delivered into each currently-entitled supporter's conversation with that creator (creating one if it doesn't exist), gated through `EntitlementService` exactly as tier-gated posts are (platform TDS §6).
- **FR-MSG-1.3** Conversation list (per user) is cursor-paginated, ordered by most-recent-message (`pagination.md` §4 — unbounded, append-only-by-activity).
- **FR-MSG-1.4** Message list within a conversation is cursor-paginated (`pagination.md` §4).
- **FR-MSG-1.5** Messages are text at M3; message length is bounded (platform-configured max) to keep abuse surface and storage bounded.
- **FR-MSG-1.6** A conversation shows read/unread state per participant.

### 6.2 Real-time delivery

- **FR-MSG-2.1** New messages in an open conversation appear without a manual refresh, via a WebSocket connection (technical-spec §6); a viewer without an active connection sees the new message on next load/poll.
- **FR-MSG-2.2** A new-message event that arrives while the recipient is not actively viewing the conversation triggers a `notifications` fan-out (the `notifications`/`notification_prefs` surface — this surface is a caller of `NotifierInterface`, not a re-implementation of it).

### 6.3 Abuse controls

- **FR-MSG-3.1** **Block:** either party in a 1:1 conversation can block the other; a block prevents the blocked party from sending further messages or starting a new conversation with the blocker, in both directions of who blocked whom being visible only to the blocker.
- **FR-MSG-3.2** **Rate-limit:** a sender is capped at N messages per window (default: per-recipient and platform-wide caps) to blunt spam/harassment bursts; a creator broadcast counts against a separate, higher broadcast-specific limit (it is one authored message fanned out, not N individual sends).
- **FR-MSG-3.3** **Report:** either party can report a conversation or a specific message; a report creates a `reports` row visible in the same admin moderation queue as content/live-stream reports (platform TDS §3, mirrors live-streaming FR-91/FR-92's admin pattern), and — like `FR-TRUST-2` — moderation actions taken are audit-logged (`sensitive-data-access.md`).
- **FR-MSG-3.4** A blocked or reported sender's ability to message is unaffected until an admin acts (block is user-controlled and immediate; report is escalation, not automatic restriction) — consistent with the platform's moderation-queue model (FR-TRUST-2) rather than automated takedown.

## 7. Non-functional requirements

- **NFR-MSG-SEC** Server-side authorization on every message read/write: a user may only read conversations they are a participant in; ownership + role middleware on every route (platform TDS §4/§6).
- **NFR-MSG-ENT** Broadcast delivery is entitlement-checked at send time against current subscribers — a supporter who cancels a tier stops receiving new broadcasts to it, but retains their existing message history (no retroactive deletion).
- **NFR-MSG-PERF** Cursor pagination bounds query cost on both lists (platform NFR-PERF); WebSocket delivery adds no additional HTTP polling load for an actively-open conversation.
- **NFR-MSG-PRIV** Message content is user-generated content and potentially sensitive; access is participant-scoped; reports/moderation actions are audit-logged (`sensitive-data-access.md`).
- **NFR-MSG-AVAIL** A dropped WebSocket connection degrades to "message appears on next reconnect/poll", never data loss — every message is durably written to `messages` before/independent of any real-time push (platform NFR-AVAIL "graceful degradation").
- **NFR-STD** DataType/Payload/DTO/Repository, PHP-DI, Slim 4; PHPStan max; custom sniffs; strict PHPUnit; every table via `bin/ubix migrate:*`; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **`SowingMeApi`**: conversation/message CRUD (cursor-paginated), block, report, broadcast-send, a WebSocket endpoint for real-time delivery.
- **Product SPA (`SowingMeJs`)**: inbox (conversation list), thread view, composer, broadcast composer (creator-only), block/report actions.
- **`SowingMeAdminApi`**: message reports queue (reuses the shared trust-safety queue UI pattern, not a parallel one).
- **`notifications` surface**: called for new-message alerts (FR-MSG-2.2).

## 9. Constraints & assumptions

- No card data or payment flow is involved in messaging; tips-in-DM are not in scope (in-stream tips are live-streaming's FR-70, a different surface).
- Real-time transport is new lightweight infrastructure for Sowing.me — it does not reuse project-neptune's F4F-domain chat-room WebSocket path (`complete-js-guide.md` §5.4), which is specific to that app's cross-domain nginx routing. It follows the same *pattern* (browser-direct WS, exponential-backoff reconnect) — see technical-spec §6 for the exact delta.
- Broadcast fan-out volume is bounded by a creator's subscriber count per tier — no unbounded audience concern distinct from what `subscription-tiers`/`EntitlementService` already handle.

## 10. Acceptance criteria (surface DoD)

1. A supporter and a creator can exchange messages in a 1:1 conversation; each sees the other's messages appear live while both have the thread open.
2. A creator sends a broadcast to Tier B; every currently-entitled Tier B (and above, per gating precedence) supporter receives it in their conversation with that creator; a non-subscriber and a lower-tier supporter do not.
3. A blocked user cannot send a new message to the blocker; the blocker does not see a "blocked" indicator that identifies who blocked whom to the blocked party.
4. A user report on a conversation appears in the admin moderation queue with an audit trail entry.
5. A user exceeding the rate-limit window is rejected with a clear error, not a silently dropped message.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Reuse an existing lightweight WS service (if `live-streaming`'s chat WS lands first) or stand up messaging's own? | Stand up messaging's own; converge later if duplication becomes a burden (mirrors live-streaming SRS Q5's framing) | technical-spec §6 |
| Q2 | Broadcast conversation model: one shared thread per tier, or fan-out into each supporter's existing 1:1 thread? | Fan-out into each supporter's 1:1 thread (default, §6.1 FR-MSG-1.2) — keeps one mental model ("my conversation with this creator") | data model |
| Q3 | Message attachments timeline? | Not in this surface; `media-storage` integration is a follow-up slice | FR-MSG-1.5 |

## 12. Traceability

Each FR maps to schema/endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **M3-03**.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
