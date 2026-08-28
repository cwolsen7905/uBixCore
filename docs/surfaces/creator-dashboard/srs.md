# Creator Dashboard — Software Requirements Specification (SRS)

**Surface:** `creator-dashboard` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M2 (roadmap **S9**, earnings half) · **Prerequisites:** `payments` (S7), `payouts` (S10), `content-posts` (S5); reads `live-streaming` (FR-LIVE, M3) data when present
**Companion docs:** [`technical-spec.md`](technical-spec.md) · project [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (parent FR-DASH domain, §5.11)

> Authored against the Platform SRS/TDS/ADS trio. The SRS says **what** and **why**; the technical-spec says **how in code**. No `architecture.md` — this surface inherits the [Platform ADS](../../projects/sowing-me/platform/architecture.md).

## 1. Purpose

Give a creator one place to see whether Sowing.me is working for them: what they've earned, who's subscribed (and who's leaving), how their posts and streams are performing, and whether their payout is on track. This is the counterpart to `supporter-feed` for the creator persona, and the surface that makes the platform's MVP promise ("a creator can get paid") visible and trustworthy.

## 2. Scope

**In scope:** earnings summary and ledger detail (subscriptions/tips/gifts), subscriber count/list, churn, post performance metrics, stream performance metrics (when `live-streaming` exists), payout status.

**Out of scope (this surface):** the money-movement logic itself (owned by `payments`, S7, and `payouts`, S10 — this surface reads their ledger/account state); post authoring/CRUD (owned by `content-posts`, S5 — the library shell's Posts/Collections/Drafts tabs are that surface's concern; this surface only overlays performance numbers onto posts that already exist); live broadcast operation (owned by `live-streaming`, M3 — this surface only reads its per-stream analytics).

## 3. Context — reads across four surfaces

| Concern | Owned by | This surface's role |
|---|---|---|
| Money ledger (`transactions`: subscription/tip/gift/fee/payout/refund) | Platform — the ledger spine | Reads, aggregates by creator + type + period |
| Subscriptions (active/cancelled, tier) | `subscription-tiers` / `payments` | Reads for subscriber count/list/churn |
| Payout account status | `payouts` (S10) — `payout_accounts` | Reads for payout status |
| Post existence & metadata | `content-posts` (S5) — `posts` | Reads to attach performance numbers |
| Stream existence & analytics | `live-streaming` (FR-LIVE, M3) — `live_streams` | Reads (conditional — section renders empty pre-M3) |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Earnings** | The sum of `transactions` rows attributable to this creator as the receiving party, by type (subscription, tip, gift) and period. |
| **Churn** | The rate at which active subscriptions to this creator end within a period, relative to the active count at the period start. |
| **Payout status** | The creator's current Connect account state (`payout_accounts.status`) plus their most recent and pending `transactions` rows of type `payout`. |

## 5. Personas & primary user stories

- **Creator.** "As a creator, I want to see how much I've earned this month, broken down by subscriptions vs. tips vs. gifts, without doing my own math."
- **Creator (retention).** "As a creator, I want to know how many supporters I have, and whether I'm losing more than I'm gaining."
- **Creator (content).** "As a creator, I want to know which posts (and, once live is available, which streams) actually land with my audience."
- **Creator (getting paid).** "As a creator, I want to know my payout is set up correctly and see when money actually lands."

## 6. Functional requirements

Parent: Platform SRS FR-DASH-1/2 (§5.11).

### 6.1 Earnings (FR-DASH-1)
- **FR-10** The dashboard shows an earnings summary for a selectable period (e.g. this month, last 30 days, all-time): total and per-type (subscription/tip/gift) amounts, in the creator's payout currency, displayed from minor-unit ledger amounts (Platform TDS §3).
- **FR-11** The dashboard shows a detailed, browsable earnings/transaction table (date, type, supporter, amount) for the creator's own `transactions` rows.
- **FR-12** Platform commission (`fee` rows, Platform TDS §7) is visible as a deduction, not hidden — a creator sees gross and net.

### 6.2 Subscribers & churn (FR-DASH-1)
- **FR-20** The dashboard shows the creator's current active subscriber count, and a browsable list (supporter display name, tier, subscribed-since date, status).
- **FR-21** The dashboard shows a churn figure for a selectable period: subscriptions that ended in the period ÷ active subscriptions at the period's start.
- **FR-22** Subscriber list and churn reflect `subscriptions.status` transitions already owned by `payments`/`subscription-tiers` — this surface does not introduce a parallel status field.

### 6.3 Post & stream performance (FR-DASH-2)
- **FR-30** The dashboard shows, per post, at minimum a view count and (where available) unique-viewer count, browsable alongside the creator's post list.
- **FR-31** Once `live-streaming` (M3) exists, the dashboard shows per-stream metrics sourced from that surface's own analytics (peak/unique viewers, watch time, chat volume, tips — live-streaming SRS FR-80): live-streaming continues to own that data model; this surface only reads and displays it.
- **FR-32** Before `live-streaming` ships, the stream-performance section is present in the UI but shows an empty/"coming soon" state rather than being removed — the layout doesn't need rework when M3 lands (mirrors the platform's extensibility posture, Platform ADS §9).

### 6.4 Payout status (FR-DASH-2)
- **FR-40** The dashboard shows the creator's Connect payout-account status (`payout_accounts.status`, owned by `payouts`, S10) and, if not yet fully onboarded, a clear call to complete onboarding.
- **FR-41** The dashboard shows the most recent payout(s) and any pending payout amount, sourced from `transactions` rows of type `payout`.

## 7. Non-functional requirements

- **NFR-1 Correctness.** Earnings, churn, and payout figures are derived only from the ledger/subscription/payout tables already owned elsewhere (§3) — this surface never becomes a second source of truth for money or subscription state.
- **NFR-2 Performance.** Dashboard reads target Platform NFR-PERF (p95 < 300 ms excl. third-party); summary figures are Memcached hot reads (`memcache-keys.md`) — see technical-spec §5.
- **NFR-3 Privacy.** Earnings/payout data is PII-adjacent financial data — permission-gated (creator sees only their own) and audit-logged where it touches payout details (`sensitive-data-access.md`, Platform NFR-PRIV).
- **NFR-4 Standards.** DataType/Payload/DTO/Repository, PHP-DI, Slim 4 (`complete-php-guide.md`); SvelteKit per `complete-js-guide.md`; every table via the migration runner.
- **NFR-5 Accessibility.** WCAG 2.1 AA (Platform NFR-A11Y) for tables and summary tiles.

## 8. External interfaces (summary — detail in technical-spec)

- **Creator dashboard** (SvelteKit, `SowingMeJs`, existing shell `creator/dashboard`): earnings summary, subscriber/churn tiles, payout status.
- **Creator library** (SvelteKit, existing shell `creator/library`): post list (owned by `content-posts`) gains a performance overlay/column from this surface.
- **`SowingMeApi`**: earnings summary + detail, subscriber list + churn, post performance, stream performance (conditional), payout status.

## 9. Constraints & assumptions

- Requires `transactions`, `subscriptions`, `payout_accounts` to exist (Platform TDS §3, `payments`/`payouts` surfaces) and `posts` to exist (`content-posts`).
- Post view-count tracking does not exist yet anywhere in the platform; this surface introduces the minimal counter needed (technical-spec §2) rather than assuming `content-posts` already tracks it.
- Stream performance (§6.3 FR-31/32) is a forward reference — no schema from this surface depends on `live-streaming` tables existing at M2; it is purely a conditional read added later.

## 10. Acceptance criteria (surface DoD)

1. A creator with subscription, tip, and gift transactions sees a correct earnings summary for "this month" and "all-time," with gross/net (commission) both visible.
2. The earnings detail table pages correctly (offset, with a correct total) and matches the summary totals for the same period.
3. Subscriber count and list match the creator's actual active `subscriptions` rows; churn for a period with known cancellations computes correctly.
4. A post with recorded views shows a non-zero view count on the dashboard/library overlay.
5. Before `live-streaming` ships, the stream-performance section renders an empty/coming-soon state without erroring.
6. Payout status reflects the creator's actual `payout_accounts.status`, and a recent `payout`-type transaction appears in the payout history.
7. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Is a post "view" counted per authenticated read, per unique viewer per day, or both? | Both: a raw view counter plus an async-deduplicated unique-viewer count (technical-spec §2) | FR-30 |
| Q2 | Churn period presets — fixed (7/30/90 day) or creator-selectable range? | Fixed presets at M2 (7/30/90 day); custom range is a fast-follow | FR-21 |
| Q3 | Does the dashboard show gross-only or does it need per-transaction commission breakdown in the detail table? | Summary shows gross/net; detail table rows show gross with a separate `fee` row visible in the same table (FR-12) rather than a subtracted column | FR-11,12 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **S9**. Changes to any FR update the traceability table and re-version both companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
