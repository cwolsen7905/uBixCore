# JS Code Review Setup

## Overview

The canonical entry point for monorepo-wide review is **`php bin/ubix code:review`** — the same command that drives the PHP side. As of 2026-05-15 it wires in every JS tool the team uses, in addition to the PHP ones:

| Tool | What it catches | How it runs |
|---|---|---|
| **CSpell** | Spelling mistakes in JS sources and the repo-level `README.md` / `CHANGELOG.md` / `docs/**/*.md` | Once at the repo root |
| **ESLint** | Lint rule violations (per-workspace flat config) | Once per discovered workspace that has an `eslint.config.js` |
| **Knip** | Unused files, exports, and dependencies (including dead deps still listed in `package.json`, and imports missing from deps) | Once at the repo root |
| **Prettier** | Formatting drift in `.js` / `.ts` / `.svelte` / `.json` / `.md` / `.css` files | Once at the repo root |
| **svelte-check** | Svelte / TypeScript type errors | Once per discovered workspace that has a `jsconfig.json` |
| **Vitest** | Unit test failures | Once per discovered workspace that has a `vite.config.js` |

**The workspace list is discovered, not enumerated** (2026-08-13): the per-workspace tools glob `app/*Js` and add the shared `js/Ubix` library, so a newly scaffolded app is reviewed the moment it exists. It used to be three hardcoded constants, which silently diverged from CI's `js-lint` job (that globs `app/*Js`) — `StudioAdminJs` was linted by CI and invisible locally for the whole time it existed, and `js/Ubix` had an `eslint.config.js` that nothing ever ran.

**A tool that could not run is a violation, not a pass** (2026-08-13). Each JS tool parses its findings out of the subprocess' output, so "no output" and "no findings" look identical — a broken or half-installed `node_modules` used to report a clean tick per tool for a whole session while CI failed on the same commit. ESLint with no JSON report, Prettier exiting above 1, svelte-check without its `COMPLETED` marker, and Vitest with a report it cannot parse now each raise a `tool-did-not-run` violation naming the workspace, the exit code and the tool's own stderr. A workspace that runs **zero** tests is likewise a violation — a `✔` beside a workspace contributing no tests is exactly the false green this exists to stop.

Every tool is **on by default**. Use the matching flag to opt out — for example `--vitest=off` to skip tests during a quick lint pass, or `--phpunit=off --vitest=off` to skip both test runners.

```bash
php bin/ubix code:review                         # everything on
php bin/ubix code:review --vitest=off            # skip JS tests
php bin/ubix code:review --output=json           # machine-readable, e.g. for CI
```

The root-level `npm run review` script remains as a **JS-only fallback** for environments that don't have the PHP CLI handy (e.g. a contributor's first push before they've run `composer install`). It only runs Knip, CSpell, and Prettier — the per-workspace tools (ESLint, svelte-check, Vitest) are not in the fallback because they need to be invoked from each workspace.

## Prerequisites

- **Node.js 20+**
- `npm install` at the repo root (installs every JS review tool — Knip, CSpell, Prettier, ESLint, svelte-check, Vitest are all hoisted to the root `node_modules/.bin/`)
- `composer install` if you want to run the canonical `php bin/ubix code:review` entry point

## Usage

```bash
# Canonical: drives every PHP and JS tool
php bin/ubix code:review

# Opt individual tools out
php bin/ubix code:review --phpunit=off --vitest=off   # skip both test suites
php bin/ubix code:review --cspell=off                 # skip spell check

# Machine-readable output (CI, automation)
php bin/ubix code:review --output=json

# JS-only fallback (does not include ESLint / svelte-check / Vitest)
npm run review
npm run review:knip
npm run review:spell
npm run review:format
```

The composite `npm run review` script uses `;` rather than `&&`, so one tool's non-zero exit does not skip the others. The `ubix code:review` command always runs every enabled tool and aggregates the findings.

## Tools in the Suite

### Knip

**Config:** `knip.json`

Knip is configured in monorepo mode with a workspace per `*Js` app plus `js/Ubix` (`app/InternalAdminJs`, `app/ProductJs`, `app/PerformerApplicationJs`, `js/Ubix` — see `knip.json` for the authoritative list; add a workspace entry when a new `*Js` app lands). It auto-detects SvelteKit route conventions (`+page.svelte`, `+layout.server.js`, etc.) via its built-in plugin and infers entry points from each workspace's `package.json`.

**Categories reported:**

