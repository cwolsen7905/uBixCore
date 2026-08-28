# AGENTS-COORD.template.md — multi-agent coordination (committed template)

> **This is the committed template.** The LIVE file is the **untracked** `AGENTS-COORD.md`
> at the repo root — per-sandbox session state (lane table + §6 log), never pushed
> (decision ported from project-neptune, Christopher, 2026-07-30 — committing live
> coordination state caused cross-lane merge races and spliced log entries on origin).
> On a fresh sandbox, copy this file and register your lane there:
>
> ```bash
> cp AGENTS-COORD.template.md AGENTS-COORD.md
> ```

Two (or more) agents may work this repo concurrently. This file is the contract
that keeps us from colliding on git, branches, and shared files. **Read it before
you branch, before you edit a shared file, and before you merge to `dev`.** It is
a living doc — append to the log at the bottom; do not rewrite another agent's
entries.

> **This file is the living instance of the coordination pattern defined in
> [`docs/standards/branching-and-git-workflow.md` § Concurrent Agent Sessions](docs/standards/branching-and-git-workflow.md#concurrent-agent-sessions).**
> The standard defines the rules (worktree-per-session, the lane table + path
> ownership + append-only log, and the serialized merge window); this file is
> where _this_ repo's agents actually register lanes and claim paths. If the two
> ever disagree, the standard wins — fix this file to match.

## Template — new agent, start here

1. Add a row to the [§1 lane table](#1-agents--roles) with your role and a
   **distinct** branch prefix (`feature-<x>-*` or `docs/*`). No prefix already in use.
2. Create your worktree off freshly-pulled `dev` (see [§2](#2-branch-rules)).
   Recommended: `php bin/ubix code:worktree <lane> <slice>` **once it is ported**
   (see note in §2); until then use the raw form:
   `git fetch origin && git worktree add ../ubixcore-worktrees/<lane> -b <prefix>/<slice> origin/dev`.
3. Add a [§6 log](#6-live-log-append-only-newest-at-bottom) entry: your lane, the
   paths you'll edit, and any shared-file claims.
4. Work only inside your worktree and your [§3](#3-path-ownership-the-core-of-conflict-avoidance)
   paths. Claim shared files in §6 before touching them.

---

## 1. Agents & roles

| Agent | Scope | Branch prefix |
| ----- | ----- | ------------- |

Add a row here first and claim a distinct branch prefix before touching anything.

## 2. Branch rules

- **Own your prefix.** Create/commit/delete only branches under your own prefix.
- **Never** check out, commit to, rebase, or delete a branch you don't own.
- Branch off **freshly-pulled `dev`** every time.
- Land one slice (or one grouped logical unit) per branch **via a merge request**
  (see §4) — `dev` is MR-only; never merge or push `dev` directly.
- **Never touch** `staging` / `main`, and never force-push any shared branch.
- **Use a separate `git worktree` per agent when working concurrently.** A single
  shared working directory is unsafe — one agent's `git checkout` swaps files out
  from under the other's uncommitted edits. Give each agent its own checkout:
  `git worktree add ../ubixcore-worktrees/<lane> -b <prefix>/<slice> origin/dev`.
  All worktrees share one `.git` and push to the same `dev`, so §3 path-ownership
  and §4 merge protocol still apply across them. Run
  `git worktree remove ../ubixcore-worktrees/<lane>` when the slice is landed, and
  `git worktree prune` to clear stale entries.
  > **Note:** the `php bin/ubix code:worktree` bootstrap (as in neptune) is **not
  > yet ported** to ubixcore (`php/Ubix/Console/Command/Code/` has Commit, Loc,
  > Merge, Review — no Worktree). Use the raw `git worktree` form above until the
  > command lands (tracked in the tooling-catchup work).

## 3. Path ownership (the core of conflict avoidance)

Edit freely inside **your** paths. To edit a path another agent owns, claim it in
the §6 log first.

**Typical lane split (adjust per the §1 table):**
- Implementation lane(s) own their feature paths: `php/`, `app/`, `js/`, `sql/`,
  `tests/`, `config/`, `bin/`, and the surface's `docs/surfaces/<slug>/**` +
  `docs/projects/sowing-me/**`.
- Docs lane owns `docs/**` outside another lane's active surface.

**Shared — claim in §6 before editing (highest conflict risk):**
- `README.md` (root)
- `CLAUDE.md`
- this file (`AGENTS-COORD.md`)
- **Framework trees** `php/Ubix/*` and `js/Ubix/*` (a change here can affect sibling apps).
- **Per-app registration files** every feature appends to: `app/<App>/src/Routes.php`,
  `app/<App>/src/Dependencies.php`. On a merge conflict here the resolution is
  almost always **keep both sides** (two features each added their own route / DI binding).

(No root `CHANGELOG.md` in this repo today; add it to the shared set if one is introduced.)

## 4. Merging to `dev` (serialize the window)

`dev` is **MR-only** — every landing goes through a merge request, per
[the standard's One land path](docs/standards/branching-and-git-workflow.md#one-land-path-mr-only-dev-2026-07-30).
Never `git merge` into or `git push` `dev` yourself.

Protocol:
1. **Announce** in §6: `LANDING <branch> (MR)` before you push.
2. Make the branch current on YOUR branch: `git fetch origin && git rebase origin/dev`.
3. Push + open the MR in one step:
   `git push -o merge_request.create -o merge_request.target=dev origin <your-branch>`.
   The `code:review` gate runs; fix any violations and re-push. **Never `--no-verify`.**
4. **Work the review threads**: every resolution starts `Fixed:` / `Dismissed:` /
   `Deferred:` per the disposition convention (resolved ≠ fixed).
5. A human approves and merges. A conflict against moved `dev` is resolved on YOUR
   branch (keep **both** sides in shared files per §5), then re-push.
6. Mark `LANDED <branch>` in §6 and delete your local branch.

## 5. Shared-file conflict conventions

- Prefer **additive** edits to `README.md` / `CLAUDE.md`; claim the section in §6 first.
- On a shared-file merge conflict, the resolution is almost always **keep both
  sides** — never drop a peer's addition.
- Route / DI files (`Routes.php`, `Dependencies.php`): keep both registrations.

## 6. Live log (append-only; newest at bottom)

Use this to claim shared files and announce landings. Format:
`YYYY-MM-DD HH:MM <agent> — <message>`

<!-- log entries appear below in the live untracked copy -->
