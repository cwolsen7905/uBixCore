<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Attribution;
use Ubix\DataTransferObject\EarliestAccount;
use Ubix\DataType\DateTime\UbixDateTime;
use Ubix\DataType\Float\UsdCurrency;
use Ubix\DataType\Int\PlatformUserId;
use Ubix\DataType\String\MpCode;
use Ubix\DataType\String\Varchar;
use Ubix\Enum\Affiliate\AffiliateProductType;
use Ubix\Enum\Exception\CustomerFacingExceptionCode;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\AttributionLog;
use Ubix\Model\PlatformUser;
use Ubix\Model\Transaction;
use Ubix\Repository\Affiliate\AffiliateReaderInterface as AffiliateReader;
use Ubix\Repository\Affiliate\AffiliateWriterInterface as AffiliateWriter;
use Ubix\Repository\AttributionLog\AttributionLogWriterInterface as AttributionLogWriter;
use Ubix\Repository\BillingTransaction\BillingTransactionReaderInterface as BillingTransactionReader;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;

/**
 * Service for attributing the user
 *
 * @see \Ubix\Tests\Service\AttributionServiceTest PHPUnit test case
 */
final class AttributionService
{
    /**
     * Constructor
     *
     * @param Logger                   $logger               The logger service
     * @param AffiliateReader          $affiliateReader      The reader service for attribution
     * @param AffiliateWriter          $affiliateWriter      The writer service for attribution
     * @param PlatformUserService      $platformUserService  The Platform User Service
     * @param BillingTransactionReader $transactionReader    The Transaction Reader
     * @param JsonService              $jsonService          The JSON service
     * @param AffiliateService         $affiliateService     The Affiliate Service
     * @param AttributionLogWriter     $attributionLogWriter The Attribution Log Writer
     * @param PlatformUserReader       $platformUserReader   The Platform User Reader
     */
    public function __construct(
        private Logger $logger,
        private AffiliateReader $affiliateReader,
        private AffiliateWriter $affiliateWriter,
        private PlatformUserService $platformUserService,
        private BillingTransactionReader $transactionReader,
        private JsonService $jsonService,
        private AffiliateService $affiliateService,
        private AttributionLogWriter $attributionLogWriter,
        private PlatformUserReader $platformUserReader,
    ) {
    }

