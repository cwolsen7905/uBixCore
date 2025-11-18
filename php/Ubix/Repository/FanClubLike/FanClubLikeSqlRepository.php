<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubLike;

use DateTime;
use Exception;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\FanClubLikeOptions;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\FanClubLike;
use Ubix\Repository\FanClubLike\FanClubLikeReaderInterface as FanClubLikeReader;
use Ubix\Repository\FanClubLike\FanClubLikeWriterInterface as FanClubLikeWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing fan club like data
 *
 * @see \Ubix\Tests\Repository\FanClubLike\FanClubLikeSqlRepositoryTest PHPUnit test case
 */
final class FanClubLikeSqlRepository implements FanClubLikeReader, FanClubLikeWriter
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
     *
     * @throws LogicException If not yet implemented code is accessed
     */
    public function save(FanClubLike $like): void
    {
        if ($like->getId() !== null && $like->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR FAN CLUB LIKES HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            //
            //  INSERT into the database
            //
            $sql = 'INSERT INTO fanclub.fanclub_likes SET
					post_id     = :postId,
					user_id     = :userId,
					screen_name = :screenName,
					created_at  = :createdAt';

            $this->sqlService->query($sql, [
                'createdAt'  => $like->getCreatedAt()?->format(DateTime::ISO8601_EXPANDED),
                'postId'     => $like->getPostId(),
                'screenName' => $like->getScreenName(),
                'userId'     => $like->getUserId(),
            ]);

            //
            //  Update the object
            //
            $like->setId((int)$this->sqlService->lastInsertId());
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If deleting the like fails
     */
    public function delete(FanClubLike $like): void
    {
        //
        //  Ensure a delete can occur
        //
        if ($like->getId() === null || $like->getId() <= 0) {
            throw new Exception('Deleting the fan club like failed.', ExceptionCode::FAIL_TO_DELETE_INVALID_FAN_CLUB_LIKE->value);
        }

        //
        //  DELETE from the database
        //
        $sql = 'DELETE FROM fanclub.fanclub_likes
				WHERE id = :id';

        $this->sqlService->query($sql, [
            'id' => $like->getId(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $userId, int $postId): array
    {
        return $this->query(new FanClubLikeOptions(
            postId: $postId,
            userId: $userId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param FanClubLikeOptions $options DTO of options to generate the query
     *
     * @return FanClubLike[] An array of like objects
     */
    private function query(FanClubLikeOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					fl.id,
					fl.post_id,
					fl.user_id,
					fl.screen_name,
					fl.created_at
				FROM fanclub.fanclub_likes fl
				WHERE 1 = 1';

        if ($options->postId !== null) {
            $sql .= ' AND fl.post_id = :postId';

            $parameters['postId'] = $options->postId;
        }

        if ($options->userId !== null) {
            $sql .= ' AND fl.user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     post_id: ?int,
         *     user_id: ?int,
         *     screen_name: ?string,
         *     created_at: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new FanClubLike(
                id:         $row['id'],
                postId:     $row['post_id'],
                userId:     $row['user_id'],
                screenName: $row['screen_name'],
                createdAt:  $row['created_at'] !== null ? new DateTime($row['created_at']) : null,
            );
        }

        return $objects;
    }
}
