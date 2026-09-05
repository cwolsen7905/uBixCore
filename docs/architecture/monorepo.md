# uBix Core Monorepo

How this repository is organised and how work flows from a feature branch to production. For per-app architecture detail see the linked docs in each section — this document is about the shape of the repo and the release process, not the internals of any one app.

## Overview

uBix Core is a **single git repository containing every service in the product suite**: the public-facing PHP APIs, the Latte-templated PHP web apps, the SvelteKit frontend apps, shared PHP framework code, a shared Svelte component library, the CLI tool, deployment manifests, and database schema. One checkout, one issue tracker, one CI pipeline, one CHANGELOG.

The monorepo exists because:

- The PHP apps share a substantial framework (`php/Ubix/`) — controllers, services, repositories, Payload/DataType validation, PHPCS sniffs — and every app upgrades together when framework conventions change.
- The JS apps share a component library (`js/Ubix/`) and follow the same SvelteKit conventions; duplicating that across repositories would multiply maintenance.
- Every app deploys to the same infrastructure with the same tooling; a single source of truth for k8s manifests (`config/`) and deploy scripts beats N per-app copies.
- Atomic changes that touch both the API and its frontend (e.g., adding a field) land in one commit instead of two coordinated PRs.

Each app still deploys independently. The monorepo is source-of-truth; the deploy pipeline can release one app at a time.

## Repository shape

```
app/             # Every uBix Core application lives here. Suffix determines type.
│  *Api/         #   PHP REST API (Slim 4, shares public/index.php)
│  *Js/          #   SvelteKit frontend
│  *Web/         #   PHP web app using Latte templates (shares public/index.php)
│  *Py/          #   Python FastAPI service (py/Ubix framework)
│  *Go/          #   Go service (cmd/ + internal/, self-contained module)
│  UbixCli/   #   CLI (Symfony Console)
bin/             # `ubix` CLI entry point + scripts
config/          # DevOps config (nginx, k8s manifests)
js/Ubix/          # Shared Svelte component library (imported by *Js apps)
php/Ubix/         # Shared PHP framework (controllers, services, DataTypes, ...)
py/Ubix/          # Shared Python framework (`@ubixsys/ubixcore` namespace: app factory, env, Redis)
public/          # Single shared web root for ALL PHP apps — APP_NAME env var selects
specs/           # Technical specification documents
sql/             # Database schema files (one per database)
templates/       # Latte templates, organised per app (e.g. templates/fanclub-api-v1/)
tests/           # PHPUnit tests (mirrors php/ structure)
vendor/          # Composer dependencies (git-ignored)
```

### App naming convention

The suffix on a directory name under `app/` is load-bearing — it tells the build/deploy pipeline what kind of app lives there:

| Suffix   | Runtime  | Purpose                                  | Example               |
| -------- | -------- | ---------------------------------------- | --------------------- |
| `*Api`   | PHP 8.3+ | REST API (JSON in/out via Payloads)      | `FanClubApi`          |
| `*Web`   | PHP 8.3+ | Server-rendered web app via Latte        | (future)              |
| `*Js`    | Node 20+ | SvelteKit frontend                       | `ProductJs`           |
| `*Py`    | Python 3.12 | FastAPI/gunicorn service (onnx AI, ...) | `RoomSfwCheckerPy`    |
| `*Go`    | Go 1.25+ | NATS-subscriber / WebSocket services     | `RealtimeFanoutGo`    |
| CLI tool | PHP 8.3+ | Symfony Console commands (exact name)    | `UbixCli`          |

Adding a new app means creating a new `app/<Name>{Api,Js,Web}/` directory and wiring the deploy pipeline to it. The suffix convention means the pipeline doesn't need per-app switches: everything ending in `Js` builds with `npm run build` and deploys as a Node container; everything ending in `Api` or `Web` shares `public/index.php`. If the new app is an **externally-reachable API**, its prod ingress must also publish the four `trident` domains — see [External API ingress hosts](#external-api-ingress-hosts-the-trident-domains).

### Shared code

Three libraries are consumed by other apps in the repo (Go apps are self-contained modules today — no shared Go library yet):

