<?php

declare(strict_types=1);

namespace Ubix\Service\Sql;

use Exception;
use Generator;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\PdoConstructorParameters;
use Ubix\DataTransferObject\PdoError;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Exception\DtoException;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Abstract service to access a database using PDO
 */
abstract class AbstractPdoSqlService implements SqlService
{
    private PdoConstructorParameters $readPdoConstructorParameters;

    private PdoConstructorParameters $writePdoConstructorParameters;

    /**
     * An array of PDO objects available as singletons stored with a key that is the MD5 hash of the PDO constructor parameters
     *
     * @var array<string, PDO> $singletons
     */
    private static array $singletons = [];

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql, array $parameters = [], bool $allowParameterReuse = false): int
    {
        // TEMPORARY: Ignore TRUNCATE statements during PHPUnit tests just until we are comfortable.
        if (stripos($sql, 'TRUNCATE') !== false && $this->writePdoConstructorParameters->username !== 'root') {
            // During PHPUnit tests we want to ignore TRUNCATE statements as they can cause issues with foreign key constraints
            $this->logger->info('Ignoring TRUNCATE statement during PHPUnit tests.', ['sql' => $sql, 'parameters' => $parameters]);
            return 0;
        }

        return $this->getPdoStatement(true, $sql, $parameters, $allowParameterReuse)->rowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn(string $sql, array $parameters = [], bool $allowParameterReuse = false): bool|int|float|string|null
    {
        $column = $this->getPdoStatement(false, $sql, $parameters, $allowParameterReuse)->fetchColumn(0);
        assert(is_scalar($column) || $column === null);
        return $column;
    }

    /**
     * {@inheritDoc}
     */
    public function getRow(string $sql, array $parameters = [], bool $allowParameterReuse = false): array|false
    {
        /**
         * @var array<string, bool|int|float|string|null>|false $row
         */
        $row = $this->getPdoStatement(false, $sql, $parameters, $allowParameterReuse)->fetch();
        return $row;
    }

    /**
     * {@inheritDoc}
     */
    public function getRows(string $sql, array $parameters = [], bool $allowParameterReuse = false): Generator
    {
        $pdoStatement = $this->getPdoStatement(false, $sql, $parameters, $allowParameterReuse);
        while (($row = $pdoStatement->fetch()) !== false) {
            yield $row;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string|false
    {
        return $this->getPdo(true)->lastInsertId($name);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the transaction fails to begin
     */
    public function beginTransaction(): void
    {
        if ($this->getPdo(true)->beginTransaction() === false) {
            throw new Exception('The transaction failed to begin.', ExceptionCode::BEGIN_TRANSACTION_FAILED_IN_PDO->value);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the transaction fails to commit
     */
    public function commit(): void
    {
        if ($this->getPdo(true)->commit() === false) {
            throw new Exception('The transaction failed to commit.', ExceptionCode::COMMIT_TRANSACTION_FAILED_IN_PDO->value);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If the transaction fails to roll back
     */
    public function rollBack(): void
    {
        if ($this->getPdo(true)->rollBack() === false) {
            throw new Exception('The transaction failed to roll back.', ExceptionCode::ROLL_BACK_TRANSACTION_FAILED_IN_PDO->value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function inTransaction(): bool
    {
        return $this->getPdo(true)->inTransaction();
    }

    /**
     * Initialize the readPdoConstructorParameter and writePdoConstructorParameter objects
     *
     * @param string             $readDsn       For reads: The Data Source Name, or DSN, contains the information required to connect to the database
     * @param ?string            $readUsername  For reads: The user name for the DSN string. This parameter is optional for some PDO drivers. (optional) (default: null)
     * @param ?string            $readPassword  For reads: The password for the DSN string. This parameter is optional for some PDO drivers. (optional) (default: null)
     * @param ?array<int, mixed> $readOptions   For reads: A key=>value array of driver-specific connection options (optional) (default: null)
     * @param ?string            $writeDsn      For writes: The Data Source Name, or DSN, contains the information required to connect to the database (read parameters will be used for writes if this parameter is omitted) (optional) (default: null)
     * @param ?string            $writeUsername For writes: The user name for the DSN string. This parameter is optional for some PDO drivers. (optional) (default: null)
     * @param ?string            $writePassword For writes: The password for the DSN string. This parameter is optional for some PDO drivers. (optional) (default: null)
     * @param ?array<int, mixed> $writeOptions  For writes: A key=>value array of driver-specific connection options (optional) (default: null)
     *
     * @return void
     */
    protected function initializePdoConstructorParameters(
        string $readDsn,
        ?string $readUsername = null,
        ?string $readPassword = null,
        ?array $readOptions = null,
        ?string $writeDsn = null,
        ?string $writeUsername = null,
        ?string $writePassword = null,
        ?array $writeOptions = null,
    ): void {
        $readPdoConstructorParameters = new PdoConstructorParameters(
            dsn:      $readDsn,
            username: $readUsername,
            password: $readPassword,
            options:  $readOptions,
        );

        $this->readPdoConstructorParameters = $readPdoConstructorParameters;

        $this->writePdoConstructorParameters = $writeDsn === null ? $readPdoConstructorParameters : new PdoConstructorParameters(
            dsn:      $writeDsn,
            username: $writeUsername,
            password: $writePassword,
            options:  $writeOptions,
        );
    }

    /**
     * Get a PDO object
     *
     * @param bool $useWrite Whether or not to use the write PDO or the read PDO (optional) (default: false)
     *
     * @throws DtoException When the database connection fails (with a \Ubix\DataTransferObject\PdoError DTO)
     *
     * @return PDO A PDO database instance
     */
    private function getPdo(bool $useWrite = false): PDO
    {
        try {
            $singletonKey = md5(serialize($useWrite ? $this->writePdoConstructorParameters : $this->readPdoConstructorParameters));

            if (!isset(self::$singletons[$singletonKey])) {
                self::$singletons[$singletonKey] = new PDO(
                    $useWrite ? $this->writePdoConstructorParameters->dsn : $this->readPdoConstructorParameters->dsn,
                    $useWrite ? $this->writePdoConstructorParameters->username : $this->readPdoConstructorParameters->username,
                    $useWrite ? $this->writePdoConstructorParameters->password : $this->readPdoConstructorParameters->password,
                    $useWrite ? $this->writePdoConstructorParameters->options : $this->readPdoConstructorParameters->options,
                );

                self::$singletons[$singletonKey]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$singletons[$singletonKey]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$singletons[$singletonKey]->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }

            return self::$singletons[$singletonKey];
        } catch (PDOException $e) {
            throw new DtoException(
                'The database connection failed.',
                ExceptionCode::ERROR_CONNECTING_TO_PDO->value,
                $this->pdoExceptionToDto($e),
            );
        }
    }

    /**
     * Get a PDO statement by executing a SQL query
     *
     * @param bool                                          $useWrite            Whether or not to use the write PDO or the read PDO
     * @param string                                        $sql                 The SQL query
     * @param array<int|string, bool|int|float|string|null> $parameters          An array of named parameters (optional)
     * @param bool                                          $allowParameterReuse Whether or not to support reusing parameters (optional) (default: false)
     *
     * @throws DtoException When the query fails (with a \Ubix\DataTransferObject\PdoError DTO object)
     *
     * @return PDOStatement This method will always return a PDOStatement, if there is a problem executing the query then an exception will be thrown
     */
    private function getPdoStatement(bool $useWrite, string $sql, array $parameters = [], bool $allowParameterReuse = false): PDOStatement
    {
        try {
            //
            //  If the $allowParameterReuse flag is set to true then go through the query and manually split up any reused parameters
            //
            if ($allowParameterReuse) {
                foreach ($parameters as $parameter => $value) {
                    if (is_string($parameter)) {
                        $pattern = '/:' . preg_quote($parameter, '/') . '(?=$|[^\p{L}\p{N}_])/u'; // Match :parameter and make sure the next character is either the end of the string or a character that isn't a letter, number or underscore (including Unicode characters)

                        $parameterCount = preg_match_all($pattern, $sql);
                        if ($parameterCount > 1) { // Only do these replacements if there are multiple occurences of :parameter in the SQL query
                            //
                            //  Replace each occurrence of :parameter with :parameter_i and update $parameters accordingly
                            //
                            for ($i = 1; $i <= $parameterCount; $i++) {
                                $newParameter = $parameter . '_' . $i; // Determine the new parameter name

                                $sql = preg_replace($pattern, ':' . $newParameter, $sql, 1); // Replace the next occurence of :parameter in the SQL query with :parameter_i
                                assert(is_string($sql));

                                $parameters[$newParameter] = $value; // Add the new parameter to the $parameters array
                            }

                            //
                            //  Remove the original parameter
                            //
                            unset($parameters[$parameter]);
                        }
                    }
                }
            }

            //
            //  Prepare and execute the query
            //
            $query = $this->getPdo($useWrite)->prepare($sql);
            $query->execute($parameters);
            return $query;
        } catch (PDOException $e) {
            throw new DtoException(
                'The query execution failed.',
                ExceptionCode::ERROR_EXECUTING_PDO_QUERY->value,
                $this->pdoExceptionToDto($e, $sql, $parameters),
            );
        }
    }

    /**
     * Convert a PDOException into a PdoError DTO
     *
     * @param PDOException                                   $e          The PDO exception
     * @param ?string                                        $sql        The SQL query (optional)
     * @param ?array<int|string, bool|int|float|string|null> $parameters An array of named parameters (optional)
     *
     * @return PdoError The PdoError DTO
     */
    private function pdoExceptionToDto(PDOException $e, ?string $sql = null, ?array $parameters = null): PdoError
    {
        //
        //  We have to do some work to get the correct $sqlState, $driverCode and $driver_messsage values because despite what the type hints claim `$e->getCode()` may return a string, e.g. "HY093"
        //
        $sqlState = isset($e->errorInfo[0]) && is_string($e->errorInfo[0]) ? $e->errorInfo[0] : null;

        $driverCode = null;
        if (isset($e->errorInfo[1]) && is_int($e->errorInfo[1])) {
            $driverCode = (string)$e->errorInfo[1];
        } elseif (isset($e->errorInfo[1]) && is_string($e->errorInfo[1])) {
            $driverCode = $e->errorInfo[1];
        } elseif (is_int($e->getCode())) {
            $driverCode = (string)$e->getCode();
        } elseif (is_string($e->getCode())) {
            $driverCode = $e->getCode();
        }

        $driverMessage = isset($e->errorInfo[2]) && is_string($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();

        //
        //  Return a PdoError DTO
        //
        return new PdoError(
            sqlState:      $sqlState,
            driverCode:    $driverCode,
            driverMessage: $driverMessage,
            sql:           $sql,
            parameters:    $parameters,
        );
    }
}
