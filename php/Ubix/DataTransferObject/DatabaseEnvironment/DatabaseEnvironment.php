<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\DatabaseEnvironment;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\Env;

/**
 * Which environment a database server says it belongs to
 *
 * One row in `SYSTEMS.Database_Environment`. The label travels with the data:
 * a clone of production arrives labelled `prod`, which is the point.
 *
 * @see \Ubix\Tests\DataTransferObject\DatabaseEnvironment\DatabaseEnvironmentTest PHPUnit test case
 */
final readonly class DatabaseEnvironment implements Dto
{
    /**
     * Constructor
     *
     * @param Env     $environment The environment the database belongs to
     * @param ?string $labelledAt  When the label was set (`Y-m-d H:i:s`)
     * @param ?string $labelledBy  Who set it
     * @param ?string $sanitisedAt When its data was last sanitised since labelling, or null
     * @param ?string $sanitisedBy Who sanitised it
     */
    public function __construct(
        public Env $environment,
        public ?string $labelledAt = null,
        public ?string $labelledBy = null,
        public ?string $sanitisedAt = null,
        public ?string $sanitisedBy = null,
    ) {
    }
}
