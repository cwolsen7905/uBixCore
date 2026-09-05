# Unit Testing Guidelines

This document establishes standards for writing PHPUnit tests in uBix Core. It assumes familiarity with PHPUnit and PHP testing concepts.

## Testing Requirements

**All concrete classes and enums must have a corresponding test case.** This is enforced by `PhpunitTestCasesTest`, which fails the build if any class lacks coverage.

Tests are required before merging feature branches to `dev`. Test-after development is acceptable, but no merge without tests.

## Coverage Targets

Coverage targets are tiered by component criticality:

| Component Type | Target | Rationale |
|----------------|--------|-----------|
| Services | 90%+ | Core business logic |
| Repositories | 90%+ | Data access correctness |
| DataTypes | 90%+ | Validation rules |
| Payloads | 90%+ | Request/response contracts |
| Models | 80%+ | Getter/setter logic |
| Collections | 80%+ | Iteration and filtering |
| Middleware | 80%+ | Request pipeline |
| Controllers | Structural | Thin delegation layer |
| DTOs | Structural | Data containers only |
| Enums | Structural | Static values |

**Structural coverage** means the automated reflection tests in `AbstractUbixConcreteClassOrEnumTestCase` are sufficient.

**Branch coverage** is more valuable than line coverage for catching logic errors. Focus on testing decision points.

## Test Structure

### File Location

Tests mirror the source structure:

```
php/Ubix/Service/AffiliateService.php
tests/Service/AffiliateServiceTest.php

php/Ubix/DataType/String/MpCode.php
tests/DataType/String/MpCodeTest.php
```

### Base Classes

All tests must extend `AbstractUbixConcreteClassOrEnumTestCase` and implement `UbixConcreteClassOrEnumTestCaseInterface`:

```php
<?php

declare(strict_types=1);

namespace Tests\Service;

use Tests\AbstractUbixConcreteClassOrEnumTestCase;
use Tests\UbixConcreteClassOrEnumTestCaseInterface;
use Ubix\Service\AffiliateService;

/**
 * PHPUnit test case for AffiliateService
 *
 * @coversDefaultClass \Ubix\Service\AffiliateService
 */
final class AffiliateServiceTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateService::class);
    }
}
```

The `testFollowingUbixStandards()` method is **required** and runs automated structural validation.

### Naming Conventions

- **Test classes**: `{ClassName}Test`
- **Test methods**: `test{MethodName}` or `test{MethodName}{Scenario}`
- **PHPDoc**: Always include `@coversDefaultClass`

```php
public function testGetById(): void
public function testGetByIdReturnsNullWhenNotFound(): void
public function testGetByIdThrowsExceptionForInvalidId(): void
```

## Testing Patterns by Component

### Services

Services contain business logic and require thorough functional testing:

```php
final class AffiliateServiceTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    private static ?AffiliateService $affiliateService = null;

    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateService::class);
    }

    public function testGetMediaBuyingDetails(): void
    {
        $result = $this->affiliateService()->getMediaBuyingDetails(
            new AffiliateId(10000001),
            new MpCode('0000'),
        );

        $this->assertInstanceOf(MediaBuyingDetails::class, $result);
        $this->assertFalse($result->isMediaBuying);
    }

    public function testGetMediaBuyingDetailsWithInvalidAffiliate(): void
    {
        $result = $this->affiliateService()->getMediaBuyingDetails(
            new AffiliateId(99999999),
            new MpCode('0000'),
        );

        $this->assertNull($result);
    }

    public function setUp(): void
    {
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates ...");
    }

    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates');
    }

    private function affiliateService(): AffiliateService
    {
        if (self::$affiliateService === null) {
            $service = $this->getContainer()->get(AffiliateService::class);
            $this->assertInstanceOf(AffiliateService::class, $service);
            self::$affiliateService = $service;
        }
        return self::$affiliateService;
    }
}
```

### Repositories

