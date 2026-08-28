# uBix Core: Complete PHP Architecture Guide

**Version:** 1.5
**Date:** 2026-08-05
**PHP Version:** 8.3+
**Framework:** Slim 4 + PHP-DI 7

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Major Object Types](#major-object-types)
3. [Architectural Patterns](#architectural-patterns)
4. [Key Base Classes](#key-base-classes)
5. [Naming Conventions](#naming-conventions)
6. [Data Flow Patterns](#data-flow-patterns)
7. [Integration Patterns](#integration-patterns)
8. [Application Bootstrap](#application-bootstrap)

---

## Executive Summary

uBix Core is a modern PHP 8.3+ monorepo implementing a multi-application platform with a clean, layered architecture. The codebase follows SOLID principles, uses strong typing throughout, and implements multiple design patterns including Repository, Service Layer, DTO, and Value Object patterns.

### Core Principles

1. **Type Safety First** - Custom DataTypes eliminate primitive obsession
2. **Layered Architecture** - Clear separation: Controller → Service → Repository → Model
3. **SOLID Compliance** - Enforced via custom PHPCS sniffs
4. **PSR Standards** - PSR-3 (Logger), PSR-7 (HTTP), PSR-15 (Middleware), PSR-16 (Cache), PSR-18 (HTTP Client)
5. **Immutability** - Readonly DTOs, immutable DataTypes
6. **Validation at Boundaries** - HTTP layer validates all input

### Architecture Diagram

```
┌──────────────────────────────────────────────────────────────┐
│                      HTTP Request                             │
└────────────────────────┬─────────────────────────────────────┘
                         ↓
┌──────────────────────────────────────────────────────────────┐
│                    Middleware Layer                           │
│  • Authentication (Bearer Token)                              │
│  • Request Normalization (IP, Host)                           │
│  • Body Parsing                                               │
└────────────────────────┬─────────────────────────────────────┘
                         ↓
┌──────────────────────────────────────────────────────────────┐
│                   Controller Layer                            │
│  • Request parsing via Payloads                               │
│  • Validation error handling                                  │
│  • Delegation to Services                                     │
│  • Response formatting                                        │
└────────────────────────┬─────────────────────────────────────┘
                         ↓
┌──────────────────────────────────────────────────────────────┐
│                    Service Layer                              │
│  • Business logic orchestration                               │
│  • Cross-entity operations                                    │
│  • Repository coordination                                    │
│  • Returns DTOs                                               │
└────────────────────────┬─────────────────────────────────────┘
                         ↓
┌──────────────────────────────────────────────────────────────┐
│                   Repository Layer                            │
│  • Data access abstraction                                    │
│  • Query building via Options DTOs                            │
│  • Returns domain Models                                      │
│  • Change tracking for updates                                │
└────────────────────────┬─────────────────────────────────────┘
                         ↓
┌──────────────────────────────────────────────────────────────┐
│                    Database Layer                             │
│  • Read/Write separation                                      │
│  • PDO abstraction                                            │
│  • Transaction support                                        │
└──────────────────────────────────────────────────────────────┘
```

---

## Major Object Types

### 1. Controllers

**Location:** `php/Ubix/Controller/`
**Base Class:** `AbstractController`
**Purpose:** Handle HTTP requests, coordinate business logic, return responses

#### Characteristics
- Extend `AbstractController` for common rendering methods
- Injected with Logger, TemplateRenderer, JsonService
- Methods are controller actions (accept PSR-7 Request/Response)
- Return PSR-7 Response objects
- **Always thin** - delegate business logic to Services

#### Base Class Features

```php
abstract class AbstractController {
    protected Logger $logger;
    protected TemplateRenderer $templateRenderer;
    protected JsonService $jsonService;

    // Helper methods
    protected function renderTemplate(Response $response, string $template, StatusCode $code): Response
    protected function renderJson(Response $response, array $output, StatusCode $code): Response
    protected function renderJsonWithPayload(Response $response, ResponsePayload $payload, StatusCode $code): Response
    protected function redirect(Response $response, string $url, int $httpCode): Response
    protected function sendToTemplate(string $name, mixed $value): void
}
```

#### Example Controller

**File:** `php/Ubix/Controller/AffiliateApi/AttributionController.php`

```php
final class AttributionController extends Controller
{
    public function __construct(
        protected Logger $logger,
        protected TemplateRenderer $view,
        protected JsonService $jsonService,
        protected AttributionService $attributionService,
        protected AffiliateService $affiliateService,
    ) {
        parent::__construct($logger, $view, $jsonService);
    }

    public function firstMoney(Request $request, Response $response): Response
    {
        try {
            // Parse and validate request
            $requestData = FirstMoneyRequestPayload::getRequest($request);
            assert($requestData instanceof FirstMoneyRequestPayload);
        } catch (DtoException $e) {
            // Return validation errors
            return $this->renderJson($response, [
                'fields'     => $e->getDto()->errors ?? [],
                'message'    => $e->getMessage(),
                'statusCode' => $e->getCode(),
            ], StatusCode::BAD_REQUEST);
        }

        // Delegate to service
        $returnData = $this->attributionService->firstMoney(
            $requestData->userId,
            $requestData->amount,
            $requestData->envMpCode,
            $requestData->transactionDate,
            $requestData->clickId,
            $requestData->voluumClickId
        );

        // Format response
        $responsePayload = AttributionResponsePayload::getResponse($returnData);
        return $this->renderJsonWithPayload($response, $responsePayload);
    }
}
```

#### When to Create Controllers
- New HTTP endpoints
- Need template rendering or JSON response helpers
- Standardized error handling required

#### Controller Anti-Patterns to Avoid
- ❌ Business logic in controllers
- ❌ Direct repository access (use services)
- ❌ Complex validation logic (use Payloads)
- ❌ Database queries

---

### 2. Services

**Location:** `php/Ubix/Service/`
**Base Class:** None (final classes, interface-driven)
**Purpose:** Contain business logic, orchestrate repositories, implement complex operations

#### Characteristics
- Final classes (not meant to be extended)
- Injected with repositories and other services
- **Stateless** (no instance state between requests)
- May implement interfaces for abstraction
- Contain domain business logic

#### Service Types

**1. Domain Services** - Business logic
- `AttributionService` - User attribution logic
- `AffiliateService` - Affiliate operations
- `PlatformUserService` - User management

**2. Infrastructure Services** - Technical concerns
- `JsonService` - JSON encoding/decoding
- `SlugService` - URL slug generation
- `XvtService` - Legacy system integration
- `FilterService` - Content filtering

**3. SQL Services** - Database abstraction
- `AbstractPdoSqlService` - Base PDO wrapper
- `MysqlPdoSqlService` - MySQL implementation
- `SqlitePdoSqlService` - SQLite implementation

**4. External Services** - Third-party integrations
- `BlobService` - File storage (S3, Filestack)
- `EmailService` - Email sending
- `GeolocationService` - IP geolocation

#### Example Service

**File:** `php/Ubix/Service/AttributionService.php`

```php
final class AttributionService
{
    public function __construct(
        private Logger $logger,
        private AffiliateReader $affiliateReader,
        private AffiliateWriter $affiliateWriter,
        private PlatformUserService $platformUserService,
        private BillingTransactionReader $transactionReader,
        private JsonService $jsonService,
        private AffiliateService $affiliateService,
        private AttributionLogWriter $attributionLogWriter,
        private PlatformUserReader $platformUserReader,
        private ClickIdLogReader $clickIdLogReader,
    ) {}

    /**
     * First money attribution logic
     */
    public function firstMoney(
        PlatformUserId $userId,
        UsdCurrency $amount,
        MpCode $envMpCode,
        ?UbixDateTime $transactionDate,
        ?Varchar $clickId,
        ?Varchar $externalClickId
    ): Attribution {
        // Get required data from multiple repositories
        $affiliate = $this->affiliateReader->getAffiliateByMpCode($envMpCode);
        $earliestAccount = $this->platformUserReader->getEarliestAccount($userId);
        $mediaBuyDetails = $this->affiliateService->getMediaBuyingDetails($affiliate);

        // BUSINESS RULE: Determine attribution
        if ($mediaBuyDetails->isMediaBuying) {
            $attributedMpCode = $envMpCode; // Media buy always wins
        } elseif ($this->isWithinProtectionWindow($earliestAccount, $transactionDate)) {
            $attributedMpCode = $earliestAccount->mpCode; // Protected account
        } else {
            $attributedMpCode = $envMpCode; // Normal attribution
        }

        // Persist attribution log
        $log = new AttributionLog(...);
        $this->attributionLogWriter->save($log);

        // Update user
        $user = $this->platformUserReader->getById($userId);
        $user->setMpCode($attributedMpCode);
        $this->platformUserWriter->save($user);

        // Return DTO
        return new Attribution(
            mpCode: $attributedMpCode,
            affiliateId: $affiliate->getId(),
            isMediaBuy: $mediaBuyDetails->isMediaBuying
        );
    }
}
```

#### When to Create Services
- Business logic spanning multiple entities
- Complex operations requiring multiple repository calls
- Integration with external systems
- Reusable logic used across controllers

#### Service Anti-Patterns to Avoid
- ❌ Direct HTTP request/response handling
- ❌ SQL queries (use repositories)
- ❌ Template rendering
- ❌ Stateful services (instance variables)

---

### 3. Repositories

**Location:** `php/Ubix/Repository/`
**Pattern:** Reader/Writer Segregation
**Purpose:** Abstract data access, provide query interface, handle persistence

#### Repository Structure

Each entity has its own folder with:
- `{Entity}ReaderInterface.php` - Read operations
- `{Entity}WriterInterface.php` - Write operations (optional)
- `{Entity}SqlRepository.php` - Concrete implementation

**Example Structure:**
```
Repository/
├── Affiliate/
│   ├── AffiliateReaderInterface.php
│   ├── AffiliateWriterInterface.php
│   └── AffiliateSqlRepository.php
├── Performer/
│   ├── PerformerReaderInterface.php
│   └── PerformerSqlRepository.php
└── PlatformUser/
    ├── PlatformUserReaderInterface.php
    ├── PlatformUserWriterInterface.php
    └── PlatformUserSqlRepository.php
```

#### Reader/Writer Pattern

**Reader Interface** - Query operations
```php
interface PerformerReaderInterface
{
    public function getByUsername(string $username): array;
    public function getById(int $id): array;
    public function getBySlug(string $slug): array;
}
```

**Writer Interface** - Mutation operations
```php
interface AffiliateWriterInterface
{
    public function save(Affiliate $affiliate): void;
    public function deleteById(AffiliateId $affiliateId): void;
}
```

**Repository Implementation** - Implements both
```php
final class PerformerSqlRepository implements PerformerReaderInterface
{
    public function __construct(
        private Logger $logger,
        private SqlService $sqlService
    ) {}

    public function getByUsername(string $username): array
    {
        $options = new PerformerOptions(username: $username, limit: 1);
        return $this->query($options);
    }

    /**
     * Private query builder using Options DTO
     */
    private function query(PerformerOptions $options): array
    {
        $sql = 'SELECT * FROM performers WHERE 1=1';
        $parameters = [];

        if ($options->id !== null) {
            $sql .= ' AND id = :id';
            $parameters['id'] = $options->id;
        }

        if ($options->username !== null) {
            $sql .= ' AND username = :username';
            $parameters['username'] = $options->username;
        }

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        // Execute and map to Models
        $performers = [];
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $performers[] = new Performer(
                id: new PerformerId($row['id']),
                username: new Varchar($row['username']),
                // ... map all properties from row
            );
        }

        return $performers;
    }
}
```

#### Repository Write Semantics — the database owns defaults

**Rule: a `null` model value is omitted from the `INSERT`/`UPDATE`; the database is the sole authority for a column's default. PHP never fabricates one.**

Models carry nullable DataType properties. When a property is `null` it means "no value supplied" — the repository must leave that column out of the written statement so the schema's `DEFAULT` applies (on `INSERT`) or the existing value is preserved (on `UPDATE`). Writing a PHP-side fallback such as `->value ?? 0` or `->value ?? ''` is a smell: it duplicates (or, worse, contradicts) the schema default and hides whether the column actually has one.

```php
// ❌ PHP fabricates the default — drift waiting to happen
$parameters['sender_ip'] = $message->getSenderIp()?->value ?? '';

// ✅ Omit when null; the column's `DEFAULT ''` does the work
$senderIp       = $message->getSenderIp()?->value;
$senderIpClause = $senderIp !== null ? ', sender_ip = :senderIp' : '';
// ...build the SQL with $senderIpClause appended...
if ($senderIp !== null) {
    $parameters['senderIp'] = $senderIp;
}
```

Consequences:
- A bound parameter is added **only** when its column appears in the SQL (PDO runs with emulation off, so a named parameter that is absent from the statement raises `HY093`).
- The same conditional-fragment approach already used for reads (the `query()` builder above only appends a `WHERE` clause when its option is non-null) applies symmetrically to writes.

**The other case: required columns with no default.** Some columns are `NOT NULL` with **no** schema default *by design* — the value is genuinely mandatory and there is no sensible default (e.g. MESSAGING `has_attachments`). For these the rule is the mirror image: **the caller supplies the value; neither PHP nor the schema fabricates one.** The repository writes the model's value directly (`$message->getHasAttachments()?->value`) with no `?? <literal>` fallback, so a caller that forgets to set a required field fails loudly (strict `sql_mode` rejects the `NULL`) rather than silently persisting a fabricated `0`/`''`. Set the value at the layer that knows it (the service building the model), not in the repository. A repository-internal protocol may still seed a required column it owns — e.g. the two-phase message insert writes `sender_message_id = 0` then backfills it to the new row id — but that is the repository providing a value it is responsible for, not inventing a default for a caller's missing input.

Do **not** reach for a migration that adds a `DEFAULT` purely to silence a missing-value error: if the column is meant to be required, keep it required and fix the caller; only add a schema `DEFAULT` when the column genuinely has a sensible default that the database should own.

#### Query Options Pattern

Each repository has a corresponding Options DTO in `php/Ubix/DataTransferObject/SqlRepository/`:

```php
final readonly class PerformerOptions implements DtoInterface
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $limit = null,
        public readonly ?string $username = null,
        public readonly ?string $slug = null,
        public readonly ?bool $isActive = null,
        // ... all possible query parameters
    ) {}
}
```

#### Change Tracking for Updates

Repositories use Model change tracking to optimize updates:

```php
public function save(Affiliate $affiliate): void
{
    if ($affiliate->getId() === null) {
        // INSERT - all fields
        $sql = 'INSERT INTO affiliates SET ...';
    } else {
        // UPDATE - only changed fields
        $sql = 'UPDATE affiliates SET ';

        foreach ($affiliate->getChangedProperties() as $property) {
            $sql .= $this->mapPropertyToColumn($property) . ' = :' . $property . ', ';
            $parameters[$property] = $this->extractPropertyValue($affiliate, $property);
        }

        $sql = rtrim($sql, ', ') . ' WHERE id = :id';
        $parameters['id'] = $affiliate->getId()->value;
    }

    $this->sqlService->query($sql, $parameters);
}
```

#### Special Repository Types

**1. HardCodedRepository** - Static data (no database)
- `StateHardCodedRepository`
- `CountryHardCodedRepository`

**2. SqlRepository** - Database-backed (most repositories)

#### When to Create Repositories
- New entity requiring persistence
- Implement Reader for read operations
- Implement Writer only if write operations needed
- Single concrete class implements both interfaces

#### Repository Anti-Patterns to Avoid
- ❌ Business logic in repositories
- ❌ Returning raw arrays (return Models)
- ❌ Complex joins across many entities (use services)
- ❌ Direct transaction management (use services)

---

### 4. Models

**Location:** `php/Ubix/Model/`
**Base Class:** `AbstractModel`
**Purpose:** Rich domain entities with behavior and change tracking

#### Characteristics
- Final classes extending AbstractModel
- Private properties with public getters/setters
- Track property changes for optimized updates
- May contain domain logic and validation
- Use DataTypes instead of primitives
- Can implement domain interfaces

#### AbstractModel Features

```php
abstract class AbstractModel
{
    protected array $changedProperties = [];

    public function hasChanges(): bool
    public function hasChanged(string $property): bool
    public function clearChanges(): void
    public function getChangedProperties(): array
    protected function markChanged(string $property): void
    protected function markAllChanged(): void
    protected function markNonNullChanged(): void
}
```

#### Example Model

**File:** `php/Ubix/Model/Affiliate.php`

```php
final class Affiliate extends AbstractModel
{
    public function __construct(
        private ?AffiliateId $id = null,
        private ?AffiliateSiteTypeEnum $siteType = null,
        private ?Varchar $username = null,
        private ?MpCode $defaultCode = null,
        private ?AffiliateStatusEnum $status = null,
        // ... 20+ more properties
    ) {
        // Mark only the explicitly-provided (non-null) properties as changed.
        // NEVER markAllChanged() here — a null property means "not loaded",
        // and marking it changed would write NULLs over unloaded columns.
        // See models-and-datatypes.md (Rule 4 + the three-state null semantics).
        $this->markNonNullChanged();
    }

    // Getters
    public function getId(): ?AffiliateId
    {
        return $this->id;
    }

    public function getUsername(): ?Varchar
    {
        return $this->username;
    }

    // Setters with change tracking
    public function setUsername(?Varchar $username): void
    {
        $this->username = $username;
        $this->markChanged('username');
    }

    public function setStatus(?AffiliateStatusEnum $status): void
    {
        $this->status = $status;
        $this->markChanged('status');
    }
}
```

#### Model with Domain Logic

**File:** `php/Ubix/Model/Performer.php`

> ⚠️ **Legacy interop — do NOT replicate this pattern.** The example below is shown
> because it demonstrates *where* domain logic lives on a Model, but the password
> handling itself is a faithful port of the **legacy credential formats** (plaintext,
> MD5, salted SHA-1) that uBix Core must keep matching until the legacy cutover. It
> violates current password-storage standards (OWASP mandates Argon2id/bcrypt via
> `password_hash()`/`password_verify()`, and `hash_equals()` for any legacy digest
> comparison — plain `===` on secrets is timing-unsafe). **Any new credential surface
> must use `password_hash()`/`password_verify()`; never add another format here.**
> The retirement path (bcrypt rehash-on-login, `CHAT_PASS_MD5` redesign) is chartered
> in [`docs/projects/auth-rewrite/charter.md`](../projects/auth-rewrite/charter.md).

```php
final class Performer extends AbstractModel implements AccountInterface
{
    // ... properties and getters/setters

    /**
     * DOMAIN LOGIC: Password validation
     */
    public function login(string $password): bool
    {
        return $this->validatePassword($password);
    }

    /**
     * DOMAIN LOGIC: legacy password formats (see warning above — interop only)
     */
    private function validatePassword(string $password): bool
    {
        // Plain text match
        if ($password === $this->password) {
            return true;
        }

        // Encrypted password match
        if ($password === $this->encPassword) {
            return true;
        }

        // MD5 hash match
        if ($this->password && md5($this->password) === $password) {
            return true;
        }

        // SHA1 with salt
        if ($this->salt && sha1($this->salt . sha1($password)) === $this->password) {
            return true;
        }

        return false;
    }
}
```

#### Model vs DTO vs Payload

| Aspect | Model | DTO | Payload |
|--------|-------|-----|---------|
| **Purpose** | Domain entity | Data transfer | HTTP boundary |
| **Mutability** | Mutable (setters) | Immutable (readonly) | Immutable (readonly) |
| **Behavior** | Rich (domain logic) | None | Validation/serialization |
| **Change Tracking** | Yes | No | No |
| **Properties** | Private + getters/setters | Public readonly | Public (mutable during construction) |
| **Validation** | Domain rules | None | HTTP input validation |

#### When to Create Models
- New business entity
- Extend AbstractModel for change tracking
- Use DataTypes for all properties (not primitives)
- Include domain behavior methods
- Implement relevant domain interfaces

#### One Model maps to one table

A `Model` is a **row mapper**: it has a single identity (one primary key), and
its change tracking + the repository's INSERT/UPDATE target **one table's
columns**. So a Model maps to exactly one physical table.

This is a rule for the *persistence* layer specifically — not a ban on
representing concepts that span tables. Those are legitimate and common; they
just belong **above** the row mapper:

- **Cross-table reads → a DTO assembled in the repository.** When a concept
  joins multiple tables (or multiple queries), the repository does the join and
  returns a purpose-built DTO/aggregate. Example: `AdminGroupMember` is built
  from a join of `Admin_Group_Users` + `Admin_Users` and returned as a DTO — not
  by widening a Model to span both tables.
- **Read/response shapes → response payloads or projection DTOs**, which may
  denormalize across many tables freely.

Why the row mapper itself stays single-table: a Model spanning two tables has
ambiguous `save()` semantics (transaction boundary, partial-failure behaviour,
which table owns a default) and a muddy identity. The canonical anti-pattern in
this codebase is the legacy `AdminUser`, which is hydrated from
`STUDIOS.Admin_Users` on one path and `VSCASH.Admin_Users` on another — one
class, two physical schemas. The M3-05 `AdminUserAccount` repository (kept
separate from the auth `AdminUserReader`) is the model to follow.

> Rare, deliberate exception: *secondary-table / table-splitting* — one logical
> row split across tables for performance (hot vs. cold columns, offloaded
> BLOBs), as mature ORMs support explicitly. uBix Core has no such case today; if
> one arises, document it at the Model rather than letting it happen by accident.

#### Model Anti-Patterns to Avoid
- ❌ Using raw primitives instead of DataTypes
- ❌ Public mutable properties (use getters/setters)
- ❌ Database operations (use repositories)
- ❌ HTTP concerns (use controllers/payloads)
- ❌ Spanning more than one table (one Model = one table; assemble multi-table concepts as repository DTOs — see above)

---

### 5. DataTypes (Value Objects)

**Location:** `php/Ubix/DataType/`
**Purpose:** Type-safe value objects with validation, prevent primitive obsession

#### DataType Hierarchy

```
AbstractDataType (root)
├── AbstractIntDataType
│   ├── Integer, BigInt, SmallInt, TinyInt, MediumInt
│   ├── Unsigned variants (UnsignedInt, UnsignedBigInt, etc.)
│   ├── Domain IDs (PlatformUserId, AffiliateId, PerformerId)
│   └── AutoIncrement
├── AbstractStringDataType
│   ├── Varchar, Text, Char
│   ├── MpCode (4-5 char codes)
│   └── ZeroDateTime (special datetime string)
├── AbstractFloatDataType
│   └── UsdCurrency (money with validation)
├── AbstractDateTimeDataType
│   ├── UbixDateTime
│   └── NullableVsmDateTime
├── AbstractBoolDataType
│   ├── Boolean
│   └── NullableBoolean
└── AbstractEnumDataType
    └── AffiliateSiteTypeEnum, AffiliateStatusEnum, etc.
```

#### Base DataType Pattern

> `validate()` throws the typed **`DataTypeValidationException`** (with
> `ExceptionCode::DATA_TYPE_VALIDATION_FAILED`) — catch *that* when constructing a
> DataType from untrusted or generated input and you need a reject/re-roll path.
> **Never catch `DtoException` around a DataType constructor** — DataTypes don't throw
> it, so the catch silently never matches (this exact bug made the username generator's
> over-length re-roll path unreachable and 500-capable — the "UsernameGenerator flake").
> Remaining known deviations (benchmark SB-06): only the *first* violation is carried,
> and a validator is built per construction rather than shared.

```php
// AbstractDataType provides validation
abstract class AbstractDataType
{
    protected function validate(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $errors = $validator->validate($this);

        if (count($errors) > 0 && $errors[0] instanceof ConstraintViolation) {
            throw new DataTypeValidationException(
                (string) $errors[0]->getMessage(),
                ExceptionCode::DATA_TYPE_VALIDATION_FAILED->value,
            );
        }
    }
}

// Type-specific abstract
abstract class AbstractStringDataType extends AbstractDataType
{
    public function __construct(
        public readonly string $value
    ) {}
}

// Concrete DataType with validation
class MpCode extends AbstractStringDataType
{
    public function __construct(
        #[Length(
            min: 4,
            max: 5,
            minMessage: 'MP Code must be at least {{ limit }} characters long',
            maxMessage: 'MP Code cannot be longer than {{ limit }} characters'
        )]
        private string $input
    ) {
        $this->validate(); // Throws Exception if invalid
        parent::__construct($input);
    }
}
```

#### Example DataTypes

**Integer with Range Validation:**
```php
class Integer extends IntDataType
{
    public function __construct(
        #[Range(min: -2147483648, max: 2147483647)]
        private int $input
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
```

**Currency with Regex Validation:**

> **Known deviation (tracked as benchmark item SB-07):** money-as-`float` is a
> well-established anti-pattern (floats can't represent most decimal amounts
> exactly; industry practice is integer minor units or decimal strings — see
> moneyphp/brick). `UsdCurrency`'s internal representation is float today and
> its migration is a chartered work item, not a pattern to extend — **don't
> model any new monetary DataType on a float**.

```php
class UsdCurrency extends FloatDataType
{
    public function __construct(
        #[Regex(
            pattern: '/^-?\d+(?:\.\d{1,2})?$/',
            message: 'Amount must be a positive or negative number with up to 2 decimal places'
        )]
        private float $input
    ) {
        $this->validate();
        parent::__construct(value: floatval($input));
    }
}
```

**String with Length Validation:**
```php
class Varchar extends StringDataType
{
    public function __construct(
        #[Length(min: 0, max: 65535)]
        private string $input
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
```

> The examples above are simplified for clarity. In the actual codebase, most DataTypes are split into a **Nullable parent + non-null concrete child** pair — see the next subsection.

#### Nullable + Concrete Pair Pattern

In practice, most DataTypes in this codebase are defined as a pair of classes: a `Nullable{Type}` parent that accepts null and carries the validation attributes, and a `{Type}` child that narrows the parameter to non-null and simply forwards to the parent. This lets the same validation rules cover both nullable columns (e.g. an optional model field) and non-null columns without duplicating the constraint definitions.

**The Nullable parent** holds the `private $input` property with its validation attributes, calls `$this->validate()`, and forwards the value to the abstract base. The property must be a *real* promoted property (not just a parameter) because `AbstractDataType::validate()` passes `$this` to Symfony's validator, which uses reflection to read properties and their attributes. PHPStan cannot see this reflection-based read, so each Nullable parent silences the expected `property.onlyWritten` warning with `// @phpstan-ignore-next-line` above the attribute:

```php
class NullableMpCode extends NullableStringDataType
{
    public function __construct(
        // @phpstan-ignore-next-line
        #[Length(
            min:        4,
            max:        5,
            minMessage: 'MP Code must be at least {{ limit }} characters long',
            maxMessage: 'MP Code cannot be longer than {{ limit }} characters',
        )]
        private ?string $input,
    ) {
        $this->validate();
        parent::__construct($input);
    }
}
```

**The concrete (non-nullable) child** does *not* use constructor promotion. Its only job is to narrow the parameter type from `?string` to `string` (or the equivalent for other base types) and forward to the parent. It does not call `validate()`, does not hold attributes, and does not need a property of its own — validation runs up in the parent against the parent's `$input`:

```php
class MpCode extends NullableMpCode
{
    public function __construct(
        string $input,
    ) {
        parent::__construct($input);
    }
}
```

Declaring `private string $input` on the child would create a dead promoted property — nothing reads it, because the child forwards the value to the parent before any property access would matter. PHPStan correctly flags that as `property.onlyWritten`. Use a plain parameter in concrete children, constructor-promoted `private $input` only in Nullable parents.

#### Using DataTypes

**In Method Signatures:**
```php
// ❌ Don't use raw primitives
public function process(int $userId, float $amount, string $mpCode)

// ✅ Use DataTypes
public function process(PlatformUserId $userId, UsdCurrency $amount, MpCode $mpCode)
```

**In Models:**
```php
final class Affiliate extends AbstractModel
{
    private ?AffiliateId $id;
    private ?MpCode $defaultCode;
    private ?UsdCurrency $commissionRate;

    public function setDefaultCode(?MpCode $code): void
    {
        $this->defaultCode = $code;
        $this->markChanged('defaultCode');
    }
}
```

**Accessing Values:**
```php
$mpCode = new MpCode('ABCD');
$value = $mpCode->value; // "ABCD"

$userId = new PlatformUserId(12345);
$id = $userId->value; // 12345
```

#### When to Create DataTypes
- Wrapping primitives with domain meaning
- Adding validation rules to values
- Preventing invalid states (validated at construction)
- Type-safe parameters in methods
- Eliminating primitive obsession

#### DataType Anti-Patterns to Avoid
- ❌ Mutable DataTypes (always readonly)
- ❌ DataTypes without validation
- ❌ Using raw primitives in service signatures

---

### 6. Payloads (Request/Response)

**Location:** `php/Ubix/Payload/`
**Base Class:** `AbstractPayload`
**Purpose:** Type-safe HTTP boundary validation and serialization

See [Payloads vs DTOs](../projects/reviews/payloads-vs-dtos.md) for detailed analysis of why this approach is superior to traditional DTOs.

#### AbstractPayload Features

```php
abstract class AbstractPayload implements RequestPayloadInterface, ResponsePayloadInterface
{
    private array $errors = [];
    private static ?Serializer $serializer = null;

    /**
     * Throws DtoException if validation errors exist
     */
    public function __construct()
    {
        if (count($this->errors) > 0) {
            throw new DtoException(
                message: 'There are issues with your input value(s).',
                dto: new PayloadError(count: count($this->errors), errors: $this->errors)
            );
        }
    }

    /**
     * Deserialize and validate request
     */
    public static function getRequest(Request $request): RequestPayload

    /**
     * Create response from DTO
     */
    public static function getResponse(Dto $dto): ResponsePayload

    /**
     * Get data for JSON response
     */
    public function getResponseData(): array

    /**
     * Validate and map individual field
     */
    public function validateAndMapField(string $name, string $dest, mixed $raw): void
}
```

#### Request Payload Example

**File:** `php/Ubix/Payload/Request/FirstMoneyRequestPayload.php`

```php
final class FirstMoneyRequestPayload extends Payload implements RequestPayload
{
    public PlatformUserId $userId;
    public UsdCurrency $amount;
    public MpCode $envMpCode;
    public ?UbixDateTime $transactionDate;
    public ?Varchar $clickId;
    public ?Varchar $voluumClickId;

    public function __construct(
        ?float $amount,
        ?string $env_mp_code,
        ?string $transaction_date,
        ?int $user_id,
        ?string $click_id,
        ?string $voluum_click_id,
    ) {
        $this->validateAndMapField('amount', 'amount', $amount);
        $this->validateAndMapField('env_mp_code', 'envMpCode', $env_mp_code);
        $this->validateAndMapField('transaction_date', 'transactionDate', $transaction_date);
        $this->validateAndMapField('user_id', 'userId', $user_id);
        $this->validateAndMapField('click_id', 'clickId', $click_id);
        $this->validateAndMapField('voluum_click_id', 'voluumClickId', $voluum_click_id);

        parent::__construct(); // Throws if errors
    }
}
```

#### Response Payload Example

**File:** `php/Ubix/Payload/Response/AttributionResponsePayload.php`

```php
final class AttributionResponsePayload extends Payload implements ResponsePayload
{
    public string $message;
    public int $code;
    public string $mp_code;

    public function __construct(
        string $message,
        int $code,
        ?MpCode $mpCode = null,
    ) {
        $this->message = $message;
        $this->code = $code;
        $this->mp_code = $mpCode->value ?? '';
    }
}
```

#### Using Payloads in Controllers

**Request Parsing:**
```php
try {
    $payload = FirstMoneyRequestPayload::getRequest($request);
    assert($payload instanceof FirstMoneyRequestPayload);
} catch (DtoException $e) {
    // Returns ALL validation errors at once
    return $this->renderJson($response, [
        'fields' => $e->getDto()->errors ?? [],
        'message' => $e->getMessage(),
    ], StatusCode::BAD_REQUEST);
}

// Access validated, typed data
$userId = $payload->userId; // PlatformUserId
$amount = $payload->amount; // UsdCurrency
```

**Response Generation:**
```php
// From service DTO
$dto = $this->service->doSomething();

// Create response payload
$responsePayload = AttributionResponsePayload::getResponse($dto);

// Render as JSON
return $this->renderJsonWithPayload($response, $responsePayload);
```

#### Error Response Format

```json
{
  "fields": [
    {"name": "amount", "error": "Amount must be a positive number with up to 2 decimal places"},
    {"name": "user_id", "error": "Cannot be of type string"},
    {"name": "env_mp_code", "error": "MP Code must be at least 4 characters long"}
  ],
  "message": "There are issues with your input value(s).",
  "statusCode": 400
}
```

#### When to Create Payloads
- New API endpoint requiring structured request data
- Need type-safe request parsing with automatic validation
- Generating structured API responses
- Want all validation errors returned at once (better UX)

---

### 7. DTOs (Data Transfer Objects)

**Location:** `php/Ubix/DataTransferObject/`
**Interface:** `DtoInterface` (marker interface)
**Purpose:** Immutable data containers for transferring data between layers

#### DTO Characteristics
- Readonly classes with public readonly properties
- No validation (simple data carriers)
- No behavior (pure data)
- Used internally (service returns, repository options)

#### DTO Categories

**1. SqlRepository Options** - `DataTransferObject/SqlRepository/`

```php
final readonly class PerformerOptions implements DtoInterface
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $limit = null,
        public readonly ?string $username = null,
        public readonly ?string $slug = null,
        public readonly ?bool $isActive = null,
        // ... all possible query parameters
    ) {}
}
```

**2. Domain Data Structures**

```php
final readonly class Attribution implements DtoInterface
{
    public function __construct(
        public readonly MpCode $mpCode,
        public readonly ?AffiliateId $affiliateId,
        public readonly bool $isMediaBuy,
    ) {}
}

final readonly class MediaBuyingDetails implements DtoInterface
{
    public function __construct(
        public readonly bool $isMediaBuying,
        public readonly string $mediaBuyType
    ) {}
}
```

**3. Error Objects**

```php
final readonly class PayloadError implements DtoInterface
{
    public function __construct(
        public readonly int $count,
        public readonly array $errors
    ) {}
}

final readonly class PdoError implements DtoInterface
{
    public function __construct(
        public readonly ?string $sqlState,
        public readonly ?int $driverCode,
        public readonly ?string $message,
        public readonly string $query,
        public readonly array $parameters
    ) {}
}
```

**4. Configuration Objects**

```php
final readonly class S3BlobServiceParameters implements DtoInterface
{
    public function __construct(
        public readonly string $bucket,
        public readonly string $region,
        public readonly string $accessKey,
        public readonly string $secretKey
    ) {}
}
```

#### DTO vs Model vs Payload

| Aspect | DTO | Model | Payload |
|--------|-----|-------|---------|
| **Purpose** | Data transfer | Domain entity | HTTP boundary |
| **Mutability** | Readonly | Mutable | Varies |
| **Validation** | None | Domain rules | Input validation |
| **Behavior** | None | Rich domain logic | Serialization |
| **Properties** | Public readonly | Private + getters/setters | Public |

#### When to Use DTOs
- Passing complex data between service layers
- Repository query options (Options pattern)
- Service return values
- Configuration objects
- Error details

---

### 8. Enums

**Location:** `php/Ubix/Enum/`
**Type:** Native PHP 8.1+ Enums
**Purpose:** Type-safe constants, domain vocabulary

#### Enum Types

**String-Backed Enums:**
```php
enum AffiliateStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING = 'pending';
}
```

**Int-Backed Enums:**
```php
enum StatusCode: int
{
    case OK = 200;
    case CREATED = 201;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
}

enum ExceptionCode: int
{
    case APP_NAME_MISSING = 1001;
    case INVALID_COMMAND_FQCN = 1002;
    case BEGIN_TRANSACTION_FAILED_IN_PDO = 2001;
    case COMMIT_TRANSACTION_FAILED_IN_PDO = 2002;
    // ... organized by category (1xxx = app, 2xxx = db, 3xxx = cache)
}
```

#### Enum Organization

```
Enum/
├── StatusCode.php              # HTTP status codes
├── ExceptionCode.php           # Custom exception codes
├── YesNo.php                   # Generic Y/N
├── Affiliate/
│   ├── AffiliateStatus.php
│   ├── AffiliateIsHouse.php
│   ├── AffiliateProductType.php
│   └── AffiliateRateType.php
├── Performer/
│   ├── PerformerStatus.php
│   └── PerformerGender.php
├── Email/
│   └── EmailContentType.php
└── ... (organized by domain)
```

#### Using Enums

**In Type Hints:**
```php
public function setStatus(?AffiliateStatusEnum $status): void
```

**In Models:**
```php
private ?AffiliateSiteTypeEnum $siteType = null;
```

**In Conditionals:**
```php
if ($affiliate->getStatus() === AffiliateStatus::ACTIVE) {
    // ...
}
```

**Creating from Values:**
```php
$status = AffiliateStatus::from('active');
$statusCode = StatusCode::from(200);
```

#### When to Create Enums
- Fixed set of values for a domain concept
- Replace magic strings/numbers
- Document valid values for a property
- Type-safe constants

---

### 9. Middleware

**Location:** `php/Ubix/Middleware/`
**Interface:** PSR-15 `MiddlewareInterface`
**Purpose:** Request/response processing pipeline

#### Middleware Pattern

```php
final class BearerTokenAuthenticationMiddleware implements Middleware
{
    public function __construct(
        private Logger $logger
    ) {}

    public function process(Request $request, Handler $handler): Response
    {
        // Pre-processing
        $bearerToken = $this->extractBearerToken($request);

        if (!$this->isValid($bearerToken)) {
            throw new HttpForbiddenException($request);
        }

        // Pass to next middleware/controller
        $response = $handler->handle($request);

        // Post-processing (if needed)
        return $response;
    }
}
```

#### Available Middleware

1. **BearerTokenAuthenticationMiddleware** - API token validation
2. **AccountAuthenticationMiddleware** - User session validation
3. **SessionMiddleware** - Session management
4. **NormalizedIpAddressMiddleware** - Extract client IP
5. **NormalizedHostMiddleware** - Standardize host header

#### Middleware Registration

**File:** `app/{AppName}/src/Middleware.php`

```php
return static function (App $app): void {
    $app->addRoutingMiddleware();

    // Slim 4's $app->add() PREPENDS to the chain — last-added wraps
    // everything before it, so the LAST line here is the OUTERMOST
    // middleware (runs first on the request, last on the response).
    // Resulting request flow:
    //   NormalizedIpAddress → NormalizedHost → BearerToken → Session
    //     → AccountAuthentication → route handler
    $app->add(AccountAuthenticationMiddleware::class);
    $app->add(SessionMiddleware::class);
    $app->add(BearerTokenAuthenticationMiddleware::class);
    $app->add(NormalizedHostMiddleware::class);
    $app->add(NormalizedIpAddressMiddleware::class);

    $app->addBodyParsingMiddleware();

    // Error handling
    $errorMiddleware = $app->addErrorMiddleware(...);
};
```

#### ⚠️ Ordering caveat — Slim 4's `add()` is LIFO

`$app->add()` *prepends* to the middleware chain — the last call wraps everything before it. The line at the **bottom** of the registration block is the **outermost** middleware (runs first on the request, last on the response). Reading the file top-to-bottom gives you the *reverse* of the actual request flow.

Two pairings are load-bearing:

1. **`SessionMiddleware` MUST run before `AccountAuthenticationMiddleware`** on the request. The auth middleware reads `$_SESSION` to recover the logged-in account; without `session_start()` having fired first, `getLoggedInAccount()` reads an empty `$_SESSION`, falls back to cookie-only auto-login, and silently treats every authenticated user as anonymous. Because of LIFO, that means `add(AccountAuthentication)` must come **before** `add(Session)` in the registration block — confusing on first read, but check the comment block above the calls.

2. **`BearerTokenAuthenticationMiddleware` runs early** (high in request flow) so any caller using a service-account bearer token short-circuits before session work happens. Order it after `Session` in the registration block (so it runs before Session on the request).

The comment block above the `$app->add(...)` lines should always show the resulting request flow explicitly so future contributors don't have to mentally reverse the LIFO. Bug history: a regression from this exact trap silently broke `GET /user/me` on `ProductApi` until the order was fixed.

#### When to Create Middleware
- Authentication/authorization
- Request logging
- Request/response transformation
- CORS handling
- Rate limiting

---

### 10. Console Commands

**Location:** `php/Ubix/Console/Command/`
**Base Class:** `AbstractCommand` (extends Symfony Command)
**Purpose:** CLI commands for maintenance, cron, deployments

#### Command Structure

```
Console/Command/
├── AbstractCommand.php
├── App/
│   ├── BuildCommand.php
│   ├── DeployCommand.php
│   └── RunCommand.php
├── Code/
│   ├── CommitCommand.php
│   ├── ReviewCommand.php
│   └── LocCommand.php
├── Cron/
│   ├── AbstractCronCommand.php
│   ├── ListCommand.php
│   └── Affiliates/
│       └── ExampleCommand.php
└── Database/
    └── ResetSchemaCommand.php
```

#### Command Naming Convention

FQCN determines CLI command name:
- `Ubix\Console\Command\` prefix removed
- `Command` suffix removed
- Namespace separators become colons
- CamelCase becomes lowercase

**Examples:**
- `Ubix\Console\Command\App\BuildCommand` → `app:build`
- `Ubix\Console\Command\Cron\Affiliates\ExampleCommand` → `cron:affiliates:example`

#### Example Command

```php
final class BuildCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setDescription('Build the application');
    }

    protected function execute(Input $input, Output $output): int
    {
        $this->displayAsciiTrident($output);

        $output->writeln('Building application...');

        // Command logic

        return Command::SUCCESS;
    }
}
```

#### When to Create Commands
- Scheduled background tasks (cron)
- Deployment automation
- Database migrations/maintenance
- Development utilities
- System administration

---

## Architectural Patterns

### 1. Dependency Injection Pattern

**Framework:** PHP-DI v7
**Configuration:** Per-app `Dependencies.php` files

#### Container Definition

**File:** `app/{AppName}/src/Dependencies.php`

```php
return static function (): Container {
    $container = new ContainerBuilder();

    $container->addDefinitions([
        // Interface to Implementation binding
        Logger::class => autowire(MonologLogger::class)
            ->constructorParameter('name', $appName)
            ->constructorParameter('handlers', [...]),

        // Reader/Writer pattern
        PerformerReader::class => autowire(PerformerSqlRepository::class),

        // Same repository for read and write
        PlatformUserReader::class => autowire(PlatformUserSqlRepository::class),
        PlatformUserWriter::class => autowire(PlatformUserSqlRepository::class),

        // Factory pattern with get()
        RequestFactory::class => get(Psr17Factory::class),
        StreamFactory::class => get(Psr17Factory::class),

        // Service with configuration
        FilterService::class => autowire()
            ->constructorParameter('bearerToken', getenv('VSM_FILTER_API_BEARER_TOKEN')),
    ]);

    return $container->build();
};
```

#### Key Features
1. **Autowiring** - Automatic dependency resolution
2. **Interface Binding** - Map interfaces to implementations
3. **Constructor Parameters** - Override specific parameters
4. **Singleton Pattern** - Container manages lifecycle
5. **Environment Configuration** - Pull from `.env`

---

### 2. Service Layer Pattern

**Purpose:** Encapsulate business logic, orchestrate repositories

#### Example Flow

```php
// Controller delegates to service
public function firstMoney(Request $request, Response $response): Response
{
    $payload = FirstMoneyRequestPayload::getRequest($request);

    // Delegate ALL business logic to service
    $result = $this->attributionService->firstMoney(
        $payload->userId,
        $payload->amount,
        $payload->envMpCode,
        $payload->transactionDate,
        $payload->clickId,
        $payload->voluumClickId
    );

    return $this->renderJsonWithPayload($response,
        AttributionResponsePayload::getResponse($result)
    );
}

// Service orchestrates
final class AttributionService
{
    public function firstMoney(...): Attribution
    {
        // Get data from multiple sources
        $affiliate = $this->affiliateReader->getByMpCode($mpCode);
        $earliestAccount = $this->platformUserReader->getEarliest($userId);
        $mediaBuy = $this->affiliateService->getMediaBuyingDetails(...);

        // BUSINESS RULE: Attribution logic
        if ($mediaBuy->isMediaBuying) {
            $attributedMpCode = $mpCode;
        } elseif ($this->isWithinProtectionWindow($earliestAccount, $transactionDate)) {
            $attributedMpCode = $earliestAccount->mpCode;
        } else {
            $attributedMpCode = $mpCode;
        }

        // Persist changes
        $this->attributionLogWriter->save($log);
        $this->updateUserMpCode(...);

        return new Attribution(...);
    }
}
```

---

### 3. Repository Pattern

**Purpose:** Abstract data access, collection-like interface

#### Reader/Writer Segregation

```php
// Read-only operations
interface PerformerReaderInterface
{
    public function getByUsername(string $username): array;
    public function getById(int $id): array;
}

// Write operations
interface AffiliateWriterInterface
{
    public function save(Affiliate $affiliate): void;
    public function deleteById(AffiliateId $id): void;
}

// Single implementation of both
final class AffiliateSqlRepository implements
    AffiliateReaderInterface,
    AffiliateWriterInterface
{
    // Implements all methods
}
```

#### Query Options Pattern

```php
// Flexible querying via Options DTO
private function query(PerformerOptions $options): array
{
    $sql = 'SELECT ... FROM performers WHERE 1=1';
    $parameters = [];

    if ($options->id !== null) {
        $sql .= ' AND id = :id';
        $parameters['id'] = $options->id;
    }

    if ($options->username !== null) {
        $sql .= ' AND username = :username';
        $parameters['username'] = $options->username;
    }

    if ($options->limit !== null) {
        $sql .= ' LIMIT ' . $options->limit;
    }

    // Execute and map to Models
    foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
        $objects[] = new Performer(...$row);
    }

    return $objects;
}
```

---

### 4. Value Object Pattern

**Implementation:** DataTypes

Immutable objects representing values, not entities:

```php
$mpCode = new MpCode('ABCD'); // Validated: 4-5 chars
$affiliateId = new AffiliateId(12345);
$datetime = new UbixDateTime('2024-01-01 12:00:00');

// Used in models
private ?MpCode $mpCode;
private ?AffiliateId $affiliateId;
```

---

### 5. Specification Pattern

**Implementation:** Repository Options DTOs

```php
// Build specification
$spec = new PerformerOptions(
    username: 'john',
    isActive: true,
    limit: 10
);

// Execute
$results = $repo->query($spec);
```

---

### 6. Adapter Pattern

**Examples:**

**SQL Service Adapters:**
- `MysqlPdoSqlService` - MySQL
- `SqlitePdoSqlService` - SQLite
- Both implement `SqlServiceInterface`

**Blob Service Adapters:**
- `S3BlobService` - AWS S3
- `FilestackBlobService` - Filestack
- Both implement `BlobServiceInterface`

---

### 7. Template Method Pattern

**AbstractDataType validation** (throws the typed `DataTypeValidationException` —
see the note at _Base DataType Pattern_ above):

```php
abstract class AbstractDataType
{
    // Template method
    protected function validate(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $errors = $validator->validate($this);

        if (count($errors) > 0) {
            throw new DataTypeValidationException($errors[0]->getMessage(), ExceptionCode::DATA_TYPE_VALIDATION_FAILED->value);
        }
    }
}

// Subclasses define validation via attributes
class MpCode extends AbstractStringDataType
{
    public function __construct(
        #[Length(min: 4, max: 5)] // Hook point
        private string $input
    ) {
        $this->validate(); // Call template method
        parent::__construct($input);
    }
}
```

---

## Key Base Classes

### AbstractController

**File:** `php/Ubix/Controller/AbstractController.php`

**Provides:**
- Common dependencies (Logger, TemplateRenderer, JsonService)
- Response rendering helpers
- Template variable management
- Redirect functionality

**Required Methods:** None (all are helpers)

**When to Extend:** Creating HTTP controllers

---

### AbstractModel

**File:** `php/Ubix/Model/AbstractModel.php`

**Provides:**
- Change tracking (`changedProperties` array)
- Methods: `hasChanges()`, `hasChanged()`, `clearChanges()`, `getChangedProperties()`
- `markChanged()`, `markNonNullChanged()` for tracking (constructors use `markNonNullChanged()`, never `markAllChanged()` — see `models-and-datatypes.md`)

**Required Methods:** None (opt-in tracking)

**When to Extend:** Creating domain models

---

### AbstractPayload

**File:** `php/Ubix/Payload/AbstractPayload.php`

**Provides:**
- Request deserialization
- Response serialization
- Validation error accumulation
- Static factory methods

**Required Methods:** None (uses Symfony Serializer)

**When to Extend:** Creating request/response payloads

---

### AbstractDataType

**File:** `php/Ubix/DataType/AbstractDataType.php`

**Provides:**
- `validate()` method using Symfony Validator

**Required:**
- Subclass defines validation via attributes
- Call `validate()` in constructor

**When to Extend:** Creating value objects

---

### AbstractPdoSqlService

**File:** `php/Ubix/Service/Sql/AbstractPdoSqlService.php`

**Provides:**
- PDO connection management (singleton)
- Query execution methods
- Transaction support
- Parameter reuse
- Error handling
- Legacy latin1 ↔ UTF-8 transcoding (see below)

**Required Methods:**
- Call `initializePdoConstructorParameters()` in constructor

**When to Extend:** Creating database-specific SQL services

#### The legacy charset boundary

The runtime DSN is `charset=latin1`, because the legacy tables are
`DEFAULT CHARSET=latin1`. A column authored with non-ASCII CP1252 bytes therefore
arrives as a byte string that is **not valid UTF-8** — and every string DataType
validates the UTF-8 charset via its `#[Length]` constraint. Hydrating such a row
used to throw `DataTypeValidationException` ("This value does not match the
expected UTF-8 charset.") from inside the repository, taking down whatever request
was loading it.

`AbstractPdoSqlService` closes that at the seam, in both directions, via
{@see \Ubix\Service\CharsetService}:

| Direction | Where | What |
|-----------|-------|------|
| DB → domain | `getColumn()` / `getRow()` / `getRows()` | every fetched string value decodes to valid UTF-8 |
| domain → DB | the single `execute()` bind point | every bound string parameter encodes to CP1252 |

**What this means when you write a repository — for text columns:** nothing. Do
*not* call `CharsetService` yourself at hydration — reads are already UTF-8 by the
time you construct a DataType, and writes already land in the column as the same
bytes legacy PHP would have written. (A handful of repositories still carry an
explicit `toUtf8()` call from before the seam existed; those are redundant but
harmless, since `toUtf8()` is idempotent on valid UTF-8.)

> #### ⚠️ Binary columns: you MUST declare them
>
> The seam transcodes **text**. It has no way to know a column holds opaque bytes
> instead — PDO does not report that. A real `varbinary(16)` comes back from
> `getColumnMeta()` as `native_type => VAR_STRING` with no distinguishing flag,
> identical to a `varchar`. So if you `SELECT` or bind a **BLOB / VARBINARY**
> value and say nothing, its bytes get reinterpreted as CP1252 and re-encoded —
> silently altering the value in either direction.
>
> Declare those fields and they are passed through untouched:
>
> ```php
> // Read: name the result column(s).
> $row = $this->sqlService->getRow(
>     sql:            'SELECT username, ip_address_long FROM ntl_db.optiusers_site_logins WHERE id = :id',
>     parameters:     ['id' => $id->value],
>     binaryFields:   ['ip_address_long'],
> );
>
> // Write: name the bound parameter(s).
> $this->sqlService->query(
>     sql:            'INSERT INTO ntl_db.optiusers_site_logins SET ip_address_long = :ip',
>     parameters:     ['ip' => $packedIp],
>     binaryFields:   ['ip'],
> );
> ```
>
> `$binaryFields` is one list naming result columns and/or bound parameters, so a
> repository that reads and writes the same blob usually needs it once per call.
> This tier has real binary columns — `ntl_db.optiusers_site_logins.ip_address_long`
> (`varbinary(16)`), `VSCASH.enc_cc_num` / `enc_DDA` / `enc_card_suffix`
> (`tinyblob`), `VSCASH.binary_data` (`longblob`), and 100+ further `varbinary`
> columns across the schemas reachable through the same `MysqlPdoSqlService` — so
> this is a live concern, not a hypothetical one. See
> `MysqlPdoSqlServiceTest::testDeclaredBinaryParameterIsWrittenWithoutEncoding()`
> and `::testDeclaredBinaryFieldIsReadBackWithoutDecoding()` for what each
> direction does with and without the declaration.

Two deliberate properties worth knowing:

- **The encode is not applied blindly.** Values that are not valid UTF-8 (binary
  parameters, or bytes already in legacy form) pass through untouched, as do
  codepoints CP1252 cannot represent (CJK, Cyrillic, emoji) — for those,
  `mb_convert_encoding` would substitute `?`, so the UTF-8 bytes are written
  through instead and both legacy PHP and `toUtf8()` read them back intact.
- **The seam is derived from the DSN, not hardcoded.** `SqlitePdoSqlService`
  (UTF-8 native, used by the feature-test system) is never touched, and when the
  legacy schema is migrated to utf8mb4 and the DSN flips, both directions switch
  themselves off with no code change. Tracked as `ARCH-12` (architecture sweep)
  and `PP-07` (schema modernisation).

Note that a latin1 `varchar(N)` counts **bytes**, so the encode is what keeps an
accented value inside its column width — writing UTF-8 through would let it
truncate mid-sequence and store invalid UTF-8.

---

### AbstractCommand

**File:** `php/Ubix/Console/Command/AbstractCommand.php`

**Provides:**
- Auto command name generation from FQCN
- ASCII art helpers
- Symfony Console integration

**Required:**
- Implement `configure()` and `execute()`
- Follow naming convention

**When to Extend:** Creating CLI commands

---

### AbstractSimpleCache

**File:** `php/Ubix/SimpleCache/AbstractSimpleCache.php`

**Provides:**
- PSR-16 key validation
- Helper methods

**Required:**
- Implement PSR-16 `CacheInterface` methods

**When to Extend:** Creating cache adapters

---

## Naming Conventions

### Interfaces

**Suffix:** `Interface`

**Pattern:** `{Domain}{Purpose}Interface`

**Examples:**
- `SqlServiceInterface`
- `PerformerReaderInterface`
- `AffiliateWriterInterface`
- `BlobServiceInterface`

**Special:** `DtoInterface` (marker, no methods)

---

### Abstracts

**Prefix:** `Abstract`

**Pattern:** `Abstract{Type}`

**Examples:**
- `AbstractController`
- `AbstractModel`
- `AbstractPayload`
- `AbstractDataType`
- `AbstractCommand`

---

### File Organization

```
php/Ubix/
├── Collection/           # Collection types
├── Console/Command/     # CLI commands (by domain)
├── Controller/          # HTTP controllers (by API)
├── DataTransferObject/  # DTOs
│   └── SqlRepository/   # Query options
├── DataType/            # Value objects (by type)
│   ├── Bool/
│   ├── DateTime/
│   ├── Enum/
│   ├── Float/
│   ├── Int/
│   └── String/
├── Enum/                # Native enums (by domain)
├── Exception/           # Custom exceptions
├── Middleware/          # PSR-15 middleware
├── Model/               # Domain models
├── Payload/             # Request/Response
│   ├── Request/
│   └── Response/
├── Repository/          # Data access (by entity)
│   └── {Entity}/
│       ├── {Entity}ReaderInterface.php
│       ├── {Entity}WriterInterface.php
│       └── {Entity}SqlRepository.php
├── Service/             # Business logic
└── SimpleCache/         # PSR-16 cache
```

---

### Class Naming

| Type | Pattern | Example |
|------|---------|---------|
| **Controller** | `{Domain}Controller` | `AttributionController` |
| **Service** | `{Domain}Service` | `AffiliateService` |
| **Repository** | `{Entity}SqlRepository` | `PerformerSqlRepository` |
| **Model** | `{Entity}` | `Performer`, `Affiliate` |
| **DTO** | `{Purpose}` | `PerformerOptions`, `Attribution` |
| **DataType** | `{DomainConcept}` | `MpCode`, `AffiliateId` |
| **Enum** | `{Domain}{Concept}` | `AffiliateStatus`, `StatusCode` |
| **Middleware** | `{Purpose}Middleware` | `BearerTokenAuthenticationMiddleware` |
| **Command** | `{Action}Command` | `BuildCommand`, `DeployCommand` |

---

### Reserved class-name tokens

Some words are reserved and cannot appear in the short name of a concrete class. Enforced by `tests/AbstractVsmConcreteClassOrEnumTestCase.php` via the `CLASS_NAME_RESERVED_WORDS_AND_EXEMPTIONS` constant — every concrete class runs through `testClassFollowingVsmStandards()`, which fails if the class short name contains a reserved word (case-insensitive substring) and the FQCN isn't in that word's exemption list.

| Reserved word | Why | Exemptions |
|---|---|---|
| `Model` | Keeps `Ubix\Model\*` as the unambiguous home for entity classes (the things that extend `AbstractModel`, hold persistent state, and use the `markChanged` concurrency pattern). Without the reservation, DTOs / services / repositories drift toward `*Model*` names that read like entities and obscure where the real entity layer lives. | `Ubix\DataTransferObject\ModelDiff` |
| `Optiuser` | Legacy term being phased out. | none |

**Domain ↔ code vocabulary mapping.** Some product domains use a word that is reserved in code. Document the equivalence next to the relevant code; do **not** rename to satisfy the product term.

| Product / spec / conversation says | Code uses | Why the gap |
|---|---|---|
| "live model" / "live models" | `LiveCam`, `LiveCams`, `LiveCamDto`, `LiveCamReader`, etc. (namespaced under `Ubix\…\LiveCam\`) | "live model" contains the reserved `Model` token. `LiveCam` matches the product-facing term ("Live Sex Cams", "Live Cam Tabs") and is collision-free. |

If a future domain hits the same wall, prefer (in order): (1) pick a synonym that isn't reserved, (2) request the rule be loosened (a namespace-aware exemption is cleaner than an ever-growing FQCN exemption list), (3) only then add an FQCN exemption. The current `Ubix\DataTransferObject\ModelDiff` exemption exists because no synonym fit and the class predates the rule.

---

## Data Flow Patterns

### Complete Request Flow

```
1. HTTP Request
   ↓
2. public/index.php (Entry Point)
   ↓
3. Load Environment (.env via Dotenv)
   ↓
4. Load Dependencies (Dependencies.php)
   - Build DI Container
   - Register services, repositories
   ↓
5. Create Slim App (via PHP-DI Bridge)
   ↓
6. Apply Middleware (Middleware.php)
   - Authentication
   - Request normalization
   - Body parsing
   ↓
7. Apply Routes (Routes.php)
   - Map URLs to Controller actions
   ↓
8. Middleware Pipeline Execution
   - BearerTokenAuthenticationMiddleware
   - NormalizedHostMiddleware
   - NormalizedIpAddressMiddleware
   ↓
9. Controller Action Invoked
   - Container injects dependencies
   ↓
10. Controller receives Request
    ↓
11. Parse Request via Payload
    - RequestPayload::getRequest($request)
    - Validates and deserializes JSON
    - Throws DtoException on errors
    ↓
12. Delegate to Service
    - Pass validated DataTypes
    ↓
13. Service orchestrates business logic
    - Call multiple Repositories
    - Apply business rules
    - Create/modify Models
    ↓
14. Repository queries/persists data
    - Use Options DTOs for queries
    - Return Model objects
    - Track changes for updates
    ↓
15. Service returns DTO
    ↓
16. Controller creates ResponsePayload
    - ResponsePayload::getResponse($dto)
    ↓
17. Controller renders response
    - renderJsonWithPayload() for APIs
    - renderTemplate() for web
    ↓
18. Middleware Pipeline (after controller)
    ↓
19. HTTP Response sent to client
```

---

### Data Transformation Per Layer

**Layer 1: HTTP → Payload**
```
Raw JSON                  →  Validated Payload
{                            FirstMoneyRequestPayload {
  "userId": 12345,             userId: PlatformUserId(12345),
  "amount": 99.99,             amount: UsdCurrency(99.99),
  "envMpCode": "ABCD"          envMpCode: MpCode("ABCD")
}                            }
```

**Layer 2: Payload → Service**
```
Payload Properties       →  Service Parameters
$payload->userId            userId: PlatformUserId
$payload->amount            amount: UsdCurrency
```

**Layer 3: Service → Repository**
```
Service Request          →  Repository Query
getAffiliateByMpCode()      query(AffiliateOptions(
$mpCode                       defaultCode: $mpCode->value
                            ))
```

**Layer 4: Repository → SQL**
```
Repository Query         →  SQL Execution
AffiliateOptions(           SELECT * FROM Affiliates
  defaultCode: "ABCD"       WHERE default_code = :code
)                           PARAMS: ['code' => 'ABCD']
```

**Layer 5: SQL → Model**
```
Database Row             →  Domain Model
[                           Affiliate(
  'id' => 123,                id: AffiliateId(123),
  'username' => 'john'        username: Varchar("john")
]                           )
```

**Layer 6: Model → DTO**
```
Domain Model             →  Data Transfer Object
Affiliate {                 Attribution {
  id: AffiliateId(123)        affiliateId: AffiliateId(123),
}                             mpCode: MpCode("ABCD")
                            }
```

**Layer 7: DTO → ResponsePayload**
```
DTO                      →  Response Payload
Attribution {               AttributionResponsePayload {
  affiliateId: 123            affiliateId: 123,
}                             mpCode: "ABCD"
                            }
```

**Layer 8: ResponsePayload → JSON**
```
Response Payload         →  JSON Output
getResponseData()           {
                              "affiliateId": 123,
                              "mpCode": "ABCD"
                            }
```

---

### Validation Locations

**1. Request Validation (Payload Layer)**
- **Where:** `AbstractPayload::getRequest()`
- **What:** JSON structure, types, required fields
- **How:** Symfony Serializer + type hints
- **Errors:** Throws `DtoException` with ALL field errors

**2. Value Validation (DataType Layer)**
- **Where:** DataType constructors
- **What:** Business rules for individual values
- **How:** Symfony Validator attributes
- **Errors:** Throws `Exception` immediately
- **Examples:** MpCode (4-5 chars), UsdCurrency (format)

**3. Business Logic Validation (Service Layer)**
- **Where:** Service methods
- **What:** Complex business rules, cross-entity validation
- **How:** Manual checks, domain logic
- **Errors:** Throws domain exceptions
- **Examples:** Attribution rules, user eligibility

**4. Database Constraints**
- **Where:** Database schema
- **What:** Referential integrity, unique constraints
- **How:** Database enforces
- **Errors:** `PDOException` wrapped in `DtoException`

**What none of these four layers is: authorization.**

Validation answers *"is this value well-formed?"*. Authorization answers *"may this caller act on it?"*. Every layer above answers the first question and none answers the second, so a hostile request that is perfectly well-formed passes the entire stack. `performer_id: 41827` is a valid `PlatformUserId` — it satisfies the Payload, the DataType, and the FK. It simply isn't the caller's.

**The rule: identity comes from the authenticated principal, never from a request field.** In uBix Core that principal is whatever the middleware established — the session recovered by `AccountAuthenticationMiddleware`, or a verified signed token such as `PaSessionTokenService::verify()`. A controller that reads an owner id out of the request body and acts on it has no authorization step at all, however thoroughly that id was validated.

The precedent: `SessionController::pepHash()` originally accepted `performer_id` in the request body and returned a live PEP broadcast credential for it. Every validation layer passed; any bearer-token holder could mint credentials for any performer. It now accepts only `session_token`, verifies the signature, and derives the performer from the verified payload — there is deliberately no performer id in the request. MR review caught this, not the gate; it is a review-blocking class on sight.

Two habits follow. **Fail closed:** when a verifier cannot verify — missing secret, expired signature, unreachable dependency — return 401, never a fallback identity. **Don't accept a redundant id:** if the endpoint can derive the subject from the principal, accepting it in the payload too creates a mismatch path that must then be checked, and one day won't be.

---

### Business Logic Locations

**Primary: Services** - Complex multi-entity operations

```php
class AttributionService
{
    public function firstMoney(...): Attribution
    {
        // Get data from multiple sources
        $affiliate = $this->affiliateReader->getByMpCode($mpCode);
        $earliestAccount = $this->platformUserReader->getEarliest($userId);

        // BUSINESS RULE: Attribution logic
        if ($this->isMediaBuy($affiliate)) {
            $attributedMpCode = $mpCode;
        } elseif ($this->isWithinProtectionWindow($earliestAccount)) {
            $attributedMpCode = $earliestAccount->mpCode;
        }

        return new Attribution(...);
    }
}
```

**Secondary: Models** - Entity-specific behavior

```php
class Performer extends AbstractModel
{
    // DOMAIN LOGIC: Password validation
    // (legacy-interop formats — see the "Model with Domain Logic" warning above;
    //  new credential surfaces use password_hash()/password_verify())
    public function login(string $password): bool
    {
        return $this->validatePassword($password);
    }

    private function validatePassword(string $password): bool
    {
        // Multiple password format support
        if ($password === $this->password) return true;
        if (md5($this->password) === $password) return true;
        return false;
    }
}
```

---

## Integration Patterns

### Database Access

**Abstraction:** `SqlServiceInterface`

**Implementations:**
- `MysqlPdoSqlService` - Production
- `SqlitePdoSqlService` - Testing

#### Read/Write Separation

```php
$this->initializePdoConstructorParameters(
    readDsn: getenv('MYSQL_READ_DSN'),      // Read replica
    readUsername: getenv('MYSQL_READ_USER'),
    readPassword: getenv('MYSQL_READ_PASS'),
    writeDsn: getenv('MYSQL_WRITE_DSN'),    // Primary
    writeUsername: getenv('MYSQL_WRITE_USER'),
    writePassword: getenv('MYSQL_WRITE_PASS')
);
```

#### Query Methods

```php
// Write operations
public function query(string $sql, array $parameters): int;

// Read operations
public function getColumn(string $sql, array $parameters): mixed;
public function getRow(string $sql, array $parameters): array|false;
public function getRows(string $sql, array $parameters): Generator;

// Transactions
public function beginTransaction(): void;
public function commit(): void;
public function rollBack(): void;
```

#### Parameter Reuse

```php
$sql = 'SELECT * FROM users WHERE (status = :status OR backup_status = :status)';
$result = $sqlService->getRows($sql, ['status' => 'active'], allowParameterReuse: true);

// Auto-transformed to:
// 'SELECT * FROM users WHERE (status = :status_1 OR backup_status = :status_2)'
// ['status_1' => 'active', 'status_2' => 'active']
```

#### Pagination

uBix Core sanctions **two** pagination patterns, one per problem shape — never hand-roll `LIMIT`/`OFFSET` or a bespoke cursor (machine code review rejects it):

- **Offset (page-based)** — bounded, browsable admin tables wanting page numbers + a total (Users, Affiliates, …). Request extends `AbstractPaginatedRequestPayload` (`limit`/`offset`/`sort`/`order`); reader returns `PagedObjects`; wire shape `{ items, limit, offset, total }`.
- **Cursor (keyset)** — unbounded infinite-scroll feeds where a total is meaningless (home stream, fanclub posts). Request extends `AbstractCursorRequestPayload` (`after`/`limit`); reader returns `CursorPage`; wire shape `{ items, nextCursor }`.

Default to **Offset** for admin tables; reach for **Cursor** only for genuine feeds. The full contracts, the decision rule, and the enforcement sniff live in **[`docs/standards/pagination.md`](../standards/pagination.md)**.

---

### External APIs

**HTTP Client:** `CurlHttpClient` (PSR-18)

```php
final class XvtService
{
    public const API_TIMEOUT = 10;

    public function __construct(
        private HttpClient $httpClient,
        private RequestFactory $requestFactory,
        private Logger $logger
    ) {}

    public function callExternalApi(string $endpoint, array $data): array
    {
        $request = $this->requestFactory
            ->createRequest('POST', $this->apiUrl . $endpoint)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(
                json_encode($data)
            ));

        try {
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() !== 200) {
                throw new HttpClientException('API returned ' . $response->getStatusCode());
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientExceptionInterface $e) {
            $this->logger->error('External API failed', ['error' => $e->getMessage()]);
            throw new HttpClientException('External API call failed', previous: $e);
        }
    }
}
```

---

### Caching Strategy

**Interface:** PSR-16 `CacheInterface`
**Implementation:** `MemcachedLegacySimpleCache`

> ⚠️ **Key naming is a correctness issue, not a style issue.** PSR-16 reserves
> `{}()/\@:` in cache keys — a compliant implementation MUST reject them, and a
> colon in a key has already caused a production incident (IA-2.0 admin lists
> 500'd; fixed in `9a5c4d54`). Follow the key standard in
> [`docs/standards/memcache-keys.md`](../standards/memcache-keys.md)
> (`NEPTUNE_` prefix, SCREAMING_SNAKE, explicit TTL) and never interpolate
> unsanitized values into a key.

```php
// Configuration
SimpleCache::class => autowire(MemcachedLegacySimpleCache::class)
    ->constructorParameter('servers', $memcacheServers)

// Usage
final class AffiliateSqlRepository
{
    public function getCached(AffiliateId $id): ?Affiliate
    {
        $cacheKey = 'NEPTUNE_AFFILIATE_' . $id->value;

        // Try cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Query database
        $affiliate = $this->getById($id);

        // Cache for 1 hour
        $this->cache->set($cacheKey, $affiliate, 3600);

        return $affiliate;
    }

    public function save(Affiliate $affiliate): void
    {
        // Save to database
        $this->sqlService->query(...);

        // Invalidate cache
        if ($affiliate->getId() !== null) {
            $this->cache->delete('NEPTUNE_AFFILIATE_' . $affiliate->getId()->value);
        }
    }
}
```

---

### Logging Strategy

**Framework:** Monolog

```php
// Configuration
Logger::class => autowire(MonologLogger::class)
    ->constructorParameter('name', $appName)
    ->constructorParameter('handlers', [
        new StreamHandler(
            getenv('LOGGER_PATH') . '/' . $appName . '.log',
            Level::fromName($logLevel),
            true,
            0777
        )
    ])
    ->constructorParameter('processors', [
        new UidProcessor()  // Add unique ID to each log entry
    ])
```

**Usage:**
```php
// Debug logging
$this->logger->debug('Attribution request', [
    'userId' => $requestData->userId->value,
    'amount' => $requestData->amount->value
]);

// Error logging
$this->logger->error('Attribution failed', [
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

---

### Email/Notifications

**Email Service:**

```php
interface EmailServiceInterface
{
    public function sendEmail(
        EmailAccount $account,
        string $toAddress,
        string $subject,
        string $body,
        EmailContentType $contentType = EmailContentType::HTML
    ): void;
}
```

**Slack Integration:**

```php
final class SlackService
{
    public function sendMessage(string $channel, string $text): void
    {
        // POST to Slack webhook
    }
}
```

---

### File/Blob Storage

**Interface:** `BlobServiceInterface`

**Implementations:**
- `S3BlobService` - AWS S3
- `FilestackBlobService` - Filestack CDN

```php
interface BlobServiceInterface
{
    public function upload(string $filePath, string $destination): string;
    public function download(string $source, string $destination): void;
    public function delete(string $path): void;
    public function getUrl(string $path): string;
}
```

---

### Legacy Session Bridge

**Purpose:** Make uBix Core-issued sessions readable by legacy webservices (`chat-room-interface.php`, `chat-login.php`, `LiveSite.cl`, etc.) without round-tripping through legacy `login.php`. The legacy stack reads `$_SESSION['USER']` as a snake_case associative array; uBix Core authenticates with strongly-typed models. The bridge writes both shapes during login.

**Why a dedicated pattern:** the legacy session shape is a real interop contract — one wrong field (e.g. `auth != md5((string)$user_id)`) and the legacy gate at `chat-room-interface.php:842` falls through to the guest path silently. Hand-building the array at every login site invites drift; the typed-DTO + serialize-at-write-boundary pattern centralizes the contract in one place.

**Shape:**

```
PlatformUser (uBix Core model)
    │
    ▼  buildLegacySessionUser()
LegacySessionUserDto                         ← typed; this is what passes through uBix Core internals
    │
    ▼  serializeLegacySessionUserDto()        ← single producer of the snake_case array
$_SESSION['USER'] = array<string, mixed>     ← legacy reads this directly
```

**Components:**

- **`Ubix\DataTransferObject\AccountAuthentication\LegacySessionUserDto`** — typed shape. CamelCase fields. Holds the legacy `USER` array's contents (`userId`, `auth`, `status`, `username`, `password`, `screenNames`, `bountyPaid`, `billNext`, `spendingGroup`, …) plus a `screenNames: array<int, LegacyScreenNameDto>` for the per-screen-name sub-array.
- **`Ubix\DataTransferObject\AccountAuthentication\LegacyScreenNameDto`** — typed shape for one entry in `$_SESSION['USER']['screen_names']`. Same camelCase / single-source rule.
- **`AccountAuthenticationService::buildLegacySessionUser()`** — the **only** producer of `LegacySessionUserDto` from a `PlatformUser`. Computes the `auth = md5((string)$userId)` invariant the legacy gate requires; consumers MUST NOT construct the DTO directly with a mismatched value.
- **`AccountAuthenticationService::serializeLegacySessionUserDto()`** — the **only** boundary where camelCase ↔ snake_case translation lives. Adding a new field is a one-property change in the DTO + one key in the serializer; no spelunking through legacy code each time.

**Invariants:**

1. `auth === md5((string)$userId)` — required by `chat-room-interface.php:842` gate. Any other value silently degrades the user to guest.
2. `status === 'A'` — same gate. Inactive accounts never read as authenticated by legacy.
3. The serialized array's keys are snake_case (legacy convention), NOT camelCase. Legacy code reads `$USER['user_id']` not `$USER['userId']`.
4. `userId` materializes to BOTH `user_id` and `optiusers_id` keys in the array — legacy reads both depending on the call site.

**When to extend:** when a legacy read site references a key not currently in the serialized array. Workflow:

1. Add the camelCase field to `LegacySessionUserDto` (or `LegacyScreenNameDto` for per-screen-name).
2. Map it from the appropriate `PlatformUser` / `ScreenName` getter in `buildLegacySessionUser` / `extractLegacyScreenNames`. If uBix Core doesn't have a source yet, pass a legacy-friendly empty default (`''` for strings, `0` / `0.0` for numerics, `false` for bools) — legacy's `!empty()` checks will treat the key as "no data" rather than asserting a stale value.
3. Add the snake_case key to `serializeLegacySessionUserDto` / its screen-names loop. Keep the array alphabetically sorted to match the existing convention.
4. Tests are mostly free — the standards-suite test on the DTO classes auto-validates the new field via reflection.

**Cookie counterpart:** `CookieService` is the parallel pattern for the legacy cookie shape (`PHPSESSID`, `CHAT_USER`, `CHAT_PASS_MD5`, `f4f_username`, `has_logged_in`). It centralizes the cookie-domain heuristic so uBix Core-issued cookies match the legacy domain shape. `SessionMiddleware` wires `CookieService::deriveLegacySessionKeyDomain()` into `SimpleCacheLegacySessionHandler` per-request so the Memcache session key (`sess_<DOMAIN>_<SESSID>`) matches the shape `_includes/lib_php1.0.1/core/Session.cl` writes. Same pattern, different layer of the contract.

**Bridge debt vs. rewrite:** every field added to the bridge is one more consumer the eventual chat-room rewrite has to honor. Add new fields only when a legacy read-site is producing a user-visible bug; preferred direction is to fix the legacy code path during the rewrite, not extend the bridge. The bridge retires when the last `$_SESSION['USER']` consumer is gone — see charter §6.4 "Authentication / session bridge end-state".

---

## Application Bootstrap

### Entry Point

**File:** `public/index.php`

```php
1. Set Timezone
   date_default_timezone_set('America/New_York');

2. Load Composer Autoloader
   require_once '../vendor/autoload.php';

3. Load Environment Variables
   (Dotenv::createUnsafeImmutable(__DIR__ . '/../'))->load();

4. Configure Error Reporting (sandbox/dev only)
   if (IS_SANDBOX || IS_DEV) {
       ini_set('display_errors', 1);
       error_reporting(E_ALL);
   }

5. Determine Active Application
   $appName = getenv('APP_NAME'); // e.g., 'AffiliateApi'
   $appFolder = __DIR__ . '/../app/' . $appName;

6. Build DI Container
   $buildContainer = require $appFolder . '/src/Dependencies.php';
   $container = $buildContainer();

7. Create Slim App
   $slimApp = Bridge::create($container);

8. Apply Middleware
   $applyMiddleware = require $appFolder . '/src/Middleware.php';
   $applyMiddleware($slimApp);

9. Register Routes
   $applyRoutes = require $appFolder . '/src/Routes.php';
   $applyRoutes($slimApp);

10. Run Application
    $slimApp->run();
```

---

### Application Structure

Every PHP app carries the same `src/` quartet; the app suffix determines its type
(see `docs/architecture/monorepo.md`). The authoritative app inventory is the
**Current Apps table in `CLAUDE.md`** — don't trust a hardcoded list here to stay
current. As of 2026-07 the PHP apps are: `AffiliateApi`, `FanClubApi`,
`IntegrationApi`, `InternalAdminApi`, `ModelSignupApi`, `NeptuneCli` (CLI),
`PerformerApplicationApi`, `ProductApi` (plus the non-PHP `*Js` / `*Py` / `*Go` apps).

```
app/
├── AffiliateApi/
│   └── src/
│       ├── Dependencies.php   # DI container
│       ├── Middleware.php     # Middleware pipeline
│       ├── Routes.php         # URL routing
│       └── Theme.php          # Template theme
├── FanClubApi/
├── ...                        # every other *Api app, same shape
└── NeptuneCli/
```

#### API contracts (OpenAPI)

Every `*Api` app also commits a generated **`app/<App>/openapi.json`**: endpoints
are annotated with `#[ApiContract]` and the spec is rendered by
`php bin/ubix openapi:generate`. The `code:review` gate's **openapi** tool
re-renders each spec and fails if the committed file is stale — so the contract
can't silently drift from the code. Regenerate (never hand-edit `openapi.json`)
whenever you add or change an endpoint.

---

### Dependencies.php Pattern

```php
return static function (): Container {
    $appName = getenv('APP_NAME');
    $theme = (require __DIR__ . '/Theme.php');

    $container = new ContainerBuilder();

    $container->addDefinitions([
        // PSR Interfaces
        Logger::class => autowire(MonologLogger::class),

        // Repository bindings
        PerformerReader::class => autowire(PerformerSqlRepository::class),
        AffiliateReader::class => autowire(AffiliateSqlRepository::class),
        AffiliateWriter::class => autowire(AffiliateSqlRepository::class),

        // Service bindings
        SqlService::class => autowire(MysqlPdoSqlService::class),
        SimpleCache::class => autowire(MemcachedLegacySimpleCache::class),
    ]);

    return $container->build();
};
```

---

### Middleware.php Pattern

```php
return static function (App $app): void {
    $app->addRoutingMiddleware();

    // Application-specific
    $app->add(BearerTokenAuthenticationMiddleware::class);
    $app->add(NormalizedHostMiddleware::class);

    $app->addBodyParsingMiddleware();

    // Error handling
    $errorMiddleware = $app->addErrorMiddleware(...);
};
```

---

### Routes.php Pattern

```php
return static function (App $app): void {
    // Register routes
    $app->map(['POST'], '/attribution/firstmoney',
        AttributionController::class . ':firstMoney');

    // 404 fallback (required)
    $app->map(
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        '/{routes:.*}',
        static function (Request $request): void {
            throw new HttpNotFoundException($request);
        }
    );
};
```

---

## Summary

uBix Core implements a **sophisticated, production-ready architecture** with:

1. ✅ **Strong Type Safety** - DataTypes, Enums, strict types
2. ✅ **Clear Separation of Concerns** - Layered architecture
3. ✅ **SOLID Principles** - DI, ISP, SRP throughout
4. ✅ **Modern PHP Patterns** - Value objects, DTOs, readonly
5. ✅ **PSR Standards** - PSR-3, 7, 15, 16, 18
6. ✅ **Testability** - DI, interfaces, thin controllers
7. ✅ **Scalability** - Read/write separation, caching
8. ✅ **Maintainability** - Consistent naming, organization

The architecture follows **industry best practices** for modern PHP development and demonstrates deep understanding of **domain-driven design** and **enterprise patterns**.

---

## Changes to this document

Updates belong in the same commit that changes the underlying framework. If a new layer pattern emerges (e.g. a new service-shape convention, a new abstract base class, a new Payload variant), document it in the relevant section. If a new PHPCS sniff lands that enforces a new rule, capture the rule here AND in `phpcs.xml`. If a Symfony component is swapped in or out, update both the dependency list AND the section that depends on it.

When you make a non-trivial change to a section, append a row to the version-history table below (chronological, at the END — do not prepend or insert mid-list) so reviewers can see what shifted and when. Bumping the version number is appropriate for: tightening or relaxing a standard, adding/removing a section, renaming a convention, changing what a PHPCS sniff enforces, adding/removing a layer pattern. Pure typo fixes or code-snippet refreshes don't need a bump. When you bump the version, also update the top-of-document `**Version:**` and `**Date:**` fields to match the latest row.

---

## Document Control

| Version | Date       | Author                | Notes |
|---------|------------|-----------------------|-------|
| 1.0     | 2025-11-26 | Christopher W. Olsen | Initial baseline. Captures the PHP-side architecture as it stood at the document's first cut: layered architecture (HTTP → Middleware → Controller → Service → Repository → SqlService → MySQL), strict type safety via custom DataTypes, the Payload pattern as the validation boundary, SOLID enforcement via PHPCS sniffs, three-layer validation (Request Payload / Service / Response Payload), DI via PHP-DI in each app's `Dependencies.php`, Repository pattern with Reader/Writer interfaces, Model pattern with strict getter/setter enforcement, Symfony Validator integration in `Abstract*DataType` classes, and the PER (PSR-12 Extended) coding standard. The original document carried a top-of-document `**Version:** 1.0` / `**Date:** 2025-11-26` header but no version-history table; this row reconstructs the baseline retrospectively when v1.1 introduces the table. |
| 1.1     | 2026-05-14 | Christopher W. Olsen | **Document Control table added.** No content changes — purely introduces the version-history pattern and the "Changes to this document" convention paragraph above, mirroring `docs/standards/migrations.md`, `docs/projects/migrations/cutover-runbook.md`, and `docs/architecture/complete-js-guide.md` (also versioned the same day, in the same session). Top-of-document `**Version:**` bumped 1.0 → 1.1, `**Date:**` bumped 2025-11-26 → 2026-05-14. Going forward, every non-trivial section change appends a row here AND updates the top-line fields. Why now: the JS guide just received substantive content tightening (per-layer guidance subsection, §4.2 + §4.9 mechanical rules) and added its own version table; the PHP guide should match the discipline so the two stay symmetric and future framework changes are auditable in-doc rather than only in git history. |
| 1.2     | 2026-07-28 | Christopher W. Olsen | **Standards-benchmark corrections (SB-06 + SB-12/13, from `docs/audits/standards-benchmark-2026-07.md`).** Security-adverse examples defused: the `Performer::validatePassword()` example is now explicitly labeled **legacy interop — do not replicate** with the OWASP target state (`password_hash`/`password_verify`, `hash_equals`) and a pointer to the auth-rewrite charter; the caching example's PSR-16-reserved colon key (`affiliate:`) replaced with a `memcache-keys.md`-conformant key + incident-history warning; `UsdCurrency` money-as-float and `AbstractDataType::validate()`'s generic first-violation `Exception` marked as **known deviations** (SB-07 / SB-06) rather than patterns to copy. Contradiction fixes: the Affiliate constructor example and AbstractModel API listing now use `markNonNullChanged()` per `models-and-datatypes.md` Rule 4 (was `markAllChanged()`, which writes NULLs over unloaded columns). Staleness: dead `architecture-review-payloads-vs-dtos.md` link → `../projects/reviews/payloads-vs-dtos.md`; Application Structure app list (had retired `HelloWorldApi`/`FanClubWeb`, missed five real apps) now defers to the CLAUDE.md table; new **API contracts (OpenAPI)** subsection documenting `#[ApiContract]` → `openapi:generate` → the gate's drift check. |
| 1.3     | 2026-07-28 | Christopher W. Olsen | **DataType validation now throws typed `DataTypeValidationException`** (`ExceptionCode::DATA_TYPE_VALIDATION_FAILED`) — closes the main half of the SB-06 known deviation. Both `validate()` snippets updated; the Base DataType Pattern callout now documents the catch contract (catch `DataTypeValidationException` for reject/re-roll paths; never `DtoException` around a DataType constructor — the never-matching catch was the root cause of the UsernameGenerator flake). Remaining deviations noted: first-violation-only message, per-construction validator build. |
| 1.4     | 2026-08-05 | Christopher W. Olsen | **Legacy latin1 charset boundary moved to the PDO seam** — new `#### The legacy charset boundary` subsection under `AbstractPdoSqlService`. The runtime DSN is `charset=latin1`, so legacy CP1252 bytes reached string DataTypes as invalid UTF-8 and their `#[Length]` charset check threw mid-hydration — the DM+ Android login failure ("This value does not match the expected UTF-8 charset." on a customer named "Martín", thrown before the password was even checked). `CharsetService::toUtf8()` existed but only 5 of 71 repositories called it. Both directions now apply centrally in `AbstractPdoSqlService` (fetched strings decode; bound parameters encode via the new `CharsetService::toCp1252()`), so **repositories must no longer call `CharsetService` themselves** — documented along with the two deliberate pass-throughs (non-UTF-8 / CP1252-unrepresentable input, rather than `mb_convert_encoding`'s silent `?` substitution) and the DSN-derived guard that leaves `SqlitePdoSqlService` alone and self-disables on the eventual utf8mb4 migration (`ARCH-12` / `PP-07`). Includes the **binary-column** callout: the seam transcodes text and PDO cannot report that a column is BLOB/VARBINARY (a `varbinary(16)` reports `native_type => VAR_STRING`, indistinguishable from `varchar`), so such fields MUST be named in the new `$binaryFields` argument on `query()`/`getColumn()`/`getRow()`/`getRows()` or their bytes are altered in both directions. |
| 1.5     | 2026-08-05 | Christopher W. Olsen | **New `Validation Locations` closer: "What none of these four layers is: authorization."** The section enumerated four validation layers (Payload, DataType, Service, DB constraints) without ever noting that none of them answers *may this caller act on this value?* — which let a real defect through: `SessionController::pepHash()` accepted `performer_id` in the request body and returned a live PEP broadcast credential for it, passing every validation layer, so any bearer-token holder could mint credentials for any performer (now takes only `session_token` and derives the performer from the verified payload). The new subsection states the rule — identity comes from the authenticated principal (`AccountAuthenticationMiddleware`'s session, or a verified token such as `PaSessionTokenService::verify()`), never from a request field — plus the two habits that follow: fail closed (401 when a verifier cannot verify, never a fallback identity) and don't accept a redundant id the endpoint can already derive. Paired with `complete-js-guide.md` v1.6, which carries the same rule at the `+server.js` boundary. |
