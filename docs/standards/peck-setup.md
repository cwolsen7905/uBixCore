# Peck Setup Guide

## Overview

Peck is now integrated into the uBix Core machine code review process. It checks for spelling mistakes in your PHP code, including comments, strings, and identifiers.

## Prerequisites

Peck requires **aspell** to be installed on your system.

### Installing aspell

#### On Ubuntu/Debian:
```bash
sudo apt-get update
sudo apt-get install aspell aspell-en
```

#### On macOS:
```bash
brew install aspell
```

#### On Windows:
Download and install from: http://aspell.net/win32/

## Usage

### Running code review with Peck

Peck is now automatically included when you run the machine code review command:

```bash
php bin/ubix code:review
```

Peck is one of the tools in the canonical gate — see the *Machine Code Review* section of `CLAUDE.md` for the authoritative list (deliberately not duplicated here; the tool set grows). Note the division of labor between the two spell checkers: **Peck** checks PHP *identifiers and DocBlocks* (via reflection); **CSpell** checks docs, JS sources, and the other text surfaces.

### Configuration

Peck uses a `peck.json` configuration file in the project root. You can customize:

- Words to ignore (technical terms, product names, etc.)
- Directories to exclude
- File patterns to skip

Example `peck.json`:
```json
{
    "preset": "base",
    "ignore": {
        "words": [
            "flirt4free",
            "neptune",
            "phpstan",
            "phpcs"
        ],
        "paths": [
            "php/Ubix/Sniffs",
            "vendor",
            "node_modules"
        ]
    }
}
```

## What Peck Checks

Peck performs **spell checking on your PHP source code** by analyzing:

### Class-Level Elements
- **Class DocBlock comments** (excluding `@` annotation lines like `@param`, `@return`)
- **Class constants** - names are split into words (e.g., `MAX_FILE_SIZE` → "max file size")
- **Property names** - camelCase and snake_case are parsed (e.g., `$firstName` → "first name")
- **Property DocBlocks**

### Method-Level Elements
- **Method names** - parsed into words (e.g., `getUserById` → "get user by id")
- **Method DocBlock comments** (excluding `@` annotations)
- **Parameter names** (e.g., `$emailAddress` → "email address")
- **Parameter DocBlocks**

### What Peck Does NOT Check
❌ String literals inside your code (e.g., `$message = "Hello wrold"`)
❌ Regular inline comments (e.g., `// This is a coment`)
❌ Variable names inside method bodies

### Example

```php
<?php

/**
 * Manages user authentcation and authorizaton
 *
 * @see UserRepository
 */
class UserManajer  // ← Peck checks: "User", "Manajer" (MISSPELLED!)
{
    /**
     * The user's email adress
     */
    private string $emailAdress;  // ← Peck checks: "email", "Adress" (MISSPELLED!)

    /**
     * Retrieves a user by thier ID
     */
    public function getUserByIdentifer(int $userId): ?User  // ← Peck checks: "get", "User", "By", "Identifer" (MISSPELLED!)
    {
        // This coment has a typo  ← NOT checked (inline comment)
        $mesage = "Error";  // ← NOT checked (variable in method body)
        return null;
    }
}
```

## How It Works

When you run `code:review`:

1. Peck uses PHP Reflection to analyze all classes in `php/` directory
2. It extracts identifiers (method names, properties, constants, parameters)
3. Identifier names are split into individual words (handles camelCase, snake_case)
4. Each word is checked against aspell's dictionary
5. DocBlock comments are parsed (but `@param`, `@return` etc. lines are skipped)
6. Results are displayed alongside other code review tools
7. If aspell is not installed, you'll see a helpful error message with installation instructions

## Disabling Peck

For a single local run, use the per-tool flag every gate tool has:

```bash
php bin/ubix code:review --peck=off
```

**Never edit `MachineCodeReviewService` (or any review configuration) to disable a tool** — changing the code-review standards or their configuration requires Christopher Olsen's prior sign-off, and the pre-push gate must pass with the full tool set regardless of what you skipped locally. The `--peck=off` flag is for iterating faster mid-task, not for landing.

## Troubleshooting

### "aspell: not found" error

If you see this error, aspell is not installed. Follow the installation instructions above for your operating system.

### False positives

If Peck flags legitimate technical terms or product names:

1. Add them to the `ignore` array in `peck.json`
2. Run the code review again

### Performance

Peck is fast and typically adds only 1-2 seconds to the code review process. For a quicker iteration loop while fixing a specific tool's findings, turn the slow runners off (`php bin/ubix code:review --phpunit=off --vitest=off`) — then run the full gate before pushing.

## Benefits

- **Catch typos before code review**: Automated spell checking catches embarrassing typos
- **Improve code quality**: Professional, well-spelled code is easier to read and maintain
- **Documentation quality**: Ensures comments and docblocks are properly spelled
- **Consistent checking**: Same rules applied across the entire codebase

## Further Reading

- [Peck GitHub Repository](https://github.com/peckphp/peck)
- Machine Code Review — the canonical gate and its tools: see the *Machine Code Review* section of [`CLAUDE.md`](../../CLAUDE.md) and [`js-code-review.md`](js-code-review.md) for the JS-side tools.
