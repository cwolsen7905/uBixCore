# uBixCore Open-Source Split — Project

**Status:** Active — slice 1 (plan + process log) 2026-09-02 on `feat/oss-01-plan`. No code moved yet.

Turn this repo into **uBixCore**, an open-source framework-plus-tooling monorepo that
third parties install from Composer / npm (and later PyPI), and move **Sowing.me**
out into its own private repo built from the uBixCore skeleton. The model is the
Laravel / Symfony "framework + skeleton" split; the whole point of doing it here is
that the *tooling* (CLI, code review, sniffs, test base classes, standards, Claude
skills) ships with the framework, not just the library code.

Every step is written down as it happens in [`process-log.md`](process-log.md);
that log is the source for the "Build on uBixCore" section of **ubixsys-web**.

| Doc | What |
|---|---|
| [`plan.md`](plan.md) | Target shape, decisions, coupling inventory, sequenced slices |
| [`process-log.md`](process-log.md) | Narrative of what we actually did, in order, with the commands — feeds the website docs |
| [`quickstart-draft.md`](quickstart-draft.md) | The "Get started with uBixCore" page as it should read when done, with a reality-check table mapping each step to a slice |

## Decisions (2026-09-02, Christopher)

| Decision | Choice | Note |
|---|---|---|
| License | **BSD-3-Clause** | matches `ubixvault` and `replikate` (initially chose BSD-2, switched same day for family consistency) |
| PHP package | `ubixsys/ubixcore` (Packagist) | already the `composer.json` name |
| JS package | `@ubixsys/ubixcore` (npm) | `js/Ubix/package.json` is currently named `vsm`; `@ubixsys` scope must be claimed |
| Python package | `ubixcore` (PyPI) | no `py/` tree exists yet; guidelines only |
| Skeleton | `skeleton/` **inside this repo**, published as `ubixsys/ubixcore-skeleton` via subtree split | one CI, framework and skeleton move together |
| Repo roles | **this repo stays uBixCore**; Sowing.me moves to a new private repo | Sowing.me repo is created by Christopher |
| Host repo + namespace | The new private repo is **KITG's company monorepo** `kitg/kitg` (Kingdom Impact Technology Group owns Sowing.me and will add a second platform later). Composer `kitg/kitg`, PSR-4 root `Kitg\` from `php/Kitg/`; products are the second segment: `Kitg\SowingMe\`, `Kitg\<OtherPlatform>\`, shared KITG code under `Kitg\Shared\`; apps side by side in `app/` selected by `APP_NAME` as today; npm scope `@kitg` | StudlyCaps `Kitg`, matching `Ubix` |

## Work matrix

Status: `Todo` · `Build` · `Done` · `Dropped`.

| ID | Slice | Status | Notes |
|---|---|---|---|
| OSS-01 | Plan + process log (this folder), lane registered | Done | docs only |
| OSS-02 | `ProjectRoot` service: one resolver for the host project root; replace every `__DIR__ . '/../../..'` walk in `php/Ubix` | Todo | see plan § Coupling inventory |
| OSS-03 | Thin entry points: `bin/ubix` + `public/index.php` call a framework bootstrap; command discovery takes a namespace list from host config | Todo | `bin/ubix` globs `php/Ubix/Console/Command/**` today |
| OSS-04 | Product code out of `Ubix\`: SowingMe controllers/repositories/services/DTOs → `Kitg\SowingMe\` PSR-4 root under `php/Kitg/SowingMe/` | Todo | biggest diff; coordinate with active M1 lanes |
| OSS-05 | Export quality tooling from the package: phpcs ruleset reachable via `vendor/…/ruleset.xml`, phpstan extension neon, `Ubix\Tests` base classes moved out of `tests/` into the package | Todo | |
| OSS-06 | `composer.json` hygiene: `license` → `BSD-3-Clause`, heavy SDKs (Filestack, GitLab API, AWS) → `suggest`/optional, `composer.lock` **committed**, Dockerfiles `composer install` not `update` | Todo | lock is gitignored today |
| OSS-07 | `skeleton/` folder + `ubix app:init` scaffolder; prove with `composer create-project` (path repo) booting a hello app in a scratch dir | Todo | |
| OSS-08 | `js/Ubix` → `@ubixsys/ubixcore`, `npm publish` job | Todo | `app/SowingMeJs` does not import `js/Ubix` today — clean start |
| OSS-09 | Publish: public mirror, Packagist hook, npm publish, subtree split for skeleton; tag `v0.1.0` | Todo | |
| OSS-10 | Sowing.me migrates to its new repo consuming released packages; `app/SowingMe*`, `docs/projects/sowing-me`, `docs/surfaces`, `sql/`, `templates/` leave this repo | Todo | |
| OSS-11 | ubixsys-web "Build on uBixCore" section written from `process-log.md` | Todo | in `~/git/ubixsys-web` |
