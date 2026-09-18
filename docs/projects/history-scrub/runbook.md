# History scrub runbook

**Status:** ready to run. Steps 1–3 are the owner's; nothing here is automated.
**Last surveyed:** 2026-09-18, against `origin/main` at `52141c6f`.

The working trees of every repo are already clean — this is only about what remains
reachable in *history*, and therefore on the public GitHub mirrors.

---

## What the survey actually found

Two separate problems with two different fixes. They were assumed to be one job; they
are not, and the larger one is far cheaper than expected.

### A. Employer production schema — reachable, but not from `main`

Fourteen `sql/*.sql` schema dumps totalling **1.85 MB**, introduced in `5d6c1cd3`, plus
the domain classes that went with them and the standards docs written for that
organisation.

The names are deliberately not written out here — this document lives on `main`, and
listing them would reintroduce exactly what the exercise removes. To see them:

```sh
git ls-tree -r --name-only 5d6c1cd3 -- sql/
```

**They are not in `main`'s history, and not in any tag from `v0.2.1` onward.** The OSS
split already removed them. They survive only on these refs:

| Ref | Last touched |
|---|---|
| `origin/archive/history-pre-github` | 2026-09-03 |
| `origin/feat/oss-01-plan` | 2026-09-02 |
| `origin/fix/phpstan-batch1` | 2026-08-28 |
| tags `v0.1.0` `v0.1.1` `v0.1.2` `v0.1.3` `v0.1.4` | — |

So this needs **ref deletion, not a history rewrite**. No rewrite of `main`, no
re-tagging of current releases, no coordination with other lanes.

### B. The hardcoded bearer token — this one *is* in `main`

`php/Ubix/Middleware/BearerTokenAuthenticationMiddleware.php` arrived at `b7faa612`
("uBixCore 0.2.1") with a live credential as its only accepted token. It was fixed on
`main` in `ef99f7de`, but `b7faa612` is an ancestor of `main` and of **every tag from
`v0.2.1` through `v0.7.1`**.

Removing it means rewriting `main` and re-tagging those releases.

> **Rotation is what actually closes this.** A rewrite makes the string unreachable; it
> does not make it unused. If the credential is still live, rotate it first and treat
> the rewrite as hygiene. If it is already rotated, step 3 is optional cleanup and can
> be scheduled rather than rushed.

---

## Before anything

```sh
git clone --mirror git@gitlab.brainchurts.com:ubixsys/ubixcore.git ~/ubixcore-backup.git
```

Keep it until you are satisfied. Every step below is destructive to remote state, and
this is the only way back.

Tell the other lanes first: the `AGENTS-COORD.md` §6 log is the place. Step 3 invalidates
every existing clone and worktree of this repo.

---

## Step 1 — Delete the refs carrying the schema dumps

This alone removes problem A in full.

```sh
cd ~/git/ubixcore
git fetch origin --prune --tags

# Branches
git push origin --delete archive/history-pre-github
git push origin --delete feat/oss-01-plan
git push origin --delete fix/phpstan-batch1

# Tags (v0.1.x predates the OSS split; nothing consumes them)
for t in v0.1.0 v0.1.1 v0.1.2 v0.1.3 v0.1.4; do
  git push origin --delete "$t"
  git tag -d "$t"
done
```

`archive/history-pre-github` was kept deliberately, so decide before deleting it: it is
the only copy of the pre-split history. If that history is worth keeping, keep it in the
private backup clone above rather than on a mirrored remote.

**Verify:**

```sh
git fetch origin --prune --tags
git for-each-ref --format='%(refname)' refs/remotes/origin refs/tags |
  while read r; do
    git merge-base --is-ancestor 5d6c1cd3a5b5e8a896c5a41ccc133a12a8006710 "$r" 2>/dev/null &&
      echo "STILL REACHABLE: $r"
  done
```

Silence means done.

## Step 2 — Same deletion on both GitHub mirrors

`github.com/ubixsys/ubixcore` and `github.com/cwolsen7905/uBixCore`. Mirror pushes carry
branches and tags, so assume both have them until checked:

```sh
gh api repos/ubixsys/ubixcore/git/refs/heads --jq '.[].ref'
gh api repos/ubixsys/ubixcore/git/refs/tags  --jq '.[].ref'
```

Delete the same refs on each. Then — and this is the part people miss — **the commits stay
served by SHA** even with no ref pointing at them, and GitHub caches them indefinitely.
Open a support request naming the repository and asking them to purge the unreachable
objects. Until they do, anyone holding a SHA can still fetch the dumps.

## Step 3 — Rewrite `main` for the bearer token (optional once rotated)

Only if the credential was not rotated, or you want it gone regardless.

```sh
pip install git-filter-repo   # once

cd /tmp && git clone git@gitlab.brainchurts.com:ubixsys/ubixcore.git rewrite && cd rewrite

# Replace the literal everywhere it appears, without ever echoing it:
#   put the old value in replacements.txt as  <old>==><removed>
#   (one line; the file never gets committed)
git filter-repo --replace-text replacements.txt

git push --force --all
git push --force --tags
```

Then re-verify each release still installs (`composer require ubixsys/ubixcore:0.7.1`),
because re-tagging changes the archive SHAs the registry recorded. Delete and republish
the affected package archives if the registry pins by commit.

Afterwards, every lane re-clones. Existing worktrees are dead; `AGENTS-COORD.md` is
untracked, so copy it aside first if this sandbox's copy matters.

---

## Order and reasoning

1. **Rotate the credential** — independent of all of this, and the only step that
   actually revokes access.
2. **Step 1** — cheap, safe, reversible from the backup, and it removes the employer's
   production schema. Highest value per unit of risk. Do this one first.
3. **Step 2** — same deletion where the public can see it, plus the cache purge request.
4. **Step 3** — the invasive one. Worth doing, not worth rushing, and pointless before
   rotation.

## Still outstanding, not covered here

- Working trees are done. The last such file was rewritten as
  `docs/architecture/experiment-bucketing.md` — the schema pattern kept, everything
  identifying dropped. Nothing in any repo's default branch carries that content now.
- The CI test database password is in plaintext in job logs 12236, 12239, 12241 and
  12242. Rotate it and delete those jobs' logs; a history rewrite does not touch CI logs.
