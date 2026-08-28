# Sowing.me — Working Status

Rolling session journal. Newest block first. Decisions recorded here the session they're made; roadmap rows flip in the same commit.

## 2026-08-27 — Project docs bootstrapped

### Work this session
- Ported `docs/architecture/` + `docs/standards/` from project-neptune (parity commit).
- Inventoried the actual Sowing.me build state (see `brief.md` §3): auth/register/confirm-email API, 2 tables, Latte marketing site, SvelteKit shell pages, admin API stubs. No payments, no content model, no creator entity.
- Wrote `README.md`, `brief.md`, `charter.md` (v0.1), `mvp-roadmap.md`, this file. Added the project row to `docs/projects/README.md`.

### Decisions
- Pitch deck is marketing, not build status; the roadmap is authoritative.
- Follow neptune's project convention (Tier 2 full tracking) from day one.
- Defaults adopted pending answers (charter §12): Stripe; separate `creators` entity; 10% commission; images/audio upload + video embed at MVP; keep Latte marketing site.

### Next
- M0-01 (delete neptune leftovers) and M0-02 (core-entities data model) are the first two rows — both unblock everything after.
- Set milestone dates after M0 to calibrate velocity.
