# uBixCore Open-Source Split — Process log

Narrative, in order, of what we did to turn a product monorepo into a
framework + skeleton that third parties can build on. Written as we go, with
the actual commands, so it can be rewritten into the ubixsys-web
"Build on uBixCore" docs without reconstructing anything. Newest at the bottom.

## 2026-09-02 — Step 0: decide the shape

**Starting point.** One repo (`ubixsys/ubixcore`, forked from project-neptune)
holding the framework (`php/Ubix`, `js/Ubix`), five Sowing.me apps under `app/`,
product SQL/templates/docs, CLI, CI, and the standards. `composer.json` already
says `type: library` and autoloads `Ubix\` from `php/Ubix/`, but the framework
cannot be installed anywhere else: commands find the repo root by walking up from
`__DIR__`, `bin/ubix` globs its own source tree for commands, and product
controllers live inside `Ubix\`.

**The model we chose.** Framework + skeleton (Laravel, Symfony). uBixCore is a
library you require; the skeleton is a template you `create-project` from; your
product is a host project in its own namespace. The framework never lands in a
host's git — it is installed from Composer/npm in the image build, and the host's
committed `composer.lock` is what pins it.

**Decisions taken** (recorded in [`README.md`](README.md)): BSD-3-Clause (BSD-2 was picked first, then switched to match `ubixvault` and `replikate`);
`ubixsys/ubixcore` + `@ubixsys/ubixcore`; skeleton lives in this repo; this repo
stays uBixCore and Sowing.me moves to a new private repo. The new private repo is KITG's company monorepo `kitg/kitg` (Kingdom Impact Technology Group owns Sowing.me and a second platform to come), PSR-4 root `Kitg\` with products as the second segment (`Kitg\SowingMe\`).

**Housekeeping.** Registered the `oss-split` lane in `AGENTS-COORD.md` and opened
a worktree so the three active M1 lanes are untouched:

```bash
git fetch origin
git worktree add ../ubixcore-worktrees/oss-split -b feat/oss-01-plan origin/dev
cp .env_dev ../ubixcore-worktrees/oss-split/.env    # pre-push hook needs it
```

**Next:** OSS-02, a `ProjectRoot` service so nothing in `php/Ubix` computes the
host root from its own file location.
