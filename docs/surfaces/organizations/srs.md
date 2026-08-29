# Organizations — Software Requirements Specification (SRS)

**Surface:** `organizations` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3+ (post-MVP) · **Prerequisites:** `creator-profile` (S3), `subscription-tiers` (S4), `payments` (S7), `payouts` (S10), `admin-console` (S11) — all unbuilt
**Companion docs:** [`technical-spec.md`](technical-spec.md) · Platform [`srs.md`](../../projects/sowing-me/platform/srs.md) §5.19 (FR-ORG) / §4 (FR-FAITH-2) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (this surface has no independent ADS — see [`README.md`](README.md))

> Authored against `docs/standards/web-development-delivery-framework.md` (Charter → **SRS** → SDD). This surface expands **Platform FR-ORG** (Platform SRS §5.19) and **FR-FAITH-2** (Platform SRS §4) — it does not restate platform-wide requirements (auth, ledger, entitlement); see the Platform SRS/TDS for those.

## 1. Purpose

Let a church or ministry run a **shared organizational account** on Sowing.me: one page, one identity, contributions from multiple creator-contributors (pastors, worship leaders, ministry staff), consolidated giving and payouts, and org-level analytics. This is the platform's flagship faith-native differentiator — the capability row with no Patreon equivalent (Platform SRS §2: "— (no equivalent) → Church/organization accounts").

## 2. Scope

**In scope:** `organizations` entity + page, `org_members` (roles), inviting/removing creator-contributors, ministry verification during onboarding, consolidated giving/payouts view, org-level analytics, admin oversight of orgs.

**Out of scope (this surface):** the giving/tithing transaction flow itself (see [`../giving-tithing/README.md`](../giving-tithing/README.md)); prayer walls scoped to an org page (see [`../prayer-requests/README.md`](../prayer-requests/README.md)); multi-org hierarchies (denominations owning multiple churches — candidate for a later revision); org-to-org transfers.

## 3. Context — why this exists

| Gap in generic creator platforms | Our stance |
|---|---|
| A creator account is always one person | **Extend.** A church is not one creator; `organizations` is a first-class owner alongside `creators` (FR-ORG-1). |
| Payouts go to one payee | **Extend.** An org's payout is consolidated across its contributors' giving into one `payout_accounts` row per org (FR-ORG-2). |
| No verification step for a ministry entity | **Add.** Org onboarding includes a ministry-verification step (FR-ORG-4) before an org can receive giving. |

## 4. Definitions

| Term | Meaning |
|---|---|
| **Organization** | The `organizations` entity — a church/ministry account with its own page, owning tiers/posts/payouts either directly or through its contributors, per the owner-polymorphism rule (ADR-007). |
| **Org member** | A row in `org_members`: a user linked to an organization with a role (`org_admin`, `contributor`, `staff`). |
| **Creator-contributor** | A `creators` row associated with an organization (per ADR-007's owner shape) — publishes under the org's identity, may also retain an independent creator page. |
| **Ministry verification** | An onboarding step confirming the organization is a genuine ministry/church entity before it can receive consolidated giving/payouts. |
| **Linked-user model** | Each org member authenticates as their own `users` row and is *linked* to the organization via `org_members`, rather than the org sharing one login (see PQ1, §9). |

## 5. Personas & primary user stories

- **Church / organization admin** (Platform SRS §3). "As a church admin, I create our org's page, invite our worship leader and youth pastor as contributors, and see one consolidated giving total and payout — not four separate creator accounts."
- **Creator-contributor.** "As a worship leader on staff, I publish under my church's page using my own login; I don't need my own separate payout setup."
- **Supporter.** "As a supporter, I give to my church's page and see it as giving to the church, not to an individual staff member."
- **Platform admin.** "As an admin, I review a new org's ministry-verification submission before it can receive consolidated payouts, and can suspend an org like I can a creator."

## 6. Functional requirements

### 6.1 Organization entity & page (realises FR-ORG-1)
- **FR-10** An `organizations` row has a public page (`/o/{slug}`) with name, bio, logo/banner, denomination/category, and links — mirroring `creators`' page shape (Platform TDS §3).
- **FR-11** An org page lists its creator-contributors, its tiers/posts (if the org itself owns content per ADR-007), and a giving CTA.
- **FR-12** Slugs are unique across both `creators` and `organizations` (shared namespace) to keep `/c/{slug}` and `/o/{slug}` unambiguous.

### 6.2 Membership & roles (realises FR-ORG-1)
- **FR-20** `org_members` links a `users` row to an `organizations` row with a role: `org_admin` (manage org, invite/remove members, view consolidated finances), `contributor` (a `creators` row publishing under the org), `staff` (org-page access, no publishing rights).
- **FR-21** An org admin can invite a user by email; the invite creates a `pending` `org_members` row until accepted (linked-user model, PQ1).
- **FR-22** An org admin can change a member's role or remove a member; removing a `contributor` does not delete their `creators` row or its content — it detaches the org association (per ADR-007's owner shape).
- **FR-23** A user may be a member of more than one organization and may independently hold their own `creators` page.

