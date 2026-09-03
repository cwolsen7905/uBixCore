# Memcache Key Conventions

**Status:** Approved
**Audience:** VS Media Development Department
**Last Updated:** 2026-07-30

This document defines the naming convention for Memcache keys written by uBix Core services. It applies to every key uBix Core sets, regardless of which Memcache cluster the request lands on (current legacy cluster, or the new mcrouter-fronted cluster once cutover happens).

---

## 1. Scope

These conventions apply to:

- All Memcache keys **set** by uBix Core PHP services (`app/*Api`, `app/*Web`, `bin/ubix` CLI tools).
- Memcache keys **read** by uBix Core from a legacy-shared key — read the legacy key in its existing format; do NOT rename a legacy key just because uBix Core is touching it. Legacy keys are documented in their owning service's spec.

These conventions do NOT apply to:

- Legacy keys written by `php-live-components/`, `_includes/lib_php1.0.1/`, or other legacy code paths. Those keep their existing shapes for cutover compatibility.
- PHP session keys (`PHPSESSID`, `sess_<DOMAIN>_<SESSID>`) — those are owned by the session handler and have their own legacy contract.

---

## 2. Convention

### 2.1 Prefix

**All uBix Core-written Memcache keys MUST begin with `NEPTUNE_`.** Eight characters of overhead per key, but worth it:

- **Self-documenting in ops tools.** `memcached-tool stats` / `mcrouter-stats` show the key prefix; `NEPTUNE_*` is unambiguous about ownership.
- **Searchable.** `grep NEPTUNE_` across the codebase finds every uBix Core cache touch point.
- **Visually distinct from legacy.** Legacy keys are inconsistent (`cdn_list_flirt4free`, `Platform_CDN_class_$platform`, `sess__<SESSID>`, etc.); the `NEPTUNE_` prefix never overlaps.
- **Avoids any future short-prefix collision** (`PN_`, `NP_`, etc. could mean other things — phone number, production network, npm package).

### 2.2 Format

**`SCREAMING_SNAKE_CASE`** — same convention PHP uses for class / module constants. Underscores between word groups; no hyphens, no dots, no spaces, no mixed case.

**Why:** PHP's constant-naming convention; visually distinct from lowercase legacy keys; matches the existing legacy exception `ARRAY_LIVE_MODELS_IS_FILTERED_<model_id>` so engineers aren't surprised; treats keys as identifiers.

### 2.3 Pattern

```
NEPTUNE_<CAPABILITY>_<DESCRIPTOR>[_<DETAIL>]...
```

- `<CAPABILITY>` — the cross-cutting capability or surface that owns the cache (`CDN`, `WHITELABEL`, `FEATURE_FLAG`, `RATE_LIMIT`, `LIVE_MODELS`).
- `<DESCRIPTOR>` — what the key holds (`PROVIDERS`, `CONFIG`, `VERSION`, `COUNTER`).
- `<DETAIL>` — optional disambiguation (a domain, a user-id, a scope name). Use ALL-CAPS unless the detail is itself a value (e.g. user id `1234`, domain literal substituted in).

### 2.4 Examples

```
NEPTUNE_CDN_PROVIDERS_ACTIVE             # CdnService eligible-list cache (this slice)
NEPTUNE_CDN_PROVIDERS_VERSION            # version-key bust counter (sub-TTL invalidation)

NEPTUNE_WHITELABEL_CONFIG_<DOMAIN>       # per-domain WhitelabelConfig
NEPTUNE_WHITELABEL_VERSION               # version-key bust (REQ-WLC-RES-003)

NEPTUNE_FEATURE_FLAG_<KEY>_<ENV>         # future Feature Flags spec REQ-FF-CACHE-*
NEPTUNE_FEATURE_FLAG_VERSION

NEPTUNE_RATE_LIMIT_<SCOPE>_<SUBJECT_ID>  # future Abuse Prevention REQ-RL-CACHE-*
NEPTUNE_RATE_LIMIT_VERSION

NEPTUNE_LIVE_MODELS_FILTER_<SERVICE>     # if/when Live Models filter chain caches
```

### 2.5 Length budget

