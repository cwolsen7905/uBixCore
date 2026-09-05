# Migration Runner Cutover Runbook

**Status:** Ready (CLI retarget options added 2026-05-14 — see §3.2.1; frozen-baseline policy locked 2026-05-14 — see §3.6)
**Owner:** Christopher W. Olsen
**Standard reference:** [`docs/standards/migrations.md`](../../standards/migrations.md)
**Plan reference:** [`./plan.md`](./plan.md)

This runbook is what slice 8 of the migration runner plan distils
into: the operational procedure for bringing the four
`bin/ubix migrate:*` commands into use against a real cluster
for the first time. It assumes slices 1–7 have shipped (the
runner is feature-complete and verified end-to-end against the
local sandbox MariaDB pod, last validated 2026-05-07).

The runbook is **per-environment** and idempotent — running it
twice on the same cluster after first cutover should be a no-op.

---

## 1. Cutover scope

Each Ubix-consumed database cluster needs to be brought through
the runner once. Sequence:

1. **`dev` (`example.com`)** — first cutover; absorbs any
   surprises before they reach the customer-facing tiers.
2. **`staging`** — clean cutover expected; staging is rebuilt from
   `sql/<DB>.sql` on every deploy and shouldn't carry orphan state.
3. **`prod`** — the production cluster. Slowest, most cautious.

After cutover is complete the **CI policy in
`migrations.md` §10** kicks in: `migrate:status --verify` and
`migrate:diff` run on every deploy with the tier-appropriate
`allow_failure` flag.

---

## 2. Pre-requisites

### 2.1 Credentials

The runner needs MySQL **DDL** grants (`CREATE`, `ALTER`, `DROP`,
`INDEX`) on every Ubix-consumed database for the cluster you're
cutting over. The standard read/write app user — `livewrite2` on
dev-db at the time of writing — has DML only and will fail on
bootstrap with `ERROR 1142 (42000): CREATE command denied`.

Per-environment options:

- **Use the existing DBA user.** Most clusters already have a
  human-DBA or migration-role account with the necessary grants.
  Populate `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD`
  with that account for the duration of the cutover (see §3.2).
- **Provision a dedicated migration user** with DDL on the
  Ubix-consumed databases only. This is the cleaner long-term
  posture but requires DBA coordination. The CI runner uses this
  user once cutover is complete.

**`MYSQL_MIGRATION_*` override**: the migration runner consumes
`MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` when
**either** is non-empty, falling back to `MYSQL_WRITE_USERNAME` /
`MYSQL_WRITE_PASSWORD` when both are empty. The override covers
**both** path families:

- **Shell-out paths** (`MigrationCredentialResolverService`):
  `migrate:up` apply via the `mariadb` CLI, destructive-backup
  `mariadb-dump`, `migrate:diff`'s `mariadb-dump`, and `seed:apply`.
- **PDO paths** (`MigrationPdoSqlService`, scoped to
  `SchemaMigrationSqlRepository` only): tracker reads, tracker
  INSERT, advisory lock acquisition / release, the pre-flight
  `INFORMATION_SCHEMA.TABLES` check.

Host, port, and database always come from `MYSQL_WRITE_*` (same
cluster, just a different cred set). The PDO path collapses read
and write pools onto the writer cluster — migration commands need
to read their own tracker writes immediately and cannot tolerate
replica lag. Operators leave the app's `MYSQL_WRITE_*` /
`MYSQL_READ_*` env alone and just set the migration-only override
for the cutover window.

**Scope of the override:** the explicit DI binding in
`app/UbixCli/src/Dependencies.php` rewires SqlService **only
for `SchemaMigrationSqlRepository`**. Other CLI commands (e.g.
`flag:set`, `flag:get`) continue to use the regular
`MysqlPdoSqlService` with app-level creds — so the elevated cred
set doesn't accidentally apply to non-migration CLI work.

### 2.2 Tools on the runner host

