# Core Entities — cross-cutting ERD

**Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Source of truth for names:** [`../projects/sowing-me/platform/technical-spec.md`](../projects/sowing-me/platform/technical-spec.md) §3. This doc is the consolidated ERD the surfaces share; it must not diverge from the platform TDS. Satisfies roadmap **M0-02**.

> Purpose: give every surface one picture of the spine so no surface invents a parallel entity. The concrete columns for a table live in its **owning** surface's TDS (linked below); this doc fixes the relationships, the ledger, and the polymorphism decisions.

## 1. Entity map

```
users ─1:1─ creators ─N:1─ organizations           (identity → creator/ministry)
  │            │  │  └────────────┐
  │            │  └─1:N─ tiers ─N:1┘ (tier belongs to owner: creator|org)
  │            │           │
  │            └─1:N─ posts ─1:N─ post_media
  │                     └─N:1─ collections
  │                     └ visibility{public|subscribers|tier}, min_tier_id, type
  │
users ─N:M(via subscriptions)─ tiers   (supporter subscribes; status, provider ids)
  │
transactions  ── the single money LEDGER (see §3)
payout_accounts ─1:1─ creator|organization  (Stripe Connect)
affiliates ─ referrals ─ attribution_logs
notifications / notification_prefs · conversations / messages · comments / reactions
prayer_requests / prayers · campaigns / giving_plans (giving)
reports / moderation_actions / audit_logs · live_streams (+ live_* tables)
```

## 2. Owner polymorphism (ADR-007)

Pages, tiers, posts, and payout accounts belong to a **creator** or an **organization**.
- **M1 (no orgs yet):** a plain `creator_id` FK; `creators.organization_id` reserved nullable.
- **M3+ (organizations surface):** promote to `owner_type` (`creator`|`organization`) + `owner_id`, via an additive, backfilled migration. The [`organizations`](../surfaces/organizations/README.md) TDS documents that migration.
- Surfaces that touch owned resources must read the owner through the same accessor so the M1→M3 promotion is transparent. (Known draft nuance: `payouts` (M2) already references `owner_type`; reconcile to the M1 `creator_id`-first form when it is built, or pull the promotion earlier.)

## 3. The transactions ledger (ADR-004) — one table for all money

Every money event is a `transactions` row; there is **no** other money table.

| Field | Notes |
|---|---|
| `type` | `TransactionTypeEnum`: `subscription` · `tip` · `gift` · `tithe` · `fee` · `payout` · `refund` |
| `amount_minor` / `currency` | integer minor units + ISO code — never floats |
| `owner` (creator/org) · `user_id` (payer) | who earns / who paid |
| `related_type`/`related_id` | post, stream, campaign, subscription, referral… |
| `provider_ref` / `provider_event_id` | Stripe charge/transfer + webhook idempotency |
| `status`, `created_at` | |

Consequences the surfaces rely on:
- **Commission** is a derived `fee` row written at charge time (payments), only *read* by payouts.
- **Giving & tithing** are `gift`/`tithe` rows — the domain ships with **no schema change** (data, not tables).
- **Affiliate revenue-share** is `fee`/`payout` rows.
- **Earnings/statements** are ledger queries, not stored aggregates.

## 4. Table → owning surface (where columns are defined)

| Entity | Owning surface |
|---|---|
| `users`, sessions, roles, password reset | [`authentication`](../surfaces/authentication/README.md) / [`registration`](../surfaces/registration/README.md) |
| `creators`, slug history | [`creator-profile`](../surfaces/creator-profile/README.md) |
| `organizations`, `org_members` | [`organizations`](../surfaces/organizations/README.md) |
| `tiers`, benefits, `subscriptions` (schema) | [`subscription-tiers`](../surfaces/subscription-tiers/README.md) |
| `posts`, `post_media`, `collections` | [`content-posts`](../surfaces/content-posts/README.md) |
| media objects / signed URLs | [`media-storage`](../surfaces/media-storage/README.md) |
| `transactions`, webhooks, subscription lifecycle | [`payments`](../surfaces/payments/README.md) |
| `payout_accounts`, payout runs | [`payouts`](../surfaces/payouts/README.md) |
| categories / denominations / faith topics | [`explore`](../surfaces/explore/README.md) |
| `affiliates`, `referrals`, `attribution_logs` | [`affiliates`](../surfaces/affiliates/README.md) |
| `notifications`, `notification_prefs` | [`notifications`](../surfaces/notifications/README.md) |
| `conversations`, `messages` | [`messaging`](../surfaces/messaging/README.md) |
| `comments`, `reactions` | [`comments-community`](../surfaces/comments-community/README.md) |
| `campaigns`, `giving_plans` | [`giving-tithing`](../surfaces/giving-tithing/README.md) |
| `prayer_requests`, `prayers` | [`prayer-requests`](../surfaces/prayer-requests/README.md) |
| `reports`, `moderation_actions`, `audit_logs` | [`trust-safety`](../surfaces/trust-safety/README.md) |
| `live_streams` (+ `live_*`) | [`live-streaming`](../surfaces/live-streaming/README.md) |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial cross-cutting ERD — spine, owner polymorphism, ledger, table→surface map. |