Memcache keys are limited to **250 bytes**. With `NEPTUNE_` accounting for 8, every key has 242 bytes of payload. This is never a real constraint for the patterns above — domain values and id substitutions stay well under the budget — but flag any key generation that could produce a value approaching 200 bytes for review.

### 2.6 Reserved characters — a correctness rule, not style

PSR-16 **reserves `{}()/\@:` in cache keys** — a compliant implementation MUST reject a key containing any of them. This is incident-tested, not theoretical: a colon in a Memcache key 500'd the IA-2.0 admin lists in production (fixed in `9a5c4d54`). The `SCREAMING_SNAKE` convention avoids the reserved set by construction, **but only for the literal parts of the key** — the trap is `<DETAIL>` interpolation:

- **Never interpolate a raw external value into a key.** A `<DOMAIN>` detail can arrive as `host:port` or a URL-ish string (`/`, `:`), an id field can arrive as something other than digits. Sanitize every interpolated detail to `[A-Za-z0-9_]` (replace anything else with `_`) or reject it before building the key.
- Numeric ids (`1234`) and enum-like scope names you control are safe to substitute directly; anything user- or config-supplied is not.
- When writing a repository/service test for cache-backed code, **assert the generated key** (capture it via the stubbed cache) — the PSR-16 stub used in tests does not enforce the reserved set, so a bad key passes tests and fails in production.

---

## 3. Versioning subkeys for sub-TTL invalidation

When ops needs sub-TTL propagation (an admin tool changes a config row and pods need to see the new value before the soft TTL expires), use a **version-key bust** pattern:

1. Maintain a separate Memcache key `NEPTUNE_<CAPABILITY>_VERSION` holding an incrementing integer.
2. The admin tool bumps the version key on every relevant write.
3. Cached values are stamped with the version they were written under (either appended to the data key or stored in the value).
4. Readers compare the cached version to the current version key; mismatch → re-query the source-of-truth and populate the cache with the new version.

**This deferred until a Slice needs sub-TTL propagation.** Slice 1 of CDN Service uses time-based TTL only (5 minutes), which is acceptable when ops adds / removes CDN providers ~once per year.

The version-key pattern mirrors the legacy `MEMCACHE_SERIAL` mechanism for `memcache-servers.inc` propagation, but per-capability rather than file-wide. (The related shadow-write design for the Memcache cluster migration was never captured in this repo — a separate concern from the key conventions in this doc; document it if that migration is picked back up.)

---

## 4. TTL guidance

Set explicit TTLs on every `set()` call. Defaults vary by capability:

| Use case | Recommended TTL | Rationale |
|---|---|---|
| Cross-pod config cache (CDN list, whitelabel rows) | 5 min | Bounds staleness on ops changes; matches REQ-WLC-CACHE-002 / REQ-CDN-CACHE-002. |
| Webservice-result cache (legacy parity) | 24 h | Matches legacy `Platform/CDN.php` / `MemcacheUtil` 86,400s convention. |
| Per-request memoization | n/a — instance state | Use private fields, not Memcache. |
| Counter / rate-limit window | matches the window | E.g., 1-minute rate limit → 60s TTL. |
| Session payloads | matches `session.gc_maxlifetime` | Owned by the session handler, not application code. |

Avoid never-expire (`0` TTL) writes — every cache entry SHOULD eventually drop on its own so a missed invalidation can't poison the cache forever.

### 4.1 Stampede (thundering-herd) protection

uBix Core's deployment model makes cache expiry a **coordinated event**: every service runs on ≥ 5 pods sharing one Memcache, so when a hot key (e.g. `NEPTUNE_CDN_PROVIDERS_ACTIVE`) expires, every pod misses at once and every pod hits the source DB simultaneously. For most keys that's five cheap queries and nobody notices; for an expensive query or a high-request-rate key it's a synchronized load spike. Defenses, in order of preference:

1. **TTL jitter (default for hot config keys).** Add a small random offset to the TTL at write time (e.g. 5 min ± 10%) so pods that populated the key at different moments don't expire it in lockstep. Cheap, no coordination, no new machinery.
2. **Soft-TTL / serve-stale-and-refresh.** Store the value with its own freshness timestamp inside a longer hard TTL; when a reader sees the soft TTL exceeded, it serves the stale value and *one* caller refreshes. Pairs naturally with the §3 version-key pattern.
3. **Single-flight lock-on-miss.** On a miss, `add()` a short-TTL lock key (`NEPTUNE_<CAPABILITY>_<DESCRIPTOR>_LOCK`); the winner recomputes and populates, losers briefly retry the read or serve a fallback. Reach for this only when the recompute is genuinely expensive — it adds a failure mode (lock-holder dies → losers wait out the lock TTL).