- `php` 8.3+ (Ubix's runtime)
- `mariadb` and `mariadb-dump` CLI clients (`mariadb-client` package
  in the existing image)
- Network reachability to the target cluster on its MySQL port

### 2.3 Awareness

Read these sections of the standard before cutting over:

- `migrations.md` §1 (forward-only)
- §3 (master tracker shape)
- §4.1 (`migrate:up` step ordering)
- §8 (manual application + reconcile)
- §11 (destructive operation safety, especially §11.3 runtime
  acknowledgement)

---

## 3. Cutover sequence — per cluster

The default flow assumes a clean `migrate:up` works without
reconcile. Section 4 covers the contingency.

### 3.1 Snapshot the current state (read-only)

```bash
# Establish the baseline — should run cleanly on a healthy cluster
# with the app-user creds, since these are read-only.
php bin/ubix migrate:status
php bin/ubix migrate:diff
```

What you're looking for:

- `migrate:status` lists every on-disk migration as `pending`
  before cutover. The tracker table doesn't exist yet —
  `trackerTableExists()` returns false and every file shows
  `pending`. This is correct.
- `migrate:diff` will report drift on every database. Most of the
  drift is normal pre-cutover state — it just means the live
  cluster has accumulated changes that aren't in the checked-in
  reference dumps. **Read every per-DB drift block carefully**
  and decide which differences are:
  - Expected (a recorded migration is about to apply)
  - Grandfathered (pre-existing state that will need
    reconciliation — see §4)
  - Unrecorded manual change (needs a follow-up migration to
    capture)

Save the diff output for the post-cutover comparison.

### 3.2 Get DDL-grant credentials in place

Before mutating anything, populate the migration-only credential
override:

```bash
export MYSQL_MIGRATION_USERNAME=<DDL user>
export MYSQL_MIGRATION_PASSWORD=<DDL password>
export ENV=<dev|staging|prod>
```

That's the entire cred swap. Both PDO paths (tracker reads/writes,
advisory lock, pre-flight `INFORMATION_SCHEMA.TABLES`) and
shell-out paths (apply / dump) pick up the migration cred set
per §2.1. App-level `MYSQL_WRITE_*` / `MYSQL_READ_*` env stays
untouched; non-migration CLI commands continue using the regular
app creds.

`ENV` is what the runner's destructive-acknowledgement guard
keys off of (§11.3). Set it accurately even on dev — the value
ends up in `Schema_Migrations.applied_by`. Host and port stay
from the app-level env — only the cred set differs.

#### 3.2.1 Alternative — operator-driven retarget via CLI options

`--target=<env>` is **required** on every `migrate:*` and
`seed:*` command (and on `database:resetSchema`) — no implicit
fallback to whatever `MYSQL_WRITE_*` happens to be loaded in
the operator's shell. The flag stamps `ENV` so the §11.3
destructive guard fires for the right tier. Cred resolution is
tier-specific:

- **`--target=sandbox` (the sandbox-exclusive path):** all
  five `SANDBOX_MYSQL_WRITE_*` env vars (host / port /
  username / password / database) are stamped onto the plain
  `MYSQL_WRITE_*` equivalents, AND any inherited
  `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD`
  from `.env` / shell are cleared so they don't shadow the
  sandbox creds. Sandbox is the only tier with a prefixed env
  block and the only tier that triggers the cred-clear —
  every other tier reads plain `MYSQL_*` env.
- **`--target=dev` / `=staging` / `=prod`:** host / port /
  cred resolution falls through to whatever the deployment
  shell's plain `MYSQL_WRITE_*` and (when set)
  `MYSQL_MIGRATION_*` env vars provide. Use
  `--host=<host>` / `--port=<port>` to override per
  invocation, and `--username=<user>` to override the cred
  set.

```bash
# Sandbox — set the SANDBOX_MYSQL_WRITE_* block once in the
# operator's shell (or .env); these are local-docker defaults
# and not secrets:
export SANDBOX_MYSQL_WRITE_HOST=127.0.0.1
export SANDBOX_MYSQL_WRITE_PORT=30306
export SANDBOX_MYSQL_WRITE_USERNAME=root
export SANDBOX_MYSQL_WRITE_PASSWORD=SandboxTestPassword1234
export SANDBOX_MYSQL_WRITE_DATABASE=ntl_db

php bin/ubix migrate:status --target=sandbox
php bin/ubix migrate:up     --target=sandbox --yes

# dev / staging / prod — invoked from a deployment shell whose
# MYSQL_WRITE_HOST / _PORT already point at the right cluster.
# When MYSQL_MIGRATION_USERNAME / _PASSWORD are pre-set in the
# environment, those win automatically; otherwise pass
# --username and the runner prompts for the password on hidden
# stdin:
php bin/ubix migrate:status --target=dev
php bin/ubix migrate:up     --target=staging --username=dba_user --yes

# Cross-tier retargeting — point at a different cluster from
# whatever shell you're in by passing --host / --port directly:
php bin/ubix migrate:status --target=staging --host=example.com --port=3306 --username=dba_user
```

