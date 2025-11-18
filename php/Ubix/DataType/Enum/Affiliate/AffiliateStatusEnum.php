<?php

declare(strict_types=1);

namespace Ubix\DataType\Enum\Affiliate;

use UnitEnum;
use Ubix\DataType\Enum\AbstractEnumDataType as EnumDataType;
use Ubix\Enum\Affiliate\AffiliateStatus;

/**
 * Object for creating and validating Status enum values
 *
 * @see \Ubix\Tests\DataType\Enum\Affiliate\AffiliateStatusEnumTest PHPUnit test case
 */
class AffiliateStatusEnum extends EnumDataType
{
    /**
     * Constructor
     *
     * @param UnitEnum|string $input The input value
     */
    public function __construct(
        UnitEnum|string $input,
    ) {
        if ($input instanceof UnitEnum) {
            parent::__construct($input);
        } else {
            parent::__construct(self::caseInsensitiveFrom($input, AffiliateStatus::class));
        }
    }
}
