<?php

declare(strict_types=1);

namespace Ubix\Enum;

/**
 * The MySQL databases uBix Core talks to — one case per schema. `databaseName()`
 * applies the DATABASE_PREFIX env (per-pipeline test schemas), so query sites
 * must always go through it rather than hard-coding the schema name.
 *
 * @see \Ubix\Tests\Enum\UbixDatabaseTest PHPUnit test case
 */
enum UbixDatabase: string
{
    case SOWINGME = 'sowingme';
    case SYSTEMS  = 'SYSTEMS';

    /**
     * The schema name to use in SQL for this database (DATABASE_PREFIX applied)
     *
     * @return string The prefixed schema name
     */
    public function databaseName(): string
    {
        $prefix = (string) getenv('DATABASE_PREFIX');

        return $prefix . $this->value;
    }
}