    /**
     * This method does the first money attribution if a user is eligible
     *
     * @param PlatformUserId $userId          The user ID for the request
     * @param UsdCurrency    $amount          The amount of the transaction causing the FM
     * @param MpCode         $envMpCode       The environments MP code at time of the FM
     * @param ?UbixDateTime   $transactionDate The time the transaction happened can be NULL
     *
     * @return Attribution The response object containing message, mp_code and status code
     */
    public function firstMoney(PlatformUserId $userId, UsdCurrency $amount, MpCode $envMpCode, ?UbixDateTime $transactionDate): Attribution
    {
        $amount                = $amount->value;
        $stringTransactionDate = $transactionDate !== null ? $transactionDate->value->format('Y-m-d H:i:s') : (new DateTimeImmutable())->format('Y-m-d H:i:s');
        assert(is_string($stringTransactionDate));

        try {
            $platformUser = $this->platformUserService->getById($userId->value);
        } catch (Exception $e) {
            return new Attribution(message: 'Invalid User ID', code: CustomerFacingExceptionCode::ATTRIBUTION_FAILED_TEMPORARY_CODE->value);
        }

        try {
            $transaction = $this->getMostRecentTransactionByUserIdBeforeCurrentTransactionDate($userId, new UbixDateTime($stringTransactionDate));
        } catch (Exception $e) {
            $transaction = null;
        }

        $transactionDateEligible = $this->isTransactionDateEligible($transaction, new UbixDateTime($stringTransactionDate));

        if ($amount <= 0 || (!$transactionDateEligible && !in_array($platformUser->getMpCode(), ['xxxx', 'yyyy', 'zzzz'], true))) {
            assert($platformUser->getMpCode() !== null);
            if ($transaction !== null) {
                $retString = 'Not Eligible For Attribution - User has a transaction within 6 months. Incoming Transaction Date: ' . $stringTransactionDate . ' Transaction Date: ' . ($transaction->getDateTime() !== null ? $transaction->getDateTime()->value->format('Y-m-d H:i:s') : 'Invalid') . ', Transaction Amount: ' . ($transaction->getAmount() !== null ? $transaction->getAmount()->value : 'NaN');
            } else {
                $retString = 'Not Eligible For Attribution - Amount was 0.';
            }

            return new Attribution(message: $retString, code: CustomerFacingExceptionCode::ATTRIBUTION_FAILED_TEMPORARY_CODE->value, mpCode: new MpCode($platformUser->getMpCode()));
        }

        //  Incoming MP_CODE
        $incomingMpCode = $envMpCode;

        //
        //  Checks To See If The Account Is Within 7 Day Protection
        //  Check reg_view created_date If Yes Then Use The Optiusers mp_code
        //  if its after 7 days then Find Related Account If None we attribute to incoming mp_code
        //  otherwise attribute them to the related account
        //
        $isWeekProtected = $platformUser->isWeekProtected($stringTransactionDate);


        try {
            $earlierAccount = $this->getEarliestAccountDetails($platformUser);
        } catch (Exception $e) {
            $this->logger->debug('No Earlier Account Found: ' . $e->getMessage());
            $earlierAccount = new EarliestAccount(user: null, reason: null);
        }

        if ($earlierAccount->user !== null) {
            try {
                assert($earlierAccount->user->getMpCode() !== null);
                $retMpCode = $this->attributeUser($platformUser, new MpCode($earlierAccount->user->getMpCode()), new Varchar('Earlier Account Match: ' . $this->jsonService->encode(['user_id' => $earlierAccount->user->getUserId(), 'mp_code' => $earlierAccount->user->getMpCode(), 'reason' => $earlierAccount->reason])));
            } catch (Exception $e) {
                assert($platformUser->getMpCode() !== null);
                return new Attribution(message: $e->getMessage(), code: CustomerFacingExceptionCode::ATTRIBUTION_FAILED_TEMPORARY_CODE->value, mpCode: new MpCode($platformUser->getMpCode()));
            }

            return $retMpCode->value !== $earlierAccount->user->getMpCode() ? new Attribution(message: 'Earlier Account ' . $earlierAccount->user->getUserId() . ' Found Attributing To ' . $earlierAccount->user->getMpCode() . ' But Overloaded To ' . $retMpCode->value, code: 200, mpCode: $retMpCode) : new Attribution(message: 'Earlier Account ' . $earlierAccount->user->getUserId() . ' Found Attributing To ' . $earlierAccount->user->getMpCode(), code: 200, mpCode: new MpCode($earlierAccount->user->getMpCode()));
        } else {
            if ($isWeekProtected) {
                try {
                    $regMpCode = $this->getUserRegistrationMpCode($userId);
                } catch (Exception $e) {
                    return new Attribution(message: 'User Has No Registration View Record', code: CustomerFacingExceptionCode::ATTRIBUTION_FAILED_TEMPORARY_CODE->value, mpCode: ($platformUser->getMpCode() !== null ? new MpCode($platformUser->getMpCode()) : null));
                }

                try {
                    $retMpCode = $this->attributeUser($platformUser, $regMpCode, new Varchar('User Is Week Protected'));
                } catch (Exception $e) {
                    assert($platformUser->getMpCode() !== null);
                    return new Attribution(message: $e->getMessage(), code: CustomerFacingExceptionCode::ATTRIBUTION_FAILED_TEMPORARY_CODE->value, mpCode: new MpCode($platformUser->getMpCode()));
                }

                return $retMpCode->value !== $regMpCode->value ? new Attribution(message: 'User Is Week Protected Using Reg mp_code ' . $regMpCode->value . ' But Overloaded To ' . $retMpCode->value, code: 200, mpCode: $retMpCode) : new Attribution(message: 'User Is Week Protected Using Reg mp_code ' . $regMpCode->value, code: 200, mpCode: $regMpCode);
            } else {
                //  No Earlier Account Found, Attribute To New $incomingMpCode
                try {
                    $retMpCode = $this->attributeUser($platformUser, $incomingMpCode, new Varchar('No Earlier Account'));
                } catch (Exception $e) {
                    assert($platformUser->getMpCode() !== null);
                    return new Attribution(message: $e->getMessage(), code: CustomerFacingExceptionCode::ATTRIBUTION_FAILED_TEMPORARY_CODE->value, mpCode: new MpCode($platformUser->getMpCode()));
                }

                return $retMpCode->value !== $incomingMpCode->value ? new Attribution(message: 'No Earlier Account Found Attributing To Incoming mp_code ' . $incomingMpCode->value . ' But Overloaded To ' . $retMpCode->value, code: 200, mpCode: $retMpCode) : new Attribution(message: 'No Earlier Account Attributing To Incoming mp_code ' . $incomingMpCode->value, code: 200, mpCode: $incomingMpCode);
            }
        }
    }

