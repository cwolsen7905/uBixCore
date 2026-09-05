# Schema Migration Standards

**Status:** Approved
**Audience:** VS Media Development Department
**Last Updated:** 2026-08-18

This document defines how schema changes flow into uBix Core's databases. It complements [`database.md`](database.md) — that doc covers _what_ a well-formed schema looks like; this doc covers _how_ a schema change actually lands in dev / staging / prod and how the platform tracks what's been applied where.

---

## 1. Scope

These standards apply to:

- Any change to the schema of a uBix Core-consumed database (`VSCASH`, `SYSTEMS`, `ntl_db`, `BILLING`, etc.) — `CREATE TABLE`, `ALTER TABLE`, `CREATE INDEX`, view / trigger / stored-procedure changes.
- Any data backfill or seed that needs to land alongside a schema change (with a split between **schema** migrations and **seed** files — see §6).
- The full flow from authoring a migration on a feature branch to applying it on prod via the deploy pipeline.

Out of scope:

- Read-only data exports (e.g. `mariadb-dump` for analytics).
- Data corrections that are one-off ad-hoc UPDATE statements (those are incidents, not migrations — separate runbook).
- Application-level config (lives in the `Feature_Flags` system, not migrations).

Legacy schema state is grandfathered. The `sql/<DB>.sql` reference dumps are the platform's **point-in-time snapshot** of legacy state at the moment migrations were adopted (2026-05-05); migrations track every change forward from there.

---

## 2. Filename Format

Migration files live under `sql/migrations/` and follow this format:

```
sql/migrations/YYYYMMDDHHMMSS_<snake_case_description>.sql
```

- **`YYYYMMDDHHMMSS`** — 14-digit UTC timestamp at authoring time (`date -u +%Y%m%d%H%M%S`). Timestamps order migrations naturally and avoid the merge-conflict pain that sequential numbering causes when two engineers branch off the same point.
- **`<snake_case_description>`** — short, descriptive, lowercase, underscores. Reads as the migration's purpose: `pre_attribution_referrer_tables`, `add_email_to_admin_users`, `drop_legacy_voyeur_columns`.

Examples:

```
sql/migrations/00000000000000_init_schema_migrations.sql
sql/migrations/20260505143045_pre_attribution_referrer_tables.sql
sql/migrations/20260612091522_add_email_to_admin_users.sql
```

The `00000000000000_` prefix is reserved for the **bootstrap migration** that creates the tracker table itself (§5).

### 2.1 Header

Every migration file begins with a structured comment header that the runner parses:

```sql
-- Migration: 20260505143045_pre_attribution_referrer_tables
-- Database: VSCASH
-- Description: Two lookup tables for the Pre-Attribution chain
--              (Pre_Attribution_Referrer_Mappings + Triggers).
-- Author: Christopher W. Olsen

CREATE TABLE VSCASH.Pre_Attribution_Referrer_Mappings (
    ...
);
```

Required fields:

- **Migration:** matches the filename (without extension). The runner verifies they agree.
- **Database:** the target database (`VSCASH`, `SYSTEMS`, etc.). One database per migration — if a logical change spans two databases, write two migrations and rely on filename ordering for sequencing.
- **Description:** human-readable summary; first line is shown by `migrate:status`.
- **Author:** name of the engineer who authored the migration.

The runner refuses to apply a file with a malformed or missing header.

**Body qualification + the prefixed-schema rewrite (2026-08-18):** qualify body statements with the `Database:` value (`VSCASH.Foo`) — either quoting style is fine, bare or backtick-quoted (`` `VSCASH`.`Foo` ``). When `DATABASE_PREFIX` is set (the unit-test / CI test-DB pass, `migrate:* --prefix=`), `MigrationApplyService` rewrites **every** reference to the declared database at `<prefix><DB>` before piping the body to the `mariadb` CLI, so a prefixed run can never reach the runtime schema. Both quoting styles are rewritten; until 2026-08-18 only the bare form was, and a backtick-quoted `ALTER TABLE` kept its unprefixed schema and died in the dev pipeline with `ERROR 1146 ... Table 'ntl_db.transact' doesn't exist` (`20260817221748_add_bin_8_column_to_ntl_db_transaction_tables` — a `RequiresDBA:` file, which is exactly the class that DOES apply inline on TEST per §11.8). A reference to any **other** database is not rewritten and would hit the real cluster in a prefixed run — one database per migration (the `Database:` rule above) is what keeps that from happening.

**Header grammar (parser contract, 2026-07-30):** the header vocabulary is exactly `Migration:`, `Database:`, `Description:`, `Author:`, `Destructive:`, `RequiresDBA:`, `AlterAck:` — **only these keys start a header line**. Any other `-- …` line while a header is open is a *continuation* of that header, including continuations whose text contains colons (`migrate:reconcile`, `pipeline-safe: …`, URLs). Earlier parser behavior treated any `word:` continuation as a fresh header and silently truncated the recorded reason — the value the §11.8 hold banner shows operators — which a Claude pipeline review caught on a real `RequiresDBA:` note (2026-07-30).

---

## 3. Master Tracker — `SYSTEMS.Schema_Migrations`

A **single** master table records every applied migration across all uBix Core databases. It lives in `SYSTEMS` alongside the Feature Flag tables — `SYSTEMS` is the established home for cross-cutting platform metadata.

```sql
CREATE TABLE SYSTEMS.Schema_Migrations (
    id                VARCHAR(96)   NOT NULL,
    target_database   VARCHAR(64)   NOT NULL,
    description       VARCHAR(512)  NOT NULL,
    checksum          CHAR(64)      NOT NULL,
    applied_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    applied_by        VARCHAR(64)   NOT NULL,
    duration_ms       INT UNSIGNED  NOT NULL,
    date_created      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_last_updated DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_target_database (target_database),
    KEY idx_applied_at (applied_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Columns:

- **`id`** — the migration's full filename (sans extension), e.g. `20260505143045_pre_attribution_referrer_tables`. Globally unique because timestamps include seconds.
- **`target_database`** — populated from the file's `Database:` header. `migrate:status --database=VSCASH` filters on this.
- **`description`** — first line of the `Description:` header; surfaced in `migrate:status`.
- **`checksum`** — SHA-256 of the migration file body (everything after the header). Used to detect after-the-fact edits to applied migrations (§7).
- **`applied_at`** — when the migration finished successfully. Failed migrations leave no row.
- **`applied_by`** — actor identifier. Format mirrors the Feature Flag audit log:
    - `cli:<unix-username>` — engineer applied via `migrate:up` locally (identity from `USER`)
    - `ci:<gitlab-user-login>` — applied by the deploy pipeline; the actor is the GitLab user who ran the job (from `GITLAB_USER_LOGIN`, passed into the migrate-apply container). Falls back to `ci:unknown` when no human triggered it (e.g. a scheduled pipeline)
    - `manual:<gitlab-user-login|unix-username>` — recorded after a manual application via `migrate:reconcile` (§8)
- **`duration_ms`** — wall-clock duration of the migration's execution. Useful for spotting migrations that grow expensive over time.

### 3.1 Why one table, not per-database

Most migration frameworks (Rails, Phinx, Flyway) put a `schema_migrations` table inside each application database. That convention assumes **cross-cluster heterogeneity** — apps that talk to N independent DBs on N servers owned by N teams. uBix Core's reality is the opposite: every database is a schema on one shared MariaDB cluster, restored as a unit, owned by one platform team. A single tracker is operationally simpler — one bootstrap, one place for dashboards to read, one row to look at when answering "did this migration land?" Tradeoff: dropping a database orphans rows in `SYSTEMS.Schema_Migrations` until cleaned up; small ops cost paid rarely.

---

## 4. Runner — UbixCli `migrate:*`

Migrations are applied via Symfony Console commands on `bin/ubix`. The command surface mirrors the existing `flag:*` commands in style and audit conventions.

```
php bin/ubix migrate:up
php bin/ubix migrate:up --database=VSCASH
php bin/ubix migrate:up --dry-run

php bin/ubix migrate:status
php bin/ubix migrate:status --database=VSCASH
php bin/ubix migrate:status --verify

php bin/ubix migrate:reconcile <migration_id> --reason="DBA emergency 2026-05-05"

php bin/ubix migrate:diff
php bin/ubix migrate:diff --database=VSCASH

