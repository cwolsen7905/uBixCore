# Code Review — the two layers and the closed loop

**Version:** 1.3
**Date:** 2026-08-24
**Status:** Active — closes architecture-sweep finding ARCH-08 (the review process was documented only in fragments across CLAUDE.md, the git-workflow standard, and CI comments).

uBix Core reviews every change twice, with a hard division of labor:

| Layer | What it catches | Where it runs | Blocking? |
|---|---|---|---|
| **Machine gate** — `php bin/ubix code:review` (see CLAUDE.md for the current tool list — the count is deliberately not repeated here; it changed three times on 2026-07-30 alone) | Everything machine-checkable: lint, types, tests, spelling, formatting, spec drift, migration headers, CHANGELOG structure, CI-config syntax | Pre-push hook on every push (all tools); the MR pipeline, pre-merge (`cspell-knip-mr` — the cspell/knip leg only, see §1); CI on `dev` (all tools) | Yes — a red gate refuses the push (`.githooks/pre-push`; **never `--no-verify`**), and on `dev` a red gate leaves `lint-and-test` red, which stage-gates `deploy-dev`. On MR pipelines: not yet — see §1 |
| **AI judgment review** — Claude via `claude-review-mr` | What the gate structurally cannot see: stale docs vs code, over-broad logic, missed invariants, design drift, misleading comments | The MR pipeline, pre-merge | Yes — via unresolved discussion threads (see below), never via job status |

The operating principle (Christopher, 2026-07-30): **push clean, don't MR-loop-clean.** Any finding class that turns out to be mechanical gets promoted out of the review and into the gate — that's how the gate grew `migrations`, `changelog`, and `ci-config` in one day. A review that keeps catching mechanical classes means the gate is under-built, not that the review is working well.

## 1. The mechanical gate

`docs/standards/` + `docs/architecture/` define the bar; the gate enforces the machine-checkable subset. See CLAUDE.md → "Machine Code Review" for the tool list and flags. Two rules matter here:

