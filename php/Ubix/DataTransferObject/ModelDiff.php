<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject;

use Ubix\DataTransferObject\DtoInterface as Dto;

/**
 * Data transfer object for the details of an prospect application dif
 *
 * @see \Ubix\Model\ProspectApplication This DTO is used by the prospect application model
 * @see \Ubix\Tests\DataTransferObject\ModelDiffTest PHPUnit test case
 */
final readonly class ModelDiff implements Dto
{
    /**
     * Constructor
     *
     * @param string  $method     The method to call
     * @param mixed[] $parameters The parameter(s) to pass
     */
    public function __construct(
        public readonly string $method,
        public readonly array $parameters,
    ) {
    }
}
