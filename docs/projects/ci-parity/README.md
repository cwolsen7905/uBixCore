# CI Parity — Project

**Status:** Active — slice 1 (lint-and-test stage, `-test` image, Discord notifications, pre-push hook) built 2026-08-27 on `feat/ci-parity`; gate is advisory until driven green (CP-05).

Bring ubixcore's GitLab pipeline up to project-neptune's shape — lint-and-test gate, migration verify/apply, promotion + rollback, destructive-PR detection, Claude MR review, failure notifications — with **Discord** replacing Slack. Secrets (webhooks, API keys) live in ubixvault at `secret/ubixcore/*`, read by CI with the read-only tokens `UBIXVAULT_CI_TOKEN_DEV` / `UBIXVAULT_CI_TOKEN_PROD` (policy `ubixcore-ci-ro`).

## Discord channels

| Channel | Webhook key (vault `secret/ubixcore/discord`) | Posts |
|---|---|---|
| `#ubixcore-ops` | `ops_webhook_url` | deploys, promotions, rollbacks (`bin/deploy.sh` trap) |
| `#ubixcore-alerts` | `alerts_webhook_url` | red pipelines (`.notify_failure`), destructive-migration detection |
| `#ubixcore-reviews` | `reviews_webhook_url` | Claude MR review summaries |

`bin/lib/notify.sh` → `discord <channel> <color> <title> <text>`; local override `DISCORD_<CHANNEL>_WEBHOOK_URL`.

## Work matrix

Status: `Todo` · `Build` · `Done` · `Dropped`.

| ID | Task | Status | Notes |
|---|---|---|---|
| CP-01 | `lint-and-test` stage: static-checks (phpcs+phpstan), phpunit (test DB via `database:resetSchema`), js-lint; `Dockerfile_Test` layered on the runtime image | Done | `allow_failure: true` until CP-05 |
| CP-02 | Discord notify: `bin/lib/notify.sh`, `.notify_failure` → alerts, `deploy.sh` → ops | Done | webhooks from vault by branch |
| CP-03 | `.githooks/pre-push` running `ubix code:review -n`; `code:review` exits FAILURE on violations | Done | install: `git config core.hooksPath .githooks` |
| CP-04 | Unit-test DB: `ubixcore-test-db` MariaDB 10.11 container on the runner host (docker-bridge IP `172.17.0.1:3307`, creds in ubixvault `secret/ubixcore/test-db`, root creds `/root/ubixcore-test-db.env` on the VM); phpunit job pulls creds via `vault_kv`, runs `resetSchema test` + creates `SYSTEMS` + `migrate:up --target=test` | Done | needs a unit-test DB/schema decision (neptune uses per-pipeline `t<id>_` prefixes — resetSchema here has no `--prefix` yet) |
| CP-05 | Drive `code:review` to green — remaining phpunit: 36 failures (`testFollowingUbixStandards`) + 22 errors in neptune-DB tests to port or delete (`MigrationPdoSqlServiceTest` uses `STUDIOS.Broadcasters`; `MigrationRunner/Status/ApplyServiceTest` per-DB fixtures; `Prospect*`, `SoloAcquisitionCampaign` repos) — overlaps Sowing.me M0-01 — 37 `testFollowingUbixStandards` failures + ~10 concrete classes without a test case (CorsMiddleware, SessionAuthenticationMiddleware, User, EmailConfirmationToken, EmailService, UserId, UserOptions, SchemaMigrationOptions, UbixDatabase, Bootstrap/vault.php) (baseline 2026-08-27: phpcs 858, phpstan 1344, phpunit 1 = 2203; 774 auto-fixable) then flip `allow_failure` off + make the hook blocking | Todo | run `ubix code:review` and accept auto-fix first |
| CP-06 | Fix sniff namespace typo: expected docblocks read `\Ubis\…` instead of `\Ubix\…` (`UbixConcreteClassOrEnumTestCase`-family sniff) | Todo | inflates CP-05 count |
| CP-07 | neptune-sync the MCR/CLI: `code:review --phpunit=off --record`, `database:resetSchema --target/--prefix`, `dropSchemas`, CHANGELOG lint | Todo | via `/neptune-sync` |
| CP-08 | `migrate-verify` + `migrate-apply` jobs, `destructive-pr-detect` → alerts | Todo | runner exists (`docs/projects/migrations/`) |
| CP-09 | `promote-to-staging` / `promote-to-main` (auto, destructive-halt + manual ack jobs) via `bin/promote.sh` → ops/alerts | Done | main is AUTO until live — flip `promote-to-main` to `when: manual` at launch. `rollback.sh` still Todo (CP-14). Needs `GITLAB_PROMOTE_TOKEN` |
| CP-10 | `claude-review-mr` job → reviews channel; needs `secret/ubixcore/anthropic.ci_api_key` | Todo | |
| CP-11 | `CHANGELOG.md` + deletion guard in pre-push | Todo | ubixcore has no CHANGELOG yet |
| CP-13 | GitLab registry cleanup policy for `ubixsys/ubixcore` (expire `<ref>-<sha>` tags older than N days, keep `dev|staging|main`) + default artifact expiry — the VM-side monthly GC only reclaims what the policy untags | Todo | GitLab UI/API |
| CP-12 | `environment:` on deploy jobs (enables GitLab env-scoped vars) | Done | dev / staging / main |
| CP-14 | `bin/rollback.sh` onto immutable `<ref>-<sha>` tags → ops | Todo | |
| CP-16 | PHP 8.5: all Dockerfiles on `nginx-php85-fpm-memcache` (suite identical to 8.4; phpstan +43 sniff-only `T_*` notes → add phpcs constant stub to phpstan bootstrap) | Done | needs baseimages `:latest` for staging/main (promotion) |
| CP-17 | `database:resetSchema` should create the `SYSTEMS` tracker DB itself (CI creates it inline today); `UbixDatabase` enum is neptune's DB list — replace with `SOWINGME`/`SYSTEMS` (M0-01) | Todo | |
| CP-15 | Registry migrated to the metadata DB 2026-08-28 (online GC active; offline GC retired after it dropped multi-arch child manifests). Cleanup policies (CP-13) still to add | Done | snapshot `backups/registry-pre-import-2026-08-28.tgz` |

