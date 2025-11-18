<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the details of a PDO error
 *
 * @see \Ubix\Service\Pdo\AbstractPdoService This DTO is used by the abstract PDO service
 * @see \Ubix\Tests\DataTransferObject\PdoErrorTest PHPUnit test case
 */
final readonly class PdoError implements Dto
{
    /**
     * Constructor
     *
     * @param ?string                                        $sqlState      SQLSTATE error code (optional) (default: null)
     * @param ?string                                        $driverCode    Driver-specific error code (optional) (default: null)
     * @param ?string                                        $driverMessage Driver-specific error message (optional) (default: null)
     * @param ?string                                        $sql           SQL query if its failed execution triggered the PDO error (optional) (default: null)
     * @param ?array<int|string, bool|float|int|string|null> $parameters    Parameters of the SQL query if its failed execution triggered the PDO error (optional) (default: null)
     */
    public function __construct(
        public readonly ?string $sqlState = null,
        public readonly ?string $driverCode = null,
        public readonly ?string $driverMessage = null,
        public readonly ?string $sql = null,
        public readonly ?array $parameters = null,
    ) {
    }
}
