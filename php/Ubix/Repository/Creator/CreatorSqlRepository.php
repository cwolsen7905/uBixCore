<?php

declare(strict_types=1);

namespace Ubix\Repository\Creator;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\CreatorOptions;
use Ubix\Enum\Creator\CreatorCategory;
use Ubix\Enum\Creator\CreatorStatus;
use Ubix\Model\Creator;
use Ubix\Repository\Creator\CreatorReaderInterface as CreatorReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * SQL-backed creator repository (read path; creator-profile TDS §2)
 *
 * @see \Ubix\Tests\Repository\Creator\CreatorSqlRepositoryTest PHPUnit test case
 */
final class CreatorSqlRepository implements CreatorReader
{
    /**
     * Constructor
     *
     * @param Logger     $logger     The Monolog logger
     * @param SqlService $sqlService The SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (required first dependency of every repository)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatorBySlug(string $slug): ?Creator
    {
        return $this->query(new CreatorOptions(slug: $slug, limit: 1))[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatorByUserId(int $userId): ?Creator
    {
        return $this->query(new CreatorOptions(userId: $userId, limit: 1))[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentSlugForRetiredSlug(string $slug): ?string
    {
        $sql = 'SELECT c.slug
                FROM sowingme.creator_slug_history h
                JOIN sowingme.creators c ON c.id = h.creator_id
                WHERE h.old_slug = :oldSlug
                LIMIT 1';

        $current = $this->sqlService->getColumn($sql, ['oldSlug' => $slug]);

        return is_string($current) ? $current : null;
    }

    /**
     * Query creators
     *
     * @param CreatorOptions $options DTO of options to generate the query
     *
     * @return array<int, Creator> The matching creators
     */
    private function query(CreatorOptions $options): array
    {
        $sql        = 'SELECT id, user_id, slug, display_name, bio, avatar_url, banner_url, category, faith_topic,
                       external_links, organization_id, payout_account_id, status, published_at, created_at, updated_at
                FROM sowingme.creators';
        $where      = [];
        $parameters = [];

        if ($options->id !== null) {
            $where[]          = 'id = :id';
            $parameters['id'] = $options->id;
        }

        if ($options->userId !== null) {
            $where[]              = 'user_id = :userId';
            $parameters['userId'] = $options->userId;
        }

        if ($options->slug !== null) {
            $where[]            = 'slug = :slug';
            $parameters['slug'] = $options->slug;
        }

        if ($options->status !== null) {
            $where[]              = 'status = :status';
            $parameters['status'] = $options->status;
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY id ASC';

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        $creators = [];
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $creators[] = $this->hydrateCreator($row);
        }

        return $creators;
    }

    /**
     * Hydrate a Creator model from a database row
     *
     * @param array<string, bool|int|float|string|null> $row The database result row
     *
     * @return Creator The hydrated creator model
     */
    private function hydrateCreator(array $row): Creator
    {
        return new Creator(
            id:                (int) $row['id'],
            userId:            (int) $row['user_id'],
            slug:              is_string($row['slug']) ? $row['slug'] : null,
            displayName:       is_string($row['display_name']) ? $row['display_name'] : null,
            bio:               is_string($row['bio']) ? $row['bio'] : null,
            avatarUrl:         is_string($row['avatar_url']) ? $row['avatar_url'] : null,
            bannerUrl:         is_string($row['banner_url']) ? $row['banner_url'] : null,
            category:          is_string($row['category']) ? CreatorCategory::from($row['category']) : null,
            faithTopic:        is_string($row['faith_topic']) ? $row['faith_topic'] : null,
            externalLinksJson: is_string($row['external_links']) ? $row['external_links'] : null,
            organizationId:    $row['organization_id'] !== null ? (int) $row['organization_id'] : null,
            payoutAccountId:   $row['payout_account_id'] !== null ? (int) $row['payout_account_id'] : null,
            status:            is_string($row['status']) ? CreatorStatus::from($row['status']) : null,
            publishedAt:       is_string($row['published_at']) ? new DateTime($row['published_at']) : null,
            createdAt:         is_string($row['created_at']) ? new DateTime($row['created_at']) : null,
            updatedAt:         is_string($row['updated_at']) ? new DateTime($row['updated_at']) : null,
        );
    }
}
