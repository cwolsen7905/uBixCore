# @ubixsys/ubixcore

The **React 19 + TypeScript** component library of
[uBixCore](https://github.com/ubixsys/ubixcore), published from `ts/Ubix/` on every
`v*` tag of the framework, with the same version number as the PHP package.

**Status:** placeholder. `src/index.ts` exports nothing yet; the first shared
components land as the first host products factor their UI out. The package exists
now so the publish path (GitLab npm registry, later npmjs.com) is proven before
there is anything to lose.

> **Flavour note.** uBixCore's JS half is React. This is a deliberate divergence
> from `project-neptune`, which is Svelte and is not built on this package.
> Host apps use **React Router v8** in framework mode.

## Install

Until the package is on npmjs.com it is published to the npm registry of the host
organisation's own GitLab. Tell npm where the `@ubixsys` scope resolves, once per
project (`.npmrc`), with the registry URL and a read-only token from uBixVault,
never committed:

```
@ubixsys:registry=<registry url>
<registry url without scheme>:_authToken=${NPM_TOKEN}
```

Then:

```bash
npm install @ubixsys/ubixcore
```

```tsx
import { /* components */ } from '@ubixsys/ubixcore';
```

`react` and `react-dom` are peer dependencies (`^19.2.7`) — the host app owns them.

## Consuming it

The package ships **built**: `exports` resolves to `dist/*.js` + `dist/*.d.ts`, which
`tsup` produces at `prepack`. Host repos consume it from the registry like any other
dependency, so they never need to compile TSX they did not write.

> **Why not raw source?** `ts/Ubix` is consumed *cross-repo* through a registry --
> a host always installs a tarball. The raw-source/no-build pattern only pays off
> for a package in the same repo as its consumer (an npm workspace, as in
> `project-neptune`'s `js/Vsm`). Note also that `publishConfig.exports` -- the usual
> trick for pointing at source in-repo and `dist` when published -- is a **pnpm/Bun**
> feature that npm silently ignores while warning it will hard-error later. Using it
> here publishes a manifest pointing at `src/` files that are not in the tarball.

To work on a component against a live host app, link it and run the watch build:

```bash
cd ts/Ubix && npm run dev      # tsup --watch -> dist/ on every save
npm link                       # then `npm link @ubixsys/ubixcore` in the host app
```

## Develop

```bash
npm install
npm run check      # tsc --noEmit
npm run build      # tsup -> dist/ (esm + .d.ts); runs automatically on prepack
npm run dev        # tsup --watch, for linked host-app development
```

Components go in `src/` and are re-exported from `src/index.ts`. There is no
showcase app in this package; exercise components from a host app.

## Boundary

Everything in here must pass the test in the root `CLAUDE.md`:

> Would a completely unrelated product built on uBixCore — not a membership
> platform, not Christian, not creator-facing — want this code **verbatim**?

Framework gets primitives, patterns and mechanism. Product logic — entitlement,
paywalls, tier precedence, domain vocabulary — belongs in the host repo, even when
two host products would both use it. `src/media/` is reserved for WHIP/WHEP/HLS
transport primitives on those terms; they land with the host surface that specifies
them, not ahead of it.
