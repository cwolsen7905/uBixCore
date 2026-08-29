# Affiliates — Technical Specification

**Surface:** `affiliates` · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (what/why) · [`README.md`](README.md) · platform [`technical-spec.md`](../../projects/sowing-me/platform/technical-spec.md) (§3 domain model, §7 ledger design, §12 per-surface template) · platform [`architecture.md`](../../projects/sowing-me/platform/architecture.md) (§4 data architecture, §9 extensibility contract — no surface-level `architecture.md` here, this surface inherits the platform ADS in full)

> **How in code.** This spec is the contract between the SRS and the implementation. It follows the uBix Core patterns in [`complete-php-guide.md`](../../architecture/complete-php-guide.md) (DataType / Payload / DTO / Repository) and [`complete-js-guide.md`](../../architecture/complete-js-guide.md). Every table lands via `bin/ubix migrate:*` per [`migrations.md`](../../standards/migrations.md). It cites the platform TDS for anything shared and documents only deltas.

## 1. Component map

| Component | Where | Responsibility |
|---|---|---|
| Affiliate domain | `php/Ubix/` (Models, Repositories, DTOs, DataTypes, Controllers, Services) | Affiliates, referrals, attribution resolution, revenue-share calculation |
| Affiliate API | `app/SowingMeApi/` routes → `Controller/SowingMeApi/*` | Self-enrollment, referral links/banners, redirect + attribution capture, dashboard reads |
| Admin API | `app/SowingMeAdminApi/` routes → `Controller/InternalAdminApi/AffiliateController` | Realises the existing `GET /affiliates` / `GET /affiliate/{id}` stub routes (`app/SowingMeAdminApi/src/Routes.php`) + status/rate management, org review |
| Revenue-share service | `Service/Affiliate/RevenueShareService` | Reads a `transactions` row for a referred conversion, derives the affiliate's `fee`/accrual rows; the only writer of affiliate-driven ledger rows |
| Entitlement/ownership | reuses platform `EntitlementService` + route ownership middleware | Affiliate-owns-referral, org-admin-owns-org-affiliate checks |
| Affiliate dashboard | `app/SowingMeJs/` routes `/affiliates`, `/affiliates/banners` + `js/Ubix/` components | Replaces the current stub pages (brief §3.1) |
| Admin affiliate screens | `app/SowingMeAdminJs/` route `/affiliates` | List/detail/status/rate, org review queue |

Neptune leftovers repurposed here, not deleted: the `attribution_logs` table + `AttributionLog` model/DTO/DataType (re-shaped, see §2.3), the `SowingMeAdminApi` affiliate route stubs, the `AffiliateStatusEnum`/`AffiliateRateTypeEnum`/`AffiliateSiteTypeEnum` DataType wrappers (their backing `Ubix\Enum\Affiliate\*` classes are new), and the `SowingMeJs` `affiliates`/`affiliates/banners` stub routes (the banners stub currently iframes a legacy `vscash` admin tool — removed).

## 2. Data model (new/changed migrations)

All tables `InnoDB`, snake_case, `created_at`/`updated_at`. FKs to existing `creators`, `organizations` (platform TDS §3, seam reserved at M1), `users`, `transactions`. Money is minor units (INT) + currency code — never floats (platform TDS §3, SRS FR-AFF-144).

### 2.1 `affiliates`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `creator_id` | BIGINT FK → `creators.id` NULL | owner is `creator` **or** `organization`, never both (SRS FR-AFF-103) |
| `organization_id` | BIGINT FK → `organizations.id` NULL | church/ministry affiliate (FR-FAITH-6) |
| `code` | VARCHAR(32) UNIQUE | human-shareable referral code |
| `status` | ENUM(`pending`,`active`,`suspended`,`inactive`) | `AffiliateStatusEnum` (SRS FR-AFF-105) |
| `rate_type` | ENUM(`percentage`,`flat`) | `AffiliateRateTypeEnum` |
| `rate_value` | INT | percentage in basis points (e.g. `1000` = 10.00%) or flat minor-unit amount, per `rate_type` |
| `attribution_window_days` | INT default 30 | SRS FR-AFF-122; per-affiliate override of the platform default |
| `reviewed_by_user_id` | BIGINT FK → `users.id` NULL | admin who approved/rejected (org affiliates, FR-AFF-161/172) |
| `reviewed_at` | DATETIME NULL | |

