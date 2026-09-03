# Get started with uBixCore — quickstart (target UX, draft)

**Status:** Draft v1, 2026-09-02; reality check updated 2026-09-03 after OSS-02/03. This is the page ubixsys-web will publish. It is
written as the finished experience; the "Reality check" table at the bottom maps
each step to the OSS-xx slice that delivers it. Update both as slices land.

uBixCore is a PHP 8.4+ / Svelte 5 framework **and** the tooling around it — CLI,
code review gate, coding-standard sniffs, PHPUnit base classes, migrations
runner, Docker + GitLab CI templates — shipped together so a new project starts
with the whole workflow, not just a library. You keep your own namespace; the
framework lives in `vendor/` and is never committed to your repo.

Requirements: PHP 8.4+, Composer 2, Node 20+, Docker (for images), git.

## 1. Create the project

```bash
composer create-project ubixsys/ubixcore-skeleton acme
cd acme
```

You get a host project — not a copy of the framework:

```
acme/
  composer.json / composer.lock   requires ubixsys/ubixcore
  public/index.php  bin/ubix      thin entry points (yours to edit)
  app/                            one folder per deployable app (empty)
  php/                            your PSR-4 root goes here (empty)
  tests/
  sql/migrations/
  phpcs.xml  phpstan.neon  phpunit.xml   point at the rules shipped in vendor/
  Dockerfile  .gitlab-ci.yml  config/devops/
  docs/standards/                 the house standards, copied so you own them
  .claude/  AGENTS-COORD.template.md   agent-session tooling
  .env.example
  vendor/                         ubixsys/ubixcore lives here — gitignored
```

## 2. Put it in git

```bash
git init -b dev
git add -A && git commit -m "chore: bootstrap from ubixsys/ubixcore-skeleton"
git remote add origin git@gitlab.example.com:acme/acme.git
git push -u origin dev
```

`composer.lock` is committed on purpose: it is the record of which uBixCore
version every image is built from. `vendor/`, `node_modules/`, `.env*` are not.

## 3. Create your first app and namespace

```bash
cp .env.example .env
bin/ubix app:init AcmeApi --namespace 'Acme\\Api'
```

This scaffolds:

```
app/AcmeApi/src/Routes.php          route table (Slim 4)
app/AcmeApi/src/Dependencies.php    PHP-DI container definitions
app/AcmeApi/dev-deploy.yaml …       k8s manifests from the template
php/Acme/Api/Controller/HealthController.php
tests/Acme/Api/Controller/HealthControllerTest.php
```

and adds `"Acme\\": "php/Acme/"` to `composer.json` autoload. `Ubix\` is the
framework's namespace; you never put code there.

## 4. Run it

```bash
bin/ubix app:run AcmeApi            # PHP built-in server on :8080
curl -s localhost:8080/health       # {"status":"ok"}
```

## 5. Add an endpoint

`php/Acme/Api/Controller/HelloController.php`:

```php
<?php

declare(strict_types=1);

namespace Acme\Api\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ubix\Controller\AbstractController;

final class HelloController extends AbstractController
{
    public function hello(Request $request, Response $response): Response
    {
        return $this->json($response, ['hello' => 'world']);
    }
}
```

`app/AcmeApi/src/Routes.php`:

```php
$app->map(['GET'], '/hello', HelloController::class . ':hello');
```

## 6. Run the gate

```bash
bin/ubix code:review        # phpcs + phpstan (level max) + phpunit, same as CI
```

The skeleton installs a pre-push hook that runs this; a red gate blocks the push.

## 7. Database + migrations

```bash
bin/ubix migrate:diff        # generates a migration from your schema change
bin/ubix migrate:up
bin/ubix migrate:status
```

## 8. Ship it

```bash
docker build --build-arg APP_NAME=AcmeApi -t acme-api .
```

The Dockerfile copies your code plus `composer.lock` and runs
`composer install --no-dev`, which pulls `ubixsys/ubixcore` from Packagist. The
included `.gitlab-ci.yml` does build → lint-and-test → deploy → notify.

## 9. Frontend (optional)

```bash
bin/ubix app:init AcmeJs --js     # SvelteKit app with @ubixsys/ubixcore
cd app/AcmeJs && npm install && npm run dev
```

## 10. Upgrade uBixCore later

```bash
composer update ubixsys/ubixcore
git add composer.lock && git commit -m "chore: ubixcore 0.4.0"
```

One-line diff, one MR. Files the skeleton copied into your repo (entry points,
configs, CI) are yours; the changelog says when an upgrade wants a change there.

---

## Reality check (2026-09-02)

| Step | Exists today? | Delivered by |
|---|---|---|
| 1 `create-project` skeleton | Yes from a path/vcs repository (`skeleton/`, OSS-07); not yet published as its own package | OSS-09 |
| 2 git shape | Yes — lock committed, images `composer install` from it (OSS-06) | — |
| 3 `app:init` + own namespace | Partly — own namespace works end to end (`App\` in the skeleton, commands discovered); no scaffolder yet, copy `app/HelloApi` | OSS-04 for Sowing.me; scaffolder later |
| 4 `app:run` | Yes — resolves `app/` through `ProjectRootService` (OSS-02) | — |
| 5 `AbstractController` in `Ubix\` | Yes | — |
| 6 `code:review` gate + pre-push hook | Yes — `vendor/bin/*` via `ProjectRootService` (OSS-02); `Ubix` phpcs standard, phpstan baseline and PHPUnit base classes ship in the package (OSS-05) | — |
| 7 migrations | Yes — `sql/` via `ProjectRootService` (OSS-02) | — |
| 8 Docker + CI templates | Yes for this repo; need to become skeleton templates | OSS-07 |
| 9 `@ubixsys/ubixcore` on npm | No — `js/Ubix` is named `vsm` | OSS-08 |
| 10 upgrade via Composer | No until published | OSS-09 |
