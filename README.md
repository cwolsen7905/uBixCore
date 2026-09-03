# uBixCore

A PHP 8.4+ / Svelte 5 application framework that ships with its tooling. Install it,
don't fork it: the framework arrives through Composer and npm, your project keeps its
own namespace and git history, and [uBixVault](https://github.com/cwolsen7905/ubixvault)
holds its secrets.

| Package | Registry | What |
|---|---|---|
| `ubixsys/ubixcore` | Composer | The framework (`Ubix\` namespace): CLI, HTTP building blocks, typed-contract bases, migrations, the `Ubix` coding standard, PHPStan baseline, PHPUnit base classes |
| `ubixsys/ubixcore-skeleton` | Composer | The `create-project` template — [repo](https://github.com/cwolsen7905/ubixcore-skeleton) |
| `@ubixsys/ubixcore` | npm | The Svelte 5 component library |

One `v*` tag publishes all three with the same version. License: BSD-3-Clause.

## Start a project

```bash
composer create-project ubixsys/ubixcore-skeleton acme
cd acme
php bin/ubix list                                     # the framework's commands, plus yours
APP_NAME=HelloApi php -S 127.0.0.1:8080 -t public     # curl /health
php bin/ubix code:review                              # phpcs · phpstan (level max) · phpunit
```

The skeleton's `README.md` is the developer quickstart; its `docs/ci-setup.md` is the
operator's checklist for a green pipeline (runner, registry access, CI variables,
uBixVault secrets, Kubernetes prerequisites). The full write-up, including a worked
installation scenario, is on [ubixsys.com](https://ubixsys.com/ubixcore).

## Working on the framework

Requirements: PHP 8.4+, Composer 2, Node 20+ (for `js/Ubix`), a MariaDB reachable for
the SQL-layer tests (CI uses `sql/ubixcore_test.sql` rebuilt by `database:resetSchema test`).

```bash
composer install
git config core.hooksPath .githooks   # pre-push gate: bin/ubix code:review --phpunit=off
cp .env.example .env                  # optional for local dev; CI and containers take their environment from uBixVault / the pod spec

vendor/bin/phpunit                    # framework tests
vendor/bin/phpstan                    # level max, bleeding edge
vendor/bin/phpcs                      # the Ubix standard
bin/ubix code:review                  # all three as one gate

cd js/Ubix && npm install && npm run prepack   # the Svelte library (svelte-package + publint)
```

Layout:

```
php/Ubix/          the framework — Bootstrap/, Console/, Controller/, Middleware/, Repository/,
                   DataType/, Model/, Payload/, Service/, Tests/ (shipped PHPUnit bases),
                   ruleset.xml + Sniffs/ (the Ubix phpcs standard), phpstan.neon (baseline)
skeleton/          the create-project template, published by the tag pipeline
js/Ubix/           @ubixsys/ubixcore
tests/             framework tests, mirroring php/Ubix/
sql/               ubixcore_test.sql (CI fixture) + the tracker's init migration
docs/standards/    the house standards hosts inherit
docs/projects/     how this repo became a framework + skeleton (ubixcore-oss/)
bin/ubix · public/index.php   thin entry points, same shape as the skeleton's
```

`Ubix\` is reserved for the framework; product code never lives here. Products built
on uBixCore live in their own host repos generated from the skeleton.

## Releasing

Tag `dev`: `git tag -a v0.2.0 && git push origin v0.2.0`. The tag pipeline publishes the
framework and the npm package from this project's registries and subtree-splits
`skeleton/` into `ubixsys/ubixcore-skeleton`. Hosts upgrade with
`composer update ubixsys/ubixcore` and commit the lock.

## Coordination

Several agent sessions may work this repo at once. Register a lane in `AGENTS-COORD.md`
(copied from the template) before branching; `dev` is MR-only. Full rules in
`docs/standards/branching-and-git-workflow.md`.
