# Migration Runner Implementation Plan

**Status:** Code complete; CI rollout paused mid-bring-up — see [`./cutover-runbook.md`](./cutover-runbook.md) §8 for the active rollout state and pickup triggers. All eight slices shipped 2026-05-07; the runner has been end-to-end validated against the local sandbox MariaDB pod but has not yet been clicked on a real CI pipeline.
**Owner:** Christopher W. Olsen
**Standard reference:** [`docs/standards/migrations.md`](../../standards/migrations.md)

This plan operationalises the migration standard into a working `bin/ubix migrate:*` command suite. The standard specifies the **contract**; this plan specifies the **implementation slices**. Each slice is a single atomic commit.

---

## Goal

Make the standard enforceable by shipping the runner. Until this lands, every reference in the standard to `bin/ubix migrate:*` is aspirational — schema changes still ride the manual path with no audit trail.

## Out of scope (deferred)

- `bin/ubix seed:apply` — seed runner. Spec'd in standard §6 but stays manual until after migrations ship. Slice 7 is the natural follow-up; not in this plan.
- `migrate:diff --mode=replay` — the canonical "rebuild from migration history" mode. Slice 5 ships `--mode=reference-dump` only; replay mode is M2.
- Tiered CI wiring — adding `migrate:status --verify` + `migrate:diff` to GitLab CI pipelines is its own ops task once the commands exist.
- Reconciling the 3 grandfathered legacy migrations (`feature_flags_schema.sql`, `feature_flags_align_environment_enum_to_env_class.sql`, `add_status_to_click_id_log.sql`) — done in slice 8 (the cleanup slice) once the runner is verified working.

---

## Open decisions to resolve in slice 1 (before code)

1. **Per-DB connection strategy.** The runner has to apply migrations against `VSCASH`, `SYSTEMS`, `ntl_db`, etc. — different databases. The existing `Ubix\Service\Sql\MysqlPdoSqlService` is constructed with one DSN at boot. Three options:
    - **(a) Per-DB SqlService factory** — `SqlServiceFactory::for(string $database): SqlService` builds a fresh `MysqlPdoSqlService` against the target. Cleanest. Used by the runner once per migration. **Recommended.**
    - (b) Switch DB via `USE <db>` statement on the existing connection. Risky across DDL — InnoDB DDL doesn't always honour `USE` consistently. Skip.
    - (c) Force every migration to qualify table names with the DB (`VSCASH.Foo`). The seed already does this. Works but means the runner connects to *some* DB; the choice is arbitrary. Half-fix.
2. **Where the credentials come from.** Existing CLI commands inherit the SQL connection from `MEMCACHE_SERVERS` / DB env vars in UbixCli's `Dependencies.php`. The factory needs to read those too; assume the DB user has rights across all schemas (it does on the shared cluster).
3. **Lock against concurrent runners.** Two engineers running `migrate:up` at once would race. Cheapest fix: take a `GET_LOCK('ubix_migrations', 30)` advisory lock at the start of `up`, release at the end. Document that staging/prod CI is the only place this risks happening, and CI runs are serialised by the deploy pipeline anyway. **Defer to slice 2** — call out in code comment so a future test can prove it.

These three are the only design questions. Everything else is mechanical.

---

## Slice plan

Each slice = one commit. Order matters: slices 1–4 unblock the runner being usable; slices 5–6 add detection; slices 7–8 close out. Safety layers from `migrations.md` §11 are interleaved into 1.5 and 2.

### Slice 1 — Scaffolding + `migrate:status` (read-only)

**Goal:** the command exists, it parses files on disk, it queries `Schema_Migrations`, it prints a status table. No mutation.

