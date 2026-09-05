# Design-System Handoff — the Claude Design ↔ Claude Code contract

**Version:** 1.3
**Date:** 2026-08-21
**Status:** Active. First worked example: [`docs/projects/performer-app-redesign/`](../projects/performer-app-redesign/README.md).

Design for uBix Core front-ends is authored in **Claude Design** (a design-system project on claude.ai/design) and consumed by **Claude Code** working in this monorepo. The two tools are operated by different people, see different files, and are each happy to generate the other's work if nobody stops them. This document is the boundary.

**The one-sentence rule:** _Claude Design owns how it looks; Claude Code owns how it runs; neither edits the other's artifacts in place._

---

## 1. Why a contract

Three failure modes justify it, all cheap to prevent and expensive to unwind:

1. **Silent re-authoring.** Claude Code fixes a token name or tweaks a variant during import. The next sync reverts it, or worse, doesn't — and the repo and the design system disagree with nobody noticing.
2. **Boundary spill.** Claude Design, asked how to use its output, helpfully emits install steps, build config, or framework code. A well-meaning operator follows them, and code lands in the repo that never passed a uBix Core gate.
3. **Shared vocabularies drifting.** Some visual names are load-bearing protocol values. PA's `font_class` (`gift_50`, `adminCritical`, `vsMonitor`, …) arrives on the wire from the Performer Endpoint; renaming one on either side breaks rendering with no error anywhere. See `docs/projects/performer-app-redesign/pep-protocol-recon.md` §4.

## 2. Direction of authority — extract before you author

**Where a design already exists, that design is canonical.** The design system is *extracted from it*, not re-derived alongside it. This is the failure that motivated the rule: a brief written from engineering recon told Claude Design to build screens from a functional spec, so it authored a fresh page that dropped components the existing design already had — correct compliance with a wrongly-framed instruction.

The extraction sequence:

1. **Inventory** every component in the existing design and publish the list before extracting.
2. **Extract, don't redesign** — reproduce exactly what the design shows. A component recurring with differences becomes **variants**; normalizing them away is a design decision nobody asked for.
3. **Derive tokens from real values** in the design — never re-pick them.
4. **Then** compare against the functional spec and **report gaps as a list**. Gaps are team decisions, not prompts to invent UI.

A functional spec is a **checklist to report against**, never a licence to author screens. Specs extracted from a prior implementation describe a *behavior floor*: an existing design is often ahead of them, and where the two disagree, the design wins on what a screen is while the spec and any protocol reference win on what the system requires.

Authoring net-new screens is the exception, and only on explicit request.

## 3. Ownership

| Artifact | Owner | The other side may… |
|---|---|---|
| Design tokens (names **and** values) | Claude Design | import verbatim; never rename or re-value in the repo |
| Component visual contract — variants, states, prop names in `.d.ts` | Claude Design | implement it; propose changes upstream |
| Iconography, brand assets | Claude Design | import; re-encode/optimize; never redraw |
| Preview cards, guideline pages, `prompt.md` usage docs | Claude Design | read as the spec |
| Framework implementation (Svelte components, runes state) | Claude Code | — Claude Design never authors it |
| Data fetching, routing, auth, realtime transport | Claude Code | — |
| Tests, build config, CI, lint/gate config | Claude Code | — |
| Functional specs (what the screens must do) | Engineering/product | Claude Design checks its extraction against them and **reports gaps**; it neither invents screens from them nor treats them as overriding an existing design (§2) |

**Reference implementations** (the `.jsx` files) are a *specification medium*, not shippable code. They are read, then re-authored in the app's framework. They are never imported, transpiled, or vendored.

## 4. What Claude Design must not do

- Emit repo-integration or installation instructions, framework code for our apps, or build/CI configuration. If asked how engineering consumes the system: _"engineering pulls this project via design-sync and handles the rest."_
- Reference anything external at runtime — CDN fonts, scripts, images, or remote stylesheets. Assets live in the project.
- Author net-new screens or content unless explicitly asked. Where a design already exists, extract from it (§2); where neither a design nor a spec covers something, that is a question to ask, not a gap to fill.
- Rename or restructure a **shared vocabulary** (see §7) unilaterally.

## 5. What Claude Code must not do

- **Edit imported tokens in the repo.** A wrong name or value is fixed upstream in the design project, then re-synced. Local edits are silently reverted by the next sync — this is the single most common way a design system rots.
- **Rename variants or props** to suit local convention. If `secondary` should be `subtle`, that's an upstream change.
- **Redraw or restyle** an imported asset to "match better." Report the mismatch.
- **Vendor the `.jsx` reference implementations** or add a React dependency to make them run.
- **Skip the gate for design-shaped work.** Imported code is code: `php bin/ubix code:review` to zero, a test per component, MR into `dev` with review threads worked. Design provenance earns no exemption.

## 6. Import mechanics

1. **Access.** The design-sync tooling reads through the *operator's* claude.ai login. Whoever runs the import needs the project shared with edit access — the project owner importing their own work needs no sharing step. Project type is fixed at creation: a regular design project can never become a design system.
   **A regular-type project is still readable, and this distinction has been misread before.** Project *listing* returns only design-system-type projects, so a regular project is invisible there — but fetching its structure and file contents by **direct project ID** works normally, which is how the PA redesign shipped. What a regular project loses is discoverability and its standing as a canonical source of truth, **not access**. So: design in a regular project freely; promote anything meant for reuse into a design-system-type project, because that is the only kind a future import can find on its own.
