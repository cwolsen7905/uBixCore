# uBixCore Open-Source Split — Process log

Narrative, in order, of what we did to turn a product monorepo into a
framework + skeleton that third parties can build on. Written as we go, with
the actual commands, so it can be rewritten into the ubixsys-web
"Build on uBixCore" docs without reconstructing anything. Newest at the bottom.

## 2026-09-02 — Step 0: decide the shape

**Starting point.** One repo (`ubixsys/ubixcore`, forked from project-neptune)
holding the framework (`php/Ubix`, `js/Ubix`), five Sowing.me apps under `app/`,
product SQL/templates/docs, CLI, CI, and the standards. `composer.json` already
says `type: library` and autoloads `Ubix\` from `php/Ubix/`, but the framework
cannot be installed anywhere else: commands find the repo root by walking up from
`__DIR__`, `bin/ubix` globs its own source tree for commands, and product
controllers live inside `Ubix\`.

**The model we chose.** Framework + skeleton (Laravel, Symfony). uBixCore is a
library you require; the skeleton is a template you `create-project` from; your
product is a host project in its own namespace. The framework never lands in a
host's git — it is installed from Composer/npm in the image build, and the host's
committed `composer.lock` is what pins it.

**Decisions taken** (recorded in [`README.md`](README.md)): BSD-3-Clause (BSD-2 was picked first, then switched to match `ubixvault` and `replikate`);
`ubixsys/ubixcore` + `@ubixsys/ubixcore`; skeleton lives in this repo; this repo
stays uBixCore and Sowing.me moves to a new private repo. The new private repo is KITG's company monorepo `kitg/kitg` (Kingdom Impact Technology Group owns Sowing.me and a second platform to come), PSR-4 root `Kitg\` with products as the second segment (`Kitg\SowingMe\`).

**Housekeeping.** Registered the `oss-split` lane in `AGENTS-COORD.md` and opened
a worktree so the three active M1 lanes are untouched:

```bash
git fetch origin
git worktree add ../ubixcore-worktrees/oss-split -b feat/oss-01-plan origin/dev
cp .env_dev ../ubixcore-worktrees/oss-split/.env    # pre-push hook needs it
```

**Next:** OSS-02, a `ProjectRoot` service so nothing in `php/Ubix` computes the
host root from its own file location.

## 2026-09-02 — Step 0b: the host repo exists

Christopher created `kitg/kitg` on GitLab (default branch `dev`) and cloned it
to `~/git/kitg`. Named after the company, not the product, because it will hold
Sowing.me and a second platform. Seeded with a README that states the shape
(`Kitg\` root, products as the second segment, uBixCore from `vendor/`, never
committed) — MR kitg/kitg!1. Nothing else goes in until OSS-07 proves the
skeleton boots a host from `vendor/`.

## 2026-09-02 — OSS-02: nothing in the framework may know where the repo is

**Problem.** Twelve places in `php/Ubix` computed the project root by walking up
from their own file (`__DIR__ . '/../../../../../'`, `dirname(__DIR__, 4)`): the
`app:build`/`app:deploy`/`app:run`/`app:generateOpenapi` commands (for `app/`,
`bin/deploy.sh`, the Dockerfile), `database:resetSchema` and `SchemaDiffService`
(for `sql/`), `code:loc`, and `MachineCodeReviewService` (for `vendor/bin/phpcs`,
`phpcbf`, `phpstan`, `phpunit` and the phpstan result cache). Installed under
`vendor/ubixsys/ubixcore/`, every one of them would have landed inside `vendor/`.

**Design.** A `Ubix\Service\ProjectRootService` with `getRoot()`,
`getPath(...$segments)` and `getVendorBinPath($tool)`. It does **not** discover
the root itself: the house standards (enforced by the `testFollowingUbixStandards`
test every class carries) forbid static methods and require `Logger $logger` as
the first constructor argument, so a `ProjectRoot::discover()` helper was out.
Instead the **host** binds the root once in its DI file — today
`app/UbixCli/src/Dependencies.php`, which already knew the root for the
migrations path — with `UBIX_PROJECT_ROOT` as an environment override for tooling
that runs from somewhere else. The framework asks; the host answers. That is the
same contract a third party's `Dependencies.php` will carry after `create-project`.

```php
$projectRoot = getenv('UBIX_PROJECT_ROOT') ?: dirname(__DIR__, 3);
ProjectRootService::class => autowire()->constructorParameter('root', $projectRoot),
```

**Mechanics.** The service is injected like any other (`private ProjectRootService
$projectRoot` in the constructor); constants that embedded a path
(`BuildCommand::BUILD_COMMANDS`, `APPS_PATH`, `SQL_PATH`) became method calls at
use time. `SchemaDiffServiceTest` builds the service by hand and gained the extra
argument. Behaviour running from this repo is unchanged: root resolves to the
same directory it always walked up to.

**Gate.** phpcs 0, phpstan 0 (`--memory-limit=2G` is needed locally; the default
128M crashes the parallel workers), phpunit 311 tests with 29 errors that are all
"The database connection failed" — this sandbox has no test-DB credentials; CI
has them. Rider: `code:loc` shelled out to `pahp` (typo) — fixed while there.

**Lesson for the docs.** "Framework in `vendor/`" is mostly a question of *who
knows the root*. Grep for `__DIR__` and `dirname(__DIR__` in the framework tree;
anything that reaches above the package boundary is a bug.

**Next:** OSS-03 — `bin/ubix` and `public/index.php` become thin skeleton files
that hand the root and a command-namespace list to a framework bootstrap.
