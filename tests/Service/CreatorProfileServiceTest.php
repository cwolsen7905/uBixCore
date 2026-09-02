<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Model\Creator;
use Ubix\Payload\Request\CreatorProfileRequestPayload;
use Ubix\Repository\Creator\CreatorReaderInterface as CreatorReader;
use Ubix\Repository\Creator\CreatorWriterInterface as CreatorWriter;
use Ubix\Service\CreatorProfileService;
use Ubix\Service\JsonService;
use Ubix\Tests\AbstractUbixConcreteClassOrEnumTestCase as UbixConcreteClassOrEnumTestCase;
use Ubix\Tests\UbixConcreteClassOrEnumTestCaseInterface as IUbixConcreteClassOrEnumTestCase;

/**
 * PHPUnit test case for \Ubix\Service\CreatorProfileService
 *
 * @coversDefaultClass \Ubix\Service\CreatorProfileService
 */
final class CreatorProfileServiceTest extends UbixConcreteClassOrEnumTestCase implements IUbixConcreteClassOrEnumTestCase
{
    /**
     * Test that the class is following VSM standards
     *
     * @return void
     */
    public function testFollowingUbixStandards(): void
    {
        $this->testClassFollowingUbixStandards(CreatorProfileService::class);
    }

    /**
     * A live active creator resolves to found; draft and suspended resolve to not-found
     *
     * @return void
     */
    public function testResolveSlugStatusMatrix(): void
    {
        $active    = new Creator(id: 1, slug: 'grace', status: CreatorStatus::ACTIVE);
        $draft     = new Creator(id: 2, slug: 'wip', status: CreatorStatus::DRAFT);
        $suspended = new Creator(id: 3, slug: 'gone', status: CreatorStatus::SUSPENDED);

        $reader = $this->createStub(CreatorReader::class);
        $reader->method('getCreatorBySlug')->willReturnMap([
            ['grace', $active],
            ['wip', $draft],
            ['gone', $suspended],
            ['unknown', null],
        ]);
        $reader->method('getCurrentSlugForRetiredSlug')->willReturn(null);

        $service = new CreatorProfileService($this->createStub(Logger::class), $reader, $this->createStub(CreatorWriter::class), new JsonService($this->createStub(Logger::class)));

        $this->assertSame($active, $service->resolveSlug('grace')->creator);
        $this->assertNull($service->resolveSlug('wip')->creator);
        $this->assertNull($service->resolveSlug('gone')->creator);
        $this->assertNull($service->resolveSlug('gone')->redirectToSlug);
        $this->assertNull($service->resolveSlug('unknown')->creator);
    }

    /**
     * A retired slug resolves to a redirect at the current slug
     *
     * @return void
     */
    public function testResolveSlugRetiredSlugRedirects(): void
    {
        $reader = $this->createStub(CreatorReader::class);
        $reader->method('getCreatorBySlug')->willReturn(null);
        $reader->method('getCurrentSlugForRetiredSlug')->willReturnMap([
            ['old-name', 'new-name'],
            ['never-existed', null],
        ]);

        $service = new CreatorProfileService($this->createStub(Logger::class), $reader, $this->createStub(CreatorWriter::class), new JsonService($this->createStub(Logger::class)));

        $this->assertSame('new-name', $service->resolveSlug('old-name')->redirectToSlug);
        $this->assertNull($service->resolveSlug('old-name')->creator);
        $this->assertNull($service->resolveSlug('never-existed')->redirectToSlug);
    }

    /**
     * The composed public profile decodes external links and omits sibling sections
     *
     * @return void
     */
    public function testComposePublicProfileShape(): void
    {
        $creator = new Creator(
            id:                1,
            slug:              'grace',
            displayName:       'Grace Chapel',
            bio:               'A church.',
            externalLinksJson: '[{"label":"Site","url":"https://example.org"}]',
            status:            CreatorStatus::ACTIVE,
        );

        $service = new CreatorProfileService($this->createStub(Logger::class), $this->createStub(CreatorReader::class), $this->createStub(CreatorWriter::class), new JsonService($this->createStub(Logger::class)));
        $profile = $service->composePublicProfile($creator);

        $this->assertSame('grace', $profile['slug']);
        $this->assertSame('Grace Chapel', $profile['displayName']);
        $this->assertSame([['label' => 'Site', 'url' => 'https://example.org']], $profile['externalLinks']);
        $this->assertArrayNotHasKey('tiers', $profile);
        $this->assertArrayNotHasKey('posts', $profile);
        $this->assertArrayNotHasKey('streams', $profile);
    }

    /**
     * Create rejects a second creator for the same user and a taken slug; otherwise creates a draft
     *
     * @return void
     */
    public function testCreateCreatorProfileGuardsAndCreatesDraft(): void
    {
        $payload = new CreatorProfileRequestPayload(displayName: 'Grace Chapel', slug: 'grace', category: 'pastor');

        $reader = $this->createStub(CreatorReader::class);
        $reader->method('getCreatorByUserId')->willReturnMap([[1, new Creator(id: 9)], [2, null], [3, null]]);
        $reader->method('slugExists')->willReturnCallback(static function (string $slug): bool {
            return $slug === 'taken';
        });

        $writer = $this->createMock(CreatorWriter::class);
        $writer->method('createCreator')->willReturnCallback(static function (Creator $creator): void {
            $creator->setId(42);
        });

        $service = new CreatorProfileService($this->createStub(Logger::class), $reader, $writer, new JsonService($this->createStub(Logger::class)));

        try {
            $service->createCreatorProfile(1, $payload);
            $this->fail('Expected duplicate-creator exception');
        } catch (Exception $e) {
            $this->assertStringContainsString('already has a creator profile', $e->getMessage());
        }

        try {
            $service->createCreatorProfile(2, new CreatorProfileRequestPayload(displayName: 'X', slug: 'taken'));
            $this->fail('Expected slug-taken exception');
        } catch (Exception $e) {
            $this->assertStringContainsString('already taken', $e->getMessage());
        }

        $creator = $service->createCreatorProfile(3, $payload);
        $this->assertSame(42, $creator->getId());
        $this->assertSame(CreatorStatus::DRAFT, $creator->getStatus());
        $this->assertSame('grace', $creator->getSlug());
    }

    /**
     * Onboarding state derives the current step from entity presence
     *
     * @return void
     */
    public function testGetOnboardingStateDerivesStep(): void
    {
        $reader = $this->createStub(CreatorReader::class);
        $reader->method('getCreatorByUserId')->willReturnMap([[1, new Creator(id: 9)], [2, null]]);

        $service = new CreatorProfileService($this->createStub(Logger::class), $reader, $this->createStub(CreatorWriter::class), new JsonService($this->createStub(Logger::class)));

        $this->assertSame('tier', $service->getOnboardingState(1)['currentStep']);
        $this->assertSame('profile', $service->getOnboardingState(2)['currentStep']);
    }
}
