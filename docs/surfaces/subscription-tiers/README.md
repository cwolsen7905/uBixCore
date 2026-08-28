# Surface: Subscription Tiers

Per-creator membership tiers — pricing, billing interval, benefits, ordering — the implicit free tier, and the `subscriptions` table whose tier ordering drives gating precedence for `EntitlementService`. Realises platform **FR-MEM**. Milestone **M1** — build-ready.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-MEM.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, entitlement-precedence contribution, frontend components.

No `architecture.md` for this surface — it inherits the platform ADS in full. Tier gating logic is a data shape + rule this surface contributes to the platform's single `EntitlementService` (ADR-008); it does not stand up a separate subsystem.

## Status

Draft v0.1 (2026-08-27). **Build-ready for M1.** `tiers`, `tier_benefits`, and `subscriptions` do not exist yet; they land via `bin/ubix migrate:*` as part of this surface. **This surface owns tier definition/management and the `subscriptions` table shape**; it does **not** own the checkout/billing action that creates or mutates a subscription's paid state — that belongs to the `payments` surface (Stripe Checkout/Billing), which writes into the `subscriptions` rows this surface defines. See `srs.md` §2 for the exact boundary.

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) (§5.4 FR-MEM) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§3 domain model, §6 entitlement, §12 per-surface template) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (ADR-004 ledger, ADR-008 EntitlementService)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S4)
Related surfaces: [`creator-profile`](../creator-profile/README.md) (tiers render on the public page) · [`payments`](../payments/README.md) (owns the subscribe/checkout action and the `transactions` ledger writes) · `content-posts` (visibility `tier`/`min_tier_id` consumes this surface's ordering; not yet authored) · `payouts` (not yet authored)
Standards: [`database.md`](../../standards/database.md) · [`migrations.md`](../../standards/migrations.md) · [`pagination.md`](../../standards/pagination.md)

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md).