A CHECK/application-level invariant enforces exactly one of `creator_id`/`organization_id` set — the same owner-polymorphism shape the platform ADS reserves for pages/tiers/payouts (ADR-007), applied here to affiliates rather than promoted to a generic `owner_type/owner_id` column, since only two owner kinds exist for this surface.

### 2.2 `referrals`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `affiliate_id` | BIGINT FK → `affiliates.id` | |
| `referred_user_id` | BIGINT FK → `users.id` NULL | set once the referred party has an account |
| `referred_creator_id` | BIGINT FK → `creators.id` NULL | set for a church-to-creator referral (SRS FR-AFF-131) |
| `referred_organization_id` | BIGINT FK → `organizations.id` NULL | reserved; referring an org itself |
| `referred_party_type` | ENUM(`supporter`,`creator`,`organization`) | disambiguates which of the FKs above applies (FR-AFF-131) |
| `status` | ENUM(`pending`,`converted`,`expired`) | |
| `attribution_log_id` | BIGINT FK → `attribution_logs.id` | the winning click (SRS FR-AFF-124) |
| `first_touch_at` | DATETIME NULL | earliest qualifying click in the chain |
| `last_touch_at` | DATETIME | most recent qualifying click (attribution-model input, FR-AFF-123) |
| `converted_at` | DATETIME NULL | when the conversion event fired |
| `conversion_transaction_id` | BIGINT FK → `transactions.id` NULL | the ledger row whose amount seeded the first revenue-share calculation (FR-AFF-140) |

A unique constraint on `(affiliate_id, referred_user_id)` (nullable-safe via application check) prevents duplicate referrals for the same pair (FR-AFF-124).

### 2.3 `attribution_logs` (repurposed)

The existing `attribution_logs` table/model (`Ubix\Model\AttributionLog`) is a neptune carry-over shaped for a different "MP code" attribution domain (`method`, `old_mp_code`, `new_mp_code`, `bounty_paid`). This surface's migration **replaces its column set** with the shape below — same treatment the `payments` surface gives `Transaction`/`BillingTransaction` (repurposed, not kept as-is). The DataType (`AttributionLogId`), DTO (`AttributionLogOptions`), and Model are updated to match.

| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT PK | |
| `affiliate_id` | BIGINT FK → `affiliates.id` | |
| `click_token` | CHAR(43) UNIQUE | opaque token set in the attribution cookie (FR-AFF-120) |
| `surface` | ENUM(`creator_page`,`explore`,`signup`) | which link/banner target was clicked (FR-AFF-112) |
| `target_id` | BIGINT NULL | e.g. the `creators.id` the link pointed at, when applicable |
| `ip_hash` | CHAR(64) | SHA-256; raw IP not retained (SRS NFR-3) |
| `user_agent` | VARCHAR(255) NULL | |
| `landing_url` | VARCHAR(255) | |
| `occurred_at` | DATETIME | the click time |

Append-only: no update/delete of a historical row (SRS NFR-2). Every touch is logged, not just the winning one, so the attribution model (last-touch now, first-touch reserved — FR-AFF-123) is a query concern, not a schema concern.

DataTypes: introduce `AffiliateStatusEnum`-backing `Ubix\Enum\Affiliate\AffiliateStatus`, `AffiliateRateTypeEnum`-backing `Ubix\Enum\Affiliate\AffiliateRateType`, `AffiliateSiteTypeEnum`-backing `Ubix\Enum\Affiliate\AffiliateSiteType` (repurposed for the `attribution_logs.surface` distinction), plus `ReferralStatusEnum` and `ReferredPartyTypeEnum` under `php/Ubix/Enum/` + matching `DataType/Enum/*` wrappers, per the framework.

## 3. Attribution & referral lifecycle

```
click referral link ──► attribution_logs row written + first-party cookie set (click_token)
                                                 │
qualifying conversion event (signup / sub start / tip / gift / tithe / creator onboarding)
                                                 │
resolve attribution: cookie click_token, else code param ──► pick winning attribution_logs
                      row per configured model (last-touch, within attribution_window_days)
                                                 │
                                    referrals row created (status=pending → converted)
                                                 │
                              conversion produces a `transactions` row (payments surface)
                                                 │
                     RevenueShareService derives affiliate's share ──► §5 ledger writes
```