Repositories require database-backed integration tests:

```php
final class AffiliateSqlRepositoryTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    private static ?AffiliateSqlRepository $repository = null;

    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateSqlRepository::class);
    }

    public function testGetById(): void
    {
        $affiliate = $this->repository()->getById(new AffiliateId(10000001));

        $this->assertInstanceOf(Affiliate::class, $affiliate);
        $this->assertSame(10000001, $affiliate->getId()->value);
    }

    public function testGetByIdReturnsNullWhenNotFound(): void
    {
        $affiliate = $this->repository()->getById(new AffiliateId(99999999));

        $this->assertNull($affiliate);
    }

    public function testSave(): void
    {
        $affiliate = new Affiliate(
            id: new AffiliateId(10000002),
            name: new Varchar('Test Affiliate'),
            mpCode: new MpCode('TEST1'),
        );

        $this->repository()->save($affiliate);

        $retrieved = $this->repository()->getById(new AffiliateId(10000002));
        $this->assertSame('Test Affiliate', $retrieved->getName()->value);
    }

    public function setUp(): void
    {
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates (id, name, mp_code) VALUES (10000001, 'Seed Affiliate', 'SEED1')");
    }

    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates');
    }

    private function repository(): AffiliateSqlRepository
    {
        if (self::$repository === null) {
            $repository = $this->getContainer()->get(AffiliateSqlRepository::class);
            $this->assertInstanceOf(AffiliateSqlRepository::class, $repository);
            self::$repository = $repository;
        }
        return self::$repository;
    }
}
```

### DataTypes

DataTypes require validation rule testing:

```php
final class MpCodeTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(MpCode::class);
    }

    public function testValidMpCode(): void
    {
        $mpCode = new MpCode('ABCD');

        $this->assertSame('ABCD', $mpCode->value);
    }

    public function testMpCodeWithFiveCharacters(): void
    {
        $mpCode = new MpCode('ABCDE');

        $this->assertSame('ABCDE', $mpCode->value);
    }

    public function testMpCodeTooShortThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('MP Code must be at least 4 characters');

        new MpCode('ABC');
    }

    public function testMpCodeTooLongThrowsException(): void
    {
        $this->expectException(ValidationException::class);

        new MpCode('ABCDEF');
    }
}
```

### Payloads

Payloads require request validation testing:

```php
final class CreateAffiliateRequestPayloadTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CreateAffiliateRequestPayload::class);
    }

    public function testValidPayload(): void
    {
        $payload = new CreateAffiliateRequestPayload(
            name: 'Test Affiliate',
            mp_code: 'TEST1',
        );

        $this->assertInstanceOf(Varchar::class, $payload->name);
        $this->assertInstanceOf(MpCode::class, $payload->mpCode);
    }

    public function testMissingRequiredFieldAccumulatesError(): void
    {
        $this->expectException(DtoException::class);

        $payload = new CreateAffiliateRequestPayload(
            name: null,
            mp_code: 'TEST1',
        );
    }

    public function testInvalidMpCodeAccumulatesError(): void
    {
        $this->expectException(DtoException::class);

        $payload = new CreateAffiliateRequestPayload(
            name: 'Test',
            mp_code: 'AB', // Too short
        );
    }

    public function testMultipleErrorsAccumulated(): void
    {
        try {
            new CreateAffiliateRequestPayload(
                name: null,
                mp_code: 'AB',
            );
            $this->fail('Expected DtoException');
        } catch (DtoException $e) {
            $errors = $e->getDto()->errors;
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('mp_code', $errors);
        }
    }
}
```

### Controllers

Controllers require HTTP request/response testing:

