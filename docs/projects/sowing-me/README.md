# Sowing.me — Project

**Status:** Active — platform foundations exist (auth, registration, email confirmation, marketing site, SvelteKit shell); build-out planning started 2026-08-27. Full tracking (Tier 2: sliced work + open-ended duration).

Sowing.me is a creator-monetisation platform for faith-based content creators (Patreon-style subscriptions, tips, exclusive content, affiliate/referral programme). It is the first product built on uBix Core and the reason the `app/SowingMe*` apps exist.

## Read order

1. [`brief.md`](brief.md) — what Sowing.me is, what actually exists in the repo today, and where it's going. Start here.
2. [`charter.md`](charter.md) — scope, phases/milestones, architecture posture, success criteria, risks, open questions.
3. [`mvp-roadmap.md`](mvp-roadmap.md) — the work matrix. Every row carries its own status cell; this answers "what's left".
4. [`status.md`](status.md) — rolling session journal (dated blocks, newest first). Decisions get recorded here the session they're made.
5. [`platform/`](platform/README.md) — the **whole-product** engineering trio (Platform SRS / TDS / ADS): full Patreon-class + faith-native scope, the shared build patterns, and the system architecture every surface inherits. The foundation.
6. [`live-streaming-plan.md`](live-streaming-plan.md) — post-MVP plan for creator live streaming; drills into [`docs/surfaces/live-streaming/`](../../surfaces/live-streaming/README.md).

## Related

- [`docs/pitch-deck.md`](../../pitch-deck.md) — investor/positioning narrative. **Marketing intent, not engineering truth** — its "Completed" list is aspirational; the roadmap is authoritative for build status.
- [`docs/surfaces/`](../../surfaces/README.md) — **surface index**: all 21 capability surfaces (SRS + TDS each; ADS where warranted), grouped by milestone.
- [`docs/data-models/core-entities.md`](../../data-models/core-entities.md) — cross-cutting ERD (the spine + `transactions` ledger + owner polymorphism) shared by every surface.
- [`docs/projects/migrations/`](../migrations/README.md) — schema-migration runner this project depends on for every table it adds.
- [`docs/architecture/complete-php-guide.md`](../../architecture/complete-php-guide.md) · [`complete-js-guide.md`](../../architecture/complete-js-guide.md) — framework references the build must follow.
- [`docs/standards/`](../../README.md#standards--enforced-rules) — enforced rules (database, migrations, pagination, unit-testing, sensitive-data-access apply directly).
- `app/SowingMeJs/THEME_GUIDE.md` — frontend theming system.

## Conventions for this folder

- Surface-level requirements + technical specs go under `docs/surfaces/<slug>/` (`srs.md`, `technical-spec.md`) once a surface is being built — the roadmap links them.
- A status flip in the roadmap and the matching `status.md` entry land in the **same commit** as the code.
