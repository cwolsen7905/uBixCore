# @ubixsys/ubixcore

The Svelte 5 component library of [uBixCore](https://github.com/cwolsen7905/ubixcore),
published from `js/Ubix/` on every `v*` tag of the framework, with the same
version number as the PHP package.

**Status:** placeholder. `src/lib/index.js` exports nothing yet; the first
shared components land as the first host products factor their UI out. The package
exists now so the publish path (GitLab npm registry, later npmjs.com) is proven
before there is anything to lose.

## Install

Until the package is on npmjs.com it is published to the npm registry of the
host organisation's own GitLab. Tell npm where the `@ubixsys` scope resolves,
once per project (`.npmrc`), with the registry URL and a read-only token from
uBixVault, never committed:

```
@ubixsys:registry=<registry url>
<registry url without scheme>:_authToken=${NPM_TOKEN}
```

Then:

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
