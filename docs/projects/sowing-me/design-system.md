# Sowing.me — Design System: source of truth & sync record

**Status:** Active · v0.1 · 2026-08-28 · Owner: Christopher W. Olsen
**Contract:** [`docs/standards/design-system-handoff.md`](../../standards/design-system-handoff.md) — Claude Design owns how it looks; Claude Code owns how it runs; neither edits the other's artifacts in place.

Design for Sowing.me's front-ends is authored in **Claude Design** (claude.ai/design) and consumed by **Claude Code** in this repo via **`/design-sync`**. This file is the per-project record the standard's §10 requires: which Claude Design project is the source of truth, the project→codebase mapping, the token-name map, and the deviations.

## 1. Source-of-truth projects (claude.ai/design)

| Project | Project ID | Type | Role |
|---|---|---|---|
| **Sowing.me Design System** | `6be3caa9-f875-4ed5-9aa7-66b4b605751d` | design-system | **Canonical** — tokens (`theme.css`), embedded fonts, component preview cards. Everything pulls tokens from here first. |
| **Sowing.me — Marketing Site** | `d8091133-aef3-4db4-a6d2-57ed86c79e29` | design-system\* | Screen compositions for the marketing site. |
| **Sowing.me — Creator Portal** (Product App) | `ee6e50b1-64d6-4d54-b231-5e49c607f9e1` | design-system\* | Screen compositions for the product app (creators **and** members — one app). |

Operator: Christopher W. Olsen (project owner; `/design-login` grants a session access).
\* The two app projects are **mis-typed** as design-system — see §5.

## 2. Project → codebase mapping (the sync target)

`/design-sync` does not infer the target — it is chosen **by project ID**, and this table is how any session knows which is which. A sync run targets **one** project → **one** codebase.

| Claude Design project | → Codebase | Consumes |
|---|---|---|
| Sowing.me Design System (`6be3caa9…`) | `js/Ubix/src/lib/` (shared) + each app's `src/lib/` | tokens + components (the source both apps inherit) |
| Sowing.me — Marketing Site (`d8091133…`) | **`SowingMeWeb`** (`templates/sowing-me-web-v1/`) | marketing page compositions |
| Sowing.me — Creator Portal (`ee6e50b1…`) | **`SowingMeJs`** (`app/SowingMeJs/`) | product-app screen compositions |

`SowingMeJs` is a single SvelteKit app serving both creators and members (role-gated) — the "Creator Portal" name is narrow; it feeds the whole product app.

## 3. Design brief handed over

The design brief lives in each project's `prompt.md`: Sowing.me = a membership platform for Christian creators, green + blue-grey identity, **extract from the live UI, don't reinvent** (handoff §2). Direction: green primary (`#2E7D32`/`#1B5E20`), blue secondary (`#1565C0`), orange accent (`#FF8F00`), Inter (headings) / Open Sans (body). Tokens extracted from `templates/sowing-me-web-v1/layout.latte` and `app/SowingMeJs/THEME_GUIDE.md`.

## 4. Token-name mapping table (§8 / §10)

The design system authors tokens under the `@theme` convention (`--color-*`, `--font-*`, …). The two live front-ends already ship their own token names; **neither side renames the other — we map.** On import, keep each app's existing var **names** and set their **values** from the design-system tokens (a thin adapter), rather than find/replacing every consumer (handoff §5).

**SowingMeWeb (`templates/sowing-me-web-v1/layout.latte`):**
| Live token | Value | Design-system token |
|---|---|---|
| `--color-primary` | #2E7D32 | `--color-brand-600` / `--color-primary` |
| `--color-primary-dark` | #1B5E20 | `--color-brand-800` / `--color-primary-hover` |
| `--color-secondary` | #1565C0 | `--color-blue-700` / `--color-secondary` |
| `--color-accent` | #FF8F00 | `--color-accent-500` / `--color-accent` |
| `--color-dark` | #263238 | `--color-slate-900` / `--color-text` |
| `--color-light` | #F5F5F5 | `--color-surface-2` |
| `--font-heading` / `--font-body` | Inter / Open Sans | `--font-heading` / `--font-body` |

**SowingMeJs (`app/SowingMeJs/THEME_GUIDE.md`):**
| Live CSS var | Design-system token |
|---|---|
| `--color-bg-primary` / `--color-bg-secondary` / `--color-bg-tertiary` | `--color-surface` / `--color-surface-2` / `--color-slate-100` |
| `--color-text-primary` / `-secondary` / `-tertiary` | `--color-text` / `--color-text-muted` / `--color-text-subtle` |
| `--color-border-light` | `--color-border` |
| `--color-accent-primary` / `-hover` / `-light` | `--color-primary` / `--color-primary-hover` / `--color-primary-soft` |
| `--shadow-sm` / `-md` / `-lg` | `--shadow-sm` / `--shadow-card` / `--shadow-lg` |

## 5. Deviations & notes (§9)

- **The two app projects are design-system-TYPE, not regular design projects.** Cause: the design-sync tooling's `create_project` only creates design-system-type projects, and type is immutable at creation. Consequence: they appear under "Design Systems" in claude.ai/design. This is **cosmetic** — they function as consuming app projects; the sync targets them by ID regardless of type. Accepted (2026-08-28). To fix, recreate them as regular projects in the UI and re-push.
- **Tokens are copied, not linked.** Claude Design projects are self-contained (handoff §4 — no runtime cross-project references), so each app project holds its **own copy** of `theme.css` + fonts. A change in the Design System does **not** propagate automatically; the copies are re-synced on each push. Drift risk is owned here: whoever pushes keeps the app-project token copies aligned with the canonical system.

## 6. Shared vocabularies (§7)

None frozen yet. Candidates to register if they cross the design↔code↔wire boundary: tier names, post `visibility` values, faith-topic/category slugs. Register in [`docs/standards/vocabulary.md`](../../standards/vocabulary.md) when one does.

## 7. Sync mechanics (when pulling into code)

Run `/design-sync <project-id>` (user-invoked). On import: **tokens → primitives → composites → screens**; shared primitives land in `js/Ubix/src/lib/` (exported from `index.js`), app-specific in the app's `src/lib/`; tokens/assets port near-verbatim, components re-authored in Svelte 5 runes (`complete-js-guide.md`); **never rename or re-value imported tokens locally** (fix upstream, re-sync); then `php bin/ubix code:review` to zero + a test per component + MR into `dev`. The importing MR names its baseline (which project + components/tokens) so the next import knows where it left off.

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-08-28 | Initial record — 3 Claude Design projects created + seeded; project→codebase mapping; token map; type-mislabel + token-copy deviations. |
