<?php

declare(strict_types=1);

namespace Ubix\Repository\DatabaseEnvironment;

use Ubix\Enum\Env;

/**
 * Writes the database's own environment label
 *
 * @see \Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository
 */
interface DatabaseEnvironmentWriterInterface
{
    /**
     * Create `SYSTEMS.Database_Environment` if it does not exist (needs DDL rights)
     *
     * @return void
     */
    public function ensureTable(): void;

    /**
     * Set the label, replacing any other; clears the sanitised stamp
     *
     * @param Env    $environment The environment
     * @param string $actor       Who
     *
     * @return void
     */
    public function label(Env $environment, string $actor): void;

    /**
     * Record that the labelled database's data has been sanitised
     *
     * @param string $actor Who
     *
     * @return void
     */
    public function markSanitised(string $actor): void;
}
