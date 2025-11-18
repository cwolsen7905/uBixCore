<?php

declare(strict_types=1);

namespace Ubix\Repository\ScreenName;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\ScreenNameOptions;
use Ubix\Model\ScreenName;
use Ubix\Repository\ScreenName\ScreenNameReaderInterface as ScreenNameReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading screen name data
 *
 * @see \Ubix\Tests\Repository\ScreenName\ScreenNameSqlRepositoryTest PHPUnit test case
 */
final class ScreenNameSqlRepository implements ScreenNameReader
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
    public function getByOptiusersId(int $optiusersId): array
    {
        return $this->query(new ScreenNameOptions(
            status:      [ScreenName::STATUS_ACTIVE, ScreenName::STATUS_INACTIVE],
            optiusersId: $optiusersId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param ScreenNameOptions $options DTO of options to generate the query
     *
     * @return ScreenName[] Array of screen name object(s)
     */
    private function query(ScreenNameOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					sn.id,
					sn.screen_name,
					sn.screen_name_lower,
					sn.password,
					sn.optiusers_id,
					sn.reconciled,
					sn.chat_default,
					sn.set_rewards_status,
					sn.set_rewards_level,
					sn.show_performers_rewards,
					sn.show_customers_rewards,
					sn.timestamp,
					sn.anonymous_status,
					sn.status,
					sn.dm_online
				FROM ntl_db.screen_names sn
				WHERE 1 = 1';

        if ($options->status !== null) {
            $sql .= ' AND sn.status IN (';

            $statuses = is_array($options->status) ? $options->status : [$options->status]; // Make the status an array if it isn't already
            foreach ($statuses as $i => $status) {
                if ($i > 0) {
                    $sql .= ',';
                }

                $sql .= (string)$status;
            }

            $sql .= ')';
        }

        if ($options->optiusersId !== null) {
            $sql .= ' AND sn.optiusers_id = :optiusersId';

            $parameters[':optiusersId'] = $options->optiusersId;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     screen_name: ?string,
         *     screen_name_lower: ?string,
         *     password: ?string,
         *     optiusers_id: ?int,
         *     reconciled: ?string,
         *     chat_default: ?string,
         *     set_rewards_status: ?int,
         *     set_rewards_level: ?int,
         *     show_performers_rewards: ?string,
         *     show_customers_rewards: ?string,
         *     timestamp: ?string,
         *     anonymous_status: ?int,
         *     status: ?int,
         *     dm_online: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new ScreenName(
                id:                    $row['id'],
                screenName:            $row['screen_name'],
                screenNameLower:       $row['screen_name_lower'],
                password:              $row['password'],
                optiusersId:           $row['optiusers_id'],
                reconciled:            $row['reconciled'],
                chatDefault:           $row['chat_default'],
                setRewardsStatus:      $row['set_rewards_status'],
                setRewardsLevel:       $row['set_rewards_level'],
                showPerformersRewards: $row['show_performers_rewards'],
                showCustomersRewards:  $row['show_customers_rewards'],
                timestamp:             $row['timestamp'] !== null ? new DateTime($row['timestamp']) : null,
                anonymousStatus:       $row['anonymous_status'],
                status:                $row['status'],
                dmOnline:              $row['dm_online'],
            );
        }

        return $objects;
    }
}
