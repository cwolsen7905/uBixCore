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

## 2026-09-03 — OSS-03: the entry points belong to the host, the bootstrap to the framework

**Problem.** `bin/ubix` was 120 lines that globbed `php/Ubix/Console/Command/**`
ten levels deep to find commands and hard-required `app/UbixCli/src/Dependencies.php`;
`public/index.php` built `app/<APP_NAME>` next to itself; both loaded
`php/Ubix/Bootstrap/vault.php` by relative path. A host copying these would have
been copying framework internals.

**Design.** Everything that is framework behaviour moved into
`php/Ubix/Bootstrap/bootstrap.php` as namespaced functions, loaded through
Composer's `files` autoload so a host calls `\Ubix\Bootstrap\console()` without
knowing where the package is installed:

- `environment($projectRoot)` — timezone, `.env`, exports `UBIX_PROJECT_ROOT`,
  vault hook, sandbox error display. Returns the normalised root.
- `console($dependenciesFile, $commandNamespaces, $argv)` — builds the container
  and registers every command found under the given namespaces.
- `http($projectRoot, $appName)` — the Slim app for `app/<APP_NAME>`.
- `discoverClasses($namespace)` — walks Composer's registered PSR-4 map
  (`ClassLoader::getRegisteredLoaders()`), so `'Acme\\Console\\Command'` in a host
  resolves to `php/Acme/Console/Command/` with no framework change.

Functions rather than a class on purpose: the bootstrap runs before the container
exists, so it cannot satisfy the class standards (logger-first constructor, no
statics), and `Ubix\Bootstrap\` is already the scanner-exempt home for `vault.php`.

The host's files are now what the skeleton will ship:

```php
// bin/ubix
$projectRoot = environment(dirname(__DIR__));
console(
    dependenciesFile:  $projectRoot . '/app/UbixCli/src/Dependencies.php',
    commandNamespaces: ['Ubix\\Console\\Command'],   // a host appends its own
    argv:              $argv ?? [],
)->run();