- **Every push must be gate-green.** The pre-push hook enforces it; there is no bypass.
- **Gate configuration changes need Christopher Olsen's prior sign-off** — including adding tools (additive growth like the three above was signed off explicitly).
- **One leg of the gate runs pre-merge, on the MR pipeline: `cspell-knip-mr`.** Every other parity job (`static-checks`, `phpunit`, `js-lint`, `py-checks`, `openapi-drift`) is `only: dev` because it needs `build-dev`'s image first, so an MR pipeline used to run `destructive-pr-detect` + `claude-review-mr` and nothing else — the pre-push hook, which is **per-clone opt-in** (`git config core.hooksPath .githooks`), was the only machine gate ahead of a merge, and a clone that never ran that line had none. Twice — 2026-07-31 and 2026-08-24 — cspell words merged clean and reddened `dev`'s `lint-and-test`, which stage-gates `deploy-dev`, so `dev` stopped being redeployed for everyone until someone noticed. The cspell/knip leg went first because it is the cheap one (`needs: []`, a stock `node:24`, no built image and **no secrets** — protected variables never reach MR pipelines; 48s wall-clock on the 2026-08-24 dev run) and because cspell is the only gate tool that fails on **prose** — a CHANGELOG bullet, a doc, a comment — which every branch touches and which neither a human reviewer nor any other MR job reads. **It is visibility, not enforcement, until the project setting "Pipelines must succeed" is ON** (SB-36's pending list): read the job, don't merge red. Extending parity to the remaining legs means putting an image build on MR pipelines — a separate cost decision, deliberately not taken here.
- **Both tools in that job always run.** They were two bare commands under `set -e`, so a red cspell aborted the shell and knip never ran — on 2026-08-24 that left knip's result simply unknown on a red `dev` and someone had to run it by hand. Each tool's exit code is captured now, so one red never hides the other.

## 2. The AI review at MR time

Every MR targeting `dev` gets reviewed by the `claude-review-mr` CI job (`.gitlab-ci.yml`), which:

1. Diffs the MR against its **merge base** (never the target tip), excluding `.env` / `.env_prod`.
2. Runs the Claude CLI on the baked `neptune-claude-review` image (BaseImages) with the versioned prompt **`config/ci/claude-review-prompt.md`** — read-only tools, judgment-focused scope, explicitly told not to repeat what the gate enforces.
3. Posts each finding as an **unresolved discussion thread** on the MR via `config/ci/post-mr-review.mjs` (quick-action-sanitized, deduplicated across re-runs, paginated).
4. Records the review to the engineering leaderboard (`review_source=mr`) — findings are **catches, not violations**; the scored signals are findings-per-review and time-to-fix (see `docs/projects/engineering-leaderboard/`).

**Enforcement is the project setting "All threads must be resolved before merge"** — not job status and not approval. The review job itself is `allow_failure` so an API outage can never wedge an MR; every could-not-run state (missing key, diff failure, model error, output that could not be parsed) posts a **blocking "treat as NOT reviewed" thread** instead of exiting silently, and posting failures alarm `#neptune-alerts`. Claude **never approves** — humans approve; the model's only lever is leaving threads.

Slack is **alarm-only** for MR reviews: a successful review posts nothing (the MR is the artifact); only posting failures page.

## 3. Working the threads — the disposition convention

Every finding gets a deliberate, recorded human decision; a resolved thread means "a human decided", not "fixed". **Start every resolution comment with a machine-parseable marker** — later reporting computes fix-rate / dismiss-rate / deferral debt per finding class from these:

- **`Fixed:`** `<sha> — <note>` — the code changed.
- **`Dismissed:`** `<reason>` — deliberate no-action; the written reason is the record.
- **`Deferred:`** `<tracking home> — <why later>` — accepted, not now; must name where it's tracked.

Judgment, not the severity tag, decides which: fix anything cheaper to fix than to argue about and all correctness/security regardless of tag; dismiss the speculative and stylistic with a reason; defer only with a real tracking home. Never resolve silently. Full guidance: `branching-and-git-workflow.md` → "Working the review threads".

## 4. Breaking the loop — where findings go to die

Review findings converge because every finding class drains into exactly one of three buckets:

1. **Fix-worthy** → fixed on the MR (the normal case).
2. **Gate-able** → a recurring *mechanical* class becomes a `code:review` tool or a test (needs Christopher's sign-off; precedent: migrations / changelog / ci-config, all promoted from review findings).
3. **Noise** → a recurring *dismissed* class goes into the review prompt's out-of-scope list, and the reviewer stops raising it.

The stop rule per MR: when a review round comes back all minor-severity polish, dismiss the marginal with reasons and **merge** — the termination condition is human satisfaction, never reviewer silence (model reviews are samples, not proofs; a fresh pass always finds *something*).

Chartered (build order + slices in [`docs/projects/engineering-leaderboard/status.md`](../projects/engineering-leaderboard/status.md) → "Review analytics"): per-finding records (`SYSTEMS.Code_Review_Findings` — agent-agnostic naming, `reviewer` column) with fingerprint identity, a nightly disposition harvester parsing the §3 resolution markers, Internal Admin roll-up + triage pages, and the two automations (dismiss-rate threshold → prompt out-of-scope proposal; recurring mechanical class → gate-tool candidate).

## 5. Operational surface

- **Prompt tuning:** edit `config/ci/claude-review-prompt.md` (versioned, reviewed like any change). Out-of-scope additions come from dismissal patterns.
- **Model/cost:** the job pins the model + turn cap in `.gitlab-ci.yml`; the CLI is baked (deliberately unpinned) in the `neptune-claude-review` image — bump by rebuilding the BaseImages folder.
- **Credentials:** `ANTHROPIC_API_KEY` and `CLAUDE_REVIEW_GITLAB_TOKEN` are Masked, **NOT Protected** CI variables (protected variables never reach MR pipelines). The GitLab token needs `api` scope, Reporter+.
- **Scripts are tested code:** `config/ci/*.mjs` pure helpers live in `review-lib.mjs` with `*.test.mjs` specs — the gate's `ci-config` tool runs them; CI-config breakage cannot reach a push.
- **The legacy `claude-review-dev` job** (post-push review of `dev`) remains as a transitional safety net and retires once MR-only enforcement has bedded in.

## 6. Cost — the AI review is metered, so push deliberately

The MR review is a **paid API call, billed per run**, and the thing that drives the bill is **push count, not MR count**. Two mechanics make that expensive if you push the way you would on an unreviewed repo:

- **Every push to an open MR re-runs the review over the ENTIRE diff**, not just the new commits. The job diffs against the *merge base* (§2.1), so the tenth push on a 600-line MR re-reads all 600 lines for the tenth time. Cost ≈ **diff size × pushes**.
- **A rebase or force-push is a push.** Rebasing onto a moved `dev` re-runs a full review even when your own code did not change.
- **Superseded pipelines still bill.** Neither review job sets `interruptible`, so pushing again while a review is mid-flight pays for both.

Measured: **2026-08-04 cost ≈ $20 in MR reviews** across 3 merged MRs — the spend was iteration, not volume. This is also what exhausted the `ANTHROPIC_API_KEY` spend cap on 2026-08-05, which took the reviewer down for **every** MR (a capped key posts the blocking "treat as UNREVIEWED" thread, so it stops merges too — a cost problem becomes a throughput problem).

**The rules:**

1. **One push per review round.** When a review comes back with findings, work **all** of them, run the gate **once**, push **once**. Pushing per-finding multiplies the bill by the finding count and buys nothing — the reviewer re-reads the whole diff either way.
2. **Push when the slice is complete, not as you go.** Push time *is* review time: `-o merge_request.create` opens the MR on the first push, and every push after it is another review. Finish the slice, then push.
3. **Never push to find out whether CI passes.** Run `php bin/ubix code:review` locally — that is what the pre-push hook runs anyway, so a red gate costs you the push *and* a review.
4. **Squash trivial follow-ups locally.** `git commit --amend` / an interactive squash before pushing costs nothing; three "fix typo" pushes cost three full reviews.
5. **Bundle slices into one MR** (per the existing bundling guidance) — fewer MRs *and* fewer pushes, and one review of a coherent 500-line diff is cheaper and better than five reviews of 100-line fragments.
6. **Genuine WIP / backup pushes:** `git push -o ci.skip`. **Never** use it on a push that should be reviewed — a skipped pipeline posts **no thread at all**, and zero threads reads as a clean bill rather than as an absent review (§2). Use it for handoff/backup only, and let the final push run the pipeline.

### 6.1 Review locally first — `bin/ai-review.sh`

The cheapest push is the one you never make, so run the review **before** you push:

```bash
bin/ai-review.sh              # 3 passes, HEAD vs origin/dev
bin/ai-review.sh -p 5         # more passes = higher recall
```

It reproduces the pipeline review — same prompt (`config/ci/claude-review-prompt.md`), same model, same merge-base diff — on your local `claude` CLI's own authentication, so it does **not** draw on the `ANTHROPIC_API_KEY` spend cap that §6 above is about. Every finding you fix before pushing is a whole review round that never gets billed. It is advisory and never blocks a push.

Two properties worth understanding before you trust it:

- **It runs against a scrubbed scratch tree, not your checkout.** The reviewer has `Read`/`Grep`/`Glob` over its working directory, and `.env`/`.env_prod` are *tracked* in this repo, so excluding them from the diff would not keep them out of reach. The script builds the tree from `git archive HEAD`, deletes both files, and refuses to run if they survive — the same copy-and-scrub the `claude-review-mr` container does. **Any future tool that hands a diff to a model must do the same**; diff-level exclusion is not file-level exclusion.
- **It is not deterministic, which is why it runs several passes.** Measured 2026-08-06 on MR !919: the pipeline caught a real duplicate-key defect, and three local runs of the identical prompt, model and diff caught it **once** — the other two returned "no findings", one explicitly calling the buggy code "correct by construction". Treat a finding from *any* pass as worth checking, and treat a clean result — local **or** pipeline — as no evidence of correctness. §2's point stands: the AI review is a second pair of eyes, not a gate.

**Structural levers (need Christopher's sign-off — see `CLAUDE.md` on changing review configuration):**

- **`interruptible: true` on both review jobs** — makes a new push cancel the superseded pipeline's review instead of paying for both. Highest leverage for the least risk; the review is already `allow_failure`, so a cancelled run cannot wedge anything.
- **Skip the review while an MR is a Draft**, running it when the MR is marked ready — turns "N pushes while iterating" into one review at the end. Needs care: the ready-state pipeline must actually run, or the MR merges unreviewed.
- **Raise or remove the key's spend cap** — a cap does not reduce cost, it converts overspend into a review outage that blocks merges.

---

## Document Control

**Version History:**

| Version | Date | Author | Changes |
|---|---|---|---|
| 1.0 | 2026-07-30 | Christopher W. Olsen | Initial standard — consolidates the two-layer review model, MR-time AI review mechanics, thread-disposition convention, and the fix/promote/suppress loop-breaking triage (ARCH-08) |
| 1.1 | 2026-08-05 | Christopher W. Olsen | New §6 **Cost** — the review is metered and billed per push, re-reading the full diff each time (cost ≈ diff size × pushes; rebases and superseded pipelines bill too). Adds the batching rules (one push per review round, push when the slice is complete, never push to test CI, squash follow-ups, `-o ci.skip` for WIP only) and the three structural levers pending sign-off (`interruptible`, draft-gating, spend cap). Written after 2026-08-04's ≈$20 review spend and the 2026-08-05 spend-cap outage that took the reviewer down for every MR |
| 1.2 | 2026-08-06 | Christopher W. Olsen | New §6.1 — `bin/ai-review.sh` runs the pipeline's review locally before a push, on the local CLI's own auth, so findings fixed pre-push cost no billed review round. Records two properties learned building it: the reviewer must run against a `git archive`-scrubbed scratch tree because `.env`/`.env_prod` are tracked and diff-exclusion is not file-exclusion; and the review is non-deterministic — the same prompt/model/diff caught a real defect in 1 of 3 runs, so it runs several passes and a clean review is not evidence of correctness |
| 1.3 | 2026-08-24 | Christopher W. Olsen | SB-36 follow-up slice 1: the machine gate now has a **pre-merge** leg — new `cspell-knip-mr` job on MR pipelines (§1). Records why that leg went first (no built image, no secrets, and cspell is the only gate tool that reads prose), that it is visibility until *Pipelines must succeed* is ON, and that the two tools no longer hide each other's result. Written after 2026-08-24, the second time cspell words merged clean and blocked `deploy-dev` for everyone. Also corrects the header version, which had lagged the 1.2 row |