    /**
     * Get Earliest Account Details
     *
     * @param PlatformUser $user The Users Data
     *
     * @return EarliestAccount The earliest account found and reason for selection
     *
     * @throws Exception If no earlier account is found
     */
    public function getEarliestAccountDetails(PlatformUser $user): EarliestAccount
    {
        try {
            $earliest = $this->platformUserReader->getEarliestAccountByEmail($user);
            return new EarliestAccount(user: $earliest, reason: 'attribute_api_email');
        } catch (Exception $e) {
            $this->logger->debug('No Earliest Account Found By Email: ' . $e->getMessage());
        }

        try {
            $earliest = $this->platformUserReader->getEarliestAccountByFingerprint($user);
            return new EarliestAccount(user: $earliest, reason: 'attribute_api_fingerprint');
        } catch (Exception $e) {
            $this->logger->debug('No Earliest Account Found By Fingerprint: ' . $e->getMessage());
        }

        try {
            $earliest = $this->platformUserReader->getEarliestAccountByIovation($user);
            return new EarliestAccount(user: $earliest, reason: 'attribute_api_iovation');
        } catch (Exception $e) {
            $this->logger->debug('No Earliest Account Found By Iovation: ' . $e->getMessage());
        }

        try {
            $earliest = $this->platformUserReader->getEarliestAccountByDetails($user);
            return new EarliestAccount(user: $earliest, reason: 'attribute_api_details');
        } catch (Exception $e) {
            $this->logger->debug('No Earliest Account Found By Details: ' . $e->getMessage());
        }

        try {
            $earliest = $this->platformUserReader->getAllPriorAccountsByPaymentAccount($user);
            return new EarliestAccount(user: $earliest, reason: 'attribute_api_cc');
        } catch (Exception $e) {
            $this->logger->debug('No Earliest Account Found By Payment Account: ' . $e->getMessage());
        }


        throw new Exception('No Earliest Account Found', ExceptionCode::NO_EARLIER_ACCOUNT->value);
    }

