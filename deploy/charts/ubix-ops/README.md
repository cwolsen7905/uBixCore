# ubix-ops

Helm chart for uBixOps, uBixCore's operations app. It re-runs a merge request's sign-off
job when the MR is approved or un-approved.

Full documentation: `docs/architecture/ubix-ops.md` in uBixCore.

```sh
helm install ubix-ops ./deploy/charts/ubix-ops -n ubix-ops --create-namespace \
  --set gitlab.apiUrl=https://gitlab.example.com/api/v4 \
  --set existingSecret=ubix-ops-credentials
```

| Value | Default | Meaning |
|---|---|---|
| `gitlab.apiUrl` | — (required) | Your GitLab API base |
| `gitlab.approvalJobNames` | `require-approval,require-approval-mr` | Jobs to retry |
| `existingSecret` | `""` | Secret with `GITLAB_WEBHOOK_SECRET`, `GITLAB_PROJECT_TOKENS` |
| `webhookSecret`, `projectTokens` | `""` | Let the chart create that Secret (test installs) |
| `extraEnv` | `[]` | e.g. uBixVault's `VAULT_*` variables |
| `ingress.*` | disabled | Expose `/gitlab/merge-request-hook` to GitLab |
