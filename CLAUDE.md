# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Agent coordination & concurrent sessions — read first

Multiple agent sessions can run in this repo at the same time and lack the ambient coordination (Slack, standups) that keeps humans from colliding. Two rules keep them from clobbering each other; follow both **every session, without waiting to be asked**:

- **`AGENTS-COORD.md`** (repo root, **untracked** per-sandbox state — seed once with `cp AGENTS-COORD.template.md AGENTS-COORD.md` if missing) is the live coordination contract. **Before you create a branch, edit a shared file, or merge**, register your lane: add a row to its §1 lane table with a distinct branch prefix and an append-only §6 log entry claiming the paths/shared files you'll touch. Shared files (claim before editing): root `README.md`, `CLAUDE.md`, `AGENTS-COORD.md`, the framework trees `php/Ubix/*` / `js/Ubix/*`, and per-app `app/<App>/src/{Routes,Dependencies}.php`. Assume concurrency unless the log clearly shows you're solo; if you truly are solo, still register your lane, work in the main checkout, and skip the worktree overhead.
- **One `git worktree` per concurrent session** — a shared working directory is unsafe (one session's `git checkout`/`rebase` swaps files out from under another's uncommitted edits). Raw form: `git fetch origin && git worktree add ../ubixcore-worktrees/<lane> -b <prefix>/<slice> origin/dev`; `git worktree remove` when landed. (The `php bin/ubix code:worktree` bootstrap from neptune is **not yet ported** — use the raw form until it lands.)

Full rules — branch topology (`dev` is MR-only), sync/merge flow, the serialized merge window, and the disposition convention — are in **`docs/standards/branching-and-git-workflow.md`** (§ Concurrent Agent Sessions). `AGENTS-COORD.md` is this sandbox's living instance of that standard; the standard wins if they disagree.

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

## Product & surface documentation (SRS / TDS / ADS)

Sowing.me is documented at **two altitudes**, both using the same three-doc model (authored against `docs/standards/web-development-delivery-framework.md`: Charter → SRS → SDD). The acronyms map onto the repo's existing filenames:

| Acronym | File | Answers | Owns |
|---|---|---|---|
| **SRS** — Software Requirements Spec | `srs.md` | **What & why** | Numbered functional (`FR-*`) + non-functional (`NFR-*`) requirements, personas, acceptance criteria, open questions |
| **TDS** — Technical Design Spec | `technical-spec.md` | **How in code** | Domain model (tables via the migration runner), API surface, DTO/DataType/Repository design, clients, `## Requirement traceability` mapping each `FR-*` to what realises it |
| **ADS** — Architecture Design Spec | `architecture.md` | **How as a system** | Topology, data/security architecture, sequences, capacity, failure modes, technology decisions/ADRs (the SDD) |

`technical-spec.md` is the neptune-inherited house name (see `docs/README.md`); `architecture.md` is added when a system design warrants its own SDD. A `README.md` in each folder gives the read order and status.

**Altitude 1 — Platform** (`docs/projects/sowing-me/platform/`): the whole product — every persona and capability domain of a Patreon-class membership platform *rebuilt for Christian creators* (memberships, content, payments, payouts, discovery, community) **plus** faith-native domains (church/organization accounts, tithing/giving, prayer, devotional content). The platform ADS is the foundation every surface plugs into; it is written so features can be added later without rework. Start here for cross-cutting shape.

**Altitude 2 — Surface** (`docs/surfaces/<slug>/`): one capability slice drills down under the platform trio, inheriting its conventions. First worked example: `docs/surfaces/live-streaming/` (roadmap M3-06).

**Keeping them in sync — required:**
- The trio moves **together** at each altitude. A requirement change in `srs.md` updates the traceability in `technical-spec.md`, any system impact in `architecture.md`, and bumps each doc's **Document control** version table.
- A surface must not contradict the platform ADS; a change that does re-versions the platform doc in the same commit.
- Write a surface's SRS + TDS **before its first migration** (charter §7 success criteria).
- A roadmap status flip + the matching `status.md` entry land in the **same commit** as the code.

## Key Entry Points

- **PHP CLI**: `bin/ubix` - Console app with auto-discovered commands
- **Web Apps**: `public/index.php` - Slim loader using APP_NAME env var
- **Frontend**: `app/SowingMeJs/` - SvelteKit application
