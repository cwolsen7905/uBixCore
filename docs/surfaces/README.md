# Sowing.me — Surface Index

Each **surface** is one user-facing capability slice, specified by a doc trio (`srs.md` = SRS, `technical-spec.md` = TDS, and `architecture.md` = ADS where the surface needs its own system design). Surfaces drill down beneath the whole-product foundation in [`docs/projects/sowing-me/platform/`](../projects/sowing-me/platform/README.md) and inherit its conventions, entity/enum names, and the extensibility contract. See `ubixcore/CLAUDE.md` → *Product & surface documentation* for the model and sync rules.

**All surfaces below are at v0.1 (SRS + TDS drafted 2026-08-27).** The roadmap tracks *build*, not doc-writing.

## By milestone

### M0/M1 — Foundation & "creator can publish"
| Surface | Parent FR | Own ADS? | Summary |
|---|---|---|---|
| [`authentication`](authentication/README.md) | FR-IAM | — | Sessions, lockout, password reset, role model; hardens existing auth |
| [`registration`](registration/README.md) | FR-ONB | — | Supporter sign-up + creator onboarding wizard; email confirmation exists |
| [`creator-profile`](creator-profile/README.md) | FR-PROF | — | `creators` entity, `/c/{slug}` page, slug history, org/payout stubs |
| [`subscription-tiers`](subscription-tiers/README.md) | FR-MEM | — | `tiers`/benefits, free tier, gating precedence (subscribe action → payments) |
| [`content-posts`](content-posts/README.md) | FR-CONT | — | `posts`/`post_media`/`collections`, visibility, drafts; faith types additive |
| [`media-storage`](media-storage/README.md) | FR-MED | **Yes** | Presigned upload, signed delivery, CDN; serves posts + live VOD |

### M2 — "Supporter can pay" (MVP / private beta)
| Surface | Parent FR | Own ADS? | Summary |
|---|---|---|---|
| [`payments`](payments/README.md) | FR-PAY | **Yes** | Stripe Checkout/Billing, webhooks, `transactions` ledger, tips/gifts |
| [`payouts`](payouts/README.md) | FR-FIN | — | Stripe Connect, commission (read from ledger), scheduled payouts, tax |
| [`supporter-feed`](supporter-feed/README.md) | FR-FEED | — | Aggregated subscribed-creator feed, subscription management |
| [`explore`](explore/README.md) | FR-DISC | — | Discovery, categories/denominations/faith-topics, creator search |
| [`creator-dashboard`](creator-dashboard/README.md) | FR-DASH | — | Earnings, subscribers, post/stream performance |
| [`admin-console`](admin-console/README.md) | FR-ADMIN | — | User/creator/org admin, ledger views, moderation queue entry |
| [`trust-safety`](trust-safety/README.md) | FR-TRUST, FR-FAITH-1 | — | Faith-aligned content policy, reports, moderation, audit |

### M3 — Growth & engagement
| Surface | Parent FR | Own ADS? | Summary |
|---|---|---|---|
| [`live-streaming`](live-streaming/README.md) | FR-LIVE | **Yes** | WHIP browser ingest → MediaMTX → tier-gated playback, restream, VOD |
| [`notifications`](notifications/README.md) | FR-NOTIF | — | Email + in-app fan-out, prefs, digests, dedup |
| [`comments-community`](comments-community/README.md) | FR-COMM | — | Comments/reactions, moderation; groups future |
| [`messaging`](messaging/README.md) | FR-MSG | — | Supporter↔creator DMs, tier broadcast, abuse controls |
| [`affiliates`](affiliates/README.md) | FR-AFF, FR-FAITH-6 | — | Referral links/banners, attribution, revenue-share (+ church share) |

### M3+ — Faith-native domains (the reason it's not generic Patreon)
| Surface | Parent FR | Own ADS? | Summary |
|---|---|---|---|
| [`organizations`](organizations/README.md) | FR-ORG, FR-FAITH-2 | — | Church/ministry accounts, multi-creator, consolidated giving/payouts |
| [`giving-tithing`](giving-tithing/README.md) | FR-GIVE, FR-FAITH-3 | — | Recurring/one-off gifts & tithes, campaigns, statements (ledger `gift`/`tithe`) |
| [`prayer-requests`](prayer-requests/README.md) | FR-PRAY, FR-FAITH-4 | — | Prayer requests & walls, visibility-scoped, pray/respond |

## Conventions
- A surface adds tables (via `bin/ubix migrate:*`), routes, UI, and enum values; it may **not** alter the ledger spine, auth model, media pipeline, or app topology (platform ADS §9). If it must, the platform ADS is revised first.
- Gating everywhere goes through the single `EntitlementService` (ADR-008); money everywhere is `transactions` ledger rows in minor units (ADR-004).
- Write a surface's SRS + TDS before its first migration (charter §7).
