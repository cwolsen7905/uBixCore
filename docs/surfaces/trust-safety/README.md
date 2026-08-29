# Surface: Trust & Safety

The faith-aligned content policy and moderation system for Sowing.me: a published content policy and ToS/privacy, a report-content/creator flow, a moderation queue and actions (hide/remove/suspend), the `reports` / `moderation_actions` / `audit_logs` tables, age/sensitivity flags that carry through to VOD, and content-policy enforcement. Realises platform **FR-TRUST** and the faith-native **FR-FAITH-1**. Milestone **M2/M3**.

This is not a generic Patreon moderation bolt-on — it is the values-aligned trust posture the platform SRS names as a first-class differentiator (platform SRS §1: "the trust & safety posture is values-aligned by design"). The content policy is explicitly grounded in the platform's faith identity, not a denomination-neutral catch-all copied from a secular creator platform.

## Read order

1. [`srs.md`](srs.md) — requirements: **what & why**, numbered FRs/NFRs tied to platform FR-TRUST and FR-FAITH-1.
2. [`technical-spec.md`](technical-spec.md) — **how in code**: `reports`/`moderation_actions`/`audit_logs` schema, API, enforcement points, VOD flag propagation.

This surface inherits the **Platform ADS** ([`../../projects/sowing-me/platform/architecture.md`](../../projects/sowing-me/platform/architecture.md)) in full — the moderation subsystem is already named there (ADS §6: "reports → queue → actions → audit; content policy enforcement (faith-aligned)") and the audit architecture is platform-wide (ADS §5). It introduces no new system-design decision, so it has no `architecture.md` of its own.

## Status

Draft v0.1 (2026-08-27). **Build-ready** for the M2 base (published policy, report flow, queue, hide/remove/suspend, audit logging); M3 items (age/sensitivity flags carried to VOD, full doctrinal-breadth policy) depend on `content-posts`/`live-streaming` and a product decision (see PQ3 below).

## Companion docs

Platform: [`srs.md`](../../projects/sowing-me/platform/srs.md) (§4 Faith-native differentiators, §5.13) · [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) · [`architecture.md`](../../projects/sowing-me/platform/architecture.md)
Project: [`charter.md`](../../projects/sowing-me/charter.md) (S11 references moderation; charter §12/Q6 content policy) · [`brief.md`](../../projects/sowing-me/brief.md)
Related surfaces: [`admin-console`](../admin-console/README.md) (hosts the moderation-queue entry point and the suspend mechanism this surface invokes) · [`live-streaming`](../live-streaming/README.md) (FR-90..93: admin force-stop, reports, and age/sensitivity flags carrying to VOD are this surface's rules applied to a stream)

## Open product question

**PQ3** (platform SRS §8): content-policy **authorship and doctrinal breadth** — denominational neutrality vs. a specific doctrinal stance — is a **product/leadership decision, not an engineering one**. This surface specifies the mechanism (how a policy is published, versioned, and enforced) so engineering isn't blocked, but the policy's actual text and doctrinal posture are out of scope for this document and must be resolved before the M2 content-policy publish step ships to production.

## Keeping these in sync

A requirement change in `srs.md` updates the traceability table in `technical-spec.md` and re-versions both via their Document control tables, per the same convention as [`live-streaming`](../live-streaming/README.md) and [`authentication`](../authentication/README.md).
