# uBix Core

A PHP/JavaScript monorepo containing shared infrastructure, multiple API services, web applications, and CLI tools.

## Requirements

- PHP 8.3+
- Node.js 20+
- Composer
- Docker (for local services)

## Getting Started

```bash
# Install PHP dependencies
composer install

# Install JS dependencies (frontend)
cd app/SowingMeJs && npm install
```

Copy the appropriate `.env` file for your environment and configure it locally — env files are not tracked in version control.

## Applications

| App | Type | Description |
|-----|------|-------------|
| `app/SowingMeApi` | Slim 4 API | Main affiliate/product API |
| `app/SowingMeAdminApi` | Slim 4 API | Internal admin API |
| `app/SowingMeWeb` | Slim 4 | Web application |
| `app/SowingMeJs` | SvelteKit | Frontend (Svelte 5 + Tailwind) |
| `app/UbixCli` | Console | CLI application |

**Entry point:** `public/index.php` — the `APP_NAME` environment variable selects which app is loaded.

## CLI

```bash
bin/ubix <command>     # Commands are auto-discovered
```

## Development

### PHP

```bash
vendor/bin/phpunit                          # Run all tests
vendor/bin/phpunit tests/path/to/File.php  # Run a single test file
vendor/bin/phpunit --coverage-html coverage/ # Generate coverage report (requires xdebug)
vendor/bin/phpstan                          # Static analysis (level max)
vendor/bin/phpcs                            # Code style check
vendor/bin/rector                           # Automated refactoring
```

### JavaScript

```bash
# From app/SowingMeJs or js/Ubix/
npm run dev      # Development server
npm run build    # Production build
npm run preview  # Preview production build
```

## Directory Structure

```
php/Ubix/          # Core PHP library (shared across apps)
app/               # Applications (API, web, frontend, CLI)
js/Ubix/           # Svelte component library
tests/             # Unit tests (mirrors php/Ubix/ structure)
templates/         # Latte templates
config/            # DevOps config (nginx, Kubernetes)
public/            # Web entry point
bin/               # CLI entry point
```

## Tech Stack

- **PHP:** Slim 4, PHP-DI 7, Latte 3, Monolog 3, Symfony 7 components, Guzzle 7, AWS SDK v3
- **JavaScript:** Svelte 5, SvelteKit 2, Vite 7, Tailwind CSS, TypeScript 5
- **Infrastructure:** Docker, Kubernetes, GitLab CI/CD, Nginx + PHP-FPM, MariaDB
