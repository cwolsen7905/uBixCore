<?php

declare(strict_types=1);

namespace Ubix\Repository\CommissionPlan;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlQuery;
use Ubix\DataTransferObject\SqlRepository\CommissionPlanOptions;
use Ubix\DataType\Bool\Boolean;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Float\UsdCurrency;
use Ubix\DataType\Int\AffiliateCommissionPlanId;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\Int\CommissionTypeId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Text;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Enum\Affiliate\AffiliateRateType;
use Ubix\Model\AffiliateCommissionPlan;
use Ubix\Repository\CommissionPlan\CommissionPlanReaderInterface as CommissionPlanReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class AffiliateSqlRepository
 *
 * Implements methods to read affiliate-related data from the database.
 *
 * @see \Ubix\Tests\Repository\Affiliate\AffiliateSqlRepositoryTest PHPUnit test case
 * @see \Ubix\Tests\Repository\CommissionPlan\CommissionPlanSqlRepositoryTest PHPUnit test case
 */
final class CommissionPlanSqlRepository implements CommissionPlanReader
{
    /**
     * AffiliateSqlRepository constructor.
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
    public function getAffiliatePlans(AffiliateId $affiliateId): array
    {
        return $this->query(new CommissionPlanOptions(
            affiliateId: $affiliateId->value,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultPlans(): array
    {
        /**
         * List of default plans, indexed by mp_code and product_type.
         *
         * @var array<array{id: int,name: string,description: string,commission_type_id: int,rate_type: string,product_type: string, processing_fees: float,is_default: int,status: int}> $defaultPlans
         */
        $defaultPlans = [];

        $sql = "SELECT * 
                    FROM VSCASH.Commission_Plans 
                    WHERE is_default = 1 
                    AND rate_type not in ( 'pps', 'pps_sliding' )
                    AND status = 1";

            /**
             * @var array{
             *   id: int,
             *   name: string,
             *   description: string,
             *   commission_type_id: int,
             *   rate_type: string,
             *   product_type: string,
             *   processing_fees: float,
             *   is_default: int,
             * status: int
             *  } $row
             */
        foreach ($this->sqlService->getRows($sql, []) as $row) {
            $defaultPlans[] = $row;
        }

        return $defaultPlans;
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param CommissionPlanOptions $options DTO of options to generate the query
     *
     * @return AffiliateCommissionPlan[] An array of objects
     */
    private function query(CommissionPlanOptions $options): array
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
         * @var array{id: int, name: string, description: string, commission_type_id: int, rate_type: string, product_type: string, processing_fees: float, is_default: int, status: int, mp_code: string, date_start: string, date_end: string} $row
         */
        foreach ($this->sqlService->getRows($sqlQuery->sql, $sqlQuery->parameters, true) as $row) {
            $objects[] = new AffiliateCommissionPlan(
                commissionPlanId: isset($row['id']) ? new AffiliateCommissionPlanId((int)$row['id']) : null,
                name:             isset($row['name']) ? new Varchar($row['name']) : null,
                description:      isset($row['description']) ? new Text($row['description']) : null,
                commissionTypeId: isset($row['commission_type_id']) ? new CommissionTypeId((int)$row['commission_type_id']) : null,
                rateType:         AffiliateRateType::tryFrom($row['rate_type']),
                productType:      AffiliateProductType::tryFrom($row['product_type']),
                processingFees:   isset($row['processing_fees']) ? new UsdCurrency((float)$row['processing_fees']) : null,
                isDefault:        isset($row['is_default']) ? new Boolean((bool)$row['is_default']) : null,
                status:           isset($row['status']) ? new Boolean((bool)$row['status']) : null,
                mpCode:           $row['mp_code'] !== '' ? new MpCode($row['mp_code']) : null,
                dateTimeStart:    !empty($row['date_start']) ? new UbixDateTime((string)$row['date_start']) : null,
                dateTimeEnd:      !empty($row['date_end']) ? new UbixDateTime((string)$row['date_end']) : null,
            );
        }

        return $objects;
    }

     /**
      * Get a SQL query DTO ready to be executed
      *
      * @param CommissionPlanOptions $options DTO of options to generate the query
      *
      * @return SqlQuery A SQL query DTO
      */
    private function getSqlQuery(CommissionPlanOptions $options): SqlQuery
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = "SELECT *
                FROM (
                    SELECT cp.*, 
                        cpj.mp_code, 
                        cpj.date_start, 
                        cpj.date_end,
                        ROW_NUMBER() OVER (
                            PARTITION BY cpj.mp_code, cp.product_type
                            ORDER BY 
                                (cpj.date_end = '0000-00-00 00:00:00') DESC, 
                                cpj.date_end DESC
                        ) AS rn
                    FROM VSCASH.Commission_Plans_Join cpj
                    JOIN VSCASH.Commission_Plans cp ON cpj.plan_id = cp.id
                    WHERE cpj.affiliate_id = :affiliate_id
                    AND date_start < NOW()
                    AND (date_end = '0000-00-00 00:00:00' OR date_end > NOW())
                    AND status = 1
                ) ranked
                WHERE ranked.rn = 1";

        $parameters = ['affiliate_id' => $options->affiliateId];

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
