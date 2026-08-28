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
| CP-04 | GitLab CI variables `TEST_MYSQL_WRITE_*` for the phpunit job (currently falls back to baked `.env`) | Todo | needs a unit-test DB/schema decision (neptune uses per-pipeline `t<id>_` prefixes — resetSchema here has no `--prefix` yet) |
| CP-05 | Drive `code:review` to green (baseline 2026-08-27: phpcs 858, phpstan 1344, phpunit 1 = 2203; 774 auto-fixable) then flip `allow_failure` off + make the hook blocking | Todo | run `ubix code:review` and accept auto-fix first |
| CP-06 | Fix sniff namespace typo: expected docblocks read `\Ubis\…` instead of `\Ubix\…` (`UbixConcreteClassOrEnumTestCase`-family sniff) | Todo | inflates CP-05 count |
| CP-07 | neptune-sync the MCR/CLI: `code:review --phpunit=off --record`, `database:resetSchema --target/--prefix`, `dropSchemas`, CHANGELOG lint | Todo | via `/neptune-sync` |
| CP-08 | `migrate-verify` + `migrate-apply` jobs, `destructive-pr-detect` → alerts | Todo | runner exists (`docs/projects/migrations/`) |
| CP-09 | `promote-to-staging` / `promote-to-main` / `rollback.sh` with immutable `<ref>-<sha>` tags → ops | Todo | tags already produced by `.build` |
| CP-10 | `claude-review-mr` job → reviews channel; needs `secret/ubixcore/anthropic.ci_api_key` | Todo | |
| CP-11 | `CHANGELOG.md` + deletion guard in pre-push | Todo | ubixcore has no CHANGELOG yet |
| CP-13 | GitLab registry cleanup policy for `ubixsys/ubixcore` (expire `<ref>-<sha>` tags older than N days, keep `dev|staging|main`) + default artifact expiry — the VM-side monthly GC only reclaims what the policy untags | Todo | GitLab UI/API |
| CP-12 | `environment:` on deploy jobs (enables GitLab env-scoped vars) | Todo | |

## Status log

- **2026-08-28** — GitLab VM ran out of disk (push `unpacker error`); grown to 249G, registry GC + buildx prune freed ~70G, maintenance cron installed on the VM (see memory `gitlab-vm-ops`). `feat/ci-parity` pushed.

- **2026-08-27** — Branch `feat/ci-parity` in worktree `../ubixcore-worktrees/ci-parity`. Vault: `secret/ubixcore/discord` on dev+prod; `ubixcore-ci-ro` policy + 1y tokens; GitLab vars added by the user. CP-01..03 built. `code:review` baseline 2203 violations → gate advisory.
