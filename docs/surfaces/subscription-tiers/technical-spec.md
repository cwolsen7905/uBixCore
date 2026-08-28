# Subscription Tiers — Technical Specification

**Surface:** `subscription-tiers` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (shared layering/seams — this doc documents only deltas)

> **How in code.** Follows [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md).

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Tier domain | `php/Ubix/` (Model, Repository, DTOs, DataTypes, Controller, Service) | `tiers`, `tier_benefits` CRUD/ordering |
| Subscription domain | `php/Ubix/` | `subscriptions` schema + read services; **write access to `status`/provider fields is exposed only to the `payments` surface's Service**, not via a public controller route |
| Tier API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/TierController` | Creator tier management, public tier read |
| Subscription API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/SubscriptionController` | Own-subscriptions read, creator subscriber list |
| Entitlement contribution | `php/Ubix/Service/Entitlement/TierPrecedenceResolver` (consumed by the platform `EntitlementService`, ADR-008) | Tier-position comparison rule |
| Tier manager UI | `app/SowingMeJs/` under `/creator/dashboard/tiers` | Create/edit/reorder/archive tiers + benefits |
| Public tiers section | rendered inside `creator-profile`'s `/c/[slug]` | Reads `GET /creators/{slug}/tiers` |

## 2. Data model (new migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. FKs to existing `creators` (from `creator-profile`) and `users`.

### 2.1 `tiers`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` | |
| `name` | VARCHAR(80) | FR-101 |
| `description` | TEXT NULL | |
| `price_amount` | INT | minor units (FR-104) |
| `price_currency` | CHAR(3) | ISO 4217, e.g. `USD` |
| `billing_interval` | ENUM (`TierBillingIntervalEnum`: `month`,`year`) | |
| `position` | SMALLINT UNSIGNED | UNIQUE per `(creator_id, position)` (FR-105); `0` is reserved (implicit free tier — never a row) so real tiers start at `1` |
| `status` | ENUM (`TierStatusEnum`: `active`,`archived`) | FR-103 |

Indexes: UNIQUE `(creator_id, position)`; index on `(creator_id, status)` for public listing.

### 2.2 `tier_benefits`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `tier_id` | BIGINT FK → `tiers.id` | |
| `description` | VARCHAR(255) | free text (FR-201) |
| `position` | SMALLINT UNSIGNED | ordering within the tier |

### 2.3 `subscriptions`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `user_id` | BIGINT FK → `users.id` | |
| `creator_id` | BIGINT FK → `creators.id` | denormalised for the uniqueness constraint and fast lookup without a join through `tiers` |
| `tier_id` | BIGINT FK → `tiers.id` | the paid tier; never null for a stored row (free "tier" is not stored, FR-102) |
| `status` | ENUM (`SubscriptionStatusEnum`: `active`,`past_due`,`canceled`,`expired`) | written by `payments` (FR-301) |
| `provider_subscription_id` | VARCHAR(255) NULL | Stripe subscription id, written by `payments` |
| `provider_customer_id` | VARCHAR(255) NULL | Stripe customer id, written by `payments` |
| `current_period_end` | DATETIME NULL | drives downgrade/cancel semantics (FR-403) |
| `canceled_at` | DATETIME NULL | |

Indexes: **UNIQUE partial constraint** enforcing "at most one non-terminal row per `(user_id, creator_id)`" — implemented as a UNIQUE index on `(user_id, creator_id)` filtered to `status IN ('active','past_due')` at the application/service layer if the DB engine lacks partial unique indexes (MariaDB: use a generated column `active_key` that is `NULL` for terminal statuses and `(user_id, creator_id)` otherwise, with a UNIQUE index on `active_key`) — this is the FR-302 enforcement mechanism.

