<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClubMembership;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\FanClubMembershipOptions;
use Ubix\Model\FanClubMembership;
use Ubix\Model\FanClubTransaction;
use Ubix\Model\Performer;
use Ubix\Repository\FanClubMembership\FanClubMembershipReaderInterface as FanClubMembershipReader;
use Ubix\Repository\FanClubMembership\FanClubMembershipWriterInterface as FanClubMembershipWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing fan club membership data
 *
 * @see \Ubix\Tests\Repository\FanClubMembership\FanClubMembershipSqlRepositoryTest PHPUnit test case
 */
final class FanClubMembershipSqlRepository implements FanClubMembershipReader, FanClubMembershipWriter
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
    public function save(FanClubMembership $membership): void
    {
        if ($membership->getId() !== null && $membership->getId() > 0) { // If there is a valid ID then update the database
            //
            //  UPDATE the database
            //
            $sql = 'UPDATE fanclub.fanclub_memberships SET
					user_id                 = :userId,
					model_id                = :modelId,
					membership_price_id     = :membershipPriceId,
					start_date              = :startDate,
					end_date                = :endDate,
					status                  = :status,
					membership_type         = :membershipType,
					screen_name             = :screenName,
					transaction_id          = :transactionId,
					ref_fanclub_transact_id = :refFanclubTransactId,
					created_at              = :createdAt
                    WHERE id = :id';

            $this->sqlService->query($sql, [
                'createdAt'            => $membership->getCreatedAt()?->format(DateTime::ISO8601_EXPANDED),
                'endDate'              => $membership->getEndDate()?->format(DateTime::ISO8601_EXPANDED),
                'id'                   => $membership->getId(),
                'membershipPriceId'    => $membership->getMembershipPriceId(),
                'membershipType'       => $membership->getMembershipType(),
                'modelId'              => $membership->getModelId(),
                'refFanclubTransactId' => $membership->getRefFanclubTransactId(),
                'screenName'           => $membership->getScreenName(),
                'startDate'            => $membership->getStartDate()?->format(DateTime::ISO8601_EXPANDED),
                'status'               => $membership->getStatus(),
                'transactionId'        => $membership->getTransactionId(),
                'userId'               => $membership->getUserId(),
            ]);
        } else { // There is no valid ID so insert into the database
            //
            //  INSERT into the database
            //
            $sql = 'INSERT INTO fanclub.fanclub_memberships SET
					user_id                 = :userId,
					model_id                = :modelId,
					membership_price_id     = :membershipPriceId,
					start_date              = :startDate,
					end_date                = :endDate,
					status                  = :status,
					membership_type         = :membershipType,
					screen_name             = :screenName,
					transaction_id          = :transactionId,
					ref_fanclub_transact_id = :refFanclubTransactId,
					created_at              = :createdAt';

            $this->sqlService->query($sql, [
                'createdAt'            => $membership->getCreatedAt()?->format(DateTime::ISO8601_EXPANDED),
                'endDate'              => $membership->getEndDate()?->format(DateTime::ISO8601_EXPANDED),
                'membershipPriceId'    => $membership->getMembershipPriceId(),
                'membershipType'       => $membership->getMembershipType(),
                'modelId'              => $membership->getModelId(),
                'refFanclubTransactId' => $membership->getRefFanclubTransactId(),
                'screenName'           => $membership->getScreenName(),
                'startDate'            => $membership->getStartDate()?->format(DateTime::ISO8601_EXPANDED),
                'status'               => $membership->getStatus(),
                'transactionId'        => $membership->getTransactionId(),
                'userId'               => $membership->getUserId(),
            ]);

            //
            //  Update the object
            //
            $membership->setId((int)$this->sqlService->lastInsertId());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): array
    {
        return $this->query(new FanClubMembershipOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByUserId(int $userId): array
    {
        return $this->query(new FanClubMembershipOptions(
            endDateInFuture: true,
            status:          ['active', 'canceled'],
            userId:          $userId,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByModelId(int $performerId): array
    {
        return $this->query(new FanClubMembershipOptions(
            endDateInFuture: true,
            modelId:         $performerId,
            status:          ['active', 'canceled'],
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveByUserAndPerformerIds(int $userId, int $performerId): array
    {
        return $this->query(new FanClubMembershipOptions(
            endDateInFuture: true,
            modelId:         $performerId,
            status:          ['active'],
            userId:          $userId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param FanClubMembershipOptions $options DTO of options to generate the query
     *
     * @return FanClubMembership[] Array of fan club membership object(s)
     */
    private function query(FanClubMembershipOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					fm.id,
					fm.user_id,
					fm.model_id,
					fm.model_id AS performer_id, /* This is not a bug, we want the fm.model_id value in both model_id and performer_id for the time being */
					m.name AS performer_name,
					REPLACE(m.name, "_", "-") AS performer_slug,
					m.image AS performer_image,
					m.studio AS performer_studio_code,
					m.harvest_code AS performer_harvest_code,
					fm.membership_price_id,
					fm.start_date,
					fm.end_date,
					fm.status,
					fm.membership_type,
					fm.screen_name,
					fm.transaction_id,
					ft.type AS transaction_type,
					ft.amount_credits AS transaction_amount_credits,
					ft.screen_name AS transaction_screen_name,
					ft.broadcaster_id AS transaction_broadcaster_id,
					ft.studio_code AS transaction_studio_code,
					ft.location_id AS transaction_location_id,
					ft.ref_fanclub_transact_id AS transaction_ref_fanclub_transact_id,
					ft.created_at AS transaction_created_at,
					ft.user_id AS transaction_user_id,
					ft.model_id AS transaction_model_id,
					fm.ref_fanclub_transact_id,
					fm.created_at
				FROM fanclub.fanclub_memberships fm
				INNER JOIN ntl_db.models m ON m.id = fm.model_id
				LEFT OUTER JOIN fanclub.fanclub_transactions ft ON ft.id = fm.transaction_id
				WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND fm.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->modelId !== null) {
            $sql .= ' AND fm.model_id = :modelId';

            $parameters['modelId'] = $options->modelId;
        }

        if ($options->userId !== null) {
            $sql .= ' AND fm.user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        if ($options->status !== null) {
            $sql .= ' AND fm.status IN (';

            $statuses = is_array($options->status) ? $options->status : [$options->status]; // Make the status an array if it isn't already
            foreach ($statuses as $i => $status) {
                if ($i > 0) {
                    $sql .= ',';
                }

                $sql .= '"' . $status . '"';
            }

            $sql .= ')';
        }

        if ($options->endDateInFuture === true) {
            $sql .= ' AND fm.end_date > NOW()';
        }

        $sql .= ' ORDER BY fm.id DESC';

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
         *     user_id: ?int,
         *     model_id: ?int,
         *     membership_price_id: ?int,
         *     start_date: ?string,
         *     end_date: ?string,
         *     status: ?string,
         *     membership_type: ?string,
         *     screen_name: ?string,
         *     transaction_id: ?int,
         *     ref_fanclub_transact_id: ?int,
         *     created_at: ?string,
         *     performer_harvest_code: ?int,
         *     performer_id: ?int,
         *     performer_image: ?string,
         *     performer_name: ?string,
         *     performer_slug: ?string,
         *     performer_studio_code: ?string,
         *     transaction_amount_credits: ?int,
         *     transaction_broadcaster_id: ?int,
         *     transaction_created_at: ?string,
         *     transaction_id: ?int,
         *     transaction_location_id: ?int,
         *     transaction_ref_fanclub_transact_id: ?int,
         *     transaction_screen_name: ?string,
         *     transaction_studio_code: ?string,
         *     transaction_type: ?string,
         *     transaction_user_id: ?int,
         *     transaction_model_id: ?int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $performer = new Performer(
                harvestCode: $row['performer_harvest_code'],
                id:          $row['performer_id'],
                image:       $row['performer_image'],
                name:        $row['performer_name'],
                slug:        $row['performer_slug'],
                studioCode:  $row['performer_studio_code'],
            );

            $transaction = new FanClubTransaction(
                amountCredits:        $row['transaction_amount_credits'],
                broadcasterId:        $row['transaction_broadcaster_id'],
                createdAt:            $row['transaction_created_at'] !== null ? new DateTime($row['transaction_created_at']) : null,
                id:                   $row['transaction_id'],
                locationId:           $row['transaction_location_id'],
                refFanclubTransactId: $row['transaction_ref_fanclub_transact_id'],
                screenName:           $row['transaction_screen_name'],
                studioCode:           $row['transaction_studio_code'],
                type:                 $row['transaction_type'],
                userId:               $row['transaction_user_id'],
                modelId:              $row['transaction_model_id'],
            );

            $objects[] = new FanClubMembership(
                id:                   $row['id'],
                userId:               $row['user_id'],
                modelId:              $row['model_id'],
                membershipPriceId:    $row['membership_price_id'],
                startDate:            $row['start_date'] !== null ? new DateTime($row['start_date']) : null,
                endDate:              $row['end_date'] !== null ? new DateTime($row['end_date']) : null,
                status:               $row['status'],
                membershipType:       $row['membership_type'],
                screenName:           $row['screen_name'],
                transactionId:        $row['transaction_id'],
                refFanclubTransactId: $row['ref_fanclub_transact_id'],
                createdAt:            $row['created_at'] !== null ? new DateTime($row['created_at']) : null,
                performer:            $performer,
                transaction:          $transaction,
            );
        }

        return $objects;
    }
}
