<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the details of a request to the document 2257 VSM API
 *
 * @see \Ubix\Service\Document2257Service This DTO is used by the document 2257 service
 * @see \Ubix\Tests\DataTransferObject\Document2257UbixApiRequestDataTest PHPUnit test case
 */
final readonly class Document2257UbixApiRequestData implements Dto
{
    /**
     * Constructor
     *
     * @param string   $legalFirstName             Legal first name
     * @param string   $legalLastName              Legal last name
     * @param string   $dateOfBirth                Date of birth
     * @param string   $formOfIdentification       Form of identification
     * @param string   $issuingAuthority           Issuing authority
     * @param string   $idNumber                   ID number
     * @param string   $stageNameForThisProduction Stage name for this production
     * @param string   $todaysDate                 Today's date
     * @param string   $beginDate                  Begin date
     * @param string   $primaryProducer            Primary product
     * @param string   $modelId                    Model ID
     * @param string[] $allOtherNames              Array of all other names
     * @param string   $signature                  Signature
     */
    public function __construct(
        public readonly string $legalFirstName,
        public readonly string $legalLastName,
        public readonly string $dateOfBirth,
        public readonly string $formOfIdentification,
        public readonly string $issuingAuthority,
        public readonly string $idNumber,
        public readonly string $stageNameForThisProduction,
        public readonly string $todaysDate,
        public readonly string $beginDate,
        public readonly string $primaryProducer,
        public readonly string $modelId,
        public readonly array $allOtherNames,
        public readonly string $signature,
    ) {
    }
}
