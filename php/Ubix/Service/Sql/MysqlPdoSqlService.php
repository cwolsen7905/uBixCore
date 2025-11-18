<?php

declare(strict_types=1);

namespace Ubix\Service\Sql;

use Psr\Log\LoggerInterface as Logger;
use Ubix\Service\Sql\AbstractPdoSqlService as PdoSqlService;

/**
 * Service to access MySQL using PDO
 *
 * @see \Ubix\Tests\Service\Sql\MysqlPdoSqlServiceTest PHPUnit test case
 */
final class MysqlPdoSqlService extends PdoSqlService
{
    private const DSN_SPRINTF_FORMAT = 'mysql:host=%s;port=%s;dbname=%s;charset=latin1';

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        parent::__construct($logger);

        $isPhpUnit = getenv('PHPUNIT_RUNNING') === '1';

        $readDsn = sprintf(
            self::DSN_SPRINTF_FORMAT,
            $isPhpUnit ? getenv('TEST_MYSQL_READ_HOST') : getenv('MYSQL_READ_HOST'),
            $isPhpUnit ? getenv('TEST_MYSQL_READ_PORT') : getenv('MYSQL_READ_PORT'),
            $isPhpUnit ? getenv('TEST_MYSQL_READ_DATABASE') : getenv('MYSQL_READ_DATABASE'),
        );

        $writeDsn = sprintf(
            self::DSN_SPRINTF_FORMAT,
            $isPhpUnit ? getenv('TEST_MYSQL_WRITE_HOST') : getenv('MYSQL_WRITE_HOST'),
            $isPhpUnit ? getenv('TEST_MYSQL_WRITE_PORT') : getenv('MYSQL_WRITE_PORT'),
            $isPhpUnit ? getenv('TEST_MYSQL_WRITE_DATABASE') : getenv('MYSQL_WRITE_DATABASE'),
        );

        $this->initializePdoConstructorParameters(
            readDsn:       $readDsn,
            readUsername:  (string)($isPhpUnit ? getenv('TEST_MYSQL_READ_USERNAME') : getenv('MYSQL_READ_USERNAME')),
            readPassword:  (string)($isPhpUnit ? getenv('TEST_MYSQL_READ_PASSWORD') : getenv('MYSQL_READ_PASSWORD')),
            writeDsn:      $writeDsn,
            writeUsername: (string)($isPhpUnit ? getenv('TEST_MYSQL_WRITE_USERNAME') : getenv('MYSQL_WRITE_USERNAME')),
            writePassword: (string)($isPhpUnit ? getenv('TEST_MYSQL_WRITE_PASSWORD') : getenv('MYSQL_WRITE_PASSWORD')),
        );
    }
}
