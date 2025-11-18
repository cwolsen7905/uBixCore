<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\DataType\String\MpCode;

/**
 * Data transfer object for a attribution response
 *
 * @see \Ubix\Tests\DataTransferObject\CollectionTest PHPUnit test case
 * @see \Ubix\Tests\DataTransferObject\AttributionTest PHPUnit test case
 */
final readonly class Attribution implements Dto
{
    /**
     * Constructor
     *
     * @param ?string $message The response message
     * @param ?int    $code    The response code
     * @param ?MpCode $mpCode  The mp_code involved in the attribution
     */
    public function __construct(
        public readonly ?string $message = null,
        public readonly ?int $code = null,
        public readonly ?MpCode $mpCode = null,
    ) {
    }
}
