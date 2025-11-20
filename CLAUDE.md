# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

uBix Core is a PHP/JavaScript monorepo containing shared infrastructure, multiple API services, web applications, and CLI tools. Originally named "Project Neptune."

## Build & Test Commands

### PHP

```bash
# Run all tests
vendor/bin/phpunit

# Run single test file
vendor/bin/phpunit tests/path/to/TestFile.php

# Run tests with coverage
vendor/bin/phpunit --coverage-html coverage/

# Static analysis (level max, bleeding edge)
vendor/bin/phpstan

# Code style checking
vendor/bin/phpcs

# PHP refactoring
vendor/bin/rector
```

### JavaScript (from js/Ubix/ or app/SowingMeJs/)

```bash
npm run dev      # Development server
npm run build    # Production build
npm run preview  # Preview production build
```

### CLI

```bash
bin/ubix         # Main CLI entry point with command auto-discovery
```

## Architecture

### Directory Structure

- **php/Ubix/** - Core PHP library
  - `Console/Command/` - CLI commands (build, deploy, code review, cron, database)
  - `Controller/` - API controllers organized by module (AffiliateApi, FanClubApi, InternalAdminApi, ModelSignupApi, ProductApi)
  - `Repository/` - Data access layer with DTO-based options pattern
  - `DataTransferObject/` - Request/response contracts and repository options
  - `DataType/` - Custom type wrappers (Bool, DateTime, Float, Int, String, Enum variants)
  - `Enum/`, `Collection/`, `Service/` - Supporting utilities

- **app/** - Applications
  - `SowingMeAdminApi/` - Admin API (Slim 4)
  - `SowingMeApi/` - Main affiliate/product API (Slim 4)
  - `SowingMeWeb/` - Web application (Slim 4)
  - `SowingMeJs/` - Svelte 5 frontend (SvelteKit)
  - `UbixCli/` - CLI application

- **js/Ubix/** - Svelte component library
- **tests/** - Unit tests mirroring php/Ubix/ structure
- **config/** - DevOps configuration (nginx, K8s)
- **templates/** - Latte templates
- **public/index.php** - Web entry point (APP_NAME env var selects app)

### Key Patterns

- **Repository Pattern** with interface contracts and DTOs for all database operations
- **Data Transfer Objects** for strongly typed API contracts and repository options
- **Custom Data Types** for type-safe domain concepts
- **PHP-DI** for dependency injection
- **Slim 4 middleware** architecture for HTTP handling

## Technology Stack

**PHP 8.3+**: Slim 4.5, PHP-DI 7, Latte 3, Monolog 3, Symfony 7.3 components (Validation, Serialization, Cache, Console, Mailer), Guzzle 7.8, AWS SDK v3

**JavaScript**: Svelte 5, SvelteKit 2, Vite 7, Tailwind CSS, TypeScript 5.3

**Infrastructure**: Docker, Kubernetes, GitLab CI/CD, Nginx + PHP-FPM, MariaDB

## Code Quality Standards

- PHPStan at level max with bleeding edge rules
- Custom CodeSniffer rules (ProjectNeptune ruleset in `php/Vsm/Sniffs/`)
- Strict PHPUnit (fails on risky tests, warnings, deprecations)
- Test cases enforce VSM standards via custom base classes

## Key Entry Points

- **PHP CLI**: `bin/ubix` - Console app with auto-discovered commands
- **Web Apps**: `public/index.php` - Slim loader using APP_NAME env var
- **Frontend**: `app/SowingMeJs/` - SvelteKit application