### 6.3 Consolidated giving & payouts (realises FR-ORG-2)
- **FR-30** All giving/tithing (see [`../giving-tithing/`](../giving-tithing/README.md)) directed at the org, or at any contributor publishing under the org, rolls up into one `transactions` view scoped to the organization (no parallel ledger — Platform ADR-004).
- **FR-31** The org has one `payout_accounts` row (Stripe Connect Express, per Platform TDS §5.10/FR-FIN) receiving the consolidated payout; individual contributors do not receive separate payouts for org-attributed giving.
- **FR-32** The org-admin view shows a per-contributor breakdown of the consolidated total for internal accountability, without exposing it to supporters.

### 6.4 Org-level analytics (realises FR-ORG-2)
- **FR-40** The org dashboard shows aggregate giving, member (supporter) count across all contributors, and per-contributor performance — the org-scoped analogue of Platform FR-DASH.

### 6.5 Ministry verification & onboarding (realises FR-ORG (Platform SRS FR-ONB-3))
- **FR-50** Creating an organization starts a `pending` verification state; the org cannot receive giving/payouts until verified.
- **FR-51** Verification submission collects ministry-identifying information (legal/ministry name, an EIN/registration number or equivalent where applicable, a contact); an admin reviews and approves/rejects via `admin-console`.
- **FR-52** A rejected or unverified org's page is still visible (informational) but its giving CTA is disabled.

### 6.6 Admin oversight (realises FR-ORG, platform-wide FR-ADMIN)
- **FR-60** An admin can list organizations, view/approve/reject verification submissions, and suspend/reinstate an org exactly as they can a creator (Platform FR-ADMIN-1).
- **FR-61** Suspending an organization suspends its page and giving CTA but does not delete member creators' independent pages.

## 7. Non-functional requirements

- **NFR-1 Consistency.** Org-scoped ledger views must never diverge from the underlying `transactions` rows — no denormalised total may be treated as authoritative; it is always derivable from the ledger (Platform ADR-004).
- **NFR-2 Security.** Role checks (`org_admin` vs `contributor` vs `staff`) are enforced server-side on every org-management route; no client-side role gating (Platform NFR-SEC).
- **NFR-3 Privacy.** Per-contributor financial breakdowns are org-admin-only, never supporter-facing (Platform NFR-PRIV).
- **NFR-4 Extensibility.** This surface must implement the ADR-007 promotion (org-FK → `owner_type`/`owner_id`) without a breaking migration for existing `creators`-owned tiers/posts/payout rows — see `technical-spec.md` §2 for the backfill plan (Platform NFR-EXT).
- **NFR-5 Standards.** DataType/Payload/DTO/Repository (`complete-php-guide.md`), PHPStan max, custom sniffs, strict PHPUnit; every table via the migration runner; JS per `complete-js-guide.md`.

## 8. External interfaces (summary — detail in technical-spec)

- **Org page & admin dashboard** (SvelteKit, `SowingMeJs`): page, member management, verification submission, consolidated giving/payout view.
- **`SowingMeApi`**: org CRUD, member invite/role/remove, verification submission.
- **`SowingMeAdminApi`**: org list, verification review/approve/reject, suspend/reinstate.

## 9. Constraints & assumptions

- Reuses existing entities that must exist first: `creators`, `tiers`, `posts`, `transactions`, `payout_accounts` (Platform TDS §3).
- **PQ1 (Platform SRS §8): shared-login vs linked-user model.** Default adopted here: **linked-user** — each org member is their own `users` row, linked via `org_members`; there is no shared org login. This keeps auth (Platform ADR-002) untouched and lets `EntitlementService`/ownership checks resolve per-user role rather than per-shared-credential. Reopen only if product finds onboarding friction unacceptable for smaller churches.
- No card data touches our systems; consolidated payouts go through the same Stripe Connect Express flow as an individual creator (Platform FR-FIN-1).

## 10. Acceptance criteria (surface DoD)

1. An org admin creates an organization, submits ministry verification, and an admin approves it via `admin-console`.
2. The org admin invites two users as contributors; both publish content under the org page.
3. A supporter gives to the org; the resulting `transactions` row is attributed to the organization and appears in the org's consolidated giving view, not to an individual contributor's personal payout.
4. The org receives one consolidated payout via Stripe Connect Express.
5. An admin suspends the org; its page and giving CTA go inactive while contributors' independent creator pages remain live.
6. Standards gates green (`phpunit`, `phpstan`, `phpcs`, JS suite); all schema via migrations.

## 11. Open questions

| # | Question | Default if unanswered | Blocks |
|---|---|---|---|
| PQ1 | Shared-login vs linked-user model for org accounts (Platform SRS §8) | Linked-user (adopted, §9) | onboarding UX, auth |
| Q1 | Can a `creators` row belong to more than one organization simultaneously? | No — one org per creator at a time; independent creator page unaffected | `org_members`/ownership model |
| Q2 | What counts as sufficient "ministry verification" (legal registration vs self-attestation)? | Self-attestation + admin manual review at launch; formal registration lookup deferred | FR-50/51 |

## 12. Traceability

Each FR maps to endpoints/tables in [`technical-spec.md`](technical-spec.md) §"Requirement traceability", and to Platform SRS **FR-ORG-1/2** and **FR-FAITH-2**. Changes to any FR update the traceability table and re-version both docs.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial SRS. |
