# uBixOps — the framework's operations app

**Version:** 1.0
**Date:** 2026-10-01
**Status:** Active

uBixOps is an HTTP app that ships **inside** uBixCore, for operational plumbing that
belongs to no product. Its first job: when someone approves or un-approves a merge
request, re-run that MR's sign-off job (`ci:requireApproval`), because GitLab does not
re-run a pipeline on approval.

## 1. What ships, and how it is inherited

| Piece | Where |
|---|---|
| The app: routes, wiring, middleware | `php/Ubix/Bootstrap/apps/UbixOpsApi/src/` |
| `Ubix\Controller\Ops\OpsController` — `GET /health`, `POST /gitlab/merge-request-hook` | framework |
| `Ubix\Service\Ci\ApprovalWebhookService` — verify, decide, retry one job | framework |
| Image `ghcr.io/ubixsys/ubixcore-ops:<version>` | `Dockerfile_Ops`, published on every `v*` tag by `.github/workflows/ops-image.yml` |
| Helm chart `ubix-ops` | `deploy/charts/ubix-ops/` |

`Ubix\Bootstrap\http()` serves `app/<AppName>` from the host when it exists, and
otherwise an app the framework ships under `apps/<AppName>`. So **any image built on
uBixCore can run uBixOps with `APP_NAME=UbixOpsApi` and no code of its own**; a host that
wants to change it copies the folder into its own `app/` and the copy wins.

## 2. What a delivery does

1. Wrong or missing `X-Gitlab-Token` → **401**. Nothing is sent to GitLab.
2. Anything but a merge-request `approved`/`unapproved` event → 200, ignored.
3. A project with no token configured → 200, ignored.
4. Otherwise: read the MR's head pipeline, find the **newest** job named in
   `APPROVAL_JOB_NAMES`, and `POST /jobs/:id/retry`. A job already queued or running is
   left alone. **One job, never a new pipeline**: a pipeline would re-run every check,
   including the AI review, for each click.
5. Any GitLab failure → **200**, with the reason logged. GitLab disables a webhook that
   keeps failing, and a disabled webhook fails silently, so only a bad secret is an error.

## 3. Deploying it

```sh
helm install ubix-ops deploy/charts/ubix-ops -n ubix-ops --create-namespace \
  --set gitlab.apiUrl=https://gitlab.example.com/api/v4 \
  --set existingSecret=ubix-ops-credentials
```

The Secret carries `GITLAB_WEBHOOK_SECRET` and `GITLAB_PROJECT_TOKENS`
(`projectId=token,projectId=token`). Each token is a **project access token, Developer
role, `api` scope** — retrying a job needs Developer; the Reporter token that
`ci:requireApproval` reads with is not enough. With uBixVault, skip the Secret and pass
`VAULT_ADDR`, `VAULT_K8S_ROLE` and `VAULT_APP_KV_PATH` through `extraEnv`; uBixCore
resolves them at boot.

Then, per GitLab project: **Settings → Webhooks → Add new webhook**, URL
`https://<host>/gitlab/merge-request-hook`, the same secret token, trigger **Merge request
events** only. Keep the endpoint internal where you can: GitLab must reach it, nobody else
needs to.

## 4. Why it is a framework app and not a product route

The first draft hung the webhook off a product API. Three reasons it moved:

- It serves every project on the GitLab instance — the framework's own included — so it
  cannot belong to any one of them.
- It holds Developer tokens for other repositories; a product's namespace, with its own
  payments and personal data, is the wrong place for that blast radius.
- Shipping it as image + chart means another organisation running uBixCore deploys it the
  same way we do, with no fork.
