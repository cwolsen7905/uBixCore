# HelloJs — the React Router app template

A minimal, working [React Router v8](https://reactrouter.com) app in framework mode (React 19 +
TypeScript). Copy it to start a frontend; delete it if this project has none.

```sh
cp -r app/HelloJs app/<YourName>Js
```

Then change **all** of the following, or the new app will collide with this one:

| Where | What |
|---|---|
| `package.json` | `name` |
| `{dev,staging,main}-deploy.yaml` | `metadata.name`, `matchLabels.app`, `template.labels.app`, container `name`, image tag suffix |
| `{dev,staging,main}-service.yaml` | `metadata.name`, `selector.app` |
| `{dev,staging,main}-ingress.yaml` | `metadata.name`, `host`, backend service name |
| `.gitlab-ci.yml` | add the app to `JS_APPS` (and `JS_TEST_APPS`) |
| `app/lib/api.ts` | the real API hosts |

**Why all of them.** `bin/deploy.sh` applies app directories alphabetically. Two Deployments that
share a name, selector or Ingress host overwrite each other and the loser is silent — in the project
this template came from, a second frontend served the first one's build for months while looking
correctly configured. Changing *some* identity fields is the failure mode; that is why they are
enumerated here rather than left to a careful reader.

## Commands

```sh
npm install
npm run dev       # vite dev server
npm run check     # react-router typegen && tsc --noEmit
npm run lint      # eslint + prettier
npm test          # vitest (jsdom + @testing-library/react)
npm run build && npm start   # production build, served by react-router-serve
```

## What the template is demonstrating

It is deliberately tiny, but every choice in it is load-bearing:

- **SSR on** (`react-router.config.ts`). The root loader runs before first paint.
- **The loader forwards the request cookie.** A loader runs server-side with no browser cookie jar;
  an authenticated call that does not forward it is an anonymous call.
- **The upstream call is bounded** with an `AbortController` and degrades instead of throwing. An
  unreachable API must not hold the page open.
- **Parent data is read by route id** (`useRouteLoaderData('root')`), not drilled through props.
- **CSS Modules** are configured (`localsConvention: 'camelCaseOnly'`). React does not scope
  component styles, and real apps use class names that collide globally.
- **vitest uses jsdom, not browser mode.** Browser mode needs Playwright browsers a plain node CI
  image does not carry, so those tests silently never run.
- **The k8s manifests are excluded from prettier.** They are infrastructure that happens to live in
  the app directory; letting prettier own them couples `npm run lint` to infra edits.

When this app grows an authenticated area, **split public and authenticated layouts** rather than
gating inside one layout — see `app/routes.ts`.

Full architecture: [`docs/architecture/complete-js-guide.md`](../../docs/architecture/complete-js-guide.md)
in the uBixCore repo.
