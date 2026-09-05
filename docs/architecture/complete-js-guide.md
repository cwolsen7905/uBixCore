# uBix Core — Complete JS Architecture Guide

**Version:** 1.11
**Date:** 2026-08-24

This guide documents how the JS side of uBix Core is structured: the `*Js` apps under `app/` (SvelteKit), how they boot, the major object types a frontend engineer works with day-to-day, and the conventions the monorepo enforces.

It is the counterpart to `docs/architecture/complete-php-guide.md` for the backend. The two guides together describe every app-runtime surface in the repo.

**Named after the language, not the framework.** SvelteKit is what every `*Js` app uses today, but the `*Js` suffix conveys "JavaScript app" — a non-SvelteKit JS frontend (e.g. a React admin experiment) would still belong under `app/*Js/` and slot into the same monorepo patterns. When the framework specifics matter below, they're called out explicitly.

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Monorepo Shape — JS Side](#monorepo-shape--js-side)
3. [How a JS App Boots](#how-a-js-app-boots)
4. [Major Object Types](#major-object-types)
    - 4.1 [Route Files (`+page.svelte`, `+page.server.js`, `+layout.*`, `+server.js`)](#41-route-files)
    - 4.2 [Components (`$lib/components/`)](#42-components)
    - 4.3 [Stores (`$lib/stores/*.svelte.js`)](#43-stores)
    - 4.4 [Load Functions (server-side + universal)](#44-load-functions)
    - 4.5 [Hooks (`hooks.server.js`)](#45-hooks)
    - 4.6 [Server Helpers (`$lib/server/`)](#46-server-helpers)
    - 4.7 [SSE / API Endpoints (`routes/api/**/+server.js`)](#47-sse--api-endpoints)
    - 4.8 [Fixtures (`$lib/fixtures/`)](#48-fixtures)
    - 4.9 [Environment-specific Helpers (`$lib/whitelabels.js`, …)](#49-environment-specific-helpers)
    - 4.10 [Markup, Styling, and Assets](#410-markup-styling-and-assets)
    - 4.11 [Errors — let them bubble](#411-errors--let-them-bubble)

---

## Executive Summary

### Core principles

1. **Per-request, no shared state — on the server.** Every SvelteKit request builds its context in `hooks.server.js` → `event.locals`. No module-level singletons that cache per-user data **in server code**. The monorepo runs with a 5+ K8s pod minimum per service (per memory `project_k8s_min_pods_five.md`); anything that lives in a pod's memory must be safely replicable across pods or it breaks.

   The "on the server" scoping is load-bearing: a module-level singleton in **browser-only** code is a different thing and is legitimate, because a browser tab is inherently one user with one lifetime. `app/PerformerApplicationJs/src/lib/pepSession.svelte.js` is deliberately one — the PEP socket, its protocol state, and the WebRTC publisher have to survive client-side navigation between `/setup` and `/studio`, which component state cannot. It constructs only in the browser (guarded on `browser` from `$app/environment`, returning `null` on the server), so no per-user state ever reaches pod memory. Judge a singleton by **where it runs**, not by the fact that it's a singleton.
2. **Server boundaries are explicit.** `$lib/server/*.js` modules are compile-time-guaranteed server-only by SvelteKit — importing them from a client-only file fails the build. External API wrappers, secret access, and anything that touches an env var belong there.

   Two distinct things live behind that boundary, and only one of them SvelteKit can enforce for you: **secrets** (a bearer token, a signing key — the browser must never *hold* them) and **authority** (who the caller is and what they may do — the browser must never *assert* it). The build error covers the first. Nothing covers the second: a request body is attacker-controlled, so identity must come from a credential the server itself verifies — a signed cookie, or `event.locals` populated in `hooks.server.js` — and never from a request field. See §4.7 for the endpoint-level rule and the uBix Core incident that motivates it.
3. **Svelte 5 runes, not the legacy store API.** State lives in `$state` / `$derived` / `$effect` inside components, or in `.svelte.js` modules when state needs to cross component boundaries. `svelte/store`'s `writable` / `readable` are legacy and not used in new code.
4. **Progressive data flow.** SvelteKit's `load` functions run server-side for the initial render and then (optionally) in the browser for subsequent client-side navigation. Pages receive data as a plain `data` prop and stay declarative; side-effects like WebSocket / SSE connections live in `$effect` blocks with proper teardown.
5. **Templates and logic are one thing.** A `.svelte` file contains markup, styling, and behavior inseparably. Don't split them by layer (there's no `.html` next to the `.svelte`); split by responsibility into smaller components.

### Architecture diagram

```
┌─────────────────────────────────────────────────────────────────┐
│  Browser                                                        │
│                                                                 │
│  ┌───────────────────────────────┐   ┌────────────────────────┐ │
│  │  +page.svelte                 │   │  Svelte components     │ │
│  │  renders data from load()     │<──┤  $lib/components/*     │ │
│  │  uses $state / $effect        │   └────────────────────────┘ │
│  └───────────────────────────────┘                              │
│                ▲                      ┌────────────────────────┐│
│                │ data prop            │  $state() stores in    ││
│                │                      │  $lib/stores/*.svelte.js││
│                │                      └────────────────────────┘│
└────────────────┼────────────────────────────────────────────────┘
                 │
                 │ HTTP (SSR) or fetch (client)
                 │
┌────────────────┼────────────────────────────────────────────────┐
│  SvelteKit server (Node, K8s pod)                               │
│                                                                 │
│  ┌───────────────┐   ┌──────────────────┐   ┌─────────────────┐ │
│  │ hooks.server  │-->│  +page.server.js │   │  +server.js     │ │
│  │  .js          │   │  load() returns  │   │  GET/POST/…     │ │
│  │  builds       │   │  { ...data }     │   │  SSE endpoints  │ │
│  │  locals       │   └──────────────────┘   └─────────────────┘ │
│  └───────────────┘             │                     │          │
│                                └─────────┬───────────┘          │
│                                          ▼                      │
│                         ┌──────────────────────────┐            │
│                         │  $lib/server/*.js        │            │
│                         │  external API wrappers   │            │
│                         │  env-var access          │            │
│                         └──────────────────────────┘            │
└──────────────────────────────┼──────────────────────────────────┘
                               │
                               │ HTTP with bearer token + forwarded context
                               ▼
                     ┌─────────────────────────┐
                     │  ProductApi / other     │
                     │  PHP backends (Slim 4)  │
                     │  — covered by the PHP   │
                     │    architecture guide   │
                     └─────────────────────────┘
```

The SvelteKit pod is **stateless** w.r.t. per-user data. Every request rebuilds `event.locals` from headers + cookies; every external call carries the user's forwarded context explicitly. That's what makes the 5+ pod minimum safe.

---

## Monorepo Shape — JS Side

```
app/
├── ProductJs/          # Customer-facing site (homepage, chat room, model pages)
│   ├── src/
│   │   ├── hooks.server.js
│   │   ├── app.html        # root HTML shell (rarely edited)
│   │   ├── app.css         # global styles + Tailwind entry
│   │   ├── routes/
│   │   │   ├── +layout.svelte / +layout.server.js
│   │   │   ├── +page.svelte / +page.server.js
│   │   │   ├── model/[slug]/+page.svelte / +page.server.js
│   │   │   ├── api/models/stream/+server.js   # SSE endpoint
│   │   │   └── …
│   │   └── lib/
│   │       ├── components/                      # UI components
│   │       │   ├── Header.svelte
│   │       │   ├── ModelCard.svelte
│   │       │   └── chat-room/
│   │       │       ├── ChatPanel.svelte
│   │       │       ├── VideoArea.svelte
│   │       │       └── RecommendedModelsCarousel.svelte
│   │       ├── server/                          # server-only modules
│   │       │   └── productApi.js
│   │       ├── stores/
│   │       │   └── sidebar.svelte.js            # runes-in-module stores
│   │       ├── fixtures/                        # static seed data
│   │       │   ├── models.js
│   │       │   └── navigation.js
│   │       ├── whitelabels.js                   # URL → sitekey resolution
│   │       └── index.js                         # re-export surface
│   ├── static/                                  # served verbatim at root
│   ├── package.json
│   ├── svelte.config.js / vite.config.js
│   ├── sandbox-deploy.yaml / dev-deploy.yaml    # K8s
│   └── …
├── InternalAdminJs/   # Internal admin backend UI
│   └── …              # same shape
```

### App-naming convention

`app/` suffixes are load-bearing for the deploy pipeline (see `docs/architecture/monorepo.md` → App naming convention). `*Js` means "Node 20+ / JavaScript frontend" and signals:

- Independent npm project with its own `package.json` (and per-app `vite.config.js` / `svelte.config.js`)
- Builds via `vite build`, deploys as a Node container
- No shared `public/index.php` — each app serves at its own hostname

### Shared JS code — `js/Ubix/`

`js/Ubix/` is the JS-side analog of `php/Ubix/` — the **framework layer** that consumer apps compose. The PHP framework was reusable from day one because it was designed that way (AbstractDataType, AbstractPayload, Reader / Writer interfaces, SqlService); the JS side is on the same trajectory and held to the same bar.

#### Wiring

`js/Ubix/` is a shared library that any `*Js/` app can import as `@ubixsys/ubixcore`. It's wired through **npm workspaces**, declared at the repo root:

```json
// package.json (repo root)
{
  "workspaces": ["js/Ubix", "app/*Js"]
}
```

Running `npm install` once at the repo root installs every workspace member's deps and creates a symlink `node_modules/@ubixsys/ubixcore → ../js/Ubix` (typically hoisted to the root `node_modules/`). Each consumer app declares the dep in its own `package.json`:

```json
// app/ProductJs/package.json
"dependencies": {
  "@ubixsys/ubixcore": "*"
}
```

And imports look like any other npm package:

```js
import { formatCount, HoverVideo } from '@ubixsys/ubixcore';
```

`js/Ubix/` is intentionally a **raw-source library** — no `@sveltejs/package` build step, no `dist/`. The `package.json` `svelte` + `exports` fields point Vite's Svelte plugin straight at `src/lib/index.js`. Edits in `js/Ubix/src/lib/*` show up in every consumer's dev server immediately via the workspace symlink. There's also no separate SvelteKit *app* inside `js/Ubix/` — no `src/app.html` or `src/routes/`, so nothing to run or deploy. The library does carry its own tooling config (`svelte.config.js`, `vite.config.js`, `vitest-setup-client.js`, `eslint.config.js`, `jsconfig.json`) so its tests, lint, and `svelte-check` run as a first-class workspace in the `code:review` gate — but the shipped surface is just the raw library source under `src/lib/`.

**Important install convention:** run `npm install` from the **repo root**, not from inside an individual app's directory. The root install resolves the whole workspace graph; per-app installs work in npm 7+ but skip the root-level coordination and can leave per-app `package-lock.json` files that drift from the source-of-truth root lockfile.

#### When to put code in `js/Ubix/`

Default to **designing for reuse from the start**, not extracting reactively after a second consumer surfaces. The right time to make a component shareable is at the moment you're writing or touching it — refactoring later is more expensive and tends to bake in awkward seams.

When writing a new component, utility, or store in any `*Js/` app, answer three questions:

1. **Could the variability be prop-controlled?** If the only thing tying this code to one app is a hardcoded string, URL, taxonomy value, or branding asset — that should be a prop with a sensible default, not a literal in the file. With that in place, the code is shareable.
2. **Are external dependencies passed in or assumed?** Following the `$lib/server/*` pattern of accepting `fetch` as a parameter, components should accept their data sources as props rather than importing app-specific helpers. A component that does `import { someThing } from '$lib/server/productApi.js'` is locked to ProductJs; a component that takes a `data` prop and renders it can be used anywhere.
3. **Is the visual treatment a sensible default that another consumer would either accept or override?** If yes (it's a primitive: button, card-shell, modal, video player), it belongs in `@ubixsys/ubixcore`. If the visual treatment is the whole point and there's no override story (a brand-locked Header, a F4F-taxonomy filter bar), it stays per-app.

If all three answers point toward shareable: ship it in `@ubixsys/ubixcore` from the start, even if there's only one consumer today. The framework grows with the project; that's how `php/Ubix/` got to its current depth.

#### Decision matrix when touching code

| Situation | Action |
|---|---|
| Adding a brand new component / utility | Apply the three questions above. Shareable → write it in `@ubixsys/ubixcore` from the start. |
| Touching an existing per-app component | Re-evaluate. If light parameterization makes it shareable, move it as part of the touch. Don't speculatively extract things you're not changing. |
| Adding the second consumer of an existing per-app component | Move it. The "wait for the second consumer" rule applied to extraction; once the second consumer is real, finishing the extract is the right move. |
| Adding a one-off feature with no plausible reuse | Per-app is fine. Brand-locked surfaces, app-specific business logic, taxonomy hardcoding belong in the app — but be honest about the assessment. |

#### Per-layer guidance — what tends to belong where

The three questions above are the rule. This table is the empirical answer for common layer types — what's emerged as belonging in `@ubixsys/ubixcore` vs staying per-app as the codebase has grown. Use it to settle close calls quickly; fall back to the three questions when the layer isn't listed.

| Layer | Belongs in `js/Ubix/` | Belongs per-app | Why |
|---|---|---|---|
| **Network primitives** | API-client factory (`createApiClient({ baseUrl, fetch })`), retry / timeout helpers, error-shape conventions, CSRF / dual-cookie helpers | The list of endpoints and their request shapes per app | Mechanism is shared (how you talk to an API). Domain content (which endpoints exist) is per-app. Same split as `AbstractPdoSqlService` (Ubix) vs `FeatureFlagSqlRepository` (per-app) on the PHP side. |
| **Auth flow primitives** | Dual-cookie handling, CSRF helpers, session-check utility, `getCookie` / `setCookieHeaders` (already shipped) | The `/login` route, the login form's branding and copy | Auth mechanism is universal; login UI is brand-specific. |
| **Form / validation helpers** | `createField(validators)` factory, generic error renderer, validator combinators (the moral equivalent of PHP's `Ubix\DataType`) | Specific forms (`RegisterFlagModal`, `RuleEditorModal`, etc.) | Pattern is shared; specific form's fields + submit logic are domain content. |
| **Generic UI shells** | `<Modal>`, `<Toast>`, `<Field>`, `<Button>` — parameterizable via props, sensible brand-neutral defaults | Domain components that compose those shells (`FlagFlipConfirmationModal` composes the generic `<Modal>`) | Shells are visual primitives; the content inside is per-app. |
| **Date / number / string utilities** | `formatDate`, `formatCount` (shipped), `formatRelativeTime`, `pluralize`, etc. | Domain-specific format choices ("audit timestamps always include seconds and timezone") | Pure functions, universally useful, no domain coupling. |
| **Env / config helpers** | `resolveEnvBaseUrl(env, { dev, staging, prod, fallback })` pattern that consumes `ENV` and returns a URL | Specific env-to-URL mappings per app (`{ dev: 'https://example.com', … }`) | The resolution pattern repeats; the URL list is per-app. |
| **Stores / state shells** | Factory patterns (`createToggleStore(initial)`, `createListStore(initial)`) | App-specific stores that USE those factories | Same logic as form helpers — pattern vs instance. |
| **Routing components** | Generic shells (e.g. a `<NavBar items=[…]>` if multiple apps grow comparable nav) | Per-app route trees, per-app `+layout.svelte`, per-app `+page.server.js` | File-based routing IS per-app by definition; only the UI primitives the routes use can be shared. |
| **Feature-specific components** | — | `FeatureFlagAuditTable`, `AffiliateGrid`, `RuleEditorModal`, `ModelCard`, etc. | Domain-coupled; visual treatment IS the point. |
| **Brand / app styling** | — | Tailwind class palettes specific to each surface (admin vs F4F vs psychic etc.) | Visual identity is per-app by design. |

A note on the depth asymmetry with `php/Ubix/`: `js/Ubix/` is not going to grow to match `php/Ubix/`'s line count, and that's structural, not a deficiency. PHP needs Ubix to provide Slim with the abstractions Slim doesn't ship (Routes, Dependencies, Middleware, Theme, plus the full DataType / Payload / Repository stack). SvelteKit ships routing, layouts, SSR, the `$lib` convention, and the `+page.server.js` boundary natively — there's less surface left to wrap. The ubix-growth axis on the JS side is **patterns and mechanisms** (how to call an API, how to wire a form, how to render a modal), not **domain content** (which endpoints, which forms, which modals). Same as PHP, just at a different granularity — PHP's domain content also lives per-app (`FeatureFlagSqlRepository` is in `php/Ubix/Repository/` namespacing but is consumed by the InternalAdminApi specifically; the *reusable mechanism* — `SqlService`, `AbstractRepository` patterns — is what's framework-level).

**Don't speculatively bulk-extract.** These two rules are halves of one policy, not a contradiction: **new** code that sits at a shared seam (API client mechanics, a generic UI primitive, a test helper) is designed for reuse *from the start* — that's the opening rule of this section. **Existing** app-local code, by contrast, is extracted *reactively*: with 3 consumer apps today (ProductJs, InternalAdminJs, PerformerApplicationJs), watch what actually gets copy-pasted between them — that's the next ubix candidate. Retroactive abstraction designed against one example tends to bake in incidental shape; abstraction designed against two or three real consumers is usually right. The decision matrix above encodes both halves.

#### What's in `js/Ubix/` today

| Export | Type | Source file | What it is |
|---|---|---|---|
| `ApiError` | class | `createApiClient.js` | Error subclass thrown on non-2xx responses. `status` (number, 0 for network failures), `data` (parsed body or `{message, networkError}` for network errors). |
| `createApiClient({baseUrl, fetch?, defaultHeaders?})` | factory | `createApiClient.js` | Returns `{get, post, put, patch, del}`. JSON serialization, credential handling, `{raw: true}` option for raw Response passthrough, network-failure wrapping. |
| `CHAT_COMMANDS` | constant | `chatSocket.js` | Frozen map of legacy WS frame command codes (`SEND_CHAT: '8002'`, etc.). |
| `createChatConnection({...})` | utility | `chatSocket.js` | Browser-direct chat-room WebSocket client. Exponential-backoff reconnect (500 ms → 30 s), JSON frame parsing. Returns `{dispose, send}`. |
| `collectSetCookieHeaders(response)` | utility | `setCookieHeaders.js` | Extracts all `Set-Cookie` headers from a Response for relay through SvelteKit `cookies.set()`. |
| `formatCount(n)` | utility | `formatCount.js` | Compact integer formatting (`1234` → `'1.2K'`); empty string for non-finite / negative input. |
| `formatDate(iso, opts?, locale?)` | utility | `dates.js` | Locale-aware date formatting with em-dash fallback for nullish input. |
| `formatDateTimeForInput(iso)` | utility | `dates.js` | Converts ISO timestamp to `YYYY-MM-DDTHH:mm` for `<input type="datetime-local">`. |
| `getCookie(name)` | utility | `getCookie.js` | Client-side `document.cookie` parser; returns value or `null`. |
| `<HoverVideo>` | component | `components/media/HoverVideo.svelte` | Hover-to-play HLS video player. Lazy-loads `hls.js` on first hover; falls back to native HLS on Safari. |
| `resolveEnvBaseUrl(env, map)` | utility | `resolveEnvBaseUrl.js` | Maps `ENV` string to the corresponding base URL. Used by `+layout.server.js` loaders in both apps. |

#### Per-app code that's a candidate when next touched

Snapshot of components in `app/ProductJs/src/lib/` evaluated against the three questions. Treat as opportunistic-extraction guidance, not a refactor mandate — extract when you're already in the file.

| Per-app code | What it'd take to share |
|---|---|
| `Header.svelte`, `Footer.svelte`, `SubHeaderBar.svelte` | Parameterize `nav` items, `logo`, `links` via props; keep brand defaults app-side |
| `ModelGrid.svelte` | Restructure into a generic `<Grid items card={Snippet}>` primitive; ProductJs-specific `<ModelCard>` becomes the snippet plug-in |
| `stores/sidebar.svelte.js` | Generalize to a `createToggleStore(initial)` factory in `@ubixsys/ubixcore` |
| `chat-room/*` | WS transport + frame parsing already extracted to `@ubixsys/ubixcore` (`createChatConnection`, `CHAT_COMMANDS`). Remaining components (`VideoArea`, `ChatPanel`, `SimilarRooms`) are F4F-rendering and stay per-app for now; revisit if a second consumer materializes. |

Components that are **genuinely per-app and stay**:

| Per-app code | Why it stays |
|---|---|
| `ModelCard.svelte` | Schema-coupled to `LiveCamDto`; visual treatment (Live / HD / Premium / New / FC badges) is F4F-specific |
| `LiveCamTabs.svelte`, `CategorySidebar.svelte`, `PromoCard.svelte` | F4F-specific taxonomy / promo concept |
| `lib/whitelabels.js` | F4F whitelabel domain list |
| `lib/server/productApi.js` | Per-app external API wrapper — by convention each app has its own |
| `lib/fixtures/*` | Per-app dev fixtures |

---

## How a JS App Boots

### Dev

```bash
# One-time, after a fresh clone or after any package.json change.
# Run from the REPO ROOT — never from inside an app dir.
cd /code/ubixcore
npm install

# Per-app dev loop. CWD is the app dir, but node_modules + the
# `@ubixsys/ubixcore` workspace symlink were set up by the root install above.
cd app/ProductJs
npm run dev            # starts vite dev server with HMR
```

Vite binds to a port and serves the app with hot module replacement. SvelteKit's dev server runs server-side code (hooks, load functions, server endpoints) in-process so breakpoints / `console.log` work naturally. The sandbox deployment of a `*Js` app runs `npm run dev` inside its container (see `Dockerfile_Node_Sandbox`), which is why you'll sometimes see a Vite HMR WebSocket (`wss://{host}/?token=…`) on sandbox traffic — it's not the app's own WS, just Vite.

**uBix Core convention — don't run `npm run dev` locally.** The standard workflow is:

1. Edit a file in your editor (on the host machine).
2. The sandbox pod host-mounts `/code/ubixcore`, so Vite inside the sandbox pod picks up the change.
3. The browser tab pointed at the sandbox URL HMRs the relevant component within ~1 second.

In other words, **edit-and-save-against-sandbox-URL is the canonical dev loop**, not a local Vite server. Reasons:

- Sandbox HMR exercises the full deploy shape (Node container, SvelteKit adapter, etc.) — a local dev server can diverge in subtle ways.
- The local dev server bypasses the sandbox's nginx / cookie / domain plumbing, so workflows that depend on those (auth handshakes, cross-origin behavior, sticky sessions) only work correctly through the sandbox URL.
- The sandbox pod is shared infrastructure that's always running anyway; spinning up a second dev server on your host adds noise and port-clashing potential.

**Skip local `npm run build` for routine sanity checks** for the same reason — the sandbox catches typical regressions through HMR. Only build locally when chasing a build-specific failure that HMR can't surface (e.g. SSR hydration mismatch that only appears in a built bundle, which is rare). When you do need to build locally, run from the app directory; output goes to `.svelte-kit/output/`.

### Prod

```bash
cd app/ProductJs
npm run build          # vite build → .svelte-kit/output/
```

Each `*Js/` app's `svelte.config.js` uses **`@sveltejs/adapter-node`**. `npm run build` emits a self-contained Node HTTP server at `app/${APP_NAME}/build/index.js` (plus `build/handler.js`, `build/client/`, `build/server/`). The dev/prod images run it directly with `node build` (which Node resolves to `build/index.js`); adapter-node reads `PORT` and `HOST` env vars at startup so the same image works in any environment that sets them. The Dockerfiles pin `PORT=8080` and `HOST=0.0.0.0` to match the containerPort declared in the deploy YAMLs.

Sandbox is the exception — its Dockerfile (`Dockerfile_Node_Sandbox`) keeps `npm run dev` because the `sandbox-deploy.yaml` mounts the host source over the container filesystem; running the dev server gives Vite HMR over the mounted source. The `npm run build` step inside the sandbox image is a build-validation step only — its output is overlaid by the host mount at runtime.

### Container builds

All three Dockerfiles (`Dockerfile_Node_Sandbox`, `Dockerfile_Node_Dev`, `Dockerfile_Node_Prod`) are workspace-aware. Build context is the repo root, not the app directory:

```bash
docker build -f Dockerfile_Node_Sandbox \
  --build-arg APP_NAME=ProductJs \
  -t ubixcore:sandbox-productjs-node \
  .
```

The Dockerfiles copy the root `package.json` + `package-lock.json` + `js/Ubix/` + the target `app/${APP_NAME}/` into `/code/ubixcore/` inside the image, run `npm ci` from there (which resolves the workspace symlinks), then build from `/code/ubixcore/app/${APP_NAME}/`. A root-level `.dockerignore` keeps host `node_modules/` / `.svelte-kit/` / `build/` out of the image.

For the sandbox deployment specifically: each `app/*Js/sandbox-deploy.yaml` mounts the **entire monorepo** (`hostPath: /code/ubixcore`) at `/code/ubixcore` in the container, overlaying the image-built content with the host source. This is what makes Vite HMR work in sandbox while still resolving workspace-hoisted deps from the repo root.

### Entry points

SvelteKit has no `main.js` to chase. The "entry points" are file-location conventions under `src/`:

- `src/app.html` — the root HTML shell with `%sveltekit.head%` / `%sveltekit.body%` placeholders. Edited rarely; most layout concerns move to `routes/+layout.svelte`.
- `src/app.css` — global styles + Tailwind v4 `@import "tailwindcss"` entry.
- `src/hooks.server.js` — the first server-side code that runs on every request (see §4.5).
- `src/routes/*` — the file-based router. Every `+page.svelte` is a page; every `+server.js` is a backend handler.

Don't introduce other top-level bootstrap files. If you need to run code "before the app starts," put it in `hooks.server.js` (server-side) or `+layout.svelte` (client-side).

---

## Major Object Types

### 4.1 Route Files

SvelteKit routes are defined by **filename convention** under `src/routes/`. Each directory becomes a URL path; bracketed segments (`[slug]`) are params. There's no central route table.

| File | Runs where | Purpose |
|---|---|---|
| `+page.svelte` | browser (after SSR) | The UI for a specific URL. Receives a `data` prop from the matching `+page.server.js`. |
| `+page.server.js` | server only | Exports `load({ params, locals, fetch, url, cookies })` that runs before the page renders. Its return value is the `data` prop on `+page.svelte`. Has access to secrets and server-only modules. |
| `+page.js` | server AND browser | Universal loader. Useful when the same load logic needs to run on both initial SSR and client-side navigation. Less common than `+page.server.js`; use when the data source is a public endpoint that doesn't need server-only secrets. |
| `+layout.svelte` | browser | Wraps every page under its directory. Renders `<slot />` (Svelte 4) or children via `$props()` (Svelte 5). |
| `+layout.server.js` | server only | Layout-level loader. Its data merges into every child page's `data` prop. |
| `+server.js` | server only | Raw HTTP handler. Export `GET`, `POST`, etc. as functions returning `Response`. Used for JSON APIs, SSE streams, redirects. See §4.7. |
| `+error.svelte` | browser | Renders when a `load` throws `error()` or the route 404s. **No `*Js` app has one yet** — until one is added, those cases fall back to SvelteKit's unstyled built-in page (§4.11). |

**Typical page triplet**, from ProductJs's model page:

```
src/routes/model/[slug]/
├── +page.server.js        # load: calls ProductApi, returns {model, chat, similarModels, f4fOriginUrl}
└── +page.svelte           # renders the chatroom layout using data.*
```

The page.server.js loader:

```js
// app/ProductJs/src/routes/model/[slug]/+page.server.js
import { error } from '@sveltejs/kit';
import { env } from '$env/dynamic/private';
import { getLiveModels, getModelRoomConfig, MODEL_NOT_FOUND } from '$lib/server/productApi.js';

/** @type {import('@sveltejs/kit').PageServerLoad} */
export async function load({ params, locals, fetch }) {
    const { slug } = params;
    const context = locals.liveCamContext ?? null;

    const roomConfig = await getModelRoomConfig({ slug, context, fetch });
    if (roomConfig === MODEL_NOT_FOUND) {
        throw error(404, `Model "${slug}" not found or off-platform.`);
    }

    // ... compose additional data ...

    return {
        model: roomConfig.model,
        chat: roomConfig.chat,
        similarModels: /* ... */,
        f4fOriginUrl: /* from env */,
    };
}
```

The matching page component:

```svelte
<!-- app/ProductJs/src/routes/model/[slug]/+page.svelte -->
<script>
    import VideoArea from '$lib/components/chat-room/VideoArea.svelte';
    import ChatPanel from '$lib/components/chat-room/ChatPanel.svelte';

    let { data } = $props();
</script>

<div class="grid grid-cols-1 md:grid-cols-3">
    <VideoArea model={data.model} />
    <ChatPanel model={data.model} chat={data.chat} f4fOriginUrl={data.f4fOriginUrl} />
</div>
```

**Key rules:**

1. `load` never imports a `.svelte` component — it only returns data. Components import data via the `data` prop.
2. `load` **must not** mutate shared state. Each request gets its own `locals`; everything else flows through the return value.
3. SvelteKit's `fetch` (destructured from the event) is preferred over the global `fetch` in `load` functions — it handles same-origin cookies and avoids an extra network hop during SSR by calling handlers in-process.
4. Redirects: call `redirect(301, '/new-path')` — not a return value. (SvelteKit 2 style: `redirect()`/`error()` throw internally, so no `throw` keyword — the SvelteKit 1 `throw redirect(...)` form still works but is the old idiom.)
5. 404 / 500: `error(404, 'message')` — rendered by the nearest `+error.svelte`, or SvelteKit's built-in error page where no app-level one exists yet (§4.11).

### 4.2 Components

Components live in `src/lib/components/`. The rules below are **absolute** — they're mechanically enforceable (one-line CI checks) and remove judgement calls from review:

- **PascalCase** filename (`ModelCard.svelte`, not `model-card.svelte`).
- **One component per file.**
- **Props are declared via `$props()`** — Svelte 5 runes, not the legacy `export let` syntax.
- **Every component lives in a subdir of `lib/components/`.** No flat `.svelte` files at depth 1. **Rule applies symmetrically** to both per-app code (`app/*Js/src/lib/components/`) and the shared ubix library (`js/Ubix/src/lib/components/`). Mechanical check: `find {app/*Js,js/Ubix}/src/lib/components -maxdepth 1 -name "*.svelte" -type f` must return zero.
- **Subdir naming category depends on location**, both options kebab-case:
  - **Per-app `lib/components/`** — subdir name matches the **feature**, which usually also matches a route: `feature-flags/` (route `/feature-flags`), `login/` (route `/login`), `chat-room/` (feature rendered on `/model/[slug]`), `live-cams/` (feature rendered on `/` and `/videos/[service]`). Plus `chrome/` for cross-app shell (header, footer, sub-header, app-wide nav) — one canonical name across apps. Single-component features still get a subdir; no reorganization needed when a second component lands. Always kebab-case; the file inside stays PascalCase (`feature-flags/CategoryBadge.svelte`).
  - **ubix `js/Ubix/src/lib/components/`** — subdir name matches the **UI type**: `media/` (`HoverVideo`, future media players), `modals/` (future shared `<Modal>` shell), `feedback/` (future `<Toast>`, `<Banner>`, `<Spinner>`), `inputs/` (future `<Field>`, `<Button>`, `<FormControl>`). UI-type grouping rather than feature-name because ubix components are domain-agnostic primitives shared across apps — there's no per-app feature context to anchor the subdir to. **Don't pre-create empty UI-type categories** as speculative taxonomy; add the subdir when the first component lands in it.

The previous wording allowed "domain sub-folders **when** a set of components forms a cohesive feature." That `when` was a judgement-call leak — author and reviewer had to agree on what counted as "cohesive." The rule above closes the leak: every component, every time, in a subdir. The per-app-vs-ubix split is determined by location (mechanical), and the category-name rule within each is also mechanical — feature-name from the route tree for per-app, UI-type name from a small documented set (`media/`, `modals/`, `feedback/`, `inputs/`) for ubix.

```svelte
<!-- app/ProductJs/src/lib/components/live-cams/ModelCard.svelte -->
<script>
    let { model } = $props();

    const href = $derived(model.slug ? `/model/${model.slug}` : `/model/${model.id}`);
    const displayImageUrl = $derived(model.sampleImageUrl ?? model.thumbnailUrl);
</script>

<a {href} class="group relative block overflow-hidden rounded-md">
    <img src={displayImageUrl} alt={model.name} />
    <!-- … -->
</a>
```

**Prop typing.** JSDoc comments on the destructured `$props()` are the minimum. Every `*Js` workspace (and `js/Ubix`) runs `svelte-check` — both via its own `npm run check` and once per workspace in the `code:review` gate — so JSDoc prop types get compile-time verification everywhere (ProductJs runs it with `checkJs` + `strict` in `jsconfig.json`).

**Styling.** Tailwind v4 utility classes in `class` attributes, with `class:name={condition}` for conditional classes. Arbitrary values (`class="grid-cols-[1fr_320px]"`) are fine but **the Tailwind v4 scanner sometimes misses new arbitrary values added to freshly-created files** — if a class seems to not apply, re-run `npm run dev` or fall back to a standard utility (see § Known Gotchas — Tailwind v4 arbitrary-value scanner).

**Children / slots.**

- Svelte 5: `{@render children?.()}` — declare `let { children } = $props()`.
- Svelte 4 / legacy: `<slot />`.

Mix-and-match is fine during the Svelte 4→5 transition, but new components should use the Svelte 5 form.

### 4.3 Stores

For state that needs to cross component boundaries, use a **`.svelte.js` module** — a regular JS file that can use runes (the `.svelte.js` suffix unlocks the compiler for runes outside `.svelte` files).

```js
// app/ProductJs/src/lib/stores/sidebar.svelte.js
let open = $state(false);

export const sidebar = {
    get open() {
        return open;
    },
    toggle() {
        open = !open;
    },
    close() {
        open = false;
    }
};
```

Any component that imports `sidebar` and reads `sidebar.open` becomes reactive to changes.

**Don't use `svelte/store`'s `writable` / `readable`.** They're the legacy API, and as of 2026-08-24 they are **extinct in this repo** — `grep -rn "svelte/store" app/*Js/src js/Ubix/src` returns zero matches. (This paragraph used to name `InternalAdminJs/src/lib/stores/userData.js` as the last holdout; that file no longer exists.) There is nothing left to migrate, so the rule is now simply: never reintroduce them. `$state` in a `.svelte.js` module is the replacement in every case.

**Careful — "store" means four different things here**, and a module singleton like `sidebar` above is only one of them. The distinction that matters is singleton vs. context: module state is per-process, so **anything user-scoped must not live in a singleton**. See [§7.7](#77-shared-state--the-four-senses-of-store) for the vocabulary and the rule.

**Don't use localStorage or sessionStorage without an abstraction.** Browser-only storage needs a guard (`typeof window !== 'undefined'`) so SSR doesn't blow up. Wrap it in a store module.

### 4.4 Load Functions

SvelteKit's `load` functions are the contract between routing and data. They come in three flavors:

| File | Runs where | When |
|---|---|---|
| `+page.server.js` | server only | Every initial render + every client-side navigation to this page (client POSTs to the server endpoint). |
| `+page.js` | server for SSR, browser for SPA nav | Same trigger as above, but the code runs in both environments. |
| `+layout.server.js` / `+layout.js` | same semantics, layout scope | Runs once per layout level; data merges into every child page. |

**Signature:**

```js
/** @type {import('@sveltejs/kit').PageServerLoad} */
export async function load({ params, url, locals, cookies, fetch, request, setHeaders }) {
    // ...
    return { /* plain serializable object */ };
}
```

Destructured properties are SvelteKit's event-API surface. The most common:

- `params` — route parameters (`params.slug` for `[slug]`).
- `url` — the current `URL` object; `url.searchParams.get('x')` for query strings.
- `locals` — the per-request context populated by hooks (§4.5).
- `fetch` — SvelteKit's scoped fetch (preferred over the global `fetch`).
- `cookies` — typed cookie API with `get` / `set` / `delete`.
- `request` — the underlying `Request` (use for forwarded headers).

**Return shape.** Must be a plain object that's JSON-serializable for SSR → client hydration. No class instances, no functions, no `Date` objects (serialize to ISO strings or Unix timestamps). SvelteKit will throw at hydration time if it can't round-trip the data.

**Error vs redirect:**

```js
import { error, redirect } from '@sveltejs/kit';

if (notFound) error(404, 'Not found.');
if (shouldRedirect) redirect(301, '/new-location');
```

Both throw internally (SvelteKit 2 dropped the explicit `throw` idiom) — never `return` them, and never wrap them in a `try/catch` that would swallow the control-flow throw.

**Universal (`+page.js`) vs server (`+page.server.js`):**

- Use `+page.server.js` when the loader needs secrets, server-only modules (anything under `$lib/server/`), or privileged calls (internal APIs with bearer tokens).
- Use `+page.js` when the data comes from a public endpoint that the client can reach too — this lets the browser skip a round trip on client-side navigation by refetching directly.

**Default choice for this codebase:** `+page.server.js`. Almost every ProductApi call needs a bearer token and lives in `$lib/server/`.

### 4.5 Hooks

`src/hooks.server.js` runs **on every server-side request before any route's `load`** fires. It's the place to build per-request state that every downstream piece of code needs.

ProductJs's hook:

```js
// app/ProductJs/src/hooks.server.js
import { env } from '$env/dynamic/private';
import { resolveWhitelabel } from '$lib/whitelabels.js';

/** @type {import('@sveltejs/kit').Handle} */
export async function handle({ event, resolve }) {
    event.locals.liveCamContext = buildLiveCamContext(event);
    return resolve(event);
}

function buildLiveCamContext(event) {
    const r = event.request;

    let ip = r.headers.get('cf-connecting-ip');
    if (!ip) {
        const xff = r.headers.get('x-forwarded-for');
        if (xff) ip = xff.split(',')[0].trim() || null;
    }

    const countryCode = r.headers.get('cf-ipcountry') || env.LIVE_CAMS_DEFAULT_COUNTRY || null;
    const stateCode = r.headers.get('cf-region-code') || env.LIVE_CAMS_DEFAULT_REGION || null;

    const whitelabel = resolveWhitelabel(event.url.hostname);

    return {
        ip,
        countryCode,
        stateCode,
        sitekey: whitelabel.sitekey,
        userType: 'guest',           // placeholder until auth lands
        userId: null,
        // Fallback: the shared sandbox's `sbenv` routing cookie (sandbox-only,
        // routes API calls to a sandbox env when the request carries no cookie;
        // it is not a credential). Don't copy the hardcoded-name pattern into a
        // new app — read the default from env (e.g. SANDBOX_DEFAULT_SBENV).
        forwardCookie: r.headers.get('cookie') ?? 'sbenv=sb-christopher-olsen',
    };
}
```

**Key properties of hooks:**

1. **Per-request, not cached.** This function runs once per HTTP request. Don't put expensive work here without measuring; it's on the critical path of every page.
2. **`event.locals` is typed in `src/app.d.ts`.** Declare the shape there so IDE autocomplete works in downstream `load` functions.
3. **Multiple hooks chain via `sequence(...)`.** If you need auth + metrics + context building, each is its own `handle` and they compose with `import { sequence } from '@sveltejs/kit/hooks'`.
4. **Client-side hooks** live in `src/hooks.client.js` (rare in this codebase — almost everything interesting happens server-side).

### 4.6 Server Helpers

`src/lib/server/*.js` modules are **compile-time server-only**. SvelteKit throws a build error if a client-side bundle imports one. Use them for:

- External API wrappers (ProductJs → ProductApi, InternalAdminJs → InternalAdminApi)
- Env-var access (via `$env/dynamic/private`)
- Secret handling (bearer tokens, signing keys)
- Anything that a browser bundle shouldn't see

ProductJs's canonical example is `lib/server/productApi.js`:

```js
// app/ProductJs/src/lib/server/productApi.js
import { env } from '$env/dynamic/private';

const DEFAULT_API_BASE = 'https://example.com';

function getApiBaseUrl() {
    if (env.PRODUCT_API_BASE_URL) return env.PRODUCT_API_BASE_URL;
    switch (env.ENV) {
        case 'PROD':    return 'https://example.com';
        case 'STAGING': return 'https://example.com';
        case 'DEV':     return 'https://example.com';
        default:        return DEFAULT_API_BASE;
    }
}

export async function getLiveModels({ service, context, fetch: fetchImpl = fetch } = {}) {
    const url = `${getApiBaseUrl()}/live-models/${service}`;
    const headers = {
        Accept: 'application/json',
        Authorization: `Bearer ${env.PRODUCT_API_BEARER_TOKEN}`,
    };
    if (context) {
        if (context.ip) headers['X-Forwarded-For'] = context.ip;
        if (context.countryCode) headers['CF-IPCountry'] = context.countryCode;
        // ...
    }
    const res = await fetchImpl(url, { headers });
    if (!res.ok) throw new Error(`live-models ${res.status}`);
    const body = await res.json();
    return body.models ?? [];
}
```

**Conventions:**

1. **Every exported function accepts a `fetch` parameter** defaulting to the global. The caller passes SvelteKit's scoped fetch from their `load` function. This keeps the module testable in isolation (tests pass a mock fetch) and lets SvelteKit short-circuit SSR calls in-process.
2. **Env-var access is centralized here.** `+page.server.js` and `+server.js` files should read env through the helper, not directly — this way a "what env vars does this app need" audit is a single grep over `$lib/server/`.
3. **No state persists between calls.** Each export is a pure function over its arguments + env. If a helper needs caching, it takes the cache as an argument (or becomes a layer in front of an external cache like Memcache).
4. **Sentinel returns for expected "not found" cases** instead of exceptions: `export const MODEL_NOT_FOUND = Symbol('MODEL_NOT_FOUND');` + `if (result === MODEL_NOT_FOUND) ...` in the loader. Exceptions are for unexpected failures only.

#### Two-layer pattern: `createApiClient` factory (in ubix) + per-app endpoint list

The going-forward shape splits the request mechanics from the endpoint inventory:

- **`js/Ubix/src/lib/createApiClient.js`** — a factory that owns the request mechanics (URL construction, error-shape conventions, retry/timeout, credentials, CSRF handling). Used by every `*Js` app's API helper.
- **`app/<App>Js/src/lib/server/<app>Api.js`** — the per-app endpoint list. Imports the factory, names one function per endpoint, owns the env-aware base URL resolution.

This is the same architectural split that PHP uses with `AbstractPdoSqlService` (in `Ubix`, owns the mechanics) vs `FeatureFlagSqlRepository` (per-app, owns the domain content). Mechanism is shared, domain content isn't.

**ubix factory shape:**

```js
// js/Ubix/src/lib/createApiClient.js
export function createApiClient({ baseUrl, fetch: providedFetch }) {
    async function request(method, path, { body, params, fetch = providedFetch } = {}) {
        const url = `${baseUrl}${path}${params ? `?${new URLSearchParams(params)}` : ''}`;
        const res = await fetch(url, {
            method,
            credentials: 'include',
            headers: body ? { 'Content-Type': 'application/json' } : undefined,
            body: body ? JSON.stringify(body) : undefined,
        });
        if (!res.ok) throw new ApiError(res.status, await res.text());
        return res.json();
    }
    return {
        get:  (path, opts) => request('GET',    path, opts),
        post: (path, opts) => request('POST',   path, opts),
        put:  (path, opts) => request('PUT',    path, opts),
        del:  (path, opts) => request('DELETE', path, opts),
    };
}
```

**Per-app endpoint list:**

```js
// app/InternalAdminJs/src/lib/server/internalAdminApi.js
import { createApiClient } from '@ubixsys/ubixcore';
import { env } from '$env/dynamic/private';

function getApiBaseUrl() {
    if (env.INTERNAL_ADMIN_API_BASE_URL) return env.INTERNAL_ADMIN_API_BASE_URL;
    switch (env.ENV) {
        case 'PROD':    return 'https://example.com';
        case 'STAGING': return 'https://example.com';
        case 'DEV':     return 'https://example.com';
        default:        return 'https://example.com';
    }
}

function getApi(fetch) {
    return createApiClient({ baseUrl: getApiBaseUrl(), fetch });
}

export const getFeatureFlags = (fetch, params)   => getApi(fetch).get('/feature-flags', { params });
export const getFeatureFlag  = (fetch, flagName) => getApi(fetch).get(`/feature-flags/${encodeURIComponent(flagName)}`);
export const getAffiliates   = (fetch, params)   => getApi(fetch).get('/affiliates', { params });
export const login           = (fetch, body)     => getApi(fetch).post('/auth', { body });
export const logout          = (fetch)           => getApi(fetch).post('/logout');
// …one named export per endpoint
```

**Pages / components consume named functions, not raw URLs:**

```svelte
<script>
    import { getFeatureFlags } from '$lib/server/internalAdminApi.js';
    // …
    const flags = await getFeatureFlags(fetch, { category: 'systems' });
</script>
```

**Why this shape:**

- **Endpoint audit is one grep.** "Find every endpoint InternalAdminJs calls" = `grep "getApi(fetch)\." app/InternalAdminJs/src/lib/server/internalAdminApi.js`. Compare to the inline-`fetch`-everywhere pattern, where the same audit is N greps across N files.
- **Refactor safety.** Renaming an endpoint upstream means changing one named function's path; every caller still uses the function name. Inline URLs require an N-site rename.
- **ubix's `createApiClient` is the third-consumer extraction.** ProductJs's `productApi.js` and InternalAdminJs's `internalAdminApi.js` both need the same request mechanics; rather than writing them twice, the factory owns them and both apps depend on it. The "wait for the second consumer" extraction rule from §"When to put code in `js/Ubix/`" is satisfied.

### 4.7 SSE / API Endpoints

`routes/**/+server.js` files are raw HTTP handlers. Use them for:

- Server-Sent Events (SSE) streams — ProductJs uses these for real-time live-model list updates
- JSON APIs consumed by the same app's client-side code
- File downloads, PDF generation, anything that doesn't return HTML

```js
// app/ProductJs/src/routes/api/models/stream/+server.js
import { getLiveModels } from '$lib/server/productApi.js';

/** @type {import('@sveltejs/kit').RequestHandler} */
export async function GET({ request, url, locals, fetch: kitFetch }) {
    const service = normalizeService(url.searchParams.get('service'));
    const context = locals.liveCamContext ?? null;
    const encoder = new TextEncoder();

    const stream = new ReadableStream({
        async start(controller) {
            let closed = false;
            const send = (event) => {
                if (closed) return;
                controller.enqueue(encoder.encode(`data: ${JSON.stringify(event)}\n\n`));
            };

            // Polling loop against ProductApi. Sends `update` events
            // only when the visible-row set actually changes.
            const poll = async () => { /* ... */ };
            // ... timer + heartbeat + request.signal teardown ...
        },
        cancel() { /* teardown */ }
    });

    return new Response(stream, {
        headers: {
            'Content-Type': 'text/event-stream',
            'Cache-Control': 'no-cache, no-transform',
            'X-Accel-Buffering': 'no',
        },
    });
}
```

**SSE rules of engagement:**

1. **One upstream subscription per connection** — no shared polling loop across users. SvelteKit pods are stateless, and K8s replicates 5+ pods minimum; a shared poller would only serve the subset of users whose SSE connection happened to land on its pod.
2. **`request.signal.addEventListener('abort', ...)`** is the teardown trigger. Clean up timers, upstream connections, and anything else pod-resident when the browser disconnects.
3. **`X-Accel-Buffering: no`** header is required behind nginx reverse-proxies. Without it, the proxy buffers the response and SSE messages don't arrive in real time.
4. **Delta filtering server-side.** If the upstream data hasn't changed, don't send an event — the client doesn't need identical-payload heartbeats. The existing `models/stream` endpoint keeps the last sent id-list and only sends `update` events when the list changes.
5. **Heartbeat** (e.g. `controller.enqueue(': ping\n\n')`) every 30s. Keeps proxies, load balancers, and other middle-boxes from timing out the connection.

**Chat WS is NOT an SSE endpoint.** The chat-room WebSocket goes browser-direct to the main F4F domain's same-origin path-routing; ProductJs pods never open a chat WS. See `docs/surfaces/chat-room/technical-spec.md` §12 for the architecture.

#### Identity comes from the session, never from the request

A `+server.js` handler is a public HTTP endpoint. Anything reachable from the browser is reachable from `curl`, so **a field in the request body is a claim, not a fact**. Derive the caller from something the server verified itself:

```js
// ❌ the caller tells us who they are
export async function POST({ request }) {
    const { performer_id } = await request.json();
    return json({ hash: await api.pepHash(performer_id) });
}

// ✅ the server derives it from a verified credential
export async function POST({ cookies, fetch, request }) {
    const token = readSessionToken(cookies);           // signed; verifies or fails
    if (token === undefined) return json({ message: 'Not signed in.' }, { status: 401 });
    return json({ hash: await api.pepHash(token) });   // API derives the performer
}
```

**Why this is its own rule rather than a corollary of input validation:** validation checks *shape*, authorization checks *permission*, and a hostile value is usually perfectly well-shaped. `performer_id: 41827` passes every type check, every Payload, and every DataType — it is a valid unsigned integer. It just isn't *yours*. No amount of validation rigour catches this class, which is exactly how it shipped once here: PA's `POST /api/pep-hash` originally took a caller-supplied `performer_id` and minted a live PEP connection hash for it, so any bearer-token holder could mint broadcast credentials for any performer. MR review caught it, not the gate — treat "the request tells us who it is" as a review-blocking finding on sight.

The corollary for **fail-closed** behaviour: when the verifier can't verify (missing secret, expired signature, unreachable dependency), the answer is 401, never a fallback identity. See `app/PerformerApplicationJs/src/lib/server/session.js` and the PHP-side counterpart `Ubix\Service\PaSessionTokenService`, plus the PHP guide's §"Validation Locations".

### 4.8 Fixtures

`src/lib/fixtures/*.js` holds static seed data — navigation menus, sample models for dev, test fixtures. Plain JS modules that export data, no runtime dependencies.

```js
// app/ProductJs/src/lib/fixtures/navigation.js
export const primaryNav = [
    { label: 'Home', href: '/' },
    { label: 'Girls', href: '/?service=girls' },
    // ...
];
```

Use fixtures when:

- Data is static across environments (nav menu, category list)
- Data is a dev-only fallback for an API that isn't wired yet
- Tests need a known-good shape

**Don't** use fixtures for anything that varies per environment (config, URLs, secrets) — that's env-var territory.

### 4.9 Environment-specific Helpers

Some app-level concerns are neither server-only nor pure UI components — e.g. resolving a request hostname to a sitekey, picking a default locale. These live in `src/lib/*.js` at the top level of `$lib`:

```js
// app/ProductJs/src/lib/whitelabels.js
const WHITELABELS = [
    { pattern: /^(www\.)?flirt4free\.com$/, sitekey: 'flirt4free' },
    { pattern: /^flirt4free-com\.(dev|sb)\.ubixsys\.com$/, sitekey: 'flirt4free' },
    { pattern: /.*\.xvc\..+$/, sitekey: 'xvc' },
    // ...
];

export function resolveWhitelabel(hostname) {
    const match = WHITELABELS.find((w) => w.pattern.test(hostname));
    return match ?? { sitekey: 'flirt4free' };
}
```

**Convention:** anything that's pure (no I/O, no env-var access, no secrets) and reused across server + client code goes directly in `$lib/` (not `$lib/server/` — that would prevent client-side import). Anything that touches a secret or an env var belongs in `$lib/server/`.

**No `lib/utils/` subdir.** Pure utilities live at `lib/<name>.js` at the root of `$lib/`. The `utils/` subdir is forbidden as a uBix Core convention because it's a magnet for "everything else" with no taxonomy — every project that has one ends up with a 40-file directory of unrelated helpers. Each pure utility should justify itself by its name at the `$lib/` root: `lib/dates.js`, `lib/whitelabels.js`, `lib/statusBadge.js`, `lib/action.js`. Mechanical check: `find app/*Js/src/lib -maxdepth 2 -type d -name utils` must return zero. Cross-app utilities still live in `js/Ubix/src/lib/` per the §"When to put code in `js/Ubix/`" rules.

### 4.10 Markup, Styling, and Assets

UI is a `.svelte` file. Markup, styling, and behavior are intentionally **not** split into parallel `.html` / `.css` / `.js` files — they co-locate inside a single component, organized by responsibility instead of by language layer. If a component grows too large, split it into smaller `.svelte` components rather than splitting by layer.

#### Where markup lives

Inside any `.svelte` file (route or component), everything outside the `<script>` block is the markup:

```svelte
<script>
    let { model } = $props();
</script>

<article class="rounded-md bg-surface-elevated p-4">
    <h2 class="text-text-primary text-lg">{model.name}</h2>
    <!-- … -->
</article>
```

**Use semantic HTML.** `<article>`, `<section>`, `<nav>`, `<header>`, `<footer>`, `<button>`, `<a>` — not `<div role="…">`. Component tests use `page.getByRole(…)` (see §9.2), so semantic elements test differently from a styled `<div>` that paints the same. Semantic markup also means the a11y / SEO / screen-reader story works without extra effort. When you reach for `role="…"` to satisfy a test, swap in the real semantic element instead.

#### Where DOM references happen — `bind:this` vs. `data-js` vs. `data-testid`

JavaScript that needs a reference to a DOM node uses different mechanisms depending on **where the markup it's referencing was authored**. One uBix Core-wide standard, context-aware application.

| Context | Use | Don't use |
|---|---|---|
| **SvelteKit `.svelte` file references its own markup** (the dominant case in `app/*Js/`) | `bind:this={ref}` | `document.querySelector` / `getElementById` / class-based selectors |
| **Test queries against a `.svelte` component** | `data-testid="…"` on just the node the test needs, just for that test (Testing Library convention) | Class names or text content as test anchors (brittle) |
| **JS attached to FE-team-owned legacy HTML** (legacy admin templates, F4F-domain interop pages, any non-SvelteKit markup the FE team authors) | `data-js="<name>"` attribute as the stable selector | Class / ID selectors (silently break on rename) |

**Why the split.** The `data-js` attribute solves a real bug class: in the FE/JS contract world, FE owns the HTML in one file and JS attaches behaviour from another, so a class rename in the HTML silently breaks the JS. The attribute creates a stable selector that survives styling churn. **SvelteKit eliminates that boundary entirely** — the `.svelte` file owns both the DOM and the JS that touches it, so the contract is in-file:

```svelte
<script>
    let inputRef;
</script>
<input bind:this={inputRef} />
<button onclick={() => inputRef.focus()}>Focus</button>
```

The component has a direct reference to the node. There's no `querySelector` call, no class/ID lookup, no contract to break. If someone deletes the `<input>`, `bind:this` fails at component-author time in the same file — no class-name mystery.

**Anti-pattern: making `data-js` a uBix Core-wide rule.** Applying `data-js` blanket to SvelteKit code creates the wrong incentives:

- Authors add `data-js="…"` to nodes they think *might* be referenced, but in SvelteKit code they never are. The attrs accumulate as dead metadata with no natural cleanup pressure (absence of usage doesn't fail a build).
- Reviewers ding PRs without the attrs. New devs ask "why am I adding these?" and the answer is "because the standard says so" — exactly the wrong reason.
- New devs see the attrs and conclude "the right way to reference DOM is `document.querySelector('[data-js=…]')`" — which trains the **opposite** of the idiomatic Svelte pattern. The standard becomes mis-training.

**For `app/InternalAdminJs/` specifically:** no `data-js` work needed; all components are SvelteKit-owned. The convention only kicks in when/if a uBix Core `*Js` app starts attaching behaviour to legacy markup.

**For test queries:** prefer Testing Library's `page.getByRole(…)` / `getByLabelText(…)` semantics. `data-testid` is the fallback when no semantic affordance exists. Test attrs live on individual nodes for individual tests — they are not a project-wide convention.

**Enforcement.** Code review enforces the rules today. A future ESLint rule banning `document.querySelector` / `document.getElementById` inside `.svelte` files is a tracked follow-up — see [`docs/standards/js-code-review.md`](../standards/js-code-review.md) § Follow-ups.

#### Where styling lives

This codebase styles entirely with **Tailwind v4 utility classes**. No CSS modules, no SCSS, no styled-components, no per-component CSS-in-JS.

| Style surface | Where it lives | Used for |
|---|---|---|
| Tailwind utilities | `class="…"` on elements in `.svelte` files | The default — almost all styling |
| Theme tokens | `@theme { … }` in `src/app.css` | Brand colors, semantic palette, custom font stacks |
| Tailwind plugins | `@plugin '…'` lines after `@import 'tailwindcss'` in `app.css` | Form resets, typography utilities. InternalAdminJs uses `@tailwindcss/forms` + `@tailwindcss/typography`; ProductJs uses neither today. |
| Global rules | top of `src/app.css` (after `@theme`) | `html`/`body` base styles — things that genuinely apply globally |
| FOUC-prevention CSS | inline `<style>` in `src/app.html` | The minimum CSS needed before the JS bundle paints (background, text color). Keep this tiny. |
| Scoped `<style>` in `.svelte` | Rare — exactly one in the repo today (a fade-in keyframe in `InternalAdminJs/.../+layout.svelte`) | Use only for things that genuinely can't be a utility (custom keyframes, vendor-prefixed properties Tailwind doesn't expose). Add a one-line comment naming the reason, like the existing one does. |

**Theme tokens.** Brand colors and semantic colors live as CSS custom properties under `@theme` in `app.css`. Tailwind v4 reads `@theme` and auto-generates utility classes — defining `--color-surface-base` gives you `bg-surface-base`, `text-surface-base`, `border-surface-base` for free. **There is no `tailwind.config.js` in any `*Js/` app today;** everything Tailwind needs is in `app.css`. When adding a new color or token, add it to `@theme` (preferably with a comment naming its source — see the room-state palette block in ProductJs's `app.css`), not as a hardcoded hex inside a component class.

```css
/* app/ProductJs/src/app.css */
@import 'tailwindcss';

@theme {
    --color-surface-base: #0a0e1a;
    --color-brand-primary: #ec4899;
    /* …semantic tokens keyed to domain concepts… */
}
```

Reference tokens by their generated utility (`bg-surface-base`, `text-brand-primary`), not the raw variable (`bg-[var(--color-surface-base)]`). The utility form is shorter and survives token renames better.

**Conditional classes.** Use Svelte's `class:name={condition}` directive, not template-literal class strings:

```svelte
<!-- yes -->
<button class="btn" class:btn-primary={isPrimary} class:btn-disabled={disabled}>

<!-- no — harder to read, easy to typo -->
<button class={`btn ${isPrimary ? 'btn-primary' : ''} ${disabled ? 'btn-disabled' : ''}`}>
```

For dynamic class composition with many cases, derive the string with `$derived` and pass it through — keeps the template scannable.

**Responsive breakpoints.** Tailwind's defaults (`sm:` 640px, `md:` 768px, `lg:` 1024px, `xl:` 1280px, `2xl:` 1536px) are the project standard. No custom breakpoints have been added. Mobile-first ordering (`class="text-sm md:text-base lg:text-lg"`) is the convention.

**Dark / light mode.** ProductJs is dark-by-default; `<meta name="color-scheme" content="dark">` in `app.html` plus the surface palette in `@theme` establish that. No light-mode toggle today. Whitelabels needing a different palette would override the relevant tokens — see `docs/projects/customer-facing-refactor/` for the planned approach.

#### Where static assets live

| Asset | Location | Reference |
|---|---|---|
| Files served verbatim at the root URL | `static/` at the app root | `/robots.txt`, `/favicon.png` (in `app.html` as `%sveltekit.assets%/favicon.png`) |
| Component-coupled images / SVGs | `src/lib/assets/` and imported as ES modules | `import logoUrl from '$lib/assets/logo.png'` → use as `src={logoUrl}` |
| Inline icons | inline SVG inside the `.svelte` file (or a small `<Icon>` component) | No SVG sprite system today |

**Rule of thumb:** if the asset is referenced from a fixed URL (favicon, robots.txt, OG image), it goes in `static/`. If it's referenced from a component and benefits from Vite's content-hashing for cache-busting, it goes in `src/lib/assets/` and gets imported.

Files in `static/` keep their filename — `static/logo.png` is served at `/logo.png` with no hash. Assets imported from `src/lib/assets/` go through Vite's pipeline and get a content hash (`logo-3f8a2c1b.png`) at build time, which is what you want for cache-busting on production deploys.

#### Quick "I'm building UI, where does X go?" map

| You're adding… | Goes in… |
|---|---|
| The page's URL + initial data | `src/routes/<path>/+page.server.js` (data) + `+page.svelte` (markup) |
| A reusable component | `src/lib/components/<Name>.svelte` (or `js/Ubix/src/lib/` if cross-app — see §2 *When to put code in `js/Ubix/`*) |
| Styles for one component | Tailwind utility classes in the component's markup |
| A new brand color or design token | `@theme` block in `src/app.css` |
| A truly global style (body bg, font reset) | After `@theme` in `src/app.css` |
| A logo or image referenced from a component | `src/lib/assets/` + `import` it |
| A fixed-URL asset (favicon, OG image, robots.txt) | `app/<Name>Js/static/` |
| State that needs to cross components | `.svelte.js` module in `src/lib/stores/` (§4.3) |

---

### 4.11 Errors — let them bubble

**Default: do not catch. Let the error reach `handleError`.** A global handler is what makes that safe, and every `*Js` app must have one.

This mirrors what the PHP side already does rather than inventing a JS-specific rule. Every `*Api` app registers `addErrorMiddleware(..., $logger)` and a default error handler, and the documented controller pattern catches exactly one thing — `DtoException` from payload validation, to turn it into a 400 with field errors. Everything else bubbles. The catch counts across `php/Ubix` show the intended shape: ~125 in `Controller/`, ~90 in `Service/`, **7** in `Repository/`. Catches cluster at the boundary and thin out sharply with depth.

#### Catch only in these three cases

1. **You can give the user a better outcome.** `error(404, 'Model not found')` in a `load` — the JS twin of `DtoException` → 400. The route has lost its reason to exist and you know what to say.
2. **The data is non-critical — degrade instead of failing.** A supplementary panel that fails must not take the page down. Chat-room SRS REQ-REL-003 specifies exactly this: an upstream failure returns 200 with an empty recommended carousel. `loadLiveCamsPage.js` does it for the scheduled-shows and model-of-the-week reads. Catch, log a `warning`, return an empty value, let the page render.
3. **Information is about to be destroyed.** A streamed promise that rejects loses its `ApiError` type across the SSR boundary and is redacted in production, so `InternalAdminJs`'s `streamBoard` catches *server-side* — where the error is still intact — and passes a description through as plain data. You catch because bubbling would throw away what you know.

Anything else: bubble.

#### The anti-pattern

```js
try {
    thing();
} catch (err) {
    console.error(err); // ...and carry on with bad state
}
```

This is strictly worse than not catching. It converts a loud failure into a silent wrong render, and it is why "handle errors everywhere" is not the standard here. **If a catch block cannot answer "what better thing do I do here?", delete it.** A catch inside a component is almost always case 2 implemented in the wrong layer — push it into the `$lib/server/*` helper that owns the read.

#### `handleError` and the log contract

`hooks.server.js` exports `handleError`; it logs the failure and returns only what is safe to render. SvelteKit deliberately withholds the real message in production, so the visitor gets a generic string plus an `errorId` to quote, and that id is what ties the report to the stack in Kibana:

```js
import { createErrorHandler, createLogger } from '@ubixsys/ubixcore';

const log = createLogger({ channel: 'ProductJs' });

export const handleError = createErrorHandler({ log });
```

The mechanics live in `@ubixsys/ubixcore`'s `createErrorHandler` rather than being retyped per app — a per-app copy is how the 404 rule or the returned field names drift apart, and `App.Error` is a contract `+error.svelte` renders against. It logs `route` / `path` / `status` / the error under a generated `errorId`, and returns only `{ message, errorId }`. All four `*Js` apps wire it this way and declare the same `App.Error`, so an `errorId` means the same thing whichever app produced it.

Declare the returned shape in `app.d.ts` as `interface Error` so `+error.svelte` sees `page.error.errorId` typed.

**Never `console.log` / `console.error` directly — use `createLogger`.** Pod stdout is collected by filebeat into ELK, so an unstructured string is one opaque line you cannot filter or aggregate. `createLogger` emits Monolog's `JsonFormatter` record shape (`message` / `context` / `level` / `level_name` / `channel` / `datetime` / `extra`), and reuses Monolog's numeric levels so a `level >= 400` filter means the same thing in either language.

**What that parity means today.** JS lines reach ELK now, because filebeat collects pod stdout. The PHP side does **not** yet: its Monolog handlers use the default `LineFormatter` and write to a file under `LOGGER_PATH`, and a separate importer for those is planned. The shape is matched now so that when PHP joins, one index template and one set of Kibana queries cover both — `channel` separating `ProductJs` from `ProductApi`, `extra.uid` carrying the correlation id Monolog's `UidProcessor` already supplies — rather than two schemas needing reconciliation after the fact.

404s log at `warning`, not `error` — they are routine and high-volume, and logging them as errors buries real failures. That split reaches the traffic it is meant for: verified against `@sveltejs/kit` 2.59.1, an unmatched route throws `SvelteKitError extends Error` and so arrives here with `status: 404`, while an explicit `error(404, …)` throws `HttpError`, which `handle_error_and_jsonify` short-circuits before the hook.

**Sensitive values are redacted by key name.** `createLogger` replaces the value of any key matching `pass` / `pwd` / `secret` / `token` / `authorization` / `bearer` / `cookie` / `session` / `api_key` / `credit_card` / `card_number` / `cvv` / `ssn` / `email` / `phone` with `[redacted]`, at any depth, in `context` and inside a serialized error alike. This matters *because* of the bubble rule: `ApiError.data` is an upstream response body, so a failed registration that echoes the submitted fields would otherwise be indexed in full in Kibana. Logs are read by more people and kept longer than the request that produced them, and `docs/standards/sensitive-data-access.md` treats customer PII as a tracked category. Correlate a person's failure with the `errorId`, never by logging who they are. The ambiguous terms are spelled out rather than matched as fragments — a bare `auth` would also blank `authorId` / `authenticated`, and a bare `credit` would blank `credits`, destroying the very signal the line exists to carry.

**Redaction is by key name only — it cannot see inside a message.** `new Error(`Invalid API key: ${token}`)` logs the token verbatim, because the secret is prose, not a field. Content-scanning every message would be heuristic and would corrupt legitimate text, so the rule is upstream of the logger: **never interpolate a credential, token, or session id into an error message or a thrown string.** Put it in `context` under a name the redactor matches, or leave it out. One path that convention does **not** cover: `ApiError`'s constructor takes its `message` from the upstream response body's `message` field, so an API that returns PII there lands it in the log without any developer having written it. Redacting that would mean content-scanning prose, which is heuristic and corrupts legitimate text — so if an upstream is known to do this, fix it at the API, not in the logger.

**Client-side errors do not reach ELK.** A browser exception never touches pod stdout, so `hooks.client.js` is currently uncovered. Closing that gap is a separate decision (a reporting endpoint, or a self-hosted error tracker); until it lands, browser-side failures are visible only in the user's console.

## 5. Architectural Patterns

### 5.1 Progressive reveal + scroll triggers

Long lists (the homepage model grid, recommended carousels) render the first N items on SSR and reveal additional batches as the user scrolls. The pattern in `ModelGrid.svelte`:

- `INITIAL_COUNT` rows render on first paint (full xl-viewport: 6 cols × 10 rows).
- An `IntersectionObserver` sentinel near the grid footer reveals `BATCH_SIZE` more rows when it intersects the viewport.
- Svelte `in:fade={{duration: 200}}` on each grid item wrapper so newly-revealed cards fade in — transitions only fire on fresh client-side mounts, so the SSR-rendered initial paint stays instant.

**Sizing rule of thumb:** pick `INITIAL_COUNT` to fill a full xl-viewport (avoids a "capped at N" feeling), and pick `BATCH_SIZE` large enough that the next batch lands visibly above the fold after each scroll trigger. For the homepage that's `INITIAL_COUNT=60, BATCH_SIZE=60`.

### 5.2 Per-connection SSE poll loops

The "real-time updates" layer (model list freshness, future presence updates) uses **per-connection polling inside an SSE stream** rather than a shared module-level poller. Each open SSE connection has its own 15-second timer that calls `$lib/server/productApi.js#getLiveModels()` with that connection's context, and sends a delta event only when the visible row-set actually changes.

Why per-connection:

1. **Per-user filtering.** The upstream endpoint returns a filtered list based on the caller's geo / sitekey / auth — two users on the same pod may see different lists.
2. **K8s safety.** SvelteKit pods are stateless and replicate 5+ times. A shared poller would only serve users whose connections happened to land on its pod.
3. **Clean teardown.** When the browser disconnects, `request.signal` fires and the poll loop winds down. No orphaned timers.

Don't re-introduce shared pollers even if they look cheaper — they don't survive the scaling model.

### 5.3 Server-forwarded context

The pattern that makes the 5+ pod minimum actually work:

1. `hooks.server.js` reads per-request headers (Cloudflare geo, `X-Forwarded-For`, session cookie) and parks them on `event.locals.liveCamContext`.
2. Every `load` function and `+server.js` handler reads `locals.liveCamContext` and passes it through to the relevant `$lib/server/*` helper.
3. The helper forwards the context as HTTP headers (`X-Forwarded-For`, `CF-IPCountry`, `Cookie`) on its call to the PHP backend.
4. The PHP backend uses those headers to build its own request context, runs its filter chain, and returns a per-user-correct response.

The key guarantee: **no per-user data is cached in any SvelteKit pod's memory**. Caching lives in the external PHP backend (Memcache-backed), which is shared across pods.

### 5.4 Browser-direct WebSocket (chat room)

The chat-room WebSocket uses **same-origin path-routing on the main F4F domain** — the browser opens `wss://{F4F_ORIGIN_URL}/{chatServerId}/chat?token=…&port_to_be=…&model_id=…` and the F4F-domain nginx terminates the upgrade and reverse-proxies `/chat{NNN}/*` to the corresponding chat backend. ProductJs pods never open a chat WS themselves.

Why this matters as a pattern:

- **Pod-stateless.** File-descriptor count per ProductJs pod doesn't scale with concurrent chat viewers.
- **No WS → SSE re-encoding hop.** Native WS end-to-end.
- **Recurring infra dependency.** Every new ProductJs hostname MUST be added to the F4F-domain nginx Origin allowlist before the chat room works on it. Documented in `docs/surfaces/chat-room/technical-spec.md` §12.2 / §21.3.

The full rationale + fallback options live in `docs/surfaces/chat-room/technical-spec.md` §12. See `<ChatPanel>` in `$lib/components/chat-room/` for the reference implementation (native `WebSocket` + exponential-backoff reconnect loop, 500ms → 30s cap, proper `$effect` teardown).

### 5.5 Login flow + auth state

Login is a **modal**, not a route. `<LoginModal>` (`$lib/components/login/LoginModal.svelte`) opens from `<Header>`'s **Log In** button, posts to a SvelteKit proxy at `/api/user/authenticate/+server.js`, and on success calls `window.location.reload()`. The proxy forwards to ProductApi `/user/authenticate` (carrying the bearer token + the user's existing cookies) and **relays every `Set-Cookie` from the upstream response back to the browser unchanged**. The legacy-compatible cookies (`PHPSESSID`, `CHAT_USER`, `CHAT_PASS_MD5`, `f4f_username`, `has_logged_in`) land in the user's browser exactly as `AccountAuthenticationService::setCookiesAndSession()` produced them, so a uBix Core-issued session and a legacy-issued session are bit-for-bit identical from any consumer's perspective.

After the reload, the next SSR request flows through `hooks.server.js`, which forwards the **entire incoming `Cookie` header** to ProductApi (rather than cherry-picking individual cookie names) so every auth-relevant cookie reaches the PHP side. ProductApi's `AccountAuthenticationMiddleware` reads the session and stamps `logged_in_account` on the request — and the homepage / chat-room pages render with the user's identity for free, no client-side state shuffling required.

This is **Path A** — full page reload after success. It's deliberately the simplest possible thing that validates the full auth chain end-to-end (browser → SvelteKit proxy → ProductApi → cookie set → SSR sees user). The user-visible cost is a brief flash on login and on logout.

**Path B (planned follow-up)** is a reactive client-side auth store that swaps the header without reloading. The shape will probably be:

- A `$state`-rune-based store in `$lib/stores/auth.svelte.js` carrying `{user: ?UserDto}` (initially hydrated from SSR via a layout-level `+layout.server.js` that reads `event.locals.user`).
- A `<Header>` that subscribes to that store and conditionally renders Log-In button vs username + credit balance + logout.
- The `LoginModal`'s success handler updates the store directly (with the user JSON the API already returns) instead of reloading.
- A new `/api/user/me/+server.js` route used to refresh the store after long idle periods (e.g. session expired in another tab).
- `hooks.server.js` would gain a thin call to ProductApi `GET /user/me` (or read the session attribute via a new endpoint) to populate `event.locals.user` per request — balancing one extra round-trip per SSR call against the ergonomic win of universal SSR auth state.

Path B is **not blocking** any feature work. The two real triggers to do it: (a) the page-flash on login becomes a noticeable UX papercut, or (b) we need partial UI updates to depend on auth state without a full reload (e.g. favorites, real-time tip-menu personalisation that has to react mid-session).

### 5.6 Legacy-URL redirect-with-validation

Migration off the legacy PHP site uses server-side 301 redirects, but every redirect **validates the slug character set** before reflecting it into the `Location` header:

```js
// app/ProductJs/src/routes/+page.server.js
const LEGACY_MODEL_SLUG_RE = /^[a-zA-Z0-9_-]+$/;

const legacyModel = url.searchParams.get('model');
if (legacyModel && LEGACY_MODEL_SLUG_RE.test(legacyModel)) {
    redirect(301, `/model/${legacyModel}`);
}
```

Without the regex, a hostile `?model=../../evil` would be reflected as-is in `Location: /model/../../evil` and break the URL structure. Defense-in-depth: the destination route's `[slug]` param is also validated by the backend's `LiveCamSlug` DataType, but client-side security shouldn't depend on that.

---

## 6. Svelte 5 Runes Conventions

### 6.1 `$state`

The unit of reactive state. Works in `.svelte` files and `.svelte.js` modules.

```js
let count = $state(0);
let items = $state([]);
let config = $state({ open: false, width: 320 });
```

Reading a `$state` value inside a reactive context (template, `$derived`, `$effect`) re-runs that context when the value changes. Writing it fires the reactivity.

**Array / object mutation works.** `items.push(x)` triggers reactivity. `config.open = true` triggers reactivity. You don't need to reassign.

**Deeply nested state is reactive by default.** Svelte 5 wraps objects/arrays in proxies; property access at any depth is tracked. No explicit `Readable<T>` / `Writable<T>` wrappers.

### 6.2 `$derived`

Computed values. Re-evaluates when any `$state` it reads changes.

```js
const isLive = $derived(model.roomStatus === 'In Open');
const visibleItems = $derived(items.slice(0, revealed));
```

Use `$derived.by(() => { … })` when the computation needs a multi-line function body.

**Don't call `$derived` conditionally or inside loops.** Declare it at the top level of a component or module — same positional rules as React hooks.

### 6.3 `$effect`

Side-effects that should run when reactive dependencies change. The canonical place to open connections, register event listeners, or sync external state.

```svelte
<script>
    let { model, chat } = $props();

    let ws = $state(null);

    $effect(() => {
        if (!chat) return;

        const socket = new WebSocket(buildUrl(chat));
        ws = socket;

        return () => {
            socket.close();
            ws = null;
        };
    });
</script>
```

**Return a cleanup function.** Svelte calls it before re-running the effect (when a dependency changes) and when the component unmounts. This is the canonical place to tear down timers, sockets, event listeners.

**`untrack(() => …)`** is the escape hatch when you need to read a `$state` without subscribing to it. Common use: initializing state from a prop on mount without causing a re-run whenever the prop changes.

```js
import { untrack } from 'svelte';

$effect(() => {
    const seed = chat?.welcomeMessage;
    untrack(() => {
        messages = seed ? [{ kind: 'system', body: seed }] : [];
    });
});
```

### 6.4 `$props`

Component inputs. Replaces `export let` from Svelte 4.

```svelte
<script>
    let { model, chat = null, f4fOriginUrl } = $props();
</script>
```

- **Default values** via destructuring: `chat = null`.
- **Rest props** via `let { foo, ...rest } = $props()` — forwards unknown props to a child.
- **Typing** via JSDoc on the destructured params or a TypeScript generic argument (InternalAdminJs uses the latter with svelte-check).

### 6.5 What NOT to use in new code

| Legacy Svelte 4 / early 5 | Svelte 5 runes |
|---|---|
| `export let foo` | `let { foo } = $props()` |
| `writable(initial)` / `readable(initial)` | `$state(initial)` |
| `$: derived = …` | `const derived = $derived(…)` |
| `onMount(() => { … })` | `$effect(() => { … })` |
| `onDestroy(() => { … })` | return a cleanup function from `$effect` (or use `onDestroy` when the teardown isn't tied to reactive state — this is still OK) |

The legacy forms still work and appear in older parts of InternalAdminJs. When touching those files, migrate opportunistically; don't churn the repo just to migrate.

---

## 7. Naming Conventions

### 7.1 Files

| Type | Pattern | Example |
|---|---|---|
| Component | `PascalCase.svelte` | `ModelCard.svelte`, `ChatPanel.svelte` |
| Store (rune state module) | `camelCase.svelte.js` | `sidebar.svelte.js`, `registerModal.svelte.js` |
| Server helper | `camelCaseApi.js` or domain name | `productApi.js` |
| Fixture | `lowercase.js` | `navigation.js`, `models.js` |
| Route file | SvelteKit convention | `+page.svelte`, `+page.server.js`, `+server.js` |
| Test | mirror source + `.spec.js` | `page.svelte.spec.js`, `demo.spec.js` |
| Type declaration | `app.d.ts` | root of `src/` |
| Svelte component file | `PascalCase.svelte` | `ModelCard.svelte`, `RegisterFlagModal.svelte` |
| Utility / store / server helper / fixture (`.js`) | `camelCase.js` or single lowercase word | `formatCount.js`, `productApi.js`, `getCookie.js`, `dates.js`, `whitelabels.js` |
| Store module using Svelte 5 runes | `camelCase.svelte.js` — one lowercase word where the name is one word, camelCase where it isn't (the `.svelte.js` suffix unlocks the rune compiler) | `sidebar.svelte.js`, `registerModal.svelte.js`, `pepSession.svelte.js` |
| Subdirectory under `lib/components/` | `kebab-case` matching the feature or route name | `feature-flags/`, `chat-room/`, `live-cams/`, `chrome/`, `login/` |

### 7.2 Svelte component names

- **PascalCase always.**
- **No abbreviations** when the expanded form fits (`ModelCard` not `MdlCard`).
- **Feature subdir** when a component belongs to one feature (`chat-room/VideoArea.svelte`, not `VideoArea.svelte` at the top level — that name would imply app-wide reusability). Subdir is **kebab-case** (matching route slugs and the §4.2 absolute rule); component file inside stays PascalCase.

### 7.3 Exports

- **Named exports** over default exports for anything except the component itself. `export const sidebar = { … }`, `export function getLiveModels(…)`.
- **Component files** export the component as default by SvelteKit convention — you don't write `export default` yourself.
- **Co-locate constants** with the function that uses them. Don't centralize all app constants in a single `constants.js`.

### 7.4 Env-var names

- **UPPER_SNAKE_CASE** per standard convention.
- **Scoped by app when ambiguous:** `PRODUCT_API_BASE_URL` (not `API_BASE_URL`), `F4F_ORIGIN_URL` (not `ORIGIN_URL`).
- **Boolean-ish vars use `IS_` or `ENABLE_` prefixes:** `IS_DEV`, `ENABLE_FEATURE_X`.
- **Every env var the app reads should appear in `$lib/server/*`** (or the occasional `+server.js`), NOT scattered across `+page.server.js` files. A grep over `$lib/server/` should surface the complete list.

### 7.5 No URL obfuscation

**Endpoints in uBix Core `*Js` code are plain readable URLs.** No `atob` / `btoa` obfuscation, no base64-encoded URL fragments, no rot13, no any-of-it. The pattern exists in some legacy codebases for muscle-memory reasons (or because someone confused obfuscation with security); it's explicitly rejected for uBix Core.

Three reasons the policy is absolute:

1. **It never provided security.** Anyone with DevTools open can inspect the network request and see the resolved URL. Obfuscation in the bundle is one `console.log(atob('...'))` away from the cleartext URL.
2. **It interferes with endpoint audit.** "Find all endpoints this app calls" should be one grep over `$lib/server/`. Base64-encoded strings break that — you'd need to decode every match by hand.
3. **It interferes with refactor safety + machine code review.** A backend endpoint rename leaves obfuscated forms silently resolving to the old name until someone manually decodes them. Machine review tooling can't reason about encoded strings the same way it reasons about literal URLs.

**Mechanical check:** `grep -rE "\\b(atob|btoa)\\(" app/*Js/src js/Ubix/src` must return zero matches. (The legacy chat WebRTC code in `/mnt/webdev/.../c2c-webrtc-playback/` uses `atob` for genuine cryptographic base64 decoding of payloads — that's a distinct use case from URL obfuscation and is not in uBix Core scope. uBix Core code that needs base64 decode of binary data should use a clearly-named helper, not raw `atob`.)

If you genuinely need to encode/decode binary blobs for a real reason (image data, encrypted payloads, signed tokens), use `TextEncoder` / `TextDecoder` or a named utility that documents the intent. Raw `atob` / `btoa` for any purpose in `*Js` code requires explicit code-review sign-off with a documented justification.

### 7.6 DOM references and the FE / JS contract

**In SvelteKit components, use `bind:this` for DOM references; never `document.querySelector` / `getElementById`.** The `.svelte` file owns both the markup and the JS that touches it, so a stable selector contract is unnecessary — the component holds a direct reference to the node.

```svelte
<script>
    let inputRef;
</script>

<input bind:this={inputRef} />
<button onclick={() => inputRef.focus()}>Focus input</button>
```

This rule exists because the alternative (legacy-style `document.querySelector('.btn-primary')`) is the source of a recurring bug class — FE renames a class, JS silently breaks. Inside a `.svelte` file the boundary doesn't exist: the same file owns both the class and the JS, so the bug class can't occur.

**Mechanical check:** `grep -rE "\b(document\.querySelector|document\.getElementById)\b" app/*Js/src --include="*.svelte" --include="*.js"` must return zero matches, with two narrowly-scoped exceptions:

1. **Legacy-FE-HTML interop** — see below.
2. **Test code that explicitly needs a DOM query** — prefer `data-testid` attributes + Testing Library helpers; raw `querySelector` in `*.spec.js` is a smell.

**For JS attached to FE-team-owned legacy HTML** (legacy F4F templates, legacy admin pages, F4F-domain interop where templates live in the legacy codebase and JS lives in uBix Core), use **`data-js="<name>"`** as the stable selector contract:

```html
<!-- Legacy template owned by FE team -->
<button data-js="login-submit" class="btn-primary">Log In</button>
```

```js
// uBix Core JS attaches via the data-js selector
document.querySelector('[data-js="login-submit"]').addEventListener('click', handleSubmit);
```

The FE team agrees not to remove `data-js` attrs without coordination. Class names and IDs can change freely; `data-js` is the load-bearing contract. **This convention only applies when crossing the uBix Core / FE-team-owned-HTML boundary** — never inside a SvelteKit component, where `bind:this` is the right tool.

**For test selectors** specifically (Vitest + vitest-browser-svelte), use **`data-testid="<name>"`** (the Testing Library convention) on just the nodes a test queries. Not for production behavior; not a substitute for `bind:this`. Adding `data-testid` is opt-in per test, not pre-emptive everywhere.

**One standard, three locations:**

| Location | Rule |
|---|---|
| SvelteKit components (`app/*Js/src/**/*.svelte`) | `bind:this` for DOM refs. Ban `querySelector` / `getElementById`. |
| Tests (`*.spec.js`) | `data-testid` on opt-in nodes; query via Testing Library. |
| Legacy-FE-HTML interop (uBix Core JS attaching to non-uBix Core-owned templates) | `data-js="<name>"`; FE team contract preserves the attr across class/ID churn. |

The scope is determined by location (mechanical), not judgment. One uBix Core standard, context-aware application — same shape as the `*Web` vs `*Api` PHPCS sniff split on the PHP side.

### 7.7 Shared state — the four senses of "store"

**Cross-component state lives in a `.svelte.js` module or in context — never on a `window` `CustomEvent` bus.** Don't `window.dispatchEvent` / `addEventListener` to move state or intent between parts of the same app.

❌ A `window` event bus for an in-app concern — a dispatch here, and a listener in a component that may or may not be mounted:

```js
// The trigger, in one component:
window.dispatchEvent(new CustomEvent('ubix:login-modal-requested'));

// The listener, somewhere else entirely:
$effect(() => {
    if (typeof window === 'undefined') return;
    const handler = () => (loginOpen = true);
    window.addEventListener('ubix:login-modal-requested', handler);
    return () => window.removeEventListener('ubix:login-modal-requested', handler);
});
```

✅ Shared runes state. Typed, checked, no teardown, no SSR guard — and the modal is bound where it is mounted:

```js
// The trigger imports the state and asks for what it wants:
import { loginModal } from '$lib/stores/loginModal.svelte.js';
loginModal.request();
```

```svelte
<!-- The one mount point, in the chrome: -->
<LoginModal bind:open={loginModal.open} />
```

Four reasons, the last of which is the load-bearing one:

1. **Untyped.** `event.detail` is `any`. A renamed field fails at runtime, in one branch, in production.
2. **Invisible to `svelte-check`.** The dispatch site and the listener share no checked contract, so the gate cannot see them disagree — a typo in the event name is not an error, it's silence.
3. **Costs teardown and an SSR guard at every site.** Each listener needs `$effect` cleanup, and each dispatch needs a `typeof window` guard, because `window` doesn't exist during SSR.
4. **A dispatch with no listener is silent.** This is not hypothetical: `ubix:get-credits-requested` / `ubix:buy-credits-requested` had **four dispatch sites and zero listeners** for months. "Get Credits" and "Buy Credits" did nothing when clicked, and the bus is precisely why nobody noticed. Shared state can't hide that the same way — the state module is one file you can grep, it says who writes it and who reads it, and an export nothing reads is a Knip finding.

Cross-*app* or cross-*document* messaging (a legacy F4F page and a uBix Core island on the same document, `postMessage` to an iframe, `BroadcastChannel` between tabs) is a genuinely different problem and out of scope for this rule — there, no shared module exists to import. Reach for it only when the two sides really are separate bundles.

**Mechanical check:** `grep -rn "window\.dispatchEvent(new CustomEvent" app/*Js/src js/Ubix/src` must return zero matches. Enforced by ESLint (`no-restricted-syntax`) in `eslint.base.config.mjs`, so the `code:review` gate fails on a reintroduction rather than relying on the grep. The rule matches a `CustomEvent` constructed inline at a `window.dispatchEvent` call — `window.dispatchEvent(new PointerEvent(…))` in a spec is untouched, because simulating real browser input is how you test a drag handler that correctly listens on `window`. A CustomEvent built on an earlier line escapes the selector; the wider `grep -rn "window\.dispatchEvent"` is the net for that, and is also zero today.

#### The four senses of "store"

The `stores/` directory name is kept (renaming is churn, and Svelte's own docs use "store" loosely) — so the qualifier does the work. When you say "store" in review, say which:

| Term | What it is | Lifetime | Example |
|---|---|---|---|
| **state singleton** | Module-level `$state` in a `.svelte.js`, exported as one object | Process-wide; per-tab in the browser | `sidebar`, `registerModal`, `loginModal` |
| **state factory** | A `createX()` that returns a fresh state object per call | Whatever the caller scopes it to | `createBioModals` |
| **context** | `setContext` / `getContext` on the component tree | Per component tree — i.e. per request on the server | `auth.js` |
| **legacy store** | `svelte/store`'s `writable` / `readable` | — | **Banned, and extinct** (§4.3) |

**The singleton-vs-context rule — the one that matters:** *module state is per-process, so anything user-scoped stays in context or per-request `load` data.* A `*Js` app runs [5+ pods](#113-minimum-pod-count) and one Node process per pod serves **every** user's requests. A module-level singleton written during SSR is therefore shared across users, and one user's data renders into another's page.

So judge a singleton by **what it holds** and **where it is written**:

- **Fine as a singleton:** UI intent with no user data — is a modal open, is the sidebar expanded, which tab is selected. Written only by client-side gestures, so it is never touched during SSR.
- **Not a singleton:** anything identifying or belonging to a user — session, profile, entitlements, balances. That belongs in `event.locals` + `load` data (server) and context (component tree).
- **Grey area — client-only per-user state:** `liveCredits` holds a credit balance, which *is* user data, but only ever in the browser where the module instance is per-tab and per-user. That is legitimate, and the store makes it structurally so with a `browser` guard on the write rather than trusting each caller to remember. If a singleton holds user data, the guard is not optional.

This is the same principle as core principle 1 ("no module-level singletons that cache per-user data") — narrowed, as v1.6 put it: **judge a singleton by where it runs.** PA's `pepSession.svelte.js` is a legitimate singleton by exactly this test; a "current user" singleton would not be.

**Where they live.** New state modules go in `$lib/stores/` per [§4.3](#43-stores) — the same answer the §5 decision table gives for "state that needs to cross components". PerformerApplicationJs (six modules: `pepSession`, `pepClient`, `pepPublisher`, `roomView`, `viewport`, `createCameraTest`) and InternalAdminJs (`authState`) predate that and keep theirs at the `$lib/` root; **migrate opportunistically when you are already editing one, but don't churn an app to relocate working modules.** Note `js/Ubix`'s `createTableController.svelte.js` is not part of that drift — a shared library's surface is flat by design (consumers import it as `from 'ubix'`), and §4.3's directory is an app convention. This is also why the ESLint rule's message names no directory: it fires in every workspace, and telling a PA engineer to use a folder their app doesn't have would be worse than useless.

**Naming.** One state module per file, `camelCase.svelte.js` per §7.1, exporting a single object spelled the same as the file: `loginModal.svelte.js` → `loginModal`, with the spec mirroring it (`loginModal.spec.js`). Factories are `createX` (§7.3) — the name is how a reader tells sense 1 from sense 2 without opening the file, and the filename follows the export there too, so `createTableController.svelte.js`. (ProductJs's `stores/` briefly ran a parallel kebab-case habit — `register-modal.svelte.js` and four siblings — while the rest of the repo stayed camelCase. Normalized to camelCase on 2026-08-24: it was the majority pattern, the one §7.1 already specified, and the one that keeps a module's file, export and spec spelled identically.)

**Testing.** A state module is a plain JS object, so it needs no DOM: name its spec `*.spec.js`, **not** `*.svelte.spec.js` — see [§9.2](#92-component-tests-vitest-browser-svelte) for why that suffix matters. Because a singleton outlives an individual test, reset it in `afterEach` or it leaks an open modal into the next spec.

---

## 8. Data Flow — Request to Render

The lifecycle of a single request, from the browser's point of view:

```
  BROWSER                 SvelteKit POD (Node)              UPSTREAM
  ─────────               ────────────────────              ────────

  GET /model/example ───► hooks.server.js
                          └► handle({ event, resolve })
                              └► event.locals.liveCamContext = {
                                    ip, countryCode, sitekey,
                                    forwardCookie, …
                                 }
                          │
                          ▼
                          +layout.server.js load()
                          └► (empty today; reserved for shared state)
                          │
                          ▼
                          +page.server.js load({ params, locals, fetch })
                          └► getModelRoomConfig({
                                 slug: params.slug,
                                 context: locals.liveCamContext,
                                 fetch
                             })
                                                    HTTP + bearer + headers
                                                ──────────────────────────►
                                                                           ProductApi
                                                                           (Slim 4)
                                                                            │
                                                                            ▼
                                                                           filter chain
                                                                           + sort + DTO
                                                                            │
                                                           ◄─────────────  JSON response
                              │
                          returns { model, chat, similarModels,
                                    f4fOriginUrl }
                          │
                          ▼
                          SvelteKit renders +page.svelte SSR
                          └► +layout.svelte wraps
                              └► +page.svelte receives `data` prop
                                  └► <VideoArea model={data.model} />
                                  └► <ChatPanel
                                       model={data.model}
                                       chat={data.chat}
                                       f4fOriginUrl={data.f4fOriginUrl}
                                     />
                          │
                          ▼
                          HTML shell + hydration script
  ◄── full HTML ───────

  (initial render complete)

  $effect fires in <ChatPanel>:
  └► new WebSocket(…) ──────────────────────────────────►  F4F-domain nginx
                                                           ──► chat{NNN} backend
  ◄── WS frames ─────────────────────────────────────────
```

**Where to reach on each kind of change:**

- UI / markup → `.svelte` file
- Data shape returned to the page → `+page.server.js` load function
- External API wrapper → `$lib/server/*.js`
- Per-request context fields → `hooks.server.js` + `src/app.d.ts` (typedef)
- Route / URL structure → rename / restructure `src/routes/` directories
- Env-var access → `$lib/server/*.js` (NOT `+page.server.js` directly)

---

## 9. Testing

### 9.1 Unit tests (vitest)

`npm run test` / `npm run test:unit` in each `*Js/` workspace runs vitest. Test files live alongside source as `*.spec.js`:

```
app/ProductJs/src/demo.spec.js
app/ProductJs/src/routes/page.svelte.spec.js
```

Plain unit tests look like:

```js
import { describe, it, expect } from 'vitest';

describe('sum test', () => {
    it('adds 1 + 2 to equal 3', () => {
        expect(1 + 2).toBe(3);
    });
});
```

### 9.2 Component tests (vitest-browser-svelte)

Component tests use `vitest-browser-svelte` to render real components in a real browser environment (Playwright-driven):

```js
import { page } from '@vitest/browser/context';
import { describe, expect, it } from 'vitest';
import { render } from 'vitest-browser-svelte';
import Page from './+page.svelte';

describe('/+page.svelte', () => {
    it('renders the Live Sex Cams heading', async () => {
        render(Page);
        const heading = page.getByRole('heading', { level: 1, name: /live sex cams/i });
        await expect.element(heading).toBeInTheDocument();
    });
});
```

Queries use the `page.getBy*` locators — prefer role/name over CSS selectors so tests survive markup restructures.

**The `*.svelte.spec.js` suffix routes a spec into the browser project — and the local gate skips it.** Each app's `vite.config.js` declares two vitest projects, split purely by filename:

| Project | Environment | Includes | Run by |
|---|---|---|---|
| `client` | real chromium (Playwright) | `src/**/*.svelte.{test,spec}.{js,ts}` | CI's `js-test` job only |
| `server` | node | everything else (that pattern *excluded*) | `code:review` + the pre-push hook, and CI |

`code:review` runs vitest as `--project=server` deliberately — booting a browser per spec took the tool from seconds to minutes (see the comment on `VITEST_COMMAND_ARGUMENTS` in `MachineCodeReviewService`). The consequence is the part to internalize: **a spec named `*.svelte.spec.js` does not run locally, and does not run in the hook that gates your push.** CI is the first thing that executes it.

Both directions follow:

- **Use the suffix only when the spec genuinely needs a browser** — rendering a component, a real click or focus, anything reading layout. Anything you can assert without a DOM (a state module, a pure helper, a data transform) belongs in `*.spec.js`, where the local gate actually runs it. A store spec misnamed `*.svelte.spec.js` is a spec you won't see fail until CI.
- **When you do touch a browser spec, run the `client` project before pushing.** It is not in the gate, so nothing else will tell you: `npx vitest run --project=client` from the workspace.

Every `*Js` app and `js/Ubix` must have a node project named `server` with **at least one spec** — a workspace that runs zero tests is a `code:review` violation, not a silent pass.

### 9.3 What to test

- **Component test**: role / name visibility, prop handling, event emission, conditional rendering based on runes.
- **Unit test**: pure functions in `$lib/*` that don't depend on the DOM or SvelteKit context (string utilities, data transforms, slug validators), and **state modules** in `$lib/stores/` — request/close transitions, context replacement, the `bind:open` write-back, and any SSR guard (§7.7). Reset the singleton in `afterEach`.
- **Skip for now**: load functions (require SvelteKit's event mocking), server endpoints (same), full routing flows (use Playwright when that layer ships).

### 9.4 Running all tests

From a `*Js/` workspace:

```bash
npm run test            # vitest run (non-watch)
npm run test:unit       # vitest watch mode during dev
```

No cross-workspace "run everything" command yet — each `*Js/` app runs independently.

---

## 10. Review Suite (root level)

The monorepo has a root `package.json` with three review commands that run across every JS workspace:

```bash
# from repo root
npm run review          # runs knip, then cspell, then prettier --check
npm run review:knip     # unused files / exports / dependencies
npm run review:spell    # cspell against JS sources + selected docs/specs
npm run review:format   # prettier --check
```

Full details in `docs/standards/js-code-review.md`. Key points:

- **Knip** catches unused files, exports, and `package.json` dependencies. Run it before merging a PR that deletes/moves files.
- **CSpell** checks JS sources under `app/*Js/` + `js/Ubix/`, plus the repo's `README.md`, `CHANGELOG.md`, and specific `docs/*.md` / `specs/*.md` files listed in the glob. When you add a new `.md` under `docs/` or `specs/` that should be spell-checked, **update the glob in the root `package.json`'s `review:spell` script** — CSpell does not auto-discover docs.
- **Prettier** runs in `--check` mode (not `--write`). `npm run format` in each workspace is the fix-in-place counterpart.

The root review suite is orthogonal to per-workspace `npm run lint` (ESLint) and `npm run check` (svelte-check) — those cover app-specific concerns. Keep both green before merging.

---

## 11. Deployment

### 11.1 Two Dockerfiles, one image per environment

The repo has two Dockerfiles for the JS side:

- **`Dockerfile_Node_Sandbox`** — runs `npm run dev` in the container, mounts the source, includes HMR. Used for the sandbox environment where engineers want hot-reload without a full rebuild cycle.
- **`Dockerfile_Node_Prod`** — runs `npm run build` at image build time and `node ...adapter-output...` at runtime. No HMR, smaller image, pre-optimized bundle.

Each `*Js/` app gets its own image variant per environment (`ubixcore:sandbox-productjs-node`, `ubixcore:prod-productjs-node`, etc.). The single-image-per-app model is under review — see `project_single_js_image_initiative.md` in memory for the pending proposal to consolidate.

### 11.2 Per-app K8s YAMLs

Each `*Js/` app ships with its own deployment / ingress / service YAMLs alongside the source:

```
app/ProductJs/
├── sandbox-deploy.yaml
├── sandbox-ingress.yaml
├── sandbox-service.yaml
├── dev-deploy.yaml
├── dev-ingress.yaml
└── …
```

Env vars flow through the deployment YAML's `env:` list. Example from `sandbox-deploy.yaml`:

```yaml
env:
  - name: ENV
    value: "SANDBOX"
  - name: APP_NAME
    value: "ProductJs"
  - name: LIVE_CAMS_DEFAULT_COUNTRY
    value: "US"
  - name: F4F_ORIGIN_URL
    value: "https://example.com"
```

**Checklist when adding a new env var:**

1. Add the read (via `$env/dynamic/private`) in `$lib/server/*`.
2. Document in the relevant spec's Deployment section (e.g. `docs/surfaces/chat-room/technical-spec.md` §21.1).
3. Add the value to every `*-deploy.yaml` in the `*Js/` app.
4. If the var is environment-specific, include it in both the sandbox and dev/staging/prod YAMLs — a silent fallback default in the JS code is a foot-gun.

### 11.3 Minimum pod count

Every `*Js/` app deploys with **5+ replicas** in production. The number is an operational floor (not a ceiling — K8s auto-scales above it under load). Sandbox / dev typically runs **one** pod, which is the most common multi-pod gotcha source for new engineers; see below.

**What "5+ replicas" means.** Kubernetes runs 5+ separate copies of the app simultaneously, each in its own pod (≈ container, one app instance per pod). A load balancer randomly routes user requests across them — request 1 might hit pod A, request 2 might hit pod B, request 3 might hit pod A again.

**Why this matters for `*Js` code.** Anything one pod remembers in memory, the other 4 don't. Code that relies on in-memory state persisting across requests works in single-pod sandbox testing and then breaks immediately in prod's multi-pod world.

**Anti-pattern + fix:**

```js
// ❌ ANTI-PATTERN: module-level cache in +page.server.js or $lib/server/*.js
const userCache = new Map(); // Lives in pod memory only.

export async function load({ params, fetch }) {
    if (userCache.has(params.userId)) {
        return userCache.get(params.userId); // Pod A has it, pod B doesn't.
    }
    const user = await fetchUserFromApi(params.userId, fetch);
    userCache.set(params.userId, user);
    return user;
}
```

```js
// ✓ CORRECT: shared cache via Memcache (every pod sees the same state)
import { getCachedUser, setCachedUser } from '$lib/server/cache.js';

export async function load({ params, fetch }) {
    const cached = await getCachedUser(params.userId);
    if (cached) return cached;
    const user = await fetchUserFromApi(params.userId, fetch);
    await setCachedUser(params.userId, user, { ttl: 60 });
    return user;
}
```

**What IS safe across pods (no special handling needed):**

- **Cookies / session IDs** — live in the browser, get sent on every request.
- **Database state** — every pod reads/writes the same DB.
- **Memcache** — designed for cross-pod sharing.
- **Stateless computation** — pure functions, no memory.

**What's NOT safe (needs care):**

- **Module-level variables** (any `let` / `const` outside a function that's mutated) — pod-local, gone on next deploy.
- **In-memory caches** — see anti-pattern above. Always reach for Memcache or the DB.
- **WebSocket / SSE connections** — pin to the pod that accepted them for the connection's lifetime. That's fine, but **a reconnect after a network blip could land on a different pod**, so any per-connection state on the server side must be reconstructable from the client's state on reconnect.
- **Module-level mutation expected to persist** — even on the single-pod sandbox, a redeploy / crash / OOM kill wipes module-level vars. The assumption is unsafe regardless of pod count.

**Practical rule worth quoting:** *"Write `*Js` code as if every function call could be the first call this pod ever served."* No module-level variables expected to persist across requests. Shared state goes through Memcache, the DB, or the browser (cookies).

**Outbound load multiplier.** If our code calls an external API once per request and 5 pods are serving traffic, the external service sees 5× our intuited traffic at peak. Worth being conscious of when introducing new external dependencies — rate limits, third-party quotas, and SLA conversations all need to factor in the pod-count multiplier.

---

## 12. Known Gotchas

### 12.1 Tailwind v4 arbitrary-value scanner

Tailwind v4 scans source files for class names at build time. When you add an arbitrary-value class (e.g. `md:grid-cols-[1fr_320px]`) to a newly-created `.svelte` file, the scanner sometimes misses it until the dev server restarts. Symptom: the class doesn't apply; DevTools shows the element but the style is missing.

**Workarounds (in order):**
1. Restart `npm run dev`.
2. Use a standard utility class if one fits (e.g. `md:grid-cols-3` + explicit `md:col-span-*` on children, instead of the arbitrary-value form).
3. If you genuinely need the arbitrary value unaltered, force-generate it with Tailwind v4's `@source inline("...")` directive in `app.css` (the v4 replacement for the old `tailwind.config.js` safelist — there is no `tailwind.config.js` in this repo; see §4.10).

First ran into this during the chat-room slice A layout.

### 12.2 Vite HMR socket in sandbox

The sandbox deployment runs `npm run dev` inside the container. Vite opens its own HMR WebSocket at `wss://{productjs-host}/?token=…`. This is NOT an app WebSocket — don't confuse it with a chat-room WS or any app traffic. It only appears in sandbox; production runs `npm run build` + no HMR.

### 12.3 svelte-check runs everywhere — keep it green per workspace

Every `*Js` app (InternalAdminJs, ProductJs, PerformerApplicationJs) and `js/Ubix` has `npm run check` wired to `svelte-check`, and the `code:review` gate runs it once per workspace — so a type error in any workspace blocks every push to `dev`. ProductJs and PerformerApplicationJs check JSDoc types via `jsconfig.json` (`checkJs` + `strict`); InternalAdminJs uses a TypeScript config. When adding a component, write the JSDoc prop types with the expectation that they are compiler-verified, not documentation-only.

### 12.4 Sandbox-default env-var foot-guns

Some env vars in `$lib/server/*` fall back to a sandbox default when unset (`PRODUCT_API_BASE_URL`, `F4F_ORIGIN_URL`). This keeps local dev painless but silently points at sandbox in any deployment that forgets to set the var. **Every production-bound deployment MUST set the var explicitly** in its `*-deploy.yaml`. A scheduled cleanup agent periodically audits this; see `RemoteTrigger` routines for the current watch-list.

### 12.5 Stale browser bundle 504 after a deps re-optimize

After any change that invalidates Vite's optimized-deps cache — pod restart with a fresh image, the cache hash flipping because a dep was added / removed, restarting `npm run dev` after `npm install` — Vite generates new chunk hashes for its pre-bundled deps. The first time you load a page, requests to `/<host>/node_modules/.vite/deps/chunk-XXX.js?v=YYY` may return **504 Gateway Timeout** instead of the chunk content.

**Symptom:** the page partially loads, network tab shows a clutch of 504s on `.vite/deps/chunk-*.js?v=*` URLs, browser console shows `Cannot find module '$app/environment'` or similar SvelteKit virtual-module errors.

**Cause:** the browser still has chunk-hash references baked into a previously-rendered HTML page (or service-worker cache). Those old hashes don't exist on the new Vite dev server, so the proxy times out instead of returning a clean 404.

**Fix:** hard refresh (Cmd-Shift-R / Ctrl-Shift-F5), or close the tab and re-open the URL. After one clean load, all subsequent requests use the current chunk hashes and everything works. Anyone hitting the URL fresh from that point gets the new bundle on first paint.

If a hard refresh doesn't fix it, Vite is genuinely hung — check `kubectl logs -n {ns} -l '...'` for `Pre-bundling dependencies` lines that never finish, or for `EBUSY` / `EACCES` errors from container-uid vs host-uid mismatches on the cache files. Last-resort fix: `kubectl exec` into the pod and `rm -rf node_modules/.vite node_modules/.vite-temp .svelte-kit`, then `kubectl rollout restart` the deployment.

---

## Related Documentation

- [`complete-php-guide.md`](complete-php-guide.md) — the backend counterpart to this guide, covering the PHP framework that every `*Js/` app calls into.
- [`monorepo.md`](monorepo.md) — repo-level structure (app suffixes, deploy model, branch workflow).
- [`../standards/js-code-review.md`](../standards/js-code-review.md) — the JS tools inside the canonical `code:review` gate (CSpell, ESLint, Knip, Prettier, svelte-check, Vitest).
- [`../projects/customer-facing-refactor/hover-preview-latency.md`](../projects/customer-facing-refactor/hover-preview-latency.md) — background on the HLS / WebRTC / H5Live transport trade-offs for the `<HoverVideo>` and `<VideoArea>` components.
- [`../surfaces/chat-room/technical-spec.md`](../surfaces/chat-room/technical-spec.md) — reference for the browser-direct WebSocket transport pattern described in §5.4.
- [`../surfaces/live-models/technical-spec.md`](../surfaces/live-models/technical-spec.md) — the ProductApi endpoint ProductJs consumes for live-cam data.

## Changes to this document

Updates belong in the same commit that changes the underlying code. If a new object type emerges (e.g. a new server-helper pattern, a new kind of `+server.js` endpoint), add it to §4; if a new architectural pattern crystallizes (e.g. an optimistic-update layer), add it to §5. If you find a gotcha that would have saved you an hour, §12 is the right home.

When you make a non-trivial change to a section, append a row to the version-history table below (chronological, at the END — do not prepend or insert mid-list) so reviewers can see what shifted and when. Bumping the version number is appropriate for: tightening or relaxing a standard, adding/removing a section, renaming a convention, changing a mechanical-enforcement rule. Pure typo fixes or code-snippet refreshes don't need a bump.

---

## Document Control

| Version | Date       | Author                | Notes |
|---------|------------|-----------------------|-------|
| 1.0     | 2026-05-14 | Christopher W. Olsen | Initial formally-versioned baseline. The document existed previously as evolving informal content; today's pass adopts the same Document Control pattern as `docs/standards/migrations.md` and `docs/projects/migrations/cutover-runbook.md` so future changes are auditable in-doc rather than only in git history. Captures the current state of the JS architecture document including three same-session additions: (1) new §"Per-layer guidance — what tends to belong where" subsection inside §"When to put code in `js/Ubix/`" — 10-row table mapping common layers (network primitives, auth, forms, UI shells, date/number/string utils, env helpers, stores, routing, feature components, brand styling) to ubix-vs-per-app, plus the depth-asymmetry note explaining why `js/Ubix/` won't grow to match `php/Ubix/`'s line count (SvelteKit already provides framework abstractions Slim doesn't; ubix's growth axis is *patterns and mechanisms*, not *domain content*). (2) §4.2 component-placement rule tightened from "domain sub-folders when a set of components forms a cohesive feature" (judgement-call leak) to an absolute rule: every component lives in a subdir of `lib/components/`, no flat `.svelte` at depth 1; two subdir categories (route-named for content, canonical `chrome/` for cross-app shell). Mechanical check spelled out inline: `find app/*Js/src/lib/components -maxdepth 1 -name "*.svelte" -type f` must return zero. (3) §4.9 utility-placement rule tightened to forbid `lib/utils/` as a uBix Core convention; pure utilities live at `lib/<name>.js` at the root of `$lib/`. Mechanical check spelled out inline: `find app/*Js/src/lib -maxdepth 2 -type d -name utils` must return zero. Two example-path references in §4.2 (`ModelCard.svelte`) and §5.5 (`LoginModal.svelte`) updated to match the new rule. Real-code violations surfaced and queued as separate cleanup tasks: 1 in InternalAdminJs (`LoginForm.svelte`), 9 in ProductJs (chrome + live-cam components). |
| 1.1     | 2026-05-14 | Christopher W. Olsen | **§7 file/subdir naming conventions codified + §4.2 wording sharpened + top-line version field added for parity with the PHP guide.** Empirical audit of all `*.svelte` / `*.js` files across `app/*Js/src/lib/` and `js/Ubix/src/lib/` confirmed 100% consistency in two casing patterns (PascalCase for `.svelte`, camelCase for `.js`) that weren't documented anywhere; §7.1 table now captures both, plus the `*.svelte.js` suffix for stores using runes (was mentioned in §4.3 only) and the kebab-case rule for subdirs under `lib/components/`. §7.2 "Domain prefix" bullet rewritten as "Feature subdir" with the kebab-case rule reinforced and the stale `Chatroom/VideoArea.svelte` example corrected to `chat-room/VideoArea.svelte`. §4.2 content-component subdir rule sharpened: was "subdir name matches the route the components serve" (literally untrue for `ChatRoom/` and the future `live-cams/` which are features rendered on multi-route surfaces, not routes themselves); now reads "kebab-case subdir name matching the feature, which usually also matches a route" with examples spelled out for each case (`feature-flags/` route-matches, `chat-room/` feature-on-`/model/[slug]`, `live-cams/` feature-on-multiple-routes). Real-code violation surfaced: `app/ProductJs/src/lib/components/ChatRoom/` is PascalCase, violates the now-codified kebab-case rule; queued as a separate rename task. Top-of-document gains `**Version:**` + `**Date:**` header fields mirroring `complete-php-guide.md`'s convention so the two architecture docs are visually + structurally symmetric. |
| 1.2     | 2026-05-14 | Christopher W. Olsen | **New §7.5 — no URL obfuscation policy.** Codifies an implicit-by-absence rule (zero `atob`/`btoa` usage exists across `app/*Js/` and `js/Ubix/` today) as an explicit uBix Core policy so future contributors don't accidentally re-introduce the legacy pattern from muscle memory. Three rationale points in the section: it never provided security (DevTools inspect trivially), it interferes with endpoint audit grep, it interferes with refactor safety + machine code review tooling. Mechanical check spelled out inline: `grep -rE "\b(atob\|btoa)\(" app/*Js/src js/Ubix/src` must return zero matches. Distinguishes the policy from the genuine cryptographic base64 decode in the legacy chat WebRTC code (`/mnt/webdev/.../c2c-webrtc-playback/`), which is a different use case and out of uBix Core scope. Surfaced by Q2 of Olga's question review where the question "should endpoints be atob'ed?" prompted closing the documentation gap. Top-line version bumped 1.1 → 1.2. |
| 1.3     | 2026-05-14 | Christopher W. Olsen | **§11.3 (Minimum pod count) expanded with inline plain-English lesson.** The previous wording was a single sentence ("Every `*Js/` app deploys with 5+ replicas. This is why everything in the Architectural Patterns section is stateless.") which assumed familiarity with K8s replicas, pod-local state, and why statelessness matters. Olga flagged the section as "over my head" in her Q9, which is the signal that the doc was failing a real reader. Expanded §11.3 now covers: what 5+ replicas means concretely (load-balancing across pods, request-N might hit pod-A or pod-B), the sandbox-is-one-pod gotcha (most common multi-pod foot-gun for new engineers — code that works in single-pod sandbox breaks in 5-pod prod), an anti-pattern + fix code example (module-level `Map` cache → Memcache-backed shared cache), what IS safe across pods (cookies, DB, Memcache, stateless computation), what's NOT safe (module-level mutation, in-memory caches, the WS / SSE reconnect-to-different-pod case), and the outbound-load-multiplier consequence for external-API SLA / quota conversations. Doc bumped 1.2 → 1.3. |
| 1.4     | 2026-05-15 | Christopher W. Olsen | **Three structural-rule expansions closing gaps surfaced by the Olga review audit:** (1) New §7.6 "DOM references and the FE / JS contract" codifies the previously-implicit rule that SvelteKit components use `bind:this` for DOM refs and ban `document.querySelector` / `getElementById`; legacy-FE-HTML interop uses `data-js="<name>"` as the load-bearing FE/JS contract; test code uses `data-testid` on opt-in nodes. One uBix Core standard with three location-scoped rules; mechanical CI check spelled out. Surfaced by Q4 of the Olga question review where Olga proposed adopting `data-js` uBix Core-wide and the right answer was "no, it's location-specific — bind:this in SvelteKit, data-js only when crossing into FE-team-owned legacy templates." (2) §3 Dev workflow expanded with the uBix Core convention "don't run `npm run dev` locally" — the sandbox pod host-mounts the repo and Vite inside it picks up edits via HMR, so edit-and-save-against-sandbox-URL is the canonical dev loop. Skip local `npm run build` for routine sanity checks too. Codifies a project-memory rule that previously lived only in the agent's memory store. Surfaced by Q11. (3) §4.6 (Server Helpers) gains a new "Two-layer pattern: `createApiClient` factory + per-app endpoint list" subsection with concrete code for both layers (ubix factory + per-app `lib/server/<app>Api.js`). Mirrors the same architectural split PHP uses with `AbstractPdoSqlService` + per-app SqlRepository — mechanism in ubix, domain content per-app. Surfaced by Q8's deep-dive on the `lib/server/` gap in InternalAdminJs; the factory pattern was only mentioned in the per-layer guidance row before this expansion. Top-line version bumped 1.3 → 1.4, date 2026-05-14 → 2026-05-15. |
| 1.5     | 2026-07-28 | Christopher W. Olsen | **Standards-benchmark corrections (SB-12/13, from `docs/audits/standards-benchmark-2026-07.md`).** Repo-truth fixes: svelte-check claims corrected — every `*Js` workspace + `js/Ubix` runs it (ProductJs via `checkJs`+`strict` `jsconfig.json`), both in §4.2 and §12.3 (retitled "svelte-check runs everywhere"); `js/Ubix` shape description corrected (it does carry `svelte.config.js`/`vite.config.js`/vitest/eslint tooling configs — what it lacks is an app shell: no `src/app.html`/`src/routes/`); consumer-app count 2 → 3 (PerformerApplicationJs). Contradiction fixes: the §"When to put code in js/Ubix" design-for-reuse-from-the-start opener and the don't-speculatively-bulk-extract closer are now explicitly two halves of one policy (new-code-at-seams vs existing-app-local code); §12.1 workaround 3 no longer instructs editing a nonexistent `tailwind.config.js` (→ Tailwind v4 `@source inline()`). SvelteKit 2 idiom refresh: `redirect()`/`error()` are called, not `throw`n (§4.1, §4.4, §4.9 examples). Link rot: Related Documentation paths fixed (`complete-php-guide.md`, `../standards/js-code-review.md`, `../projects/customer-facing-refactor/hover-preview-latency.md`, `../surfaces/...`). Remaining idiom modernization (per-route `./$types` load typing, Svelte 5.16+ `class={[...]}` syntax) deliberately deferred — tracked under SB-28. |
| 1.6     | 2026-08-05 | Christopher W. Olsen | **The server boundary now covers authority, not just secrets; the singleton ban is scoped to server memory.** Two gaps closed after a PA finding (`POST /api/pep-hash` accepted a caller-supplied `performer_id` and minted a live PEP broadcast credential for it) showed the docs described *shape* validation as if it covered *permission*. (1) Core principle 2 now names the two things behind `$lib/server/`: **secrets**, which SvelteKit enforces at build time, and **authority**, which nothing enforces — identity must come from a server-verified credential (signed cookie, `event.locals`), never a request field. (2) New §4.7 subsection "Identity comes from the session, never from the request" with the ❌/✅ pair from the real incident, the reason it is its own rule rather than a corollary of input validation (a hostile id is usually well-formed — `performer_id: 41827` passes every type check; it just isn't yours), and the fail-closed corollary (401, never a fallback identity). Mirrored on the PHP side by `complete-php-guide.md` v1.5. (3) Core principle 1 narrowed to server memory: "no module-level singletons that cache per-user data" was unqualified and read as banning a legitimate pattern — PA's `pepSession.svelte.js` is deliberately a module singleton so the PEP socket and WebRTC publisher survive `/setup` → `/studio` navigation, constructing only in the browser and returning `null` on the server. The principle is about pod memory across a 5-pod minimum; the rule is now "judge a singleton by where it runs." |
| 1.7     | 2026-08-24 | Olga Geinitz | **New §7.7 (shared state, and the four senses of "store") + two stale claims corrected + the vitest project split written down.** Surfaced by the !999 review, where the reviewer first read "`/stores/` singleton" as the legacy `svelte/store` API — because this doc still taught that meaning. (1) **Stale claims removed:** §4.3 named `InternalAdminJs/src/lib/stores/userData.js` as the last legacy-store holdout and §12.4 named `src/lib/stores.js`; **neither file exists**, and `grep -rn "svelte/store" app/*Js/src js/Ubix/src` returns zero matches — the legacy API is extinct, so the rule is now "never reintroduce" rather than "migrate opportunistically". §12.4's second claim was stale too (InternalAdminJs's `+layout.server.js` no longer resolves env vars with an inline switch — it uses the shared `resolveEnvBaseUrl` helper from `@ubixsys/ubixcore`, the same as ProductJs and PerformerApplicationJs), so the whole section is gone and §12.5/§12.6 renumbered to §12.4/§12.5. (2) **New §7.7**, same shape as §7.6: cross-component state goes in a `.svelte.js` module or context, never a `window` `CustomEvent` bus — with the four reasons (untyped `detail`, invisible to `svelte-check`, per-site teardown + SSR guards, and the load-bearing one: a dispatch with no listener is silent, as `ubix:get-credits-requested` / `ubix:buy-credits-requested` proved with four dispatch sites and zero listeners for months). Mechanical grep spelled out inline. Cross-document messaging (`postMessage`, `BroadcastChannel`) explicitly scoped out. (3) **The vocabulary**, because "store" meant four things in review: state singleton / state factory / context / legacy store, in a table with lifetimes and examples — plus the rule that actually matters, *module state is per-process, so anything user-scoped stays in context or per-request `load` data*, worked through for the fine / not-fine / grey-area cases. This narrows core principle 1 the same way v1.6 did rather than restating it: judge a singleton by what it holds and where it is written. The `stores/` directory name is deliberately kept — the fix is the qualifier, not the path. (4) **New §9.2 subsection on the two vitest projects:** the `*.svelte.spec.js` suffix is the *only* thing routing a spec into the `client` (browser) project, `code:review` runs `--project=server`, and so a browser spec runs in neither the local gate nor the pre-push hook — CI's `js-test` is the first thing to execute it. Both consequences spelled out (don't misname a DOM-free spec; do run `--project=client` by hand when you touch one), plus the zero-tests-is-a-violation rule. §9.3's unit-test bullet extended to state modules. |
| 1.8     | 2026-08-24 | Olga Geinitz | **§7.7's rule promoted from convention to machine-enforced.** New ESLint `no-restricted-syntax` rule in the shared `eslint.base.config.mjs`, so `code:review` and the pre-push hook fail on a reintroduced `window` `CustomEvent` bus instead of relying on the §7.7 grep and a reviewer's eye. Bumped as a version rather than a typo fix because it changes a mechanical-enforcement rule. Requires Christopher Olsen's gate-config sign-off per CLAUDE.md — landed as its own commit so it can be dropped whole if declined. The selector is deliberately narrow: it matches a `CustomEvent` constructed inline at a `window.dispatchEvent` call and nothing else, so `element.dispatchEvent`, `window.dispatchEvent(new PointerEvent(…))` in a spec (PA's `StaticConsolePreview.svelte.spec.js` does this six times to drive a drag handler that correctly listens on `window` — the first, broader selector red-flagged all six, which is what prompted the narrowing) and `postMessage` / `BroadcastChannel` all stay legal. Known gap recorded in both the config comment and §7.7: a CustomEvent built on an earlier line escapes the selector, and the grep is the wider net. Rule documented in `docs/standards/js-code-review.md`, which gains a `### ESLint` subsection under §"Tools in the Suite" — it previously described only *proposed* rules under §Follow-ups, with no record of the shared config's enforced ones. |
| 1.9     | 2026-08-24 | Olga Geinitz | **Rune-store filenames normalized to camelCase, and §7.1 now says so unambiguously.** Writing §7.7's naming paragraph surfaced that `$lib/stores/` was internally inconsistent — five kebab-case modules (`register-modal`, `login-modal`, `credits-modal`, `live-credits`, `bio-modals`) against a repo that is otherwise camelCase throughout (`authState`, `pepSession`, `pepClient`, `pepPublisher`, `roomView`, `viewport`, `createCameraTest`, `createTableController`, `homepagePrefs`, `liveModelCount`). §7.1 had **two near-duplicate rows** for store files, one giving the pattern as `lowercase.svelte.js` and neither deciding the multi-word case, so both spellings could claim to follow it. Resolved toward camelCase — the majority pattern (10 files vs 5), what §7.1's own `camelCase.js` row already implied, and the spelling that keeps a module's file, export and spec identical (`loginModal.svelte.js` → `loginModal` → `loginModal.spec.js`). The five kebab files were renamed in the same commit, with 21 importing files updated; `createX` factories keep `createX` filenames, so the file follows the export there too. Both §7.1 rows now read `camelCase.svelte.js`, and the stale `userData.svelte.js` example is replaced with files that exist — it named the legacy store that v1.7 had just finished deleting from §4.3. §7.7's naming paragraph records the normalization instead of presenting the split as an open question. |
| 1.10    | 2026-08-24 | Olga Geinitz | **§7.7's ESLint rule signed off, and its message made location-neutral + a `stores/` disposition note added.** Christopher Olsen signed off the `no-restricted-syntax` rule introduced in v1.8, on the grounds that it is exactly the pathway `docs/standards/code-review.md` §14 prescribes ("any finding class that turns out to be mechanical gets promoted out of the review and into the gate") and §51's triage for a gate-able mechanical class, with `migrations` / `changelog` / `ci-config` as precedent — and that it is the JS twin of `phpcs.xml`'s `ForbiddenFunctions`, which already maps a banned global to a prescribed seam on the PHP side (`curl_init` → `CurlHttpClient::sendRequest`, `is_null` → `=== null`). Two consequential wording fixes: the rule fires in **every** workspace, but its message and the gate doc's rule table both told the developer to use `$lib/stores/*.svelte.js` — a directory 2 of the 4 `*Js` apps do not have, so a PerformerApplicationJs engineer tripping the rule was being pointed at a folder that does not exist in their app. Both strings now say `.svelte.js` and name no location; §7.7's opening rule was already location-neutral. New **"Where they live"** paragraph in §7.7 carries the disposition the deleted §12.4 used to (v1.7): `$lib/stores/` is prescriptive for **new** modules per §4.3, PA (6 modules at the `$lib/` root) and InternalAdminJs (`authState`) predate it and are migrated opportunistically rather than by churning a working app, and `js/Ubix`'s `createTableController` is explicitly not drift because a shared library's surface is flat by design. Repo count at this revision: 16 rune state modules, 8 of them in ProductJs's `stores/` (the only compliant app), 8 outside it. |
| 1.11    | 2026-08-24 | Christopher W. Olsen | **New §4.11 — errors bubble to `handleError`, and JS logs become structured.** The guide previously covered only `error()` / `redirect()` *syntax*; nothing said when to catch, when to let an error bubble, or where an unexpected one goes. In practice no `*Js` app had a `handleError` hook and none had an `+error.svelte`, so §4.1/§4.4's claim that errors render in the nearest `+error.svelte` was untrue everywhere (both lines are corrected here rather than left contradicting this row) and an unexpected error was redacted in production and logged nowhere. §4.11 states the rule uBix Core already follows on the PHP side rather than inventing a JS one — a global handler is mandatory and you catch only where you can beat it, evidenced by the catch distribution across `php/Ubix` (~125 `Controller/`, ~90 `Service/`, **7** `Repository/`) and the documented controller pattern that catches `DtoException` alone. Three cases justify a catch (better outcome for the user; non-critical data that should degrade per chat-room SRS REQ-REL-003; information destroyed by bubbling, as `InternalAdminJs`'s `streamBoard` shows); everything else bubbles. Names the anti-pattern explicitly — a `catch` that logs and continues with bad state is worse than none, because it turns a loud failure into a silent wrong render. Adds the logging contract: new `createLogger` in `js/Ubix` emits Monolog's `JsonFormatter` record shape (`message`/`context`/`level`/`level_name`/`channel`/`datetime`/`extra`), so filebeat lands PHP and JS in one ELK index with `channel` separating apps and `extra.uid` carrying the correlation id Monolog's `UidProcessor` already supplies — direct `console.log`/`console.error` is out, since an unstructured line cannot be filtered or aggregated in Kibana. 404s log at `warning` so routine crawler traffic cannot bury real failures. Records the open gap: browser errors never reach pod stdout, so `hooks.client.js` is uncovered until a reporting endpoint or self-hosted tracker is chosen. Numbered **4.11** deliberately rather than inserting at 4.10 — three docs outside this guide cite §4.10 as "Markup, Styling, and Assets" (`docs/audits/standards-benchmark-2026-07.md`, `docs/audits/2026-05-monorepo/02-js-architecture/README.md`, `docs/projects/performer-app-redesign/status.md`), and renumbering would have broken all three. |
