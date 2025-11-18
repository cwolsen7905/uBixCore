<?php

declare(strict_types=1);

namespace Ubix\Repository\Deal;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\DealOptions;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\BrokerId;
use Ubix\DataType\Int\DealId;
use Ubix\DataType\Int\UnsignedSmallInt;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateSiteType;
use Ubix\Enum\ServiceType;
use Ubix\Enum\Status;
use Ubix\Enum\YesNo;
use Ubix\Model\Deal;
use Ubix\Repository\Deal\DealReaderInterface as DealReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class DealSqlRepository
 *
 * Implements methods to read deal-related data from the database.
 *
 * @see \Ubix\Tests\Repository\Deal\DealSqlRepositoryTest PHPUnit test case
 */
final class DealSqlRepository implements DealReader
{
    /**
     * DealSqlRepository constructor.
     *
     * @param Logger     $logger     The logger interface
     * @param SqlService $sqlService The SQL service for database interactions
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getDealsByAffiliateId(AffiliateId $affiliateId): array
    {
        return $this->query(new DealOptions(
            affiliateId: $affiliateId->value,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDealMpCode(MpCode $mpCode): array
    {
        return $this->query(new DealOptions(
            mpCode: $mpCode->value,
        ));
    }

     /**
      * Generate and execute a database query then return its results
      *
      * @param DealOptions $options DTO of options to generate the query
      *
      * @return Deal[] An array of objects
      */
    private function query(DealOptions $options): array
    {
        //
        //  Get the SQL query
        //
        $sqlQuery = $this->getSqlQuery($options);

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         * id: int,
         * name: string,
         * description: string,
         * contact_info: string,
         * campaign_type: string,
         * site_type: string,
         * service: string,
         * affiliate_id: int,
         * broker_id: int,
         * default_ad_type: string,
         * current_dealmaker_id: int,
         * import_costs: string,
         * status: string,
         * channel_type: string
         * } $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters, true) as $row) {
            $objects[] = new Deal(
                id:                 new DealId((int)$row['id']),
                name:               new Varchar($row['name']),
                description:        new Text($row['description']),
                contactInfo:        new Text($row['contact_info']),
                campaignType:       new Varchar($row['campaign_type']),
                siteType:           AffiliateSiteType::from($row['site_type']),
                service:            ServiceType::from($row['service']),
                affiliateId:        new AffiliateId((int)$row['affiliate_id']),
                brokerId:           new BrokerId((int)$row['broker_id']),
                defaultAdType:      new Varchar($row['default_ad_type']),
                currentDealmakerId: new UnsignedSmallInt($row['current_dealmaker_id']),
                importCosts:        YesNo::from($row['import_costs']),
                status:             Status::from($row['status']),
                channelType:        isset($row['channel_type']) ? new Varchar($row['channel_type']) : null,
            );
        }

        return $objects;
    }

     /**
      * Get a SQL query DTO ready to be executed
      *
      * @param DealOptions $options DTO of options to generate the query
      *
      * @return SqlQuery A SQL query DTO
      */
    private function getSqlQuery(DealOptions $options): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        if ($options->mpCode !== null) {
            // Check DB
            $sql = 'SELECT d.* FROM VSCASH.Deal_MP_Codes dmc JOIN VSCASH.Deals d ON dmc.deal_id = d.id
                        WHERE dmc.mp_code = :mp_code';

            $parameters = ['mp_code' => $options->mpCode];
        } else {
            //
            //  Generate our SQL query based on the $options parameter
            //
            $sql = 'SELECT * FROM VSCASH.Deals 
                        WHERE channel_type like "MB_%"';

            if ($options->affiliateId !== null) {
                $sql .= ' AND affiliate_id = :affiliateId';

                $parameters['affiliateId'] = $options->affiliateId;
            }
        }

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Return a DTO
        //
        return new SqlQuery(
            sql:        $sql,
            parameters: $parameters,
        );
    }
}
