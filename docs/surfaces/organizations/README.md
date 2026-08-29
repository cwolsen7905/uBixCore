# Organizations — Surface Overview (README)

**Surface:** `organizations` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3+ (post-MVP, faith-native) · **Prerequisites (unbuilt):** `creator-profile` (S3), `subscription-tiers` (S4), `payments` (S7), `payouts` (S10), `admin-console` (S11)

Church / ministry accounts for Sowing.me: an `organizations` entity that owns a page, has multiple creator-contributors via `org_members`, and receives consolidated giving and payouts. This is the platform's first faith-native differentiator — realises Platform SRS **FR-ORG** ([`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.19) and **FR-FAITH-2** (§4).

## Read order

1. [`srs.md`](srs.md) — requirements: what & why, numbered FRs/NFRs, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — how in code: data model (including the ADR-007 owner-polymorphism promotion this surface triggers), API, service/entitlement changes.

## No architecture.md

This surface has no independent ADS. It inherits the Platform ADS in full — [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md), specifically **ADR-007** (owner polymorphism: org-FK at M1, promoted to `owner_type`/`owner_id` when orgs land — this surface is that promotion) and the extensibility contract row for "Church/organization accounts" (§9). Any system-level decision beyond what ADR-007 already settles must be raised as a new ADR against the Platform ADS, not documented here.

## Status

Draft v0.1 (2026-08-27). **Everything this surface needs is unbuilt**: `creators`, `tiers`, `posts`, `transactions`, and `payout_accounts` all exist only as reserved schema in the Platform TDS (§3) — none has shipped migrations yet. This surface cannot start until `creator-profile`, `subscription-tiers`, and `payments`/`payouts` are real tables, because the owner-polymorphism promotion (TDS §3, ADR-007) rewrites how those tables attach to an owner. `admin-console` is a soft dependency for org moderation/verification review.

Open question **PQ1** (Platform SRS §8) — shared-login vs linked-user model for org accounts — is resolved with a default (linked-user) in `srs.md` §9, but remains open for product sign-off.

## Keeping these in sync

`srs.md` and `technical-spec.md` move together, and both cite the Platform SRS/TDS/ADS for anything shared rather than restating it. A requirement change here re-versions both docs' Document control tables. Roadmap status flips in the same commit as the code.