# Operator-driven retarget (§4.5) — --target is required on every
# invocation. --host / --port override the cluster per-call:
php bin/ubix migrate:status --target=sandbox
php bin/ubix migrate:up --target=staging --username=dba_user --yes
php bin/ubix migrate:status --target=prod --host=example.com --port=3306
```

### 4.1 `migrate:up`

Applies every migration in `sql/migrations/` whose `id` is not yet recorded in `SYSTEMS.Schema_Migrations`, in filename (timestamp) order. For each pending migration:

1. **Header validation** — parses + verifies the `Migration:` / `Database:` / `Description:` / `Author:` fields.
2. **Pre-flight check** — inspects the target schema for objects the migration is about to `CREATE`. If any already exist, the runner aborts with: *"Object `<DB>.<Object>` already exists; this migration appears to have been applied manually. Run `migrate:reconcile <id>` to record it."* (§8)
3. **Apply** — opens a transaction (where the SQL allows; some DDL in MariaDB is non-transactional), runs the migration body against the target database.
4. **Record** — on success, inserts the `Schema_Migrations` row with `applied_by`, `duration_ms`, and the file's SHA-256 checksum.
5. **Fail-loud** — on any error, the migration's transaction is rolled back where possible; no `Schema_Migrations` row is written; the runner exits non-zero with the upstream error.

Before any DDL runs, the runner **classifies the pending set** and applies the §10 gates **per migration**: a `Destructive:` migration is held unless `--i-acknowledge-destructive` is given (§11.3), and a `RequiresDBA:` migration is held **unconditionally** (no flag overrides it) and routes out-of-band (§11.8). Held migrations are skipped — everything else pending still applies in the same run, except migrations of the **same target database** sorting after a held one, which are *dammed* behind it (strict per-database ordering; a later migration may depend on the held DDL). On `dev` / `sandbox` the destructive gate is relaxed for fast local iteration; the `RequiresDBA` abort applies on every tier except `test`, where both classes apply inline — the unit-test schema is materialised from scratch with zero rows each run, and holding there would leave it missing the columns real tiers gain at reconcile time (§11.8).

`--dry-run` prints what _would_ apply without touching the DB.

#### 4.1.1 Slack notification

After a `migrate:up` run that applies one or more migrations on **dev / staging / prod**, the runner posts a summary to the **#databases** Slack channel (environment, count, the list of applied ids + databases, and the `applied_by` actor) via the shared `SlackService`. `migrate:reconcile` posts the same way (a distinct "reconciled — recorded as applied without running" message). This covers every path — CI jobs *and* manual operator shell runs. The **sandbox** and **test** environments are deliberately skipped (sandbox is local-iteration noise; the test environment churns schemas on every unit-test run via `database:resetSchema --target=test` and must never reach a live channel). The notification is best-effort: a Slack failure is logged and never fails the command. Implemented in `MigrationNotificationService`, wired into `app/UbixCli/src/Dependencies.php` (reads the existing `SLACK_API_ENDPOINT`).

The `applied_by` actor shown in the notification (and recorded in the tracker) is resolved by `AbstractMigrationCommand::resolveActorIdentity()`: in CI it reads `GITLAB_USER_LOGIN` (the GitLab user who ran the job — `.gitlab-ci.yml` passes `GITLAB_USER_LOGIN` + `CI` into the migrate-apply container), and locally it falls back to `USER`. `isCiRun()` (via `CI` / `GITLAB_CI`) drives the `ci:` vs `cli:` prefix.

### 4.2 `migrate:status`

Lists, for each migration on disk:

- whether it's applied (with `applied_at` + `applied_by`)
- whether it's pending
- whether its on-disk checksum still matches the recorded checksum (with `--verify`)

`--verify` is the strict-checksum mode (§7). CI runs `migrate:status --verify` on every deploy — a checksum mismatch fails the build.

### 4.3 `migrate:reconcile`

Records a migration as applied **without running it**. Used after an out-of-band manual application (§8). Inserts the `Schema_Migrations` row with `applied_by = manual:<username>` and `description` augmented with the `--reason` value.

### 4.4 `migrate:diff`

Compares the **actual** schema of every uBix Core-consumed database against the **expected** schema produced by replaying every applied migration on top of the baseline `sql/<DB>.sql` dump.

**v1 ships `--mode=reference-dump` only** — it diffs live against the baseline dump, which makes the output structurally noisy under the frozen-baseline policy (every applied migration's effect shows up as "extra in live"). Treat the v1 output as advisory; the canonical `--mode=replay` (rebuild scratch DB from baseline + migration history, diff against that) is v2 work. See §9 for the full drift-detection story.

CI runs `migrate:diff` on every PR per the tiered policy in §10, but until replay-mode ships its failures should be triaged manually rather than acted on as hard gates.

### 4.5 Operator-driven retarget — `--target` / `--host` / `--port` / `--username`

Every `migrate:*` and `seed:*` command (and `database:resetSchema`) accepts four CLI options for pointing the runner at a specific cluster:

- `--target=<env>` — **required** on every invocation. One of `dev` / `sandbox` / `staging` / `prod`. Stamps `ENV` so the destructive-runtime guard (§11.3) fires for the right tier — `--target=prod` enforces prod rules even when invoked from a sandbox shell. Host/port/cred resolution is tier-specific:
    - `--target=sandbox` is the **sandbox-exclusive** path: ALL five `SANDBOX_MYSQL_WRITE_*` env vars (host / port / username / password / database) are stamped onto the plain `MYSQL_WRITE_*` equivalents, AND any inherited `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` from `.env` or the shell are cleared so they don't shadow the sandbox per-tier creds via the precedence rule below. Sandbox is the only tier with a prefixed env block — it's the canonical "reachable from any operator shell via port-forward" target, and the rule "use the `SANDBOX_*` block exclusively when retargeting at sandbox" is the single exception to the otherwise-universal "read plain `MYSQL_*` env" model. `--username=<user>` still wins (it re-stamps `MYSQL_MIGRATION_*` AFTER the clear).
    - `--target=dev` / `=staging` / `=prod` does NOT do host/port/cred stamping. The runner reads plain `MYSQL_WRITE_HOST` / `MYSQL_WRITE_PORT` / `MYSQL_WRITE_USERNAME` / `MYSQL_WRITE_PASSWORD` (with `MYSQL_MIGRATION_*` winning by precedence when set) from whatever deployment shell the command was invoked from. CI sets these per-tier; operators on a deployment shell get them for free.
  Required-target enforcement is the explicit-tier invariant — there is no implicit fallback to whatever `MYSQL_WRITE_*` happens to be loaded. Omitting `--target` is a hard error.
- `--host=<host>` / `--port=<port>` — stamp `MYSQL_WRITE_HOST` / `MYSQL_WRITE_PORT` directly for the single invocation. Win over any tier-derived value from `--target`. Useful for cross-tier targeting (e.g. operator on a dev shell pointing at a staging cluster) without `export`ing env vars. For `--target=sandbox` they also relax the "no host/port configured" error when `SANDBOX_MYSQL_WRITE_*` is unset.
- `--username=<user>` — triggers a hidden-stdin password prompt and sets `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` for the single invocation. The prompt is refused in non-interactive runs (no TTY) to avoid an empty-password silent connect; CI continues to set the env vars directly per §10. `--username` wins over any per-tier creds picked up by `--target` because `MYSQL_MIGRATION_*` beats `MYSQL_WRITE_*` everywhere downstream (and, for sandbox, the `--username` stamp happens AFTER the `MYSQL_MIGRATION_*` clear, so the operator override re-asserts cleanly).

Resolution precedence when the options compose with the existing env-var workflow:

1. `--target=<env>` stamps `ENV=<env>`. For sandbox only: populates `MYSQL_WRITE_HOST` / `_PORT` / `_USERNAME` / `_PASSWORD` / `_DATABASE` from `SANDBOX_MYSQL_WRITE_*`, and clears `MYSQL_MIGRATION_USERNAME` / `_PASSWORD`.
2. `--host=<host>` / `--port=<port>` override `MYSQL_WRITE_HOST` / `_PORT` from step 1.
3. `--username=<user>` + prompt populates `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` (re-asserting the migration-cred override after step 1's sandbox clear when both are in effect).
4. Anything left untouched falls through to `MYSQL_MIGRATION_*` (when non-empty and not cleared), then to `MYSQL_WRITE_*` (deployment shell / sandbox per-tier values from step 1).

`MYSQL_WRITE_DATABASE` is stamped from `SANDBOX_MYSQL_WRITE_DATABASE` when `--target=sandbox` (part of the "use ALL `SANDBOX_*` values" exclusivity rule) and left untouched for every other tier — the tracker table (`SYSTEMS.Schema_Migrations`) has the same name in every uBix Core cluster, and per-migration target databases come from each file's `Database:` header.

The runner prints a one-line "Migration target overrides:" banner before any DB work whenever either option is supplied, so the operator can confirm the resolved cluster matches expectation before answering the apply-confirmation prompt.

This option surface complements the env-var workflow documented in `docs/projects/migrations/cutover-runbook.md` §2.1 / §3.2 — it does not replace it.

---

## 5. Bootstrap — `00000000000000_init_schema_migrations.sql`

The tracker table has a chicken-and-egg problem: the runner records applied migrations in `SYSTEMS.Schema_Migrations`, but that table doesn't exist until the first migration runs. The bootstrap migration solves this with a special-case path in the runner:

- **Filename:** `00000000000000_init_schema_migrations.sql` (zero timestamp; reserved).
- **Database:** `SYSTEMS`.
- **Body:** the `CREATE TABLE` for `Schema_Migrations` itself.
- **Runner behavior:** if `SYSTEMS.Schema_Migrations` doesn't exist when the runner starts, it applies this single file directly and records itself as the first row. Every subsequent migration runs through the normal path.

This is the **only** migration with the `00000000000000_` prefix — it's essentially infrastructure, not a schema change in the usual sense. Engineers should never write another file with that prefix.

---

## 6. Schema vs Seed Split

Migrations split into two file types under `sql/`:

| Type | Path | Purpose | Re-runnable? |
|---|---|---|---|
| **Schema migration** | `sql/migrations/<id>.sql` | Structural change (`CREATE TABLE`, `ALTER TABLE`, `CREATE INDEX`, view / trigger). Tracked in `Schema_Migrations`. Applied **exactly once**. | No — fail-loud on second apply. |
| **Seed file** | `sql/seeds/NNN_<descriptor>.sql` | Reference / lookup data (country lists, referrer mappings, default flag values). Idempotent + key-backed (`ON DUPLICATE KEY UPDATE` or `WHERE NOT EXISTS` — §6.1); numeric-prefixed for order (§6.2). NOT tracked in `Schema_Migrations`. | Yes — re-applying refreshes data without breaking anything. |

A seed file is the right home for:
- Reference tables that are part of the platform's logic (referrer-mappings, country lists, currency rates).
- Default values for new feature-flag rows.
- Lookup data that gets refreshed periodically.

A seed is **not** the right home for:
- One-off data corrections (those are incidents).
- User data, customer data, content data — those are operational state, not platform state.

When a single change requires both a schema change and seed data — e.g. _"add a new lookup table and populate its 60 rows"_ — author **two files**:

1. `sql/migrations/<id>_<schema_description>.sql` — `CREATE TABLE` only.
2. `sql/seeds/<descriptor>.sql` — `INSERT INTO …` rows.

Seeds are NOT tracked in `Schema_Migrations`. `bin/ubix seed:apply <descriptor> [<descriptor> …]` applies named seeds; `--all` applies every seed (in prefix order — see §6.2).

### 6.1 Idempotency — enforced, and it must be *key-backed*

A seed re-applies without accumulating rows. `code:review` (phpunit — `Ubix\Tests\SeedFileIdempotencyTest`) enforces **two** things:

1. **A guard is present** — every `INSERT INTO` uses `ON DUPLICATE KEY UPDATE`, is `INSERT IGNORE`, or is an `INSERT … SELECT … WHERE NOT EXISTS (…)` conditional.
2. **The guard is key-backed** — for `ON DUPLICATE KEY UPDATE` / `INSERT IGNORE`, the target table must have a UNIQUE/PRIMARY key whose columns are **all among the inserted columns**. Without one, the guard **never fires** and each run inserts a *new* row — silently, no error. The test parses the seed's columns + the table's DDL (baseline `sql/<db>.sql` + migrations) and fails if no key is covered.

⚠️ Many uBix Core tables have only an auto-increment `id` PK (they allow historical rows, so a natural unique key on the "identity" columns often doesn't exist — and shouldn't be forced). In that case **use `INSERT … SELECT … WHERE NOT EXISTS (…)`** — idempotent regardless of keys — rather than `ON DUPLICATE` + a unique key the domain doesn't want.

### 6.2 Ordering, dependencies, foreign keys

Seeds apply in **ascending filename order**, so a **numeric prefix sequences them**: `010_<descriptor>.sql`, `020_…` (lower runs first; leave gaps to insert between).

uBix Core uses **soft foreign keys — no hard `FOREIGN KEY` constraints** (database.md). A child row referencing a missing parent won't *error*, but the data is logically broken — so ordering matters for **correctness**:
- **Prefer self-contained seeds** — a seed that owns a parent + its children inserts them top-to-bottom in one file (e.g. `020_internal_admin_2_0_page_registry` inserts the category, captures its id in `@…`, then the options that reference it). No cross-file ordering needed.
- **Cross-file dependency** — give the prerequisite seed the lower prefix.

### 6.3 Apply model — apply-on-change (dev + staging)

The `seed` CI stage runs **after `migrate-apply`** (schema first) and applies **only the seeds changed in the push** (`git diff` over the push range → `seed:apply <changed descriptors>`). Why not re-apply all every deploy: a routine deploy that touched no seed does nothing, and a non-idempotent slip's blast radius is bounded to the commit that changes the file — not *every* merge forever.
- **dev + staging only** — no prod seed job: staging shares prod's DB cluster, so seeding staging seeds prod.
- **Fresh-environment bootstrap** — `bin/ubix seed:apply --all` (every seed, prefix order), run manually on a new DB.

### 6.4 Slack notifications

The CI seed job posts a `#databases` summary (`SeedNotificationService`, `:trident:` branding): a success list of applied seeds, or a failure header naming the failed ones. Because only *changed* seeds are applied, a post reflects a real change — routine no-op deploys are silent. `--all` bootstrap notifies the full set (a deliberate manual action).

---

## 7. Strict Checksum Enforcement

Once a migration's row exists in `SYSTEMS.Schema_Migrations`, the file is **immutable**. The runner enforces this via SHA-256 checksums:

- On every `migrate:up` and on `migrate:status --verify`, the runner recomputes each applied file's checksum and compares against the recorded value.
- A mismatch fails the runner immediately with: *"Migration `<id>` has been edited after application (recorded checksum `<a>` ≠ on-disk checksum `<b>`). Migrations are forward-only; write a new migration to amend the schema."*
- The CI deploy pipeline runs `migrate:status --verify` as a gate. Mismatches block the deploy until reconciled.