The CLI path is equivalent to the `export` workflow above and
both honour the same §11.3 destructive guard — `--target=prod`
forces prod rules even from a sandbox shell. The `--username`
prompt is refused without a TTY, so CI / scripted runs must
keep using `MYSQL_MIGRATION_*` env vars per §2.1; the option
surface is operator-ergonomic, not a CI replacement.

The sandbox `MYSQL_MIGRATION_*` clear (above) is what makes
`--target=sandbox` work cleanly when `.env` ships baseline
`MYSQL_MIGRATION_USERNAME` / `_PASSWORD` values for the
non-sandbox tiers — without the clear, those baseline creds
would survive the sandbox retarget and silently fail auth
against the local docker MySQL (whose `root` user is the only
account that exists). `--username=<user>` still wins when
present — it re-stamps `MYSQL_MIGRATION_*` AFTER the sandbox
clear inside `apply()`, so the deliberate-override escape
hatch is preserved.

### 3.3 Dry-run to confirm the plan

```bash
php bin/ubix migrate:up --dry-run
```

The banner should show:

- Resolved target host:port matching your DDL credentials.
- The pending list with one entry per non-applied migration.
- A 🔥 marker on any destructive migration.

If the host doesn't match what you expect, **stop**. Re-check
your env vars before doing anything mutating.

### 3.4 Apply

```bash
php bin/ubix migrate:up
```

When the confirmation prompt appears (`Proceed? [y/N]`), confirm
the host one more time before answering `y`. CI / scripted runs
should pass `--yes` to skip the prompt.

If any pending migration carries a `Destructive:` header AND
`ENV` is `staging` or `prod`, the runner aborts with the
destructive guard's table per §11.3. Re-run with
`--i-acknowledge-destructive` to proceed; the
`+destructive-ack` suffix lands in
`Schema_Migrations.applied_by` for the audit trail.

### 3.5 Verify

```bash
php bin/ubix migrate:status --verify
php bin/ubix migrate:diff
```

Expected post-cutover state:

- `migrate:status --verify` shows every migration as `applied`
  with a `Checksum OK`. Any `DRIFT` row indicates an applied
  migration whose on-disk file was edited after apply — fix
  forward (§7), don't edit back.
- `migrate:diff` should show **less** drift than the §3.1
  baseline. The tables created by the just-applied migrations
  now appear in live but won't be in the reference dumps until
  they're refreshed (see §3.6). Every other drift block should
  match the pre-cutover baseline; if a NEW drift block appears,
  the migration body did something other than what the
  reference state expected.

### 3.6 Do NOT refresh reference dumps

**Policy (locked 2026-05-14):** the `sql/<DB>.sql` dumps are the
frozen **pre-migration baseline**. They are never refreshed
post-cutover — every schema change from this point forward lives
in `sql/migrations/` and only there. The combination of
`sql/<DB>.sql` + the migration history (in filename order) is
the canonical source of truth for the current schema.

This means:

- `database:resetSchema` reproduces the live state cleanly by
  importing dumps + running `migrate:up` (§5.2).
- `migrate:diff` against the reference dump (the only mode v1
  ships) becomes structurally noisy after the first migration
  applies — live will always contain "extra" from the migration
  history. Treat that mode as advisory only until replay-mode
  ships (`migrations.md` §9).
- The eventual baseline-squash will happen as a deliberate
  event (re-export from a known-clean cluster, simultaneously
  remove the squashed-in migrations), not as a routine
  per-deploy refresh.

No action in §3.6 — proceed directly to §3.7.

### 3.7 Roll back to app-user credentials

Drop the migration cred override:

```bash
unset MYSQL_MIGRATION_USERNAME MYSQL_MIGRATION_PASSWORD
unset ENV
```

App-level `MYSQL_WRITE_*` / `MYSQL_READ_*` env was never touched
in §3.2, so nothing to restore there. The CI runner's environment
carries the DDL-grant set in `MYSQL_MIGRATION_*` scoped to its
job; nothing else should retain DDL access after the cutover.

