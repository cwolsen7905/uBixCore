# uBix Core — Documentation

> `architecture/` and `standards/` are the framework-level docs; app-specific docs (`api-contracts/`, `surfaces/`, `data-models/`) were **not** ported because the original apps themselves are not in this repo.

All documentation lives under `docs/` organised by intent. Pick the subfolder that matches what you're looking for.

## Folders

### `architecture/` — Framework references (long-lived)

Read these to understand how the platform is built. Stable; rarely change.

- [`complete-js-guide.md`](architecture/complete-js-guide.md) — full SvelteKit / Svelte 5 / Vite / shared-library guide (routes, runes, load functions, hooks, server helpers, SSE endpoints, testing, deployment)
- [`complete-php-guide.md`](architecture/complete-php-guide.md) — full PHP / Slim 4 / PHP-DI / DataType / Payload / repository guide
- [`complete-py-guide.md`](architecture/complete-py-guide.md) — full Python `*Py` guide (the `py/Ubix` shared framework + `@ubixsys/ubixcore` namespace, FastAPI factory, app structure, packaging, Redis seam)
- [`models-and-datatypes.md`](architecture/models-and-datatypes.md) — DataType + Model pattern reference
- [`monorepo.md`](architecture/monorepo.md) — repository shape, npm workspaces, git workflow, release process
- [`feature-test-system.md`](architecture/feature-test-system.md) — Feature Tests system planning doc

### `standards/` — Enforced rules

Reviewed-and-approved coding / testing / quality standards. PRs that don't meet these get fixed before merging.

- [`ai-coding-guidelines.md`](standards/ai-coding-guidelines.md) — AI-assisted development guidelines (Copilot + chat AI usage, per-level expectations from Engineer I → III)
- [`database.md`](standards/database.md) — DB conventions (table names, column types, FK patterns)
- [`go-coding-guidelines.md`](standards/go-coding-guidelines.md) — Go `*Go` conventions (gofumpt, golangci-lint, cmd/+internal layout)
- [`js-code-review.md`](standards/js-code-review.md) — JS machine code review suite (Knip, CSpell, Prettier)
- [`memcache-keys.md`](standards/memcache-keys.md) — Memcache key namespacing conventions
- [`migrations.md`](standards/migrations.md) — schema migration filename format, master `SYSTEMS.Schema_Migrations` tracker, `bin/ubix migrate:*` runner, schema-vs-seed split, drift detection
- [`pagination.md`](standards/pagination.md) — the two sanctioned pagination patterns (offset/page-based for admin tables, cursor/keyset for infinite-scroll feeds), their decision rule, wire contracts, and the MCR rule that enforces them
- [`peck-setup.md`](standards/peck-setup.md) — PHP spell checker setup
- [`py-coding-guidelines.md`](standards/py-coding-guidelines.md) — Python `*Py` conventions (ruff, mypy strict, pytest, src layout, the stub-less-dep override idiom)
- [`sensitive-data-access.md`](standards/sensitive-data-access.md) — PII / sensitive-subject access must be permission-gated + audit-logged to `SYSTEMS.Pii_Access_Audits` (the uBix Core-owned event-table audit seam); covers customers/affiliates/broadcasters/models
- [`unit-testing.md`](standards/unit-testing.md) — testing conventions (PHPUnit + vitest + pytest)
- [`vocabulary.md`](standards/vocabulary.md) — cross-language terminology: which word a domain concept gets, and how that word is spelled in PHP / SQL / JS / CSS / on a frozen wire field. Status: **Proposed**.
- [`web-development-delivery-framework.md`](standards/web-development-delivery-framework.md) — department-wide SDLC framework (Initiating / Prioritizing / Planning / Executing / Transitioning). Defines Charter / SRS / SDD / TDD / Test Plan / Task structures that `docs/projects/` folders are authored against. Status: **DRAFT**.

### `projects/` — Per-initiative living docs

Each project folder contains the brief, charter, plans, and rolling status for one initiative. **[`projects/README.md`](projects/README.md) is the status index** — one row per project (Proposed / Active / Paused / Complete / Superseded), each row linking the project's entry point — plus the per-project convention (folder shape, `**Status:**` line, and the deterministic triggers that require full tracking with a `status.md` + work matrix). Don't duplicate the project list here; the index owns it.

## Adding new documentation

- **Architecture or standards changes** — long-lived references go in `architecture/` or `standards/`. PR review applies.
- **A new project** — make `projects/<project-slug>/` and put the brief / charter / status there. The project's own `README.md` is the entry point, and it gets a row in the [`projects/README.md`](projects/README.md) status index in the same commit (the index documents the full convention, including when a `status.md` + work matrix become mandatory).

## Filename convention

All filenames are **kebab-case** (lowercase, hyphen-separated). Per-surface folders use plain `srs.md` / `technical-spec.md` since the folder name carries the disambiguator.

## Cross-references

Use **root-relative paths** in cross-references (`docs/architecture/complete-php-guide.md`, not `../architecture/complete-php-guide.md`). Easier to grep, easier to refactor, less brittle than relative paths.
