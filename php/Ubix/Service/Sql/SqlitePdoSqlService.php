<?php

declare(strict_types=1);

namespace Ubix\Service\Sql;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Sql\AbstractPdoSqlService as PdoSqlService;

/**
 * Service to access SQLite using PDO
 *
 * @see \Ubix\Tests\Service\Sql\SqlitePdoSqlServiceTest PHPUnit test case
 */
final class SqlitePdoSqlService extends PdoSqlService
{
    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
    ) {
        parent::__construct($logger);

        $readDsn  = 'sqlite:' . getenv('SQLITE_DATABASE_PATH') . '/test.db';
        $writeDsn = 'sqlite:' . getenv('SQLITE_DATABASE_PATH') . '/test.db';

        $this->initializePdoConstructorParameters(
            readDsn:  $readDsn,
            writeDsn: $writeDsn,
        );

        //
        //  Attach every <name>.db beside test.db as a schema called <name>, so a query can
        //  address `<name>.table` exactly as it would against MySQL. Which schemas exist is
        //  the host's business: it decides by which files it puts in SQLITE_DATABASE_PATH.
        //
        foreach ($this->attachableDatabases() as $database) {
            $this->query('ATTACH DATABASE \'' . getenv('SQLITE_DATABASE_PATH') . '/' . $database . '.db\' as ' . $database);
            if ($readDsn !== $writeDsn) { // We call getRow() to run this query on the read PDO as well after query() handles the write PDO if they aren't identical
                $this->getRow('ATTACH DATABASE \'' . getenv('SQLITE_DATABASE_PATH') . '/' . $database . '.db\' as ' . $database);
            }
        }
    }

    /**
     * Schema names for every attachable *.db file in SQLITE_DATABASE_PATH
     *
     * Only names that are safe as unquoted SQL identifiers are attached; test.db is the
     * main database, not an attachment.
     *
     * @return string[]
     */
    private function attachableDatabases(): array
    {
        $databases = [];
        foreach (glob(getenv('SQLITE_DATABASE_PATH') . '/*.db') ?: [] as $file) {
            $name = basename($file, '.db');
            if ($name !== 'test' && preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) === 1) {
                $databases[] = $name;
            }
        }
        sort($databases);

        return $databases;
    }
}