---

## 4. Reconcile contingency

If `migrate:up` aborts with the §8 manual-application message —

```
Object `<DB>.<Object>` already exists; this migration appears
to have been applied manually. Run `migrate:reconcile <id>`
to record it.
```

— the listed object pre-exists on the cluster (manual `CREATE`,
prior environment state, or an earlier bypass). The fix path:

### 4.1 Verify the object's state matches the migration

Before reconciling, confirm the existing object matches what the
migration would have created. Two scenarios:

- **Matches**: the migration was applied manually before the
  runner adopted this cluster. Reconcile is the correct
  recovery — the migration's body has effectively run, the
  tracker just doesn't know about it yet.
- **Doesn't match**: the existing object has different columns,
  indices, defaults, or types. **Do not reconcile.** Decide
  whether to (a) rename / drop the existing object so the
  migration can apply cleanly, or (b) author a new
  `ALTER`-based migration to bring the existing object up to
  spec. The choice depends on data — talk to the database
  owner.

The `migrate:diff` output from §3.1 is the primary input for
this decision.

### 4.2 Reconcile when matching

```bash
php bin/ubix migrate:reconcile <migration_id> \
  --reason="<short prose explaining WHY this was applied out-of-band>"
```

The `--reason` is required and is appended to the recorded row's
description as `(Reconciled: <reason>)` so reviewers see the
rationale without leaving the tracker. The recorded
`applied_by` is `manual:<unix-username>`.

### 4.3 Resume migrate:up

```bash
php bin/ubix migrate:up
```

The runner skips the reconciled migration (already in the
tracker) and continues with the next pending file.

### 4.4 Reconcile-cascade

If multiple migrations are in the manual-application state on
the same cluster, reconcile them in filename order before
re-running `migrate:up`. The runner aborts at the first
manual-state match per `up`, so reconcile + re-run is a loop
until either the tracker is fully caught up or you hit a
genuinely-broken one (§4.1 second case).

---

## 5. Rollback / recovery

### 5.1 Mid-apply failure

If `migrate:up` fails mid-loop (e.g. the mariadb CLI exits
non-zero because of a syntax error in the body, or the writer
INSERT fails after the body succeeded — the situation that
prompted the bootstrap-description widening in commit
`a4174a8a`), the runner exits at the failed migration without
attempting subsequent ones. Forward-only invariant per §1.

The recovery path depends on which step failed:

- **Body failed, no tracker row written**: the live state may
  be partially modified (some statements ran before the failing
  one). Manually reverse those changes, fix the migration body,
  re-run.
- **Body succeeded, tracker write failed**: the live state has
  the migration's effect but the tracker doesn't know.
  - If the migration is repeatable (`CREATE TABLE IF NOT
    EXISTS` etc.): re-run `migrate:up` once the tracker-write
    issue is fixed.
  - If not repeatable (most schema migrations): use
    `migrate:reconcile` per §4.

### 5.2 Cluster-level recovery on dev / sandbox

For dev or sandbox where data is reproducible, the nuclear
option is `database:resetSchema --target=<env> --drop-database`
(or the positional shorthand `database:resetSchema <env>
--drop-database`). The command does the full sequence in one
shot: discovers every `sql/<DB>.sql` baseline dump, imports
each (optionally `DROP DATABASE`-ing first), then runs
`migrate:up --yes` inline so the migration history lands on top.
Under the frozen-baseline policy (§3.6) this *always* reproduces
the canonical current state because the dumps never drift — the
delta between the baseline and "now" is entirely owned by
`sql/migrations/`. Pass `--skip-migrations` if you only want
the dump-import step (e.g. debugging a bad migration or
inspecting the original baseline). The command **refuses
outright** against `staging` and `prod` — those tiers must use
the cutover sequence in §3 of this runbook. Cred resolution
shares the `migrate:*` `--target` / `--username` plumbing per
§3.2.1; in regular use during local sandbox validation, no
flags are needed because the sandbox's `MYSQL_WRITE_*` env
already points at `127.0.0.1:30306`.

### 5.3 Cluster-level recovery on staging / prod

