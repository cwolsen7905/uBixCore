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
    private const DATABASES = [
        'ntl_db',
        'BILLING',
        'VSCASH',
    ];

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        parent::__construct($logger);

        $readDsn  = 'sqlite:' . getenv('SQLITE_DATABASE_PATH') . '/test.db';
        $writeDsn = 'sqlite:' . getenv('SQLITE_DATABASE_PATH') . '/test.db';

        $this->initializePdoConstructorParameters(
            readDsn:  $readDsn,
            writeDsn: $writeDsn,
        );

        foreach (self::DATABASES as $database) {
            $this->query('ATTACH DATABASE \'' . getenv('SQLITE_DATABASE_PATH') . '/' . $database . '.db\' as ' . $database);
            if ($readDsn !== $writeDsn) { // We call getRow() to run this query on the read PDO as well after query() handles the write PDO if they aren't identical
                $this->getRow('ATTACH DATABASE \'' . getenv('SQLITE_DATABASE_PATH') . '/' . $database . '.db\' as ' . $database);
            }
        }
    }
}
