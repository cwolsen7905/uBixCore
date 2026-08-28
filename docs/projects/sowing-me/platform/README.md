# Sowing.me — Platform documentation (SRS / TDS / ADS)

The **whole-product** foundation for Sowing.me — a Patreon-class membership & monetisation platform **rebuilt for Christian content creators**, extended with faith-native domains (church/organization accounts, giving & tithing, prayer, devotional content). This trio is the altitude above the per-surface docs in [`docs/surfaces/`](../../../surfaces/); every surface inherits and must not contradict it.

## Read order
1. [`srs.md`](srs.md) — **Platform SRS**: full product breadth — personas, every capability domain (Patreon parity + our extensions) tagged by milestone, faith-native differentiators, platform NFRs.
2. [`technical-spec.md`](technical-spec.md) — **Platform TDS**: the engineering handbook every surface inherits — layering, the cross-cutting domain/ledger model, API conventions, external seams, the per-surface doc template.
3. [`architecture.md`](architecture.md) — **Platform ADS**: system/container views, data & security architecture, subsystems, the **extensibility contract** (why post-MVP features won't force rework), and the ADR index.

## Relationship to the other docs
- [`../charter.md`](../charter.md) owns **scope, phases, governance**; this trio owns the **engineering specification** of that scope.
- [`../brief.md`](../brief.md) is the orientation/inventory; [`../mvp-roadmap.md`](../mvp-roadmap.md) is the work matrix; [`../status.md`](../status.md) is the journal.
- Surfaces (e.g. [`live-streaming`](../../../surfaces/live-streaming/README.md)) drill down beneath this trio.

## Status
Draft v0.1 (2026-08-27). MVP spine (M1+M2) is "a supporter can pay a creator"; the data & architecture **seams for all post-MVP domains exist from M0** (ADS §9). See `ubixcore/CLAUDE.md` → *Product & surface documentation* for the sync rules.
