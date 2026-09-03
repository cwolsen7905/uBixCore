#!/usr/bin/env bash
# Publish skeleton/ as its own Composer package (ubixsys/ubixcore-skeleton).
#
# Runs on a tag pipeline after deploy-composer has published the framework:
#   1. `git subtree split` the skeleton/ directory into a standalone history
#   2. push that history + the same tag to the ubixsys/ubixcore-skeleton project
#   3. ask GitLab to publish the tag to that project's Composer registry
#
# Needs (CI variables on this project):
#   GITLAB_SKELETON_TOKEN   project access token on ubixsys/ubixcore-skeleton
#                           (Maintainer; scopes api + write_repository)
#   GITLAB_SKELETON_PATH    optional, default ubixsys/ubixcore-skeleton
#
# Exits 1 when the token is not configured. The CI job is allow_failure, so the
# pipeline still passes but the job shows as a warning instead of green - a
# publish that did nothing must never look like one that succeeded.
set -euo pipefail

TAG="${CI_COMMIT_TAG:-}"
if [ -z "$TAG" ]; then
    echo "publish-skeleton: not a tag pipeline (CI_COMMIT_TAG unset) — nothing to do."
    exit 0
fi

TOKEN="${GITLAB_SKELETON_TOKEN:-}"
if [ -z "$TOKEN" ]; then
    echo "publish-skeleton: GITLAB_SKELETON_TOKEN is not configured — the skeleton was NOT published for ${TAG}." >&2
    echo "publish-skeleton: add it as a masked CI/CD variable on this project (token from ubixsys/ubixcore-skeleton, Maintainer, api + write_repository) and retry this job." >&2
    exit 1
fi

HOST="${CI_SERVER_HOST:-gitlab.brainchurts.com}"
SKELETON_PATH="${GITLAB_SKELETON_PATH:-ubixsys/ubixcore-skeleton}"
SKELETON_URL="https://oauth2:${TOKEN}@${HOST}/${SKELETON_PATH}.git"
API="${CI_API_V4_URL:-https://${HOST}/api/v4}"
ENCODED_PATH="$(printf '%s' "$SKELETON_PATH" | sed 's#/#%2F#g')"
SPLIT_BRANCH="skeleton-split-${TAG}"

git config user.email "${GITLAB_USER_EMAIL:-ci@${HOST}}"
git config user.name  "${GITLAB_USER_NAME:-GitLab CI}"

echo "publish-skeleton: splitting skeleton/ at ${TAG}"
git subtree split --prefix=skeleton -b "${SPLIT_BRANCH}" >/dev/null
SPLIT_SHA="$(git rev-parse "${SPLIT_BRANCH}")"

echo "publish-skeleton: pushing ${SPLIT_BRANCH} (${SPLIT_SHA:0:8}) to ${SKELETON_PATH} as main + ${TAG}"
git push --force "${SKELETON_URL}" "${SPLIT_BRANCH}:refs/heads/main"
git push --force "${SKELETON_URL}" "${SPLIT_SHA}:refs/tags/${TAG}"

echo "publish-skeleton: publishing ${TAG} to the ${SKELETON_PATH} Composer registry"
curl --fail --silent --show-error \
     --header "PRIVATE-TOKEN: ${TOKEN}" \
     --data "tag=${TAG}" \
     "${API}/projects/${ENCODED_PATH}/packages/composer" >/dev/null

git branch -D "${SPLIT_BRANCH}" >/dev/null
echo "publish-skeleton: done — composer create-project ubixsys/ubixcore-skeleton:${TAG#v} now resolves from the ${SKELETON_PATH} registry."
