# Sowing.me — Platform ADS (Architecture Design Spec)

**Altitude:** Platform (whole product) · **Status:** Draft v0.1 · 2026-08-27 · Owner: Christopher W. Olsen
**Companion docs:** [`srs.md`](srs.md) (SRS) · [`technical-spec.md`](technical-spec.md) (TDS) · [`README.md`](README.md)
**Framework references:** [`monorepo.md`](../../../architecture/monorepo.md) · [`complete-php-guide.md`](../../../architecture/complete-php-guide.md) · standards in [`docs/standards/`](../../../README.md)

> **How the whole product hangs together as a system**, and — critically — the seams that let every post-MVP domain (church accounts, giving, prayer, live, community, public API) be added **without reworking the foundation** (NFR-EXT). This is the doc a surface must not contradict.

## 1. System context (C4 level 1)

```
        Visitors / Seekers        Supporters        Creators        Church/Org admins        Platform admins
              │                       │                 │                  │                       │
              ▼                       ▼                 ▼                  ▼                       ▼
        ┌───────────────────────── Sowing.me platform ─────────────────────────────────────────────┐
        │  Marketing (SowingMeWeb) · Product SPA (SowingMeJs) · Admin SPA (SowingMeAdminJs)          │
        │  Product API (SowingMeApi) · Admin API (SowingMeAdminApi) · Media/Live (SowingMeStream)    │
        └───────┬───────────────┬────────────────┬───────────────┬───────────────┬──────────────────┘
                │ Stripe        │ S3 storage     │ Email (SMTP)  │ CDN           │ (future) push, SMS
                ▼               ▼                ▼               ▼               ▼
           Payments/KYC     Media objects     Transactional    Static/media     external
           (Connect)                          mail             delivery
```

External dependencies are all behind interfaces (TDS §5): Stripe (`PaymentProviderInterface`), S3 (`MediaStorageInterface`), SMTP (`MailerInterface`), CDN (delivery only). Nothing in the domain layer imports a vendor SDK directly.

## 2. Container view (C4 level 2) — apps on k3s

| Container | Tech | Scale | Notes |
|---|---|---|---|
| `SowingMeWeb` | Slim 4 + Latte, PHP-FPM/nginx | stateless, HPA | marketing; mostly cacheable |
| `SowingMeJs` | SvelteKit (node/adapter) | stateless, HPA | product SPA |
| `SowingMeAdminJs` | SvelteKit | stateless | admin SPA, internal |
| `SowingMeApi` | Slim 4 + PHP-DI | stateless, HPA | core product API |
| `SowingMeAdminApi` | Slim 4 | stateless | admin/moderation |
| `SowingMeStream` | MediaMTX (Go) + ffmpeg | node-pinned StatefulSet | live surface; UDP media (see live ADS) |
| MariaDB | managed/statefulset | primary + replicas later | schema via migration runner |
| Cache | Memcached (keys standard exists) | — | sessions/hot reads |

All apps are **stateless** (session store + DB + cache are external), so core browsing/paying scales horizontally and survives a pod loss (NFR-AVAIL/SCALE). Media & live are deliberately isolated so their failure degrades only those features.

## 3. Deployment & environments

- k3s (Rancher), nodes `kube-001..004`, nginx ingress, GitLab CI with `dev` / `staging` / `main` (+ `sandbox`) per-app manifests (`{env}-deploy.yaml`, `{env}-ingress.yaml`, …).
- One app selected at runtime via `APP_NAME` (`public/index.php` loader).
- Secrets via k8s Secrets / uBixVault (family tooling); config via env (`NEPTUNE_*` vars retained). Media/live get their own manifests (`SowingMeStream`).
- CI gates: `phpunit`, `phpstan`, `phpcs`, JS build/test before deploy.

## 4. Data architecture

- **MariaDB** is the system of record; every table via `bin/ubix migrate:*`. Read replicas + caching added when read load requires (feeds/explore first).
- **Ledger-centric money model**: the generic `transactions` table (TDS §3) is the spine for subscriptions, tips, gifts, tithes, fees, payouts, refunds — so new money features are rows, not schema.
- **Owner polymorphism** for pages/tiers/payouts: a page/tier/payout belongs to a `creator` **or** an `organization`. **ADR-007** picks the concrete mechanism (org FK on `creators` for M1 simplicity, promoted to `owner_type/owner_id` when orgs land) — chosen so M1 ships simply while M3+ orgs don't require rewriting content/tier/payout tables.
- **Cross-cutting shapes** (ledger, attribution, media) may also be documented under `docs/data-models/` when they span surfaces.
- **Privacy by design**: PII columns identified; export/delete path designed now (NFR-PRIV) even if the tooling lands later.

## 5. Security architecture

