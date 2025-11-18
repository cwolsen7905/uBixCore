<?php

declare(strict_types=1);

namespace Ubix\Service;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Model\AgeVerificationRestrictedCountry;
use Ubix\Model\AgeVerificationRestrictedRegion;
use Ubix\Model\PlatformUser;
use Ubix\Model\PlatformUserAgeVerification;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;
use Ubix\Repository\PlatformUser\PlatformUserWriterInterface as PlatformUserWriter;
use Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationWriterInterface as PlatformUserAgeVerificationWriter;
use Ubix\Repository\PlatformUserPayment\PlatformUserPaymentReaderInterface as PlatformUserPaymentReader;
use Ubix\Repository\ScreenName\ScreenNameReaderInterface as ScreenNameReader;

/**
 * Service to access platform users
 *
 * @see \Ubix\Tests\Service\PlatformUserServiceTest PHPUnit test case
 */
final class PlatformUserService
{
    /**
     * Constructor
     *
     * @param Logger                            $logger                            Logger
     * @param PlatformUserAgeVerificationWriter $platformUserAgeVerificationWriter Platform user age verification writer
     * @param PlatformUserPaymentReader         $platformUserPaymentReader         Platform user payment reader
     * @param PlatformUserReader                $platformUserReader                Platform user reader
     * @param PlatformUserWriter                $platformUserWriter                Platform user writer
     * @param ScreenNameReader                  $screenNameReader                  Screen name reader
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private PlatformUserAgeVerificationWriter $platformUserAgeVerificationWriter,
        private PlatformUserPaymentReader $platformUserPaymentReader,
        private PlatformUserReader $platformUserReader,
        private PlatformUserWriter $platformUserWriter,
        private ScreenNameReader $screenNameReader,
    ) {
    }

    /**
     * Get a platform user by ID
     *
     * @param int     $id      The platform user's ID
     * @param ?string $sitekey The platform user's sitekey (optional)
     *
     * @throws Exception If a platform user is not found with a matching ID
     *
     * @return PlatformUser The platform user with matching ID
     */
    public function getById(int $id, ?string $sitekey = null): PlatformUser
    {
        $platformUsers = $this->platformUserReader->getById($id, $sitekey);

        if (count($platformUsers) > 0) {
            return $platformUsers[0];
        } else {
            throw new Exception('No platform user found with the ID `' . $id . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_PLATFORM_USER_ID->value);
        }
    }

    /**
     * Get a platform user by screen name
     *
     * @param string  $screenName The platform user's screen name
     * @param ?string $sitekey    The platform user's sitekey (optional)
     *
     * @throws Exception If a platform user is not found with a matching screen name
     *
     * @return PlatformUser The platform user with a matching screen name
     */
    public function getByScreenName(string $screenName, ?string $sitekey = null): PlatformUser
    {
        $platformUsers = $this->platformUserReader->getByScreenName($screenName, $sitekey);
        if (count($platformUsers) > 0) {
            return $platformUsers[0];
        } else {
            throw new Exception('No platform user found with the screen name `' . $screenName . '`', ExceptionCode::NO_MATCHES_FOUND_FOR_PLATFORM_USER_SCREEN_NAME->value);
        }
    }

    /**
     * Get platform users by OAuth account
     *
     * @param string $oauthProvider The OAuth provider
     * @param string $oauthGuid     The OAuth GUID
     *
     * @return PlatformUser[] The platform users with a matching OAuth account
     */
    public function getByOauth(string $oauthProvider, string $oauthGuid): array
    {
        return $this->platformUserReader->getByOauth($oauthProvider, $oauthGuid);
    }

    /**
     * Load a platform user's screen names
     *
     * @param PlatformUser $platformUser The platform user
     *
     * @return void
     */
    public function loadScreenNames(PlatformUser $platformUser): void
    {
        assert(is_int($platformUser->getUserId()));
        $screenNames = $this->screenNameReader->getByOptiusersId($platformUser->getUserId());

        if (count($screenNames) > 0) {
            //
            //  Determine which screen name is flagged as the chat default and assign that one to the platform user
            //
            $defaultScreenNamePosition = 0; // Set to zero by default so if no match is found the first screen name will be used as the default
            foreach ($screenNames as $i => $screenName) {
                if ($screenName->getChatDefault() === 'Y') {
                    $defaultScreenNamePosition = $i;
                    break;
                }
            }

            $platformUser->setScreenName($screenNames[$defaultScreenNamePosition]->getScreenName());

            //
            //  Assign all screen names to the platform user
            //
            $platformUser->setScreenNames($screenNames);
        }
    }

    /**
     * Verify a platform user's age verification status against an age verification restricted country and region
     *
     * @param PlatformUser                     $platformUser                     The platform user
     * @param AgeVerificationRestrictedCountry $ageVerificationRestrictedCountry The age verification restricted country
     * @param AgeVerificationRestrictedRegion  $ageVerificationRestrictedRegion  The age verification restricted region
     *
     * @return void
     */
    public function verifyAgeVerificationStatus(
        PlatformUser $platformUser,
        AgeVerificationRestrictedCountry $ageVerificationRestrictedCountry,
        AgeVerificationRestrictedRegion $ageVerificationRestrictedRegion,
    ): void {
        if ($platformUser->getAgeVerified() === 'N') { // Only verify for platform users that aren't already age verified
            //
            //  Determine if the platform user qualifies for age verification
            //
            $qualifiesForAgeVerification = false;

            assert(is_int($platformUser->getUserId()));
            foreach ($this->platformUserPaymentReader->getByUserId($platformUser->getUserId()) as $platformUserPayment) {
                $dateCreatedIsValid  = $platformUserPayment->getDateCreated() <= $ageVerificationRestrictedRegion->getCreditCardCheckDate();
                $potential1clickUser = ($platformUserPayment->getAccountType() === 'SEGPAY' && $platformUserPayment->getPaymentType() === '') || !in_array($platformUserPayment->getProcessorId(), [9, 11, 17], true); // TEMPORARY: ANDREW:: [9,11,17]] are hardcoded numbers from AgeVerify.cl - move them into a constant
                $hasSpent            = $platformUser->getTotalSpent() !== null && $platformUser->getTotalSpent() > 0;
                if ($dateCreatedIsValid && (!$potential1clickUser || $hasSpent)) {
                    $qualifiesForAgeVerification = true;
                    break;
                }
            }

            //
            //  If the platform user qualifies for age verification then verify them
            //
            if ($qualifiesForAgeVerification) {
                //
                //  Create a new platform user age verification object and save it
                //
                $platformUserAgeVerification = new PlatformUserAgeVerification(
                    userId:        $platformUser->getUserId(),
                    country:       $ageVerificationRestrictedCountry->getCode(),
                    state:         $ageVerificationRestrictedRegion->getCode() !== AgeVerificationRestrictedRegion::CODE_FOR_WHOLE_COUNTRY ? $ageVerificationRestrictedRegion->getCode() : '',
                    dateVerified:  new DateTime(),
                    method:        'Credit Card - flirt.fans', // NOT_IMPLEMENTED: flirt.fans should not be hard coded like this but is fine for right now as nothing else invokes this code
                    avsRegulation: $ageVerificationRestrictedRegion->getAvsRegulationCode(),
                );

                $this->platformUserAgeVerificationWriter->save($platformUserAgeVerification);

                //
                //  Update the platform user's age verification property and save it
                //
                $platformUser->setAgeVerified('Y');

                $this->platformUserWriter->save($platformUser);
            }
        }
    }
}
