# Go Coding Guidelines

How we write Go in uBix Core. Go's toolchain settles most style questions
that PSR-12/PER settle for PHP, so this is short by design — where the language
has one canonical way, we use it; the few genuine choices are recorded here.

**Author:** Christopher W. Olsen — **Applies to:** every `app/*Go` service (first: `RealtimeFanoutGo`).

## References (the "written standard")

1. [Effective Go](https://go.dev/doc/effective_go) — idioms.
2. [Google Go Style Guide](https://google.github.io/styleguide/go/) — the closest thing to a formal spec beyond Effective Go; our tie-breaker.
3. [Go Code Review Comments](https://go.dev/wiki/CodeReviewComments) — the review checklist.

## The decisions

| Area | Choice | Why |
| --- | --- | --- |
| **Formatting** | **gofumpt** (stricter superset of `gofmt`) | Formatting is not a matter of opinion in Go; gofumpt tightens the few cases `gofmt` leaves loose. Output stays `gofmt`-compatible. |
| **Linting** | **golangci-lint**, curated strict set (`.golangci.yml`) | The de-facto aggregate linter (bundles `staticcheck`, `govet`, `errcheck`, …). Our analog of max-level PHPStan + custom sniffs. |
| **Layout** | idiomatic **`cmd/` + `internal/`** | What the Go team and idiomatic projects use; `internal/` enforces import privacy. We deliberately do **not** adopt `golang-standards/project-layout` (popular but explicitly unofficial, over-structured for a service). |
| **Testing** | stdlib **`testing`**, table-driven subtests | The idiomatic default; no assertion library. Add a dependency only when a real need appears. |
| **Errors** | wrap with `fmt.Errorf("...: %w", err)`; check with `errors.Is/As` | Enforced by `errorlint`. |
| **Logging** | stdlib **`log/slog`** (JSON handler) | Structured logs, no third-party logger. |
| **Modules** | Go modules; commit `go.mod` **and** `go.sum` | Reproducible, checksum-verified builds + a cacheable module-download layer. |

### Tool versions are pinned (like phpcs/phpstan)

`gofumpt` and `golangci-lint` are installed at pinned versions in the
[`ubix-go` base image](https://example.com/k8s/BaseImages) (`ubixsys/ubix-go`),
so every build and CI lint job uses one version set. Bump them **deliberately**,
together — never drift silently. (Note: golangci-lint v2 exists; we pin v1.x for
config stability — migrating to v2 is a deliberate, separate change.)

## Layout

```
app/<Name>Go/
  go.mod  go.sum
  .golangci.yml
  cmd/<binary>/main.go     entrypoint only — wire deps, call into internal/
  internal/<pkg>/          all real code; not importable from outside the module
  internal/<pkg>/*_test.go tests live beside the code
```

`main` stays thin: build config, construct dependencies, start/stop. Put logic in
`internal/` packages so it is testable without `main`.

## Naming & style (enforced, not litigated)

- `MixedCaps` / `mixedCaps`, never `snake_case`; exported identifiers start upper.
- Exported symbols carry a doc comment starting with the symbol name (`revive`'s `exported` rule).
- Short names in small scopes (`i`, `r`, `w`, `nc`); descriptive names for package-level symbols.
- Accept interfaces, return structs. Keep interfaces small and defined at the consumer.
- No naked returns in non-trivial functions; handle every error (`errcheck`).

## Running the gate locally

The `ubix-go` base carries both tools; from an `app/*Go` dir:

```bash
gofumpt -l -w .            # format (CI checks it via golangci-lint)
golangci-lint run ./...    # the lint gate
go test ./...              # tests
```

## CI / code:review

The Go leg is inside the machine gate (landed 2026-07-29 with Christopher
Olsen's sign-off — benchmark item SB-03), at both layers:

- **`php bin/ubix code:review`** runs **`golangci-lint`** and **`go test`**
  as first-class tools (`--golangci-lint=on|off`, `--go-test=on|off`) over every
  `app/*Go` module. Like the Python tools with a missing `.venv`, they are
  **skipped when the Go toolchain isn't on PATH** (Go apps build in Docker, so
  most local machines don't carry it) — a local green without the toolchain is
  not a Go verdict.
- **CI is the authoritative run:** the `go-checks-dev` job executes
  `golangci-lint run ./...` + `go test ./...` for each `app/*Go` on the
  `ubix-go` base image (same pinned tool versions the build uses). The
  `*Go` build loop still builds each service on that base.
