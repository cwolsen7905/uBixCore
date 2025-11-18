<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubComment;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\FanClubCommentOptions;
use Ubix\Model\FanClubComment;
use Ubix\Repository\FanClubComment\FanClubCommentReaderInterface as FanClubCommentReader;
use Ubix\Repository\FanClubComment\FanClubCommentWriterInterface as FanClubCommentWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing fan club comment data
 *
 * @see \Ubix\Tests\Repository\FanClubComment\FanClubCommentSqlRepositoryTest PHPUnit test case
 */
final class FanClubCommentSqlRepository implements FanClubCommentReader, FanClubCommentWriter
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
    public function save(FanClubComment $comment): void
    {
        if ($comment->getId() !== null && $comment->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR FAN CLUB COMMENTS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            //
            //  INSERT into the database
            //
            $sql = 'INSERT INTO fanclub.fanclub_comments SET
					post_id      = :postId,
					user_id      = :userId,
					screen_name  = :screenName,
					comment_text = :commentText,
					created_at   = :createdAt,
					status       = :status';

            $this->sqlService->query($sql, [
                'commentText' => $comment->getCommentText(),
                'createdAt'   => $comment->getCreatedAt()?->format(DateTime::ISO8601_EXPANDED),
                'postId'      => $comment->getPostId(),
                'screenName'  => $comment->getScreenName(),
                'status'      => $comment->getStatus(),
                'userId'      => $comment->getUserId(),
            ]);

            //
            //  Update the object
            //
            $comment->setId((int)$this->sqlService->lastInsertId());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getByPostId(int $postId): array
    {
        return $this->query(new FanClubCommentOptions(
            orderBy: 'fc.created_at DESC',
            postId:  $postId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param FanClubCommentOptions $options DTO of options to generate the query
     *
     * @return FanClubComment[] An array of comment objects
     */
    private function query(FanClubCommentOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					fc.id,
					fc.post_id,
					fc.user_id,
					fc.screen_name,
					fc.comment_text,
					fc.created_at,
					fc.status,
					fc.admin_id,
					fc.deactivated_at,
					fc.deactivation_reason
				FROM fanclub.fanclub_comments fc
				WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND fc.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->postId !== null) {
            $sql .= ' AND fc.post_id = :postId';

            $parameters['postId'] = $options->postId;
        }

        if ($options->orderBy !== null) {
            $sql .= ' ORDER BY ' . $options->orderBy;
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
         *     comment_text: ?string,
         *     created_at: ?string,
         *     status: ?string,
         *     admin_id: ?int,
         *     deactivated_at: ?string,
         *     deactivation_reason: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new FanClubComment(
                id:                 $row['id'],
                postId:             $row['post_id'],
                userId:             $row['user_id'],
                screenName:         $row['screen_name'],
                commentText:        $row['comment_text'],
                createdAt:          $row['created_at'] !== null ? new DateTime($row['created_at']) : null,
                status:             $row['status'],
                adminId:            $row['admin_id'],
                deactivatedAt:      $row['deactivated_at'] !== null ? new DateTime($row['deactivated_at']) : null,
                deactivationReason: $row['deactivation_reason'],
            );
        }

        return $objects;
    }
}
