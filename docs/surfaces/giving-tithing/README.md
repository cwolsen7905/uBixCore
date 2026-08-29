# Giving & Tithing — Surface Overview (README)

**Surface:** `giving-tithing` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Milestone:** M3+ (post-MVP, faith-native) · **Prerequisites (unbuilt):** `payments` (S7), `payouts` (S10), `organizations` ([`../organizations/README.md`](../organizations/README.md))

Recurring and one-off gifts/tithes to a creator, organization, or campaign — monetisation distinct from a membership subscription. Realises Platform SRS **FR-GIVE** ([`../../projects/sowing-me/platform/srs.md`](../../projects/sowing-me/platform/srs.md) §5.20) and **FR-FAITH-3** (§4).

## Read order

1. [`srs.md`](srs.md) — requirements: what & why, numbered FRs/NFRs, acceptance criteria, PQ2 (tithing legal/receipt distinction).
2. [`technical-spec.md`](technical-spec.md) — how in code: campaigns, giving plans, and how gifts/tithes land as rows in the **existing** `transactions` ledger (no parallel money table — Platform ADR-004).

## No architecture.md

This surface has no independent ADS. It inherits the Platform ADS — [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md), specifically **ADR-004** (generic `transactions` ledger already covers `gift`/`tithe`) and the extensibility contract row "Giving & tithing → generic `transactions` ledger already has `gift`/`tithe` types" (§9).

## Status

Draft v0.1 (2026-08-27). **Prerequisites unbuilt:** the `payments`/`payouts` surfaces (Stripe Checkout/Billing/Connect, the `transactions` ledger itself) have not shipped their first migration yet, and this surface's org/campaign attribution depends on [`../organizations/`](../organizations/README.md) for org-directed giving. Creator-directed one-off giving could, in principle, start as soon as `payments` lands even if `organizations` is still pending — campaign/org rows would simply be absent until then.

**Open question carried from the Platform SRS (PQ2):** whether tithing is legally/operationally distinct from a tip in our jurisdiction (receipts/statements). This is **explicitly unresolved** — see `srs.md` §9 — and is jurisdiction-dependent; do not build a jurisdiction-specific receipt format against this spec without product/legal sign-off.

## Keeping these in sync

`srs.md` and `technical-spec.md` move together and cite the Platform SRS/TDS/ADS for anything shared. A requirement change here re-versions both docs' Document control tables.