This forces the **forward-only policy**: once a migration is in any environment, you can't go back and edit it. To change the schema produced by a previous migration, write a new migration that performs the change.

The cost is real: small typos in old migrations stay forever (or get a follow-up correction migration). The benefit is real too: prod is never in a state that doesn't reproduce from the migration history.

---

## 8. Manual Application + Reconcile

In an emergency — runner broken, DBA needs to land a hotfix, environment is offline from CI — engineers may apply schema changes manually via `mariadb` / phpmyadmin / the cluster's admin tooling. The standards permit this _with a follow-up_:

1. **Manual apply:** engineer pipes the migration file's SQL body into `mariadb` against the target DB. The schema is now in the desired state, but `SYSTEMS.Schema_Migrations` doesn't know.
2. **Reconcile:** within the same incident (or by the next CI run at the latest), the engineer runs `bin/ubix migrate:reconcile <migration_id> --reason="<one-line context>"`. This inserts the `Schema_Migrations` row with `applied_by = manual:<unix-username>`. The migration is now recorded as applied; the runner won't try to re-apply it; CI's drift check passes.
3. **Audit:** the row's `applied_by` field permanently captures that this migration came in through the manual path. Dashboards can flag manual applications on prod for retro review.

A migration that was applied manually but **not** reconciled creates drift: the next `migrate:up` will try to re-apply it (and fail because the table exists), and the next `migrate:diff` will report no drift but `migrate:status` will list it as pending. That contradiction is the signal — fix it via `migrate:reconcile` rather than ignoring it.

**Cultural rule worth quoting in code review:** *"If you must apply schema manually, follow up with `migrate:reconcile` in the same incident. An applied migration without a `Schema_Migrations` row is a P3 — it gets caught by CI but creates work for whoever lands the next migration."*

---

## 9. Drift Detection — `migrate:diff`

The canonical source of truth for what each database's schema should look like is **the migration history**: replaying every applied migration in filename order into a clean DB produces the expected schema.

The `sql/<DB>.sql` files in the repo are the **frozen pre-migration baseline** (per the frozen-baseline policy locked 2026-05-14 — see `docs/projects/migrations/cutover-runbook.md` §3.6). They capture the schema as it stood before the migration runner existed and are never refreshed; every change after that point lives in `sql/migrations/`. `<baseline dump> + <migration history>` is the canonical expected schema.

`migrate:diff` ships two modes:

1. **`--mode=replay` (canonical, v2 work).** Rebuilds a scratch DB from the baseline dump + every applied migration, diffs the live cluster against that. This is the only mode that returns a clean signal under the frozen-baseline policy. **Not implemented in v1** — the scratch-DB lifecycle on the runner host needs to land first.
2. **`--mode=reference-dump` (v1 default, advisory only).** Diffs live against the baseline dump. After any migration applies, "extra in live" rows include both real out-of-band drift AND the entire migration history's effect — output is structurally noisy. Useful for human inspection ("did anything weird land that's not in any migration?") but not safe to use as a CI gate.

CI's drift policy in §10 currently uses the v1 reference-dump mode; treat its `migrate:diff` failures as advisory until replay-mode ships. The tiered alerting (paging on prod) is the operational gate of last resort.

`migrate:status --verify` (checksum drift on applied migrations — §7) is unaffected by the frozen-baseline policy and remains a hard CI gate per §10.

---

## 10. CI Flow — Continuous, Classification-Gated

Migrations are code: once merged to `dev` a migration is **production-bound** and flows through the tiers on the normal code-promotion path (merge `dev` → `staging` → `main`). What gates a migration is **what it does**, not which tier it is on. CI classifies every pending migration into one of three classes and applies the same gate at every tier:

| Class | Detected by | CI apply behavior (identical dev / staging / prod) |
|---|---|---|
| **Non-destructive** (default) | no destructive statements (§11.1) and no `RequiresDBA:` header | **Auto-applies** before deploy. Flows all the way through with zero migration-specific human action. |
| **Destructive** | destructive statement (§11.1), requires a `Destructive:` header (§11.2) | The migration is **held per-migration** (exit 3, wrapper-converted to a loud green HOLD — every other pending migration applies and the pipeline progresses; same-database migrations dam behind the hold — §11.3.1); applied only via the manual `migrate-apply-<tier>-destructive` button, which then cascades to `deploy-<tier>-destructive`. A deliberate, separate-release step. |
| **RequiresDBA** | `RequiresDBA:` header (§11.8) | The migration is **held per-migration** (exit 4, wrapper-converted to a loud green HOLD — every other pending migration applies and the pipeline progresses; same-database migrations dam behind the hold) and routes to the MariaDB team. The pipeline never runs it — applied out-of-band, then recorded with `migrate:reconcile` (§11.8). Exception: applies inline on the `test` tier (empty rebuilt schema — §11.8). |

This is the **expand/contract** model (§11.5): non-destructive changes are backward-compatible by rule, so they are safe to apply ahead of the deploy and safe for the old code still running during a rolling deploy — which is why they can flow continuously without a human gate. Destructive (contract) changes are deferred to a later release and stay deliberately manual. `RequiresDBA` changes are the heavy / online-DDL cases the pipeline can't run safely (§11.8).

**Verify is separate from apply and never gates it.** `migrate:status --verify` (checksum drift on already-applied migrations — §7) is a read-only pre-flight signal that auto-runs on every tier. Drift is loud — advisory on dev / staging, blocking + paging on prod (a P2 incident) — but it does **not** block apply or deploy at any tier. The runner publishes the signal; the operator acts on it. (Verify is *not* a schema-readiness check — it only validates the integrity of already-applied migrations, so gating deploy on it would be both the wrong signal and falsely reassuring.)

**Deploy gating (2026-06-24).** Each tier has a **normal deploy + an explicit emergency bypass** (`deploy-<tier>-emergency` — manual, `needs: [build-*]` only — ships the just-built image past every gate, for rollbacks / CI-infra flakes / an unrelated red gate; never auto-fires, visible in pipeline history). The normal deploys:

