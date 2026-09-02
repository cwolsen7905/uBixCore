<?php

declare(strict_types=1);

namespace Ubix\Tests\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Model\Creator;
use Ubix\Repository\Creator\CreatorReaderInterface as CreatorReader;
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

        $service = new CreatorProfileService($this->createStub(Logger::class), $reader, new JsonService($this->createStub(Logger::class)));

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

        $service = new CreatorProfileService($this->createStub(Logger::class), $reader, new JsonService($this->createStub(Logger::class)));

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

        $service = new CreatorProfileService($this->createStub(Logger::class), $this->createStub(CreatorReader::class), new JsonService($this->createStub(Logger::class)));
        $profile = $service->composePublicProfile($creator);

        $this->assertSame('grace', $profile['slug']);
        $this->assertSame('Grace Chapel', $profile['displayName']);
        $this->assertSame([['label' => 'Site', 'url' => 'https://example.org']], $profile['externalLinks']);
        $this->assertArrayNotHasKey('tiers', $profile);
        $this->assertArrayNotHasKey('posts', $profile);
        $this->assertArrayNotHasKey('streams', $profile);
    }
}
