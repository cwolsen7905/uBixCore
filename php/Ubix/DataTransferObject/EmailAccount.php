<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for an email account
 *
 * @see \Ubix\Tests\DataTransferObject\EmailAccountTest PHPUnit test case
 */
final readonly class EmailAccount implements Dto
{
    /**
     * Constructor
     *
     * @param string  $emailAddress The account's email address
     * @param ?string $name         The account's name (optional) (default: null)
     */
    public function __construct(
        public readonly string $emailAddress,
        public readonly ?string $name = null,
    ) {
    }
}
