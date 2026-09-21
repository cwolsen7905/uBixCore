# Federated Identity — Plan

**Status:** Draft v0.1 · 2026-09-21 · FED-01/02 in !169 · Owner: Christopher W. Olsen
**First consumer:** kitg / Sowing.me — `docs/surfaces/authentication/{srs,technical-spec,architecture}.md` and platform ADR-009 in that repo.

## 1. The boundary

The framework test (kitg `CLAUDE.md`): *would an unrelated product built on uBixCore want
this code verbatim?* "Sign in with Google" is wanted verbatim by a forum, a CRM and a game
alike, so the protocol belongs here. What an identity *means* to a product does not.

| Here (`Ubix\`) — mechanism | Host (`Kitg\`, or any product) — policy |
|---|---|
| Build the provider redirect: `state`, `nonce`, PKCE, scopes | Which providers are enabled, and on which pages |
| Hold the in-flight authorization and bind it to the browser | Where the user lands afterwards (within an allow-list this layer enforces) |
| Exchange the code, verify the ID token, fetch the profile | Whether a verified identity signs in, signs up, or must prove an existing account first |
| Report facts: `subject`, `email`, `emailVerified`, `emailIsPrivateRelay`, names | Whether a given email claim is trusted enough to skip a confirmation email |
| Verify provider → server notices (Apple notifications, Meta data deletion) | What to delete, disable or email when one arrives |
| Issue and verify one-time codes (digest, constant-time compare) | Code length/TTL/attempt policy values, storage tables, rate limits, the email itself |
| Validate a `returnTo` target against configured origins | The list of origins |

Nothing in this project may mention a product noun (supporter, creator, tier), and no
table ships from here: identity and code storage are the host's schema.

## 2. Components

All new directories; the payment seam (`Ubix\Service\Payment`) is the model — one
interface, DTOs, one class per vendor, the vendor library in `suggest`.

### 2.1 The seam

| Type | Path | Role |
|---|---|---|
| `IdentityProviderServiceInterface` | `php/Ubix/Service/Identity/` | `beginAuthorization(BeginAuthorizationRequest): PendingAuthorization` and `completeAuthorization(CallbackParameters, PendingAuthorization): VerifiedIdentity` |
| `IdentityProvider` (enum) | `php/Ubix/Enum/Identity/` | `GOOGLE`, `APPLE`, `FACEBOOK` — provider names are not product vocabulary |
| `BeginAuthorizationRequest` | `php/Ubix/DataTransferObject/Identity/` | registered redirect URI, validated `returnTo`, optional `loginHint`, optional opaque `hostContext` |
| `PendingAuthorization` | same | provider, the `authorizationUrl` to redirect to, `state`, `nonce`, PKCE `codeVerifier`, redirect URI, `returnTo`, `hostContext`, created-at |
| `CallbackParameters` | same | `code`, `state`, `error`, and Apple's `user` form field — built from GET query **or** POST body |
| `VerifiedIdentity` | same | provider, `subject`, `email?`, `emailVerified`, `emailIsPrivateRelay`, `givenName?`, `familyName?`, `hostedDomain?` |

`VerifiedIdentity` is a separate type so a host handler taking one cannot be handed claims
straight off a request — the same reasoning as the payment seam's `VerifiedWebhookEvent`.
Only provider implementations build one, after verification.

### 2.2 Flow state — and why Apple forces it out of the session

`AuthorizationFlowService` keeps a `PendingAuthorization` in PSR-16 cache (10-minute TTL,
keys per `docs/standards/memcache-keys.md`) under a random 32-byte handle, and **takes it
exactly once** (delete on read). The handle travels in its own cookie:

```
Set-Cookie: __Host-ubix_authflow=<handle>; Path=/; Secure; HttpOnly; SameSite=None; Max-Age=600
```

Not in the PHP session, deliberately. Sign in with Apple returns with
`response_mode=form_post` — a cross-site POST from `appleid.apple.com` — which it requires
whenever name or email is requested. Browsers do not send a `SameSite=Lax` cookie on a
cross-site POST, and host session cookies are Lax (kitg authentication FR-60). A flow
stored in the session is therefore invisible on Apple's callback, and the usual "fix" of
relaxing the session cookie to `None` weakens every other request. A separate,
single-purpose, ten-minute cookie is the narrow version.

**The host's session is not visible on the callback either** — the same cross-site POST
that drops the flow cookie's Lax cousin drops the host's session cookie. So anything the
host needs to know on return ("this flow connects a provider to signed-in user 42", "this
flow proves an account for a pending link") travels as `hostContext`: an opaque string
(≤ 1 KiB) the framework stores with the flow and hands back verbatim, never parses and never
sends to the provider. It is trustworthy because it never left the server.

The callback is accepted only if the cookie's handle resolves **and** the returned `state`
equals the stored one (constant-time compare). Either missing is a failed sign-in, never a
fallback.

### 2.3 OIDC ID-token verification

`OidcIdTokenVerifierService`, used by Google and Apple:

- keys from the provider JWKS via `firebase/php-jwt`'s `CachedKeySet` (PSR-6 cache +
  the existing PSR-18 client); an unknown `kid` refreshes the set at most once per minute;
- algorithm allow-list per provider (`RS256` for both today) — `none` and every `HS*` are
  refused before a key is looked up;
- `iss` exact match, `aud` equals the configured client id, `exp`/`iat` with 60 s leeway,
  `nonce` equals `PendingAuthorization::nonce`.

### 2.4 Providers

| Provider | Protocol | Notes that shape the code |
|---|---|---|
| **Google** | OIDC, authorization code + PKCE `S256` | `email_verified` reported as given; `hd` surfaced as `hostedDomain` so a host can apply Google's own guidance (treat the email claim as authoritative only for `gmail.com` or a matching `hd`). |
| **Apple** | OIDC, code flow, `response_mode=form_post` | The client secret is itself a JWT: ES256, signed with the team's `.p8` key (from Vault), `iss`=team id, `sub`=services id, `aud`=`https://appleid.apple.com`, cached ≤ 1 h. `is_private_email` → `emailIsPrivateRelay`. Name arrives **only on the first authorization**, in the unsigned `user` form field — surfaced as a display hint, never as a verified claim. |
| **Facebook** | OAuth 2.0 (web Facebook Login is not OIDC) | Code → access token → Graph `/me?fields=id,email,first_name,last_name` with `appsecret_proof`. The token is checked against **our** app id (`debug_token`) before its profile is trusted, to refuse a token minted for another app. Graph does not assert email verification, so `emailVerified` is **always false** from Facebook, and `email` may be absent. |

Provider access/refresh tokens are discarded after the identity is built. See §6 Q1 for
the one case that may need Apple's refresh token kept.

### 2.5 Provider → server notices

| Type | Verifies | Returns |
|---|---|---|
| `AppleServerNotificationVerifier` | Apple-signed JWT (same JWKS path as §2.3) | typed event: `email-disabled`, `email-enabled`, `consent-revoked`, `account-delete`, with `subject` |
| `FacebookSignedRequestParser` | Meta `signed_request` (base64url payload, HMAC-SHA256 with the app secret, constant-time) | the Facebook `user_id` whose data-deletion was requested |

Meta refuses App Review without a data-deletion callback or instructions URL; Apple sends
notifications when a user stops sharing email or deletes their Apple account. The host
decides what each one does.

### 2.6 One-time codes

`OneTimeCodeService`:

- `issue(purpose, subjectKey)` → the plaintext code (6 digits, `random_int`) and its
  digest; `verify(presented, digest, purpose, subjectKey)` → bool, constant-time;
- digest = HMAC-SHA256 with a server **pepper** from Vault over `purpose|subjectKey|code`.
  A six-digit code has a 10⁶ keyspace, so a plain hash is reversible offline in
  milliseconds; the pepper is what makes a leaked table useless. Binding `purpose` and
  `subjectKey` stops a code issued for one email or one action verifying another;
- expiry, attempt limits and rate limits are the **host's** (with its table). The
  framework says so in the docblock: at 10⁶ the code is only as strong as the attempt cap.

### 2.7 Return-target validation

`ReturnToValidatorService` accepts a same-origin relative path (`/x`, not `//x`, no `\`, no
scheme, no userinfo) or an absolute URL whose origin is in a configured allow-list —
reusing the origin list a host already gives `CorsMiddleware`. Everything else falls back
to the host's default. An OAuth callback that redirects wherever `returnTo` says is an
open redirect with a login page in front of it.

### 2.8 Rate limiting (FED-07)

`RateLimiterService` in `php/Ubix/Service/RateLimit/` — generic, not identity-specific; it
lands with this project because sign-in is the first thing that needs it (email-code
requests per address and per IP, code attempts, confirmation resends).

- `hit(scope, subject, limit, windowSeconds)` → `RateLimitResult` DTO: `allowed`,
  `remaining`, `retryAfterSeconds`. Fixed window per `(scope, subject)`.
- Keys follow the reservation already in `docs/standards/memcache-keys.md`:
  `UBIX_RATE_LIMIT_<SCOPE>_<hash>` — the subject (an email, an IP) is SHA-256-hashed, so no
  address sits in cache in the clear and key length is bounded.
- Counting uses the backend's atomic increment where it has one (memcached). PSR-16 alone has
  no atomic increment, so on a plain PSR-16 cache the limit is approximate under concurrent
  requests; the docblock says so. For abuse limits "about 5 per hour" is the requirement,
  not "exactly 5".
- The host picks scopes and numbers; the framework ships no defaults that mean anything.

### 2.9 Session rotation at sign-in (FED-08)

`SessionService::startAuthenticatedSession(array $payload, string $key = 'user')`: rotate
the session id (`session_regenerate_id(true)`) and **then** write the payload — the order is
the whole point. Session fixation is the bug every app reintroduces by writing
`$_SESSION['user']` directly: kitg did, on two of its three sign-in paths (kitg !151). One
framework call makes the safe order the easy one; what goes in the payload stays the host's.

## 3. Flows

```
Google / Facebook                                  Apple
browser → host  GET /…/{provider}/start            (same)
host: beginAuthorization → store pending,
      set __Host-ubix_authflow, 302 → provider
provider → browser → host  GET /…/callback         provider → browser → host  POST /…/callback
      ?code&state                                        (form_post: code, state, id_token, user)
host: take pending by cookie handle, compare state, completeAuthorization
      → VerifiedIdentity → host policy (sign in / sign up / prove existing account)
```

The callback route must accept POST for Apple, and must not sit behind a CSRF-token check if
a host adds one — its protection is `state` + the flow cookie + `nonce`.

## 4. Security rules (tested, not advisory)

1. `state`, `nonce` and (where supported) PKCE on every flow; flow state single-use.
2. ID tokens verified per §2.3 before any claim is read; no claim from an unsigned source
   is ever marked verified.
3. Identities are keyed by `(provider, subject)`. The docblocks say so: email is a hint
   that changes, and Apple's may be a relay address.
4. Secrets (client secrets, Apple `.p8`, Meta app secret, OTP pepper) come from Vault via
   `VaultCredentialResolverService`; nothing in `.env` of a deployed image.
5. Nothing logs a code, token, `state`, `nonce` or handle beyond a short prefix.
6. Redirect URIs are exact-match registered values from config, never derived from the
   request host.

## 5. Slices

| ID | Slice | Tag |
|---|---|---|
| FED-01 | Seam: interface, enum, DTOs, `AuthorizationFlowService` + cookie helper, `ReturnToValidatorService` | with FED-02 |
| FED-02 | `OidcIdTokenVerifierService` + `GoogleIdentityProviderService`; `firebase/php-jwt` in `suggest` | **v0.13.0** |
| FED-03 | `FacebookIdentityProviderService` (`appsecret_proof`, `debug_token`) + `FacebookSignedRequestParser` | v0.14.0 |
| FED-04 | `AppleIdentityProviderService` (client-secret JWT, form_post callback) + `AppleServerNotificationVerifier`; accepts the native bundle id as a second audience | **deferred** — kitg launches without Apple (2026-09-21) and revisits it with a native iOS app |
| FED-05 | `OneTimeCodeService` | may ride any of the above |
| FED-06 | `docs/architecture/complete-php-guide.md` section: wiring a provider in `Dependencies.php`, the callback route, the cookie | with the last code slice |
| FED-07 | `RateLimiterService` + `RateLimitResult` (§2.8) | with FED-05 — code requests are its first user |
| FED-08 | `SessionService::startAuthenticatedSession()` (§2.9) | any; small, can go first |

All additive, so each is a MINOR tag. Tests use locally generated keys and signed fixture
tokens — no network, no real provider — plus one negative test per §4 rule (wrong `aud`,
expired, bad `nonce`, `alg: none`, replayed flow handle, foreign-app Facebook token).

## 6. Open questions

| # | Question | Default |
|---|---|---|
| Q1 | Apple requires apps that offer account deletion to **revoke** the user's Apple tokens, which needs the refresh token kept. Web-only today. | Discard tokens; revisit when a native iOS app is scoped — then store the refresh token encrypted, host-side. |
| Q2 | PKCE for Apple and Facebook web flows: supported and honoured? | Send it; do not rely on it — `state` + `nonce` + flow cookie carry the security. Verify during FED-03/04. |
| Q3 | Passkeys (WebAuthn) are the obvious next seam member. | Not in this project. |

## Document control
| Version | Date | Change |
|---|---|---|
| 0.1 | 2026-09-21 | FED-07 (rate limiter) and FED-08 (session rotation helper) added the same day: both generic, both needed by kitg's sign-in, neither existed. Initial plan, written alongside kitg's authentication spec revision and ADR-009. `hostContext` added the same day: kitg's TDS showed that connect-from-settings cannot read the host session on Apple's callback. |
