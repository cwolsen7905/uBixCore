<?php

declare(strict_types=1);

namespace Ubix\DataType\Enum\Affiliate;

use UnitEnum;
use Ubix\DataType\Enum\AbstractEnumDataType as EnumDataType;
use Ubix\Enum\Affiliate\AffiliateSiteType;

/**
 * Object for creating and validating Char strings
 *
 * @see \Ubix\Tests\DataType\String\CharTest PHPUnit test case
 * @see \Ubix\Tests\DataType\Enum\Affiliate\AffiliateSiteTypeEnumTest PHPUnit test case
 */
class AffiliateSiteTypeEnum extends EnumDataType
{
    /**
     * Constructor
     *
     * @param UnitEnum|string|int $input The input value
     */
    public function __construct(
        UnitEnum|string|int $input,
    ) {
        if ($input instanceof UnitEnum) {
            parent::__construct($input);
        } else {
            parent::__construct(self::caseInsensitiveFrom($input, AffiliateSiteType::class));
        }
    }
}