// public/index.php
http(environment(dirname(__DIR__)), (string) getenv('APP_NAME'))->run();
```

**Found while there.** The old `bin/ubix` referenced `ExceptionCode::MissingCommandClass`
and `::InvalidCommandClass`, which do not exist (the cases are
`MISSING_COMMAND_CLASS` / `INVALID_COMMAND_CLASS`). It never fataled because the
branch only runs on a broken command class, and phpstan never saw it because
`bin/ubix` has no `.php` extension. Moving the logic into an analysed file
caught it.

**Verified.** `php bin/ubix list` shows the same 15 commands; `cron:*` gating
unchanged; `APP_NAME=SowingMeApi php -S … -t public` serves the API (401 from the
session middleware, as before). phpcs 0, phpstan 0.

**Next:** OSS-04 — move Sowing.me controllers, repositories, services, models and
payloads out of `Ubix\` into `Kitg\SowingMe\` (in this repo first, so the move
to `kitg/kitg` later is a directory copy, not a rename).

## 2026-09-03 — OSS-05: the quality gate ships with the framework

**Problem.** A host running `bin/ubix code:review` needs three things that only
existed as repo-level files: the phpcs rules (581 lines in `phpcs.xml`, only the
custom sniffs lived in the package), the phpstan settings, and the PHPUnit base
classes `Ubix\Tests\Abstract*TestCase` (autoloaded from `tests/`, so never
installed anywhere).

**phpcs.** `php/Ubix/` was already a phpcs *installed path* (that is what makes
the `Ubix.*` sniff codes work), but its `ruleset.xml` was a one-line stub. It is
now the whole standard: PSR-12 base, the PER/VSM additions, the Slevomat refs
and the 29 custom sniffs — 231 sniffs total. Path patterns inside the rules were
made layout-generic (`php/Ubix/Model/*` → `*/Model/*`, `app/*/src/Routes.php` →
`*/src/Routes.php`, `tests/**/*.php` → `*/tests/*`) so they cover a host's own
trees. The two rules whose include-lists named neptune-only files stayed in the
project `phpcs.xml`. A project file is now the config, the file list, and:

```xml
<config name="installed_paths" value="vendor/ubixsys/ubixcore/php/Ubix,vendor/slevomat/coding-standard" />
<rule ref="Ubix" />
```

(Here, `installed_paths` says `php/Ubix` instead of the vendor path.)

**phpstan.** `php/Ubix/phpstan.neon` carries level max, the ignore-tolerance
settings and the framework-only excludes, with paths relative to itself so they
never touch host code; excludes that may not exist in a given install carry the
`(?)` optional marker. The project `phpstan.neon` includes it and adds `paths:`,
`tmpDir:` and its own excludes.

**PHPUnit.** `Ubix\Tests\{AbstractTestCase, AbstractUbixConcreteClassOrEnumTestCase,
UbixConcreteClassOrEnumTestCaseInterface}` moved to `php/Ubix/Tests/` and
`composer.json` maps `Ubix\Tests\` to both `php/Ubix/Tests/` and `tests/`, so
the ~190 existing test files did not change. `AbstractTestCase` finds the host
through `UBIX_PROJECT_ROOT` / the working directory instead of `__DIR__`, with
`getDependenciesFile()` overridable. The "every concrete class has a test case"
check became `AbstractPhpunitTestCasesTestCase`, parameterised by code dir,
code namespace, tests dir and tests namespace; this repo's
`tests/PhpunitTestCasesTest.php` is 60 lines of configuration on top of it, and a
host writes the same file for `php/Kitg` + `Kitg\`.

**Gotcha found by the pre-push hook.** `<rule ref="Ubix"/>` auto-includes *every*
sniff under `Sniffs/`, including three that were never enabled (the old file had
them commented out as WIP): `WhiteSpace.ContinuationIndent`,
`Classes.ProtectedInFinalClass`, `ErrorHandling.RequirePreviousException`. A cached
`phpcs` run hid it; the hook runs `--no-cache`. They are now switched off inside
the standard with `<severity>0</severity>` until finished. Lesson: after touching
a ruleset, run phpcs with `--no-cache`.

**Verified.** phpcs 0 on the repo (uncached), 231 sniffs listed, a probe file with a
long-array violation is flagged (so the standard really loaded); phpstan 0;
scanner + container-backed tests green.

**Known gap, carried to OSS-04.** Some custom sniffs still hard-code `Ubix\`
(`ConcreteClassTestCaseSniff` even names a parent class that no longer exists),
and the standards checker treats a class's namespace prefix as `Ubix\…` when
deciding which rule family applies. Both need a configurable root namespace
before a `Kitg\SowingMe\Controller\*` gets controller rules applied.

## 2026-09-03 — OSS-06: a package needs a license, a lock, and a light footprint

**License.** `composer.json` said `"uBix License"`, which no registry accepts.
Now `BSD-3-Clause` with a root `LICENSE` copied from ubixvault so the whole uBix
family reads the same.

**Heavy SDKs.** `aws/aws-sdk-php`, `filestack/filestack-php` and
`m4tthumphrey/php-gitlab-api` were unconditional requires; each backs one optional
service (`S3BlobService`, `FilestackBlobService`; the GitLab client is not used by
any framework code today). They moved to `require-dev` (so this repo still
analyses and tests them) plus `suggest`, so a host only installs what it uses.

**Lock file.** `composer.lock` was gitignored and every Dockerfile ran
`composer update`, so an image could change with no commit behind it. The lock is
now committed. The versions in it are the ones a build would have pulled today
anyway (the last image was built with `composer update`), which is also why
this commit carries some transitive bumps (guzzle, php-di 7.0.11 → 7.1.1,
symfony patch releases): they were already in production, just unrecorded.
`php-di/php-di` had an exact pin; loosened to `^7.0.11`.

**Images.** Runtime Dockerfiles copy `composer.lock` and run
`composer install --no-dev --prefer-dist`, so live pods stop shipping phpunit,
phpcs and phpstan. `Dockerfile_Test` runs a full `composer install` on top for
the lint-and-test stage. Composer will refuse to build if the lock is out of
date with `composer.json`, which is the guarantee we wanted.

**Verified locally.** `composer validate` clean, `composer install --no-dev
--dry-run` removes exactly the 47 dev packages, phpstan 0, phpcs 0 (uncached),
phpunit unchanged (29 DB-connection errors from this sandbox, none new). The
image change is verified by the pipeline on merge.

**Also spotted.** `.gitlab-ci.yml` already has a `deploy-composer` job that
publishes every tag to the project's GitLab Composer registry (neptune-inherited).
That is step 2 of the "source of the package" plan, already wired.

## 2026-09-03 — OSS-07: a stranger's first ten minutes, for real

**What was built.** `skeleton/`: a `create-project` template with the thin
`bin/ubix` / `public/index.php` from OSS-03, a `HelloApi` app (`Dependencies`,
`Middleware`, `Routes`), a `HealthController` under the placeholder namespace
`App\`, its test plus the every-class-has-a-test scanner configured for
`php/App`, `phpcs.xml` / `phpstan.neon` / `phpunit.xml` pointing at the rules in
`vendor/ubixsys/ubixcore`, a runtime `Dockerfile`, `.env.example`, and a README
that is the quickstart.

**The proof, as run** (scratch dir, framework from this checkout via a path
repository):

```bash
composer create-project --no-install --stability=dev \
  --repository='{"type":"path","url":"<ubixcore>/skeleton"}' ubixsys/ubixcore-skeleton acme
cd acme
composer config --unset repositories.0          # the GitLab vcs entry, not needed locally
composer config repositories.ubixcore path <ubixcore>
composer require "ubixsys/ubixcore:*@dev"       # symlinks vendor/ubixsys/ubixcore -> checkout
cp .env.example .env
php bin/ubix list                               # 15 commands
APP_NAME=HelloApi php -S 127.0.0.1:8090 -t public &
curl 127.0.0.1:8090/health                      # {"status":"ok","app":"HelloApi"}
curl 127.0.0.1:8090/nope                        # 404 via the JSON error handler
vendor/bin/phpcs && vendor/bin/phpstan analyse && vendor/bin/phpunit
```

**What the first run caught** (16 phpcs errors, 1 phpstan) — all in the
framework's assumptions, none in the boot path:

- Two sniffs mapped test classes by editing the literal string `Ubix\`
  (`SeeTestCase`, `UbixConcreteClassOrEnumTestCase`), so an `App\Controller\X`
  was told its test lives at `\App\Controller\XTest`. Both now insert/remove the
  `Tests` segment after *any* vendor root. `ThrowSlimException` and
  `ModelGetterSetter` matched `Ubix\Controller` / `Ubix\Model` literally; they now
  match a `Controller`, `Middleware` or `Model` segment in any namespace.
- `DemandCustomDataTypes` and `ModelPropertiesType` were scoped in the old
  project file to a handful of neptune files that no longer exist, i.e. off.
  Auto-inclusion turned them on for the whole host. They are now `severity 0`
  in the standard and re-enabled with `severity 5` + include lists in this
  repo's `phpcs.xml` (still a no-op here, but explicit).
- The rest were my skeleton files not following house layout (alias
  `AbstractController as Controller`, a blank line between a file docblock and
  `return`, no arrow functions, `Level::fromName()` wants a literal).

**Still assuming `Ubix\`.** `AbstractUbixConcreteClassOrEnumTestCase` picks
which rule family to apply (controller, repository, service…) by
`str_starts_with($className, 'Ubix\\Controller\\')` etc., so a host's controller
only gets the general checks. Fix alongside OSS-04, when the first non-`Ubix\`
product code exists to test against.

**Not built.** `ubix app:init` scaffolder — copying `app/HelloApi` and renaming
is the manual form and is documented; the scaffolder is nicer, not blocking.

**Next.** OSS-09: publish. The `deploy-composer` CI job already pushes tags to
the GitLab registry; add the `skeleton/` subtree split so
`ubixsys/ubixcore-skeleton` resolves without a path repository. Then the kitg
repo can be created from it for real.

## 2026-09-03 — OSS-09: publishing, one job away from a first tag

**Framework.** `deploy-composer` (neptune-inherited) already publishes any `v*`
tag to this project's GitLab Composer registry; it only gained `--fail` so a
rejected publish turns the job red instead of silently passing.

**Skeleton.** A GitLab project registry holds one package per project and reads
the repo's root `composer.json`, so `skeleton/` cannot be published from
ubixcore's own registry. `bin/publish-skeleton.sh` does what Symfony's
read-only split repos do: `git subtree split --prefix=skeleton` at the tag,
force-push that history as `main` plus the same tag to
`ubixsys/ubixcore-skeleton`, then call the Composer packages API on *that*
project to publish the tag. The `publish-skeleton` job runs it after
`deploy-composer` on tag pipelines. Without `GITLAB_SKELETON_TOKEN` it exits
0 with a notice, so a tag never goes red on an optional step.

**Why a separate token.** `GITLAB_PROMOTE_TOKEN` is a project access token on
ubixcore and cannot push to another project. The skeleton project needs its own
(Maintainer, `api` + `write_repository`), stored as a masked CI variable on
ubixcore.

**Manual steps (Christopher):** create the empty `ubixsys/ubixcore-skeleton`
project (no README, no default branch protection on `main` for the CI token),
add `GITLAB_SKELETON_TOKEN`, then tag: `git tag v0.1.0 <dev sha> && git push
origin v0.1.0`. The tag pipeline publishes both packages. After that, a host's
`composer.json` uses the group Composer registry (one `composer` repository
entry for both packages) and `create-project ubixsys/ubixcore-skeleton` works
with no path repository.

**Deferred to after OSS-10.** GitHub push mirror and Packagist: not until the
app layer has left this repo, since a public mirror would publish Sowing.me.

## 2026-09-03 — first tag, and a green job that did nothing

`v0.1.0` was tagged. The runner was down for the first pipeline (re-pushed the
tag as a no-op), then `publish-skeleton` ran, printed "GITLAB_SKELETON_TOKEN is
not configured — skipping" and went **green**. Christopher, rightly: "this feels
like a bug". It was a design mistake on my side: I had it exit 0 so a release tag
would never go red on an optional step, which made a no-op indistinguishable from
a success. Changed to exit 1 with `allow_failure: true` on the job — the pipeline
still passes, the job shows orange with a warning, and the message says what to do.

Lesson for the docs: a publish step that cannot publish must be visible.
`allow_failure` is the GitLab idiom for "optional but loud".

## 2026-09-03 — v0.1.0 is out; a stranger's install works

With `GITLAB_SKELETON_TOKEN` in place the tag pipeline published both packages:
the framework to the ubixcore registry, and `skeleton/` split into
`ubixsys/ubixcore-skeleton` as `main` + `v0.1.0`, written entirely by CI. Then
the test that matters, from a clean directory with **no** path repository:

```bash
composer create-project --repository='{"type":"vcs","url":"git@gitlab.brainchurts.com:ubixsys/ubixcore-skeleton.git"}' \
  ubixsys/ubixcore-skeleton:0.1.0 acme
cd acme
php bin/ubix list                        # 15 commands
APP_NAME=HelloApi php -S 127.0.0.1:8080 -t public &
curl 127.0.0.1:8080/health               # {"status":"ok","app":"HelloApi"}
vendor/bin/phpcs && vendor/bin/phpstan analyse && vendor/bin/phpunit   # 0 / 0 / green
```

`vendor/ubixsys/ubixcore` is a real installed package (cloned at `dev-dev`, the
skeleton's transition constraint), not a symlink. Step 1 of the quickstart is
real. Once the group Composer registry is wired into the skeleton's
`composer.json`, the `--repository` flag goes away too.

**Directive from Christopher, applied from here on:** every piece of
documentation promotes **uBixVault** as the secrets mechanism. The framework
already has the hook (`Ubix\Bootstrap\vault.php`: `VAULT_ADDR` on, `VAULT_TOKEN`
or `VAULT_K8S_ROLE`, `VAULT_DB_KV_PATH`); the skeleton README, `.env.example`
and the quickstart now lead with it and frame `.env` as local fallback only.
## 2026-09-03 — OSS-08: the JS package, plumbing before contents

`js/Ubix` was a `svelte-package` shell named `vsm` 0.0.1 that exported nothing
(the neptune broadcasting stubs were deleted in M0-01, and `app/SowingMeJs`
never imported it). It is now `@ubixsys/ubixcore` 0.1.0 with proper metadata
and a README, and a `publish-npm` job on `v*` tags that runs `npm version
<tag>`, `npm ci`, `npm run prepack` (via `prepublishOnly`/`prepack`) and
`npm publish` into this project's GitLab npm registry, authenticated with the
job token. Version numbers track the framework tag, so the PHP and JS halves of
a release always match.

Publishing an empty library on purpose: it proves the path while there is
nothing to lose. The first real components arrive when Sowing.me's UI is split
out (OSS-10). Consumers configure the `@ubixsys` scope in `.npmrc` with a
read-only deploy token kept in uBixVault — see `js/Ubix/README.md`.

Verification needs a tag; `v0.1.1` after this lands.

## 2026-09-03 — v0.1.1: all three packages from one tag

`v0.1.1` tagged from `dev`. The tag pipeline published, in order:
`ubixsys/ubixcore` 0.1.1 (Composer, ubixcore project registry),
`ubixsys/ubixcore-skeleton` 0.1.1 (Composer, skeleton project registry, via the
subtree split), `@ubixsys/ubixcore` 0.1.1 (npm, ubixcore project registry).
Verified by listing `Packages::Package` on the GitLab server. One tag now
releases the PHP framework, the JS library and the skeleton with matching
version numbers — that is the release model going forward.
## 2026-09-04 — OSS-04a: the house rules stop caring what the vendor is called

**Why now.** kitg is bootstrapped and the next thing to move is SowingMeApi with
its DataTypes, Models, Payloads and DTOs. The standards checker every test runs
(`testFollowingUbixStandards`) chose its rule family with
`str_starts_with($class, 'Ubix\\Model\\')` and friends — 39 places — and used the
same test to exempt DataTypes/DTOs/Models/Payloads from the logger-first
constructor rule. A `Kitg\SowingMe\DataType\Int\PostId` would have got no
DataType rules and been told it needs a `Logger`.

**Change.** One helper, `isInFamily($fqcn, $family)`: walk the segments after the
vendor root, the first one that is a known family (`Controller`, `Model`,
`DataType`, `Service`, …) decides, and compound families (`Service\Sql`,
`Console\Command`) match from there. So `Ubix\Controller\X`,
`Kitg\Controller\X` and `Kitg\SowingMe\Controller\Api\X` are all controllers,
while `Ubix\Enum\Exception\ExceptionCode` stays an enum because `Enum` comes
first. The repository `query()` rule now checks the Options DTO by suffix.
The sniffs needed nothing: `DemandCustomDataTypes` and `ModelPropertiesType`
already reflect on `AbstractDataType` / `AbstractModel`.

**Proof.** `tests/Tests/FamilyDetectionTest.php` with framework, host and
host-with-product names; the full suite is unchanged for every existing class.
Then the real test: SowingMeApi's 66 classes copied into kitg under
`Kitg\\SowingMe\\` ran the same standards suite — 67 tests green after one more
positional assumption fell (the repository rule read the type from the third
namespace segment; it now reads the segment after `Repository`).

**Aside.** `ubixsys/ubixcore-skeleton` now push-mirrors to
https://github.com/cwolsen7905/ubixcore-skeleton. The skeleton is public while
its dependency still resolves from the private GitLab registry, which is fine as
a preview; Packagist for the framework itself waits on OSS-10.

## 2026-09-04 — SowingMeApi in kitg, and a namespace decision reversed

**The move.** SowingMeApi's closure — 66 classes, 54 tests, the app folder,
templates, `sowingme.sql`, four migrations — copied into kitg under
`Kitg\SowingMe\` (controllers as `Controller\Api`). Two extra classes joined
because the reverse-dependency check caught the framework depending on product
code: `SessionAuthenticationMiddleware` (needs `UserId`/`UserService`) and
`TierPrecedenceService`. `Email` and `Password` DataTypes stayed: they are
generic. After OSS-04a the kitg gate is green on the moved code with no edits
beyond one missing `use` (a class that had shared a namespace with `JsonService`)
and phpcbf import ordering. ubixcore's copies stay until kitg serves traffic.

**Pipeline.** kitg's `.gitlab-ci.yml` is ubixcore's minus JS and publish jobs.
The runtime image needs uBixCore from the private `ubixsys` group Composer
registry, so the build passes `auth.json` as a BuildKit secret (a deploy token
from `secret/kitg/composer`, else the job token) and the Dockerfile mounts it
for the `composer install` step only. Consequence for the lock: a lock generated
against a `vcs` source pins a git URL and cannot be installed inside Docker, so
kitg's `composer.json` points at the group registry and its lock must be
regenerated with a read token — the step that needs Christopher.

**Namespaces.** I had proposed `kitg-{dev,staging,prod}` and built the
manifests and doc rows. Christopher: "why are we changing them ... that
shouldn't change." He is right about the cost/benefit: `regcred` is already a
personal-token credential that pulls any path, the Vault role is already bound
to `ws-*`/`live-*`, and applying over the existing Deployment/Ingress names
makes the first kitg deploy the cutover with no interim hostnames. Reverted;
the plan §5 now records the decision and the one rule it creates (ubixcore
stops applying those manifests once kitg owns them).

## 2026-09-03 — SowingMeApi deploys from kitg (dev)

The first kitg `dev` pipeline built the image from the private registry
(job-token allowlists on ubixcore and k8s/baseimages), ran phpcs/phpstan, ran
phpunit against the shared test DB with credentials from uBixVault
(`secret/kitg/test-db`, policy `kitg-ci-ro` — raw HCL, not a JSON wrapper), and
`deploy-dev` replaced the `ws-dev/sowing-me-api` Deployment's image with
`kitg/kitg:dev`. Four small gaps surfaced on the way and each got a kitg fix plus
a skeleton/framework fix: `rector.php` missing, `.env` assumed by the test base,
`.gitkeep` rejected by the migration scanner, and `config/devops/nginx.conf` not
shipped in the image (nginx 404 on the first deploy).

**Handoff rule, now in force:** ubixcore no longer carries SowingMeApi's k8s
manifests, so its pipeline cannot redeploy over kitg's. The PHP sources stay in
ubixcore until AdminApi/Web move too (they share the `Ubix\` product classes),
then all of `app/SowingMe*` and the product code leave in one cut.

## 2026-09-03 — ubixcore is (almost) just the framework

After SowingMeApi proved out on dev, the rest followed in one kitg MR:
AdminApi and Web (their closures were entirely framework; Web needed one
controller, now `Kitg\SowingMe\Controller\Web`), both SvelteKit apps with the
node image build and lint jobs restored in kitg's pipeline, and the non-secret
env block on every dev Deployment. ubixcore then lost 100 paths: every product
class and test, `app/SowingMe*`, the Sowing.me templates, `Dockerfile_Node_*`,
`docs/projects/sowing-me`, `docs/surfaces`, the product migrations. The gate
stayed green: 247 framework tests remain.

What did **not** leave, and why: the framework still names the Sowing.me
database in `UbixDatabase`, `ResetSchemaCommand` and the migration tests, and
its CI rebuilds `sql/sowingme.sql` as the test fixture. That is a genericity
gap (the host should supply the database list), tracked as OSS-12.

**Lesson worth the website:** a baked `.env` hides every piece of config an
image depends on. The first kitg pod logged to `/dev/SowingMeApi.log`, lost
sessions and failed DB calls until the non-secret settings became Deployment
env; secrets were fine because uBixVault already supplied them.