A conversion outside the affiliate's `attribution_window_days` leaves any earlier click unresolved (`referrals` row is never created for it) — SRS FR-AFF-122.

## 4. API surface

### 4.1 `SowingMeApi` — affiliate self-service
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| POST | `/affiliate/enroll` | Enroll the current creator (or, from the org admin console, the current organization) as an affiliate | FR-AFF-101,102,103 |
| GET | `/affiliate/me` | Current affiliate's profile: code, status, rate, attribution window | FR-AFF-104,105 |
| GET | `/affiliate/me/links` | Generate/list referral links for public creator pages, explore, signup | FR-AFF-110,112 |
| GET | `/affiliate/me/banners` | Platform-supplied banner creatives | FR-AFF-111 |
| GET | `/affiliate/me/referrals` | Cursor-paginated referral list + status | FR-AFF-130,150 |
| GET | `/affiliate/me/earnings` | Cumulative/pending revenue share, reads the `transactions` ledger | FR-AFF-150,151 |
| GET | `/r/{code}` | Public redirect endpoint: writes `attribution_logs`, sets the cookie, redirects to the link's target | FR-AFF-120 |

### 4.2 Internal — conversion hook (called by the owning surface, not the client)
| Method | Path | Purpose |
|---|---|---|
| POST | `/internal/affiliate/conversion` | Called by registration/payments code paths on a qualifying event (signup, subscription start, tip/gift/tithe, creator onboarding) with `{ userId, transactionId? }`. Resolves attribution, creates/updates the `referrals` row, and — when a `transactionId` is present — invokes `RevenueShareService` (§5). |

This mirrors the live-streaming surface's internal-hook pattern (its `/internal/stream/event`): a same-cluster, contract-first seam rather than the affiliate domain reaching into payments/registration code directly.

### 4.3 `SowingMeAdminApi` (realises existing stubs, `app/SowingMeAdminApi/src/Routes.php`)
| Method | Path | Purpose | Key FRs |
|---|---|---|---|
| GET | `/affiliates` | List affiliates, offset-paginated (existing stub route, new controller) | FR-AFF-160,162 |
| GET | `/affiliate/{affiliateId}` | Affiliate detail: profile, referrals, attribution activity (existing stub route, new controller) | FR-AFF-160,162 |
| PATCH | `/affiliate/{affiliateId}/status` | `active`/`suspend`/`reinstate`; org enrollment approve/reject | FR-AFF-105,161,172 |
| PATCH | `/affiliate/{affiliateId}/rate` | Override `rate_type`/`rate_value` | FR-AFF-104,161 |

## 5. Revenue-share calculation & ledger writes (the money seam)

