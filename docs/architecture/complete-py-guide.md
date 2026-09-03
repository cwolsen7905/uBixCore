# Complete Python Guide

How Python is structured in uBix Core — the **third language leg** alongside PHP (`app/*Api`/`*Web`) and JS (`app/*Js`). Python services are the `*Py` app type: small, typed, FastAPI-based services for work that wants the Python ecosystem (local-AI/ML, image processing, queue consumers) rather than the PHP request/response stack. `RoomSfwCheckerPy` is the first.

**Author:** Christopher W. Olsen — **Applies to:** `py/Ubix` + every `app/*Py`.
**See also:** [`docs/standards/py-coding-guidelines.md`](../standards/py-coding-guidelines.md) (the ruff/mypy/pytest bar), [`docs/architecture/monorepo.md`](monorepo.md) (app-type table), [`docs/standards/unit-testing.md`](../standards/unit-testing.md) (pytest conventions).

## The three-language symmetry

uBix Core runs one philosophy per language: a **shared framework package** named `Ubix`, consumed by thin per-app code. The Python leg mirrors the other two deliberately — same shape, idiomatic tooling.

| Language | Shared lib | Namespace | Apps | Packaging | Image |
| --- | --- | --- | --- | --- | --- |
| PHP | `php/Ubix/` | `Ubix\` | `app/*Api`, `app/*Web` | composer autoload | one shared image, `APP_NAME` selects |
| JS | `js/Ubix/` | `vsm` | `app/*Js` | npm workspace | per-app node image |
| **Python** | **`py/Ubix/`** | **`vsm`** | **`app/*Py`** | editable install | per-app image on a shared base |

The load-bearing idea is the same as everywhere else in uBix Core: **reuse lives at the seam.** Anything a second `*Py` app would want — app bootstrap, config loading, a Redis client, (later) bearer auth / structured logging / a uBix Core-API client — belongs in `py/Ubix` under the `vsm` namespace, built as the reusable form on first use. App packages hold only what is genuinely app-specific.

## The shared framework — `py/Ubix` (the `vsm` package)

`py/Ubix/vsm/` is the importable shared library. Its public API is whatever `vsm/__init__.py` re-exports; today:

```python
from vsm import create_app, create_redis_client, load_env
```

- **`create_app(*, title, lifespan=None) -> FastAPI`** (`vsm/app.py`) — the FastAPI application factory, the Python analog of the Slim `Dependencies`/`Middleware` bootstrap. Returns a configured app with the shared baseline (a `/health` liveness route today). Pass a FastAPI `lifespan` to run startup/shutdown work — e.g. a background queue consumer alongside the HTTP surface. Bearer auth, structured logging, and JSON error conventions will land **here** as the seam grows, not in each app.
- **`load_env(path=…) -> None`** (`vsm/env.py`) — loads the baked env file into `os.environ`, the `*Py` counterpart to the Node images' `node --env-file=.env`. Call it **at import, before any config read**. `override=False`, so K8s-set env wins over the baked file (matching the Node/PHP precedence); a no-op when the file is absent.
- **`create_redis_client(server=None, *, db=0, decode_responses=True) -> redis.Redis`** (`vsm/redis_client.py`) — builds a client from a `host:port` string (defaults to `REDIS_SERVER`). Redis is the shared queue/cache seam for `*Py` apps; reuse this factory rather than constructing `redis.Redis` per app.

**`vsm` ships its inline types** — `vsm/py.typed` (PEP 561) is packaged as `package-data`, so consumers get `vsm`'s types under mypy `strict` with no stub package.

**When you add a shared primitive:** put it in a module under `vsm/`, re-export it from `vsm/__init__.py`, add it to `__all__`, and give it a test in `py/Ubix/tests/`. That is the whole "extend the framework" ritual — the same instinct as adding a DataType/Service in `php/Ubix` or a component in `js/Ubix`.

## App structure — `app/<Name>Py`

`src/` layout, one installable package per app:

```
app/<Name>Py/
  pyproject.toml                  package + ruff/mypy/pytest config
  neptune.json                    app metadata (exposure, etc.)
  src/<name>/                     the app package (snake_case)
    __init__.py
    main.py                       ASGI entrypoint — `main:app`
    <module>.py                   app logic, one concern per module
  tests/                          pytest suite, mirrors src modules
    test_<module>.py
  {dev,sandbox,staging,main}-{deploy,service,ingress}.yaml   K8s manifests (12)
```

- **Package name / import root** is the snake_case app name (`room_sfw_checker`), discovered from `src/` via `[tool.setuptools.packages.find] where = ["src"]`.
- **ASGI target** is `<package>.main:app` — what gunicorn/uvicorn serve.
- **`main.py` is the thin entrypoint** (the Go `cmd/main.go` instinct): call `load_env()` first, build the app via `vsm.create_app(...)`, wire routes/deps, and pass a `lifespan` if the app runs a background worker. Keep real logic in sibling modules (`detector.py`, `policy.py`, `consumer.py`, …) so it's testable without booting the server.

Minimal shape:

```python
from vsm import create_app, load_env

load_env()                       # baked env -> os.environ, before any config read
app = create_app(title="Room SFW Checker")

@app.get("/rate")
def rate() -> dict[str, str]:
    ...
```

Hybrid HTTP + worker shape (a background consumer alongside the HTTP surface) uses a FastAPI `lifespan` passed to `create_app` — see `RoomSfwCheckerPy`'s consumer. The worker and any ad-hoc HTTP route call the **same in-process function**; neither calls the other over HTTP.

## Configuration & environment

- Config comes from `os.environ` after `load_env()`. Read env at module import via `os.environ.get(...)`; because `load_env()` ran first, the baked values are present in time.
- uBix Core provisions shared env (`REDIS_SERVER`, etc.). Server-to-server calls use in-cluster addresses per tier (see [`monorepo.md`](monorepo.md) / the deploy conventions), not public hosts.
- **K8s-set env always wins** over the baked `.env`/`.env_prod` (`load_env` uses `override=False`).

## Redis seam

The first cross-`*Py` shared datastore — RoomSfwChecker's screencap queue + per-room SFW state. Get a client from `create_redis_client()` (reads `REDIS_SERVER`). Tests use `fakeredis` (an in-memory fake with `WATCH`/`MULTI`) so consumer/debounce logic is testable without a live Redis.

## Packaging & the toolchain venv

- Apps do **not** re-declare `vsm` as a dependency — `ubix py:install` co-installs `py/Ubix[dev]` and every `app/*Py` (editable) into one repo-root `.venv` (gitignored, like `vendor/` and `node_modules/`). So `from vsm import …` resolves in dev, tests, and the gate.
- The only manual prerequisite is the system packages: `sudo apt install -y python3-venv python3-pip`, then `php bin/ubix py:install` (idempotent) owns the rest.
- Per-app config (deps, ruff/mypy/pytest) lives in each `pyproject.toml`. Because the gate type-checks each app in isolation (cwd = the app dir), every app sets `mypy_path = "../../py/Ubix"` so `vsm`'s source resolves standalone.

## Tests

pytest, one `tests/` dir per project (`py/Ubix/tests`, `app/*Py/tests`), `test_<module>.py` mirroring the source modules; `testpaths = ["tests"]` in `pyproject.toml`. `httpx` drives the FastAPI surface; `fakeredis` stands in for Redis. See [`docs/standards/unit-testing.md`](../standards/unit-testing.md).

## Image & deploy

Each `*Py` app builds its **own image on a shared Python base** (`ubuntu:24.04` / Python 3.12), served by **gunicorn** with the maintained `uvicorn_worker.UvicornWorker` ASGI worker class. The Dockerfile bakes the monorepo-root env file (`.env` dev / `.env_prod` staging+prod) that `load_env()` reads. `/health` backs the K8s liveness probe. The per-tier manifest matrix + `app:build`/`app:deploy` flow is the same as the other legs — see the **neptune-deploy-manifests** skill and [`monorepo.md`](monorepo.md).

## Relationship to the gate

`ruff`, `mypy`, and `pytest` are three of the `php bin/ubix code:review` tools, run per-project over `py/Ubix` + every `app/*Py`. An absent `.venv` is reported as a **`tool-did-not-run` violation** on all three rather than a silent skip — run `ubix py:install` so the Python leg is actually held to the bar. The `ruff`/`mypy` versions in the `dev` extras are pinned exactly so a local venv and CI's install-at-job-time agree on identical code. The conventions those tools enforce are in [`docs/standards/py-coding-guidelines.md`](../standards/py-coding-guidelines.md).
