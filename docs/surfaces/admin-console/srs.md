# Admin Console — Software Requirements Specification (SRS)

**Surface:** `admin-console` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 · **Parent:** platform FR-ADMIN ([`srs.md`](../../projects/sowing-me/platform/srs.md) §5.12) · **Roadmap:** S11
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md) · platform [`srs.md`](../../projects/sowing-me/platform/srs.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface expands platform FR-ADMIN-1..3 into buildable requirements. It does not restate the platform SRS/TDS/ADS — see those for the domain model, layering, and system design this surface inherits.

## 1. Purpose

Give platform admins (us) one internal console to run Sowing.me day to day: see and act on users/creators/organizations, see where the money is and intervene when it's wrong, manage the affiliate program, and reach the moderation queue. This is the operational backstop behind the public product — it exists so a human can suspend a bad actor, fix a stuck payout, or resolve a dispute without a database console.

## 2. Scope

**In scope:** user/creator/organization directory with status changes (suspend/reinstate); transaction/ledger read views; payout oversight and dispute handling (view + operator actions that reflect into the ledger); affiliate and organization management (list, detail, status); a moderation queue **entry point** that lists open reports and links into moderation actions.

**Out of scope (this surface):** the content-policy rules, report intake UX, and moderation action semantics (hide/remove/suspend-for-policy) — all owned by [`trust-safety`](../trust-safety/README.md); this surface only surfaces the queue and executes the status changes trust-safety or a payments dispute decides. Platform-metrics dashboards beyond what's needed to operate (creator/analytics dashboards are FR-DASH, a separate surface). Building a general BI tool.

## 3. Context — why this isn't "just a database view"

Every action here touches money, PII, or account status — all three are audit-logged categories under [`sensitive-data-access.md`](../../standards/sensitive-data-access.md) and platform NFR-SEC/NFR-PRIV. The console is internal-exposure (`SowingMeAdminApi`/`SowingMeAdminJs`, ADS §2), but "internal" does not mean "unaudited" — an admin looking up a supporter's transaction history is a PII access event same as any customer-facing lookup elsewhere in uBix Core.

## 4. Definitions

| Term | Meaning |
|---|---|
| **Status change** | Transition of a `users`/`creators`/`organizations` row's account-status field (e.g. `active`→`suspended`→`active`), distinct from a moderation action taken for content-policy reasons (trust-safety owns those semantics; this surface owns the mechanism admins use to apply them). |
| **Ledger view** | A read-only, filterable/sortable view over `transactions` — never a mutation path; corrections happen via provider-reflected adjustments (refund/dispute rows), never an edit to an existing row. |
| **Dispute** | A Stripe chargeback/dispute reflected into `transactions`; "handling" here means visibility + operator response (submit evidence via Stripe, mark reviewed), not building our own dispute adjudication. |
| **Moderation queue entry point** | The admin-console screen that lists open `reports` and routes an admin into the trust-safety moderation action flow; this surface does not define what actions exist. |

## 5. Personas

| Persona | Needs |
|---|---|
| **Platform admin / moderator** | Look up any user/creator/org, see status and history, suspend/reinstate, see the ledger, resolve a stuck payout or a dispute, manage affiliates/orgs, reach the moderation queue. |
| **Finance-facing admin** (may be the same person) | Payout oversight, dispute handling, ledger correctness — the finance half of FR-ADMIN. |

No supporter/creator-facing persona touches this surface — it is internal only (platform ADS §5, §2).

## 6. Functional requirements

### 6.1 Access & auth (FR-ADMIN-1x)
- **FR-ADMIN-10** Admin console access requires an authenticated `admin` role session; every route is both role-gated (middleware) and, for routes returning subject PII, `permissionKey`-gated per [`sensitive-data-access.md`](../../standards/sensitive-data-access.md) — session auth alone is never sufficient for a PII-returning route.
- **FR-ADMIN-11** The existing skeleton `GET/POST /auth` becomes the real admin login/session-validate pair, built out from stub to a working `AuthController` (currently referenced by `Routes.php` but absent from `php/Ubix/Controller/`).

### 6.2 User / creator / organization directory & status changes (FR-ADMIN-2x)
- **FR-ADMIN-20** Offset-paginated, sortable, searchable list of users, of creators, and of organizations (three views over related entities), per [`pagination.md`](../../standards/pagination.md) — admin tables are the canonical offset use case.
- **FR-ADMIN-21** Detail view per user/creator/org: profile summary, current status, role(s) held, subscription/tier if a supporter, recent transactions, recent moderation history (linked from trust-safety).
- **FR-ADMIN-22** Status change actions: suspend and reinstate a user, creator, or organization. A suspend action cascades per entity (e.g. suspending a creator hides their page and gates new subscriptions; exact cascade rules are a service-layer concern in the technical spec).
- **FR-ADMIN-23** Every status change and every PII-returning lookup writes an audit record (FR-ADMIN-1x + `sensitive-data-access.md`); status changes additionally write to `audit_logs` alongside the `Pii_Access_Audits` PII trail where a lookup preceded the action.
- **FR-ADMIN-24** A status change requires a reason (free text minimum), stored with the audit record.

### 6.3 Transaction & ledger views (FR-ADMIN-3x)
- **FR-ADMIN-30** Offset-paginated `transactions` ledger view, filterable by type (`subscription`/`tip`/`gift`/`tithe`/`payout`/`fee`/`refund`), date range, creator/org, and supporter; sortable by date/amount.
- **FR-ADMIN-31** Transaction detail: full row plus its `provider_ref`, related entity (creator/org/post/stream), and any linked refund/dispute rows.
- **FR-ADMIN-32** Ledger views are **read-only** — no endpoint here writes a `transactions` row directly; corrections happen only through the payment provider (refund/dispute) and are reflected back via the existing webhook path (platform TDS §7), never a manual edit.

### 6.4 Payout oversight & dispute handling (FR-ADMIN-4x)
- **FR-ADMIN-40** Payout list (per creator/org): scheduled, in-flight, completed, failed, with `payout_accounts` status (Connect onboarding state).
- **FR-ADMIN-41** An admin can view why a payout failed (Stripe Connect error surfaced) and see the retry/next-scheduled state; this surface does not build a manual override of Stripe's payout mechanics.
- **FR-ADMIN-42** Dispute/chargeback list, reflected from `transactions` rows of type `refund` or a dispute marker; an admin can mark a dispute reviewed and record a note. Evidence submission itself happens in Stripe's dashboard (no in-house evidence workflow at M2).
- **FR-ADMIN-43** Dispute and payout-failure views and any status/note change are audit-logged (money-movement audit, platform ADS §5).

### 6.5 Affiliate & organization management (FR-ADMIN-5x)
- **FR-ADMIN-50** Affiliate list and detail — builds out the existing skeleton `GET /affiliates` and `GET /affiliate/{id}` from stub routes (wired to a non-existent `AffiliateController`) into working list/detail screens with real repositories.
- **FR-ADMIN-51** Affiliate status changes (approve, suspend) and visibility into referral/attribution activity (`attribution_logs`).
- **FR-ADMIN-52** Organization management: list, detail, contributor roster, status changes (shares the FR-ADMIN-22 mechanism); org-specific fields (ministry verification state, per FR-ORG) surfaced as they land — reserved, not built at M2.

### 6.6 Moderation queue entry point (FR-ADMIN-6x)
- **FR-ADMIN-60** An admin-console screen lists open `reports` (count + a paginated list) as a dashboard entry point into moderation. Report content, decisioning, and the `moderation_actions` taken are entirely defined and owned by [`trust-safety`](../trust-safety/srs.md) (FR-TRUST) — this surface links out, it does not duplicate that flow.
- **FR-ADMIN-61** The admin console's user/creator/org status-change mechanism (FR-ADMIN-22) is the same mechanism trust-safety's suspend action invokes — one suspend implementation, two callers (an operational status change here, a policy-driven one there).

## 7. Non-functional requirements

- **NFR-ADMIN-1 Security.** Every PII-returning route is `permissionKey`-gated + `AdminFunctionAccessMiddleware`, never session-auth-only ([`sensitive-data-access.md`](../../standards/sensitive-data-access.md)). Internal-exposure boundary per platform ADS §5.
- **NFR-ADMIN-2 Audit.** Every subject-PII access writes `Pii_Access_Audits` (one row per subject); every status change, payout note, and dispute-review action writes `audit_logs`. Both are DB rows, never Monolog-only.
- **NFR-ADMIN-3 Pagination.** All admin list endpoints (users, creators, organizations, transactions, payouts, affiliates, reports) use offset pagination per [`pagination.md`](../../standards/pagination.md) — `AbstractPaginatedRequestPayload`/`AbstractPaginatedResponsePayload`, `{ items, limit, offset, total }`. No cursor pagination on this surface (bounded, browsable admin-scale data throughout).
- **NFR-ADMIN-4 Performance.** Admin-scale data (hundreds to low-thousands of rows); client-side prefetch (pagination.md §7.1) is the default performance move — no background pre-warming needed at this scale.
- **NFR-ADMIN-5 Standards.** PHP follows DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via `bin/ubix migrate:*`; JS follows `complete-js-guide.md`.
- **NFR-ADMIN-6 No mutation of money history.** No endpoint edits or deletes a `transactions` row; every ledger change is an additive row reflecting a provider event.

## 8. External interfaces (summary — detail in technical-spec)

- **`SowingMeAdminApi`**: auth, user/creator/org directory + status changes, transactions/ledger, payouts, disputes, affiliates, organizations, reports (entry point).
- **`SowingMeAdminJs`**: directory tables (server-driven `DataTable`), detail pages, ledger/payout/dispute views, affiliate/org management screens, a moderation-queue landing screen linking into trust-safety's flow.

## 9. Constraints & assumptions

- Reuses existing entities that must exist first: `users`, `creators`, `organizations`, `transactions`, `payout_accounts`, `affiliates`, `attribution_logs`, `reports` (owned by trust-safety), `audit_logs`.
- `SowingMeAdminApi`/`SowingMeAdminJs` scaffolding (Slim app, SvelteKit shell, k8s manifests) already exists; the domain code (controllers, services, repositories) does not — see [`README.md`](README.md).
- Runs on the existing Rancher/k3s cluster, GitLab CI, MariaDB via migration runner (platform ADS §3).

## 10. Acceptance criteria (surface DoD)

1. An admin logs in via the real (non-stub) auth flow and reaches a user/creator/organization list with working offset pagination, search, and sort.
2. An admin suspends a creator; the creator's page reflects suspended state; the action appears in `audit_logs`; reinstating reverses it.
3. An admin filters the transaction ledger by type and date range and opens a transaction's detail, including any linked refund/dispute.
4. An admin sees a failed payout and its Stripe-surfaced failure reason; marks a dispute reviewed with a note; both actions are audit-logged.
5. An admin opens the affiliate list/detail (built out from the existing stub routes) and changes an affiliate's status.
6. An admin sees the moderation queue entry point (open report count + list) and can navigate into a trust-safety moderation action.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Does a suspend cascade need per-entity custom logic (e.g. creator suspend also pausing active subscriptions/payouts) at M2, or is "hide + block new subscriptions" sufficient for beta? | Hide + block new subscriptions only; full cascade (existing subscription handling) deferred | FR-ADMIN-22 |
| Q2 | Is in-house dispute-evidence submission ever in scope, or permanently "go to Stripe"? | Permanently out of scope; console is visibility + note only | FR-ADMIN-42 |
| Q3 | Do org-specific admin fields (ministry verification) land in this surface at M2 or wait for FR-ORG (M3+)? | Reserved column/UI slot now, logic at FR-ORG | FR-ADMIN-52 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **S11** / milestone **M2**. Parent requirements: platform FR-ADMIN-1 (§6.2), FR-ADMIN-2 (§6.3–6.4), FR-ADMIN-3 (§6.5–6.6).

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
