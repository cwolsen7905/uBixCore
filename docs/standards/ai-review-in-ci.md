# AI review in CI — `ci:aiReview`

**Version:** 1.0
**Date:** 2026-09-30
**Status:** Active

An advisory review of each merge request by an LLM, run on the GitLab runner. The job sends
the MR diff to Gemini and writes the answer to **one note on the MR**, replaced on every push.
It is never a gate: the job is `allow_failure`, it never approves, and with no keys it skips
green. Humans decide what to act on. Local review, with Claude or whatever each developer
uses, is separate and unaffected.

## 1. What the framework ships, and what the host owns

| Piece | Layer |
|---|---|
| `Ubix\Service\Ci\AiReviewService` — Gemini request, response parsing, one-note upsert | uBixCore |
| `Ubix\Console\Command\Ci\AiReviewCommand` — `bin/ubix ci:aiReview` | uBixCore |
| Generic review instructions (`php/Ubix/Service/Ci/ai-review-instructions.md`): bugs, security, the host's conventions, tests; no style nits; a fixed output format | uBixCore |
| **The host's conventions file** — the rules that have caused that product's incidents | Host |
| **The CI job** — which paths are sent, which image runs it | Host |
| **The two CI variables** | Host |

The instructions the model sees are the generic ones followed by the host's file under
"This project's conventions". Write that file as rules a reviewer could check against a diff,
not as product documentation.

## 2. Setting it up in a host

### 2.1 A Gemini API key

Create one at **https://aistudio.google.com/apikey** (sign in with the Google account that
should own it, "Create API key", pick or create a Google Cloud project). No billing is needed
for the free tier.

**The free tier trains on what you send.** Google uses free-tier prompts to improve its
products; the paid tier does not. Decide that per project, and keep anything that is not
shareable out of the diff (§2.4). Credentials belong in uBixVault, never in the repo, which is
what makes this acceptable for most hosts. Free-tier rate limits are small (requests per
minute and per day); a busy day of pushes can exhaust them, and the job then fails yellow with
"rate limit reached" rather than posting.

### 2.2 A GitLab token to write the note

Project → Settings → Access tokens → **Add new token**: role **Reporter**, scope **`api`**,
an expiry you will remember to rotate. `CI_JOB_TOKEN` cannot write MR notes.

### 2.3 The CI variables

Project → Settings → CI/CD → Variables. Both **Masked**, both **not Protected** — MR branches
are unprotected, so a protected variable never reaches an MR pipeline:

| Variable | Value |
|---|---|
| `GEMINI_API_KEY` | the AI Studio key |
| `AI_REVIEW_GITLAB_TOKEN` | the project access token |
| `AI_REVIEW_MODEL` | optional; default `gemini-flash-latest` (Google's alias for its newest Flash) |

Unprotected means any branch's pipeline can read them, which is why both are low-privilege:
a free-tier key, and a Reporter token that can comment.

### 2.4 The job

```yaml
ai-review-mr:
  stage: lint-and-test
  rules:
    - if: $CI_PIPELINE_SOURCE == "merge_request_event"
  allow_failure: true
  timeout: 10 minutes
  script:
    - |
      git fetch -q --depth=1 origin "$CI_MERGE_REQUEST_DIFF_BASE_SHA"
      git diff "$CI_MERGE_REQUEST_DIFF_BASE_SHA" HEAD -- . ':(exclude)docs' ':(exclude)composer.lock' \
        | php bin/ubix ci:aiReview --guide=bin/ci/ai-review-guide.md
```

- Diff against `CI_MERGE_REQUEST_DIFF_BASE_SHA` (the merge base), never the target branch's tip,
  or the review sees changes the MR did not make.
- **Exclude what should not leave the building**, and what only costs tokens: lockfiles,
  generated files, and any docs that hold infrastructure detail.
- Run it wherever the host's `bin/ubix` runs. kitg runs it inside the MR's own `-test` image,
  passing the variables through with `docker run -e`.

Diffs over 400,000 characters are cut at the last whole file, and the note says so.

**A host with its own CLI container** (one that passes its own file to `console()`, as kitg
does) must copy the framework's `AiReviewService` binding from `cli-dependencies.php`: it gives
the review a 240-second HTTP client. Without it the review uses the shared client's 5-second
default and every run times out.

## 3. What a run does

1. No key or token: prints "Skipped" and exits 0.
2. Empty diff: "Skipped", exit 0.
3. Sends the diff and instructions to `generateContent`.
4. Writes the note, marked `<!-- ubix-ai-review -->`, or replaces the one an earlier push
   wrote — one note per MR however many pushes.
5. A rate limit, a refusal or an empty answer exits 1 with the reason in the job log and posts
   nothing: a note saying "the review failed" is noise on the MR.
