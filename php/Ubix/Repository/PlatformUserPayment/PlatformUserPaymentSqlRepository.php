<?php

declare(strict_types=1);

namespace Ubix\Repository\PlatformUserPayment;

use DateTime;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PlatformUserPaymentOptions;
use Ubix\Model\PlatformUserPayment;
use Ubix\Repository\PlatformUserPayment\PlatformUserPaymentReaderInterface as PlatformUserPaymentReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading platform user payment data
 *
 * @see \Ubix\Tests\Repository\PlatformUserPayment\PlatformUserPaymentSqlRepositoryTest PHPUnit test case
 */
final class PlatformUserPaymentSqlRepository implements PlatformUserPaymentReader
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
    public function getByUserId(int $userId): array
    {
        return $this->query(new PlatformUserPaymentOptions(
            userId: $userId,
        ));
    }

    /**
     * Generate and execute a database query then return its results
     *
     * @param PlatformUserPaymentOptions $options DTO of options to generate the query
     *
     * @return PlatformUserPayment[] An array of matching platform user payment(s)
     */
    private function query(PlatformUserPaymentOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
                    up.id,
                    up.user_id,
                    up.processor_id,
                    up.processor_inv_id,
                    up.payment_id,
                    up.affiliate_payment_id,
                    up.pin,
                    up.bin,
                    up.last_digits,
                    up.card_expire,
                    up.card_expire_last_used,
                    up.payment_type_id,
                    up.external_account_id,
                    up.date_created,
                    up.date_last_purchased,
                    up.status,
                    up.account_type,
                    up.payment_type,
                    up.distribution_group_id,
                    up.card_category,
                    up.type_of_card,
                    up.country_code,
                    up.card_brand,
                    up.issuing_bank
				FROM BILLING.User_Payment up
                WHERE 1 = 1';

        if ($options->userId !== null) {
            $sql .= ' AND up.user_id = :userId';

            $parameters['userId'] = $options->userId;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     user_id: ?int,
         *     processor_id: ?int,
         *     processor_inv_id: ?string,
         *     payment_id: ?string,
         *     affiliate_payment_id: ?string,
         *     pin: ?string,
         *     bin: ?string,
         *     last_digits: ?string,
         *     card_expire: ?string,
         *     card_expire_last_used: ?string,
         *     payment_type_id: ?int,
         *     external_account_id: ?int,
         *     date_created: ?string,
         *     date_last_purchased: ?string,
         *     status: ?int,
         *     account_type: ?string,
         *     payment_type: ?string,
         *     distribution_group_id: ?int,
         *     card_category: ?string,
         *     type_of_card: ?string,
         *     country_code: ?string,
         *     card_brand: ?string,
         *     issuing_bank: ?string,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters, true) as $row) {
            $objects[] = new PlatformUserPayment(
                id:                  $row['id'],
                userId:              $row['user_id'],
                processorId:         $row['processor_id'],
                processorInvId:      $row['processor_inv_id'],
                paymentId:           $row['payment_id'],
                affiliatePaymentId:  $row['affiliate_payment_id'],
                pin:                 $row['pin'],
                bin:                 $row['bin'],
                lastDigits:          $row['last_digits'],
                cardExpire:          $row['card_expire'],
                cardExpireLastUsed:  $row['card_expire_last_used'],
                paymentTypeId:       $row['payment_type_id'],
                externalAccountId:   $row['external_account_id'],
                dateCreated:         $row['date_created'] !== null ? new DateTime($row['date_created']) : null,
                dateLastPurchased:   $row['date_last_purchased'] !== null ? new DateTime($row['date_last_purchased']) : null,
                status:              $row['status'],
                accountType:         $row['account_type'],
                paymentType:         $row['payment_type'],
                distributionGroupId: $row['distribution_group_id'],
                cardCategory:        $row['card_category'],
                typeOfCard:          $row['type_of_card'],
                countryCode:         $row['country_code'],
                cardBrand:           $row['card_brand'],
                issuingBank:         $row['issuing_bank'],
            );
        }

        return $objects;
    }
}