2. **Read before write.** Enumerate the project structure first; fetch file contents only for what's being imported this slice.
3. **Order.** Tokens → primitives → composites → screens. Tokens first is not stylistic: everything downstream references them.
4. **Placement.** Genuinely shared primitives go in `js/Ubix/src/lib/` and are exported from its `index.js` (the reuse-at-seams rule); app-specific components go in that app's `src/lib/`.
5. **Port, don't copy.** Tokens and raster/vector assets land near-verbatim. Everything else is re-authored per `docs/architecture/complete-js-guide.md` — Svelte 5 runes, our naming, our test conventions.
6. **Record the sync.** The importing MR states which design-system project and which components/tokens it covers, so the next import knows the baseline.

## 7. Shared vocabularies

Some names cross the boundary and belong to **neither** side alone — typically because a third system (a wire protocol, a backend enum, a legacy CSS contract) also uses them.

Rule: a shared vocabulary is **named in the project's docs**, and changing it takes a decision recorded there, not a unilateral edit on either side. Map protocol values to design variants explicitly (a lookup in the app layer) rather than assuming the two name spaces will stay identical — that indirection is what lets either side rename safely later.

The monorepo-wide register of these terms — and the per-layer spelling transforms (PHP / SQL / JS / CSS / `data-*` / frozen wire) that keep a name derivable rather than negotiated per file — is [`vocabulary.md`](vocabulary.md). Where a name crosses the design↔code boundary *and* another layer, register it there; this section governs the boundary itself.

## 8. Token-naming convention

**For a design system we are authoring:** tokens are authored as CSS custom properties in **Tailwind v4 `@theme` namespaces** — `--color-*`, `--font-*`, `--text-*`, `--spacing-*`, `--radius-*`, `--shadow-*` — with conventional scales (color steps 50–950, numeric spacing). This is what makes the import mechanical rather than a translation pass.

**Where an existing system diverges, we map rather than rename.** This paragraph previously claimed the convention above "applies to every uBix Core design system" — it does not, and asserting it did not make it so. The organisation-wide `Flirt4Free Design System` names tokens as prefixed custom properties (`--f4f-*`) plus semantic aliases, and renaming them upstream would break every existing consumer for the convenience of one importer. So the rule is:

- **A new system** follows the `@theme` convention. There is no reason not to, and it removes a translation step permanently.
- **An existing system that does not** keeps its names. The **importing MR carries an explicit mapping table** from the system's token names to our `@theme` names, recorded in the consuming project's docs. This is §7's rule — map explicitly across a boundary rather than assuming two namespaces stay identical — applied one layer up, from single names to a whole namespace.
- **Neither side renames the other's tokens to close the gap.** That is §3 and §5, and it holds here.

The mapping is engineering-owned: the design side is not asked to emit it (§4).

## 9. Drift

Today drift is caught by eye. Two mitigations, in order of cost:

- **Now:** the importing MR names its baseline (§6.6), and any repo-side deviation from the design system is recorded in the project's `status.md` with the reason. An undocumented deviation is a review finding.
- **Proposed:** a `code:review` check comparing imported token names/values against the last synced manifest, making drift a gate failure. This is a **gate-toolset change and needs Christopher Olsen's prior sign-off** before anyone builds it (see `docs/standards/code-review.md` §1).

## 10. Per-project obligations

Each project consuming a design system records, in its `docs/projects/<slug>/`:

- which Claude Design project is its source of truth, and who operates it;
- the design brief handed over (so the boundary is in the prompt, not just this doc);
- its shared vocabularies (§7);
- the **token-name mapping table**, where the system's token naming diverges from §8's convention. This is a required artifact, not a deviation — the system is compliant and the mapping is ours to hold — so it needs its own home in the project's docs rather than a row in the deviations table;
- deviations and their reasons (§9).

A project brief may **narrow** this contract but never loosen it; where a brief and this document disagree, this document wins.

---

## Version history

| Version | Date | Change |
|---|---|---|
| 1.0 | 2026-07-31 | Initial standard. Extracted from the Performer App redesign handoff, which was the first Claude Design → Claude Code project. |
| 1.1 | 2026-08-01 | Added §2 *Direction of authority — extract before you author*, after a brief framed as "build from the spec" produced a fresh page that dropped components an existing canonical design already had. Reconciled the ownership table's *Functional specs* row and the "must not invent" rule with §2 — both previously read as spec-over-design. |
| 1.2 | 2026-08-21 | Corrected §8, which asserted the Tailwind `@theme` convention "applies to every uBix Core design system" — the organisation-wide `Flirt4Free Design System` does not use it, so the standard's only design-system-type instance contradicted it. §8 now governs systems we author, and requires an explicit **mapping table in the importing MR** where an existing system diverges — §7's rule applied to a whole namespace. Clarified §6.1: a regular-type project is invisible to *listing* but readable by **direct project ID**; what it loses is discoverability and canonical standing, not access. Both found while preparing the Internal Admin 2.0 hand-over — see [`docs/audits/2026-08-design-system-review.md`](../audits/2026-08-design-system-review.md). |
| 1.3 | 2026-08-21 | §10 gained the **token-name mapping table** as a recorded per-project obligation. v1.2 made that table mandatory in §8 but left §10 — the canonical list of what a consuming project's docs must record — unchanged, so a project working §10 as a checklist had no prompt to record it, and it is not covered by any existing entry: explicitly not a deviation (the diverging system is compliant), and not part of the brief. Caught by the local AI review against the v1.2 diff before it shipped. |