A shared implementation of 2/3 belongs in the `SimpleCache` seam rather than hand-rolled per call site (tracked as benchmark item SB-23's follow-up); until it exists, apply jitter inline — it's one line.

---

## 5. Operational notes

### 5.1 Server pool & taking a server out of rotation

`MEMCACHE_SERVERS` is a CSV of `host:port` entries, and **list position is load-bearing**: `MemcachedLegacySimpleCache` routes a key to slot `crc32($key) % slotCount` — the same routing as legacy `MemcacheUtil.cl` — so inserting or deleting an entry changes the modulo and remaps virtually every key on the pool (mass miss-storm; sessions dropped).

To take a server out of rotation, **never delete its entry**. Keep the entry in place and set its port to the magic value **`9999`** (`MemcachedLegacySimpleCache::DISABLED_PORT`):

```
MEMCACHE_SERVERS='...,memcache006.lan.vsmedia.net:11211,memcache007.lan.vsmedia.net:9999,memcache008.lan.vsmedia.net:11211,...'
```

A `:9999` slot stays in the modulo but is never connected to; keys hashing to it walk forward (wrapping) to the next enabled slot. Only that server's `1/slotCount` share of keys moves. This is the uBix Core equivalent of the `=> false` flag in the legacy `memcache-servers.inc` ("DO NOT COMMENT OUT ANY MEMCACHE SERVERS — SET THEM TO FALSE"), and the prod slot layout MUST mirror that file's (all entries, in order, including `false` ones) so both stacks route shared keys — e.g. `sess_*` — to the same server. `MemcachedLegacySimpleCache::getServerForKey()` reports where a key routes without connecting, for diagnostics.

- **Sandbox / dev / staging Memcache clusters** are separate from prod (per `MEMCACHE_SERVERS` env config). Keys don't cross environments.
- **Multi-pod consistency:** Memcache is shared across the K8s pod fleet, so all pods see writes within Memcache propagation time (sub-second). This is the primary reason to use Memcache over per-pod static state.
- **Read-through wrapper:** services SHOULD implement read-through caching (read cache → on miss query source-of-truth → write cache → return). NOT write-through (every write of source-of-truth is also a Memcache write) — that couples the source-of-truth path to Memcache health.
- **Cache failures are non-fatal.** A `SimpleCache::get()` exception or null return SHALL NOT propagate to the consumer; treat as a cache miss and continue. Same for `set()` failures — log warn and continue.

---

## 6. References

- Existing uBix Core services that follow these conventions (post-2026-05-01):
  - `Ubix\Service\Cdn\CdnService` — first slice to land using the new conventions; key `NEPTUNE_CDN_PROVIDERS_ACTIVE`.
- Spec entries that reference cache shape (and SHALL match the conventions here):
  - `docs/surfaces/whitelabel-chrome/srs.md` REQ-WLC-CACHE-001..003
  - `docs/surfaces/cdn-service/technical-spec.md` REQ-CDN-CACHE-001..004
  - `docs/surfaces/feature-flags/srs.md` REQ-FF-CACHE-* (future)
  - `docs/surfaces/abuse-prevention/technical-spec.md` REQ-RL-CACHE-* (future)
- Legacy Memcache key inventory (DO NOT use as a template for new keys; documented for cutover-period awareness):
  - `cdn_list_flirt4free` — `Platform/CDN.php` flat-file fallback cache, 24h TTL
  - `Platform_CDN_class_<platform>` — `Platform/CDN.php` webservice-fallback cache
  - `sess__<SESSID>` (or `sess_<DOMAIN>_<SESSID>` in prod) — PHP session payload
  - `ARRAY_LIVE_MODELS_IS_FILTERED_<model_id>` — legacy live-cams override
  - `MEMCACHE_SERIAL` — `memcache-servers.inc` versioning
