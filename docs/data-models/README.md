# Data Models — cross-cutting

Entity shapes that **span multiple surfaces** live here; single-surface tables live in that surface's `technical-spec.md`. This is the shared ERD reference the platform TDS §3 and the charter point to.

- [`core-entities.md`](core-entities.md) — the identity → creator/org → tiers → subscriptions → posts spine, the generic `transactions` ledger, and the owner-polymorphism (creator/organization) shape. Satisfies roadmap **M0-02**.

Rule: every table is created via `bin/ubix migrate:*` ([`../standards/migrations.md`](../standards/migrations.md)); money is stored in minor units + currency; new post types / ledger types are additive enum values, not new tables (platform ADS §9 extensibility contract).