## Status log

- **2026-08-28 (M0-01 landed via ci-parity lane)** — phpcs **0**, phpstan **0**. Remaining phpunit: house-standard violations in Sowing.me code (`testFollowingUbixStandards`: controllers depend on repositories directly → need a UserService/TokenService layer; `CorsMiddleware` lacks `$logger`; SQL repos lack the `query(<Options>)` pattern) plus the migration-service tests that still assume neptune schemas — next CP-05 slice; then flip lint-and-test to blocking.

- **2026-08-28 (CP-05 sweep)** — phpcs 2203-era baseline → **2** (both in neptune leftover models); phpstan 1414 → **373** (config excludes were still `Vsm`; typed hydration in User/EmailConfirmationToken repos; AuthController payload narrowing + two real bugs; AdminApi dead InternalAdminApi routes removed). **328 of the remaining 373 phpstan findings and all 22 phpunit errors live in neptune-leftover code** — M0-01 (delete Performer/AgeVerification/Notification/Transaction/AdminUser/Broadcaster/Studio/Slug/DuplicateProspect/FanClub/BillingTransaction services, models, DTOs and their tests) is the next step to a green gate.

- **2026-08-28 (late)** — baseimages: php84 image fixed (pinned alpine 3.22), php85 image added, promotion chain dev→staging→master live. Registry → metadata DB. ubixcore MRs: !74 phpstan paths, !75 test-scan crash, !77 test container/DI, !78 resetSchema, !76 PHP 8.5, this MR (test DB). Job-token pulls from baseimages needed the ubixcore bot granted access on `k8s`.

- **2026-08-28** — !72 merged. Registry GC incident (see memory `gitlab-vm-ops`): all multi-arch images rebuilt via baseimages `dev` push. `feat/ci-promotion`: CP-09 + CP-12 built, `CI_JOB_TOKEN` ARG/ENV removed from runtime Dockerfiles (cache-buster + secret in image history).

- **2026-08-28** — GitLab VM ran out of disk (push `unpacker error`); grown to 249G, registry GC + buildx prune freed ~70G, maintenance cron installed on the VM (see memory `gitlab-vm-ops`). `feat/ci-parity` pushed.

- **2026-08-27** — Branch `feat/ci-parity` in worktree `../ubixcore-worktrees/ci-parity`. Vault: `secret/ubixcore/discord` on dev+prod; `ubixcore-ci-ro` policy + 1y tokens; GitLab vars added by the user. CP-01..03 built. `code:review` baseline 2203 violations → gate advisory.
