# Python Coding Guidelines

How we write Python in uBix Core. Like the Go guide, this is short by design: the toolchain (ruff + mypy strict) settles most style questions that PSR-12/PER settle for PHP, so we record the few genuine choices and let the tools enforce the rest.

**Author:** Christopher W. Olsen — **Applies to:** `py/Ubix` + every `app/*Py` service (first: `RoomSfwCheckerPy`).
**See also:** [`docs/architecture/complete-py-guide.md`](../architecture/complete-py-guide.md) (the framework's shape + the `vsm` shared seam).

## References (the "written standard")

1. [PEP 8](https://peps.python.org/pep-0008/) — style; enforced by ruff's `E`/`W`.
2. [PEP 484](https://peps.python.org/pep-0484/) + [PEP 561](https://peps.python.org/pep-0561/) — typing and shipping inline types; enforced by mypy `strict` + the `py.typed` marker.
3. [FastAPI docs](https://fastapi.tiangolo.com/) — the service framework.

## The decisions

| Area | Choice | Why |
| --- | --- | --- |
| **Format + lint** | **ruff** — `check` (lint + import-sort) and `format` | One fast tool for what would be Black + isort + Flake8. Our analog of phpcbf/phpcs. Rule set: `E, W, F, I, B, C4, UP` (pycodestyle, pyflakes, isort, bugbear, comprehensions, pyupgrade). `line-length = 120`. |
| **Types** | **mypy `strict`** | Max-level static typing — the PHPStan-max analog. Every function typed; no implicit `Any` at our own boundaries. |
| **Tests** | **pytest** | The idiomatic default. `httpx` for the FastAPI surface, `fakeredis` for Redis. No fixtures framework beyond pytest itself unless a real need appears. |
| **Web framework** | **FastAPI** on **gunicorn** + `uvicorn_worker.UvicornWorker` | ASGI; the maintained worker class (the in-tree `uvicorn.workers` shim is deprecated). Apps get theirs from `vsm.create_app`, never bare `FastAPI()`. |
| **Env loading** | `vsm.load_env()` at import | `*Py` analog of `node --env-file`; baked file, K8s-env wins (`override=False`). Never read config before it runs. |
| **Packaging** | `setuptools`, `src/` layout, editable install | `ubix py:install` co-installs `vsm` + every app into one repo-root `.venv`. Apps don't re-declare `vsm`. |

### Version targets are split on purpose

- **Source targets 3.10** (`requires-python = ">=3.10"`, ruff `target-version = "py310"`) — the local dev venv may be 3.10, so don't use 3.11+-only syntax in our code.
- **mypy targets 3.12** (`python_version = "3.12"`) — the *only* runtime (every `*Py` image is `ubuntu:24.04` / Python 3.12). Targeting 3.12 also lets mypy accept 3.12-only stub syntax (`type X = …`) that modern transitive stubs use. Keep both settings as-is when creating a new app; copy them from an existing `pyproject.toml`.

### Stub-less third-party deps — the boundary idiom

Strict mypy trips on dependencies that ship no (or hostile) type info. Treat them as untyped **at the import boundary only** — our own wrapper code stays fully typed — via a `pyproject.toml` override, and add a one-line comment saying why:

```toml
[[tool.mypy.overrides]]
module = ["cv2.*", "nudenet.*", "onnxruntime.*"]
ignore_missing_imports = true
```

This is the established idiom (see `RoomSfwCheckerPy/pyproject.toml` for `nudenet`/`cv2`/`onnxruntime`, and the `follow_imports = "skip"` variant for `redis`'s strict-hostile `ResponseT` unions). It is **additive boundary treatment, not a loosening** of strict mode — do not reach for per-line `# type: ignore` or relax `strict` to dodge a typing problem in our own code.

## Layout & naming (enforced, not litigated)

- **`src/` layout**, package = snake_case app name; `main.py` is the thin ASGI entrypoint (`<pkg>.main:app`), logic in sibling modules. Shared code goes in `py/Ubix/vsm/` (see the architecture guide).
- `snake_case` modules, functions, variables; `PascalCase` classes; `UPPER_SNAKE` constants.
- `from __future__ import annotations` at the top of every module (forward-reference-safe annotations; matches the existing files).
- Public functions/classes carry a docstring; module docstrings state the module's role (see `vsm/app.py` for the house style — imperative, `:param:`/`:returns:`/`:raises:`).
- Prefer explicit keyword-only args for optional config (`def create_app(*, title, lifespan=None)`).

## Running the gate locally

From the repo root (once `ubix py:install` has built the `.venv`), or from a project dir (`py/Ubix`, `app/*Py`):

```bash
.venv/bin/ruff check --fix .     # lint + import-sort, autofix
.venv/bin/ruff format .          # format
.venv/bin/mypy .                 # strict type check
.venv/bin/pytest .               # tests
```

## CI / code:review

Unlike Go, the Python tools **are** folded into the canonical gate. `php bin/ubix code:review` runs **ruff** (`check --output-format=json` + `format --check`), **mypy** (`--show-column-numbers --no-error-summary`, strict), and **pytest** (`--tb=line -q`) **per project**, over `py/Ubix` + every `app/*Py`. A missing `.venv` is a **`tool-did-not-run` violation** on all three (it used to be a silent skip that read as "0 Python violations", which is how an unformatted file reached `dev` on 2026-08-19) — run `ubix py:install` and re-run. `ruff` and `mypy` are **pinned exactly** in `py/Ubix`'s `dev` extras rather than ranged, because CI installs those extras at job time and both tools' findings move with the version; bump them deliberately, in their own commit. Drive the three to 0, same bar as PHP and JS. Do not change the ruff/mypy rule sets or the gate wiring without Christopher Olsen's sign-off (per `CLAUDE.md`); adding a genuinely-new stub-less-dep override is the sanctioned additive exception.