```php
final class AffiliateControllerTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    private static ?AffiliateController $controller = null;

    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AffiliateController::class);
    }

    public function testCreateEndpointSuccess(): void
    {
        $request = $this->createJsonRequest('POST', '/affiliates', [
            'name' => 'New Affiliate',
            'mp_code' => 'NEW01',
        ]);
        $response = $this->createResponse();

        $result = $this->controller()->create($request, $response);

        $this->assertSame(201, $result->getStatusCode());
        $body = json_decode((string) $result->getBody(), true);
        $this->assertArrayHasKey('id', $body);
    }

    public function testCreateEndpointValidationFailure(): void
    {
        $request = $this->createJsonRequest('POST', '/affiliates', [
            'name' => '',
            'mp_code' => 'X', // Invalid
        ]);
        $response = $this->createResponse();

        $result = $this->controller()->create($request, $response);

        $this->assertSame(400, $result->getStatusCode());
        $body = json_decode((string) $result->getBody(), true);
        $this->assertArrayHasKey('fields', $body);
    }

    public function setUp(): void
    {
        $this->insertSeedData("INSERT INTO VSCASH.Affiliates ...");
    }

    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates');
    }

    private function controller(): AffiliateController
    {
        if (self::$controller === null) {
            $controller = $this->getContainer()->get(AffiliateController::class);
            $this->assertInstanceOf(AffiliateController::class, $controller);
            self::$controller = $controller;
        }
        return self::$controller;
    }

    private function createJsonRequest(string $method, string $uri, array $body): ServerRequestInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest($method, $uri);
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withParsedBody($body);
        return $request;
    }

    private function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
}
```

### Middleware

Middleware requires request pipeline testing:

```php
final class AuthenticationMiddlewareTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(AuthenticationMiddleware::class);
    }

    public function testAuthenticatedRequestPassesThrough(): void
    {
        $middleware = $this->createMiddleware();
        $request = $this->createAuthenticatedRequest();
        $handler = $this->createMock(RequestHandlerInterface::class);
        $expectedResponse = $this->createResponse();

        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($expectedResponse);

        $result = $middleware->process($request, $handler);

        $this->assertSame($expectedResponse, $result);
    }

    public function testUnauthenticatedRequestReturns401(): void
    {
        $middleware = $this->createMiddleware();
        $request = $this->createUnauthenticatedRequest();
        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects($this->never())->method('handle');

        $result = $middleware->process($request, $handler);

        $this->assertSame(401, $result->getStatusCode());
    }

    private function createMiddleware(): AuthenticationMiddleware
    {
        return new AuthenticationMiddleware(
            $this->createMock(Logger::class),
            $this->getContainer()->get(JsonService::class),
            $this->getContainer()->get(ResponseFactory::class),
        );
    }
}
```

### Console Commands

Console commands require input/output testing:

```php
final class ImportAffiliatesCommandTest extends AbstractUbixConcreteClassOrEnumTestCase implements UbixConcreteClassOrEnumTestCaseInterface
{
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(ImportAffiliatesCommand::class);
    }

    public function testExecuteWithValidFile(): void
    {
        $command = $this->getContainer()->get(ImportAffiliatesCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'file' => '/path/to/test/affiliates.csv',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Imported 5 affiliates', $commandTester->getDisplay());
    }

    public function testExecuteWithMissingFile(): void
    {
        $command = $this->getContainer()->get(ImportAffiliatesCommand::class);
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'file' => '/nonexistent/file.csv',
        ]);

        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('File not found', $commandTester->getDisplay());
    }

    public function setUp(): void
    {
        $this->insertSeedData("TRUNCATE TABLE VSCASH.Affiliates");
    }

    public function tearDown(): void
    {
        $this->insertSeedData('TRUNCATE TABLE VSCASH.Affiliates');
    }
}
```

## Database Testing

### Environment Setup

Tests connect to a real MariaDB via the `TEST_MYSQL_*` env-var set (separate from the runtime `MYSQL_*` connection so unit tests can never accidentally touch a runtime cluster):

