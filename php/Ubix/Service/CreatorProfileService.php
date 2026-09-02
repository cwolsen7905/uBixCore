<?php

declare(strict_types=1);

namespace Ubix\Service;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\CreatorProfileResolution;
use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Model\Creator;
use Ubix\Repository\Creator\CreatorReaderInterface as CreatorReader;

/**
 * Resolves public creator slugs and composes the public profile (creator-profile TDS §3/§5)
 *
 * Resolution order: live active creator → retired-slug redirect → not found.
 * Draft and suspended creators resolve to not-found (SRS FR-305, Q4). Sibling
 * sections (tiers, posts, streams) are omitted until their surfaces exist —
 * "call if present, omit if absent" (TDS §5).
 *
 * @see \Ubix\Tests\Service\CreatorProfileServiceTest PHPUnit test case
 */
final class CreatorProfileService
{
    /**
     * Constructor
     *
     * @param Logger        $logger        Logger
     * @param CreatorReader $creatorReader Creator reader
     * @param JsonService   $jsonService   JSON service (external links decoding)
     */
    public function __construct(
        private Logger $logger,
        private CreatorReader $creatorReader,
        private JsonService $jsonService,
    ) {
    }

    /**
     * Resolve a requested slug per TDS §3
     *
     * @param string $slug The requested slug
     *
     * @return CreatorProfileResolution The resolution outcome
     */
    public function resolveSlug(string $slug): CreatorProfileResolution
    {
        $creator = $this->creatorReader->getCreatorBySlug($slug);

        if ($creator !== null && $creator->getStatus() === CreatorStatus::ACTIVE) {
            return new CreatorProfileResolution(creator: $creator);
        }

        if ($creator !== null) {
            // Draft/suspended pages are unavailable, not partially rendered (FR-305)
            $this->logger->info('Creator page unavailable', [
                'slug'   => $slug,
                'status' => $creator->getStatus()?->value,
            ]);

            return new CreatorProfileResolution();
        }

        $currentSlug = $this->creatorReader->getCurrentSlugForRetiredSlug($slug);
        if ($currentSlug !== null) {
            return new CreatorProfileResolution(redirectToSlug: $currentSlug);
        }

        return new CreatorProfileResolution();
    }

    /**
     * Compose the public profile response body for a live creator (TDS §5)
     *
     * @param Creator $creator The live creator
     *
     * @return array<string, mixed> The public profile fields; optional sibling sections are absent keys
     */
    public function composePublicProfile(Creator $creator): array
    {
        $externalLinksJson = $creator->getExternalLinksJson();

        return [
            'avatarUrl'     => $creator->getAvatarUrl(),
            'bannerUrl'     => $creator->getBannerUrl(),
            'bio'           => $creator->getBio(),
            'category'      => $creator->getCategory()?->value,
            'displayName'   => $creator->getDisplayName(),
            'externalLinks' => $externalLinksJson !== null ? $this->jsonService->decode($externalLinksJson) : [],
            'faithTopic'    => $creator->getFaithTopic(),
            'publishedAt'   => $creator->getPublishedAt()?->format('Y-m-d'),
            'slug'          => $creator->getSlug(),
        ];
    }
}
