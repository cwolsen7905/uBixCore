---
name: neptune-sync
description: Sync ubixcore with advancements in ~/git/project-neptune (the maintained upstream fork parent) WITHOUT porting neptune's apps. Covers the shared PHP framework (php/Vsm → php/Ubix), CLI commands, machine code review (MCR) toolchain and sniffs, docs/architecture, docs/standards, .claude skills, and CI/tooling config — with the Vsm→Ubix / neptune→ubix rename and the fixed list of things that must NOT be renamed. Invoke when the user says "sync with neptune", "port from neptune", "catch ubixcore up", "what's new in neptune", "neptune parity", "bring over the MCR/CLI/standards changes", or asks to port a specific neptune command/doc/sniff.
---

# neptune-sync — keep ubixcore in parity with project-neptune's framework & tooling

ubixcore is a fork of project-neptune's **framework, tooling and docs**, not its product apps. Neptune keeps moving; this skill is the repeatable procedure for pulling the framework-level advances across. Do the phases in order; every phase ends with a checkpoint the user can see.

Upstream: `~/git/project-neptune` (read-only for this skill — never edit it). Target: `~/git/ubixcore` on branch `dev`.

## 0. Ground rules (read every time)

**In scope — port it:**
- `php/Vsm/**` framework code → `php/Ubix/**` (DataType, Payload, DTO, Repository base classes, Service, Middleware, SlimApp/SlimHandler, Console, Sniffs, Collection, Enum, Exception, External, HttpClient, Renderer, SessionHandler, SimpleCache, Attribute, Filters, `ruleset.xml`)
- `php/Vsm/Console/Command/**` — CLI commands (`bin/neptune` → `bin/ubix`)
- MCR: `code:review` command + tools config (`phpcs.xml`, `phpstan.neon`, `rector.php`, `.cspell*`, `peck`, knip/eslint/prettier configs at repo root, `config/ci/*`)
- `tests/**` that cover ported classes (mirror path, same rename)
- `docs/architecture/**`, `docs/standards/**`, `docs/README.md` (index — trimmed to what exists here)
- `docs/projects/migrations/**` (already ported; keep in sync)
- `.claude/skills/neptune-*` that are framework/tooling skills (code-review, gotchas, deploy-manifests, vsm-extract, arch-audit) — port as `.claude/skills/<same-name>/` with rename applied
- `js/Vsm/**` shared Svelte lib → `js/Ubix/**`; `py/Vsm` only if/when ubixcore grows a `py/` leg
- Root tooling: `composer.json` deps/scripts, root `package.json` workspaces, `Dockerfile*`, `.gitlab-ci.yml`, `.githooks/`, `bin/*` scripts