- `TEST_MYSQL_WRITE_HOST`
- `TEST_MYSQL_WRITE_PORT`
- `TEST_MYSQL_WRITE_USERNAME`
- `TEST_MYSQL_WRITE_PASSWORD`
- `TEST_MYSQL_WRITE_DATABASE`
- `TEST_MYSQL_READ_*` (mirror set for read-side queries)

These come from `.env` locally. In CI they're set as GitLab project variables.

**Never mock SQL for repository or service tests.** Use the real test database on your sandbox.

#### Prefixed-schema isolation

Each operator (and each CI pipeline) runs tests against its own `<prefix><DB>` schema set on the test MariaDB so concurrent tests don't collide:

| Environment        | Prefix shape                  | Source                                     |
|--------------------|-------------------------------|--------------------------------------------|
| Local              | `tlocal_<unix-user>_`         | Auto-derived from `get_current_user()`     |
| CI pipeline (`dev`)| `t${CI_PIPELINE_ID}_`         | `DATABASE_PREFIX` set in the GitLab job    |

`tests/AbstractTestCase.php` reads `DATABASE_PREFIX` from env; when empty, it falls back to `tlocal_<sanitized-username>_`. Every `UbixDatabase::<CASE>->databaseName()` call inside production code resolves to `<prefix><DB>` so seed-data inserts, repository reads, and service tests all stay scoped to the operator's namespace.

#### Local bootstrap (one-time per session)

Before the first `vendor/bin/phpunit` in a fresh sandbox, materialise the prefixed schemas and apply every migration:

```bash
php bin/ubix database:resetSchema --target=test --yes
```

Under `--target=test` / `--target=sandbox`, the command auto-derives `--prefix=tlocal_<sanitized-user>_` from `get_current_user()` — the same source `tests/AbstractTestCase.php` uses — and announces the derived value in a `<comment>` line so you can sanity-check it. No `--prefix` flag needed; usernames with hyphens (`christopher-olsen`) normalise to underscores (`tlocal_christopher_olsen_`) so the result passes the `^[A-Za-z0-9_]+$` validation. Pass `--prefix=<P>` explicitly when you need to bootstrap someone else's namespace or a CI-shaped `t<digits>_` prefix.

Then run tests as normal:

```bash
vendor/bin/phpunit
vendor/bin/phpunit tests/Repository/Affiliate/AffiliateSqlRepositoryTest.php   # one file
php bin/ubix code:review                                                    # the full gate (every tool)
```

When you're done (optional — schemas are harmless if left in place until the next reset):

```bash
php bin/ubix database:dropSchemas --target=test --yes
```

`dropSchemas` uses the same auto-derive under `--target=test|sandbox`, so it drops exactly the namespace `resetSchema` materialised earlier in the session.

**Username-mismatch footgun:** `get_current_user()` returns the **owner of the PHP script**, not the shell user `$USER`. On a typical operator sandbox they're the same. If you bootstrapped from a different account than the one that owns `vendor/`, set `DATABASE_PREFIX=tlocal_<correct-username>_` before invoking `phpunit` to force the override (or pass `--prefix=<P>` explicitly to the CLI commands).

#### CI pipeline

The `lint-and-test-dev` GitLab job (auto-runs on every `dev` push) wraps the same lifecycle around `code:review`:

1. `database:resetSchema --target=test --prefix=t${CI_PIPELINE_ID}_ --yes` — fresh schemas per pipeline.
2. `code:review` — the full gate, every tool (authoritative list: *Machine Code Review* in [`CLAUDE.md`](../../CLAUDE.md)); `phpunit` and `pytest` are the legs that need the schemas above.
3. `database:dropSchemas --target=test --prefix=t${CI_PIPELINE_ID}_ --yes` — runs in `after_script`, so cleanup happens even when the main script fails.

The manual-play `drop-orphan-schemas-dev` Play button on every `dev` pipeline backstops the case where the runner pod itself crashes before reaching `after_script`. See [docs/projects/test-db-isolation/plan.md](../projects/test-db-isolation/plan.md) for the full design.