**Add:**
- `php/Ubix/DataTransferObject/Migration/MigrationFile.php` — readonly DTO: `id`, `targetDatabase`, `description`, `author`, `body`, `checksum`, `filePath`. Parsed from a file by the parser.
- `php/Ubix/DataTransferObject/Migration/AppliedMigration.php` — readonly DTO mirroring a `Schema_Migrations` row.
- `php/Ubix/Service/Migration/MigrationFileParserService.php` — reads a file path, parses the header (`Migration:` / `Database:` / `Description:` / `Author:`), splits header from body, computes SHA-256 of the body, returns `MigrationFile`. Throws `MigrationParseException` on malformed input.
- `php/Ubix/Service/Migration/MigrationFileScannerService.php` — scans `sql/migrations/` (path injected via constructor; default `__DIR__/../../../../sql/migrations`), sorts by filename ascending, returns `MigrationFile[]`. The scanner enforces the filename regex `^\d{14}_[a-z0-9_]+\.sql$`.
- `php/Ubix/Repository/SchemaMigration/SchemaMigrationReaderInterface.php` + `SchemaMigrationSqlRepository.php` — reads `SYSTEMS.Schema_Migrations` rows.
- `php/Ubix/Service/Migration/SchemaMigrationServiceFactory.php` (or `SqlServiceFactory.php` — name TBD) — solves the per-DB connection question. **Decide which option from the open-decisions list above before writing this.**
- `php/Ubix/Console/Command/Migrate/StatusCommand.php` — Symfony Console command. Cross-references on-disk files vs `Schema_Migrations` rows; prints a table:
    ```
    | id                                              | database  | status   | applied_at          |
    | 00000000000000_init_schema_migrations           | SYSTEMS   | applied  | 2026-05-06 10:12:34 |
    | 20260505143045_pre_attribution_referrer_tables  | VSCASH    | pending  | —                   |
    ```
    Supports `--database=<name>` filter.
- `app/UbixCli/src/Dependencies.php` — bind the new services + repository.
- Tests: parser hit/miss/malformed, scanner ordering, command happy-path with `mysqldump`-style fixture (vfs-stream or temp dir).

