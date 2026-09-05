<?php

declare(strict_types=1);

namespace Ubix\Enum\RegularExpression;

/**
 * Enumeration of regular expression patterns
 *
 * @see \Ubix\Tests\Enum\RegularExpression\RegularExpressionPatternTest PHPUnit test case
 */
enum RegularExpressionPattern: string
{
    case BASE64_ENCODED = '(?:[A-Za-z0-9+\/]{4})*(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?';
    case UUID_V4        = '[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
}