`RevenueShareService` is the **only** writer of affiliate-driven `transactions` rows — it does not decide payout timing (that remains the `payouts` surface's scheduled job) and it does not maintain its own running balance (the dashboard reads the ledger, SRS FR-AFF-151).

On a qualifying `transactions` row for a referred party's conversion (subscription charge, tip, gift, or tithe):

1. Look up the open `referrals` row for that user (created at conversion time, §3).
2. Compute `share = rate_type === 'percentage' ? amount * rate_value / 10000 : rate_value` (minor units).
3. Write a `transactions` row `type=fee`, `related_id` = the original transaction's id, `amount = share`, with a description tagging the `affiliate_id` — this is additional platform cost of the referral, alongside the existing platform-commission `fee` row (platform TDS §7); it does **not** replace or double-count that commission.
4. Write a `transactions` row `type=payout` (accrual) crediting the **affiliate owner's** (`creator` or `organization`) balance for `share`, referencing the affiliate via `related_id`.
5. No new ledger `type` values are introduced — this is data in the existing `fee`/`payout` enum, per platform ADR-004 and the extensibility contract's "Affiliate revenue-share" row (platform architecture §9).

Refunds/chargebacks on the original transaction reverse steps 3–4 the same way the payments surface reverses platform commission (SRS FR-AFF-143) — a mirrored negative `fee`/`payout` pair, idempotent by the original transaction id.

The scheduled payout job (payouts surface) sweeps any creator's/organization's accrued `payout` rows — including affiliate revenue share — into the next Stripe Connect transfer; affiliates have no separate payout instrument (SRS FR-AFF-142).

## 6. Frontend (SvelteKit)

### 6.1 Affiliate dashboard (`app/SowingMeJs/src/routes/affiliates/+page.svelte`)
Replaces the current stub ("This is the Affiliates page.") with: enroll CTA (if not yet an affiliate), referral code + copyable links, referral list (status, referred party type, converted date), earnings summary sourced from `GET /affiliate/me/earnings`, payout history (reads the `payouts` surface). Reuses `Sidebar`/`ThemeToggle` per `complete-js-guide.md`.

### 6.2 Banners (`app/SowingMeJs/src/routes/affiliates/banners/+page.svelte`)
Replaces the current stub, which iframes a legacy `vscash`-domain admin tool with no Sowing.me meaning — removed entirely. Real page lists platform-supplied creatives from `GET /affiliate/me/banners`, each with a copy-embed snippet carrying the affiliate's `code`.

### 6.3 Admin (`app/SowingMeAdminJs/src/routes/affiliates`)
List/detail screens over `GET /affiliates` / `GET /affiliate/{id}`, status and rate controls, and an org-affiliate review queue (FR-AFF-161/172).

## 7. Security & privacy

- Attribution cookie: first-party, `SameSite=Lax`, `Secure`, opaque `click_token` — never the affiliate `code` alone, so a token can't be replayed to fabricate a different affiliate's click (SRS NFR-2).
- `attribution_logs.ip_hash` stores a SHA-256 of the IP, not the raw address (SRS NFR-3, platform NFR-PRIV).
- Admin status/rate-change endpoints run through existing role + ownership middleware (`admin` role); org-affiliate approval additionally checks the org exists and is in good standing.
- Revenue-share ledger writes happen only inside `RevenueShareService`, server-side, off the internal conversion hook — never from a client-supplied amount (SRS NFR-5).

## 8. Testing

- **Unit:** revenue-share calculation (percentage vs. flat, rounding on minor units), attribution-window and last-touch resolution logic, referral dedup, `AffiliateStatusEnum`/`AffiliateRateTypeEnum` DataTypes, Payload validation. Non-container per the migration-test pattern.
- **Integration:** `/internal/affiliate/conversion` against a stubbed conversion event for each referred-party type (supporter/creator/organization); ledger row shape assertions (`fee` + `payout` pair, refund reversal); admin status/rate endpoints against role/ownership middleware.
- **E2E (staging):** click a referral link → cookie set → sign up/subscribe within window → referral converts → dashboard shows earnings → next payout run includes the accrual. Matches SRS §10.
- Gates: `phpunit` (strict), `phpstan` max, `phpcs` (custom sniffs), JS suite. Every table via migration.

## Requirement traceability

| FR | Realised by |
|---|---|
| FR-AFF-101/102/103 | `POST /affiliate/enroll`, `affiliates.creator_id`/`organization_id` |
| FR-AFF-104/105 | `affiliates.rate_type`/`rate_value`/`status`, `AffiliateStatusEnum`/`AffiliateRateTypeEnum` |
| FR-AFF-110/111/112 | `GET /affiliate/me/links`, `GET /affiliate/me/banners`, `attribution_logs.surface` |
| FR-AFF-120/121/122/123 | `GET /r/{code}`, attribution cookie, §3 lifecycle, `affiliates.attribution_window_days` |
| FR-AFF-124/130/131 | `POST /internal/affiliate/conversion`, `referrals` table + `referred_party_type` |
| FR-AFF-140/141/142/143/144 | `RevenueShareService` §5, `transactions` `fee`/`payout` rows, `payouts` surface sweep |
| FR-AFF-150/151 | `GET /affiliate/me/earnings`, `GET /affiliate/me/referrals`, dashboard §6.1 |
| FR-AFF-160/161/162 | `GET /affiliates`, `GET /affiliate/{id}`, `PATCH .../status`, `PATCH .../rate` |
| FR-AFF-170/171/172 (FR-FAITH-6) | `affiliates.organization_id`, admin review (`reviewed_by_user_id`/`reviewed_at`), §5 accrual to org balance |

Full table maintained as the surface is sliced; each new endpoint/table cites its FR.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial technical spec. |