DataTypes: `php/Ubix/Enum/TierBillingIntervalEnum`, `TierStatusEnum`, `SubscriptionStatusEnum` (shared with platform TDS §3's enum list — reuse if already declared by `payments`; do not redeclare) + matching `DataType/Enum/*` wrappers. A `MinorUnitAmount` DataType (if not already introduced by `payments`) wraps `price_amount`.

## 3. API surface (`SowingMeApi`)

Payloads use the DataType/Payload validation system; responses are DTOs. Creator routes require session auth + ownership middleware; the `subscriptions`-mutating routes below are intentionally absent — see §3.3.

### 3.1 Creator — tier management
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/creator/tiers` | Create a tier (+ initial benefits) | FR-101, FR-104 |
| GET | `/creator/tiers` | List own tiers (incl. archived) | — |
| PATCH | `/creator/tiers/{id}` | Edit name/description/price/interval | FR-101 |
| POST | `/creator/tiers/reorder` | Reorder `{ tierId, position }[]`, transactional renumbering | FR-105 |
| PATCH | `/creator/tiers/{id}/status` | Archive/reactivate | FR-103 |
| PUT | `/creator/tiers/{id}/benefits` | Replace ordered benefit list | FR-201 |
| GET | `/creator/subscribers` | Subscriber list (offset-paginated, per `pagination.md`) | FR-303 |

### 3.2 Public / supporter
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/creators/{slug}/tiers` | Public tier list (active only): name, price, benefits, position — includes a synthetic `{ position: 0, name: "Free" }` entry the API constructs, not stores | FR-102, consumed by `creator-profile` |
| GET | `/me/subscriptions` | Supporter's own subscription rows (all creators) | FR-303 |

### 3.3 Deliberately not here
No `POST /subscriptions` or `PATCH /subscriptions/{id}` route exists in this surface. Creating/mutating a `subscriptions` row's `status` and provider fields is exclusively the `payments` surface's Service, invoked from its Checkout-session-complete and Billing-webhook handlers. This surface's `SubscriptionRepository` exposes a `write()` method callable only by DI-injected services in the `payments` namespace (enforced by code review / architecture convention, not a runtime check) — documented here so a future contributor doesn't add a duplicate write path.

## 4. Upgrade/downgrade flow (sequence, informative)

```
Supporter picks a new tier on the public page
  → payments surface starts a Stripe Billing plan change (proration per Stripe's rules)
  → Stripe webhook confirms
  → payments' Service calls SubscriptionRepository::write():
       upgrade:   tier_id = new tier immediately, status stays active
       downgrade: tier_id stays current until current_period_end,
                  a scheduled change is recorded (payments-owned scheduling detail)
                  and applied at renewal
  → EntitlementService reads the *current* tier_id's position at every check (§5) —
    no separate cache to invalidate, so the upgrade is visible immediately by construction
```

## 5. Entitlement resolution — this surface's contribution

`TierPrecedenceResolver::resolve(userId, creatorId): int` returns the caller's current tier position:
- No active/`past_due`-within-grace `subscriptions` row for `creatorId` → `0` (implicit free tier, FR-502).
- Active row → that row's `tiers.position`.

The platform `EntitlementService::resolve(user, resource)` (ADR-008) calls this resolver and compares the returned position against the resource's required position:
- `visibility=public` → always allowed (no call needed).
- `visibility=subscribers` → allowed if resolved position `≥ 1`.
- `visibility=tier` (with `min_tier_id`) → allowed if resolved position `≥` `tiers.position` for `min_tier_id`.

This mirrors the identical rule already documented in `docs/surfaces/live-streaming/technical-spec.md` §8, so `content-posts` and `live-streaming` consume the same resolver without inventing a second gating mechanism (SRS FR-501, FR-504).

## 6. Frontend components

- `/creator/dashboard/tiers` (SvelteKit): tier list with drag-to-reorder (calls `POST /creator/tiers/reorder`), create/edit modal, benefits editor, archive toggle.
- Tiers grid inside `creator-profile`'s `/c/[slug]` (shared component, e.g. `js/Ubix/TierCard.svelte`): renders the free tier synthetic entry plus paid tiers in position order, each with a subscribe CTA that hands off to `payments`' checkout flow.
- Supporter subscription view (under existing `settings` route): reads `/me/subscriptions`; upgrade/downgrade/cancel buttons deep-link into `payments`' billing-portal flow rather than posting to this surface.

## 7. External-seam usage

None directly. This surface never calls `PaymentProviderInterface` — all provider interaction is `payments`'. It does register its `SubscriptionRepository::write()` method for DI injection so `payments`' Service can call it without a cross-surface HTTP hop.

## 8. Testing

- **Unit:** tier CRUD validation (price/currency, position uniqueness), reorder transactional renumbering, benefits ordering, `TierPrecedenceResolver` matrix (no subscription / active at various positions / past_due / canceled / expired × required visibility), the free-tier synthetic entry construction.
- **Integration:** the `(user_id, creator_id)` non-terminal uniqueness constraint (attempt two concurrent active rows, expect rejection); subscriber-list pagination against `pagination.md`'s offset contract; `SubscriptionRepository::write()` invoked from a stubbed `payments` service.
- **E2E (staging):** creator creates two ordered tiers → public page shows Free + both in order → (once `payments` exists) supporter subscribes to the lower tier → upgrades → `EntitlementService` grants the higher tier's content immediately → downgrades → retains access until `current_period_end`.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite.

## 9. Requirement traceability

| FR | Realised by |
|---|---|
| FR-101/104/105 | `tiers` table, `TierController`/`Service`, reorder endpoint |
| FR-102 | Synthetic free-tier entry in `GET /creators/{slug}/tiers` |
| FR-103 | `PATCH /creator/tiers/{id}/status` |
| FR-201/202 | `tier_benefits` table, `PUT /creator/tiers/{id}/benefits` |
| FR-301/302 | `subscriptions` table, non-terminal uniqueness constraint |
| FR-303/304 | `GET /me/subscriptions`, `GET /creator/subscribers`; absence of a public write route |
| FR-401/402/403/404 | §4 upgrade/downgrade flow, `current_period_end` field |
| FR-501/502/503/504 | `TierPrecedenceResolver`, §5 |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
