<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use DateTimeInterface;
use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the data of a prospect application submission
 *
 * @see \Ubix\Service\ProspectService This DTO is used by the prospect service
 * @see \Ubix\Tests\DataTransferObject\ProspectApplicationSubmissionDataTest PHPUnit test case
 */
final readonly class ProspectApplicationSubmissionData implements Dto
{
    /**
     * Constructor
     *
     * @param string                   $type                   The type
     * @param string                   $firstName              The first name
     * @param string                   $lastName               The last name
     * @param string                   $studioName             The studio name
     * @param string                   $studioWebsite          The studio website
     * @param string                   $emailAddress           The email address
     * @param string                   $phoneNumber            The phone number
     * @param string                   $preferredContactMethod The preferred contact method
     * @param string                   $countryCode            The country code
     * @param string                   $stateCode              The state code
     * @param string                   $password               The password
     * @param bool                     $omitPassword           Whether to omit password validation and setting
     * @param DateTimeInterface|string $dateOfBirth            The date of birth
     * @param string                   $gender                 The gender
     * @param string                   $tracker                The tracker
     * @param string                   $refAffiliateId         The referring affiliate ID
     * @param string                   $refModelId             The referring model ID
     * @param string                   $refBroadcasterId       The referring broadcaster ID
     * @param string                   $refScreenName          The referring screen name
     * @param string                   $language               The language
     * @param string                   $ipAddress              The IP address
     * @param string                   $userAgent              The user agent
     * @param ?int                     $campaignId             The campaign ID (optional) (default: null)
     */
    public function __construct(
        public readonly string $type,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $studioName,
        public readonly string $studioWebsite,
        public readonly string $emailAddress,
        public readonly string $phoneNumber,
        public readonly string $preferredContactMethod,
        public readonly string $countryCode,
        public readonly string $stateCode,
        public readonly string $password,
        public readonly bool $omitPassword,
        public readonly DateTimeInterface|string $dateOfBirth,
        public readonly string $gender,
        public readonly string $tracker,
        public readonly string $refAffiliateId,
        public readonly string $refModelId,
        public readonly string $refBroadcasterId,
        public readonly string $refScreenName,
        public readonly string $language,
        public readonly string $ipAddress,
        public readonly string $userAgent,
        public readonly ?int $campaignId = null,
    ) {
    }
}