- **AuthN:** PHP session cookie (`Secure`, `SameSite=Lax`); lockout on repeated failures. **Token/JWT seam reserved** for future mobile/public API — isolated behind the auth middleware so adding it doesn't touch domain code.
- **AuthZ:** defence in depth — route middleware (role) + service checks (ownership) + `EntitlementService` (tier gating). No access decision is client-side.
- **Secrets & sensitive data:** encrypted at rest, permission-gated, audit-logged, never logged in plaintext (`sensitive-data-access.md`). Stripe/restream/media keys live in Secrets/uBixVault.
- **PCI:** out of scope by design — hosted Stripe surfaces, no card data on our systems.
- **Boundaries:** admin apps internal-exposure; internal service endpoints (e.g. media hooks) shared-secret + NetworkPolicy; TLS terminated at ingress.
- **Audit:** money movement and moderation actions are audit-logged.

## 6. Key subsystems

- **Payments subsystem:** `PaymentProviderInterface` + webhook controller + ledger + Connect payouts. Single signature-verification point; idempotent webhooks; commission as derived `fee` rows. (SRS FR-PAY/FR-FIN.)
- **Media subsystem:** presigned upload → `post_media` → signed/CDN delivery; async derivatives. Shared by content and live-VOD.
- **Live subsystem:** `SowingMeStream` (MediaMTX) — full ADS at [`docs/surfaces/live-streaming/architecture.md`](../../../surfaces/live-streaming/architecture.md); it plugs into the same auth/entitlement/ledger/media seams rather than inventing its own.
- **Notification subsystem:** `NotifierInterface` fan-out (email + in-app) via jobs; per-user prefs; rate-limit/dedup.
- **Moderation subsystem:** reports → queue → actions → audit; content policy enforcement (faith-aligned).

## 7. Request/data flows (representative)

**Supporter subscribes (MVP spine):**
```
SPA → Stripe Checkout (hosted) → Stripe webhook → SowingMeApi (verify, idempotent)
   → transactions(subscription) + fee row + subscriptions(active) → creator balance updated
Supporter views gated post → EntitlementService.resolve → signed media URL or paywall
```
**Creator payout:** scheduled job → Connect transfer → transactions(payout) → statement.
**Went-live / new-post notification:** event → NotifierInterface → jobs fan out to entitled supporters (deduped).

## 8. Scalability & performance

- Stateless app tier scales via HPA; DB read replicas + Memcached for feed/explore hot paths; cursor pagination bounds query cost.
- Media/live egress is CDN/cache-served, never off app pods (dominant-cost isolation).
- Async offload for anything heavy (derivatives, fan-out, VOD, payouts).
- Capacity specifics for live are in the live ADS; platform target p95 < 300 ms (excl. third-party).

## 9. Extensibility contract (why we won't need rework — NFR-EXT)

The foundation reserves these seams so post-MVP domains are additive:

| Future domain | Seam reserved at M0/M1 |
|---|---|
| Church/organization accounts | `organizations` entity + owner polymorphism (§4, ADR-007) |
| Giving & tithing | generic `transactions` ledger already has `gift`/`tithe` types |
| Prayer requests/walls | standalone tables + `EntitlementService` reuse for scoping |
| Live streaming | media + entitlement + ledger seams; isolated `SowingMeStream` app |
| Comments/community/messaging | `EntitlementService` for gating; new tables, no core change |
| Affiliate revenue-share | `attribution_logs` seed + ledger `fee`/`payout` types |
| Public API / mobile | token-auth seam behind existing auth middleware; API is additive routes |
| Localization / multi-currency | money stored as minor-units + currency from day one; i18n envelope pattern exists (`monorepo.md`) |

**Rule:** a new surface may add tables, routes, UI, and enum values; it may **not** require altering the ledger spine, the auth model, the media pipeline, or the app topology. If it seems to, the platform ADS is revised first (via an ADR) and re-versioned.

## 10. Architecture Decision Records (ADR index)

| ID | Decision | Status |
|---|---|---|
| ADR-001 | Modular monolith on uBix Core (shared `php/Ubix/`), split into apps by exposure, not microservices | Accepted |
| ADR-002 | PHP session cookie auth for M1; token/JWT seam reserved for mobile/public API | Accepted |
| ADR-003 | Stripe (Checkout + Billing + Connect Express); no card data on our systems | Accepted |
| ADR-004 | Generic `transactions` ledger as the money spine (types cover tip/gift/tithe/fee/payout/refund) | Accepted |
| ADR-005 | S3-compatible media with presigned upload + signed delivery + CDN | Accepted |
| ADR-006 | Live streaming via MediaMTX in a dedicated `SowingMeStream` app (see live ADS) | Accepted |
| ADR-007 | Owner polymorphism (creator/org): org-FK at M1, promote to `owner_type/owner_id` when orgs land | Accepted |
| ADR-008 | Central `EntitlementService` as the single gating authority across posts/media/live/community | Accepted |

New cross-cutting decisions are appended here and referenced from surfaces.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-27 | Initial platform ADS — context/container views, data & security architecture, extensibility contract, ADR index. |
