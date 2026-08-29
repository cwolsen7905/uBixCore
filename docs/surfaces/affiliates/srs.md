# Affiliates — Software Requirements Specification (SRS)

**Surface:** `affiliates` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3 (post-MVP) · **Prerequisites:** `creator-profile`, `payments` (`transactions` ledger), `payouts` (`payout_accounts`), `subscription-tiers`
**Companion docs:** [`technical-spec.md`](technical-spec.md) · [`README.md`](README.md) · platform [`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) (FR-AFF, FR-FAITH-6)

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). The SRS says **what** and **why**; the technical-spec says **how in code**. This surface has no `architecture.md` of its own — it inherits the platform ADS ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)), especially §4 (data architecture, ADR-007 owner polymorphism) and §9 (extensibility contract row "Affiliate revenue-share"). A requirement change here re-versions this SRS and `technical-spec.md` together.

## 1. Purpose

Let a Sowing.me creator or church/organization refer new supporters and creators to the platform, track who brought whom, and earn a **revenue share** on the business that referral generates — paid through the platform's existing money rails rather than a parallel one. This realises platform **FR-AFF-1/FR-AFF-2** and the faith-native differentiator **FR-FAITH-6** (church affiliate revenue-share).

## 2. Scope

**In scope:** affiliate enrollment (creator or organization), referral links + banners, cookie/link-based attribution tracking with a first/last-touch window, referral records, revenue-share calculation on referred transactions, payout of accrued revenue share via the `transactions` ledger and the `payouts` surface, an affiliate-facing dashboard, admin affiliate management (realising the existing `SowingMeAdminApi` stubs), and church/organization revenue-share for creators and supporters a church brings (FR-FAITH-6).

**Out of scope (this surface):** multi-tier/MLM affiliate structures (sub-affiliates); paid ad-network integrations; affiliate self-serve banner *design* tooling (creatives are platform-supplied initially); public affiliate marketplace/discovery. Candidates for a later revision.

## 3. Context — how this differs from a generic affiliate program

| Generic SaaS affiliate program | Our stance |
|---|---|
| Affiliate = an external marketer, unrelated to the product | **Extend.** An affiliate is usually one of our own **creators** or a **church/organization** (`organizations`) referring supporters or other creators — the affiliate graph overlaps the creator/org graph rather than sitting beside it (FR-FAITH-6). |
| Revenue share paid via a separate payment rail (PayPal, manual) | **Adopt the platform's own rails.** Revenue share accrues and pays out through the same `transactions` ledger (`fee`/`payout` types) and `payouts` surface every creator/org already uses — no parallel money system (platform ADR-004, TDS §7). |
| Attribution is last-click only | **Adopt configurable first/last-touch** within an attribution window, because a church referring a supporter and a creator referring a fellow creator have different natural attribution shapes. |
| Affiliate signup is public self-serve | **Adopt for creators**, but church/organization affiliate status additionally reflects the org's standing as a ministry account (admin-reviewable), consistent with FR-FAITH-1's values-aligned posture. |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Affiliate** | A `creator` or `organization` enrolled to earn revenue share for referrals it brings. |
| **Referral link / banner** | A URL or creative carrying an affiliate's referral **code**, used to drive traffic to a creator page, the explore page, or a signup flow. |
| **Click / attribution event** | A recorded visit through a referral link, written to `attribution_logs`. |
| **Attribution window** | The time period after a click during which a resulting signup/subscription/tithe is still credited to the affiliate. |
| **First-touch / last-touch** | Attribution model choosing whether the *first* recorded click or the *most recent* recorded click before conversion gets credit, when a visitor touches more than one referral link. |
| **Referral** | A tracked outcome (`referrals` row) linking an affiliate to the user/creator/org it brought, with a status (`pending`→`converted`/`expired`). |
| **Revenue share** | The percentage or flat rate of a referred transaction's value paid to the affiliate, configured per affiliate. |
| **Church affiliate revenue-share** | FR-FAITH-6: an `organization` (church/ministry) acting as the affiliate for creators or supporters it brings, using the same affiliate/referral/ledger mechanism as a creator affiliate. |

## 5. Personas & primary user stories

- **Affiliate creator.** "As a creator, I get a referral link and banners for my page; when someone I refer subscribes, I see the referral in my dashboard and my share shows up in my payout, same as my regular earnings."
- **Church / organization admin (affiliate).** "As a church admin, I refer both supporters and other creators to Sowing.me; my church earns a revenue share on what they give and pay, consolidated with our organization's payouts." (FR-FAITH-6.)
- **Referred supporter/creator.** Experiences no visible difference — attribution is transparent and never blocks or alters their own flow.
- **Platform admin.** "As an admin, I can see all affiliates, review church/org affiliate applications, inspect referral and attribution activity, and adjust or suspend an affiliate's status."

## 6. Functional requirements

### 6.1 Enrollment & affiliate management (FR-AFF-10x)
- **FR-AFF-101** A `creator` can self-enroll as an affiliate from their dashboard; enrollment creates an `affiliates` row with `status=pending` (or `active` if auto-approved per platform policy).
- **FR-AFF-102** An `organization` can enroll as an affiliate the same way, from its org admin console (FR-FAITH-6); this is the mechanism by which a church earns referral revenue for creators/supporters it brings.
- **FR-AFF-103** Each affiliate has exactly one owner — a `creator` **or** an `organization`, never both (mirrors the platform's owner-polymorphism approach, ADR-007) — and a unique, human-shareable referral **code**.
- **FR-AFF-104** Each affiliate has a configured revenue-share **rate type** (percentage or flat) and **rate value**; default rate is a platform-wide config, overridable per affiliate by an admin.
- **FR-AFF-105** Affiliate `status` lifecycle: `pending` → `active` → `suspended`/`inactive`; only an `active` affiliate accrues new referrals.

### 6.2 Referral links & banners (FR-AFF-11x)
- **FR-AFF-110** An active affiliate can generate referral links to any public creator page, the explore page, or a signup flow, each carrying the affiliate's `code`.
- **FR-AFF-111** The platform supplies a set of referral banner creatives (image + link) an affiliate can copy/embed; affiliate-supplied creative upload is out of scope for this revision.
- **FR-AFF-112** A referral link/banner records which surface it points at, so the dashboard can show which creative is driving referrals.

### 6.3 Attribution tracking (FR-AFF-12x)
- **FR-AFF-120** Visiting a referral link writes an `attribution_logs` entry (click) and sets a first-party attribution cookie carrying the affiliate `code` and a click token.
- **FR-AFF-121** Attribution is resolved server-side at the moment of a qualifying conversion event (signup, subscription start, tithe/gift, creator onboarding) by reading the attribution cookie or a link-carried code param if no cookie is present (cookie-blocked clients).
- **FR-AFF-122** Attribution uses a configurable **window** (default 30 days) from the click; a conversion after the window does not credit the affiliate.
- **FR-AFF-123** Attribution model is configurable per platform default (default: **last-touch** within the window) — if a visitor clicks more than one affiliate's link before converting, the most recent qualifying click within the window wins; **first-touch** is supported as an alternate mode for a future revision without a schema change (the log already carries every touch).
- **FR-AFF-124** A conversion creates exactly one `referrals` row referencing the winning `attribution_logs` entry; duplicate conversions for the same user/affiliate pair do not create duplicate referrals.

### 6.4 Referral records (FR-AFF-13x)
- **FR-AFF-130** A `referrals` row tracks: the affiliate, the referred user/creator/organization, status (`pending`→`converted`/`expired`), first-touch and last-touch timestamps, and the conversion event it resolves to.
- **FR-AFF-131** A referral can be **church-to-creator** (a church refers a new creator), **church-to-supporter** (a church refers a giving supporter — FR-FAITH-6), or **creator-to-creator/supporter** — the referred party type is recorded, not assumed.

### 6.5 Revenue-share calculation & payout (FR-AFF-14x)
- **FR-AFF-140** When a referred conversion produces revenue (a subscription charge, tip, gift, or tithe recorded in the `transactions` ledger), the platform calculates the affiliate's revenue share using the affiliate's rate against that transaction's amount.
- **FR-AFF-141** The calculated share is written to the `transactions` ledger as a `fee` row (the platform's cost of the referral, related to the original transaction) and a corresponding `payout`-eligible accrual credited to the affiliate owner's (`creator` or `organization`) balance — **no new ledger types and no parallel payment rail**; this is data in the existing ledger, per platform ADR-004 and the extensibility contract (platform architecture §9).
- **FR-AFF-142** Accrued affiliate revenue share is paid out through the existing `payouts` surface's scheduled payout run to the affiliate owner's `payout_account`, alongside that creator's/org's own earnings; affiliates never receive a separate payout instrument.
- **FR-AFF-143** Refunds/chargebacks on a referred transaction reverse the associated revenue-share `fee`/accrual the same way they reverse platform commission (payments surface's refund/dispute handling).
- **FR-AFF-144** Money amounts throughout this surface are minor units (INT) + currency code, never floats, per platform TDS §3.

### 6.6 Affiliate dashboard (FR-AFF-15x)
- **FR-AFF-150** An affiliate (creator or org admin) sees: their referral code/links, banner creatives, referral list with status, cumulative and pending revenue-share earnings, and payout history (surfaced from the `payouts` surface).
- **FR-AFF-151** Dashboard figures reconcile against the `transactions` ledger — the dashboard reads the ledger, it does not maintain a separate running total.

### 6.7 Admin affiliate management (FR-AFF-16x)
- **FR-AFF-160** An admin can list all affiliates (offset-paginated, per platform pagination convention), view a single affiliate's detail, referrals, and attribution activity, and change status (`pending`→`active`, `suspend`, `reinstate`).
- **FR-AFF-161** An admin can review and approve/reject an organization's affiliate enrollment (FR-FAITH-6 review step) and override an affiliate's revenue-share rate.
- **FR-AFF-162** These capabilities realise the existing `SowingMeAdminApi` stub routes `GET /affiliates` and `GET /affiliate/{id}` (brief §3.1) — this surface implements the controllers those routes already call and extends them with the write actions above.

### 6.8 Church / organization revenue-share (FR-AFF-17x / FR-FAITH-6)
- **FR-AFF-170** An `organization` can be an affiliate exactly like a `creator` (FR-AFF-102/103); no separate data model — `affiliates.organization_id` is the seam.
- **FR-AFF-171** A church's referral revenue share accrues to the organization's own balance/payout, consolidating with the org's other income per platform FR-ORG-2 (consolidated giving & payouts) once that surface lands; until then it pays out via the org's own `payout_account` like any affiliate owner.
- **FR-AFF-172** Church affiliate enrollment is admin-reviewable (FR-AFF-161), consistent with the platform's values-aligned trust posture (FR-FAITH-1) for an entity representing a ministry, not an individual.

## 7. Non-functional requirements

- **NFR-1 Correctness of money.** Revenue-share calculation and payout must be idempotent and reconcilable 1:1 against `transactions` rows; no revenue-share amount is ever computed or stored outside the ledger (platform NFR-STD, ADR-004).
- **NFR-2 Attribution integrity.** Attribution cookies are first-party, `SameSite=Lax`, `Secure`; click tokens are unguessable; `attribution_logs` writes are append-only (no update/delete of historical clicks).
- **NFR-3 Privacy.** `attribution_logs` stores IP in hashed form and does not retain more visitor detail than needed to resolve attribution (platform NFR-PRIV).
- **NFR-4 Performance.** A referral-link visit's attribution write must not add perceptible latency to the redirect (fire-and-continue; the log write is not on the critical path of the page it lands on).
- **NFR-5 Security.** Admin rate-override and status-change actions are role + ownership gated; revenue-share `fee`/payout ledger writes happen server-side only, never client-supplied.
- **NFR-6 Standards.** PHP follows DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner (`bin/ubix migrate:*`); JS follows `complete-js-guide.md`.
- **NFR-7 Extensibility.** Adding sub-affiliates, paid-ad integrations, or a public affiliate marketplace later must not require altering the ledger spine or the auth model (platform NFR-EXT).

## 8. External interfaces (summary — detail in technical-spec)

- **Affiliate dashboard** (SvelteKit, `SowingMeJs` — `/affiliates`, `/affiliates/banners`): replaces the current stub pages (brief §3.1) with real referral-code/link, banner, referral-list, and earnings views.
- **`SowingMeApi`**: affiliate self-enrollment, referral-link/banner listing, dashboard reads, the redirect/attribution endpoint.
- **`SowingMeAdminApi`**: affiliate list/detail/status/rate management, org affiliate review — realises the existing `GET /affiliates` / `GET /affiliate/{id}` stubs.
- **`SowingMeAdminJs`**: admin affiliate list/detail screens (currently a stub route per brief §3.1).

## 9. Constraints & assumptions

- Reuses existing entities that must exist first: `creators`, `organizations` (schema seam reserved at M1 per platform ADR-007; full org surface may still be landing in parallel), `transactions`, `payout_accounts`, `users`.
- `AttributionLog` exists today as a neptune carry-over model (`php/Ubix/Model/AttributionLog.php`) shaped for a different domain (media-partner "MP code" attribution) — this surface's migration **repurposes** the `attribution_logs` table and its DataType/DTO layer to the shape in §2 of `technical-spec.md`, it does not keep the old columns (brief §3.4/§5, same treatment as `Transaction`/`BillingTransaction` in the payments surface).
- The `AffiliateStatusEnum`/`AffiliateRateTypeEnum`/`AffiliateSiteTypeEnum` DataType wrappers exist as neptune carry-overs (`php/Ubix/DataType/Enum/Affiliate/`) but their backing `Ubix\Enum\Affiliate\*` enum classes do not exist yet — this surface creates them with values matching §6.1/§6.5 (`pending`/`active`/`suspended`/`inactive`; `percentage`/`flat`).
- The `SowingMeAdminApi` route file (`app/SowingMeAdminApi/src/Routes.php`) already wires `GET /affiliates` and `GET /affiliate/{affiliateId}` to `Ubix\Controller\InternalAdminApi\AffiliateController`, which does not exist yet in `php/Ubix/Controller/` — this surface creates it.
- No card data touches this surface; revenue-share money movement is entirely ledger rows, reusing the `payments`/`payouts` surfaces' Stripe integration.

## 10. Acceptance criteria (surface DoD)

1. A creator enrolls as an affiliate, gets a referral link, and a visitor who clicks it and later subscribes produces one `referrals` row crediting that affiliate.
2. A church/organization enrolls as an affiliate, is approved by an admin, refers a creator sign-up and a supporter tithe, and both produce correctly-typed `referrals` rows (FR-AFF-131).
3. A referred subscription charge produces a revenue-share `fee` row in `transactions` and an accrual reflected in the affiliate's dashboard; the next scheduled payout run includes it in the affiliate owner's payout.
4. A refund on a referred transaction reverses the revenue-share accrual.
5. An admin can list affiliates, view one's referrals/attribution activity, and suspend it — realising the existing stub routes.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| Q1 | Attribution model at launch: last-touch only, or ship first-touch toggle too? | Last-touch only; first-touch is a later config value, schema already supports it | FR-AFF-123 |
| Q2 | Default attribution window length? | 30 days | FR-AFF-122 |
| Q3 | Auto-approve creator affiliate enrollment, or admin review for everyone? | Auto-approve creators; admin review required for organizations (FR-FAITH-6) | FR-AFF-101/102/161 |
| Q4 | Default revenue-share rate and whether it varies creator vs. church | Single platform default; admin can override per affiliate | FR-AFF-104 |
| Q5 | Does `organizations` land before or alongside this surface? | Reserve `affiliates.organization_id` now; church affiliate flows block on the `organisations` surface (roadmap M3-04) reaching a usable `organizations` table | FR-AFF-102/170 |

## 12. Traceability

Each FR maps to endpoints/components in [`technical-spec.md`](technical-spec.md) §"Requirement traceability" and to roadmap row **M3-01**. FR-AFF-17x additionally realises platform **FR-FAITH-6**. Changes to any FR update the traceability table and re-version the companion docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