The destructive-safety stack provides `mariadb-dump
--single-transaction` snapshots in
`/var/ubix-migration-backups/<id>/<env>-<timestamp>.sql.gz`
for every destructive migration. Recovery from a destructive
mistake is gunzip + mysql import. The non-destructive case (a
plain `CREATE TABLE` failed mid-statement) needs no backup —
just drop the partially-created object and re-run.

---

## 6. Communications

### 6.1 Pre-cutover

- Coordinate the cutover window with the team chat. Even
  non-destructive cutovers acquire the
  `GET_LOCK('ubix_migrations', 30)` advisory lock — a parallel
  deploy hitting the same cluster blocks until the cutover
  finishes (or times out).
- Stage staging cutover ahead of prod cutover — staging acts as
  the second integration test after the local sandbox.

### 6.2 Post-cutover

- Update the team chat with the cutover result and a link to
  the `migrate:status` output.
- File a follow-up ticket if `migrate:diff` surfaced any
  unrecorded manual changes — those need migrations authored to
  capture them, otherwise the next cutover-target cluster will
  trip on the same drift.

---

## 7. First-cutover lessons (2026-05-07)

These showed up during end-to-end validation against the local
sandbox at `127.0.0.1:30306` and are baked into the runner now.
Listed for awareness during real-cluster cutovers:

- **Bootstrap description was VARCHAR(512), too small for
  one-paragraph migration descriptions.** Widened to TEXT in
  commit `a4174a8a`. Future authors should keep migration
  descriptions concise, but the column itself is no longer the
  bottleneck.
- **`MYSQL_WRITE_*` and `MYSQL_READ_*` mismatch silently
  routed pre-flight to the wrong cluster.** The §3.2 step
  exports both sets together; do not split them across
  clusters under any normal cutover scenario.
- **`database:resetSchema --drop-database` was failing on FK
  constraints across DBs.** Fixed in commit `a4174a8a` with a
  `SET FOREIGN_KEY_CHECKS=0` / `=1` bracket around the drop
  pass. Sandbox-only command, but the lesson generalises:
  cross-DB FKs in legacy schemas need careful handling during
  any whole-cluster operation.
- **The generic `DtoException::getMessage()` "The query
  execution failed." swallowed the real driver message.** Fixed
  in commit `58a57c8c` — `migrate:up` now drills into
  `PdoError::driverMessage` for a useful one-line error.

---

## 8. Active rollout — pickup notes (paused 2026-05-07)

**Context for whoever picks this back up:** the runner code is
done and validated against the local sandbox MariaDB pod. The
GitLab CI side is partially wired but not yet executed against
a real cluster — the rollout was paused mid-CI-bring-up to
swing focus back to the customer-facing refactor (see
[`docs/projects/customer-facing-refactor/status.md`](../customer-facing-refactor/status.md)
"Suggested next moves").

### 8.1 What's done

- **Runner code:** all four commands (`migrate:up` /
  `migrate:status [--verify]` / `migrate:reconcile` /
  `migrate:diff`) plus seeds (`seed:apply` / `seed:list`)
  shipped, end-to-end validated against `127.0.0.1:30306`.
- **MR:** open against `dev` from `feature-home-page-revamp`
  (head commit `8753133a` at pause time).
- **`.gitlab-ci.yml`:** `migrate-verify-{dev,staging,prod}` jobs
  defined and **gated behind `when: manual`** so they only fire
  on a play-button click. `destructive-pr-detect` stays
  automatic.
- **Dockerfiles:** `Dockerfile`, `Dockerfile_Prod`,
  `Dockerfile_Sandbox` all copy `sql/` into `/web/sql/` so the
  runner can find migrations inside the image.
- **`--workdir /web`** added to every `docker run` invocation in
  the CI YAML so `bin/ubix` resolves correctly.
- **Fixed (2026-06-02): `DATABASE_PREFIX` not applied to the
  migration PDO DSN.** The first real prefixed test-tier run
  (`database:resetSchema --target=test --prefix=t<pipeline_id>_`)
  failed with `SQLSTATE[HY000] [1049] Unknown database 'ntl_db'`.
  Root cause: `MigrationPdoSqlService` pinned `dbname` to the
  UNPREFIXED `MYSQL_WRITE_DATABASE` (e.g. `ntl_db`), while the
  body-apply path (`runBodyViaMysqlCli`) and the tracker table
  create/use the PREFIXED schema (`t<pid>_ntl_db`). The PDO
  connection now prepends `DATABASE_PREFIX` to its DSN `dbname`,
  matching those paths. No-op when the prefix is empty
  (dev / staging / prod runtime). This was the local-sandbox
  validation gap — local runs happened to use a prefix whose
  database already existed; the ephemeral per-pipeline prefix
  surfaced it.

