<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\CreatorProfileResolution;
use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\Creator;
use Ubix\Payload\Request\CreatorProfileRequestPayload;
use Ubix\Repository\Creator\CreatorReaderInterface as CreatorReader;
use Ubix\Repository\Creator\CreatorWriterInterface as CreatorWriter;

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
     * @param CreatorWriter $creatorWriter Creator writer
     * @param JsonService   $jsonService   JSON service (external links decoding)
     */
    public function __construct(
        private Logger $logger,
        private CreatorReader $creatorReader,
        private CreatorWriter $creatorWriter,
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

    /**
     * Create a creator profile for a user (wizard profile step; FR-101, FR-402)
     *
     * @param int                          $userId  The owning user id
     * @param CreatorProfileRequestPayload $payload The validated profile fields
     *
     * @throws Exception If the user already has a creator, or the slug is taken
     *
     * @return Creator The created creator (status draft)
     */
    public function createCreatorProfile(int $userId, CreatorProfileRequestPayload $payload): Creator
    {
        if ($this->creatorReader->getCreatorByUserId($userId) !== null) {
            throw new Exception('This account already has a creator profile', ExceptionCode::CREATOR_ALREADY_EXISTS->value);
        }

        if ($this->creatorReader->slugExists($payload->slug->value)) {
            throw new Exception('This slug is already taken', ExceptionCode::CREATOR_SLUG_TAKEN->value);
        }

        $creator = new Creator(
            userId:      $userId,
            slug:        $payload->slug->value,
            displayName: $payload->displayName->value,
            bio:         $payload->bio,
            category:    $payload->category,
            faithTopic:  $payload->faithTopic,
            status:      CreatorStatus::DRAFT,
        );

        $this->creatorWriter->createCreator($creator);
        $this->logger->info('Creator profile created', [
            'creator_id' => $creator->getId(),
            'slug'       => $creator->getSlug(),
            'user_id'    => $userId,
        ]);

        return $creator;
    }

    /**
     * Get a user's own creator, any status (FR-401/404)
     *
     * @param int $userId The owning user id
     *
     * @return ?Creator The creator, or null if the user has none
     */
    public function getOwnCreator(int $userId): ?Creator
    {
        return $this->creatorReader->getCreatorByUserId($userId);
    }

    /**
     * Derive the onboarding state (registration TDS §2.3 — derived, not stored)
     *
     * Tier and payout steps report false until their surfaces exist.
     *
     * @param int $userId The owning user id
     *
     * @return array<string, mixed> The current step and per-step completion
     */
    public function getOnboardingState(int $userId): array
    {
        $creator = $this->creatorReader->getCreatorByUserId($userId);

        return [
            'currentStep' => $creator === null ? 'profile' : 'tier',
            'steps'       => [
                'payout'  => false,
                'profile' => $creator !== null,
                'tier'    => false,
            ],
        ];
    }
}