- **`php/Ubix/`** — the PHP framework. Controllers, services, repositories, models, DataTypes, Payloads, custom PHPCS sniffs. Every `*Api` / `*Web` / CLI app depends on it. See [complete-php-guide.md](complete-php-guide.md) for the deep dive.
- **`js/Ubix/`** — a Svelte 5 raw-source library wired through **npm workspaces** (declared in the repo-root `package.json`'s `"workspaces": ["js/Ubix", "app/*Js"]`). Every `*Js` app declares `"@ubixsys/ubixcore": "*"` and imports as `import { Foo } from '@ubixsys/ubixcore'`; the workspace install creates a symlink from `node_modules/@ubixsys/ubixcore` to `js/Ubix`. No build step inside the library — Vite's Svelte plugin processes raw `.svelte` source via the package's `svelte` exports field. Components shared across more than one JS app belong here; app-specific components stay in `app/*Js/src/lib/`. See [complete-js-guide.md → Shared JS code](complete-js-guide.md#shared-js-code--jsvsm) for the deeper writeup.
- **`py/Ubix/`** — the shared Python framework (`@ubixsys/ubixcore` namespace: FastAPI app factory, env loading, Redis client seam, `py.typed`). Every `*Py` app depends on it via an editable co-install (`ubix py:install`). See [complete-py-guide.md](complete-py-guide.md).

Framework changes in either shared library are monorepo-wide events — they happen alongside the updates to every consuming app in the same commit or PR.

### Domain vocabulary mapping

A few product terms collide with reserved class-name tokens (see [complete-php-guide.md → Reserved class-name tokens](complete-php-guide.md#reserved-class-name-tokens) for the full rule). When that happens, the **product vocabulary stays unchanged in conversation, specs, and product copy**, and the **code uses a synonym**. The mapping is documented so reviewers don't try to "correct" either side.

| Product / spec / conversation says | Code uses | Reason |
|---|---|---|
| "live model" / "live models" | `LiveCam` / `LiveCams` (e.g. `LiveCamDto`, `Ubix\Service\LiveCam\…`) | The word `Model` is reserved in concrete-class names to keep `Ubix\Model\*` as the unambiguous entity-class namespace. |

When you add a class in one of these domains, follow the code-side spelling. When you write a spec, planning doc, or commit message about it, use the product-side spelling.

Note the narrow scope of this table: it covers **reserved-class-name-token collisions only**. The broader question — which word a domain concept gets, and how that word is spelled as it crosses PHP / SQL / JS / CSS / a frozen wire field — is owned by [`docs/standards/vocabulary.md`](../standards/vocabulary.md). So `--color-chat-model` and the JS union member `'model'` are correct as they stand; neither is a class name, and neither is subject to the synonym above.

## How apps boot

### PHP apps — shared entrypoint, `APP_NAME` selects

Every PHP-side app (`*Api`, `*Web`, and the root of the CLI's HTTP mode) shares `public/index.php` as a single entrypoint. The `APP_NAME` environment variable tells `index.php` which app to load:

```
APP_NAME=AffiliateApi  → app/AffiliateApi/src/{Dependencies,Middleware,Routes,Theme}.php
APP_NAME=FanClubApi    → app/FanClubApi/src/{Dependencies,Middleware,Routes,Theme}.php
APP_NAME=ProductApi    → app/ProductApi/src/{Dependencies,Middleware,Routes,Theme}.php
```

Each app's `src/` must contain:

- `Dependencies.php` — PHP-DI container configuration.
- `Middleware.php` — Slim middleware stack.
- `Routes.php` — route definitions, **alphabetically sorted** (enforced by a PHPCS sniff).
- `Theme.php` — Latte template configuration (only for `*Web` apps).

In production, a single PHP container image is built once and N pods run it with different `APP_NAME` values. There is no per-app PHP Dockerfile. See [complete-php-guide.md](complete-php-guide.md) for the framework detail and [specs/](../specs/) for the spec history.

### JS apps — independent projects

Each `app/*Js/` is a self-contained SvelteKit project with its own `package.json`, `vite.config.js`, and `svelte.config.js`. Each app has its own K8s deployment YAMLs and runs on its own hostname. The full architecture — route files, components, runes conventions, server helpers, SSE endpoints, testing, deployment — is documented in [complete-js-guide.md](complete-js-guide.md).

The shared `js/Ubix/` library is wired in via npm workspaces (see "Shared code" above). It already provides a small public surface (a `formatCount` utility today) and grows as cross-app sharing needs emerge.

- Each JS app runs its own `npm run dev`, `npm run build`, `npm run check`, and `npm test` inside its own directory.
- `npm install` is run **once at the repo root**, not per-app — the workspace install handles every member in a single pass.
- There is **not** currently a single-image-per-JS-app story analogous to the PHP one — each JS app builds and deploys its own container. A planning-phase proposal to unify this is tracked in auto-memory; ask before adopting.

See [complete-js-guide.md](complete-js-guide.md) for the per-app architecture details, [js-code-review.md](../standards/js-code-review.md) for the JS review tooling, and [complete-php-guide.md](complete-php-guide.md) for comparisons between the PHP and JS conventions where they diverge.

## Cross-cutting capability scope (which apps adopt it)

Some cross-cutting capabilities apply to a defined subset of the monorepo's apps rather than to all of them. The rule is captured per-capability in its tech spec; flagged here so the scope question is discoverable when adding a new app.

- **i18n / localisation** — applies to all `*Js` apps + `*Api` apps that power them. Integration-only `*Api` apps (consumed by partners, server-to-server tooling, or external webhooks) do **not** adopt the i18n envelope contract and ship plain English strings. See [`docs/surfaces/i18n/technical-spec.md`](../surfaces/i18n/technical-spec.md) §1.5 for the per-app classification + future-app rule.

A new app inherits the rule by default — a new `*Js` adopts the architecture, a new integration `*Api` doesn't. When an `*Api` serves both `*Js` consumers AND integrations, ship envelopes universally (integration consumers use the `fallbackText` field and ignore the rest); simpler than per-endpoint classification.

## Per-app deploys

Each app deploys independently — a bug fix in `FanClubApi` does not require redeploying `ProductApi`. The monorepo ships atomically from the git perspective (every merge contains a consistent tree) but the **deploy step is per-app**.

Implication: the monorepo version (see **Versioning** below) is a calendar marker, not a per-app API-stability contract. A tag `v2026.04.1` means "the repo looked like this on April 21, 2026" — it does not imply that any specific app's public API is stable, or that all apps are at the same functional version. Per-app compatibility and deprecation notices belong in that app's CHANGELOG entries and release notes, not in the version number.

## Per-app manifest (`ubix.json`)

Each app declares its non-derivable facts in a small, language-neutral **`app/<App>/ubix.json`** at the app root (alongside `openapi.json` / `package.json` / `pyproject.toml`). This is the single machine-readable source of truth for anything the deploy pipeline, generators, and `code:review` checks need to know about an app that they **cannot** infer from the directory. It follows the monorepo's established pattern — apps are glob-discovered from `app/*/` and their *type* is derived from the suffix — so the manifest holds only what the suffix can't tell you.

```json
{
  "exposure": "public",
  "description": "Product management / live-models public API"
}
```

Fields:

- **`exposure`** (`"public"` | `"internal"`) — the **network-reachability** axis: is this app reachable from *outside* our network? `public` includes partner/integration APIs (server-to-server, webhooks), not just browser-facing ones. This is the field that drives the `trident` decision below. It is **not** the same as the i18n *audience* classification (customer-facing vs integration vs internal — see the [i18n spec](../surfaces/i18n/technical-spec.md) §1.5); keep the two axes distinct.
- **`description`** — one-line human summary.

Do **not** store the app *type* here — it's derived from the directory suffix (`*Api`/`*Web`/`*Js`/`*Py`/`*Go`), and duplicating it invites drift. Every app carries a `ubix.json`, including non-service apps like `UbixCli` (`internal`, no HTTP surface). The file is expected to grow additional fields over time (owner/team, health-probe path, etc.) — add them when a consumer needs them, not speculatively.

## External API ingress hosts (the `trident` domains)

Every API that is meant to be reachable from **outside** our network is published under a single shared wildcard namespace — the subdomain **`trident`** — on all four externally-owned API domains, in addition to its internal `example.com` host. The four domains are DNS/registrar duplicates of one another (`.com`/`.net` × two spellings) so that a typo or a lapsed registration on any one of them never takes the external API surface down:

- `example-api.com`
- `example-api.net`
- `api-example.com`
- `api-example.net`

**Rule:** an external API's **prod** ingress (`app/<App>/main-ingress.yaml`) must carry, as separate `spec.rules` host entries, its existing `*example.com` host **plus** all four `trident` hosts. The per-app label is the app's existing kebab service name (the ingress `metadata.name` / backend service `name`), so the host is `<service>.trident.<domain>`:

| App | Service label | prod host(s) on `main-ingress.yaml` |
|-----|---------------|--------------------------------------|
| `ProductApi` | `product-api` | `example.com` + `product-api.trident.{example-api.com,example-api.net,api-example.com,api-example.net}` |
| `FanClubApi` | `fanclub-api` | `example.com`¹ + `fanclub-api.trident.{…}` |
| `AffiliateApi` | `affiliate-api` | `example.com`¹ + `affiliate-api.trident.{…}` |
| `ModelSignupApi` | `modelsignup-api` | `example.com`¹ + `modelsignup-api.trident.{…}` |
| `PerformerApplicationApi` | `performer-application-api` | `example.com` + `performer-application-api.trident.{…}` |
| `StudioAdminApi` | `studio-admin-api` | `example.com` + `studio-admin-api.trident.{…}` |

¹ These three still front their prod deployment on a `example.com` host rather than `example.com`. The `trident` hosts were added alongside the existing host; the `.lan`-vs-`.prod` inconsistency is pre-existing and tracked separately — do not "fix" it as a drive-by.

**Scope — driven by `ubix.json`.** An app gets `trident` hosts iff it is an API (`*Api`, or a `*Web` serving as one) **and** its [`ubix.json`](#per-app-manifest-ubixjson) declares `"exposure": "public"`. Apps with `"exposure": "internal"` (e.g. `InternalAdminApi`, `IntegrationApi`) stay on their internal `example.com` host only. When adding a new `*Api`, set its `exposure` in `ubix.json`; if `public`, add all four `trident` hosts to its `main-ingress.yaml` at wire-up time. (Frontend `*Js` apps can be `public` too, but `trident` is an API-domain concern — they publish on their own hosts, not `trident`.)

**Environment scope.** Only **prod** (`main-ingress.yaml`) carries `trident` hosts today. `dev` / `staging` / `sandbox` ingresses keep their `*.{dev,staging,sb}example.com` hosts unchanged. Each `trident` label is a single DNS level, so one wildcard TLS cert per domain (`*.trident.example-api.com`, …) covers every app.

Each host rule is an independent `spec.rules` entry pointing at the same backend service/port as the existing host — see any external app's `main-ingress.yaml` for the exact shape.

## Git workflow

### Branches

- **`dev`** — the main working branch. **MR-only (2026-07-30):** nobody pushes it directly — every landing arrives via a merge request from a feature branch, reviewed pre-merge (blocking discussion threads). CI runs on every landing.
- **`staging`** — receives merges from `dev` at release-cut time. Used for final validation before production.
- **`prod`** — receives merges from `staging` after validation. This is what end users see.
- **`feature-*`** — all work starts on a feature branch prefixed `feature-`. Merged into `dev` when ready. Deleted after merge.

### Working on a feature

> **The authoritative git workflow lives in
> [`docs/standards/branching-and-git-workflow.md`](../standards/branching-and-git-workflow.md)**
> (branch topology, the MR-only land path + thread-disposition convention,
> sync cadence, concurrent-agent / worktree rules). This section is only the
> quick shape; if the two ever disagree, the standard wins.

```
git checkout dev
git pull --ff-only
git checkout -b feature-<short-description>

# ... commit work ...

php bin/ubix code:review  # the canonical gate (PHP + JS + Py + Go) — drive to 0

git checkout dev
git pull --ff-only
git merge --no-ff feature-<short-description>   # --no-ff preserves the per-slice grouping
git push origin dev                             # pre-push hook re-runs code:review
git branch -d feature-<short-description>
```

Feature slices land with `--no-ff` merge commits (per the branching standard) so each
slice stays a discrete group in history that can be reverted as a unit.

### Commit messages

- Short imperative subject line (~60 chars).
- Blank line, then a paragraph explaining **why** the change is needed and, if non-obvious, **what** it does. Do not narrate mechanical diffs — those are self-evident from the code.
- Reference planning docs, issues, or CHANGELOG entries by relative path or ID where useful.
- **Never skip hooks (`--no-verify`)** — there is no emergency bypass (Christopher's standing rule, 2026-07-22). If the gate is blocked by someone else's WIP, coordinate via `AGENTS-COORD.md`; never push around the gate.

### Before merging to `dev`

All merges to `dev` must pass the canonical machine review — **`php bin/ubix code:review`**, one command covering every machine-enforced standard across PHP, JS, Python, and Go. The authoritative tool list lives in the *Machine Code Review* section of [`CLAUDE.md`](../../CLAUDE.md) and is not duplicated here — the set grows (it gained three tools on 2026-07-30 alone), and a second copy only rots. Drive it to **0 violations across all tools** before pushing — the committed `.githooks/pre-push` hook refuses a red push to `dev`. `npm run review` survives only as a JS-only fallback for environments without the PHP CLI.

See [js-code-review.md](../standards/js-code-review.md) and [peck-setup.md](../standards/peck-setup.md) for tool detail.

## Release process

Releases are cut on `dev`, cascaded through `staging` and `prod`, and tagged on `prod` after production validation.

### Step 1 — Cut the release on `dev`

On a freshly synced `dev` checkout, in a single commit:

1. In `CHANGELOG.md`: rename `## [Unreleased]` to `## [YYYY.MM.N] - YYYY-MM-DD` and re-open an empty `## [Unreleased]` header above it. `N` is the 1-based counter of releases cut that month (`2026.04.1` is the first release of April 2026; the next is `2026.04.2`).
2. In root `package.json`: update `"version"` to match (e.g. `"version": "2026.04.1"`).
3. In `README.md`: update the `**Version:**` label on line 3 to the new identifier.

Commit as `Cut release YYYY.MM.N`. Push to `origin/dev`.

### Step 2 — Merge to `staging`

```
git checkout staging
git pull --ff-only
git merge --ff-only dev
git push origin staging
```

This triggers the staging deploy. Validate manually and/or with automated smoke tests.

### Step 3 — Merge to `prod`

Once staging is green:

```
git checkout prod
git pull --ff-only
git merge --ff-only staging
git push origin prod
```

This triggers the production deploy.

### Step 4 — Tag `v{version}` on `prod` after the deploy lands

Once production has confirmed the deploy succeeded (smoke tests pass, oncall hasn't rolled back):

```
git checkout prod
git pull --ff-only
git tag -a v2026.04.1 -m "Release 2026.04.1"
git push origin v2026.04.1
```

**Tag on `prod`, not on `dev` or `staging`.** The tag's meaning is "this exact code is running in production." If a release is cut on `dev` but blocked in staging and superseded by a later cut, the earlier version never ships and therefore never gets a tag. The intermediate CHANGELOG entry remains as the historical record.

### What if staging finds a blocker?

Fix on `dev` (feature branch if the fix is non-trivial, otherwise a direct commit), cut the next version (`2026.04.2`), and cascade again. The broken `2026.04.1` never gets tagged and never reaches `prod`. Its CHANGELOG entry stays as a record of what was tried; a `### Fixed` entry in `2026.04.2` explains what the blocker was.

## Versioning

uBix Core uses **CalVer** (`YYYY.MM.MICRO`) — see the [Versioning section of the README](../README.md#versioning) for the full rationale. Quick facts:

- `YYYY.MM` is the release year and month.
- `MICRO` is a 1-based counter within that month. First release of April 2026 is `2026.04.1`; second is `2026.04.2`.
- Months with no releases have no entries — the counter does not have to be continuous across months.
- Historical CHANGELOG entries retain their original SemVer identifiers (e.g., `0.50.1`, the last SemVer-era release). Do not retro-rename them.
- The version is a **monorepo-wide release marker**, not a per-app API-stability contract. SemVer MAJOR/MINOR/PATCH semantics do not apply.

## Related documentation

- [complete-php-guide.md](complete-php-guide.md) — the PHP framework in depth (`php/Ubix`, validation, DI, repositories, controllers).
- [complete-js-guide.md](complete-js-guide.md) — the JS side in depth (SvelteKit conventions, runes, load functions, hooks, server helpers, SSE endpoints, deployment).
- [architecture-models-and-datatypes.md](architecture-models-and-datatypes.md) — Model pattern, DataType hierarchy, `markChanged` concurrency model.
- [architecture-review-payloads-vs-dtos.md](architecture-review-payloads-vs-dtos.md) — why the Payload/DataType/DTO split exists instead of traditional DTOs.
- [database.md](../standards/database.md) — DB conventions (naming, column standards, soft-FK rules); [migrations.md](../standards/migrations.md) — how schema changes land.
- [unit-testing.md](../standards/unit-testing.md) — test conventions across PHP / JS / Python.
- [complete-py-guide.md](complete-py-guide.md) — the Python (`*Py`) leg; [go-coding-guidelines.md](../standards/go-coding-guidelines.md) — the Go (`*Go`) leg.
- [branching-and-git-workflow.md](../standards/branching-and-git-workflow.md) — branch topology, land paths, concurrent-agent rules.
- [js-code-review.md](../standards/js-code-review.md) — root-level JS review suite (Knip, CSpell, Prettier).
- [peck-setup.md](../standards/peck-setup.md) — PHP spell checker setup.
- [feature-test-system.md](feature-test-system.md) — planning-phase proposal for the Feature Tests system rewrite.
- [hover-preview-latency.md](hover-preview-latency.md) — planning-phase discussion of hover-preview transport options (HLS vs H5Live vs WebRTC).

## Changes to this document

Updates to repository structure, app-naming conventions, branch model, or release process should be reflected here in the same commit that makes the change. If a change is contentious or exploratory, consider a planning-phase sibling doc (see `feature-test-system.md` or `hover-preview-latency.md` for the pattern) rather than editing this one in place.
