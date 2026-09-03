# @ubixsys/ubixcore

The Svelte 5 component library of [uBixCore](https://gitlab.brainchurts.com/ubixsys/ubixcore),
published from `js/Ubix/` on every `v*` tag of the framework, with the same
version number as the PHP package.

**Status:** placeholder. `src/lib/index.js` exports nothing yet; the first
shared components land as the first host products factor their UI out. The package
exists now so the publish path (GitLab npm registry, later npmjs.com) is proven
before there is anything to lose.

## Install

The package lives in the GitLab npm registry of the `ubixsys/ubixcore` project.
Tell npm where the `@ubixsys` scope resolves, once per project (`.npmrc`):

```
@ubixsys:registry=https://gitlab.brainchurts.com/api/v4/projects/<ubixcore project id>/packages/npm/
//gitlab.brainchurts.com/api/v4/projects/<ubixcore project id>/packages/npm/:_authToken=${GITLAB_NPM_TOKEN}
```

`GITLAB_NPM_TOKEN` is a read-only deploy token (`read_package_registry`).
Keep it in uBixVault and read it into the environment in CI, never in the
repo. Then:

```bash
npm install @ubixsys/ubixcore
```

```svelte
<script>
  import { /* components */ } from '@ubixsys/ubixcore';
</script>
```

## Develop

```bash
npm install
npm run dev        # showcase app (src/routes)
npm run prepack    # svelte-package -> dist/ + publint
```

Components go in `src/lib/` and are re-exported from `src/lib/index.js`;
`src/routes/` is a showcase only and is not published.
