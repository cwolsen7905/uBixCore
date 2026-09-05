# Migrations — Runner & Cutover

**Status:** Active — runner code-complete; CI bring-up in progress across tiers. The **live rollout state lives in [`cutover-runbook.md`](cutover-runbook.md) §8** — trust it over any per-doc status line.

The `bin/ubix migrate:*` runner, its CI integration, and the cutover from frozen `sql/<DB>.sql` baselines to migrations as the only forward schema source. The enforced conventions (filename format, `Destructive:`/`RequiresDBA:` headers, tracker table) live in [`docs/standards/migrations.md`](../../standards/migrations.md) — this folder is the project that built and is rolling out the machinery.

## Read in this order

1. **[`plan.md`](plan.md)** — runner implementation plan (all eight slices shipped 2026-05-07).
2. **[`cutover-runbook.md`](cutover-runbook.md)** — the operational runbook; **§8 is the live rollout state + pickup triggers.**
3. **[`test-db-prefix-plan.md`](test-db-prefix-plan.md)** — test-DB prefix + `migrate:diff --mode=replay` plan (draft pending review).
