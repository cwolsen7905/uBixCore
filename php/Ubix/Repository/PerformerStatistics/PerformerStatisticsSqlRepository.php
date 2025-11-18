<?php

declare(strict_types=1);

namespace Ubix\Repository\PerformerStatistics;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PerformerStatisticsOptions;
use Ubix\Model\Performer;
use Ubix\Model\PerformerStatistics;
use Ubix\Repository\PerformerStatistics\PerformerStatisticsReaderInterface as PerformerStatisticsReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading performer statistic data
 *
 * @see \Ubix\Tests\Repository\PerformerStatistics\PerformerStatisticsSqlRepositoryTest PHPUnit test case
 */
final class PerformerStatisticsSqlRepository implements PerformerStatisticsReader
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getByPerformerId(int $performerId): array
    {
        return $this->query(new PerformerStatisticsOptions(
            performerId: $performerId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param PerformerStatisticsOptions $options DTO of options to generate the query
     *
     * @return PerformerStatistics[] Array of performer statistic object(s)
     */
    private function query(PerformerStatisticsOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT

					/* Public statistics (all customers can see these) */
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_posts
						WHERE model_id = :performerId
						AND visibility = "public"
						AND status = "published"
					) AS public_posts,
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_posts
						WHERE model_id = :performerId
						AND visibility = "members"
						AND status = "published"
					) AS members_posts,
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_posts
						WHERE model_id = :performerId
						AND visibility = "extra"
						AND status = "published"
					) AS extra_posts,

					/* Private statistics (only the performer can see their own) */
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_memberships
						WHERE model_id = :performerId
						AND status = "active"
						AND end_date > NOW()
					) AS active_subscriptions,
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_memberships
						WHERE model_id = :performerId
						AND status = "active"
						AND end_date > NOW()
						AND membership_type = "gifted"
					) AS gifted_subscriptions,
					(
						SELECT COUNT(*)
						FROM fanclub.files f
						INNER JOIN fanclub.model_files mf ON mf.file_id = f.id
						WHERE SUBSTRING(mime_type, 1, 6) = "image/"
						AND mf.model_id = :performerId
					) AS images,
					(
						SELECT COUNT(*)
						FROM fanclub.files f
						INNER JOIN fanclub.model_files mf ON mf.file_id = f.id
						WHERE SUBSTRING(mime_type, 1, 6) = "video/"
						AND mf.model_id = :performerId
					) AS videos,
					CAST(COALESCE((
						SELECT quota_mb
						FROM fanclub.model_storage_quota
						WHERE model_id = :performerId
						AND effective_start <= NOW()
						ORDER BY effective_start DESC
						LIMIT 1
					), ' . Performer::QUOTA_DEFAULT . ') AS UNSIGNED) AS quota_mb,
					CAST((
						SELECT ROUND(SUM(size_bytes) / (1024 * 1024), 2)
						FROM fanclub.files f
						INNER JOIN fanclub.model_files mf ON mf.file_id = f.id
						WHERE SUBSTRING(mime_type, 1, 6) = "image/"
						AND mf.model_id = :performerId
					) AS UNSIGNED) AS images_mb,
					CAST((
						SELECT ROUND(SUM(size_bytes) / (1024 * 1024), 2)
						FROM fanclub.files f
						INNER JOIN fanclub.model_files mf ON mf.file_id = f.id
						WHERE SUBSTRING(mime_type, 1, 6) = "video/"
						AND mf.model_id = :performerId
					) AS UNSIGNED) AS videos_mb,
					(
						SELECT ROUND(SUM(duration) / 60, 2)
						FROM fanclub.files f
						INNER JOIN fanclub.model_files mf ON mf.file_id = f.id
						WHERE SUBSTRING(mime_type, 1, 6) = "video/"
						AND mf.model_id = :performerId
					) AS videos_duration,
					(
						SELECT COUNT(*)
						FROM fanclub.files f
						INNER JOIN fanclub.model_files mf ON mf.file_id = f.id
						WHERE status != "completed"
						AND mf.model_id = :performerId
					) AS unconverted,
					CAST((
						SELECT COALESCE(ROUND(SUM(ft.orig_size) / (1024 * 1024), 2), 0)
						FROM fanclub.file_transformations ft
						INNER JOIN (
							SELECT file_id, MAX(transformed_at) AS max_time
							FROM fanclub.file_transformations
							GROUP BY file_id
						) latest ON ft.file_id = latest.file_id AND ft.transformed_at = latest.max_time
						INNER JOIN fanclub.model_files mf ON mf.file_id = ft.file_id
						WHERE mf.model_id = :performerId
					) AS UNSIGNED) AS preconversion_mb,
					CAST((
						SELECT COALESCE(ROUND(SUM(ft.new_size) / (1024 * 1024), 2), 0)
						FROM fanclub.file_transformations ft
						INNER JOIN (
							SELECT file_id, MAX(transformed_at) AS max_time
							FROM fanclub.file_transformations
							GROUP BY file_id
						) latest ON ft.file_id = latest.file_id AND ft.transformed_at = latest.max_time
						INNER JOIN fanclub.model_files mf ON mf.file_id = ft.file_id
						WHERE mf.model_id = :performerId
					) AS UNSIGNED) AS postconversion_mb,
					CAST((
						SELECT SUM(amount_credits)
						FROM fanclub.fanclub_transactions
						WHERE model_id = :performerId
					) AS UNSIGNED) AS credits_earned,
					(
						SELECT COUNT(*) AS total_transactions
						FROM fanclub.fanclub_transactions
						WHERE model_id = :performerId
					) AS transactions';

        $parameters['performerId'] = $options->performerId;

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     public_posts: ?int,
         *     members_posts: ?int,
         *     extra_posts: ?int,
         *     active_subscriptions: ?int,
         *     gifted_subscriptions: ?int,
         *     images: ?int,
         *     videos: ?int,
         *     quota_mb: ?int,
         *     images_mb: ?int,
         *     videos_mb: ?int,
         *     videos_duration: ?int,
         *     unconverted: ?int,
         *     preconversion_mb: ?int,
         *     postconversion_mb: ?int,
         *     credits_earned: ?int,
         *     transactions: ?int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $objects[] = new PerformerStatistics(
                publicPosts:         $row['public_posts'],
                membersPosts:        $row['members_posts'],
                extraPosts:          $row['extra_posts'],
                activeSubscriptions: $row['active_subscriptions'],
                giftedSubscriptions: $row['gifted_subscriptions'],
                images:              $row['images'],
                videos:              $row['videos'],
                quotaMb:             $row['quota_mb'],
                imagesMb:            $row['images_mb'],
                videosMb:            $row['videos_mb'],
                videosDuration:      $row['videos_duration'],
                unconverted:         $row['unconverted'],
                preconversionMb:     $row['preconversion_mb'],
                postconversionMb:    $row['postconversion_mb'],
                creditsEarned:       $row['credits_earned'],
                transactions:        $row['transactions'],
            );
        }

        return $objects;
    }
}
