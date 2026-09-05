# Sensitive-Data Access — Auditing & Gating

**Author:** Christopher W. Olsen — **Status:** Draft for review — **Date:** 2026-07-28.

How uBix Core surfaces expose and record access to **PII / sensitive subject data** — customers, affiliates, broadcasters/models, and admin users. Access to this data is a **regulated control** (PCI-DSS §10, SOC 2, ISO 27001, GDPR accountability): *who* accessed *whose* data, *when*, and *why* must be gated and recorded. This standard makes that a uBix Core convention rather than a per-surface decision.

**Applies to:** any uBix Core endpoint or surface that returns PII / lets an operator search or look up a person's record (customers, affiliates, performers/broadcasters, admin users). Its first implementation is the universal-search customer lookup (`GET /customers`).

## The rule

A surface that exposes sensitive subject data must:

1. **Be permission-gated.** Every such route carries a `permissionKey` + `AdminFunctionAccessMiddleware` (never session-auth-only). The key is the same one that gates the corresponding page/scope, so page + search + direct API call share one authorization rule (defense-in-depth). *A read endpoint returning PII with only session auth is a finding, not an accident — see architecture-sweep ARCH-02 for how a gap like that slips the gate.*
2. **Audit-log the access.** Each access that returns subject PII writes an immutable audit record to the shared **`SYSTEMS.Pii_Access_Audits`** table (below) capturing the actor, the subject(s), the entity type, a reason, and the timestamp. The record is a **DB row, not a Monolog line** — an audit trail must be durable, queryable, and retained, which application logs are not (they rotate, aren't queryable-by-subject, and aren't retention-governed). Monolog remains for *operational* logging only.

Both halves are required: gating says *who may*, the audit says *who did*.

## The audit table — `SYSTEMS.Pii_Access_Audits`

A uBix Core-owned **event table** (`database.md` §6.5), in `SYSTEMS` alongside the other cross-cutting uBix Core tables (`Schema_Migrations`, `Feature_Flags`, `Code_Review_Runs`, `Release_Gate*`) — deliberately **not** in a legacy business DB, so the trail sits above all subject domains and evolves at uBix Core's pace, coupled to no single legacy team's release cadence.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED AUTO_INCREMENT` | Event-table PK — no capacity ceiling. |
| `admin_id` | `INT UNSIGNED NOT NULL` | The acting admin (always known — routes are permission-gated). Indexed. |
| `entity_type` | `ENUM('customer','affiliate','broadcaster','model')` `NOT NULL` | The subject domain — a discrete, indexable column (never a JSON/CSV blob, per §6.5). |
| `subject_id` | `INT UNSIGNED NOT NULL` | The accessed subject's id in its domain. Composite-indexed with `entity_type`. |
| `search_term` | `VARCHAR` | The term/params that produced the access. |
| `reason` | `VARCHAR` | Why (e.g. `universal-search-lookup`). |
| `date_created` | `DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP` | The access event time. |

- **Append-only / immutable** — rows are `INSERT`ed, never `UPDATE`d or `DELETE`d (except scheduled retention purge). No `date_last_updated` (documented §6.1 omission for an event table).
- **One row per returned subject.** A lookup that returns N subjects writes N rows, so *"who accessed subject Y"* is a plain indexed query (`WHERE entity_type = ? AND subject_id = ?`) — the query the legacy `BILLING.Search_Tracking` CSV-blob design cannot answer.
- **Indexes:** `idx_admin_id` (actor + time queries), `idx_entity_type_subject_id` (the subject query).
- **BI export + retention.** As an event table it flows to the BI ClickHouse export, where long-term retention lives. The uBix Core-side table is a **1-year rolling window** (monthly purge, env-overridable) — chosen 2026-07-28; a future compliance requirement can lengthen it via the retention flag without a schema change. The **BI/DW approver signs off** that it's on the export schedule.

## Relationship to legacy audit trails

Legacy customer search (`customers/search.php`) writes its own audit to `BILLING.Search_Tracking` — a **customer-only** table owned by the processing team, with a customer-shaped schema (CSV result blob, `mediumint` PK). That table is **left as-is** (no uBix Core migration on a legacy billing table; no dependency on the slowest-to-adopt team). uBix Core's own PII surfaces write to `Pii_Access_Audits` instead. During cutover, customer access therefore has two trails — the legacy *form* path (`Search_Tracking`) and the uBix Core path (`Pii_Access_Audits`) — which consolidates onto the uBix Core table if/when customer search goes native. Affiliate / broadcaster / model access had **no** trail before this standard; `Pii_Access_Audits` gives them one.

## Implementation seam

A shared writer (`PiiAccessAudit` service in `php/Ubix/`) is the single insert path — surfaces call it after returning results; they do not hand-roll the INSERT. `entity_type` + `subject_id` use domain DataTypes. New PII surfaces reuse this seam rather than adding a parallel audit.

## Checklist for a new PII surface

- [ ] Route is `permissionKey`-gated + `AdminFunctionAccessMiddleware` (not session-auth-only).
- [ ] Every access that returns subject PII writes `Pii_Access_Audits` rows (one per subject) via the shared writer.
- [ ] `entity_type` is one of the enum values; add a value only via an additive schema change.
- [ ] Retention window for the surface is documented; BI/DW sign-off obtained (event-table export).