- **Unused files** — files not reachable from any entry point. Common false positives: standard SvelteKit scaffolding (`src/app.css`, `src/app.d.ts`) and placeholder `src/lib/index.js` stubs.
- **Unused dependencies** — declared in `dependencies` but not imported anywhere.
- **Unused devDependencies** — declared in `devDependencies` but not imported. Common false positives: plugins loaded via config files that Knip does not parse (e.g. `prettier-plugin-svelte` is declared in `.prettierrc` JSON, `@tailwindcss/forms` is loaded via the Vite plugin chain).
- **Unlisted dependencies** — imported in source but missing from `package.json`.

**Adding a legitimate false positive to the allowlist:** extend the relevant workspace block in `knip.json`. For example, to tell Knip that `prettier-plugin-svelte` is used even though it is never `import`ed:

```json
{
    "workspaces": {
        "app/ProductJs": {
            "ignoreDependencies": ["prettier-plugin-svelte"]
        }
    }
}
```

See the [Knip configuration reference](https://knip.dev/reference/configuration) for the full schema.

### CSpell

**Config:** `cspell.json`

Scope is deliberately narrow: only `app/*Js/**`, `js/Ubix/**`, and the root `README.md` / `CHANGELOG.md`. The PHP side of the repo is covered by Peck (see `docs/standards/peck-setup.md`); running both spell-checkers against the same PHP files would double-count and force two parallel dictionaries to be maintained.

**Project dictionary:** `cspell.json > words` holds the accepted domain vocabulary (e.g. `fanclub`, `powerscore`, `varchar`, `datatypes`, `dtos`, and the PHP tool names referenced in JS-side docs). Add new words here rather than inline-suppressing them — this keeps the dictionary discoverable and reviewable in PRs.

**Adding a word:** append it alphabetically to the `words` array in `cspell.json`. Words are case-insensitive, so `Fanclub` / `fanclub` / `FANCLUB` all match a single `fanclub` entry.

**Ignoring a path** (use sparingly — prefer fixing real misspellings):

```json
{
    "ignorePaths": [
        "app/ProductJs/src/legacy/**"
    ]
}
```

### Prettier

**Config:** `.prettierrc` (root), `.prettierignore` (root), `app/InternalAdminJs/.prettierrc` (local override for Tailwind)

The root `.prettierrc` defines the shared style (tabs, single quotes, no trailing commas, 100-column width, `prettier-plugin-svelte` for `.svelte` parsing). `app/InternalAdminJs/` carries a local `.prettierrc` that layers `prettier-plugin-tailwindcss` on top — Prettier's nearest-config-wins resolution means files under that app use the Tailwind-aware rules while every other JS workspace uses the root defaults.

The root `.prettierignore` excludes lockfiles, `**/node_modules/`, `**/.svelte-kit/`, `**/build/`, `**/dist/`, `**/coverage/`, `**/static/`, and the root `/CHANGELOG.md`. Per-app `npm run lint` / `npm run format` scripts pass `--ignore-path=../../.prettierignore` so the same ignore list applies whether Prettier is invoked from the root or from inside a workspace.

**What the check actually covers is narrower than "the repo".** The glob is `{app/*Js,js/Ubix}/**/*.{js,ts,svelte,json,md,css}` — so root-level and `docs/` markdown are **not** Prettier-checked, even though CSpell does read them. That asymmetry matters for the root `CHANGELOG.md`: formatting it is both unnecessary (the gate never looks) and actively harmful (Prettier reflows every bullet it touches, turning a one-line addition into a diff that rewrites entries other concurrent lanes own, and manufacturing the merge conflicts `AGENTS-COORD.md` §5 exists to prevent). It is in `.prettierignore` as of 2026-08-06 so an explicit `prettier --write CHANGELOG.md` is now a no-op.

**Fixing drift:** run `npm run format` from inside the relevant workspace (`app/InternalAdminJs/`, `app/ProductJs/`, `js/Ubix/`). The root suite intentionally only checks — it does not auto-fix — so that a fresh review run never modifies source files.

### ESLint

Each `*Js` workspace and `js/Ubix` has an `eslint.config.js` that is a thin call into the shared `eslint.base.config.mjs` at the repo root (passing only the app-relative `./.gitignore` and `./svelte.config.js`), so a rule added to the base config applies everywhere at once. On top of `@eslint/js` recommended + `eslint-plugin-svelte` recommended + the Prettier compatibility configs, the base config carries these uBix Core rules:

| Rule | What it enforces | Standard |
|---|---|---|
| `no-unused-vars` (configured) | Standard rule, with `^_` exempted for the conventional throwaway binding (unused function args, destructured locals, `{#each as _, i}`) | — |
| `no-restricted-syntax` → `window.dispatchEvent` | **No `window` `CustomEvent` bus for in-app state** — cross-component state belongs in a `.svelte.js` state module or in context | [`complete-js-guide.md`](../architecture/complete-js-guide.md) §7.7 |

The `window.dispatchEvent` ban (added 2026-08-24) is a finding class promoted into the gate rather than a new convention: the events it bans were untyped, invisible to `svelte-check`, and — the reason it is a rule and not advice — silent when nothing listened, which is how `ubix:get-credits-requested` / `ubix:buy-credits-requested` sat with four dispatch sites and zero listeners for months while the buttons did nothing. The selector is deliberately narrow — it matches a `CustomEvent` constructed inline at a `window.dispatchEvent` call, and nothing else. Three lookalikes stay legal: `element.dispatchEvent` (firing an event on a real node); `window.dispatchEvent(new PointerEvent(…))` and friends, which is how a spec simulates real browser input against a drag handler that correctly listens on `window` (PA's `StaticConsolePreview.svelte.spec.js` does this six times, and narrowing the rule is why it still passes); and `postMessage` / `BroadcastChannel` (cross-document / cross-tab, where there is no shared module to import). A CustomEvent built on an earlier line escapes the selector — the guide's grep is the wider net. A genuine exception takes an `eslint-disable-next-line` with a one-line justification, same as any other rule here.

## Workflow

1. After pulling or before opening a PR: `npm run review` from the repo root.
2. Fix Prettier drift first (it is always mechanical): `cd app/<target>Js && npm run format`.
3. Review Knip findings by hand. Allowlist false positives in `knip.json`; delete or wire up genuinely unused code.
4. Review CSpell findings. Add new legitimate domain words to `cspell.json > words`; fix actual misspellings in source.

## Follow-ups

Tracked future additions to the review suite. Each is non-blocking today; pick up when the team has capacity.

- **ESLint rule: ban `document.querySelector` / `document.getElementById` inside `.svelte` files.** Enforces the DOM-reference convention in [`docs/architecture/complete-js-guide.md`](../architecture/complete-js-guide.md) §4.10 ("Where DOM references happen") — SvelteKit components should use `bind:this`, not class-based selectors. Today the convention is code-review-enforced; an ESLint rule (e.g. via `no-restricted-syntax` or `no-restricted-globals` against `document.querySelector` / `document.querySelectorAll` / `document.getElementById` in `.svelte` files) lets the suite catch regressions without a human eye. Implementation home: each workspace's `eslint.config.js`. Allowlist legitimate exceptions via per-line `eslint-disable-next-line` comments with a one-line justification.

- **Render smoke specs for route pages.** The suite has no way to catch a page that throws at render: every tool is static, so a component crash reaches the browser as the first sign of trouble. This is not hypothetical — the Internal Admin scoreboard shipped rendering nothing at all, because it fed id-less rows to `DataTable` and Svelte's keyed `{#each}` threw `each_key_duplicate`; the page had no spec, and every gate tool passed. As of 2026-08-06 only **8 of 27** `+page.svelte` files in `app/InternalAdminJs` have a co-located spec. The cheap version of this is not full coverage but a *smoke* spec per page — render with a representative row set of **two or more** rows and assert something appeared. Two rows matters: a duplicate-key crash is invisible at one row, so a single-row fixture reproduces nothing. Whether to make that a gate rule (a JS analogue of the PHP `ConcreteClassTestCase` sniff, which would need a `ubix code:review` config change and therefore sign-off) is the open question; adopting it convention-first is the low-risk start.

## What's Out of Scope

- **Architectural boundary enforcement** (e.g. "`app/InternalAdminJs` may import `@ubixsys/ubixcore` but not `app/ProductJs`"). `dependency-cruiser` was evaluated and deferred because its `.svelte` file parsing is unreliable. The likely replacement is `eslint-plugin-boundaries`, which piggybacks on the existing Svelte ESLint parser and can be added to each workspace's `eslint.config.js` when the team is ready.
- **PHP files** — JS tools ignore `php/`, `vendor/`, `templates/`, and `app/*Api/` / `app/*Web/` / `UbixCli/`. The PHP toolchain (PHPCS, PHPStan, PHPUnit, Peck) covers those, and `ubix code:review` runs all of them together.
- **Auto-fix for JS tools** — the auto-fix prompt at the end of the review currently only runs `phpcbf`. Prettier violations are marked auto-fixable in the UI, but the user must run `prettier --write` themselves (or `npm run format` from the relevant workspace) until the auto-fix runner is extended to cover JS tools.
