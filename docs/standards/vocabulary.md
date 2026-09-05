<!--
	This document quotes deliberate misspellings and banned synonyms as
	counter-examples (§2, §7, §9). They are intentional and must stay wrong,
	so they are ignored here inline rather than added to cspell.json — a
	dictionary entry would make the misspelling acceptable everywhere.
-->
<!-- cspell:ignore whispr lintable -->

# Cross-Language Vocabulary Conventions

**Status:** Proposed — pending Christopher W. Olsen's sign-off
**Audience:** VS Media Development Department
**Last Updated:** 2026-08-21

This document is the single authority for **what a domain concept is called** as it crosses uBix Core's language boundaries — PHP, JS/Svelte, CSS, SQL, and the legacy wire protocols. It exists so that one concept has one name, and so that name's spelling in each layer is *derivable* rather than *negotiated per file*.

It answers two different questions, and the split matters:

- **Which word is it?** (`whisper`, not `dm` / `pm` / `privateMessage`) — a **choice**. Choices need a registry: §5.
- **How is that word spelled in this layer?** (`whisper` → `--color-whisper-off` → `data-whisper="off"`) — **mechanical**. Mechanics need rules: §4 and §7.

Naming conventions alone cannot solve the first question; a glossary alone cannot solve the second, because nobody opens a table to decide between `whisperOffBg` and `whisperBgOff`. Both halves are required.

---

## 1. Scope

**Applies to** every name for a domain concept that crosses **two or more** of these layers:

- a PHP class, DataType, DTO, or service name (`php/Ubix/**`, `app/*Api/**`)
- a database column or table (`sql/**`)
- a wire field on a legacy protocol (chat-manager socket, PEP, Performer Endpoint)
- a JS/Svelte identifier, prop, or `@typedef` union member (`app/*Js/**`, `js/Ubix/**`)
- a CSS custom property / Tailwind `@theme` token, or a `data-*` state attribute
- product copy, specs, and conversation

**Does NOT apply to:**

- **Purely local identifiers.** A loop variable, a private helper, a single-file `const` — those stay under normal per-language conventions (`docs/architecture/complete-js-guide.md`, `complete-php-guide.md`, `docs/standards/py-coding-guidelines.md`, `go-coding-guidelines.md`). One layer, one file, no coordination cost, no entry here.
- **Legacy spellings uBix Core cannot change.** Those are *recorded* as frozen (§6), never renamed. Same principle as `docs/standards/memcache-keys.md` §1 on legacy keys.
- **Vocabularies another doc already owns.** This document points at them; it does not restate them (§10).

---

## 2. Why this exists — three live divergences

Not hypothetical. All three were verified in the tree on 2026-08-11.