**Out of scope — never port:**
- `app/**` (neptune's apps: ProductApi/Js, InternalAdmin*, PerformerApplication*, RealtimeFanoutGo, RoomSfwCheckerPy, StudioAdminJs …)
- App-specific controllers/repos/models/DTOs/enums under `php/Vsm/` — anything whose only consumers are neptune apps (see §2 classification)
- `docs/api-contracts/`, `docs/surfaces/`, `docs/data-models/`, `docs/audits/`, `docs/projects/*` other than `migrations/` — these describe neptune's product
- `templates/` app templates, `sql/*.sql` baselines, `config/k8s/*` app manifests
- `BUGS.md`, `CHANGELOG` entries about apps, `AGENTS-COORD.template.md`

**Rename table (apply to ported files; BSD sed needs `[[:<:]]`/`[[:>:]]` for word bounds, not `\b`):**

| From | To |
|---|---|
| `Vsm\` namespace, `\Vsm` | `Ubix\` / `\Ubix` |
| `php/Vsm`, `js/Vsm`, `py/Vsm`, `tests/Vsm` | `php/Ubix`, `js/Ubix`, `py/Ubix`, `tests/Ubix` |
| `VsmStandards`, `VsmDateTime`, `VsmConcreteClassOrEnumTestCase(Interface)` | `UbixStandards`, `UbixDateTime`, `UbixConcreteClassOrEnumTestCase(Interface)` |
| `Vsm.ProjectNeptune.<Sniff>` (phpcs refs) | `Ubix.ProjectNeptune.<Sniff>` |
| `bin/neptune`, `neptune code:…`, `neptune migrate:…` (CLI invocations) | `bin/ubix`, `ubix code:…` |
| `/code/project-neptune`, image tag `project-neptune:` | `/code/ubixcore`, `ubixcore:` |
| `../neptune-worktrees` | `../ubixcore-worktrees` |
| prose "Project Neptune" / "Neptune" | "uBix Core" |

**Do NOT rename (ubixcore code still uses these verbatim — verify with grep before ever changing):**
`NEPTUNE_*` env vars (e.g. `NEPTUNE_MIGRATION_BACKUP_DIR`) · `APP_NAME=NeptuneCli` · the `vsm` npm/py package name (`js/Ubix/package.json` is still `"name": "vsm"`) · the `ProjectNeptune` sniff namespace / phpcs ruleset name · `neptune-go`, `neptune-claude-review`, `#neptune-alerts`, `neptune.json` (infra names) · `SYSTEMS.*` table names · `vsmedia.net` URLs.

## 1. Diff — what changed upstream since last sync

Last-sync marker: the newest `chore(sync)`/`feat(...)`/`docs:` commit in ubixcore that names neptune, plus the memory file `tooling-catchup-state` (lists known-missing items). Read both first.

```bash
N=~/git/project-neptune; U=~/git/ubixcore
# Framework tree diff (after virtual rename) — the authoritative "what's new" list
diff -rq <(cd $N/php/Vsm && find . -type f | sort) <(cd $U/php/Ubix && find . -type f | sort) | sort
ls $N/php/Vsm/Console/Command | sort > /tmp/n; ls $U/php/Ubix/Console/Command | sort > /tmp/u; diff /tmp/n /tmp/u
# Content drift for files present in both
for f in $(cd $N/php/Vsm && find . -name '*.php'); do [ -f $U/php/Ubix/$f ] && \
  ! diff -q <(sed 's/Vsm/Ubix/g' $N/php/Vsm/$f) $U/php/Ubix/$f >/dev/null && echo "DRIFT $f"; done
# Docs
diff -rq $N/docs/architecture $U/docs/architecture; diff -rq $N/docs/standards $U/docs/standards
# Tooling
for f in phpcs.xml phpstan.neon rector.php composer.json .gitlab-ci.yml; do diff -q $N/$f $U/$f; done
ls $N/.claude/skills; ls $U/.claude/skills
# Upstream history since the last sync date (from the marker)
git -C $N log --since="<last-sync-date>" --oneline -- php/Vsm docs/architecture docs/standards phpcs.xml phpstan.neon .claude/skills bin config/ci
```

Note: DRIFT hits where ubixcore *intentionally* diverges (e.g. `Bootstrap/`, `SowingMe*` controllers, `NeptuneCli` app name) are not sync targets — ubixcore may also have local fixes worth keeping; always `diff` before overwriting.

**Checkpoint 1:** present a table — *new files / drifted files / new commands / new docs / new skills*, each marked **port / skip (app-specific) / needs decision** — and proceed unless the user objects (in autonomous runs, proceed with the port/skip classification below).

## 2. Classify — framework vs app-specific

A neptune file under `php/Vsm` is **app-specific** (skip) if any of:
- Its namespace segment names a neptune app (`Controller/ProductApi`, `Repository/LiveCam*`, `*Performer*`, `*Room*`, `*Chat*`, `*FanClub*`, `*Affiliate*`, `*Studio*`, `*Whitelabel*`, `*Promo*`, `*Versus*`, `*Vod*`, `*Sfw*`, `*Flirt*`, `*Attribution*`, `*Recombee*`) — check with `grep -rl "<ClassName>" $N/app $N/php/Vsm` : if the only non-self consumers are `app/**` or other app-specific classes → skip.
- It is a Model/Repository/DTO for a neptune business table (anything not `SYSTEMS.*` / `Schema_Migrations` / `Country` / `State` / `MachineCodeReview*`).

It is **framework** (port) if it's consumed by `Console/`, `SlimApp/`, base classes, sniffs, tests infra, or by multiple unrelated apps. When a framework class drags an app-specific dependency, port the framework class and stub/decouple the dependency — record the decision in the checkpoint.

Console commands: port all except ones whose purpose is a neptune app (e.g. `Openapi` only if it's generic; `Py` only when a `py/` leg exists; `Seed` — port, it's generic; `Flag` — port with the feature-flag service if that's framework-level, else defer). Update the memory `tooling-catchup-state` with whatever is deferred and why.

## 3. Port

For each item (framework code first, then tests, then tooling config, then docs, then skills):

```bash
# copy + rename in one go (works for dirs)
rsync -a $N/php/Vsm/<Path>/ $U/php/Ubix/<Path>/
find $U/php/Ubix/<Path> -type f -exec sed -i '' -E \
  -e 's#Vsm\\#Ubix\\#g; s#\\Vsm#\\Ubix#g' \
  -e 's#php/Vsm#php/Ubix#g; s#js/Vsm#js/Ubix#g; s#tests/Vsm#tests/Ubix#g' \
  -e 's#[[:<:]]Vsm(Standards|DateTime|ConcreteClassOrEnumTestCase|ConcreteClassOrEnumTestCaseInterface)[[:>:]]#Ubix\1#g' \
  -e 's#bin/neptune#bin/ubix#g' {} +
```
Then the doc-prose renames (`[[:<:]]Neptune[[:>:]]`→`uBix Core`, `neptune (code|branch|py|migrations|migrate):`→`ubix \1:`) **only on `.md` files**.

After each batch: `grep -rn 'Vsm\|neptune' <ported paths>` and justify every hit against the do-not-rename list.

Docs specifics: after copying `docs/README.md`, drop the sections for folders that don't exist here (`api-contracts`, `surfaces`, `data-models`, `reviews`) and keep the "ported from neptune" banner at the top. Dangling root-relative links to unported neptune docs are tolerated (keeps the diff clean) — count them and report.

Skills specifics: port `.claude/skills/neptune-*` framework skills under the **same folder name** (the name is part of neptune's vocabulary); apply the rename table inside; drop entries that only concern neptune apps/pods. Keep `.claude/skills/README.md` catalog in sync.

## 4. Verify

```bash
cd $U && composer dump-autoload -q
vendor/bin/phpstan --no-progress
vendor/bin/phpcs --no-cache
vendor/bin/phpunit          # DB-backed tests need the dev DB (see memory db-host-env-mapping)
bin/ubix list               # every ported command registers
bin/ubix code:review        # if the MCR command exists — must be fully green
```
JS side (only if `js/Ubix` changed): `npm ci` at root, `npm run build` in `js/Ubix` and each `app/*Js`.

Fix rename fallout here, not by editing neptune. Common fallout: PSR-4 path ≠ namespace after a partial rename; sniff `installed_paths` in `phpcs.xml`; tests referencing `NeptuneCli` fixtures.

## 5. Land

- Commit per logical unit (e.g. `feat(cli): port seed:* commands from neptune`, `docs: sync standards with neptune @<short-sha>`), referencing the neptune commit range synced: `Synced-From: project-neptune <from-sha>..<to-sha>`.
- Update `docs/README.md` / `.claude/skills/README.md` catalogs if files were added.
- Update memory `tooling-catchup-state` (what was synced on which date, what's still deferred and why). That memory + the `Synced-From` trailer are the next run's starting marker.
- Do not push unless asked.

## Known deferred items (keep current)

As of 2026-08-27: Console commands `Flag`, `Openapi`, `Py`, `Seed` not yet ported; `py/` leg absent; `.claude/skills` not yet ported (neptune has code-review, arch-audit, deploy-manifests, gotchas, vsm-extract); `js/Ubix` package still named `vsm`. Docs `architecture/` + `standards/` synced 2026-08-27.
