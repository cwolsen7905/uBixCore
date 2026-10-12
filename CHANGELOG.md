# Changelog

## Unreleased

### uBixOps

- A fallback token, `GITLAB_API_TOKEN` (chart value `apiToken`), is used for any project
  with no `GITLAB_PROJECT_TOKENS` entry, so a new repo needs only the webhook.
- A sign-off job that is `manual` and not yet run is played (`/jobs/:id/play`) instead of
  retried.
- An approval no longer retries a sign-off job that already passed; an un-approve still
  does, so the verdict can turn red.
- Emoji events: a `thumbsup` awarded to or revoked from a merge request re-runs the job.
- Chart `ubix-ops` 0.2.0.
