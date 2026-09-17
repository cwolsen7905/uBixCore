# uBixCore — Complete JS Architecture Guide

> **Status:** v2.0 — rewritten 2026-09-16 for **React 19 + React Router v8 (framework mode) +
> TypeScript**. v1.x documented SvelteKit and the `app/*Js` apps that used to live in this
> repository; both premises are gone. uBixCore's JS half became React at `v0.3.0`, and OSS-10 moved
> every product app to its host repo, so this guide now describes **how a host builds its frontend
> on uBixCore** rather than how this repo builds one.

## What this covers

uBixCore ships `@ubixsys/ubixcore` — a React component library published from `ts/Ubix/`. It does
**not** ship frontend applications. Those live in host repos as `app/<Name>Js/`, and this guide is
the contract they follow.

The reference implementations are Sowing.me's two apps in the KITG host repo (`SowingMeJs`,
`SowingMeAdminJs`). Everything below is drawn from building them; where a rule exists because
something broke, the guide says so, because that is the part worth remembering.

**This is the framework profile.** For branch topology and release flow see
[`../standards/branching-and-git-workflow.md`](../standards/branching-and-git-workflow.md)
§ Repository Profiles.

## Table of contents

1. [Core principles](#1-core-principles)
2. [Where JS lives](#2-where-js-lives)
3. [App shape](#3-app-shape)
4. [Route modules](#4-route-modules)
5. [Data flow](#5-data-flow)
6. [State](#6-state)
7. [Styling](#7-styling)
8. [The server boundary](#8-the-server-boundary)
9. [Testing](#9-testing)
10. [The gate](#10-the-gate)
11. [Deployment](#11-deployment)
12. [Gotchas](#12-gotchas)

---

## 1. Core principles

1. **Server-render by default.** `ssr: true` in `react-router.config.ts`. A route's `loader` runs
   before first paint, so the page arrives with its data. Turn SSR off per route only when the route
   genuinely cannot be rendered server-side, not to avoid writing a loader.
2. **The loader is the data layer.** If a component fetches on mount, ask why it is not a loader.
   Fetch-on-mount renders an empty state first, costs a round trip, and cannot be server-rendered.
3. **Derive, don't mirror.** State that can be computed during render must not be stored and synced
   from an effect. React's `react-hooks/set-state-in-effect` rule enforces this and it is not
   advisory — every instance it flagged during the Sowing.me migration was a real defect.
4. **Effects are for external systems**, in the literal sense: the DOM, `localStorage`, a media
   query, a socket. Reading one is `useSyncExternalStore`; writing one is `useEffect`.
5. **Types are generated, not hand-written.** `react-router typegen` produces per-route types from
   `routes.ts`. Import `Route` from `./+types/<route>` and let params, loader data and actions type
   themselves end to end.
6. **The framework knows nothing about the product.** `@ubixsys/ubixcore` must pass the boundary
   test in the root `CLAUDE.md`: would an unrelated product want this verbatim? Entitlement,
   paywalls and domain vocabulary stay in the host.

## 2. Where JS lives

```
ubixcore/
  ts/Ubix/                  @ubixsys/ubixcore — the published React library
    src/index.ts            barrel; everything exported passes the boundary test
    src/media/              reserved for WHIP/WHEP/HLS transport primitives
    tsup.config.ts          publish-time build -> dist/ (esm + .d.ts)

<host>/
  app/<Name>Js/             one React Router app per frontend
  ts/<Host>/                optional: cross-product code that is NOT framework material
```

`ts/` names the ecosystem and mirrors `php/Ubix/`. The `*Js` app suffix is an **app-type marker**
(`*Api` PHP, `*Py` Python, `*Js` frontend) wired into `APP_NAME`, manifest names, ingress hostnames
and `bin/deploy.sh` — it is not a language claim and must not be renamed to `*Ts`.

### Consuming the library

`@ubixsys/ubixcore` ships **built**: `exports` resolves to `dist/*.js` + `dist/*.d.ts`, produced by
`tsup` at `prepack`. `react` and `react-dom` are peer dependencies (`^19.2.7`) — the host owns them.

It is deliberately *not* a raw-source package. That pattern only pays off when the package lives in
the same repo as its consumer (an npm workspace); this one is consumed cross-repo through a
registry, so a host always installs a tarball and never sees source. Note also that
`publishConfig.exports` — the usual trick for pointing at source in-repo and `dist` when published —
is a **pnpm/Bun** feature that npm silently ignores while warning it will hard-error. Using it
publishes a manifest pointing at `src/` files that are not in the tarball.

To work on a component against a live host app: `npm run dev` here (`tsup --watch`) plus `npm link`.

## 3. App shape

```
app/<Name>Js/
  app/
    root.tsx              document shell, root loader, ErrorBoundary
    routes.ts             route config
    routes/               one module per route + its .module.css
    components/           shared components
    lib/                  api, session, theme, hooks
    styles/               global CSS (tokens)
  public/                 static assets served at /
  react-router.config.ts  { ssr: true }
  vite.config.ts          reactRouter() + tailwindcss() + tsconfigPaths()
  vitest.config.ts        jsdom; deliberately WITHOUT the React Router plugin
  tsconfig.json           includes .react-router/types
  .react-router/          generated types — gitignored, and prettier-ignored
```

`routes.ts` is explicit configuration, not filesystem convention:

```ts
import { type RouteConfig, index, layout, route } from '@react-router/dev/routes';

export default [
  layout('routes/public.tsx', [
    route('c/:slug', 'routes/creator.$slug.tsx'),
    route('login', 'routes/login.tsx'),
  ]),
  layout('routes/shell.tsx', [
    index('routes/home.tsx'),
    route('settings', 'routes/settings.tsx'),
  ]),
] satisfies RouteConfig;
```

**Split public and authenticated layouts from the start.** An app with one layout that gates on
"is there a user" will eventually gate something that must stay public — a shareable profile, a
password-reset page, the login page itself. That exact bug shipped in Sowing.me: a single layout
blurred its content behind a login modal on every route, including the public creator page the
marketing site links to. Two layouts cost nothing on day one and are awkward to retrofit.

## 4. Route modules

A route module exports some subset of:

| Export | Runs | Purpose |
|---|---|---|
| `loader` | server | Data for the route, before render |
| `action` | server | Mutations (form POST) |
| `default` | both | The component; receives `loaderData` |
| `meta` | both | `<title>`, meta tags |
| `ErrorBoundary` | both | Renders thrown responses and errors |
| `headers` | server | Response headers |

```tsx
import { data, redirect } from 'react-router';
import type { Route } from './+types/creator.$slug';

export async function loader({ params, request }: Route.LoaderArgs) {
  const res = await fetch(`${apiBaseUrl()}/creators/${encodeURIComponent(params.slug)}`, {
    headers: { 'Content-Type': 'application/json', cookie: request.headers.get('cookie') ?? '' },
  });

  if (!res.ok) throw data('Not found', { status: 404 });

  const profile = await res.json();

  // Canonicalise a retired slug.
  if (profile.slug !== params.slug) throw redirect(`/c/${profile.slug}`, 301);

  return { profile };
}

export function meta({ loaderData }: Route.MetaArgs) {
  return [{ title: `${loaderData?.profile.displayName} | Example` }];
}

export default function CreatorPage({ loaderData }: Route.ComponentProps) {
  return <h1>{loaderData.profile.displayName}</h1>;
}
```

Notes that cost time to learn:

- **`throw`, don't return**, for `data(...)` and `redirect(...)`. They are control flow.
- **`meta` receives `loaderData`, not `data`.** v7 called it `data`; v8 renamed it. The generated
  types catch this, which is the argument for using them.
- **Status codes are explicit.** `redirect(url, 301)` — the default is 302, and for a canonical URL
  that silently loses the SEO value the redirect exists for.

## 5. Data flow

```
 request ──► root loader ──► route loader(s) ──► SSR HTML ──► hydrate
                 │                                              │
                 └── session, config ──────────────────────────►│
                                                                ▼
                                       revalidate() re-runs loaders, no reload
```

**Forward the cookie explicitly.** A loader runs on the server and has no browser cookie jar. Read
it off the inbound request and pass it on, or every authenticated fetch is anonymous:

```ts
const cookie = request.headers.get('cookie') ?? '';
```

**Bound every upstream call.** An unreachable API must not hold the page open:

```ts
const controller = new AbortController();
const timeout = setTimeout(() => controller.abort(), 2000);
try { /* fetch with signal: controller.signal */ }
catch { return { user: null, systemError: true }; }
finally { clearTimeout(timeout); }
```

Return a degraded shape rather than throwing, so the app can render "service unavailable" instead of
a stack trace.

**After a mutation, revalidate — never reload.** `useRevalidator().revalidate()` re-runs the loaders
and re-reads state from the server. `location.reload()` throws away the whole client for what is
only stale loader data, and it is the single most common thing to get wrong when porting from a
store-based framework.

**Reading parent loader data:** `useRouteLoaderData('root')`, not a prop drill.

## 6. State

Pick by where the state actually lives:

| Where it lives | Use | Example |
|---|---|---|
| The server | `loader` + `loaderData` | session, lists, anything fetched |
| The URL | `useSearchParams` | tokens, filters, pagination |
| One component | `useState` | a dropdown, a form field |
| Many components, one app | React context | the resolved session |
| An external store | `useSyncExternalStore` | theme (localStorage + OS media query) |

**Context, not a global store object.** Provide it from `root.tsx`, consume with a typed hook that
throws when the provider is missing. Hedge over inconsistent API shapes **once**, in that hook,
rather than at every call site.

**`useSyncExternalStore` for anything outside React.** Theme is the canonical case: the preference
lives in `localStorage` and the OS media query, both external. Mirroring them into `useState` from
an effect produces a render-then-correct pass, which is the light-then-dark flash on hydration.
Provide a `getServerSnapshot` that returns what the server actually rendered.

Pair it with a pre-paint inline script in `<head>` that applies the stored value to the document
before React runs. The server cannot know a `localStorage` value; without the script the first paint
is always the default:

```tsx
<script dangerouslySetInnerHTML={{ __html: THEME_INIT_SCRIPT }} />
```

Keep the script and the store reading the same key and the same media query, or they will disagree.

## 7. Styling

**CSS Modules per component or route.** Svelte scoped every component's `<style>` automatically;
React does not. Real files use names like `.content`, `.avatar`, `.section-title` — global CSS makes
those collide immediately.

```ts
// vite.config.ts
css: { modules: { localsConvention: 'camelCaseOnly' } }
```

so `.creator-card-small` is `styles.creatorCardSmall`.

**Design tokens stay global.** The `:root` / `[data-theme='dark']` custom-property file is imported
once in `root.tsx` and is not a module — everything else reads those properties.

> **If a design system generates that token file, its property names are a contract.** Renaming one
> locally breaks every future import. Change values upstream and re-sync.

**Class names are `Record<string, string>`.** Vite types CSS Modules that way, so a component that
takes another module's styles as a prop cannot declare a structural interface — it would never be
satisfied. Use `Record<string, string>` and name the required classes in a comment.

**Clickable things are `<button>`.** A `<div onClick>` is not focusable or announced. When porting,
expect to cancel the UA chrome the original never had to: `background: none; border: none; padding:
0; font: inherit;`.

## 8. The server boundary

- **`.server.ts` files are server-only.** Put secrets and privileged calls there.
- **`process.env` is server-only.** Resolve environment-dependent config in a loader and hand the
  result to the client; do not expect it in a component.
- **Never log credentials.** A root loader that logs the inbound `cookie` header writes a session
  token to stdout on every request — trivially done while debugging, easily missed in review.

## 9. Testing

**vitest + jsdom + `@testing-library/react`.** Deliberately *not* browser mode: that needs Playwright
browsers, which a plain node CI image does not carry — so those tests silently never run. In
Sowing.me four spec files sat in the repo for months having never executed once in CI.

`vitest.config.ts` must **not** load the React Router plugin; it owns the route/SSR build and would
make unit tests depend on a full app build.

What is worth testing:

- **Pure functions** — slug rules, formatting, anything with edge cases.
- **Hooks** with `renderHook`, especially ones that normalise inconsistent API shapes.
- **Components that degrade** — a page whose optional sections come from surfaces that may not exist
  yet should be tested with those sections absent.
- **Security-relevant attributes** — `rel="noopener noreferrer"` on user-supplied links is exactly
  the sort of thing a refactor drops silently.

A component reading only `loaderData` renders without a router; cast the generated props type down
to what the component actually uses.

## 10. The gate

Per app, from its own directory:

```bash
npm run lint     # eslint + prettier
npm run check    # react-router typegen && tsc --noEmit
npm test         # vitest
```

- **ESLint flat config**: in `eslint-plugin-react-hooks` v7 the flat configs are under
  `configs.flat['recommended-latest']`. `configs['recommended-latest']` is still eslintrc-shaped and
  fails with *"plugins key defined as an array of strings"*.
- **Ignore generated output.** `.react-router/` and `build/` belong in `.prettierignore` as well as
  `.gitignore`, or prettier lints machine-generated types.
- **CI runs the gate in the app's own image** — see below, because which image matters.

## 11. Deployment

**One parameterised Dockerfile, built once per app.** Not one per environment:

```dockerfile
ARG APP_DIR
RUN test -n "$APP_DIR" || (echo "APP_DIR build-arg is required" >&2; exit 1)
COPY ${APP_DIR}/package.json ${APP_DIR}/package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY ${APP_DIR}/ ./
RUN npm run build
```

A hardcoded app path in a shared Dockerfile is a bug generator: in Sowing.me three byte-identical
`Dockerfile_Node_{Dev,Staging,Main}` all copied the same app, so the admin frontend pulled the
consumer app's image for months while looking correctly configured — the Deployments differed only
by an `APP_NAME` the Vite build never reads. Requiring `APP_DIR` with no default makes that failure
loud.

**Three stages, and the order matters:**

| Stage | Contents | Used for |
|---|---|---|
| `build` | full `node_modules`, source, build output | **the CI gate** |
| `prune` | `npm prune --omit=dev` | intermediate |
| `runtime` | build output + pruned deps only | the Deployment |

The build stage must **end before the prune**. Put `npm prune` at the end of `build` and
`--target build` includes it, so the gate image loses eslint, tsc and vitest and every lint job dies
with `eslint: not found`. This is not hypothetical — it took two attempts to get right, and neither
failure reproduced locally, because a developer's `node_modules` is never pruned.

Serve with `react-router-serve ./build/server/index.js`. Never `vite preview`: it is a development
preview server, and its `allowedHosts` allowlist is a symptom of using it where it does not belong —
`react-router-serve` has no such concept.

## 12. Gotchas

**`vite.config.js` shadows `vite.config.ts`.** Leaving the old one behind during a migration gives
`Cannot find package '@sveltejs/kit'` from a config you thought you had replaced.

**Generated types must be in `tsconfig.json`.** `.react-router/types/**/*` in `include`, plus
`rootDirs: [".", "./.react-router/types"]`, or every `./+types/...` import fails to resolve.

**`react-router typegen` runs before `tsc`.** They are one command in `check` for that reason; run
`tsc` alone after adding a route and it will not see it.

**Tailwind v4 composes with the React Router plugin** — `tailwindcss()` before `reactRouter()` in
the plugins array. Verified, but order is the thing to check first if utilities go missing.

**Protected CI variables do not exist in merge-request pipelines.** A job that resolves secrets from
a vault works on a protected branch and fails on an MR with an empty credential. Give it a fallback,
or give the MR job a path that needs no secret.

**Node 22.22.0+** is required by React Router v8. An older base image fails at install, not at
runtime, which is at least honest.

---

## Related documentation

- [`../standards/js-code-review.md`](../standards/js-code-review.md) — what the gate runs and why
- [`../standards/design-system-handoff.md`](../standards/design-system-handoff.md) — design → code
- [`monorepo.md`](monorepo.md) — repository shape and app-type suffixes
- [`../standards/branching-and-git-workflow.md`](../standards/branching-and-git-workflow.md) — profiles
- `ts/Ubix/README.md` — the published package

## Changes to this document

| Version | Date | Change |
|---|---|---|
| 2.0 | 2026-09-16 | **Full rewrite for React 19 + React Router v8 + TypeScript.** v1.x documented SvelteKit and the `app/*Js` apps that lived in this repo; uBixCore's JS half became React at `v0.3.0` and OSS-10 moved every product app to its host, so both premises were false. Product-specific content inherited from the neptune lineage is gone. Every rule here is drawn from the Sowing.me migration, and the ones that exist because something broke say so. |
| 1.0–1.6 | 2026-05-14 → 2026-08-05 | SvelteKit era. See git history. |
