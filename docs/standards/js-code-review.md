# JS Code Review

> **Status:** v2.0 — rewritten 2026-09-16. v1.x documented a root-level JS review suite (Knip,
> CSpell, svelte-check, Vitest across `app/*Js` workspaces) that ran **in this repository**. Neither
> half of that is true any more: OSS-10 moved every product app to its host, and uBixCore's
> `code:review` ships three tools — PHPCS, PHPSTAN, PHPUNIT. There is no JS tooling in the framework
> and no root `package.json`, `knip.json` or `cspell.json`.
>
> This is therefore a **standard a host implements**, not a description of local tooling.

## What uBixCore runs

`php bin/ubix code:review` drives PHP only:

| Tool | Checks |
|---|---|
| **PHPCS** | The `Ubix` coding standard |
| **PHPSTAN** | Level max + bleeding edge, `phpVersion` pinned |
| **PHPUNIT** | Framework tests |

`ts/Ubix` has its own gate, run from that directory:

```bash
cd ts/Ubix
npm run check     # tsc --noEmit
npm run build     # tsup -> dist/ (also runs at prepack)
```

## What a host must implement

A host repo owns its frontend apps and therefore owns their gate. The contract is **three commands
per app, run from the app's own directory**, because each app has its own `node_modules`, its own
config and its own dependency tree:

```bash
npm run lint      # eslint + prettier --check
npm run check     # react-router typegen && tsc --noEmit
npm test          # vitest
```

Nothing here mandates a *root-level* runner. A single hoisted suite across app workspaces is an
option, not a requirement, and it was a source of drift when uBixCore had one: the local list of
workspaces and CI's glob diverged, so an app was linted by CI and invisible locally for the whole
time it existed.

### Rules the host's CI must honour

These exist because each one has failed in a real pipeline.

**1. A tool that could not run is a violation, not a pass.**
JS tools are parsed out of subprocess output, so "no output" and "no findings" look identical. A
half-installed `node_modules` will report a clean tick for a whole session while CI fails on the
same commit. An app that runs **zero** tests is likewise a violation — a green tick beside an app
contributing no tests is exactly the false pass this rule exists to stop.

**2. Run the gate in an image that still has devDependencies.**
If the production image is multi-stage and prunes devDependencies, it has no eslint, tsc or vitest.
Publish the pre-prune build stage as a separate CI-only image and run the gate in that. Two
consecutive pipelines failed on `eslint: not found` learning this, and **neither reproduced
locally**, because a developer's `node_modules` is never pruned.

**3. Do not silently skip tests that need a browser.**
`vitest` browser mode needs Playwright browsers that a plain node image does not carry. An app
configured that way does not fail loudly in CI — its tests simply never run. Four spec files sat in
Sowing.me for months having executed exactly zero times. Use jsdom +
`@testing-library/react` unless a test genuinely needs a real browser, and if one does, make the
image carry the browsers rather than letting the job pass.

**4. Merge requests must run the gate.**
Pinning every job to a deploy branch means an MR runs nothing and the post-merge pipeline is the
first check — a review ritual, not a gate. Run the gate on `merge_request_event` too.

**5. Protected CI variables do not exist in merge-request pipelines.**
A job resolving secrets from a vault works on a protected branch and fails on an MR with an empty
credential. Give it a fallback, or give the MR path a job that needs no secret. Do **not** unprotect
the variable to make the pipeline green.

**6. Ignore generated output in the linters, not just in git.**
`.react-router/` and `build/` belong in `.prettierignore` as well as `.gitignore`, or prettier
lints machine-generated route types.

## ESLint configuration notes

- **Flat config.** In `eslint-plugin-react-hooks` v7 the flat configs live under
  `configs.flat['recommended-latest']`. The bare `configs['recommended-latest']` is still
  eslintrc-shaped and fails with *"A config object has a 'plugins' key defined as an array of
  strings"*.
- **`react-hooks/set-state-in-effect` is not advisory.** Every instance it raised during the
  Sowing.me migration was a genuine defect — state mirrored from an effect that should have been
  derived, or a fetch-on-mount that should have been a loader. Fix them; do not disable the rule.

## Related

- [`../architecture/complete-js-guide.md`](../architecture/complete-js-guide.md) — the patterns being reviewed
- [`code-review.md`](code-review.md) — the PHP side and the review process itself
