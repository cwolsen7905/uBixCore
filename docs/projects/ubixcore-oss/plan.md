# uBixCore Open-Source Split — Plan

**Status:** Draft v1, 2026-09-02. Decisions in [`README.md`](README.md).

## 1. Target shape

Three things, where today there is one:

1. **uBixCore** (this repo, public). One monorepo that publishes several packages
   from one tree and owns the tooling that is not code.
2. **The skeleton** (`skeleton/` in this repo, published as
   `ubixsys/ubixcore-skeleton`). The shape of a host project; what
   `composer create-project` hands a third party.
3. **A host project** (e.g. Sowing.me, private). An instance of the skeleton with
   its own namespace, consuming uBixCore from `vendor/` and `node_modules/`.

```
ubixcore/                       (public)
  php/Ubix/                     Ubix\  — PSR-4, ships as ubixsys/ubixcore
  js/Ubix/                      @ubixsys/ubixcore (svelte-package)
  py/ubixcore/                  (later) PyPI ubixcore
  skeleton/                     create-project template → ubixsys/ubixcore-skeleton
    composer.json  public/index.php  bin/ubix  app/  phpcs.xml  phpstan.neon
    Dockerfile*  .gitlab-ci.yml  config/devops/  .claude/  AGENTS-COORD.template.md
  docs/standards/               shipped; `ubix init` copies into hosts
  tests/                        framework tests only
  composer.json  LICENSE (BSD-3-Clause)  .gitlab-ci.yml (test + publish)

sowingme/                       (private host)
  composer.json                 name kitg/sowingme; requires ubixsys/ubixcore ^0.x
  composer.lock                 COMMITTED — pins the framework the image is built from
  public/index.php  bin/ubix    thin, copied from skeleton, owned by the host
  app/SowingMeApi/src/          Routes.php, Dependencies.php  (namespace Kitg\SowingMe\Api)
  php/Kitg/SowingMe/            controllers, repositories, services, DTOs (PSR-4 root Kitg\SowingMe\)
  app/SowingMeJs/               imports @ubixsys/ubixcore
  sql/ templates/ docs/         product-owned
  vendor/ node_modules/ .env*   NOT tracked
```

