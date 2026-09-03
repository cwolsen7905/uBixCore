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

kitg/                           (private host — KITG company monorepo; Sowing.me + a future platform)
  composer.json                 name kitg/kitg; requires ubixsys/ubixcore ^0.x
  composer.lock                 COMMITTED — pins the framework the image is built from
  public/index.php  bin/ubix    thin, copied from skeleton, owned by the host
  app/SowingMeApi/src/          Routes.php, Dependencies.php  (namespace Kitg\SowingMe\Api)
  php/Kitg/SowingMe/            controllers, repositories, services, DTOs (PSR-4 root Kitg\ → php/Kitg/)
  php/Kitg/Shared/              code both KITG products use
  app/<OtherPlatform>Api/       second product later, same layout
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

**Prerequisite (done in OSS-06):** `composer.lock` was gitignored and the
Dockerfiles ran `composer update`, so an image could change with no commit
behind it. The lock is now committed and images run `composer install`; once the
framework is a dependency, the lock is the record of which uBixCore built the image.

### Source of the package: GitLab first, GitHub as a mirror

`ubixsys/ubixcore` is only a name; the host's `composer.json` decides where it is
fetched from. Sequence (Christopher, 2026-09-02):

1. **Transition — VCS repository.** In `kitg/kitg`:
   ```json
   "repositories": [{ "type": "vcs", "url": "git@gitlab.brainchurts.com:ubixsys/ubixcore.git" }],
   "require": { "ubixsys/ubixcore": "dev-dev" }
   ```
   Composer reads branches/tags straight from GitLab; every push to ubixcore
   `dev` is consumable at once. CI authenticates with a GitLab deploy token via
   `COMPOSER_AUTH` (`gitlab-token`), stored in ubixvault like the other CI tokens.
2. **Tagged — GitLab Composer package registry.** On each `v*` tag the
   `deploy-composer` job publishes the framework to this project's registry and
   `publish-skeleton` (`bin/publish-skeleton.sh`) subtree-splits `skeleton/` into
   the `ubixsys/ubixcore-skeleton` project and publishes it there; kitg switches to
   `{ "type": "composer", "url": "https://gitlab.brainchurts.com/api/v4/group/<id>/-/packages/composer/packages.json" }`
   and `"ubixsys/ubixcore": "^0.1"`. The lock then pins a tag, not a branch commit.
3. **Public — mirror + Packagist.** Once the app layer is gone from ubixcore,
   GitLab push-mirrors the repo to GitHub and Packagist is pointed at a public
   URL (Packagist accepts a public GitLab URL directly; the GitHub mirror is for
   discoverability). kitg keeps using the private registry unless switched.

The same shape applies to npm: GitLab's npm registry for `@ubixsys/ubixcore`
first, npmjs.com when public.

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

## 5. Deployment moves with Sowing.me (OSS-10)

Once Sowing.me lives in `kitg/kitg`, **every Sowing.me deploy comes from that
repo's pipeline**, not from ubixcore's. ubixcore's CI shrinks to test + publish.

**Source of truth:** `~/git/kubernetes` is the disaster-recovery record for
cluster config. Any namespace, pull secret, ingress or certificate we add for
KITG is committed there **in the same change** that first uses it — never only
applied by hand or only generated by a pipeline.

Today (APPLICATIONS.md, 2026-08-19 survey): `ubixsys/ubixcore` → `sowing-me-web`,
`sowing-me-js`, `sowing-me-api`, `sowing-me-admin-api` in the shared tier
namespaces `live-{dev,staging,prod}` / `ws-{dev,staging,prod}`; the k8s manifests
sit in the ubixcore repo under `app/<App>/<env>-{deploy,ingress}.yaml` and are
applied by `bin/deploy.sh`.

**Decision (Christopher, 2026-09-04): the tier namespaces do not change.**
Sowing.me keeps deploying into `ws-<env>` (api, admin-api) and `live-<env>`
(web); only the deploying project and the image path change. Reasons this is
simpler than the `kitg-*` namespaces first proposed: the existing `regcred` is
a personal-token credential that already pulls any registry path; the
`sowingme-<env>` uBixVault role is already bound to these namespaces; and the
kitg pipeline applies over the existing Deployment/Ingress names, so the
cutover *is* the first kitg deploy — no interim hostnames, no side-by-side.
The one rule afterwards: ubixcore's pipeline must stop applying those
manifests (its `app/SowingMe*` folders leave with OSS-10) so the two projects
never fight over the same objects, and new migrations are written in kitg only.

Checklist for the cutover MR set (ubixcore OSS-10 + kitg + kubernetes repos):
1. `kubernetes/docs/reference/applications.md` row `kitg/kitg → sowing-me-*`
   in `ws-*`/`live-*` (MR kube-stuff/kubernetes!3).
2. ubixvault CI tokens/policies for `kitg` (`secret/kitg/*`: composer, test-db,
   discord) — values in vault, names in `kubernetes/docs/reference/secrets.md`.
3. `kitg/kitg`: skeleton `Dockerfile*`, `.gitlab-ci.yml`, `bin/deploy.sh`,
   `app/<App>/<env>-*.yaml` with the new namespace + image path.
4. Ingress/cert: unchanged (same namespaces, same hosts).
5. Cut over dev first, then staging, then prod; retire the ubixcore-driven
   Deployments and the `-node` image variants from `ubixsys/ubixcore`.

## 6. Open questions

- ~~BSD-2 vs BSD-3~~ resolved: BSD-3-Clause, matching the rest of the uBix family.
- Skeleton delivery: `composer create-project ubixsys/ubixcore-skeleton` vs
  `ubix init` from a globally installed CLI. Default: both, `create-project` first.
- Versioning: start at `v0.1.0` and stay 0.x until Sowing.me runs on a released
  tag; CalVer (neptune) was considered and rejected for a library.
- ~~Namespaces at cutover~~ resolved: stay in `live-*`/`ws-*` (§5).
- Where `docs/standards` live for a host: copied by `ubix init` (host owns them)
  vs referenced from `vendor/` (framework owns them). Default: copied.