### Test Isolation

Each test must manage its own data using `setUp()` and `tearDown()`:

```php
public function setUp(): void
{
    // Insert only the data this test needs
    $this->insertSeedData("INSERT INTO table_name (col1, col2) VALUES ('val1', 'val2')");
}

public function tearDown(): void
{
    // Clean up all tables touched by this test
    $this->insertSeedData('TRUNCATE TABLE table_name');
}
```

**Requirements:**
- Tests must not depend on data from other tests
- Tests must clean up all data they create
- Use specific INSERT statements, not shared fixtures

### Accessing the Database

Use the inherited `insertSeedData()` method for setup/teardown, and the DI container for repositories:

```php
// For setup/teardown only
$this->insertSeedData($sql, $parameters);

// For testing repository methods
$repository = $this->getContainer()->get(MyRepository::class);
```

## Mocking Guidelines

### When to Mock

**Mock these:**
- External HTTP clients
- Third-party APIs
- Logger (when not testing logging behavior)
- Request handlers in middleware tests
- File system operations (when not testing file handling)

**Do not mock:**
- SQL/Database (use test database)
- Internal services (use real implementations via DI container)
- DataTypes and Payloads (test real validation)

### Mocking Syntax

Use PHPUnit's built-in mocking:

```php
// Simple mock
$logger = $this->createMock(Logger::class);

// Mock with expectations
$handler = $this->createMock(RequestHandlerInterface::class);
$handler->expects($this->once())
    ->method('handle')
    ->with($this->isInstanceOf(ServerRequestInterface::class))
    ->willReturn($response);

// Mock with callback
$httpClient = $this->createMock(HttpClientInterface::class);
$httpClient->method('send')
    ->willReturnCallback(function ($request) {
        return new Response(200, [], '{"success": true}');
    });
```

## Negative Testing

All components with business logic must include negative test cases.

### Required Negative Tests

**DataTypes:**
- Invalid input (wrong type, out of range, invalid format)
- Boundary conditions (min/max values)
- Empty/null handling

**Payloads:**
- Missing required fields
- Invalid field values
- Multiple simultaneous errors

**Services:**
- Not found scenarios
- Invalid state transitions
- Business rule violations

**Repositories:**
- Record not found
- Duplicate key handling
- Invalid foreign key references

### Negative Test Naming

Use descriptive names that indicate the failure scenario:

```php
public function testGetByIdReturnsNullWhenNotFound(): void
public function testCreateThrowsExceptionForDuplicateMpCode(): void
public function testValidationFailsWhenNameExceedsMaxLength(): void
public function testProcessRejectsInactiveAffiliate(): void
```

## Assertions

### Preferred Assertions

Use strict assertions:

```php
// Correct - strict comparison
$this->assertSame($expected, $actual);
$this->assertTrue($condition);
$this->assertNull($value);
$this->assertInstanceOf(ClassName::class, $object);

// Avoid - loose comparison
$this->assertEquals($expected, $actual); // Use assertSame instead
```

### Custom Assertions

For complex validations, extract to private methods:

```php
private function assertValidAffiliate(Affiliate $affiliate, array $expected): void
{
    $this->assertSame($expected['id'], $affiliate->getId()->value);
    $this->assertSame($expected['name'], $affiliate->getName()->value);
    $this->assertSame($expected['mpCode'], $affiliate->getMpCode()->value);
}
```

## Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/Service/AffiliateServiceTest.php

# Run specific test method
vendor/bin/phpunit --filter testGetById tests/Service/AffiliateServiceTest.php

# Run with coverage report
vendor/bin/phpunit --coverage-html coverage/