**Verify:** `php bin/ubix migrate:status` runs locally against sandbox; prints the bootstrap migration as **pending** (it hasn't been applied yet) plus the pre-attribution one also as pending. `--database=SYSTEMS` shows only the bootstrap row.

### Slice 1.5 — Destructive-statement detection (`migrations.md` §11.1–11.2)

**Goal:** the parser recognises destructive SQL and enforces the `Destructive:` header. No runtime guard yet — that's slice 2.

**Add:**
- `php/Ubix/Service/Migration/DestructiveStatementDetectorService.php` — takes a migration body string, regex-scans for `DROP TABLE` / `DROP DATABASE` / `DROP VIEW` / `DROP INDEX` / `TRUNCATE TABLE` / `RENAME TABLE` / `ALTER TABLE … DROP COLUMN` / `ALTER TABLE … MODIFY` / `DELETE FROM` (without `WHERE`). Returns a list of matched statement strings + line numbers. Strips SQL comments before scanning to avoid false positives on docstrings.
- Extend `MigrationFileParserService` (slice 1) with a 5th optional header line `Destructive:`. When the body contains destructive statements AND the header is missing, `parse()` throws `MigrationParseException` with a clear message naming the offending statements.
- Extend `MigrationFile` DTO with `?string $destructiveReason` (null when not destructive).
- Extend `migrate:status` output to mark destructive rows with a 🔥 column or `[DESTRUCTIVE]` tag.
- Tests: each destructive statement type fires the detector; comment-stripping prevents false positives; a clean migration is unaffected; the parser fails clearly when a destructive body lacks a `Destructive:` header.

**Verify:** craft a fixture migration with `DROP TABLE Foo;` and no `Destructive:` header — parser fails. Add the header — parser passes; `migrate:status` shows the 🔥 marker.

**Note:** this slice ships the detector + the lint, but does NOT block applies yet. Slice 2 adds the runtime guard. The detector exists alone first because it has no DI surface area beyond the parser; isolating it makes slice 2 simpler.

### Slice 2 — `migrate:up` + bootstrap path + destructive runtime guard + backup snapshot

**Goal:** the runner actually applies migrations + safety layers from §11.3 (runtime ack) and §11.4 (backup snapshot) are wired in.

**Add:**
- `php/Ubix/Service/Migration/MigrationApplyService.php` — applies a single `MigrationFile`. Steps: pre-flight (CREATE TABLE clash check), run body in target DB, write `Schema_Migrations` row with checksum + duration. Returns an `AppliedMigration` DTO.
- `php/Ubix/Repository/SchemaMigration/SchemaMigrationWriterInterface.php` + repo `save()` method.
- Bootstrap path: in `MigrationApplyService` (or a thin orchestrator), detect missing `SYSTEMS.Schema_Migrations` table; if missing AND the file is `00000000000000_init_schema_migrations`, run it directly without the pre-flight (the table can't pre-exist) and self-record the row. Subsequent migrations go through the normal path.
- `php/Ubix/Service/Migration/DestructiveBackupService.php` — for any migration with `Destructive:` header running on staging / prod, takes a `mysqldump --single-transaction --skip-comments` of every table the migration touches (object list comes from the pre-flight inspection); compresses to `/var/ubix-migration-backups/<migration_id>/<environment>-<UTC_timestamp>.sql.gz`. Skipped on dev / sandbox. Path + retention configurable via env (`UBIX_MIGRATION_BACKUP_DIR`, default `/var/ubix-migration-backups`).
- `php/Ubix/Console/Command/Migrate/UpCommand.php` — iterates pending migrations in filename order; applies each; reports per-migration outcome. Supports `--dry-run` (prints what would apply, no DB writes), `--database=<name>`, `--i-acknowledge-destructive`.
- **Destructive runtime guard** (§11.3): if any pending migration has a `Destructive:` header AND the runner detects it's running against staging or prod (via `ENV` env var), the command aborts with a structured table of pending destructive migrations + their reasons + the backup path that will be created. Re-running with `--i-acknowledge-destructive` proceeds AND appends `+destructive-ack` to the `Schema_Migrations.applied_by` value. Never required on dev / sandbox.
- **Lock against concurrent runners** (open decision #3 from above): take `GET_LOCK('ubix_migrations', 30)` at the top of `up`, release at the end. Document in code comment.
- Tests: bootstrap path (table doesn't exist → bootstrap runs), normal apply path, dry-run does nothing, destructive without ack on staging aborts, destructive with ack records the suffix + creates the backup file, advisory lock prevents concurrent applies (mock or fake the second connection).

**Verify:** `php bin/ubix migrate:up --dry-run` prints both pending migrations. `php bin/ubix migrate:up` applies them; `migrate:status` now shows both as applied. Author a fixture destructive migration; `migrate:up` aborts on staging; re-run with the ack flag; the backup file appears under `/var/ubix-migration-backups/<id>/` and the row's `applied_by` ends with `+destructive-ack`.

### Slice 3 — Strict checksum enforcement (`--verify`)

**Goal:** detect after-the-fact edits to applied migrations.

**Add:**
- `--verify` flag on `migrate:status`. For each applied row, recompute on-disk checksum, compare to recorded. Mismatch → red "DRIFT" status + non-zero exit.
- Tests: edited-after-apply scenario (write a fixture migration, run up, mutate the file body, run status --verify, assert exit 1 + clear message).

**Verify:** edit `20260505143045_pre_attribution_referrer_tables.sql` body → `migrate:status --verify` flags it red.

### Slice 4 — `migrate:reconcile`

**Goal:** the manual-apply recovery path is supported.

**Add:**
- `php/Ubix/Console/Command/Migrate/ReconcileCommand.php` — takes an argument `<migration_id>` and `--reason="…"`. Validates the file exists on disk. Inserts the `Schema_Migrations` row with `applied_by = manual:<unix-username>` and `description` augmented with the reason. Refuses if the row already exists.
- Tests: reconcile-when-pending happy path, reconcile-when-already-applied error path, reconcile-when-file-missing error path.

**Verify:** drop the bootstrap row, run reconcile, status shows the row recorded with `manual:` prefix.

### Slice 5 — `migrate:diff` (reference-dump mode)

**Goal:** detect schema drift caused by manual changes that were never recorded.

**Add:**
- `php/Ubix/Service/Migration/SchemaDiffService.php` — for each Ubix-consumed database, run `mysqldump --no-data --skip-comments --skip-extended-insert <DB>` against the live cluster, normalise (sort tables, strip AUTO_INCREMENT counters, etc.), and diff against `sql/<DB>.sql`. Reports per-DB drift findings.
- `php/Ubix/Console/Command/Migrate/DiffCommand.php` — wraps the service; supports `--database=<name>` filter; exit code 1 if any drift found.
- Tests: stub the dump output; assert clean run when input matches reference; assert non-zero + structured output when a column is added.

**Verify:** add a random column via `mysql` directly → `migrate:diff --database=VSCASH` reports the drift.

**Note:** v1 only supports `--mode=reference-dump` (compare against checked-in `sql/<DB>.sql`). The canonical `--mode=replay` is M2 work because it requires a scratch-DB lifecycle on the runner host.

### Slice 6 — Tiered CI wiring

**Goal:** the standard's tiered policy is actually enforced by CI.

**Add:**
- `.gitlab-ci.yml` (or wherever the pipeline lives) gains a `migrate-verify` stage that runs `php bin/ubix migrate:status --verify` and `migrate:diff`. Exit-code translation:
    - On `dev` branch deploys: stage is `allow_failure: true` → loud, non-blocking.
    - On `staging` deploys: blocking.
    - On `prod` deploys: blocking + alert webhook to ops Slack channel.
- **GitLab MR approval rule** (§11.6): require **two approvers** on any PR that touches `sql/migrations/*.sql` AND contains a `Destructive:` header. Configured via GitLab's project-level "Merge request approvals" settings — a custom rule with a CODEOWNERS-style file glob + a content-match condition. (If GitLab's content-match isn't expressive enough, a CI job can set a job-level approval requirement based on a grep over the diff; document the exact mechanism here once configured.)
- Document the GitLab pipeline integration steps in `docs/standards/migrations.md` §10 if anything material differs from what's already written.

**Verify:** open a throwaway PR that edits an applied migration → CI flags drift on dev (loud), blocks on staging.

### Slice 7 — `seed:apply` runner (cousin of `migrate:up`)

**Goal:** seeds aren't manual either.

**Add:**
- `php/Ubix/Console/Command/Seed/ApplyCommand.php` — takes `<descriptor>` argument; resolves to `sql/seeds/<descriptor>.sql`; parses the seed header (similar to migration but with no `Migration:` field); applies idempotently. Per-DB connection same as migrations.
- `php/Ubix/Console/Command/Seed/ListCommand.php` — `php bin/ubix seed:list` lists every file in `sql/seeds/` with its `Database:` + `Description:`.
- Tests: apply-once works, apply-twice is idempotent, apply-with-bad-header errors.

**Verify:** `php bin/ubix seed:apply pre_attribution_referrer_seed` populates the two referrer tables; running it again refreshes without error.

### Slice 8 — Reconcile grandfathered legacy migrations + cleanup

**Goal:** the 3 pre-existing migrations get recorded in `Schema_Migrations` so `migrate:status` shows the platform as fully consistent.

**Add:**
- A one-shot script or doc procedure that runs:
    ```bash
    php bin/ubix migrate:reconcile feature_flags_schema --reason="Grandfathered — applied to sandbox before runner adoption"
    php bin/ubix migrate:reconcile feature_flags_align_environment_enum_to_env_class --reason="Grandfathered"
    php bin/ubix migrate:reconcile add_status_to_click_id_log --reason="Grandfathered"
    ```
- These files are NOT in the new filename format (no timestamp prefix). Decide:
    - (a) Rename them to `YYYYMMDDHHMMSS_<original_name>.sql` and reconcile the new IDs. Cleanest going forward but creates a tiny risk if anyone pulled them in their working tree under the old name.
    - (b) Allow the runner to recognise both formats, with a one-time exemption for these three. Less clean but safer for in-flight branches.
    - **Recommended: (a)**, with a coordinated communication that anyone with these files in flight should rebase.
- Update `CHANGELOG.md` `[Unreleased] § Documentation` with the cleanup.

**Verify:** `migrate:status` shows zero pending migrations across all DBs and zero drift in `migrate:diff`.

---

## Risk register

| Risk | Likelihood | Mitigation |
|---|---|---|
| Per-DB SqlService factory is harder to wire than expected (PHP-DI composition) | Medium | Slice 1 starts with the factory + a smoke test against two DBs. If it grows, isolate as its own commit ahead of slice 1's command. |
| `mysqldump` output isn't byte-stable across MariaDB versions | Medium | Slice 5's normalise step strips AUTO_INCREMENT counters, table comments, etc. Build the normaliser test-first against two known-good dumps. |
| Slice 8 file rename breaks an in-flight branch on a teammate's machine | Low | Coordinate via the team channel before merging slice 8. The standard already grandfathered these so the urgency is low. |
| Bootstrap migration's special-case path makes the runner harder to reason about | Low | Keep the special-case logic in one method (`MigrationApplyService::applyBootstrap`) that the orchestrator invokes only when `Schema_Migrations` doesn't exist. Test the path explicitly. |
| Destructive-statement detector throws false positives on legitimate SQL (e.g. column comment containing `DROP TABLE`) | Medium | Strip block + line comments before regex-scanning. Test fixtures cover comment edge cases. Worst case false-positive: engineer adds `Destructive:` header unnecessarily — annoying, not unsafe. |
| Backup snapshot path fills disk on a runner host that wasn't provisioned for it | Low | `UBIX_MIGRATION_BACKUP_DIR` env var lets ops point it at dedicated storage. Cron prunes after 90 days. Document in slice 6's CI wiring. |
| GitLab content-match approval rule isn't expressive enough for "PR touches sql/migrations/*.sql AND file contains `Destructive:`" | Medium | Fall back to a CI job that greps the diff and sets an approval-required label. Documented as the alt path in slice 6. |

## Time estimate

| Slice | Effort |
|---|---|
| 1. Scaffolding + status | half day |
| 1.5. Destructive lint | 2 hours |
| 2. `migrate:up` + bootstrap + destructive guard + backup | most of a day |
| 3. Strict checksum | 1–2 hours |
| 4. Reconcile | 1–2 hours |
| 5. `migrate:diff` reference-dump | half day (mostly normaliser) |
| 6. CI wiring + GitLab two-approver rule | 2 hours |
| 7. `seed:apply` | half day |
| 8. Cleanup | 1 hour |

**Total:** ~3.5 working days end-to-end, less if slices overlap. Slices 1–4 are the critical path; 5 onwards is enhancement. Slice 1.5 + the guard/backup additions to slice 2 are the destructive-safety story (`migrations.md` §11) — net additional effort ≈ 4 hours over the original plan.

---

## Tomorrow's pickup notes

1. Read `docs/standards/migrations.md` end-to-end, including the new §11 destructive-safety section (15 min).
2. Decide the per-DB connection strategy (open decision #1). The recommended option is **factory** — write down why if you pick something else.
3. Start slice 1; ship it as a single commit before lunch.
4. Slice 1.5 (destructive lint) right after — small, focused, isolates the regex work from the runtime guard.
5. Slice 2 in the afternoon; that gets you a working `migrate:up` plus the destructive runtime guard and backup snapshot by EOD.
6. Slices 3–4 are quick; ship them on day 2.
7. Slice 5 is the meatiest non-critical slice; do it day 2 PM or day 3 if needed.
8. Slices 6–8 close out the initiative; slice 6 includes wiring the GitLab two-approver rule for destructive PRs.

When picking up, the FF command files at `php/Ubix/Console/Command/Flag/*Command.php` are the closest pattern to crib from — same Symfony Console base class, same DI shape, same option / argument idiom. Mirror that layout.

---

## Document Control

| Version | Date | Author | Notes |
|---|---|---|---|
| 1.0 | 2026-05-05 | Christopher W. Olsen | Initial plan. 8-slice breakdown for the `bin/ubix migrate:*` runner; identifies 3 open decisions to resolve in slice 1; defers `seed:apply`, `--mode=replay`, CI wiring, and grandfathered cleanup to dedicated slices 6–8. |
| 1.1 | 2026-05-05 | Christopher W. Olsen | Folded the destructive-safety stack from `migrations.md` §11 into the plan: new slice 1.5 (destructive-statement detection + required `Destructive:` header), slice 2 gains the runtime ack guard + `mysqldump` backup snapshot, slice 6 gains the GitLab two-approver MR rule. Risk register adds false-positive detection / disk-fill / GitLab-content-match risks. Time estimate: +4 hours overall. |
| 1.2 | 2026-05-07 | Christopher W. Olsen | Status flipped to "In progress" — slices 1 + 1.5 shipped. Slice 1.5 dropped the dedicated `MigrationParseException` symbol in favour of the existing `InvalidArgumentException` already used by the parser; rationale captured in CHANGELOG. No scope changes to remaining slices. |
| 1.3 | 2026-05-07 | Christopher W. Olsen | Slice 2 shipped (`migrate:up` apply path + advisory lock + destructive runtime guard + backup snapshot). Two deviations from the plan: (1) §4.1 step 2 pre-flight `CREATE TABLE` clash check moves to slice 3 — Ubix's standards-test forbids `SqlService` injection outside `*SqlRepository`, so adding `INFORMATION_SCHEMA.TABLES` lookup needed reader-method scaffolding that wasn't worth bundling. Duplicate-apply now surfaces as the mysql CLI's "Table already exists" error until slice 3 lands. (2) Advisory-lock primitive lives on `SchemaMigrationReader` rather than a separate `MigrationLock` repo (split would force `MigrationLockOptions` DTO + `query()` boilerplate with no semantic role). |
| 1.4 | 2026-05-07 | Christopher W. Olsen | Slice 3 shipped (`migrate:status --verify` strict-checksum mode + the deferred §4.1 step 2 pre-flight `CREATE TABLE` clash check). `MigrationStatusService` now computes `checksumMatches` unconditionally (the on-disk and recorded checksums are both available without extra I/O); `--verify` only governs the non-zero-exit-on-drift behaviour so operators inspecting status get the visibility regardless. New `SchemaMigrationReader::tableExists($schema, $table)` backs the pre-flight; `MigrationApplyService` consumes it and aborts with the §8 manual-application message before the mysql CLI runs. |
| 1.5 | 2026-05-07 | Christopher W. Olsen | Slice 4 shipped (`migrate:reconcile <id> --reason="…"`). New `MigrationApplyService::reconcile()` records the tracker row without running the body; `applied_by = manual:<unix-username>`; description augmented with `(Reconciled: <reason>)`. Three guard rails: missing file, already applied, missing reason. Closes the four-command MVP runner trio (only `migrate:diff` remains). |
| 1.6 | 2026-05-07 | Christopher W. Olsen | Slice 5 shipped (`migrate:diff` reference-dump mode). `mysqldump --no-data` for the live snapshot, normaliser strips banners + `AUTO_INCREMENT=N` counters + the conditional SET-block, line-diff against `sql/<DB>.sql`. Surfaces drift as `extraInLive[]` + `missingFromLive[]`. `--database=<name>` filter. Mode is reference-dump only — replay-mode (slice 5 v1 note) deferred to M2. The four runner commands are now feature-complete; slice 6 (CI wiring) ties them into the deploy pipeline. |
| 1.7 | 2026-05-07 | Christopher W. Olsen | Slice 6 shipped (tiered CI wiring + destructive-PR detection). `.gitlab-ci.yml` gets a `migrate-verify` stage with three branch-scoped jobs (`migrate-verify-dev` `allow_failure: true`, staging + prod blocking) and a `destructive-pr-detect` job in the `test` stage. Standards `migrations.md` §10.1 documents the implementation; §11.6 covers the GitLab UI scoping trade-off (content-match isn't natively expressible, so every migration PR picks up two-approver — the CI banner clarifies which are genuinely destructive). Standard doc bumped to v1.2. |
| 1.8 | 2026-05-07 | Christopher W. Olsen | Slice 7 shipped (`seed:apply` + `seed:list`). `SeedFile` DTO (no checksum), `SeedFileParserService`, `SeedFileScannerService` (with `getByDescriptor()` lookup), `SeedApplyService` (mysql-CLI shell-out — third copy of the helper, marked for tech-debt consolidation), `ApplyCommand`, `ListCommand`. NOT tracked in `Schema_Migrations`; no advisory lock or destructive guard; idempotent by convention. End-to-end against `sql/seeds/pre_attribution_referrer_seed.sql` validated via `seed:list`. |
| 1.9 | 2026-05-07 | Christopher W. Olsen | Slice 8 shipped as `./cutover-runbook.md` rather than another code commit. End-to-end validation against the local sandbox (`127.0.0.1:30306`) surfaced four real bugs along the way (bootstrap description-column too small, READ/WRITE cluster-mismatch silently routing pre-flight, `--drop-database` FK-check failure, generic `DtoException` hiding driver messages) — all fixed in commits `a4174a8a` / `58a57c8c`. The runbook captures the per-cluster cutover procedure (pre-flight → DDL-cred swap → dry-run → apply → verify → dump-refresh → roll back to app creds), the reconcile contingency for grandfathered state, and rollback paths. The four-command runner trio plus seeds is now feature-complete and validated. |
| 1.10 | 2026-05-14 | Christopher W. Olsen | **Frozen-baseline policy locked + replay-mode now a v2 prerequisite.** Project decision (cross-referenced in `cutover-runbook.md` §3.6 v1.5 and `migrations.md` §9 v1.4): `sql/<DB>.sql` dumps are the frozen pre-migration baseline; never refreshed post-cutover. **Implication for this plan:** the original "Out of scope > `migrate:diff --mode=replay` (M2)" item is upgraded from nice-to-have to a hard prerequisite for using `migrate:diff` as a real CI drift gate — under the frozen-baseline policy, the v1 reference-dump mode is structurally noisy (every applied migration surfaces as "extra in live"). Until replay-mode ships, treat `migrate:diff` failures as advisory and rely on `migrate:status --verify` (checksum drift) as the hard gate. Also in 1.10: `database:resetSchema` rewired (auto-discover dumps + inline `migrate:up`, refuses staging/prod) and the `--target` / `--username` retarget options added to every migrate / seed command — both leverage the frozen-baseline invariant. Code complete; no change to the CI rollout state. |