### 8.2 What's outstanding

In likely order of remaining-effort:

1. **Configure GitLab CI variables.** `MYSQL_WRITE_HOST`,
   `MYSQL_WRITE_PORT`, `MYSQL_WRITE_USERNAME`,
   `MYSQL_WRITE_PASSWORD`, `MYSQL_READ_*` (mirror), plus
   `MEMCACHE_SERVERS` and `LOGGER_PATH`. Settings → CI/CD →
   Variables in GitLab, scoped per environment (dev / staging /
   prod). DBA-coordination required for the per-environment
   credential set.
2. **Confirm gitlab-runner host network reachability** to
   `example.com:3306` (and the equivalent staging /
   prod cluster hosts).
3. **First real `migrate-verify-dev` click.** Once 1 + 2 are
   done, click the play button on a dev-branch pipeline. Expect
   `migrate:status --verify` to read fine (read-only, app-user
   `livewrite2` has SELECT) and `migrate:diff` to surface real
   drift. The drift output is the backlog of "what got applied
   manually before the runner existed."
4. **Cutover dev cluster** — follow §3 of this runbook. DDL
   creds swap is the gating step; everything downstream is
   one-shot.
5. **Drop `when: manual` from `migrate-verify-dev`** once the
   verify path has clean runs against dev. Staging / prod stay
   manual until their respective cutovers run.

### 8.3 Pickup triggers

When returning to this work, look for:

- A red `migrate-verify-dev` pipeline → §8.2 step 1 or 2 not
  done yet.
- A green `migrate-verify-dev` with non-zero exit on
  `migrate:diff` → that's the cutover backlog; switch to §3 of
  this runbook against dev.
- "I want to start cutover for staging / prod" → §3 with the
  appropriate `ENV` value.
- "Why is `migrate:up` failing in CI?" → most likely the
  app-user creds rather than the DDL-grant set. Slice 8 of
  `plan.md` and §2.1 of this runbook cover the credential
  story.

### 8.4 Why we paused

Customer-facing refactor work is the higher-leverage thread for
the team this week (Charter is in stakeholder review window,
homepage tech spec just landed). The CI rollout's outstanding
items are operational — DBA coordination, GitLab variable
configuration, network sign-off — none of which require the
runner code to change. Resuming is a matter of getting people
in a room rather than writing more code.

---

## Document Control