    /**
     * Attrbute the user
     *
     * @param PlatformUser $user            The User Data Array
     * @param MpCode       $newMpCode       Incoming mp_code To Attribute to
     * @param Varchar      $reason          Why We're Attributing The User
     * @param bool         $forceBountyPaid Should We Recalculate The Bounty Paid Code
     *
     * @return MpCode The mp_code the user was attributed to wich could have ben overloaded to wwww
     */
    public function attributeUser(PlatformUser $user, MpCode $newMpCode, Varchar $reason = new Varchar(''), bool $forceBountyPaid = false): MpCode
    {
        $mpCodeValid = true;

        $originalMpCode = $user->getMpCode();
        assert($originalMpCode !== null);

        try {
            $affiliate = $this->affiliateReader->getAffiliateByMpCode($newMpCode);
        } catch (Exception $e) {
            $mpCodeValid = false;

            // If the mp_code is not valid, we overload to wwww so we can hold them to allow time for the affiliate to capture them or report them missing.
            $newMpCode = new MpCode('wwww');

            $reason = new Varchar($reason->value . ' - Invalid mp_code provided: ' . $newMpCode->value . ', overloading to wwww: ' . $e->getMessage());
        }

        $bountyPaid = $user->getBountyPaid();
        assert($bountyPaid !== null);

        if ($mpCodeValid) {
            try {
                $rateType = $this->affiliateService->getRateTypeByMpCode($newMpCode, AffiliateProductType::LIVE)->value;
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
                $rateType = 'unknown';
            }

            assert($affiliate->getId() !== null);
            $isHouse = $this->affiliateService->isHouseAccount($affiliate->getId());

            if (!$forceBountyPaid) {
                if (preg_match('/pps/', $rateType) || preg_match('/pps_sliding/', $rateType)) {
                    $bountyPaid = 'N';
                } elseif ((preg_match('/sliding/', $rateType) || preg_match('/percent/', $rateType)) && $user->getBountyPaid() === 'H' && !$isHouse) {
                    $bountyPaid = 'R';
                } elseif (preg_match('/cpl/', $rateType)) {
                    $bountyPaid = 'L';
                } elseif (preg_match('/cpa/', $rateType)) {
                    $bountyPaid = 'A';
                } elseif (preg_match('/cpr/', $rateType)) {
                    $bountyPaid = 'C';
                }

                if ($bountyPaid !== $user->getBountyPaid()) {
                    $user->setBountyPaid($bountyPaid);
                }
            }
        }

        $user->setMpCode($newMpCode->value);

        // NOT_IMPLEMENTED: Update to user Platform user service for saving save
        // $platformUserService->save($platformUser);
        $this->affiliateWriter->updateUserMpCodeAndBountyPaid($user);

        assert($user->getUserId() !== null);
        $attributionLog = new AttributionLog(
            userId:     new PlatformUserId($user->getUserId()),
            oldMpCode:  new MpCode($originalMpCode),
            newMpCode:  $newMpCode,
            reason:     $reason,
            bountyPaid: new Varchar($bountyPaid),
            method:     new Varchar('first_money'),
        );

        $this->attributionLogWriter->save($attributionLog);


        // NOT_IMPLEMENTED: Move to a service
        //  Send Confirmation Message To The User
        $url = 'http://cs-ws-http.lan.vsmedia.net/models/send-referral-confirmation-message.php';

        $data = [
            'mp_code' => $newMpCode->value,
            'user_id' => $user->getUserId(),
        ];

        $this->logger->info('Sending referral confirmation for user ' . $user->getUserId() . ' with mp_code ' . $newMpCode->value . ' to ' . $url);

        // Your bearer token
        $token = 'WS_os3lk4a';

        // Initialize cURL
        $ch = curl_init($url); // @phpcs:ignore Generic.PHP.ForbiddenFunctions.FoundWithAlternative

        // Set options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        // Add headers with bearer token
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ]);

        // Execute request
        $response = curl_exec($ch);
        $this->logger->info('Referral confirmation response: ' . ($response === false ? 'cURL error: ' . curl_error($ch) : $response));

        // Close cURL
        curl_close($ch);

        return $newMpCode;
    }

    /**
     * Check if the transaction date is eligible.
     * An incoming transaction is considered ineligible if the last transaction is within the last 6 months of the incoming transaction.
     *
     * @param ?Transaction $lastTransaction         The transaction to check
     * @param UbixDateTime  $incomingTransactionDate The date to compare against in 'Y-m-d H:i:s' format
     *
     * @return bool True if the transaction date is eligible, false otherwise
     */
    public function isTransactionDateEligible(?Transaction $lastTransaction, UbixDateTime $incomingTransactionDate): bool
    {
        if ($lastTransaction === null || $lastTransaction->getDatetime() === null) {
            return true;
        }

        assert($lastTransaction->getDateTime() !== null);
        $transactionTimeObj = $lastTransaction->getDateTime()->value;

        $sixMonthsBefore = DateTime::createFromImmutable($incomingTransactionDate->value)->sub(new DateInterval('P6M'));

        return !($transactionTimeObj > $sixMonthsBefore);
    }

    /**
     * Returns The Users Registration Code Which Happens To Be A String In The DB
     *
     * @param PlatformUserId $userId The optiuser ID
     *
     * @return MpCode The mp_code the user registered with
     */
    private function getUserRegistrationMpCode(PlatformUserId $userId): MpCode
    {
        return $this->affiliateReader->getUserRegistrationMpCode($userId);
    }

    /**
     * Get the most recent transaction by user ID
     *
     * @param PlatformUserId $userId                 The user's ID
     * @param UbixDateTime    $currentTransactionDate The date to compare against in 'Y-m-d H:i:s' format
     *
     * @return Transaction An array containing the transaction details
     */
    private function getMostRecentTransactionByUserIdBeforeCurrentTransactionDate(PlatformUserId $userId, UbixDateTime $currentTransactionDate): Transaction
    {
        return $this->transactionReader->getMostRecentByUserIdBeforeDate($userId, $currentTransactionDate);
    }
}
