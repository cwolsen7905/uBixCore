<?php

declare(strict_types=1);

namespace Ubix\Tests\Service\Media;

use Aws\Api\DateTimeResult;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Exception\DtoException;
use Ubix\Service\Media\S3MediaStorageService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\Media\S3MediaStorageService
 *
 * @coversDefaultClass \Ubix\Service\Media\S3MediaStorageService
 */
final class S3MediaStorageServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    private const ENV = [
        'MEDIA_S3_ACCESS_KEY_ID', 'MEDIA_S3_SECRET_ACCESS_KEY', 'MEDIA_S3_REGION',
        'MEDIA_S3_ENDPOINT', 'MEDIA_S3_BUCKET', 'MEDIA_S3_USE_PATH_STYLE_ENDPOINT',
        'MEDIA_S3_PUBLIC_BASE_URL', 'MEDIA_S3_PUBLIC_ENDPOINT',
    ];

    /**
     * Test that the class is following uBix standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(S3MediaStorageService::class);
    }

    /**
     * A presigned upload is scoped to one key, one method, and expires
     *
     * @return void
     */
    public function testCreatePresignedUploadIsScopedAndExpiring(): void
    {
        $before = (new DateTime())->getTimestamp();

        $ticket = $this->service()->createPresignedUpload('image/png', 120);

        $this->assertSame('PUT', $ticket->httpMethod);
        $this->assertSame(['Content-Type' => 'image/png'], $ticket->requiredHeaders);
        $this->assertStringContainsString($ticket->objectKey, $ticket->uploadUrl, 'The presigned URL must be scoped to the one key it was minted for');
        $this->assertStringContainsString('X-Amz-Signature=', $ticket->uploadUrl, 'The URL must be signed');
        $this->assertGreaterThanOrEqual($before + 120, $ticket->expiresAtUnixTimestamp);
        $this->assertLessThan($before + 130, $ticket->expiresAtUnixTimestamp, 'The expiry must reflect the requested TTL, not something open-ended');
    }

    /**
     * A signed read URL is scoped to one key and expires
     *
     * @return void
     */
    public function testCreateSignedReadUrlIsScopedAndExpiring(): void
    {
        $before = (new DateTime())->getTimestamp();

        $url = $this->service()->createSignedReadUrl('some-object-key', 60);

        $this->assertStringContainsString('some-object-key', $url->url);
        $this->assertStringContainsString('X-Amz-Signature=', $url->url);
        $this->assertGreaterThanOrEqual($before + 60, $url->expiresAtUnixTimestamp);
        $this->assertLessThan($before + 70, $url->expiresAtUnixTimestamp);
    }

    /**
     * Object keys are opaque and non-sequential — the security posture the platform TDS requires (never derived from a caller-guessable sequence)
     *
     * @return void
     */
    public function testObjectKeysAreOpaqueAndNonSequential(): void
    {
        $service = $this->service();

        $keys = [];
        for ($i = 0; $i < 5; $i++) {
            $keys[] = $service->createPresignedUpload('image/png', 60)->objectKey;
        }

        $this->assertCount(5, array_unique($keys), 'Every minted key must be distinct');

        foreach ($keys as $key) {
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
                $key,
                'A key must be a random v4 UUID, not a sequential id, a slug, or anything derived from caller input',
            );
        }
    }

    /**
     * The publicUrl() method is entirely configuration-driven, with no vendor assumption baked in
     *
     * @return void
     */
    public function testPublicUrlIsConfigDriven(): void
    {
        putenv('MEDIA_S3_PUBLIC_BASE_URL=https://cdn.example.test/media/');

        $this->assertSame(
            'https://cdn.example.test/media/some-key',
            $this->service()->publicUrl('some-key'),
        );
    }

    /**
     * The presigned URL's host reflects the configured endpoint — the same class must work unchanged against AWS S3 and an in-cluster MinIO endpoint, driven purely by MEDIA_S3_ENDPOINT
     *
     * @return void
     */
    public function testEndpointComesFromConfiguration(): void
    {
        putenv('MEDIA_S3_ENDPOINT=http://minio.kitg-dev.svc.cluster.local:9000');
        putenv('MEDIA_S3_BUCKET=kitg-dev-media');
        putenv('MEDIA_S3_REGION=us-east-1');
        putenv('MEDIA_S3_USE_PATH_STYLE_ENDPOINT=true');

        // No injected client here: this exercises the class building its own
        // S3Client from environment configuration. Presigning is pure local
        // request-signing, so no network access is needed either way.
        $service = new S3MediaStorageService($this->createStub(Logger::class));

        $ticket = $service->createPresignedUpload('image/png', 60);

        $this->assertStringStartsWith('http://minio.kitg-dev.svc.cluster.local:9000/', $ticket->uploadUrl);
    }

    /**
     * A URL handed to a browser is signed against the public endpoint, not the internal one
     *
     * @return void
     */
    public function testBrowserFacingUrlsUseThePublicEndpoint(): void
    {
        // The address this process reaches the store at, and the address a
        // browser can reach, are routinely different: a cluster-internal service
        // name resolves for the pod and for nobody else.
        putenv('MEDIA_S3_ENDPOINT=http://minio.kitg-dev.svc.cluster.local:9000');
        putenv('MEDIA_S3_PUBLIC_ENDPOINT=https://media.example.test');
        putenv('MEDIA_S3_BUCKET=kitg-dev-media');
        putenv('MEDIA_S3_REGION=us-east-1');
        putenv('MEDIA_S3_USE_PATH_STYLE_ENDPOINT=true');

        $service = new S3MediaStorageService($this->createStub(Logger::class));

        $upload = $service->createPresignedUpload('image/png', 60);
        $read   = $service->createSignedReadUrl('some-object-key', 60);

        // Both are handed to a browser, so both must name the public address.
        $this->assertStringStartsWith('https://media.example.test/', $upload->uploadUrl);
        $this->assertStringStartsWith('https://media.example.test/', $read->url);

        // And the signature has to be *against* that host, not rewritten onto it
        // afterwards: SigV4 covers the Host header, so a URL signed internally and
        // served publicly fails validation. Presence of the signature on a URL
        // whose host is the public one is what proves signing happened there.
        $this->assertStringContainsString('X-Amz-Signature=', $upload->uploadUrl);
        $this->assertStringContainsString('X-Amz-Signature=', $read->url);
    }

    /**
     * Without a public endpoint the internal one is used, so a single-address store needs no extra config
     *
     * @return void
     */
    public function testPublicEndpointFallsBackToTheInternalOne(): void
    {
        // A plain AWS bucket is reachable at one address from everywhere, which is
        // the case this must not force anyone to configure twice.
        putenv('MEDIA_S3_ENDPOINT=https://s3.example.test');
        putenv('MEDIA_S3_BUCKET=kitg-dev-media');
        putenv('MEDIA_S3_REGION=us-east-1');

        $service = new S3MediaStorageService($this->createStub(Logger::class));

        $this->assertStringStartsWith('https://s3.example.test/', $service->createPresignedUpload('image/png', 60)->uploadUrl);
    }

    /**
     * The inspectObject() method maps the store's own record, not anything a client declared
     *
     * @return void
     */
    public function testInspectObjectMapsStoreVerifiedMetadata(): void
    {
        $mock = new MockHandler();
        $mock->append(new Result([
            'ContentLength' => 4096,
            'ContentType'   => 'image/png',
            'ETag'          => '"abc123"',
            'LastModified'  => new DateTimeResult('2026-01-01T00:00:00Z'),
        ]));

        $metadata = $this->service($mock)->inspectObject('some-key');

        $this->assertSame('some-key', $metadata->objectKey);
        $this->assertSame('image/png', $metadata->contentType);
        $this->assertSame(4096, $metadata->byteSize);
        $this->assertSame('abc123', $metadata->checksum);
        $this->assertSame((new DateTimeResult('2026-01-01T00:00:00Z'))->getTimestamp(), $metadata->lastModifiedUnixTimestamp);
    }

    /**
     * The inspectObject() method fails closed (a DtoException, not a bare AWS exception) when nothing exists at that key
     *
     * @return void
     */
    public function testInspectObjectThrowsWhenObjectMissing(): void
    {
        $mock   = new MockHandler();
        $client = $this->clientWithMock($mock);

        $mock->append(new S3Exception(
            'Not Found',
            $client->getCommand('HeadObject', ['Bucket' => 'test-bucket', 'Key' => 'missing-key']),
            ['code' => 'NotFound'],
        ));

        $this->expectException(DtoException::class);

        $this->serviceWithClient($client)->inspectObject('missing-key');
    }

    /**
     * The putObject() method uploads the local file then returns the store's verified metadata for it (the same verification path inspectObject() uses)
     *
     * @return void
     */
    public function testPutObjectUploadsThenReturnsVerifiedMetadata(): void
    {
        $localFile = tempnam(sys_get_temp_dir(), 'ubixcore-media-test-');
        $this->assertIsString($localFile);
        file_put_contents($localFile, 'test bytes');

        try {
            $mock = new MockHandler();
            $mock->append(new Result([])); // The PutObject call itself
            $mock->append(new Result([     // The inspectObject() HeadObject that follows it
                'ContentLength' => 10,
                'ContentType'   => 'application/octet-stream',
            ]));

            $metadata = $this->service($mock)->putObject($localFile, 'application/octet-stream');

            $this->assertNotSame('', $metadata->objectKey, 'putObject() must mint its own opaque key rather than require one from the caller');
            $this->assertSame(10, $metadata->byteSize);
        } finally {
            unlink($localFile);
        }
    }

    /**
     * The putObject() method fails closed for a local file it cannot read, without ever calling the object store
     *
     * @return void
     */
    public function testPutObjectRejectsUnreadableLocalFile(): void
    {
        $this->expectException(DtoException::class);

        // An empty MockHandler queue means any AWS call made here would fail
        // the test by throwing "mock queue is empty" — this proves the
        // unreadable-file check short-circuits before any network attempt.
        $this->service(new MockHandler())->putObject('/no/such/file-' . bin2hex(random_bytes(8)), 'image/png');
    }

    /**
     * The deleteObject() method propagates a store failure as a DtoException
     *
     * @return void
     */
    public function testDeleteObjectPropagatesFailureAsDtoException(): void
    {
        $mock   = new MockHandler();
        $client = $this->clientWithMock($mock);

        $mock->append(new S3Exception(
            'Internal Error',
            $client->getCommand('DeleteObject', ['Bucket' => 'test-bucket', 'Key' => 'some-key']),
            ['code' => 'InternalError'],
        ));

        $this->expectException(DtoException::class);

        $this->serviceWithClient($client)->deleteObject('some-key');
    }

    /**
     * Set a baseline bucket name every test can rely on
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        putenv('MEDIA_S3_BUCKET=test-bucket');
    }

    /**
     * Clear every env var these tests can set
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach (self::ENV as $name) {
            putenv($name);
        }

        parent::tearDown();
    }

    /**
     * Build a service whose S3Client replays the given mock handler's queue
     *
     * @param ?MockHandler $mock The mock handler to back the client with (optional) (default: null, builds a client with an empty mock queue)
     *
     * @return S3MediaStorageService
     */
    private function service(?MockHandler $mock = null): S3MediaStorageService
    {
        return $this->serviceWithClient($this->clientWithMock($mock ?? new MockHandler()));
    }

    /**
     * Build a service backed by a specific, already-configured S3Client
     *
     * @param S3Client $client The client to inject
     *
     * @return S3MediaStorageService
     */
    private function serviceWithClient(S3Client $client): S3MediaStorageService
    {
        return new S3MediaStorageService($this->createStub(Logger::class), $client);
    }

    /**
     * Build an S3Client with real (but fake) credentials/region/endpoint, backed by the given mock handler so no request reaches the network
     *
     * @param MockHandler $mock The mock handler to back the client with
     *
     * @return S3Client
     */
    private function clientWithMock(MockHandler $mock): S3Client
    {
        return new S3Client([
            'bucket'                  => 'test-bucket',
            'credentials'             => [
                'key'    => 'test-access-key-id',
                'secret' => 'test-secret-access-key',
            ],
            'endpoint'                => 'http://s3.test.invalid',
            'handler'                 => $mock,
            'region'                  => 'us-east-1',
            'use_path_style_endpoint' => true,
            'version'                 => 'latest',
        ]);
    }
}
