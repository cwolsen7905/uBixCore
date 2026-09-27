<?php

declare(strict_types=1);

namespace Ubix\Repository\DatabaseEnvironment;

use Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment;

/**
 * Reads the database's own environment label
 *
 * @see \Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository
 */
interface DatabaseEnvironmentReaderInterface
{
    /**
     * The label, or null when the database has none (no table, or no row)
     *
     * @return ?DatabaseEnvironment The label
     */
    public function current(): ?DatabaseEnvironment;
}
