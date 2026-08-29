# Surface: Payments

Subscription checkout, tips & gifts, the `transactions` ledger, Stripe webhook ingestion, commission fee rows, and dunning/refund/dispute handling for Sowing.me. Realises platform **FR-PAY**. Milestone **M2** — build-ready.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-PAY.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model deltas, API, `PaymentProviderInterface`, webhook controller.
3. [`architecture.md`](architecture.md) — **how as a system**: Stripe integration topology, Checkout/Billing flows, the webhook ingestion + idempotency + ledger-reconciliation pipeline, the PCI boundary, refund/dispute handling. Adds only payment-specific system design on top of the platform ADS — it does not restate [`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md) §6.

## Status

Draft v0.1 (2026-08-27). **Build-ready for M2.** No Stripe dependency exists in `composer.json` yet; `BillingTransaction` repository and a `Transaction` model exist as neptune carry-overs and are **repurposed**, not kept as-is (see [`brief.md`](../../projects/sowing-me/brief.md) §3.2/§3.4). The `transactions` ledger table itself does not exist yet — it lands via `bin/ubix migrate:*` as part of this surface.

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§7 payments & ledger design) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (§6 payments subsystem, ADR-003, ADR-004)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S7, Q1 Stripe, Q3 10% commission)
Related surface: [`payouts`](../payouts/README.md) (Stripe Connect Express, `payout_accounts`, scheduled payouts — consumes this surface's ledger and `fee` rows) · in-stream tips originate in [`live-streaming`](../live-streaming/README.md) and land through this surface's tip flow
Standards: [`database.md`](../../standards/database.md) · [`migrations.md`](../../standards/migrations.md) · [`sensitive-data-access.md`](../../standards/sensitive-data-access.md)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md`, any system impact in `architecture.md`, and re-versions all three via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md).
