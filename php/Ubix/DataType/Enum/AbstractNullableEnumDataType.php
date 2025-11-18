<?php

declare(strict_types=1);

namespace Ubix\DataType\Enum;

use BackedEnum;
use UnitEnum;
use ValueError;
use Ubix\DataType\AbstractDataType as DataType;

/**
 * Abstract datatype for use.
 */
abstract class AbstractNullableEnumDataType extends DataType
{
    /**
     * Constructor
     *
     * @param ?UnitEnum $value The string value for this data type
     */
    public function __construct(
        public readonly ?UnitEnum $value,
    ) {
    }

    /**
     * Case-insensitive enum value resolver for string-backed and int-backed enums.
     *
     * @param string|int $value     The value to match (string or int)
     * @param string     $enumClass The fully qualified enum class name
     *
     * @return UnitEnum The matching enum case if found
     *
     * @throws ValueError If no matching enum case is found
     */
    public function caseInsensitiveFrom(string|int $value, string $enumClass): UnitEnum
    {
        // For backed enum match on value (case-insensitive for strings)
        if (is_subclass_of($enumClass, BackedEnum::class)) {
            foreach ($enumClass::cases() as $case) {
                if (is_string($case->value) && is_string($value)) {
                    if (strcasecmp($case->value, $value) === 0) {
                        return $case;
                    }
                } elseif (is_int($case->value) && is_numeric($value)) {
                    if ($case->value === (int)$value) {
                        return $case;
                    }
                }
            }
            throw new ValueError('No matching enum case found for value.');
        }

        // For unit enums, match by case name (case-insensitive)
        assert(is_subclass_of($enumClass, UnitEnum::class));
        foreach ($enumClass::cases() as $case) {
            if (strcasecmp($case->name, (string)$value) === 0) {
                return $case;
            }
        }

        throw new ValueError('No matching enum case found for value.');
    }
}
