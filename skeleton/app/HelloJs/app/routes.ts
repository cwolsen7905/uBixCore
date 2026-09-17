import { type RouteConfig, index } from '@react-router/dev/routes';

// Explicit route configuration, not filesystem convention.
//
// When this app grows an authenticated area, split it from the public one with
// two `layout(...)` entries rather than gating inside a single layout. An app
// with one layout that gates on "is there a user" eventually gates something
// that must stay public -- a shareable page, a password reset, the login page
// itself -- and that is awkward to retrofit.
export default [index('routes/home.tsx')] satisfies RouteConfig;
