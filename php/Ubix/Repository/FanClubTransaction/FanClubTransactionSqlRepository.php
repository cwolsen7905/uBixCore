<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubTransaction;

use DateTime;
use LogicException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\FanClubTransactionOptions;
use Ubix\Model\FanClubFile;
use Ubix\Model\FanClubPost;
use Ubix\Model\FanClubTransaction;
use Ubix\Repository\FanClubTransaction\FanClubTransactionReaderInterface as FanClubTransactionReader;
use Ubix\Repository\FanClubTransaction\FanClubTransactionWriterInterface as FanClubTransactionWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing fan club transaction data
 *
 * @see \Ubix\Tests\Repository\FanClubTransaction\FanClubTransactionSqlRepositoryTest PHPUnit test case
 */
final class FanClubTransactionSqlRepository implements FanClubTransactionReader, FanClubTransactionWriter
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
    public function save(FanClubTransaction $transaction): void
    {
        if ($transaction->getId() !== null && $transaction->getId() > 0) { // If there is a valid ID then update the database
            throw new LogicException('UPDATES FOR FAN CLUB TRANSACTIONS HAVE NOT BEEN CODED YET'); // NOT_IMPLEMENTED: needs to be done
        } else { // There is no valid ID so insert into the database
            //
            //  INSERT into the database
            //
            $sql = 'INSERT INTO fanclub.fanclub_transactions SET
					user_id                 = :userId,
					model_id                = :modelId,
					type                    = :type,
					amount_credits          = :amountCredits,
					screen_name             = :screenName,
					broadcaster_id          = :broadcasterId,
					studio_code             = :studioCode,
					location_id             = :locationId,
					ref_fanclub_transact_id = :refFanclubTransactId,
					created_at              = :createdAt';

            $this->sqlService->query($sql, [
                'amountCredits'        => $transaction->getAmountCredits(),
                'broadcasterId'        => $transaction->getBroadcasterId(),
                'createdAt'            => $transaction->getCreatedAt()?->format(DateTime::ISO8601_EXPANDED),
                'locationId'           => $transaction->getLocationId(),
                'modelId'              => $transaction->getModelId(),
                'refFanclubTransactId' => $transaction->getRefFanclubTransactId(),
                'screenName'           => $transaction->getScreenName(),
                'studioCode'           => $transaction->getStudioCode(),
                'type'                 => $transaction->getType(),
                'userId'               => $transaction->getUserId(),
            ]);

            //
            //  Update the object
            //
            $transaction->setId((int)$this->sqlService->lastInsertId());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): array
    {
        return $this->query(new FanClubTransactionOptions(
            id: $id,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByUserId(int $userId): array
    {
        return $this->query(new FanClubTransactionOptions(
            userId: $userId,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByModelId(int $performerId): array
    {
        return $this->query(new FanClubTransactionOptions(
            modelId: $performerId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param FanClubTransactionOptions $options DTO of options to generate the query
     *
     * @return FanClubTransaction[] An array of fan club transaction object(s)
     */
    private function query(FanClubTransactionOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					ft.id,
					ft.user_id,
					ft.model_id,
					ft.type,
					ft.amount_credits,
					ft.screen_name,
					ft.broadcaster_id,
					ft.studio_code,
					ft.location_id,
					ft.ref_fanclub_transact_id,
					ft.created_at,
					fp.title AS post_title,
					f.mime_type AS file_mime_type
				FROM fanclub.fanclub_transactions ft
				LEFT OUTER JOIN fanclub.fanclub_post_unlocks fpu ON fpu.transaction_id = ft.id
				LEFT OUTER JOIN fanclub.fanclub_posts fp ON fp.id = fpu.post_id
				LEFT OUTER JOIN fanclub.files f ON f.id = fp.file_id
				WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND ft.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->modelId !== null) {
            $sql .= ' AND ft.model_id = :modelId';

            $parameters['modelId'] = $options->modelId;
        }

        if ($options->userId !== null) {
            $sql .= ' AND ft.user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        $sql .= ' ORDER BY ft.created_at DESC, ft.id DESC';

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     user_id: ?int,
         *     model_id: ?int,
         *     type: ?string,
         *     amount_credits: ?int,
         *     screen_name: ?string,
         *     broadcaster_id: ?int,
         *     studio_code: ?string,
         *     location_id: ?int,
         *     ref_fanclub_transact_id: ?int,
         *     created_at: ?string,
         *     post_title: ?string,
         *     file_mime_type: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $file = new FanClubFile(
                mimeType: $row['file_mime_type'],
            );

            $post = new FanClubPost(
                file:  $file,
                title: $row['post_title'],
            );

            $objects[] = new FanClubTransaction(
                id:                   $row['id'],
                userId:               $row['user_id'],
                modelId:              $row['model_id'],
                type:                 $row['type'],
                amountCredits:        $row['amount_credits'],
                screenName:           $row['screen_name'],
                broadcasterId:        $row['broadcaster_id'],
                studioCode:           $row['studio_code'],
                locationId:           $row['location_id'],
                refFanclubTransactId: $row['ref_fanclub_transact_id'],
                createdAt:            $row['created_at'] !== null ? new DateTime($row['created_at']) : null,
                post:                 $post,
            );
        }

        return $objects;
    }
}
