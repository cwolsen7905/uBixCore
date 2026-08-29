# Surface: Live Streaming

Creator live video for Sowing.me — browser (WHIP) + encoder ingest, tier-gated playback, restream/simulcast, live chat & tips, and automatic replay (VOD). Post-MVP (roadmap **M3-06**).

## Read order
1. [`srs.md`](srs.md) — requirements: **what & why**, Patreon-informed, numbered FRs/NFRs, acceptance criteria.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: data model, API, MediaMTX config, clients, chat, entitlement.
3. [`architecture.md`](architecture.md) — **how as a system**: topology, network/UDP model, sequences, capacity, failure modes, the MediaMTX decision + alternatives.

Higher-level orientation and phasing live in the project plan: [`../../projects/sowing-me/live-streaming-plan.md`](../../projects/sowing-me/live-streaming-plan.md).

## Status
Draft v0.1 (2026-08-27). **Prerequisites unbuilt:** `creator-profile`, `subscription-tiers`, `media-storage`, `notifications`. The only part startable today is **Phase 0** (media-server spike — see `architecture.md` §9), which has no app dependencies.

## Keeping these in sync
The three docs move together — see `ubixcore/CLAUDE.md` → *Surface documentation*. A requirement change in `srs.md` updates the traceability in `technical-spec.md`, any system impact in `architecture.md`, and re-versions all three via their Document control tables. Roadmap status flips in the same commit as the code.