- **`deploy-dev`** — auto-on-green (stage-ordered behind lint-and-test + `migrate-apply-dev`).
- **`deploy-staging`** — **auto-on-green** (continuous delivery to the test-bed tier): fires once the build, `static-checks-staging` + `phpunit-staging` (hard gates), and `migrate-apply-staging` all succeed. `migrate-apply-staging` applies every applicable migration and **holds** destructive / `RequiresDBA` ones per-migration (same-database migrations dam behind a hold), exiting with a dedicated code (3 / 4) that the `.migrate_apply` CI wrapper converts to a **loud green expected hold** — the job succeeds with a HOLD banner and the **pipeline progresses, deploy included**: deploying on a hold is safe by the §11.5 expand/contract rule, and code that depends on a held migration must not merge until it reconciles (§11.8). The destructive path resumes when a human plays `migrate-apply-staging-destructive`, which auto-cascades to **`deploy-staging-destructive`** (the OR-sibling of this job — GitLab `needs:` has no OR, so it's a second deploy job). A *real* apply error (exit 1) still hard-reds the pipeline. `migrate-verify-staging` is advisory (`allow_failure: true`) — waited on for the drift signal, not blocking.
- **`deploy-prod`** — **manual** (deliberate one-click), gated on `migrate-verify-prod` (blocking — prod schema-drift makes the normal button un-playable; hit `deploy-prod-emergency` to override). Prod stays manual on purpose: `main` has no test gate (staging is the test bed), the deploy is a straight rollout with no canary / auto-rollback, and legacy still shares the databases. Auto-prod ("continuous *deployment*") is a future step gated on progressive rollout + SLO-driven auto-rollback + a prod smoke gate — see §13. The destructive path has its own button, **`deploy-prod-destructive`**, gated on `migrate-apply-prod-destructive` being green (you play the apply, then the deploy — two deliberate clicks); `migrate-apply-prod` itself holds on a destructive / `RequiresDBA` migration the same way staging does (exit 3 / 4 → ⚠️ warning, pipeline stays green).

Staging now couples deploy → migrate-**apply** (auto-deploy only when nothing's blocking); prod still gates on migrate-**verify** only. Full deploy↔apply coupling on prod remains the post-cutover target (§13), not wired while legacy callers still share the databases.

### 10.0 Why continuous flow is safe (and where it isn't yet)

The discipline that makes "merged = prod-bound" safe is **backward compatibility**, enforced by the three-way classification:

- A **non-destructive** change (add column / table / index) is safe for both the currently-running uBix Core code *and* the legacy PHP callers that still share these databases — adding is additive. So auto-flow to prod doesn't endanger legacy.
- Anything that *could* break legacy (drop / narrow / rename) trips destructive detection (§11.1) and is therefore gated — never auto-applied. The legacy-coexistence risk is covered by the destructive gate, not by a separate per-tier hold.
- A heavy operation on a large hot table (full rewrite, lock risk) is flagged `RequiresDBA` (§11.8) and kept out of the auto path entirely until self-service online-DDL tooling lands (§13).

### 10.1 Implementation in `.gitlab-ci.yml`

`migrate-verify-<tier>` (read-only) and `migrate-apply-<tier>` (DDL) jobs run in the `migrate-verify` stage (between `build` and `deploy`) against the freshly-built `:$CI_COMMIT_SHA` image with the per-tier `--target` / `ENV` set.

| Branch | `migrate-verify-<tier>` | `migrate-apply-<tier>` (plain) | `migrate-apply-<tier>-destructive` |
|---|---|---|---|
| `dev` | `on_success` / `allow_failure: true` | `on_success` — auto-applies non-destructive; holds (loud green, wrapper-converted exit 3/4) on destructive / RequiresDBA | `manual` / `true` |
| `staging` | `on_success` / `true` | `on_success` — auto-applies non-destructive; holds (loud green, wrapper-converted exit 3/4) on destructive / RequiresDBA | `manual` / `true` |
| `main` / `master` | `on_success` / `false` (blocking + paging on drift) | `on_success` — auto-applies non-destructive; holds (loud green, wrapper-converted exit 3/4) on destructive / RequiresDBA | `manual` / `true` |

Changes from the prior operator-discretion model (2026-06-24):

- **Non-destructive `migrate-apply` auto-runs on staging and prod**, not just dev — the continuous-flow change above (was `manual` on staging / prod). The `-destructive` sibling stays `manual` at every tier.
- **`migrate-verify-prod` flips `manual` → auto** (`on_success`), keeping `allow_failure: false` so prod drift still turns the pipeline red + pages. Verify is read-only; there was never a safety reason to gate it behind a Play button (now matches the dev / staging verify jobs).
- The plain `migrate-apply-<tier>` **holds per-migration** on the destructive / RequiresDBA classes — naming the `-destructive` button (§11.3.1) or routing to the MariaDB team (§11.8) respectively — while **every other pending migration applies in the same run** (only same-database migrations dam behind a hold, preserving per-database ordering). The run exits with a **dedicated code** (3 destructive / 4 RequiresDBA; RequiresDBA wins when both classes are held), which the `.migrate_apply` CI wrapper converts to a **loud green success** — a HOLD banner in the job log, pipeline progresses, the held migration stays pending — while a *real* apply error (exit 1) still hard-reds it. (Model history: `allow_failure: false` reddened the whole pipeline on every expected hold; the v2.7 `allow_failure.exit_codes: [3, 4]` model was supposed to render holds as ⚠️ warnings but GitLab did not honor it on the first live hold — 2026-07-30, dev + staging both hard-failed on exit 4 — so the conversion moved into the wrapper script and the dead `exit_codes` keyword was removed; the whole-run abort was replaced by the per-migration hold the same day after the first hold froze every unrelated database's queue.)
- **Deploy is `needs:`-gated on the apply** (not `needs: [build-*]` only — that note was stale). The gate protects deploys from a *real apply error* (exit 1 → deploy skipped); an expected **hold does NOT skip the deploy** — the wrapper-converted apply job is green, so `deploy-<tier>` proceeds. That is intentional and safe: §11.5's expand/contract rule already requires merged code to tolerate a not-yet-applied migration, and code that *depends* on a held migration must not merge until it reconciles (§11.8 author responsibility). The held destructive path ships its schema via a second deploy job, **`deploy-<tier>-destructive`**, which `needs:` the manual `migrate-apply-<tier>-destructive` and so stays skipped until that button is played, then runs (auto on staging/dev, manual on prod). This is how "deploy after migrate-apply **OR** migrate-apply-destructive" is expressed — GitLab `needs:` has no OR, so it is two deploy jobs, one per apply path. The `deploy-<tier>-emergency` bypass (`needs: [build-*]` only) still exists for rollbacks / CI-infra flakes.

Paging is webhook-based: set `UBIX_OPS_WEBHOOK_URL` as a CI variable scoped to the prod environment.

CI variables required across all tiers: `MYSQL_WRITE_HOST` / `MYSQL_WRITE_PORT` / `MYSQL_WRITE_USERNAME` / `MYSQL_WRITE_PASSWORD` (per-environment), plus `MEMCACHE_SERVERS` and `LOGGER_PATH`.

---

## 11. Destructive Operation Safety

Schema migrations are powerful — `DROP TABLE`, `TRUNCATE`, `ALTER TABLE … DROP COLUMN`, `RENAME TABLE`, and unbounded `DELETE FROM` can all wipe data on prod with no undo. Layers §11.1–§11.4 are enforced by the runner, §11.5–§11.6 by process, and §11.8 routes heavy / online-DDL changes out of the automated path entirely. Together they classify every migration into one of three CI flow classes — **non-destructive** (auto-flows, §10), **destructive** (`Destructive:` header, manual `-destructive` apply), and **`RequiresDBA`** (`RequiresDBA:` header, applied out-of-band §11.8).

### 11.1 Destructive-statement detection

The runner classifies any migration whose body matches at least one of the following statement patterns as **destructive**:

- `DROP TABLE` / `DROP DATABASE` / `DROP VIEW` / `DROP INDEX`
- `TRUNCATE TABLE`
- `RENAME TABLE`
- `ALTER TABLE … DROP COLUMN`
- `ALTER TABLE … MODIFY` (type changes can silently lose data)
- `DELETE FROM` without a `WHERE` clause

Detection runs at parse time, before any SQL hits the database.

### 11.2 Required `Destructive:` header

A destructive migration MUST add a fifth header line declaring intent:

```sql
-- Migration: 20260612091522_drop_legacy_voyeur_columns
-- Database: VSCASH
-- Description: Drop the unused voyeur_* columns from Performer_Login.
-- Author: Christopher W. Olsen
-- Destructive: Voyeur columns last referenced 2025-Q1; verified zero reads in legacy + uBix Core.

ALTER TABLE VSCASH.Performer_Login
    DROP COLUMN voyeur_enabled,
    DROP COLUMN voyeur_price_credits;
```

The `Destructive:` value is a free-form one-line reason explaining _why this can be done safely_. The runner refuses to apply a migration that contains destructive statements without this header — fails parse with: *"Migration `<id>` contains destructive statements but lacks a `Destructive:` header. Either soften the change or document the rationale."* The header forces engineers to type the word _destructive_ before they can drop a table.

### 11.3 Runtime acknowledgement on staging / prod

`migrate:up` runs every migration freely on `dev` / sandbox environments — engineers iterate fast there. On `staging` and `prod`, a pending migration with a `Destructive:` header is **held** — skipped while every other applicable migration still applies in the same run (same-database migrations dam behind it, §10) — and the runner banners:

```
DESTRUCTIVE MIGRATIONS PENDING:
  20260612091522_drop_legacy_voyeur_columns (VSCASH)
    Reason: Voyeur columns last referenced 2025-Q1; verified zero reads.

Re-run with --i-acknowledge-destructive to proceed.
```

The flag is logged into `Schema_Migrations.applied_by` with a `+destructive-ack` suffix:

```
applied_by = 'ci:deploy-prod-2026-06-12T09:15:22+destructive-ack'
```

Permanent paper trail. Dashboards filter on the suffix to surface every destructive prod apply for retro review.

**Exit code.** The destructive-pending abort returns a dedicated exit code — `UpCommand::EXIT_DESTRUCTIVE_PENDING` (**3**), distinct from `Command::FAILURE` (**1**, a genuine error) — and the RequiresDBA abort (§11.8) returns `EXIT_REQUIRES_DBA_PENDING` (**4**). This lets CI treat an *expected hold* differently from a *failure*: the `.migrate_apply` wrapper converts exits 3/4 to a loud green success (HOLD banner, pipeline progresses — §10), while exit 1 still reds it. `UpCommandTest::testHoldExitCodesMatchCiContract` pins these values to the CI contract.

#### 11.3.1 CI apply buttons — destructive-aware (staging / prod)

Each runtime tier exposes **two always-present apply jobs**:

- `migrate-apply-{staging,prod}` — the plain apply, runs `migrate:up` **without** the flag. A pending destructive migration is **held** (§11.3): it is skipped — everything else applicable still applies in the same run — and the job exits 3, which the wrapper converts to a loud green HOLD; the pipeline progresses and the `-destructive` button remains the only way the held DDL actually runs.
- `migrate-apply-{staging,prod}-destructive` — passes `--i-acknowledge-destructive` and sets `UBIX_MIGRATION_BACKUP_DIR` for the snapshot (§11.4). This is the conscious destructive apply. On green it cascades to **`deploy-<tier>-destructive`** (the deploy job gated on this apply — auto on staging, manual on prod), so playing this one button carries the destructive migration *and* the deploy through.

There is **no separate pipeline detection job** for which button to play. The plain `migrate-apply-*` job reads the real pending-vs-applied list inside the runner and, when a destructive migration is pending, holds (§11.3) and **names the `-destructive` button** to play. That signal is accurate even for a destructive migration left pending from an *earlier* push (a git-diff detector misses that case), and it appears in the job the operator just ran. Advance notice at code-review time is the separate `destructive-pr-detect` MR job (§11.6). The plain apply is `when: on_success` with wrapper-converted hold codes (an expected hold never reds the pipeline); the `-destructive` sibling is `when: manual, allow_failure: true`, so the un-played sibling never leaves the pipeline in a `blocked` state.

> **Why two buttons and not a flag toggled by detection?** An earlier design (v2.0) keyed `--i-acknowledge-destructive` — and a separate `destructive-ack-{staging,prod}` gate — on a `DESTRUCTIVE_MIGRATION` dotenv emitted by a `destructive-detect-*` job, evaluated inside the apply job's `rules:`. GitLab evaluates `rules:` at **pipeline-creation time**, *before* the detect job has run and produced that dotenv, so the variable was always empty at rule-eval: the flag was never set and the ack gate never materialised. Destructive applies on staging/prod therefore always aborted. dotenv variables are only available to downstream jobs at **script runtime**, never in `rules:` — so the choice is moved out of `rules:` entirely into two static jobs, and the now-redundant `destructive-detect-*` jobs were removed.

On `dev`, the auto `migrate-apply-dev` (`when: on_success`) handles the common non-destructive case. Dev relaxes the destructive guard (it applies destructive migrations without the flag), so exit 3 never fires there; but a `RequiresDBA` migration is held on every tier but `test` (exit 4, everything else pending applies), and the shared wrapper renders that hold as a loud green HOLD rather than a red. `migrate-apply-dev-destructive` (manual) remains for explicitly re-applying a destructive migration on dev. Dev keeps its faster auto-run model (§10).

### 11.4 Pre-apply backup snapshot

Before running a destructive migration on `staging` or `prod`, the runner takes a `mariadb-dump --single-transaction --skip-comments` of every table the migration touches and writes it to:

```
/var/ubix-migration-backups/<migration_id>/<environment>-<timestamp>.sql.gz
```

The recovery path is documented in the migration's `Description:` header (engineers SHOULD note the recovery command in the description). Snapshots are retained for 90 days, then aged out by an ops cron. Storage is bounded — destructive migrations are rare.

For migrations that affect many tables (e.g. `RENAME TABLE` chains), the runner snapshots all of them. The pre-flight inspection in §4.1 enumerates the affected objects.

**Snapshot directory must be writable by `www`.** The default root is `/var/ubix-migration-backups` (override with `UBIX_MIGRATION_BACKUP_DIR`). The runtime image runs as the non-root `www` user, which **cannot** `mkdir` under `/var`. The CI `migrate-apply-*-destructive` jobs therefore set `-e UBIX_MIGRATION_BACKUP_DIR=/tmp/ubix-migration-backups` — an in-container path `www` owns (mode 1777) — so the service's recursive `mkdir` of the per-migration subdir succeeds.

> **Why not a host bind mount?** The original design bind-mounted `/tmp/ubix-migration-backups → /var/...` with a job-side `mkdir -p && chmod 777`. That **breaks on the `the original monorepo` dind runner**: the `-v` *source* resolves on the **dind daemon's** filesystem, not the job container where the `mkdir`/`chmod` ran. Docker then auto-creates the source **root-owned**, mounts it, and `www` gets `EACCES` creating the subdir — the migration fails before any DDL. Pointing `UBIX_MIGRATION_BACKUP_DIR` at an in-container path sidesteps the daemon-vs-job filesystem split entirely.
>
> **Trade-off: snapshots are ephemeral under dind** — the in-container `/tmp` is discarded with the `--rm` container, so they do **not** honour the 90-day retention above. This is the already-accepted state under dind. **For durable prod snapshots**, mount a persistent volume (RWX PVC / NFS — note the runner's default PVC is RWO, which can `Multi-Attach`-fail under concurrent pods) at `/tmp/ubix-migration-backups` (or repoint the env var at the mount); no code change is needed once the volume exists. Tracked as a systems-team follow-up.

### 11.5 Expand/contract & soft-delete-first

**Backward compatibility is the rule that makes continuous flow (§10) safe.** Every migration must be safe to run against the code that is *currently live* — not just the code in the same release. This isn't optional politeness: uBix Core deploys are rolling across **≥ 5 pods** (see `database.md` / the K8s deploy model), so during any deploy the old and new code run **simultaneously** against one schema, and the legacy PHP callers share these databases the whole time. A migration is therefore never atomic with its deploy; it must work for the version before *and* after.

That forces the **expand/contract** (parallel-change) discipline, and it maps directly onto the §10 classes:

- **Expand** — additive, backward-compatible (`ADD COLUMN` nullable / with safe default, new table, new index, widen). Safe for old code, new code, and legacy. This is the **non-destructive** class → auto-flows all the way to prod.
- **Contract** — remove what nothing uses any more (`DROP` / narrow / `RENAME`). Only safe *after* the code that used it is fully gone, so it ships as a **separate, later release** — the **destructive** class, deliberately gated. Never drop in the same migration (or release) that retires the usage.

Concretely, retiring a column is at least two releases: (1) expand + deploy code that stops writing/reading it; (2) once monitoring confirms zero reads, a later destructive migration drops it. The soft-delete-first convention is the table-level form of the same idea:

The strongest safety pattern is **don't drop in the same migration that retires usage**. The convention:

1. Migration A renames the about-to-be-dropped object to a `_deprecated_` prefix:
   ```sql
   RENAME TABLE VSCASH.Old_Table TO VSCASH._deprecated_Old_Table_2026_06_12;
   ```
2. Migration B (≥ 30 days later, after monitoring confirms zero reads) actually drops:
   ```sql
   DROP TABLE VSCASH._deprecated_Old_Table_2026_06_12;
   ```

Both A and B are flagged destructive (rename and drop both trip the lint), so both go through layers 11.2 + 11.3. The 30-day buffer gives the platform real recovery time if the retirement was premature — a `RENAME` can be undone in seconds; a `DROP` cannot.

This is a **convention**, not a runner-enforced rule. Code review checks for the pattern.

### 11.6 Two-approver PR rule

GitLab MR settings require **two approvers** on any PR that touches `sql/migrations/*.sql` AND contains a `Destructive:` header. The lint surfaces the destructive flag during code review so the second reviewer knows what they're approving.

The runtime detection lives in `.gitlab-ci.yml` as the `destructive-pr-detect` job (in the `test` stage, MR-only). It greps the diff against the target branch for `^+.*Destructive:` lines under `sql/migrations/*.sql` and prints a clear stdout banner so both reviewers see the trigger.

The actual approval-count enforcement is configured in the GitLab UI: **Settings → Merge requests → Merge request approvals → Approval rules**, with a custom rule scoped to file path `sql/migrations/*.sql` and minimum approvers `2`. Content-match (the `Destructive:` filter) is not expressible as a GitLab native rule — every migration PR therefore picks up the two-approver requirement, with the CI banner clarifying which ones are genuinely destructive. This trade-off ships every migration PR at the higher review bar; the alternative (only-destructive triggers two-approver) needs custom GitLab webhook plumbing that's not worth the complexity for the rare destructive case.

### 11.7 What this stack doesn't prevent

Be honest about the ceiling: a determined-and-careless engineer can `--i-acknowledge-destructive` their way through staging and prod with both approvers asleep at the wheel. The layers above don't stop them — they make every step **deliberate and auditable**:

- Layer 11.2 forces the word _destructive_ into the source file
- Layer 11.3 forces a runtime flag with a logged suffix
- Layer 11.4 leaves a recoverable backup
- Layer 11.5 keeps drops in a separate window from rename
- Layer 11.6 puts a second human in the loop

No system removes the human's ability to do harm; this stack ensures harm leaves a trail.

### 11.8 `RequiresDBA` — out-of-band / online-DDL migrations

Some migrations are non-destructive yet still unsafe for the pipeline to run directly: a full table rewrite, a type change that forces `ALGORITHM=COPY`, or any operation that would lock or heavily load a large hot table. These need an online-schema-change process (native `ALGORITHM=INPLACE, LOCK=NONE`, or `pt-online-schema-change`) with throttling and replication-lag monitoring that the automated runner does not perform — and will not: runner-integrated pt-osc was considered and rejected (§11.9, 2026-07-30). The MariaDB team owns these indefinitely.

A migration declares this with a sixth header line:

```sql
-- Migration: 20260701120000_rewrite_transactions_charset
-- Database: BILLING
-- Description: Convert Transactions to utf8mb4 (full table rewrite, ~400M rows).
-- Author: Christopher W. Olsen
-- RequiresDBA: Full-table rewrite on a hot table; run via pt-osc with throttling. Coordinate via #databases.
```

The `RequiresDBA:` value is a free-form one-line note on *why* it needs the team and *how* (tool / coordination). It is **orthogonal** to `Destructive:` — a migration may carry both headers; `RequiresDBA` is the stronger gate and wins.

**Runner + CI behavior.** When the pending list contains a `RequiresDBA` migration, `migrate:up` **holds it on every real tier** — there is no acknowledgement flag that makes the pipeline run it (unlike `--i-acknowledge-destructive` for destructive). The hold is **per-migration** (since 2026-07-30; previously the whole run aborted, which froze every unrelated database's queue behind an out-of-band migration): every other pending migration still applies in the same run, and only later migrations of the **same target database** dam behind the hold. The one exemption is the **`test` tier**: the unit-test database is materialised from scratch with zero rows on every run, so the hold's rationale (long online DDL against big hot tables) cannot apply — and holding there would leave the test schema permanently missing the columns the real tiers gain at reconcile time, so DB-backed tests would run against the wrong schema. `RequiresDBA` (and `Destructive:`, per §11.3's existing relaxation) migrations therefore apply **inline on `test` only**. On every other tier the hold banner names the migration and routes it:

```
REQUIRES-DBA MIGRATIONS PENDING:
  20260701120000_rewrite_transactions_charset (BILLING)
    Reason: Full-table rewrite on a hot table; run via pt-osc with throttling.

Apply out-of-band via the MariaDB team (online DDL), then record it with:
  php bin/ubix migrate:reconcile 20260701120000_rewrite_transactions_charset --reason="..."
```

Because migrations apply in strict order, a pending `RequiresDBA` migration **holds the queue** on that tier (later migrations can't apply past it) until it's resolved. Resolution is **Model A — out-of-band**:

1. The MariaDB team applies the DDL with their online-DDL tooling against the target cluster.
2. An operator records it as applied with **`migrate:reconcile <id> --reason="MariaDB team applied via pt-osc <date>"`** (§4.3) — this stamps `applied_by = manual:<user>`, writes the checksum, and posts the reconcile notification to #databases. The runner now treats it as applied and the queue moves on.

On **dev**, an engineer may instead apply a `RequiresDBA` migration manually against the dev cluster (low stakes) and reconcile it themselves — the gate exists so the *automated* pipeline never runs a heavy operation, not to forbid deliberate manual dev application.

> **Why reuse `migrate:reconcile` rather than add a `migrate:mark-applied`?** Reconcile already does exactly this — "record as applied without running it" (§4.3, §8). A separate command would duplicate it. The `RequiresDBA` flow is reconcile's existing out-of-band-application path with a dedicated reason.

**Review-time signal.** `destructive-pr-detect` (§11.6) also greps the MR diff for `^+.*RequiresDBA:` and prints a banner, so reviewers and the DBA team see a `RequiresDBA` migration coming before it merges.

**Target state.** `RequiresDBA` is not an interim — it is the permanent split of responsibilities (§11.9, 2026-07-30): the pipeline's job is to *classify and reject*, the MariaDB team's job is to run online DDL out-of-band. What §13 still pursues is shrinking how often the route is needed — pushing correct `ALGORITHM=INSTANT` / `ALGORITHM=INPLACE, LOCK=NONE` into how migrations are authored (lint-guided, of which the §11.9 guard is the first slice) so most hot-table changes never need a table copy at all.

---

**Merge-when-ready + author responsibility (2026-07-30).** A `RequiresDBA:` migration should merge to `dev` only when its out-of-band apply is actually **scheduled** with the MariaDB team — until then it stays on the author's feature branch. The runner tolerates a parked hold gracefully (per-migration holds; only the same database's queue dams), but a hold that sits for days still dams that database and leaves a schema gap between tiers. Two hard rules for the author while their migration is held anywhere: **(1)** application code that depends on the held schema must not merge (or must be feature-flag-gated off) until the migration is reconciled on the tiers that code deploys to — deploys intentionally proceed during a hold (§10.1), so unmerged-dependency discipline is what keeps them safe; **(2)** later migrations that depend on the held one must target the same database (they dam behind it automatically) — never encode the dependency across databases.

### 11.9 Hot-table DDL guard — `AlterAck:` (machine-enforced since 2026-07-30)

**The incident this prevents:** 2026-07-29, a migration created one table and inline-`ALTER`ed three *pre-existing* BILLING transaction tables (millions of rows). The DDL replayed single-threaded on the replicas and stalled replication behind it. §11.8 existed for exactly this case — but nothing forced the classification.

**The rule (enforced by the parser for migration IDs ≥ `20260730000000`):** any `ALTER TABLE` or `CREATE INDEX … ON` whose target table is **not created in the same file** must carry one of:

- **`RequiresDBA: <reason>`** — the default for anything big or uncertain. Holds the pipeline (exit 4, §11.8) and routes to the MariaDB team, who apply it out-of-band with **pt-online-schema-change** (chunked copy, `--max-lag` replica-aware throttling — the direct cure for replication lag; uBix Core's no-FK policy §database.md removes pt-osc's main complication), then record it with `migrate:reconcile`.
- **`AlterAck: <why inline is safe>`** — the author explicitly vouches every altered pre-existing table is small enough for inline DDL (rule of thumb: < ~100k rows / seconds-not-minutes to alter). The ack is recorded in the header and attributable.

A flagged migration with neither header **fails to parse** — it cannot land, apply, or even be status-listed, so the mistake is caught at the gate, not on a replica. Detection is comment-aware (`HotTableAlterDetectorService`); migrations older than the cutoff are grandfathered (headers on applied files would be churn — header lines are outside the body checksum, but there is no value in editing history).

**pt-osc stays out of the pipeline — by decision (2026-07-30).** A runner-integrated pt-osc mode was considered and rejected: a chunked, `--max-lag`-throttled copy of a large table takes hours *by design*, and any long-running statement inside a CI job recreates the incident's second failure mode (job timeout kills the runner between executing the DDL and recording it as applied, leaving an applied-but-unrecorded migration). Rejection at the gate is the pipeline's whole job here; pt-osc is the MariaDB team's out-of-band tool, run detached from any pipeline, recorded afterwards via `migrate:reconcile`. There is no `OnlineDDL:` runner mode planned.

---

## 12. Authoring Checklist

Before opening a PR that adds a migration:

- [ ] Filename matches `sql/migrations/YYYYMMDDHHMMSS_<snake_case_description>.sql`. Timestamp is real-time UTC.
- [ ] File begins with the structured header (`Migration:` / `Database:` / `Description:` / `Author:`).
- [ ] If the migration contains any destructive statement (§11.1), the header includes a `Destructive:` line with a one-line rationale.
- [ ] **Backward-compatible (expand) by rule** — safe for the currently-live code *and* the legacy callers during a rolling deploy (§11.5). Removals (contract) ship as a separate, *later* release, never coupled to the code change that retires the usage.
- [ ] Destructive changes follow the soft-delete-first convention (§11.5) where reasonable — rename in this migration, drop in a follow-up ≥ 30 days later.
- [ ] If the change needs online DDL — a heavy operation on a large hot table (full rewrite, type change, lock risk) — it carries a `RequiresDBA:` header (§11.8) so the pipeline routes it out-of-band instead of running it inline.
- [ ] A `RequiresDBA:` migration merges to `dev` **merge-when-ready** — only when the MariaDB team's out-of-band apply is scheduled (§11.8). While it is held: no dependent application code merges un-flagged, and dependent follow-up migrations target the same database so they dam behind it.
- [ ] One target database per migration. (Multi-DB changes are multiple files.)
- [ ] Schema-only — no `INSERT` rows for reference data. Seed data goes in `sql/seeds/<descriptor>.sql`.
- [ ] Any seed file is **idempotent + key-backed** — every `INSERT INTO` has a guard (`ON DUPLICATE KEY UPDATE` / `WHERE NOT EXISTS`), and any `ON DUPLICATE` is covered by a real UNIQUE/PRIMARY key on the inserted columns (else use `WHERE NOT EXISTS`). Numeric-prefixed (`NNN_`) for order. `SeedFileIdempotencyTest` enforces the guard + key-backing (§6.1–6.2).
- [ ] Conforms to [`database.md`](database.md) for every new table / column / index.
- [ ] Reversibility considered — the migration is forward-only; if the change is risky, write a clearly-flagged follow-up migration that reverts it (don't ship _both_ sides at once unless the rollback is paired with a flag-controlled cutover).
- [ ] Locally validated — `php bin/ubix migrate:up --dry-run` parses cleanly; `migrate:up` runs on the local sandbox; `migrate:status --verify` reports green.
- [ ] PR description names the migration ID and (if a seed file lands too) the seed descriptor. Destructive migrations name the recovery path.

The `00000000000000_` prefix is reserved for the bootstrap migration; never use it.

---

## 13. Future Work

- **`bin/ubix migrate:* command suite** — ✅ **implemented** (2026-05). Commands: `migrate:status`, `migrate:up`, `migrate:diff`, `migrate:reconcile`. CI integration: `migrate-verify-*` (read-only checksum check, auto-runs) and `migrate-apply-*` (DDL execution, manual-play gate for destructive migrations) jobs run per-environment in the GitLab pipeline. See `php/Ubix/Console/Command/Migrate/` for the runner code and `.gitlab-ci.yml` for the CI jobs. The manual path (§8) remains as a documented fallback.
- **`bin/ubix seed:apply` command** — sister command for the seed split (§6). Same DI surface as `migrate:*`.
- **Cluster-level transactional migrations** — MariaDB's DDL is largely non-transactional. Future work might wrap groups of migrations in a coordinator that pauses replicas, applies, then resumes. Out of v1 scope.
- **Cross-DB foreign-key migrations** — uBix Core's [`database.md`](database.md) §3 requires soft-FK only (no `FOREIGN KEY` constraints). If that policy ever changes, this standard needs an addendum on cross-DB constraint ordering.
- **Per-environment override files** — currently disallowed (§1). If a real use case for env-specific schema appears, revisit; until then, env divergence belongs in seed files or runtime config.
- **Self-service online DDL — retire the `RequiresDBA` bottleneck (§11.8).** Push correct `ALGORITHM=INPLACE, LOCK=NONE` / `ALGORITHM=INSTANT` into how migrations are authored — the §11.9 guard is the first, structural slice of the `strong_migrations`-style linter (it forces the author to classify hot-table DDL). Runner-integrated pt-osc was considered and **rejected** (2026-07-30, see §11.9): long-running online copies don't belong inside pipeline jobs. Heavy rewrites stay `RequiresDBA` → MariaDB team, detached pt-osc, `migrate:reconcile`.
- **Two-phase apply record (`running` → `applied`).** The runner currently records a migration only *after* its statements complete, so a statement that outlives the job (timeout, killed pod, lost connection) leaves an applied-but-unrecorded migration and a pending-loop mess only `migrate:reconcile` can untangle — the 2026-07-29 incident's second failure mode. Fix: write a `running` marker row before executing and flip it to `applied` after; an interrupted run then surfaces as an explicit in-flight/interrupted state that blocks blind re-execution and points at reconcile. Pair with a small session `lock_wait_timeout` so a DDL statement queued behind a metadata lock fails fast instead of damming all subsequent queries on the table behind it.
- **Couple deploy to apply (post-cutover).** Today deploy is decoupled from migrate at the prod tier (§10) because legacy callers share the databases and a migration may be legacy-only. Once legacy callers retire, the meaningful safety gate is "don't ship code ahead of its schema" — wire `deploy-<tier>` to `needs:` the non-destructive `migrate-apply-<tier>` so a deploy waits for its migrations. (Gate on *apply*, not *verify* — verify is a drift signal, not a schema-readiness check.) `deploy-dev` and `deploy-staging` already do this; prod is the remaining step.
- **Prod continuous deployment (auto-`deploy-prod`).** `deploy-prod` is deliberately manual today. Making it auto-on-green needs the safety net that prod currently lacks: (1) **progressive / canary rollout** — ship to a small slice of pods or a traffic %, hold a bake window, then auto-promote or auto-rollback on health (in K8s this is an **Argo Rollouts** / **Flagger** controller, not the native rolling-update Deployment, plus a metrics source); (2) **SLO-driven auto-rollback** wired to those metrics/alerts; (3) a **prod smoke gate** (`main` has no test jobs today — staging is the test bed); and ideally (4) **feature-flag-decoupled release** (ship code dark via the existing `Feature_Flags` system, ramp the feature separately) so a deploy is boring. Until those exist, the one-click `deploy-prod` + `deploy-prod-emergency` pair is the right posture.

---

## Document Control

**Version History:**

| Version | Date | Author | Changes |
|---|---|---|---|
| 1.0 | 2026-05-05 | Christopher W. Olsen | Initial standard. Master `SYSTEMS.Schema_Migrations` tracker; `YYYYMMDDHHMMSS_<snake>.sql` filename format with `Migration:` / `Database:` / `Description:` / `Author:` header; UbixCli `migrate:*` runner spec (`up`, `status`, `reconcile`, `diff`); forward-only policy; schema-vs-seed split; strict checksum enforcement; tiered CI drift policy (loud-only on dev, blocking + paging on staging / prod); manual-apply + reconcile recovery path; `sql/<DB>.sql` reference dumps as drift-detection second source of truth. Runner implementation deferred to a follow-up slice. |
| 1.1 | 2026-05-05 | Christopher W. Olsen | New §11 Destructive Operation Safety — five layers: (11.1) destructive-statement detection on `DROP TABLE` / `TRUNCATE` / `RENAME TABLE` / `ALTER … DROP COLUMN` / `ALTER … MODIFY` / unbounded `DELETE FROM`; (11.2) required `Destructive: <reason>` fifth header line, runner refuses the apply without it; (11.3) runtime `--i-acknowledge-destructive` flag on staging / prod with a `+destructive-ack` suffix logged to `applied_by`; (11.4) pre-apply `mysqldump --single-transaction` snapshot to `/var/ubix-migration-backups/<id>/`, 90-day retention; (11.5) soft-delete-first convention (rename in migration A, drop in migration B ≥ 30 days later); (11.6) GitLab MR rule requiring two approvers on any PR touching `sql/migrations/*.sql` with a `Destructive:` header. §12 Authoring Checklist gains the destructive entries. Section numbering shifted: old §11 → §12, old §12 → §13. |
| 1.2 | 2026-05-07 | Christopher W. Olsen | New §10.1 documents the runner-side CI implementation: three `migrate-verify` stage jobs (dev `allow_failure: true`, staging blocking, prod blocking + paging webhook). §11.6 expanded to cover the actual mechanism — the `destructive-pr-detect` CI job greps the MR diff for `^+.*Destructive:` and prints a stdout banner; GitLab UI approver-rule scoping clarified (file-path `sql/migrations/*.sql` minimum 2 — content-match isn't expressible natively, so every migration PR picks up the higher review bar). |
| 1.3 | 2026-05-14 | Christopher W. Olsen | New §4.5 documents the `--target=<env>` / `--username=<user>` options on every `migrate:*` and `seed:*` command. `--target` resolves to `<TARGET>_MYSQL_WRITE_HOST` / `_PORT` env vars and stamps `ENV` so the destructive guard fires for the right tier; `--username` triggers a hidden-stdin password prompt and is refused without a TTY. Resolution precedence falls through to the existing `MYSQL_MIGRATION_*` / `MYSQL_WRITE_*` env-var workflow when the options are omitted, so CI is unaffected. Code example block (§4) gains two sample invocations. Implementation lives in `MigrationConnectionTargetService` + `AbstractMigrationCommand`; `MigrationPdoSqlService` was refactored to read env lazily via a new `ensureInitialized()` hook on `AbstractPdoSqlService` so the `putenv()` overrides take effect before the first PDO connect (boot-time env read would have locked in the wrong cluster). |
| 1.4 | 2026-05-14 | Christopher W. Olsen | **Frozen-baseline policy locked in.** §9 rewritten: `sql/<DB>.sql` dumps are the **pre-migration baseline** and are never refreshed post-cutover (cross-references `cutover-runbook.md` §3.6 v1.5). The canonical expected schema = baseline dump + applied migration history. §4.4 reframed: `migrate:diff` v1 ships `--mode=reference-dump` only, which is structurally noisy under the new policy (every applied migration shows as "extra in live") — treat as advisory; `--mode=replay` is now a v2 prerequisite for using `migrate:diff` as a hard CI drift gate. §10 tiered policy untouched but a one-liner clarifies that current diff failures should be triaged manually until replay-mode ships. `migrate:status --verify` (checksum drift) is unaffected and remains a hard gate. Old §9 "two complementary sources of truth" framing removed — under the new policy there's one canonical source (replay) and one advisory inspection tool (reference-dump). |
| 1.5 | 2026-05-18 | Christopher W. Olsen | **`--target=<env>` now also stamps per-tier write credentials** (§4.5 amended). When `<TARGET>_MYSQL_WRITE_USERNAME` AND `<TARGET>_MYSQL_WRITE_PASSWORD` are both set in the operator's environment, `--target=<env>` copies them onto `MYSQL_WRITE_USERNAME` / `MYSQL_WRITE_PASSWORD` alongside the existing host/port stamping. Previously `--target` retargeted host/port only, which silently authenticated against the new tier with whatever username the operator's default `MYSQL_WRITE_*` carried — a footgun once tiers like sandbox started running with their own root password (see `database:resetSchema --target=sandbox` failing to auth with the dev-cluster `livewrite2` user against the local docker MySQL). The cred pair is all-or-nothing; partial config is ignored so tier defaults don't get mixed with shell defaults. `--username=<user>` still wins (it stamps `MYSQL_MIGRATION_*`, which beats `MYSQL_WRITE_*` everywhere downstream). Implementation: new `MigrationConnectionTargetService::getTargetCredentials()`; `apply()` signature gains `$writeUsername` / `$writePassword` parameters; `AbstractMigrationCommand::applyTargetOptions()` wires the lookup through. CI unaffected — when the per-tier env vars aren't set, the behaviour is identical to v1.3. |
| 1.6 | 2026-05-19 | Christopher W. Olsen | **Tiered drift policy shifts to operator-discretion model + dev gets destructive-aware auto-run.** §10 + §10.1 rewritten to reflect the 2026-05-19 `.gitlab-ci.yml` changes. **Deploys decoupled from migrate-verify at every tier** via `needs: [build-*]` on `deploy-staging` and `deploy-prod` (`deploy-dev` was already independent through stage-only ordering with `allow_failure: true`). Both staging and prod `deploy-*` jobs gain `when: manual` so the operator triggers them consciously. **`migrate-verify-staging` flipped to `allow_failure: true`** — staging drift no longer leaves the pipeline in "manual / blocked" status when not played; the deploy is independently triggerable. **Prod migrate-verify keeps `allow_failure: false`** so prod-drift paging behavior is preserved when the operator plays the job. **New `destructive-detect-dev` job** + revamped `migrate-verify-dev`: the dev migrate-verify now auto-runs on every push (`when: on_success, allow_failure: true`) UNLESS the push diff under `sql/migrations/*.sql` contains a `Destructive:` header, in which case it flips to `when: manual, allow_failure: false` (pipeline pauses for explicit operator acknowledgement before destructive changes ship to dev). The destructive flag is passed between jobs via a dotenv artifact (`DESTRUCTIVE_MIGRATION=true\|false`). Existing `destructive-pr-detect` (MR-time warning banner) is untouched — the new push-time detector serves a different purpose (pipeline gating vs reviewer signal). |
| 1.7 | 2026-05-19 | Christopher W. Olsen | **Host/port resolution narrowed to sandbox; `--host` / `--port` overrides added; `--target` now required (§4.5 rewritten).** Tier-prefixed host/port env vars for non-sandbox tiers (`DEV_MYSQL_WRITE_HOST` / `_PORT`, `STAGING_*`, `PROD_*`) are gone — dev / staging / prod read plain `MYSQL_WRITE_HOST` / `_PORT` directly from the deployment shell, which is where they already point at the right cluster. Only `SANDBOX_MYSQL_WRITE_HOST` / `_PORT` remain (sandbox is the canonical "reachable from any operator shell via port-forward" target). New `--host=<host>` / `--port=<port>` CLI options stamp `MYSQL_WRITE_HOST` / `_PORT` for one-off cross-tier retargeting; they win over any tier-derived value from `--target`. **`--target` is now required** on every migration command — no implicit fallback to whatever `MYSQL_WRITE_*` happens to be loaded — to force an explicit tier choice. CI YAML updated to pass `--target=dev` / `=staging` / `=prod` on the `migrate:status --verify` and `migrate:diff` invocations (alongside the existing `-e ENV=<tier>`). Code lives in `MigrationConnectionTargetService::getTargetHostPort()` (sandbox-only branch) + `AbstractMigrationCommand::configureTargetOptions()` (new `--host` / `--port` options) + `applyTargetOptions()` (required-target check). Username / password resolution is unchanged in this pass — tracked as a follow-up. |
| 1.8 | 2026-05-19 | Christopher W. Olsen | **Sandbox-exclusive `SANDBOX_*` rule + `MYSQL_MIGRATION_*` clear (§4.5 amended).** `--target=sandbox` now stamps ALL five `SANDBOX_MYSQL_WRITE_*` env vars (host / port / username / password / **database**) onto the plain `MYSQL_WRITE_*` equivalents, AND clears any inherited `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` from `.env` / shell so they don't shadow the sandbox per-tier creds via the documented `MYSQL_MIGRATION_*`-beats-`MYSQL_WRITE_*` precedence. Closes the cutover bug where `.env`'s `MYSQL_MIGRATION_USERNAME='ubix-migrations'` survived the sandbox retarget and silently caused `Access denied for user 'ubix-migrations'` against the local docker MySQL (which only has `root`). `--username=<user>` still wins — it re-stamps `MYSQL_MIGRATION_*` AFTER the sandbox clear inside `apply()`, so operators can deliberately override the sandbox `root` creds when there's a reason to. Other tier resolution is unchanged: dev / staging / prod continue to read plain `MYSQL_*` env (with `MYSQL_MIGRATION_*` winning by precedence when set). The "all `SANDBOX_*` values, no exceptions" framing is the single exception to the otherwise-universal "read plain `MYSQL_*` env" model — sandbox is the only tier with a prefixed env block, so it gets its own resolution rule. Implementation: new `MigrationConnectionTargetService::getTargetDatabase()`; `getTargetCredentials()` narrowed from tier-prefixed to sandbox-only; `apply()` signature gains `$writeDatabase` and `$clearMigrationCreds` parameters; `AbstractMigrationCommand::applyTargetOptions()` sets `$clearMigrationCreds = ($target === SANDBOX && --username not given)` to preserve the deliberate-override escape hatch. Tests: 20/20 green, three new cases cover database stamping, migration-cred clearing, and `--username` re-stamping after the clear. End-to-end validated: `ubix migrate:status --target=sandbox` connects to the local sandbox MySQL as `root` and lists all applied migrations. |
| 1.9 | 2026-05-21 | Christopher W. Olsen | **`migrate-verify-staging` flipped from manual to auto-run.** §10.1 table updated. `migrate:status --verify` is a read-only checksum-drift comparison with no DDL, so the operator-Play gate previously in place on the staging verify job added friction without safety. The apply job downstream stays `when: manual` so an operator still confirms before any live-cluster DDL runs (per §10's "staging + prod are manual-only at all times" framing — applies to *apply*, not *verify*). Mirrors the auto-run pattern already in place on `migrate-verify-dev`. `allow_failure: true` retained so pipeline status stays green when drift is reported and the deploy can still be triggered independently. Implementation: removed `when: manual` from `migrate-verify-staging` in `.gitlab-ci.yml`; updated the policy-rationale comment block on both the verify job and the downstream apply job's "operator clicks Play after eyeballing" note. |
| 2.2 | 2026-06-09 | Christopher W. Olsen | **Reconcile notifies + CI actor identity fixed (§3, §4.1.1).** `migrate:reconcile` now posts to #databases too (`notifyReconciled` — a distinct "recorded as applied without running" message). **`applied_by` now records the real CI user:** the old `ci:<deploy-id>` path keyed off `CI_DEPLOY_ID`, which is not a GitLab variable, so CI applies fell through to an unset `USER` and stamped `cli:unknown`. New shared `AbstractMigrationCommand::resolveActorIdentity()` reads `GITLAB_USER_LOGIN` in CI (falling back to `USER` locally, then `unknown`), and `isCiRun()` (`CI`/`GITLAB_CI`) drives the `ci:`/`cli:` prefix → `ci:<gitlab-user-login>`. `.gitlab-ci.yml` passes `GITLAB_USER_LOGIN` + `CI` into all three migrate-apply containers. §3 actor vocabulary updated accordingly. Both `UpCommand` and `ReconcileCommand` share the helpers (and `resolveRuntimeEnvironment()`, kept distinct from the nullable `resolveEnvironment(): ?Env` that `DropSchemasCommand` / `ResetSchemaCommand` use for the refuse-on-unset destructive path). |
| 2.1 | 2026-06-09 | Christopher W. Olsen | **Slack #databases notification on apply (new §4.1.1).** `migrate:up` now posts a summary (environment, count, applied ids + databases, actor) to the #databases Slack channel after applying ≥1 migration on dev / staging / prod, via the shared `SlackService`. Covers CI and manual operator runs. Sandbox / test are skipped (local noise / unit-test schema churn must never reach a live channel). Best-effort — Slack failures are logged, never fail the apply. New `MigrationNotificationService` (+ test); `SlackService` and its PSR-17/18 collaborators wired into `app/UbixCli/src/Dependencies.php` (reuses the existing `SLACK_API_ENDPOINT`). `UpCommand` collects the applied `AppliedMigration` list and notifies in a `finally` so a partial-failure run still reports what landed. |
| 2.0 | 2026-06-09 | Christopher W. Olsen | **Separate manual destructive-acknowledgement gate on staging / prod (new §11.3.1).** Previously the `--i-acknowledge-destructive` flag was auto-supplied to `migrate-apply-{staging,prod}` based on the `destructive-detect-*` dotenv flag, so a single Play on the apply job silently acknowledged destructive DDL. New `destructive-ack-{staging,prod}` jobs (`when: manual`, `allow_failure: false`) materialise only when the push carries a `Destructive:` header and **block** the apply job via an `optional` `needs:` edge — the pipeline visibly halts on the gate (the halt is the "you have destructive migrations" signal) and apply cannot start until an operator reviews the listed migrations and plays the gate. Two conscious clicks on a destructive push: ack → apply. Non-destructive pushes resolve the gate to `when: never` (not created); apply's optional need is satisfied immediately and runs as before. Dev keeps its faster destructive-aware auto-run model (§10) and does not get the gate. Implementation: two new jobs in `.gitlab-ci.yml` + `optional` need added to `migrate-apply-{staging,prod}`. |
| 2.3 | 2026-06-22 | Christopher W. Olsen | **Destructive apply reworked to two static buttons; v2.0 ack-gate mechanism removed (§11.3.1 rewritten); backup-dir mount documented (§11.4).** The v2.0 `destructive-ack-{staging,prod}` gate and the `ACK_FLAG` rule toggle both keyed on the `DESTRUCTIVE_MIGRATION` dotenv **inside `rules:`**, which GitLab evaluates at pipeline-creation — before the detect job has produced the dotenv. The variable was always empty at rule-eval, so the ack gate never materialised and the flag was never set: **every destructive apply on staging/prod aborted** (surfaced applying `20260619140043_add_dupe_enum_studio_prospect_application` to staging). Replaced with two always-present manual buttons per tier — `migrate-apply-{dev,staging,prod}` (no flag, aborts on destructive) + `migrate-apply-{dev,staging,prod}-destructive` (passes `--i-acknowledge-destructive`). The redundant `destructive-detect-{dev,staging,prod}` jobs were **removed** — the plain apply button's abort reads the real pending list and names the `-destructive` button to play (accurate even for a destructive migration pending from an earlier push, unlike the old git-diff grep); advance notice stays in `destructive-pr-detect` (§11.6). Both `allow_failure: true` so the un-played sibling doesn't leave the pipeline `blocked` (this flips staging/prod apply from blocking to non-blocking manual). Also fixes the `mkdir(): Permission denied` on `/var/ubix-migration-backups` — the image runs as non-root `www`, so the `-destructive` jobs `mkdir`+`chmod 777` a host dir and bind-mount it onto the backup path; dev/staging use the runner's ephemeral `/tmp`, prod carries a WARNING to repoint at durable storage before relying on snapshots (§11.4). Removed the two `destructive-ack-*` jobs + the `optional` `needs:` edges in `.gitlab-ci.yml`. |
| 2.4 | 2026-06-24 | Christopher W. Olsen | **Continuous classification-gated migration flow + `RequiresDBA` class + expand/contract elevated (§10 rewritten, new §11.8, §11.5 expanded).** Migrations are now production-bound once merged to `dev` and flow on the code-promotion path; the gate is *what a migration does*, not its tier. §10 rewritten around three classes applied identically at every tier: **non-destructive auto-applies all the way to prod** (was `manual` on staging/prod — the policy change), **destructive** stays the manual `-destructive` apply, and the new **`RequiresDBA`** class aborts the auto path everywhere and routes to the MariaDB team. **`migrate-verify-prod` flipped `manual` → auto** (read-only, keeps `allow_failure: false` so prod drift still pages). New §11.8 specifies the `RequiresDBA:` sixth header line (orthogonal to `Destructive:`, stronger gate), the abort-and-route behavior, and **Model A out-of-band resolution reusing the existing `migrate:reconcile`** (no new `migrate:mark-applied` command — reconcile already records-as-applied-without-running). §11.5 retitled "Expand/contract & soft-delete-first" and elevated to the backward-compatibility *rule* that makes continuous flow safe (≥5-pod rolling deploys + legacy shared DBs mean a migration is never atomic with its deploy). §11 intro + §12 checklist + §13 (self-service online-DDL target to retire `RequiresDBA`; deploy↔apply coupling post-cutover) updated. **Pipeline `.gitlab-ci.yml` + runner `migrate:up` `RequiresDBA` detection are follow-up implementation slices to this spec.** |
| 2.5 | 2026-06-24 | Christopher W. Olsen | **Per-tier deploy gating + explicit emergency bypass (§10 deploy paragraph).** Every runtime tier now has a gated normal deploy plus an explicit `deploy-<tier>-emergency` manual bypass (`needs: [build-*]` only — ships the built image past all gates; never auto-fires, visible in history), mirroring the dev model across staging + prod. Normal deploys: `deploy-staging` gated on `static-checks-staging` + `phpunit-staging` (hard) + `migrate-verify-staging` (advisory); `deploy-prod` gated on `migrate-verify-prod` (blocking — prod drift makes the normal button un-playable, use the emergency to override). Replaces the prior "staging/prod deploy `needs: [build]` only, the normal job IS the emergency mechanism" framing — the normal job is now safe-by-default and the bypass is explicit. Still gates on migrate-**verify** (drift signal + escape hatch), not migrate-**apply** (the §13 post-cutover target). `.gitlab-ci.yml` only. |
| 2.6 | 2026-06-24 | Christopher W. Olsen | **`deploy-staging` flipped manual → auto-on-green (§10 deploy paragraph; §13).** Staging now does continuous delivery: `deploy-staging` fires automatically once the build, both quality gates, and `migrate-apply-staging` succeed — coupling deploy → migrate-**apply** at the staging tier, so it auto-deploys only when there's no blocking (destructive / `RequiresDBA`) migration (a blocking one aborts migrate-apply red and holds the deploy until handled + `deploy-staging-emergency`). `deploy-prod` stays manual; §13 gains the prod-continuous-deployment prerequisites (progressive/canary rollout via Argo Rollouts/Flagger, SLO-driven auto-rollback, a prod smoke gate, feature-flag-decoupled release). `.gitlab-ci.yml` only. |
| 2.7 | 2026-06-27 | Christopher W. Olsen | **Expected holds no longer red the pipeline + destructive deploy path + backup-dir dind fix (§10, §11.3, §11.3.1, §11.4).** Two problems fixed. **(1) Pipeline status:** a pending destructive / `RequiresDBA` migration made `migrate-apply-<tier>` exit non-zero with `allow_failure: false`, reddening the whole pipeline for an *expected, human-gated* state. `UpCommand` now returns dedicated exit codes — `EXIT_DESTRUCTIVE_PENDING` (3) / `EXIT_REQUIRES_DBA_PENDING` (4) — distinct from `Command::FAILURE` (1, a real error), and the migrate-apply jobs (all tiers) carry `allow_failure.exit_codes: [3,4]`, so an expected hold is a ⚠️ warning (pipeline stays green) while a genuine apply error still hard-fails. `UpCommandTest::testHoldExitCodesMatchCiContract` pins the codes. **Deploy gating expressed as two jobs** (GitLab `needs:` has no OR): `deploy-<tier>` `needs:` the plain apply (held → skipped); new **`deploy-<tier>-destructive`** `needs:` the manual `migrate-apply-<tier>-destructive` and auto-cascades on its green (manual on prod), replacing "ship via `-emergency` after a destructive apply". Corrected the stale "deploy `needs: [build-*]` only" note. **(2) Backup dir (§11.4):** the bind-mount approach (`-v /tmp:/var/...` + job-side `chmod 777`) **broke under the `the original monorepo` dind runner** — the `-v` source resolves on the daemon fs, so Docker auto-created it root-owned and the non-root `www` user got `EACCES` (the `mkdir(): Permission denied` on `20260627110741_drop_idx_pa_status_app_date`). Replaced with `-e UBIX_MIGRATION_BACKUP_DIR=/tmp/ubix-migration-backups` (a `www`-owned in-container path); snapshots are ephemeral under dind (already the accepted state), durable prod snapshots still need a persistent volume (RWX — the runner's RWO PVC `Multi-Attach`-fails under concurrent pods). `.gitlab-ci.yml` + `UpCommand` + `UpCommandTest`. |
| 2.8 | 2026-07-17 | Christopher W. Olsen | **Seeds now run automatically + idempotency is enforced (§6).** A new `seed` pipeline stage runs `seed:apply --all` after `migrate-apply` on **dev + staging only** — staging shares prod's DB cluster, so seeding staging seeds prod (no prod seed job). Realizes the previously-aspirational "CI runs all seeds as a routine sync." `seed:apply` gains an `--all` flag (apply every `sql/seeds/*.sql`, no-fail-fast) alongside the single-`<descriptor>` form. New `Ubix\Tests\SeedFileIdempotencyTest` (a `code:review`/phpunit gate) fails any seed whose `INSERT INTO` lacks `ON DUPLICATE KEY UPDATE` — a non-idempotent seed would duplicate-key-error on its second (every-deploy) apply. Fixed + de-duplicated the flat50 seed (`add_1461418_to_flat50`: `Seed:` header now matches the filename, `Database: STUDIOS`; removed the redundant timestamped copy — seeds don't use migration-style timestamp prefixes). `.gitlab-ci.yml` (`seed` stage + `.seed_apply` + `seed-apply-{dev,staging}`), `Console/Command/Seed/ApplyCommand`, `sql/seeds/`, `tests/SeedFileIdempotencyTest`. |
| 2.9 | 2026-07-17 | Christopher W. Olsen | **Seed Slack notifications (§6).** `seed:apply --all` now posts a `#databases` summary after each dev/staging sync via the new `SeedNotificationService` (mirrors `MigrationNotificationService`; same `:trident:` branding, Slack failures swallowed): a success list of applied seeds, or a failure header naming the errored ones. Notifies on every sync — no-op suppression (post only on real row changes) is deferred because the `mysql`-CLI batch apply path doesn't expose a reliable affected-row count. Also switched the *migration* notification icon `:floppy_disk:` → `:trident:` for consistent branding across all pipeline Slack posts. `Service/Seed/SeedNotificationService`, `Console/Command/Seed/ApplyCommand`, `Service/Migration/MigrationNotificationService`. |
| 2.10 | 2026-07-17 | Christopher W. Olsen | **Seed system v2 — apply-on-change, ordering, key-backed idempotency (§6 rewritten).** (1) **Apply-on-change:** the CI `seed` stage now applies only the seeds *changed in the push* (`git diff` → `seed:apply <descriptors>`), not `--all` every deploy — bounds a non-idempotent slip to the commit that changes it, and makes no-seed-change deploys silent no-ops. `--all` kept for fresh-env bootstrap; `seed:apply` takes variadic descriptors, applied in ascending-filename order. (2) **Ordering:** seeds adopt a numeric prefix (`NNN_`); existing two renamed (`010_pre_attribution_referrer_seed`, `020_internal_admin_2_0_page_registry`). Soft-FK note: prefer self-contained parent+children seeds; cross-file deps ordered by prefix. (3) **Key-backed idempotency:** `SeedFileIdempotencyTest` gains a check that any `ON DUPLICATE KEY UPDATE` / `INSERT IGNORE` seed's table has a UNIQUE/PRIMARY key covered by the inserted columns (parsing DDL from baseline + migrations) — catching the silent-duplicate trap (a guard with no backing key never fires). (4) Notifications now reflect real changes (only changed seeds apply), dropping the every-merge no-op post. `Console/Command/Seed/ApplyCommand`, `Service/Seed/SeedNotificationService`, `tests/SeedFileIdempotencyTest`, `.gitlab-ci.yml`, `sql/seeds/`. |
| 2.11 | 2026-07-30 | Christopher W. Olsen | **§11.9 hot-table DDL guard.** Parser-enforced (IDs ≥ 20260730000000): `ALTER TABLE`/`CREATE INDEX` on a table not created in-file requires `RequiresDBA:` (out-of-band pt-osc path) or the new `AlterAck:` header (author vouches the table is small). Response to the 2026-07-29 replication-lag incident (inline ALTERs on multi-million-row BILLING tables). New `HotTableAlterDetectorService` + `MigrationFile.alterAckReason`; older migrations grandfathered. pt-osc runner integration chartered as SB-38 phase 2. |
| 2.12 | 2026-07-30 | Christopher W. Olsen | **SB-38 phase 2 dropped — pt-osc stays out of the pipeline.** Runner-integrated pt-osc rejected: hours-long throttled copies inside CI jobs recreate the timeout → applied-but-unrecorded failure mode. §11.9 + §13 updated; pt-osc = MariaDB-team out-of-band tool + `migrate:reconcile`. §13 gains the two-phase apply record (`running`→`applied`) + `lock_wait_timeout` future-work item. |
| 2.13 | 2026-07-30 | Christopher W. Olsen | **`test` tier applies `RequiresDBA` migrations inline (§10, §10.1, §11.8).** Surfaced by the first held migration (`20260728233256`): the unit-test DB provisioning run held too, leaving the test schema missing the columns real tiers gain at reconcile time — so DB-backed tests would target the wrong schema. The unit-test database is rebuilt from scratch with zero rows every run, so the hold's rationale (long online DDL on big hot tables) can never apply there. `UpCommand::requiresDbaGuard()` now takes the resolved `Env` and passes on `Env::TEST` only (mirrors the destructive guard's existing non-staging/prod relaxation, which already covered `test`); every real tier still aborts unconditionally (exit 4). Behavioral tests added (guard holds on dev/sandbox/staging/prod, passes on test). |
| 2.14 | 2026-07-30 | Christopher W. Olsen | **§2.1 header grammar made explicit + parser hardened.** Only the seven known header keys start a header line; continuation lines may contain colons. Fixes silent truncation of multi-line `RequiresDBA:` reasons (Claude review catch on 20260728233256). |
| 2.15 | 2026-07-30 | Christopher W. Olsen | **Expected holds converted to loud green success in the CI wrapper (§10, §10.1, §11.3, §11.3.1).** The v2.7 `allow_failure.exit_codes: [3,4]` model was never exercised until the first real `RequiresDBA` hold (2026-07-30) — and GitLab did not soften the jobs: `migrate-apply-dev` and `-staging` both hard-failed on exit 4, blocking pipelines for an expected, human-gated state. The `.migrate_apply` wrapper now converts exits 3/4 to exit 0 with a prominent HOLD banner (nothing was applied; the migration stays pending until the `-destructive` button or the DBA apply + `migrate:reconcile`), which is executor- and GitLab-semantics-proof; `allow_failure.exit_codes` stays on the jobs as defense-in-depth. Exit 1 (real apply error) still reds the pipeline. Runner exit codes unchanged (`UpCommandTest` contract intact). |
| 2.16 | 2026-07-30 | Christopher W. Olsen | **Per-migration holds + per-database damming; deploys proceed on holds; merge-when-ready policy (§4, §10, §10.1, §11.3, §11.8, §12).** The first live `RequiresDBA` hold froze the whole migration queue: the runner aborted the entire run on the first held migration, so `20260730170532` (`SYSTEMS.Claude_Reviews`) never applied on dev — and the interim wrapper conversion also left the `needs:`-gated deploys firing with contradictory docs (Claude-review findings on `a39c7a0b`). Resolution: **(1)** `UpCommand` now *partitions* the pending set — held migrations (`RequiresDBA:` everywhere but test; unacknowledged `Destructive:` on staging/prod) are skipped while everything else applies in the same run; later migrations of the **same database** dam behind a hold (strict per-database ordering preserved; other databases flow — the scoped form of Flyway's `outOfOrder`); exit codes unchanged (4 wins over 3), wrapper still converts holds to a loud green job. **(2)** Deploys intentionally **proceed** on a hold — safe by §11.5 expand/contract; the §11.8 author rules make it stay safe (no dependent code merges un-flagged until reconcile; dependent follow-up migrations target the same database). **(3)** `RequiresDBA:` migrations land **merge-when-ready** (§11.8/§12) — merged only when the DBA apply is scheduled; `20260728233256` was pulled off `dev` and parked on `feature-mig-reland-transact-attempt-id` accordingly. **(4)** The dead `allow_failure.exit_codes` blocks (unreachable after wrapper conversion) were removed from the migrate-apply jobs and the stale deploy-gating comments/doc bullets corrected. |
| 2.17 | 2026-08-18 | Christopher W. Olsen | **Backtick-quoted schema qualifiers are now rewritten under `DATABASE_PREFIX` (§2.1).** `MigrationApplyService::runBodyViaMariadbCli()` rewrote qualified references with a plain `str_replace('<db>.', '<prefix><db>.')`, which matched only the BARE form — a body written as `` ALTER TABLE `ntl_db`.`transact` `` (the conventional quoting, and what the pre-flight clash check already handled via `splitObjectRef()`) kept its unprefixed schema and ran against the real cluster's name. It broke the dev pipeline's test-DB pass on `20260817221748_add_bin_8_column_to_ntl_db_transaction_tables` with `ERROR 1146 ... Table 'ntl_db.transact' doesn't exist`, and bit precisely the `RequiresDBA:` class of file that v2.13 made apply inline on TEST. Replaced with a backtick-aware, identifier-anchored `preg_replace_callback` (new private `prefixQualifiedReferences()`); a table whose name merely ends with the database name (`archive_ntl_db.x`) is left alone. New DB-backed regression test applies a backtick-quoted `CREATE TABLE` and asserts it lands in the prefixed schema (verified to fail against the old rewrite). §2.1 gains the body-qualification rule: either quoting style is fine, cross-database references are NOT rewritten. |
