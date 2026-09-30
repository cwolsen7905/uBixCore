<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Identity;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for a freshly issued one-time code
 *
 * Two halves that must go to different places, which is the reason this is a type
 * rather than an array: the **plaintext** is sent to the subject and then forgotten,
 * and the **digest** is the only half that may be stored. A method returning one
 * string would let a caller store the wrong one and nothing would fail until a
 * database leaked.
 *
 * @see \Ubix\Service\Identity\OneTimeCodeService::issue()
 * @see \Ubix\Tests\DataTransferObject\Identity\OneTimeCodeTest PHPUnit test case
 */
final readonly class OneTimeCode implements Dto
{
    /**
     * Constructor
     *
     * @param string $code   The digits to send to the subject; never stored and never logged
     * @param string $digest The peppered HMAC to store in place of the code
     */
    public function __construct(
        public readonly string $code = '',
        public readonly string $digest = '',
    ) {
    }
}
