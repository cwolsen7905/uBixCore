# uBixCore Open-Source Split — Project

**Status:** Active — OSS-01/02 landed 2026-09-02, OSS-03 landed, OSS-05/06 landed, OSS-07 (skeleton proof) built 2026-09-03. OSS-04 deferred until the M1 lanes land; next OSS-08 (npm package) / OSS-09 (publish + skeleton subtree split).

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
| OSS-02 | `ProjectRootService`: one resolver for the host project root; replace every `__DIR__ . '/../../..'` walk in `php/Ubix` | Done | 2026-09-02, `feat/oss-02-project-root`. Root bound once in the host's `Dependencies.php` (`UBIX_PROJECT_ROOT` overrides); 12 call sites in 8 files rewritten; `php/Ubix` has no root-relative `__DIR__` left |
| OSS-03 | Thin entry points: `bin/ubix` + `public/index.php` call a framework bootstrap; command discovery takes a namespace list from host config | Done | 2026-09-03, `feat/oss-03-entry-points`. `Ubix\Bootstrap\{environment,console,http,discoverClasses}()` via Composer `files` autoload; commands found through Composer's PSR-4 map, so a host adds `'Acme\\Console\\Command'` to one array |
| OSS-04 | Product code out of `Ubix\`: SowingMe controllers/repositories/services/DTOs → `Kitg\SowingMe\` PSR-4 root under `php/Kitg/SowingMe/` | Todo | biggest diff; coordinate with active M1 lanes |
| OSS-05 | Export quality tooling from the package: phpcs ruleset reachable via `vendor/…/ruleset.xml`, phpstan extension neon, `Ubix\Tests` base classes moved out of `tests/` into the package | Done | 2026-09-03, `feat/oss-05-export-tooling`. `php/Ubix/ruleset.xml` is now the full `Ubix` standard (231 sniffs); project `phpcs.xml` is `<rule ref="Ubix"/>` + tuning. `php/Ubix/phpstan.neon` baseline included from the project file. `php/Ubix/Tests/` ships the base classes + a parameterised every-class-has-a-test scanner |
| OSS-06 | `composer.json` hygiene: `license` → `BSD-3-Clause`, heavy SDKs (Filestack, GitLab API, AWS) → `suggest`/optional, `composer.lock` **committed**, Dockerfiles `composer install` not `update` | Done | 2026-09-03, `feat/oss-06-composer-hygiene`. LICENSE (BSD-3, matches ubixvault/replikate); SDKs in require-dev + suggest; lock committed (60 prod / 47 dev packages); runtime images `composer install --no-dev`, `Dockerfile_Test` adds dev deps; php-di pin loosened to `^7.0.11` |
| OSS-07 | `skeleton/` folder + `ubix app:init` scaffolder; prove with `composer create-project` (path repo) booting a hello app in a scratch dir | Done | 2026-09-03, `feat/oss-07-skeleton`. `skeleton/` is a complete `create-project` template (HelloApi app, `App\` namespace, gate configs on `vendor/`, Dockerfile). Proven: create-project → `/health` JSON, CLI 15 commands, phpcs 0 / phpstan 0 / phpunit green in the generated project. Four sniffs made vendor-generic on the way. `app:init` scaffolder deferred (copying `app/HelloApi` is the manual form) |
| OSS-08 | `js/Ubix` → `@ubixsys/ubixcore`, `npm publish` job | Todo | `app/SowingMeJs` does not import `js/Ubix` today — clean start |
| OSS-09 | Publish: GitLab Composer registry for framework + skeleton (subtree split), first tag; public mirror + Packagist later | Build | 2026-09-03, `feat/oss-09-publish`: `publish-skeleton` tag job + `bin/publish-skeleton.sh`. **Waiting on Christopher:** (1) create empty GitLab project `ubixsys/ubixcore-skeleton`; (2) project access token there (Maintainer, `api` + `write_repository`) → masked CI variable `GITLAB_SKELETON_TOKEN` on ubixcore; (3) tag `v0.1.0`. Public mirror/Packagist stay Todo until the app layer leaves (OSS-10) |
| OSS-10 | Sowing.me migrates to `kitg/kitg` consuming released packages; `app/SowingMe*`, `docs/projects/sowing-me`, `docs/surfaces`, `sql/`, `templates/` leave this repo; **all Sowing.me deploys move to the kitg pipeline**; k8s namespaces/secrets/ingress recorded in `~/git/kubernetes` (source of truth) in the same change — see plan §5 | Todo | recommend new `kitg-{dev,staging,prod}` namespaces |
| OSS-11 | ubixsys-web "Build on uBixCore" section written from `process-log.md` | Todo | in `~/git/ubixsys-web` |