**Namespace rule:** `Ubix\` is reserved for the framework. Hosts choose their own
PSR-4 root and the framework finds host code through configuration, never by path.

**Ownership rule for copied files:** everything the skeleton copies in (entry
points, quality configs, Dockerfiles, CI, skills, standards) belongs to the host
after the copy. Framework upgrades that need changes there come via the changelog
and, later, an `ubix upgrade` diff — the Laravel skeleton model.

## 2. How a host gets the framework (git + pipeline)

- The host commits **its own code plus the lock files**. uBixCore is never committed
  in a host repo; it arrives through `composer install` / `npm ci`, exactly as Slim
  and Svelte do today.
- The Dockerfile copies `composer.json` + `composer.lock` and runs
  `composer install --no-dev --prefer-dist`. Composer resolves `ubixsys/ubixcore`
  from Packagist (public) or from a GitLab package registry / VCS entry (private,
  needs `COMPOSER_AUTH` in CI). The node image does the same with `npm ci` and an
  `.npmrc` token for the `@ubixsys` scope.
- A framework upgrade in a host is `composer update ubixsys/ubixcore` plus a
  one-line lock diff in an MR. Nothing else changes.
- uBixCore's own CI publishes on tag: Packagist webhook for PHP, `npm publish`
  for JS, subtree split of `skeleton/` to its own package.

**Prerequisite this forces:** `composer.lock` is gitignored today and the
Dockerfiles run `composer update`. An image can therefore change with no commit
behind it. Once the framework is a dependency, the lock is the only record of
which uBixCore built the image (OSS-06).

### Maintaining both at once

As a third party the flow above is complete. As the author of both repos:

- **Composer `path` repository** with `symlink: true` makes
  `vendor/ubixsys/ubixcore` a symlink to `../ubixcore`. The entry lives in
  `composer.json`, so either add it locally without committing, or give `bin/ubix`
  a `dev:link` command that swaps the symlink in and out (like `npm link`).
- **npm link** covers the JS side natively.
- **Branch pinning** is the transition mode: the host requires `dev-dev` from a VCS
  entry so CI builds from the framework's `dev` head, then moves to tags once the
  API settles.

## 3. Coupling inventory (what stops `php/Ubix` from living in `vendor/`)

Measured 2026-09-02 on `dev` @ `482fb24`.

| # | Coupling | Where | Slice |
|---|---|---|---|
| C1 | Product code in the framework namespace | `Ubix\Controller\SowingMeApi\*`, `Ubix\Controller\SowingMeWeb\*`, `Ubix\Repository\{Creator,Post,User,EmailConfirmationToken,PasswordResetToken,Country,State}`, matching `Service`, `Model`, `Payload`, `DataTransferObject` trees; `EmailService` names the product | OSS-04 |
| C2 | Repo-root path walks | `Console/Command/App/{Build,Deploy,Run,GenerateOpenapi}Command`, `Console/Command/Database/ResetSchemaCommand` (`SQL_PATH`), `Console/Command/Code/LocCommand`, `Service/Migration/SchemaDiffService` (`sql/`), `Service/MachineCodeReviewService` (locates `vendor/bin/{phpcs,phpcbf,phpstan,phpunit}` and the phpstan cache by walking up from `__DIR__`) | OSS-02 |
| C3 | Entry points assume this layout | `bin/ubix` globs `php/Ubix/Console/Command/**` (8 levels) and hard-requires `app/UbixCli/src/Dependencies.php`; `public/index.php` builds `app/<APP_NAME>` relative to itself; both load `php/Ubix/Bootstrap/vault.php` by path | OSS-03 |
| C4 | Quality tooling only reachable by relative path | `php/Ubix/ruleset.xml` + `Sniffs/` referenced from root `phpcs.xml`; `Ubix\Tests\*` base classes that enforce house standards live in `tests/` (autoloaded from `tests/`, so not shipped) | OSS-05 |
| C5 | Heavy unconditional requires | `filestack/filestack-php`, `m4tthumphrey/php-gitlab-api`, `aws/aws-sdk-php` in `require` | OSS-06 |
| C6 | Package metadata | `"license": "uBix License"`; `minimum-stability: dev`; `php-di/php-di` pinned exact; `js/Ubix` named `vsm` 0.0.1 | OSS-06 / OSS-08 |
| C7 | Product assets at the root | `sql/`, `templates/`, `docs/projects/sowing-me`, `docs/surfaces`, `app/SowingMe*`, per-app k8s yaml, `.env_dev` | OSS-10 |

Items C1–C4 are worth doing even if uBixCore never ships: they are the same
cleanup that makes the framework testable in isolation.

## 4. Sequence

1. **Decouple in place** (OSS-02 → OSS-06). This repo keeps building and deploying
   Sowing.me throughout; every slice lands via MR on `dev` like any other.
2. **Prove the skeleton** (OSS-07). `composer create-project` from a `path` repo
   into a scratch dir; boot a hello app; run `bin/ubix`, phpcs, phpstan, phpunit
   against it. Until this passes nothing is "extractable".
3. **Publish** (OSS-08, OSS-09). Public mirror, Packagist, npm, first tag.
4. **Move Sowing.me out** (OSS-10). New private repo from the skeleton; the
   product trees leave this repo; this repo's Dockerfiles/CI shrink to
   framework test + publish.
5. **Document** (OSS-11). Rewrite `process-log.md` into the ubixsys-web section.

Each slice: claim its paths in `AGENTS-COORD.md` §6 first (`php/Ubix/**` is a
shared tree and three M1 lanes are active), keep the gate green, flip the matrix
row in the same commit.

## 5. Open questions

- ~~BSD-2 vs BSD-3~~ resolved: BSD-3-Clause, matching the rest of the uBix family.
- Skeleton delivery: `composer create-project ubixsys/ubixcore-skeleton` vs
  `ubix init` from a globally installed CLI. Default: both, `create-project` first.
- Versioning: start at `v0.1.0` and stay 0.x until Sowing.me runs on a released
  tag; CalVer (neptune) was considered and rejected for a library.
- Where `docs/standards` live for a host: copied by `ubix init` (host owns them)
  vs referenced from `vendor/` (framework owns them). Default: copied.
