<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubPostUnlock;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Model\FanClubPostUnlock;
use Ubix\Repository\FanClubPostUnlock\FanClubPostUnlockWriterInterface as FanClubPostUnlockWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for writing fan club post unlock data
 *
 * @see \Ubix\Tests\Repository\FanClubPostUnlock\FanClubPostUnlockSqlRepositoryTest PHPUnit test case
 */
final class FanClubPostUnlockSqlRepository implements FanClubPostUnlockWriter
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
    public function save(FanClubPostUnlock $postUnlock): void
    {
        if ($postUnlock->getId() !== null && $postUnlock->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR FAN CLUB POST UNLOCKS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            $sql = 'INSERT INTO fanclub.fanclub_post_unlocks SET
					user_id                 = :userId,
					post_id                 = :postId,
					screen_name             = :screenName,
					purchased_at            = :purchasedAt,
					transaction_id          = :transactionId,
					content_type            = :contentType,
					ref_fanclub_transact_id = :refFanclubTransactId';

            $this->sqlService->query($sql, [
                'contentType'          => $postUnlock->getContentType(),
                'postId'               => $postUnlock->getPostId(),
                'purchasedAt'          => $postUnlock->getPurchasedAt()?->format(DateTime::ISO8601_EXPANDED),
                'refFanclubTransactId' => $postUnlock->getRefFanclubTransactId(),
                'screenName'           => $postUnlock->getScreenName(),
                'transactionId'        => $postUnlock->getTransactionId(),
                'userId'               => $postUnlock->getUserId(),
            ]);

            //
            //  Update the object
            //
            $postUnlock->setId((int)$this->sqlService->lastInsertId());
        }
    }
}
