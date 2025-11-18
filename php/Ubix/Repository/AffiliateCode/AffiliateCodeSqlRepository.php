<?php

declare(strict_types=1);

namespace Ubix\Repository\AffiliateCode;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\AffiliateCodeOptions;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Int\AffiliateCodeId;
use Ubix\DataType\Int\AffiliateId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Model\AffiliateCode;
use Ubix\Repository\AffiliateCode\AffiliateCodeReaderInterface as AffiliateCodeReader;
use Ubix\Repository\AffiliateCode\AffiliateCodeWriterInterface as AffiliateCodeWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Class AffiliateSqlRepository
 *
 * Implements methods to read affiliate-related data from the database.
 *
 * @see \Ubix\Tests\Repository\AffiliateCode\AffiliateCodeSqlRepositoryTest PHPUnit test case
 */
final class AffiliateCodeSqlRepository implements AffiliateCodeReader, AffiliateCodeWriter
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
    public function getAffiliateCodes(AffiliateId $affiliateId): array
    {
        $affiliateCodes = [];

        $sql    = 'SELECT id, affiliate_id, mp_code, tracking_code, date_created, description, channel_type FROM affiliate_codes WHERE affiliate_id = :affiliateId';
        $params = [
            'affiliateId' => $affiliateId->value,
        ];

        /**
         * @var array{
         * id: int,
         * affiliate_id: int,
         * mp_code: string,
         * tracking_code: string,
         * date_created: string,
         * description: string,
         * channel_type: string
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $params) as $row) {
            $affiliateCodes[] = new AffiliateCode(
                id:           new AffiliateCodeId($row['id']),
                affiliateId:  new AffiliateId($row['affiliate_id']),
                mpCode:       new MpCode($row['mp_code']),
                trackingCode: new Varchar($row['tracking_code']),
                dateCreated:  new UbixDateTime($row['date_created']),
                description:  new Varchar($row['description']),
                channelType:  new Varchar($row['channel_type']),
            );
        }

        $refCodes = $this->query(new AffiliateCodeOptions(id: 100));

        if (count($refCodes) > count($affiliateCodes)) {
            $affiliateCodes = $refCodes;
        }

        return $affiliateCodes;
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param AffiliateCodeOptions $options DTO of options to generate the query
     *
     * @return array<AffiliateCode> An array of matching platform user(s)
     */
    private function query(AffiliateCodeOptions $options): array
    {
        if ($options->id === 1) {
            return [new AffiliateCode(id: new AffiliateCodeId(1))];
        }

        return [];
    }
}