| Version | Date       | Author                | Notes |
|---------|------------|-----------------------|-------|
| 1.0     | 2026-05-07 | Christopher W. Olsen | Initial cutover runbook drafted after end-to-end validation against the local sandbox. Distils slice 8 of `plan.md` into a per-cluster procedure: pre-flight, DDL-cred swap, dry-run, apply, verify, dump-refresh, roll back to app creds. Section 4 covers the reconcile contingency for grandfathered state; section 5 covers rollback paths; section 7 records the four real bugs end-to-end validation surfaced. |
| 1.1     | 2026-05-07 | Christopher W. Olsen | New §8 "Active rollout — pickup notes" captures the in-flight CI-bring-up state at pause time: what's done (runner code + Dockerfiles + manual-gated jobs + workdir fix), what's outstanding (GitLab CI variables, network reachability, first verify click, cutover proper, dropping `when: manual` post-stabilisation), and pickup triggers so the next session can re-enter without re-loading context. Also captures *why* we paused: customer-facing refactor is higher leverage this week, and the remaining migration work is operational coordination rather than code. |
| 1.2     | 2026-05-08 | Christopher W. Olsen | **Cred-swap workflow simplified.** §2.1 + §3.2 + §3.7 rewritten around a new `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` env-var pair that overrides the app-level write creds for **both** the runner's shell-out paths (`MigrationCredentialResolverService` — apply, destructive backup, schema diff, seed apply) **and** its PDO paths (`MigrationPdoSqlService`, scoped to `SchemaMigrationSqlRepository` only — tracker reads/writes, advisory lock, `INFORMATION_SCHEMA` pre-flight). The override is scoped surgically: other CLI commands (`flag:set`, etc.) keep using the regular `MysqlPdoSqlService` with app creds, so elevated grants don't leak past the migration tier. Operators no longer have to swap the entire `MYSQL_WRITE_*` / `MYSQL_READ_*` env for cutover — set `MYSQL_MIGRATION_*` and the cred set picks up everywhere the runner needs DDL grants; unset it after cutover and the runner falls back to app creds. Smoke-validated against the local sandbox: `migrate:status` and `migrate:up --dry-run` run cleanly with bogus `MYSQL_WRITE_*` + valid `MYSQL_MIGRATION_*`, confirming both PDO and shell-out paths honour the override. No code-side schema or behavioural change; pure operator-UX win + closes a four-way duplication of env-reading code into the shared resolver service. |
| 1.3     | 2026-05-14 | Christopher W. Olsen | **Operator-driven retarget via `--target` / `--username` (cross-references `migrations.md` §4.5).** New §3.2.1 documents the sandbox-initiated path: per-env `<TARGET>_MYSQL_WRITE_HOST` / `_PORT` env vars set once on the operator's box, then per-invocation `--target=<env>` + `--username=<user>` (the latter triggers a hidden-stdin password prompt) point any `migrate:*` / `seed:*` command at the chosen cluster without `export`ing secrets. `--target` also stamps `ENV` so the destructive guard from §11.3 fires for the right tier even when invoked from a sandbox shell. The option surface is operator-ergonomic only — the `--username` prompt is refused without a TTY, so CI continues to rely on `MYSQL_MIGRATION_*` env vars per §2.1. Code lives in new `MigrationConnectionTargetService` + `AbstractMigrationCommand`; `MigrationPdoSqlService` was refactored to read env lazily on first PDO use (new `ensureInitialized()` hook on `AbstractPdoSqlService`) so the CLI override `putenv()`s take effect before the connection is built. |
| 1.4     | 2026-05-14 | Christopher W. Olsen | **`database:resetSchema` rewired** (§5.2 rewritten). The command now (1) auto-discovers every `sql/<DB>.sql` baseline dump rather than hardcoding a 14-DB sandbox subset, (2) shares the `migrate:*` `--target` / `--username` plumbing for cred resolution, (3) runs `migrate:up --yes` inline after the dump import so the cluster ends at the post-cutover state in one command, (4) refuses outright against `staging` / `prod` (the old command silently did nothing for those because their `MYSQL_DATABASES` arrays were empty — now the refusal is loud), and (5) added `--skip-migrations` for the rare cases an operator wants the dump-import step alone. Positional `env` argument is now optional and acts as shorthand for `--target=<env>`. |
| 1.5     | 2026-05-14 | Christopher W. Olsen | **Frozen-baseline policy locked in** (§3.6 rewritten, §5.2 cross-references). The `sql/<DB>.sql` dumps are now declared frozen at their pre-migration baseline — **never refreshed post-cutover**. Every schema change from this point forward lives in `sql/migrations/` and only there; the combination `sql/<DB>.sql` + `sql/migrations/*` in filename order is the canonical source of truth for the current schema. The old §3.6 ("Refresh reference dumps") is gone — replaced with a clear no-op step pointing at the policy. **Downstream implication:** `migrate:diff`'s only v1 mode (reference-dump comparison) becomes structurally noisy after the first migration applies — live will always contain "extra in live" from the migration history. That mode is now documented as advisory only (`migrations.md` §9). Replay-mode (rebuild scratch DB from migration history, diff against that) becomes a v2 prerequisite before `migrate:diff` is usable as a real drift gate; tracked as a follow-up in `plan.md`. **Why now:** committing to this policy rather than refreshing dumps post-cutover keeps `database:resetSchema` clean forever (dumps + migrations always reproduce live state), avoids the v1.4 "caveat to track" entirely, and defers the more complex baseline-squash work until the migration list is actually long enough to warrant it. |
| 1.6     | 2026-05-18 | Christopher W. Olsen | **Per-tier write credentials wired into `--target` (§3.2.1 amended; cross-references `migrations.md` §4.5 v1.5).** `--target=<env>` now also picks up `<TARGET>_MYSQL_WRITE_USERNAME` / `_PASSWORD` from the operator's environment (alongside the existing host/port lookup) and stamps them onto `MYSQL_WRITE_USERNAME` / `MYSQL_WRITE_PASSWORD`. Closes a footgun where `database:resetSchema --target=sandbox` retargeted host/port but kept whatever username the operator's default `MYSQL_WRITE_*` carried — for the canonical local-docker-sandbox case (`root` / `SandboxTestPassword1234`, host `127.0.0.1:30306`) that meant authenticating against the sandbox MySQL with the dev-cluster `livewrite2` user and silently failing. §3.2.1 gains an `export SANDBOX_MYSQL_WRITE_USERNAME=…` / `_PASSWORD=…` block alongside the existing host/port exports. Cred pair is all-or-nothing; `--username=<user>` still wins via `MYSQL_MIGRATION_*` precedence. Implementation: new `getTargetCredentials()` method on `MigrationConnectionTargetService`; `apply()` signature extended; CI unaffected when the per-tier creds aren't set. |
| 1.7     | 2026-05-19 | Christopher W. Olsen | **Host/port resolution narrowed to sandbox; `--host` / `--port` overrides added; `--target` now required (§3.2.1 rewritten).** Tier-prefixed host/port env vars (`DEV_MYSQL_WRITE_HOST` / `_PORT`, `STAGING_*`, `PROD_*`) are gone — dev / staging / prod read plain `MYSQL_WRITE_HOST` / `MYSQL_WRITE_PORT` directly from the deployment shell, which is where they already point at the right cluster. Only `SANDBOX_MYSQL_WRITE_HOST` / `_PORT` remain (sandbox is the canonical "reachable from any operator shell via port-forward" target). New `--host=<host>` / `--port=<port>` CLI options stamp `MYSQL_WRITE_HOST` / `_PORT` for one-off cross-tier retargeting; they win over any tier-derived value from `--target`. `--target=<env>` is now **required** on every migration command — no implicit fallback to whatever `MYSQL_WRITE_*` happens to be loaded — to force an explicit tier choice. CI YAML updated to pass `--target=dev` / `=staging` / `=prod` on the `migrate:status --verify` and `migrate:diff` invocations (alongside the existing `-e ENV=<tier>`). Code lives in `MigrationConnectionTargetService::getTargetHostPort()` (sandbox-only branch) + `AbstractMigrationCommand::configureTargetOptions()` (new `--host` / `--port` options) + `applyTargetOptions()` (required-target check). Username / password resolution is unchanged in this pass — tracked separately. |
| 1.8     | 2026-05-19 | Christopher W. Olsen | **Sandbox-exclusive `SANDBOX_*` rule + `MYSQL_MIGRATION_*` clear (§3.2.1 amended; cross-references `migrations.md` §4.5 v1.8).** `--target=sandbox` now stamps ALL five `SANDBOX_MYSQL_WRITE_*` env vars (host / port / username / password / **database**) onto the plain `MYSQL_WRITE_*` equivalents AND clears any inherited `MYSQL_MIGRATION_USERNAME` / `MYSQL_MIGRATION_PASSWORD` so they don't shadow the sandbox creds via the `MYSQL_MIGRATION_*`-beats-`MYSQL_WRITE_*` precedence. Closes the cutover bug where `.env`'s `MYSQL_MIGRATION_USERNAME='ubix-migrations'` survived the sandbox retarget and silently failed auth against the local docker MySQL (which only has `root@%` and `root@localhost`). `--username=<user>` still wins — it re-stamps `MYSQL_MIGRATION_*` AFTER the sandbox clear, preserving the deliberate-override escape hatch. The §3.2.1 example block gains the missing `export SANDBOX_MYSQL_WRITE_DATABASE=ntl_db` line (the fifth `SANDBOX_*` value, now stamped on retarget). dev / staging / prod paths unchanged — they continue to read plain `MYSQL_*` env. Implementation: new `MigrationConnectionTargetService::getTargetDatabase()`; `getTargetCredentials()` narrowed from tier-prefixed to sandbox-only; `apply()` signature gains `$writeDatabase` and `$clearMigrationCreds`. End-to-end validated: `ubix migrate:status --target=sandbox` connects as `root` and lists the migrations; `--target=dev` continues to work as before. |