**1. One term, three spellings, no mapping recorded.** `screen_name` (DB column — `FanClubComment`, `FanClubPostUnlock`), `screenName` (PHP model getters / bound params), and `screenname` (JS — `roomView.svelte.js`'s `LiveMessage` typedef and its three read sites). Each is locally idiomatic. Nothing declares them the same concept, so nothing catches a fourth.

**2. Two apps forked the same row-kind vocabulary.** `app/PerformerApplicationJs`'s `RoomRowKind` is `'tip' | 'model' | 'whisper' | 'customer'`. `app/ProductJs`'s chat tokens are `--color-chat-{welcome,model,tip,admin,promo,guest,group}` + `--color-chat-lovense-{from,to}`. They agree on exactly **two** members (`model`, `tip`). There is no `--color-chat-whisper` and no `--color-chat-customer`; `admin`, `promo`, `guest`, `group`, `welcome`, and `lovense` have no `RoomRowKind`. Two surfaces of one product, already unable to share a component.

**3. Classification by substring on a frozen wire value.** `roomView.svelte.js:44` does `fontClass.includes('whisper')` against the PEP `font_class` field. Rename either side and rendering silently changes with no error anywhere — the hazard `docs/standards/design-system-handoff.md` §7 warns about, in production code.

The repo already had **three partial answers**, none load-bearing:

| Existing | What it gives | Why it isn't enough |
|---|---|---|
| `docs/architecture/monorepo.md` § Domain vocabulary mapping | The right table shape | One row (`live model` → `LiveCam`); scoped to reserved-token collisions only |
| `docs/standards/design-system-handoff.md` §7 | The correct rule, stated | Names no actual vocabulary and enforces nothing |
| `cspell.json` / `peck.json` | Blocks misspellings | **Spelling-acceptance, not term-authority** — it rejects `whispr` and passes `directMessage` clean |

---

## 3. The two halves

| | Owned by | Size | Enforcement |
|---|---|---|---|
| **Nouns** — which concepts exist and what each is called | the glossary (§5) | small, curated, entry-gated | banned-synonym check (§9) |
| **Composition** — how a noun becomes an identifier in a layer | the rules (§4, §7) | one transform table + one closed suffix set | derivable → lintable |

---

## 4. Layer transforms — one term, N spellings

Do **not** force every layer to use the same identifier. Force each layer to use a **mechanical transform of the same term**. Given the term `whisper`:

| Layer | Transform | Result |
|---|---|---|
| PHP class / DTO / service | `PascalCase` | `Whisper`, `WhisperMessage`, `WhisperDto` |
| PHP DataType pair | `PascalCase`, Nullable-parent + concrete child | `NullableWhisperText` / `WhisperText` |
| DB column | `snake_case` | `whisper_sent_at` |
| PHP variable / bound param | `camelCase` | `$whisperText`, `:whisperText` |
| JS identifier / prop | `camelCase` | `whisper`, `whispersOff` |
| JS union member (`@typedef`) | `lowercase` | `'whisper'` |
| CSS token | `kebab-case` in a Tailwind `@theme` namespace | `--color-whisper-off` |
| DOM state | `data-<term>="<state>"` | `data-whisper="off"` |
| Wire field | **frozen — see §6** | `font_class` contains `whisper` |

The transform is the deterministic part. Once the term is in §5, every one of these is derivable without a decision, and a reviewer can check each mechanically.

**Two rules that keep transforms honest:**

- **No abbreviations of a glossary term, ever.** `wOff` for `whispersOff` breaks the chain — the transform is no longer reversible to the term, which defeats the point.
- **Never assume two namespaces will stay identical.** Where a frozen wire value feeds a design variant, map it **explicitly** in the app layer (a lookup), per `design-system-handoff.md` §7. `fontClass.includes('whisper')` is that mapping done implicitly, and it is the fragile form.

---

## 5. Glossary

### 5.1 Entry criterion

A term belongs here **iff it appears in ≥2 of the layers listed in §1**. One-layer terms stay out. This is deterministic on purpose: it is what stops the table becoming a 400-row unmaintained inventory nobody reads.

### 5.2 Chat room

Spellings below are **as-found in the tree on 2026-08-11**, including the inconsistent ones. A ⚠️ marks a divergence to be reconciled, not a convention to copy.

| Term | PHP | DB / wire | JS | CSS token |
|---|---|---|---|---|
| **whisper** | — | `font_class` substring (frozen) | `'whisper'` (`RoomRowKind`) | ⚠️ none — gap |
| **tip** | `ChatTipRequestDto`, `TipMenuItemDto`, `TipChatNotifierService` | — | `'tip'`, `TipPanel.svelte` | `--color-chat-tip` |
| **model** | `LiveCam*` (see §5.4) | — | `'model'` | `--color-chat-model` |
| **screenname** | `screenName` | `screen_name` | `screenname` | — |
| **guest** | customer type `8` | `<user type="8">` | ⚠️ `'customer'` (`RoomRowKind`) | `--color-chat-guest` |
| **group** (group pledge) | — | — | — | `--color-chat-group` |
| **lovense** | — | — | — | `--color-chat-lovense-{from,to}` |
| **credits** | `UsdCurrency` where monetary | — | `credits` (`LiveMessage`) | — |
| **room** | `ChatRoomDto`, `Service/ChatRoom/**` | — | `roomView`, `RoomCurtain` | — |
| **curtain** | — | — | `RoomCurtain.svelte` | — |

### 5.3 Known reconciliations (open)

- **`screenname` / `screenName` / `screen_name`** — pick `screenname` as the term (it is already the `cspell.json` dictionary entry) and let §4 generate the per-layer spelling. The DB column is frozen; the JS and PHP spellings are not.
- **`--color-chat-whisper` / `--color-chat-customer` do not exist** while `RoomRowKind` has both members — either the tokens are missing or the union has dead members. Resolve before any chat-row component is shared between ProductJs and PerformerApplicationJs.
- **`RoomRowKind: 'customer'` vs the customer-type taxonomy** — `'customer'` is the PA row kind's catch-all fallback, not a tier. It reads as a peer of `guest`/`basic`/`premium`/`vip` and is not one. Rename or document.

### 5.4 Reserved-token synonyms

`monorepo.md` § Domain vocabulary mapping remains authoritative: product **"live model"** is code **`LiveCam`**, because `Model` is reserved for `Ubix\Model\*`. Note the scope carefully — that mapping binds **PHP class names**. The CSS token `--color-chat-model` and the JS union member `'model'` are *correct as-is*; neither is a class name. Do not "fix" them.

---

## 6. Frozen wire spellings

Some spellings are fixed by a legacy protocol or a shipped DB column and **cannot be renamed by uBix Core**. They are recorded as facts, with the mapping made explicit at the boundary:

| Frozen spelling | Owner | uBix Core-side term |
|---|---|---|
| `font_class` (values `gift_50`, `adminCritical`, `vsMonitor`, …) | PEP wire — see `docs/projects/performer-app-redesign/pep-protocol-recon.md` §4 | classified into `RoomRowKind` |
| `<user type="4\|6\|7\|8">` | chat-manager socket — `chat-manager-protocol.md` §6 owns this taxonomy | `basic` / `premium` / `vip` / `guest` |
| `screen_name` | shipped DB columns | `screenname` |

Adding a row here is **not** a rename proposal. It is a note that the boundary needs an explicit lookup.

---

## 7. Composition — the closed facet set

For names derived from a glossary term, the suffix comes from this **closed** set. Anything outside it is a review finding.

| Facet | Suffixes | Example |
|---|---|---|
| **state** | `Off` `On` `Active` `Disabled` | `whispersOff` |
| **copy** | `Label` `Hint` `Tooltip` `Error` | `whisperLabel`, `whisperToggleLabel` |
| **handler** | `on<Term>` | `onWhisper` |
| **style** | `Fg` `Bg` `Border` — **only** where genuinely dynamic (§8) | `whisperFg` |

Rejected by this rule, with the reason:

- `whisperDeco` — `Deco` is not a facet; `text-decoration` is CSS's job (§8).
- `whisperCursor`, `whisperColor` — same; presentation, not state or copy.
- `whisperTitle` — `title` is an HTML attribute, not a semantic facet. Use `Label`.
- `whisperOffFor` — meaning unrecoverable without reading the template.
- `wOff` — abbreviation of a glossary term (§4).

Note what the facet set does for the pair `whisperLabel` / `whisperToggleLabel`: it forces the distinction between the row's label and the button's label to be *in the name*. `whisperTitle` / `whisperToggleTitle` leaves a reader guessing which is which.

---

## 8. Shrink the surface before you name it

**The cheapest way to keep terminology consistent across three languages is to reduce how many names have to cross the boundary at all.** Naming discipline scales badly; layering discipline scales well.

The chat-room port produced this shape — seven names for one boolean, presentation computed in JS, raw un-tokened colour values:

```js
// ❌ seven names cross the JS→CSS boundary; two of these values are un-tokened
whisperOffFor:   wOff,
whisperColor:    wOff ? 'var(--color-muted-2)' : 'var(--color-text-2)',
whisperDeco:     wOff ? 'line-through' : 'none',
whisperCursor:   wOff ? 'default' : 'pointer',
whisperTitle:    wOff ? t.whispersOffTitle : t.whisper,
whisperOffBg:    wOff ? 'rgba(255,84,104,0.16)' : 'var(--color-panel-2)',
whisperOffColor: wOff ? '#ff9daa' : 'var(--color-muted)',
```

Three defects before naming enters it: JS is computing `color` / `text-decoration` / `cursor`, which CSS owns; `#ff9daa` and `rgba(255,84,104,0.16)` are un-tokened (a `design-system-handoff.md` §9 drift finding on its own, and invisible to theming); and `wOff` / `whisperOffFor` are unreadable.

```js
// ✅ JS owns state + copy. Two names cross.
const whispersOff = …;
const whisperLabel       = whispersOff ? t.whispersOffLabel : t.whisperLabel;
const whisperToggleLabel = whispersOff ? t.whispersOnAsk    : t.whispersOffAction;
```

```svelte
<button data-whisper={whispersOff ? 'off' : 'on'} aria-label={whisperToggleLabel}>
```

```css
[data-whisper='off'] {
	color: var(--color-chat-whisper-off);
	background: var(--color-chat-whisper-off-bg);
	text-decoration: line-through;
	cursor: default;
}
```

**Seven names became two.** The rule: **JS owns state and copy; CSS owns presentation; the `data-*` attribute is the contract between them.** A style facet in JS (§7) is legitimate only when the value is genuinely dynamic — computed from server data, not from a boolean a CSS selector could read.

---

## 9. Enforcement

In cost order. Tiers 1 is in force with this document; tiers 2 and 3 are **proposals**.

**Tier 1 — now, free.** This document is the authority; §10 makes the three existing partial answers link here instead of each holding a fragment. A name that contradicts §4, §5, or §7 is an **MR review finding**, worked under the `Fixed:` / `Dismissed:` / `Deferred:` disposition convention (`docs/standards/code-review.md`).

**Tier 2 — proposed: a `vocabulary` check in `code:review`.** Two deterministic, language-agnostic greps: a banned-synonym list (`\bdm\b`, `privateMessage`, `pm_`, …) derived from §5, and facet-suffix validation on identifiers built from a glossary term. Cheap and fast — but it is a **gate-toolset change and requires Christopher Olsen's prior sign-off** before anyone builds it (`CLAUDE.md` § Machine Code Review; `docs/standards/code-review.md` §1). Not built.

**Tier 3 — proposed, only once §5 is stable.** Promote the glossary from documentation to **generator**: one `vocabulary.json`, and a `ubix vocab:generate` that emits the PHP enum, the JS const map, and the CSS token names. Drift then becomes structurally impossible rather than policed. **Hold this until the table has been stable for a quarter** — codegen over a vocabulary still in flux is worse than a doc.

**Note on cspell — and a trap worth knowing.** This file **is** spell-checked by the gate, which scans `docs/**/*.md` wholesale (`MachineCodeReviewService::CSPELL_DEFAULT_TARGETS`). Its deliberate counter-example misspellings therefore carry an **inline `cspell:ignore`** at the top of the file rather than `cspell.json` entries — a dictionary entry would make the misspelling acceptable repo-wide, which is the opposite of the intent.

The trap: `package.json`'s `review:spell` script — the JS-only fallback documented in `docs/standards/js-code-review.md` for environments without the PHP CLI — checks an **explicit per-file allowlist** of docs, not `docs/**/*.md`. So the two invocations disagree, and **a new doc can pass `npm run review:spell` clean and still red the canonical gate.** This document was written that way and had to be fixed. Reconciling the two target lists is a per-tool configuration change requiring Christopher Olsen's sign-off (`CLAUDE.md` § Machine Code Review); flagged, not changed. Until then, the canonical command is the only trustworthy check — as `CLAUDE.md` already says.

---

## 10. Relationship to existing docs

This document is the authority for cross-language terminology. The following are **narrower** and remain authoritative within their scope — this file points at them rather than restating them:

| Doc | Owns |
|---|---|
| `docs/surfaces/chat-room/chat-manager-protocol.md` §6 | the customer-type taxonomy (`4`/`6`/`7`/`8` → basic/premium/vip/guest) |
| `docs/architecture/monorepo.md` § Domain vocabulary mapping | reserved-class-name-token synonyms (`live model` → `LiveCam`) |
| `docs/standards/design-system-handoff.md` §7–§8 | the design↔code boundary, and the Tailwind `@theme` token namespaces **for systems we author** — an existing system that names tokens otherwise is mapped in the importing MR, not renamed (§8, v1.2) |
| `docs/projects/performer-app-redesign/pep-protocol-recon.md` §4 | the PEP `font_class` value space |
| `cspell.json` / `peck.json` | accepted *spellings* (a term's presence there is not authority for its *use*) |

Where this document and a narrower one disagree, **the narrower one wins within its scope** — and the disagreement is itself a finding to reconcile here.

---

## 11. Adding or changing a term

**Adding.** Confirm the §5.1 ≥2-layer criterion, add the row with its per-layer spellings as they will actually be written, and note any frozen spelling in §6. Additive — no sign-off needed beyond normal MR review.

**Changing an existing term.** A rename touches every layer at once and is a **decision, recorded here**, not a unilateral edit on either side of a boundary — same rule as `design-system-handoff.md` §7. The MR that renames updates this table in the same commit.

**Never** rename a frozen spelling (§6) to satisfy a convention. Map it.

---

## Version history

| Version | Date | Change |
|---|---|---|
| 1.0 | 2026-08-11 | Initial standard. Written after the customer-facing chat-room port surfaced derived style keys (`whisperDeco`, `whisperOffBg`, `whisperOffFor`) with no authority for either the term or its per-layer spelling. Consolidates three pre-existing partial answers (`monorepo.md` § Domain vocabulary mapping, `design-system-handoff.md` §7, the `cspell`/`peck` dictionaries) and records three verified live divergences: the `screenname`/`screenName`/`screen_name` split, the forked `RoomRowKind`-vs-`--color-chat-*` row-kind vocabularies across ProductJs and PerformerApplicationJs, and `fontClass.includes('whisper')` classifying on a frozen wire value. |
| 1.1 | 2026-08-21 | §10's reference-table row for `design-system-handoff.md` corrected: it cited the Tailwind `@theme` token namespaces as the convention for design systems generally, which that standard's own §8 stopped claiming at v1.2 — the namespaces govern systems **we author**, and an existing system that names tokens otherwise is mapped in the importing merge request rather than renamed. No change to this document's own rules, terms or transforms; the row was quoting another standard's claim, and the claim moved. |
