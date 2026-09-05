<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the parameters of a PDO constructor
 *
 * @see \Ubix\Service\Pdo\AbstractPdoService This DTO is used by the abstract PDO service
 * @see \Ubix\Tests\DataTransferObject\PdoConstructorParametersTest PHPUnit test case
 */
final readonly class PdoConstructorParameters implements Dto
{
    /**
     * Constructor
     *
     * @param string             $dsn      The Data Source Name, or DSN, contains the information required to connect to the database
     * @param ?string            $username The user name for the DSN string. This parameter is optional for some PDO drivers. (optional)
     * @param ?string            $password The password for the DSN string. This parameter is optional for some PDO drivers. (optional)
     * @param ?array<int, mixed> $options  A key=>value array of driver-specific connection options (optional)
     */
    public function __construct(
        public readonly string $dsn,
        public readonly ?string $username = null,
        public readonly ?string $password = null,
        public readonly ?array $options = null,
    ) {
    }
}
