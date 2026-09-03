# Test-DB Prefix + `migrate:diff --mode=replay` Plan

**Status:** Draft pending review (2026-05-14)
**Owner:** Christopher W. Olsen
**Standard reference:** [`docs/standards/migrations.md`](../../standards/migrations.md)
**Related plans:** [`./plan.md`](./plan.md) (v1 migration runner — code complete), [`./cutover-runbook.md`](./cutover-runbook.md) (operational runbook)

This plan operationalises a design decision discussed and locked on 2026-05-14 alongside the v1 migration runner + frozen-baseline policy work: the local MariaDB pod hosts **two parallel database namespaces** — the unprefixed sandbox set (dev's working data) and a `TEST_`-prefixed mirror used by PHPUnit. The prefix is added transparently by the `SqlService` layer when `PHPUNIT_RUNNING=1`, so application code is unchanged.

Solves three problems in one architectural move:

1. **Test isolation.** PHPUnit can `TRUNCATE` and mutate freely without touching dev's working data on the same pod. The current `AbstractPdoSqlService::query()` "skip TRUNCATE unless username is root" hack disappears.
2. **Trustworthy `migrate:diff` reference.** The `TEST_*` namespace is by construction `baseline + every on-disk migration applied in order`, because `database:resetSchema` (in test mode) brings it there and the sync service keeps it there. `migrate:diff --mode=replay` becomes "diff target cluster against `TEST_*`."
3. **`code:review` reliability.** Before PHPUnit runs as part of the meta `code:review` command, the sync service guarantees the test DB has every on-disk migration applied — tests run against the current schema, not last week's.

---

## Goal

Make the local MariaDB pod safely shareable between dev work and PHPUnit, and use the resulting always-current `TEST_*` namespace as the canonical replay reference for `migrate:diff`.

By end-of-plan:

- A `code:review` run (which invokes PHPUnit) is self-healing against schema lag: any pending on-disk migration is applied to the `TEST_*` namespace before tests start.
- `vendor/bin/phpunit` from the CLI does the same — the bootstrap runs the sync service.
- `migrate:diff --mode=replay` (the canonical drift detector per `migrations.md` §9 v1.4) ships, using `TEST_*` as the always-current reference.
- The `TRUNCATE`-skip hack in `AbstractPdoSqlService::query()` is removed.

---

## Background — design decisions locked 2026-05-14

The conversation that produced this plan landed on these choices. Captured here so future-me doesn't re-litigate.

1. **Single MariaDB pod, two namespaces.** Not two pods. The prefix layer is what isolates dev from test, not separate infrastructure. CI may use a dedicated sidecar pod via `TEST_MYSQL_WRITE_*` env vars — the prefix logic is harmless on a clean pod.
2. **`PHPUNIT_RUNNING=1` is the trigger.** Already set by the existing PHPUnit harness; already detected by `MigrationPdoSqlService`. The new prefix logic keys off the same signal.
3. **`TEST_MYSQL_WRITE_*` env vars stay independent.** They point to whatever pod tests should connect to; dev still uses `MYSQL_WRITE_*`. In local dev they're typically the same pod; in CI they diverge.
4. **Rewriting happens transparently inside `SqlService`.** Application code (controllers, services, repos) never sees `TEST_VSCASH`; the rewrite is invisible. Shell-out paths (`MigrationApplyService`, `DestructiveBackupService`, `SchemaDiffService`, `ResetSchemaCommand`) call the prefixer explicitly because they bypass PDO.
5. **Rewrite covers BOTH table references and string literals.** `WHERE table_schema = 'VSCASH'` (in `INFORMATION_SCHEMA` queries) gets the `'TEST_VSCASH'` rewrite too, so `migrate:status` pre-flight against the test set works correctly.
6. **`UbixDatabaseCatalogue` is single source of truth for the DB list.** Globs `sql/*.sql`. Replaces the inline glob in `ResetSchemaCommand` and feeds the prefixer.
7. **Bootstrap syncs once per PHPUnit process.** Not per test. Status check is ~100ms; `migrate:up --yes` only fires when something's pending.

---

## Out of scope (deferred)

- **Separate scratch cluster for `migrate:diff` in production CI.** Under this plan the test pod IS the scratch cluster. CI's dedicated MariaDB sidecar fits the same shape — the `TEST_MYSQL_WRITE_*` env vars get pointed at it. A literal "spin up a fresh pod just for the diff job" pattern is unnecessary.
- **Per-feature-branch DB namespacing** (e.g., `BRANCH_foo_VSCASH`). The prefixer architecture supports it cheaply — different env-var-driven prefix string — but no concrete demand yet. Note it as future leverage.
- **MySQL-side schema search-path equivalent.** PostgreSQL has `SET search_path`; MySQL doesn't. The prefix-in-SQL approach is the workaround. Not deferred so much as "out of architectural scope."

---

## Slice plan

Each slice is a single atomic commit. Each one ships green-tests + green-lints; nothing left half-done at slice boundaries.

### Slice 1 — `UbixDatabaseCatalogue` service

Pure utility. One method: `getAll(): array<string>` — returns the list of Ubix-consumed database names by globbing `sql/*.sql` (excluding `migrations/` and `seeds/` subdirectories). Sorted, deduplicated. Lives at `php/Ubix/Service/Database/UbixDatabaseCatalogue.php`.

**Behaviour:**
- `getAll()` returns `['ADSERVER', 'ASIA', 'BI', 'BILLING', …]` based on the current `sql/` contents.
- Memoised per-instance — the catalogue doesn't change mid-process.

**Refactor:** `ResetSchemaCommand::discoverDatabases()` is replaced by an injected `UbixDatabaseCatalogue` call. Same behaviour, single owner.

**Test surface:** standards test + behavioural tests for sort order, exclusion of `migrations/` and `seeds/` subdirs, empty-directory case.

**Why first:** unblocks every downstream slice that needs the DB list. No dependency on anything new.

---

### Slice 2 — `TestDatabasePrefixerService` + heavy test pass

Pure-function service. Takes:
- SQL string
- `UbixDatabaseCatalogue` (injected)
- `bool $shouldPrefix` flag

Returns: SQL with every Ubix DB name rewritten to `TEST_<DB>` when `$shouldPrefix` is true; passthrough otherwise.

**Two rewrite passes, both comment-aware (strip `--` and `/* */` regions before matching, re-attach):**

1. **Table-reference pass.** Matches `\b<DB>\.` where `<DB>` is any catalogued name. Rewrites to `TEST_<DB>.`.
2. **String-literal pass.** Matches `'<DB>'` (single-quoted exact match) for any catalogued name. Rewrites to `'TEST_<DB>'`. This is the path that makes `INFORMATION_SCHEMA` queries work.

**Edge cases that have to be tested:**

- Column name happens to match a DB name (`SELECT VSCASH FROM Foo` — should NOT rewrite, because no trailing `.`).
- DB name appears inside a string column comment (`COMMENT 'see VSCASH for details'` — should NOT rewrite under the table-ref pass; only the explicit-`'VSCASH'` string-literal pass would, and only if the operator is querying for that literal).
- Multi-statement bodies (`CREATE TABLE VSCASH.Foo (…); ALTER TABLE SYSTEMS.Bar …;`).
- Cross-DB references in a single migration body (`Database: VSCASH` header but body references `SYSTEMS.Schema_Migrations`).
- `Schema_Migrations` references specifically — these MUST get prefixed when `$shouldPrefix=true`, otherwise the runner's tracker reads/writes hit the unprefixed table.
- `INFORMATION_SCHEMA.TABLES WHERE table_schema = 'VSCASH'` — table-ref pass leaves `INFORMATION_SCHEMA` alone (it's not in the catalogue), string-literal pass rewrites `'VSCASH'` to `'TEST_VSCASH'`.

**No I/O, no PDO, no shell-out. Just string manipulation.** This is where 80% of the bug surface lives — generous behavioural test coverage is the cost of correctness.

**Why second:** every later slice depends on this. It must be rock solid before anything else.

---

### Slice 3 — Wire prefixer into `SqlService` PDO path

`MysqlPdoSqlService` and `MigrationPdoSqlService` both:
- Inject `TestDatabasePrefixerService`.
- On construction (or via `ensureInitialized` for the migration service), detect `PHPUNIT_RUNNING=1` and stash a `bool $shouldPrefix` flag.
- In `AbstractPdoSqlService::query()`, `getColumn()`, `getRow()`, `getRows()`: run the prefixer over `$sql` before `prepare()`.
- DSN's `dbname` parameter gets `TEST_` prefixed when in test mode.

**Removable in the same commit:** the `if (stripos($sql, 'TRUNCATE') !== false && $this->writePdoConstructorParameters->username !== 'root')` hack in `AbstractPdoSqlService::query()`. Once tests target `TEST_*` DBs, they can `TRUNCATE` freely.

**Test surface:** the existing PDO-service standards tests cover construction; new behavioural tests verify that `getRow('SELECT * FROM VSCASH.Foo')` in `PHPUNIT_RUNNING=1` mode actually queries `TEST_VSCASH.Foo`.

**Why third:** turns on the prefix for every PDO query. After this slice, repos that hit Ubix DBs via the SqlService work correctly under tests — modulo the migration runner's shell-out paths which are still pending.

---

### Slice 4 — Wire prefixer into `MigrationApplyService` shell-out

`MigrationApplyService::apply()` shells out to `mysql` CLI with the migration body piped in. The body is a string; the prefixer rewrites it before piping.

**Critical ordering inside `apply()`:**

1. `DestructiveStatementDetectorService::detect($body)` — runs on the **unprefixed** body so the regex matches canonical names. Already comment-aware.
2. Prefixer runs (if `PHPUNIT_RUNNING=1`).
3. Pre-flight `tableExists()` check uses the prefixed schema name (the reader query goes through the SqlService, which auto-prefixes, so this is correct without extra effort).
4. Shell out to `mysql` with the prefixed body.
5. Tracker insert: also goes through SqlService → auto-prefixed → tracker row lands in `TEST_SYSTEMS.Schema_Migrations` when in test mode.

**`applyBootstrap()`** follows the same path. The bootstrap migration creates `Schema_Migrations` — in test mode, it creates `TEST_SYSTEMS.Schema_Migrations`.

**Test surface:** behavioural test that `apply()` under `PHPUNIT_RUNNING=1` shells out with a prefixed body (can be verified by stubbing `ProcessService` and asserting the captured command).

**Why fourth:** unlocks running migrations against the test set. Without this, `migrate:up` against the test pod still tries to mutate unprefixed DBs.

---

### Slice 5 — Wire prefixer into `DestructiveBackupService` + `SchemaDiffService`

Both shell out via `mysqldump`. Both reference DB + table names in the shell command line. Both need prefixing in test mode.

- `DestructiveBackupService::snapshot()` — the `mysqldump <DB> <table1> <table2> …` invocation gets the prefixed `<DB>` name. Test mode produces backups under `/var/ubix-migration-backups/<id>/test-<timestamp>.sql.gz` (the `<env>` segment becomes `test` when `PHPUNIT_RUNNING=1` — separate concern from the prefix, but worth tying together).
- `SchemaDiffService::diffAll()` — the `mysqldump --no-data <DB>` invocations get the prefix. Diff results are reported against the canonical name (`SchemaDiffResult::$database = 'VSCASH'` not `'TEST_VSCASH'`) so the operator-facing output doesn't leak the implementation detail.

**Test surface:** behavioural test per service verifying the shell-out command line gets the prefix.

**Why fifth:** completes the shell-out path coverage. After this, all five migration-runner services consistently prefix in test mode.

---

### Slice 6 — `database:resetSchema` test-mode behaviour

Under `PHPUNIT_RUNNING=1`, `ResetSchemaCommand`:

- Auto-discovered DBs from the catalogue stay the canonical names; the prefix is added when constructing the per-DB `DROP DATABASE` / `CREATE DATABASE` / `mysql <DB> < sql/<DB>.sql` shell-outs.
- **The dump body itself needs prefixing** before being piped into mysql. `sql/VSCASH.sql` contains `CREATE TABLE VSCASH.Foo …`; in test mode the prefixer rewrites this to `CREATE TABLE TEST_VSCASH.Foo …` so the import lands in the right namespace.
- The inline `migrate:up --yes` call at the end also runs under `PHPUNIT_RUNNING=1` and prefixes everything — the migration bodies, the tracker writes, the works.

**Also adds a `--test` operator flag** for manual invocation outside PHPUnit. Sets `PHPUNIT_RUNNING=1` internally for the duration of the command. Equivalent to `PHPUNIT_RUNNING=1 php bin/ubix database:resetSchema --target=sandbox --drop-database`.

**Refused combinations:**
- `--test --target=staging` → refused. Test mode only makes sense against dev/sandbox tier.
- `--test --target=prod` → refused (already refused by §11.3-style guard).

**Test surface:** behavioural test that `--test` triggers the prefix path; smoke-test against the local pod that `database:resetSchema --test` produces `TEST_*` DBs alongside the unprefixed ones.

**Why sixth:** the test set has to exist before anything can target it. Slice 6 brings it into existence.

---

### Slice 7 — `LocalTestSchemaSyncService` + wire into `code:review`

New service: `LocalTestSchemaSyncService::sync(Output): bool`.

**Behaviour:**
1. Call `MigrationStatusService::getStatus(databaseFilter: null)` under `PHPUNIT_RUNNING=1` mode (so it queries the `TEST_*` tracker).
2. Filter to pending migrations (`!isApplied`).
3. If pending is empty AND no `--verify`-style checksum drift exists → print `<info>Local test DB current</info>`, return true.
4. If pending is non-empty → print "Applying N pending migrations to local test DB…", invoke `migrate:up --yes` inline (under `PHPUNIT_RUNNING=1`), return based on its exit code.
5. If checksum drift → return false with a clear "test DB has drift; run `database:resetSchema --test --drop-database` to nuke and rebuild" message.

**Wire into `MachineCodeReviewService`** at the top, before the PHPUnit step. If sync fails, `code:review` aborts before any tooling runs.

**Test surface:** behavioural tests for the three branches (current / pending / drift). Mock the inner `migrate:up` invocation; assert correct branching.

**Why seventh:** with the test set existing (slice 6), keeping it current is the next gate. After slice 7, `code:review` is self-healing.

---

### Slice 8 — PHPUnit bootstrap calls the sync service

`tests/bootstrap.php` (or wherever the PHPUnit bootstrap lives — needs locating in slice 1's research step):

1. Sets `PHPUNIT_RUNNING=1` (probably already does).
2. Builds the DI container (probably already does).
3. **New:** resolves `LocalTestSchemaSyncService` from the container and calls `sync()`.
4. If sync fails, dies with the error so PHPUnit doesn't run against a stale test DB.

**Per-process, not per-test.** PHPUnit boots once, syncs once, then every test class runs against the now-current `TEST_*` namespace. ~100ms when nothing's pending.

**Test surface:** existing PHPUnit suite continues to pass. Manual smoke: drop a TEST_VSCASH table, run `vendor/bin/phpunit`, watch bootstrap reapply the migration.

**Why eighth:** generalises slice 7's safety net to every PHPUnit invocation, not just `code:review`. Bare `vendor/bin/phpunit` is now also self-healing.

---

### Slice 9 — `migrate:diff --mode=replay`

Under the test-DB-prefix design, replay-mode is structurally simple:

1. Operator runs `migrate:diff --target=prod --mode=replay`.
2. Command first calls `LocalTestSchemaSyncService::sync()` to ensure the test DB is current. Aborts if sync fails.
3. For each catalogued DB:
   - `mysqldump --no-data` from the target cluster (the canonical name, e.g. `VSCASH`).
   - `mysqldump --no-data` from the test pod (the prefixed name, `TEST_VSCASH`). Normalise the dump to strip the `TEST_` prefix from any embedded references so the line-diff matches.
   - Apply the existing `SchemaDiffService` normaliser to both sides (banner stripping, AUTO_INCREMENT counters, conditional SET block).
   - Line-diff. Report drift as `extraInLive[]` / `missingFromLive[]` per the existing `SchemaDiffResult` shape.
4. Exit non-zero if any DB has drift.

**`--mode=reference-dump` stays available** as the legacy advisory mode (per `migrations.md` §9 v1.4). `--mode=replay` becomes the new default.

**New service:** `SchemaReplayDiffService` — sibling of `SchemaDiffService`, shares the normaliser. Could absorb into `SchemaDiffService` directly, but keeping them separate makes the slice atomic and the mode-switch in `DiffCommand` clean.

**Test surface:** behavioural tests for the prefix-strip normaliser; an end-to-end test that drives the full diff against a contrived live + test pair (slow-ish, optional in the standard sweep).

**Why ninth:** depends on slices 6-8 to keep the test DB trustworthy. Premature before that.

---

### Slice 10 — Docs + memory + CHANGELOG

Pure documentation slice. Code is feature-complete after slice 9.

- **`docs/standards/migrations.md`** — new §13 (or appropriate section) documenting the `TEST_*` namespace convention. §9 updated: replay-mode is now the default; reference-dump is the legacy advisory mode. Standard bumped to v1.5.
- **`docs/projects/migrations/cutover-runbook.md`** — §3.2.1 cross-references the sync service. New §3.6 cross-reference to test-DB design. Bumped to v1.6.
- **`docs/projects/migrations/plan.md`** — version-history row noting v2 work landed.
- **`.env` / `.env_prod` examples** — confirm `SANDBOX_MYSQL_WRITE_*` and `TEST_MYSQL_WRITE_*` documentation is accurate. Add `PHPUNIT_RUNNING=1` to test bootstrap notes.
- **`CHANGELOG.md`** — single dated entry summarising the whole arc: test-DB prefix architecture, sync service, replay-mode diff.
- **Memory note** — new `project_test_db_prefix.md` capturing the design decision so future sessions don't re-litigate. Link from `MEMORY.md`.

---

## Risks

### High

- **Prefixer correctness.** Wrong rewrite → tests mutate wrong DB. Mitigated by heavy slice-2 test coverage including the edge cases listed. Worth a dedicated test pass before relying on it.
- **Performance regression.** Every test query now runs through the prefixer (regex passes). Probably negligible (microseconds per query) but worth measuring against the existing test suite.

### Medium

- **Cross-DB migrations.** A migration whose body references both `VSCASH.Foo` and `SYSTEMS.Bar` needs both prefixed. The prefixer iterates the catalogue so this works automatically — but it's worth a behavioural test specifically for cross-DB bodies.
- **Existing test data.** Existing PHPUnit fixtures may depend on the unprefixed `VSCASH` etc. After slices 3-6 land, those fixtures break. Mitigation: run the full PHPUnit suite at each slice boundary; fix fixtures as they surface. Likely a half-day of cleanup spread across slices 3-8.
- **String literal false positives.** A test setup that inserts the literal string `'VSCASH'` into a column for some reason would get rewritten to `'TEST_VSCASH'`. Probably no real cases, but worth grepping the test suite for `'VSCASH'` etc. before declaring slice 2 done.

### Low

- **Migration body literal collision.** A migration that intentionally references the unprefixed DB name in a string column comment (`COMMENT 'historic: VSCASH'`) wouldn't be a problem because the table-ref pass requires the `<DB>.` trailing dot. The string-literal pass only matches exact-quoted forms (`'VSCASH'`).
- **CI parallelism.** If CI runs PHPUnit in parallel jobs sharing a MariaDB pod, two jobs would both target `TEST_*`. Mitigation: CI provisions a per-job sidecar (current pattern), so this isn't a real risk under the existing CI shape.

---

## Open questions (to resolve before slice 1)

1. **Where exactly does the PHPUnit bootstrap live?** Need to locate the file in slice 1's intro before slice 8 can land. Probably `tests/bootstrap.php` or referenced from `phpunit.xml`.
2. **Does `MigrationCredentialResolverService` (shell-out cred path) need a `TEST_MYSQL_MIGRATION_*` analogue?** Probably not — tests typically use the sandbox `root` user which has all grants. Confirm in slice 1.
3. **Should the bootstrap sync also seed via `seed:apply` for canonical seeds?** Out of scope for v1 — seeds are idempotent and the test suite probably manages its own fixtures. Note for follow-up.

---

## Document Control

| Version | Date       | Author                | Notes |
|---------|------------|-----------------------|-------|
| 1.0     | 2026-05-14 | Christopher W. Olsen | Initial plan drafted in the same session that locked the frozen-baseline policy + shipped the migrate:* `--target` / `--username` plumbing + rewired `database:resetSchema`. 10-slice breakdown for the `TEST_*` namespace architecture + `migrate:diff --mode=replay`. Status: pending review. Implementation deferred to a future session per operator request. |
