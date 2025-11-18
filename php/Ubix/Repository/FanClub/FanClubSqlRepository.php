<?php

declare(strict_types=1);

namespace Ubix\Repository\FanClub;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\FanClubOptions;
use Ubix\Model\FanClub;
use Ubix\Model\Performer;
use Ubix\Repository\FanClub\FanClubReaderInterface as FanClubReader;
use Ubix\Repository\FanClub\FanClubWriterInterface as FanClubWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading and writing fan club data
 *
 * @see \Ubix\Tests\Repository\FanClub\FanClubSqlRepositoryTest PHPUnit test case
 */
final class FanClubSqlRepository implements FanClubReader, FanClubWriter
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
    public function save(FanClub $fanClub): void
    {
        //
        //  Save the status
        //
        if ($fanClub->getStatus() !== null) {
            $sql = 'SELECT mfcs.fc_state
					FROM fanclub.model_fc_state mfcs
					WHERE mfcs.model_id = :modelId
					ORDER BY mfcs.changed_at DESC
					LIMIT 1';

            $currentStatus = $this->sqlService->getColumn($sql, [
                'modelId' => $fanClub->getModelId(),
            ]);

            if (empty($currentStatus) || $currentStatus !== $fanClub->getStatus()) { // Only do an INSERT if there is no current status in the database or if the status has changed
                $fanClub->setStatusUpdatedAt(new DateTime());

                $sql = 'INSERT INTO fanclub.model_fc_state SET
						fc_state   = :fcState,
						model_id   = :modelId,
						changed_at = :changedAt';

                $this->sqlService->query($sql, [
                    'changedAt' => $fanClub->getStatusUpdatedAt()?->format(DateTime::ISO8601_EXPANDED),
                    'fcState'   => $fanClub->getMonthlyPrice(),
                    'modelId'   => $fanClub->getModelId(),
                ]);
            }
        }

        //
        //  Save the monthly price
        //
        if ($fanClub->getMonthlyPrice() !== null) {
            $sql = 'SELECT fcmp.price_credits
					FROM fanclub.fanclub_membership_prices fcmp
					WHERE fcmp.model_id = :modelId
					AND fcmp.effective_start <= NOW()
					ORDER BY fcmp.effective_start DESC
					LIMIT 1';

            $currentMonthlyPrice = $this->sqlService->getColumn($sql, [
                'modelId' => $fanClub->getModelId(),
            ]);

            if (empty($currentMonthlyPrice) || (int)$currentMonthlyPrice !== $fanClub->getMonthlyPrice()) { // Only do an INSERT if there is no current price in the database or if the price has changed
                $fanClub->setMonthlyPriceUpdatedAt(new DateTime());

                $sql = 'INSERT INTO fanclub.fanclub_membership_prices SET
						price_credits   = :priceCredits,
						model_id        = :modelId,
						effective_start = :effectiveStart,
						created_at      = :createdAt';

                $this->sqlService->query($sql, [
                    'createdAt'      => $fanClub->getMonthlyPriceUpdatedAt()?->format(DateTime::ISO8601_EXPANDED),
                    'effectiveStart' => $fanClub->getMonthlyPriceUpdatedAt()?->format(DateTime::ISO8601_EXPANDED),
                    'modelId'        => $fanClub->getModelId(),
                    'priceCredits'   => $fanClub->getMonthlyPrice(),
                ]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $modelId): array
    {
        return $this->query(new FanClubOptions(
            modelId: $modelId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param FanClubOptions $options DTO of options to generate the query
     *
     * @return FanClub[] An array of fan club objects
     */
    private function query(FanClubOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					m.id AS model_id,
					m.id AS performer_id, /* This is not a bug, we want the m.model_id value in both model_id and performer_id for the time being */
					m.name AS performer_name,
					REPLACE(m.name, "_", "-") AS performer_slug,
					m.username AS performer_username,
					m.password AS performer_password,
					m.enc_password AS performer_enc_password,
					m.salt AS performer_salt,
					m.image AS performer_image,
					m.studio AS performer_studio_code,
					m.harvest_code AS performer_harvest_code,
					m.firstname AS performer_firstname,
					m.middlename AS performer_middlename,
					m.lastname AS performer_lastname,
					s.broadcaster_id AS performer_broadcaster_id,
					mfcs.fc_state AS status,
					mfcs.changed_at AS status_updated_at,
					CAST(COALESCE((
						SELECT _fcmp.price_credits
						FROM fanclub.fanclub_membership_prices _fcmp
						WHERE _fcmp.model_id = m.id
						AND _fcmp.effective_start <= NOW()
						ORDER BY _fcmp.effective_start DESC
						LIMIT 1
					), ' . (int)FanClub::MONTHLY_PRICE_DEFAULT . ') AS UNSIGNED) AS monthly_price,
					(
						SELECT _fcmp.created_at
						FROM fanclub.fanclub_membership_prices _fcmp
						WHERE _fcmp.model_id = m.id
						AND _fcmp.effective_start <= NOW()
						ORDER BY _fcmp.effective_start DESC
						LIMIT 1
					) AS monthly_price_updated_at
				FROM ntl_db.models m
				INNER JOIN ntl_db.studios s ON s.studio = m.studio
				LEFT OUTER JOIN fanclub.model_fc_state mfcs ON mfcs.model_id = m.id
				WHERE 1 = 1';

        if ($options->modelId !== null) {
            $sql .= ' AND m.id = :modelId';

            $parameters['modelId'] = $options->modelId;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     model_id: ?int,
         *     status: ?string,
         *     status_updated_at: ?string,
         *     monthly_price: ?int,
         *     monthly_price_updated_at: ?string,
         *     performer_id: ?int,
         *     performer_name: ?string,
         *     performer_slug: ?string,
         *     performer_username: ?string,
         *     performer_password: ?string,
         *     performer_enc_password: ?string,
         *     performer_salt: ?string,
         *     performer_image: ?string,
         *     performer_studio_code: ?string,
         *     performer_harvest_code: ?int,
         *     performer_firstname: ?string,
         *     performer_middlename: ?string,
         *     performer_lastname: ?string,
         *     performer_broadcaster_id: ?int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $performer = new Performer(
                id:            $row['performer_id'],
                name:          $row['performer_name'],
                slug:          $row['performer_slug'],
                username:      $row['performer_username'],
                password:      $row['performer_password'],
                encPassword:   $row['performer_enc_password'],
                salt:          $row['performer_salt'],
                image:         $row['performer_image'],
                studioCode:    $row['performer_studio_code'],
                harvestCode:   $row['performer_harvest_code'],
                firstname:     $row['performer_firstname'],
                middlename:    $row['performer_middlename'],
                lastname:      $row['performer_lastname'],
                broadcasterId: $row['performer_broadcaster_id'],
            );

            $objects[] = new FanClub(
                modelId:               $row['model_id'],
                performer:             $performer,
                status:                $row['status'],
                statusUpdatedAt:       $row['status_updated_at'] !== null ? new DateTime($row['status_updated_at']) : null,
                monthlyPrice:          $row['monthly_price'],
                monthlyPriceUpdatedAt: $row['monthly_price_updated_at'] !== null ? new DateTime($row['monthly_price_updated_at']) : null,
            );
        }

        return $objects;
    }
}