# Run tests matching a pattern
vendor/bin/phpunit --filter "testCreate"
```

## Checklist Before Merge

Before merging your feature branch to `dev`:

- [ ] All new concrete classes have corresponding test files
- [ ] All tests include `testFollowingUbixStandards()` method
- [ ] Services and repositories have functional tests (not just structural)
- [ ] DataTypes and Payloads have validation tests including negative cases
- [ ] Database tests use `setUp()`/`tearDown()` for isolation
- [ ] All tests pass locally: `vendor/bin/phpunit`
- [ ] Code quality passes: `vendor/bin/phpcs && vendor/bin/phpstan analyse`

## Python (pytest)

`*Py` apps and the shared `py/Ubix` framework test with **pytest**. The PHP conventions above are the model; the Python specifics:

- **Location** — one `tests/` dir per project (`py/Ubix/tests`, `app/*Py/tests`), `test_<module>.py` mirroring the source module it covers. `[tool.pytest.ini_options] testpaths = ["tests"]` in each `pyproject.toml`.
- **Coverage bar** — the PHP "every concrete class has a test" rule maps to **every source module has a `test_<module>.py`** with real behavioural coverage (not just an import smoke-test). New shared primitives in `ubix/` get a test in `py/Ubix/tests/`.
- **Doubles** — `httpx` drives the FastAPI surface (`from fastapi.testclient import TestClient` / `httpx`); `fakeredis` is the in-memory Redis fake (supports `WATCH`/`MULTI`) so consumer/debounce logic is tested without a live Redis. Reach for a real dependency double only at a genuine external boundary — same instinct as the PHP "mock at the seam" rule.
- **Negative cases** — assert the raise, same as the PHP negative-testing section: `with pytest.raises(ValueError): create_redis_client("")`.

Run from the repo-root `.venv` (built by `ubix py:install`):

```bash
.venv/bin/pytest                                   # all Python tests
.venv/bin/pytest app/RoomSfwCheckerPy              # one project
.venv/bin/pytest app/RoomSfwCheckerPy/tests/test_detector.py::test_rate   # one test
```

`code:review` runs pytest (plus ruff + mypy) per project over `py/Ubix` + every `app/*Py`, **skipped when the `.venv` is absent** — so run `ubix py:install` before trusting a local green. Full conventions: [`docs/standards/py-coding-guidelines.md`](py-coding-guidelines.md) + [`docs/architecture/complete-py-guide.md`](../architecture/complete-py-guide.md).

## Flaky-Test Policy

A **flaky test** — one that fails non-deterministically without a code change — is a defect, not noise. Because `code:review` gates every push to `dev`, a single flake can randomly block any lane's landing (this has happened: `UsernameGeneratorServiceTest::testSuggestReturnsThreeDistinctValidUsernames` blocked a push once and passed on the next run — a `random_int`-driven generator with no seed and a rare failure mode).

The rules:

1. **Never rerun-to-green and move on.** A flake that blocked a push gets *reported* even if a retry passed: note it in `AGENTS-COORD.md` (so other lanes recognize it) and record which test, which failure shape (assertion vs exception), and whether it reproduced in isolation.
2. **A known flake is either fixed or quarantined within a week.** Fix means removing the non-determinism (seed the RNG, inject a clock, pin the fixture) — not loosening the assertion (a weakened assertion is a permanently worse test; see "Tests may expose real bugs"). Quarantine means `markTestSkipped()` with a ticket/tracker reference in the message — never a deleted test, never a commented-out assertion.
3. **Non-determinism is a design smell in the code, not just the test.** If a service can't be tested deterministically, it's missing an injectable seam (clock, RNG, sequence provider). Adding the seam is in scope for the fix.
4. **Time-based and randomness-based tests must control their inputs.** No `sleep()`-based assertions, no unseeded randomness in the system under test, no assertions on wall-clock ordering.

## Future Considerations

### Mutation Testing

Mutation testing (using Infection PHP) measures test quality by introducing small changes to code and verifying tests catch them. A Mutation Score Indicator (MSI) of 60%+ indicates effective tests.

This is planned for future implementation.

### Performance Testing

Performance and load testing standards will be established in a future iteration of this document.
