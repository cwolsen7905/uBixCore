# Surface: Payouts

Stripe Connect Express onboarding, `payout_accounts`, platform commission, scheduled payouts, balance/pending views, and tax documentation for Sowing.me creators and organizations. Realises platform **FR-FIN**. Milestone **M2** — build-ready.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-FIN.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model deltas, API, Connect Express onboarding, scheduled payout job.

This surface inherits the **Platform ADS** ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md) §6 payments subsystem, ADR-003, ADR-007) and the **Payments ADS** ([`../payments/architecture.md`](../payments/architecture.md) — PCI boundary, ledger/webhook pipeline) in full. It introduces no system-design decision either doesn't already cover — Connect Express is the same Stripe integration, KYC stays at Stripe, and payouts are just another `transactions` type flowing through the same ledger — so it has no `architecture.md` of its own.

## Status

Draft v0.1 (2026-08-27). **Build-ready for M2.** No Stripe dependency exists yet (added alongside [`payments`](../payments/README.md)); `payout_accounts` does not exist and lands via `bin/ubix migrate:*` as part of this surface.

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) (§5.10 FR-FIN) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (ADR-003, ADR-007)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S10, Q3 10% commission)
Related surface: [`payments`](../payments/README.md) (FR-PAY — the `transactions` ledger and commission `fee` rows this surface pays out against; its architecture.md is this surface's system design too)
Standards: [`database.md`](../../standards/database.md) · [`migrations.md`](../../standards/migrations.md) · [`sensitive-data-access.md`](../../standards/sensitive-data-access.md)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md) and [`authentication`](../authentication/README.md).
