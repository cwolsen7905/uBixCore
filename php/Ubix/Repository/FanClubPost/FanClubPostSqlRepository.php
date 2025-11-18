<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubPost;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use stdClass;
use Ubix\DataTransferObject\SqlRepository\FanClubPostOptions;
use Ubix\Model\FanClubFile;
use Ubix\Model\FanClubPost;
use Ubix\Model\FanClubTransaction;
use Ubix\Model\Performer;
use Ubix\Repository\FanClubPost\FanClubPostReaderInterface as FanClubPostReader;
use Ubix\Service\FanClubService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading fan club post data
 *
 * @see \Ubix\Tests\Repository\FanClubPost\FanClubPostSqlRepositoryTest PHPUnit test case
 */
final class FanClubPostSqlRepository implements FanClubPostReader
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
    public function getByMostLiked(): array
    {
        return $this->query(new FanClubPostOptions(
            dateFiltering: true,
            limit:         1,
            orderBy:       'likes_count DESC',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByMostCommented(): array
    {
        return $this->query(new FanClubPostOptions(
            dateFiltering: true,
            limit:         1,
            orderBy:       'comments_count DESC',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByTopHashtag(): array
    {
        $topHashtagMetadata = $this->getTopHashtagAndPostId();

        return $this->query(new FanClubPostOptions(
            dateFiltering: true,
            limit:         1,
            orderBy:       'COALESCE(fp.published_at, fp.scheduled_at) DESC',
            topHashtag:    isset($topHashtagMetadata->top_hashtag) && is_string($topHashtagMetadata->top_hashtag) ? $topHashtagMetadata->top_hashtag : null,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByDateCreated(): array
    {
        return $this->query(new FanClubPostOptions(
            dateFiltering: true,
            limit:         1,
            orderBy:       'COALESCE(fp.published_at, fp.scheduled_at) DESC',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicPosts(): array
    {
        return $this->query(new FanClubPostOptions(
            alternativeDateFiltering: true,
            limit:                    FanClubService::TRENDING_POSTS_COUNT,
            orderBy:                  'fp.published_at DESC',
            visibility:               'public',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): array
    {
        return $this->query(new FanClubPostOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByModelId(int $performerId, ?string $status = 'published', ?string $visibility = null, ?bool $purchasedOnly = false, ?int $userId = null): array
    {
        return $this->query(new FanClubPostOptions(
            activeUser:    $userId,
            modelId:       $performerId,
            purchasedOnly: $purchasedOnly,
            orderBy:       $purchasedOnly === true ? 'ft.created_at DESC' : 'fp.created_at DESC',
            status:        $status,
            visibility:    $visibility,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getUnlockedByUserId(int $userId, ?int $postId = null): array // NOT_IMPLEMENTED: This should probably be removed and be available as a method in \Ubix\Repository\FanClubPostUnlockReaderInterface
    {
        return $this->query(new FanClubPostOptions(
            alternativeDateFiltering: true,
            orderBy:                  'ft.created_at DESC',
            unlockedFor:              $userId,
            id:                       $postId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param FanClubPostOptions $options DTO of options to generate the query
     *
     * @return FanClubPost[] Array of fan club post object(s)
     */
    private function query(FanClubPostOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					fp.id,
					fp.model_id,
					fp.model_id AS performer_id, /* This is not a bug, we want the fp.model_id value in both model_id and performer_id for the time being */
					m.name AS performer_name,
					REPLACE(m.name, "_", "-") AS performer_slug,
					m.image AS performer_image,
					m.studio AS performer_studio_code,
					s.broadcaster_id AS performer_broadcaster_id,
					m.harvest_code AS performer_harvest_code,
					fp.file_id,
					f.handle AS file_handle,
					f.thumbnail_handle AS file_thumbnail_handle,
					f.mime_type AS file_mime_type,
					' . ($options->topHashtag !== null ? 'CONCAT(fp.title, " (", :topHashtag, ")")' : 'fp.title') . ' AS title,
					fp.description,
					fp.hashtags,
					fp.visibility,
					fp.price_credits,
					fp.scheduled_at,
					COALESCE(fp.published_at, fp.scheduled_at) AS published_at,
					fp.status,
					fp.created_at,
					fp.updated_at,
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_likes
						WHERE post_id = fp.id
					) AS likes_count,
					(
						SELECT COUNT(*)
						FROM fanclub.fanclub_comments
						WHERE post_id = fp.id
						AND status = "active"
					) AS comments_count,
					CAST((
						SELECT SUM(_ft.amount_credits)
						FROM fanclub.fanclub_post_unlocks _fpu
						INNER JOIN fanclub.fanclub_transactions _ft ON _ft.id = _fpu.transaction_id
						WHERE _fpu.post_id = fp.id
					) AS UNSIGNED) AS lifetime_credits,
					fpu.id AS unlock_id,
					fpu.user_id AS unlock_user_id,
					fpu.post_id AS unlock_post_id,
					fpu.screen_name AS unlock_screen_name,
					fpu.purchased_at AS unlock_purchased_at,
					fpu.transaction_id AS unlock_transaction_id,
					fpu.content_type AS unlock_content_type,
					fpu.ref_fanclub_transact_id AS unlock_ref_fanclub_transact_id,
					ft.user_id AS transaction_user_id,
					ft.type AS transaction_type,
					ft.amount_credits AS transaction_amount_credits,
					ft.screen_name AS transaction_screen_name,
					ft.broadcaster_id AS transaction_broadcaster_id,
					ft.studio_code AS transaction_studio_code,
					ft.location_id AS transaction_location_id,
					ft.ref_fanclub_transact_id AS transaction_ref_fanclub_transact_id,
					ft.created_at AS transaction_created_at';

        if ($options->topHashtag !== null) {
            $parameters['topHashtag'] = $options->topHashtag;
        }

        if ($options->activeUser !== null) {
            $sql .= ',
					IF(
						(
							SELECT COUNT(*)
							FROM fanclub.fanclub_likes _fl
							WHERE _fl.post_id = fp.id
							AND _fl.user_id = :activeUser
							LIMIT 1
						) > 0, "Y", "N"
					) AS has_liked,
					IF(
						(
							SELECT COUNT(*)
							FROM fanclub.fanclub_memberships _fm
							LEFT OUTER JOIN fanclub.fanclub_transactions _ft ON _ft.id = _fm.transaction_id
							WHERE _fm.user_id = :activeUser
							AND _fm.status IN ("active","canceled")
							AND _fm.end_date > NOW()
							LIMIT 1
						) > 0, "Y", "N"
					) AS c,
					IF(
						fp.visibility = "extra"
						AND (
							SELECT COUNT(*)
							FROM fanclub.fanclub_post_unlocks _fpu
							WHERE _fpu.post_id = fp.id
							AND _fpu.user_id = :activeUser
							LIMIT 1
						) > 0, "Y", "N"
					) AS has_unlocked';

            $parameters['activeUser'] = $options->activeUser;
        }

        $sql .= '	FROM fanclub.fanclub_posts fp
					INNER JOIN ntl_db.models m ON m.id = fp.model_id
					INNER JOIN ntl_db.studios s ON s.studio = m.studio
					INNER JOIN fanclub.files f ON f.id = fp.file_id
					LEFT OUTER JOIN fanclub.fanclub_post_unlocks fpu ON fpu.post_id = fp.id
					LEFT OUTER JOIN fanclub.fanclub_transactions ft ON ft.id = fpu.transaction_id
					WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND fp.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->modelId !== null) {
            $sql .= ' AND fp.model_id = :modelId';

            $parameters['modelId'] = $options->modelId;
        }

        if ($options->status !== null) {
            $sql .= ' AND fp.status = :status';

            $parameters['status'] = $options->status;
        }

        if ($options->unlockedFor !== null) {
            $sql .= ' AND fpu.user_id = :unlockedFor';

            $parameters['unlockedFor'] = $options->unlockedFor;
        }

        if ($options->visibility !== null) {
            $sql .= ' AND fp.visibility = :visibility';

            $parameters['visibility'] = $options->visibility;
        }

        if ($options->purchasedOnly === true) {
            $sql .= ' AND fpu.id IS NOT NULL';
        } elseif ($options->purchasedOnly === false) {
            $sql .= ' AND fpu.id IS NULL';
        }

        $sql .= ' GROUP BY fp.id';

        if ($options->orderBy !== null) {
            $sql .= ' ORDER BY ' . $options->orderBy;
        }

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     model_id: ?int,
         *     file_id: ?int,
         *     title: ?string,
         *     description: ?string,
         *     hashtags: ?string,
         *     visibility: ?string,
         *     price_credits: ?int,
         *     scheduled_at: ?string,
         *     published_at: ?string,
         *     status: ?string,
         *     created_at: ?string,
         *     updated_at: ?string,
         *     likes_count: ?int,
         *     comments_count: ?int,
         *     lifetime_credits: ?int,
         *     has_liked: ?string,
         *     has_membership: ?string,
         *     has_unlocked: ?string,
         *     transaction: ?int,
         *     file_handle: ?string,
         *     file_id: ?int,
         *     file_mime_type: ?string,
         *     file_thumbnail_handle: ?string,
         *     performer_harvest_code: ?int,
         *     performer_id: ?int,
         *     performer_image: ?string,
         *     performer_name: ?string,
         *     performer_slug: ?string,
         *     performer_studio_code: ?string,
         *     performer_broadcaster_id: ?int,
         *     transaction_amount_credits: ?int,
         *     transaction_broadcaster_id: ?int,
         *     transaction_created_at: ?string,
         *     unlock_transaction_id: ?int,
         *     transaction_location_id: ?int,
         *     transaction_ref_fanclub_transact_id: ?int,
         *     transaction_screen_name: ?string,
         *     transaction_studio_code: ?string,
         *     transaction_type: ?string,
         *     transaction_user_id: ?int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $file = new FanClubFile(
                handle:          $row['file_handle'],
                id:              $row['file_id'],
                mimeType:        $row['file_mime_type'],
                thumbnailHandle: $row['file_thumbnail_handle'],
            );

            $performer = new Performer(
                harvestCode:   $row['performer_harvest_code'],
                id:            $row['performer_id'],
                image:         $row['performer_image'],
                name:          $row['performer_name'],
                slug:          $row['performer_slug'],
                studioCode:    $row['performer_studio_code'],
                broadcasterId: $row['performer_broadcaster_id'],
            );

            $transaction = new FanClubTransaction(
                amountCredits:        $row['transaction_amount_credits'],
                broadcasterId:        $row['transaction_broadcaster_id'],
                createdAt:            $row['transaction_created_at'] !== null ? new DateTime($row['transaction_created_at']) : null,
                id:                   $row['unlock_transaction_id'],
                locationId:           $row['transaction_location_id'],
                refFanclubTransactId: $row['transaction_ref_fanclub_transact_id'],
                screenName:           $row['transaction_screen_name'],
                studioCode:           $row['transaction_studio_code'],
                type:                 $row['transaction_type'],
                userId:               $row['transaction_user_id'],
            );

            $objects[] = new FanClubPost(
                id:              $row['id'],
                modelId:         $row['model_id'],
                performer:       $performer,
                fileId:          $row['file_id'],
                file:            $file,
                title:           $row['title'],
                description:     $row['description'],
                hashtags:        $row['hashtags'],
                visibility:      $row['visibility'],
                priceCredits:    $row['price_credits'],
                scheduledAt:     $row['scheduled_at'] !== null ? new DateTime($row['scheduled_at']) : null,
                publishedAt:     $row['published_at'] !== null ? new DateTime($row['published_at']) : null,
                status:          $row['status'],
                createdAt:       $row['created_at'] !== null ? new DateTime($row['created_at']) : null,
                updatedAt:       $row['updated_at'] !== null ? new DateTime($row['updated_at']) : null,
                likesCount:      $row['likes_count'],
                commentsCount:   $row['comments_count'],
                lifetimeCredits: $row['lifetime_credits'],
                hasLiked:        $row['has_liked'] ?? 'N',
                hasMembership:   $row['has_membership'] ?? 'N',
                hasUnlocked:     $row['has_unlocked'] ?? 'N',
                transaction:     $transaction,
            );
        }
        return $objects;
    }

    /**
     * This whole method is a temporary hack to emulate what Miro is doing in his Python repo - he is doing multiple queries to end up at a result and we need to simplify his step of determining the "top hashtag" to make things simple - in truth his database design could be improved so that hashtags aren't a single CSV column within fanclub.fanclub_posts which would negate the need for this function (and also make his Python code simpler) but this will scratch the itch until we have time to get to that
     *
     * @return ?stdClass Details of the top hash tag and post ID in an object or null on failure
     */
    private function getTopHashtagAndPostId(): ?stdClass
    {
        $hashtagCounts = [];
        $hashtagPosts  = [];

        $oneHundredDaysAgo = (new DateTime('-100 days'))->format(DateTime::ISO8601_EXPANDED);

        $sql = 'SELECT
					fp.id AS post_id,
					fp.model_id,
					fp.title,
					fp.description,
					fp.hashtags,
					COALESCE(fp.published_at, fp.scheduled_at) AS published_at,
					f.handle
				FROM fanclub.fanclub_posts fp
				JOIN fanclub.files f ON fp.file_id = f.id
				WHERE (
					(fp.status = "published" AND fp.published_at >= "' . $oneHundredDaysAgo . '")
					OR (fp.status = "scheduled" AND fp.scheduled_at <= NOW() AND fp.scheduled_at >= "' . $oneHundredDaysAgo . '")
				)
				AND fp.hashtags IS NOT NULL
				AND fp.hashtags <> ""';

        /**
         * @var array{
         *     post_id: ?int,
         *     model_id: ?int,
         *     title: ?string,
         *     description: ?string,
         *     hashtags: ?string,
         *     published_at: ?string,
         *     handle: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql) as $row) {
            $raw = trim(is_string($row['hashtags']) ? $row['hashtags'] : '');

            $tags = preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $tagLower = strtolower($tag);
                    if (!isset($hashtagCounts[$tagLower])) {
                        $hashtagCounts[$tagLower] = 0;
                        $hashtagPosts[$tagLower]  = $row;
                    }

                    $hashtagCounts[$tagLower]++;
                }
            }
        }

        if (!empty($hashtagCounts)) {
            arsort($hashtagCounts);
            reset($hashtagCounts);
            $topTag = key($hashtagCounts);

            return (object)[
                'post_id'     => $hashtagPosts[$topTag]['post_id'],
                'top_hashtag' => $topTag,
            ];
        }

        return null;
    }
}
