# Federated Identity — Project

**Status:** Build — plan drafted 2026-09-21; FED-01 (seam) + FED-02 (Google) in !169, tag v0.13.0 on merge. Next FED-03 (Facebook); Apple (FED-04) deferred with kitg's launch set.

A vendor-agnostic **sign-in seam**: let a host accept "Continue with Google / Apple /
Facebook" and passwordless email codes without importing a provider SDK anywhere but
one implementation class, and without re-deriving the security mechanics (state, nonce,
PKCE, ID-token verification, Apple's cross-site callback) in every product.

The first consumer is kitg's Sowing.me, whose product spec lives in kitg
(`docs/surfaces/authentication/`, ADR-009). That spec decides **who an identity
belongs to** — account linking, sign-up vs sign-in, what an unverified email may do.
None of that is here. This project delivers only the mechanism that answers "this
browser just proved it holds Google account `sub=…`, with these verified claims".

| Doc | What |
|---|---|
| [`plan.md`](plan.md) | Boundary, components, flows, security rules, dependency choice, sequenced slices FED-01..06 |

## Decisions

| Decision | Choice | Note |
|---|---|---|
| Shape | Interface + DTOs + one class per provider, the payment seam's pattern (`Ubix\Service\Payment`) | a host binds the providers it enables in DI |
| JWT library | `firebase/php-jwt` in `suggest`, not `require` | JWKS parsing + ES256 signing; a host without social login installs nothing |
| No OAuth client library | Protocol code is ours, over the existing PSR-18 client | three providers do not justify `league/oauth2-client` plus three provider packages |
| Provider tokens | Not returned to the host by default | sign-in needs the verified identity, not an access token; a host that must call the provider later opts in explicitly |
| Account linking | **Out of scope, permanently** | whose account an identity joins is product policy |
| Rate limiter, session rotation | **In** (FED-07, FED-08) | generic plumbing sign-in needs first; every host benefits, and kitg shipped the session-fixation bug that FED-08 makes hard to write |
