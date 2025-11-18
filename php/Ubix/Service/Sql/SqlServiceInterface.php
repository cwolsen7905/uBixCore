<?php

declare(strict_types=1);

namespace Ubix\Service\Sql;

use Generator;

/**
 * Interface for a service managing SQL databases
 *
 * Basic example:
 * ```
 * $sqlService->query('INSERT INTO table SET column = "value"');
 * ```
 *
 * Basic example with a parameter:
 * ```
 * $sqlService->query('INSERT INTO table SET column = :column', ['column' => 'value']);
 * ```
 *
 * Select a single column example:
 * ```
 * $fetch1 = $sqlService->getColumn('SELECT column FROM table WHERE id = 123 LIMIT 1');
 * ```
 *
 * Select a single row example:
 * ```
 * $row = $sqlService->getRow('SELECT * FROM table WHERE id = 123 LIMIT 1');
 * ```
 *
 * Select multiple rows example:
 * ```
 * foreach ($sqlService->getRows('SELECT * FROM table') as $row) {
 *     var_dump($row);
 * }
 * ```
 *
 * Last inserted ID example:
 * ```
 * $sqlService->query('INSERT INTO table SET column = "value"');
 * var_dump($sqlService->lastInsertId()); // The ID of the newly inserted row
 * ```
 */
interface SqlServiceInterface
{
    /**
     * Execute a SQL query
     *
     * Example:
     * ```
     * $sqlService->query('INSERT INTO table SET column = "value"');
     * ```
     *
     * Example with a parameter:
     * ```
     * $sqlService->query('INSERT INTO table SET column = :column', ['column' => 'value']);
     * ```
     *
     * @param string                                        $sql                 The SQL query
     * @param array<int|string, bool|int|float|string|null> $parameters          An array of named parameters (optional)
     * @param bool                                          $allowParameterReuse Whether or not to support reusing parameters (optional) (default: false)
     *
     * @return int The number of rows affected by the query
     */
    public function query(string $sql, array $parameters = [], bool $allowParameterReuse = false): int;

    /**
     * Return a single column value from a SQL query
     *
     * Example:
     * ```
     * $fetch1 = $sqlService->getColumn('SELECT column FROM table WHERE id = 123 LIMIT 1');
     * ```
     *
     * @param string                                        $sql                 The SQL query
     * @param array<int|string, bool|int|float|string|null> $parameters          An array of named parameters (optional)
     * @param bool                                          $allowParameterReuse Whether or not to support reusing parameters (optional) (default: false)
     *
     * @return bool|int|float|string|null This method will return the value of the first column of the first row of the parameterized SQL query
     */
    public function getColumn(string $sql, array $parameters = [], bool $allowParameterReuse = false): bool|int|float|string|null;

    /**
     * Return a single row of values from a SQL query
     *
     * Example:
     * ```
     * $row = $sqlService->getRow('SELECT * FROM table WHERE id = 123 LIMIT 1');
     * ```
     *
     * @param string                                        $sql                 The SQL query
     * @param array<int|string, bool|int|float|string|null> $parameters          An array of parameters, either indexed or named (optional)
     * @param bool                                          $allowParameterReuse Whether or not to support reusing parameters (optional) (default: false)
     *
     * @return array<string, bool|int|float|string|null>|false Returns the first row of the parameterized SQL query if a row is found, otherwise false
     */
    public function getRow(string $sql, array $parameters = [], bool $allowParameterReuse = false): array|false; // TEMPORARY: ANDREW:: is return false an anti-pattern? Andrew needs to discuss with Chris

    /**
     * Return multiple rows of values from a SQL query
     *
     * Example:
     * ```
     * foreach ($sqlService->getRows('SELECT * FROM table') as $row) {
     *     var_dump($row);
     * }
     * ```
     *
     * @param string                                        $sql                 The SQL query
     * @param array<int|string, bool|int|float|string|null> $parameters          An array of parameters, either indexed or named (optional)
     * @param bool                                          $allowParameterReuse Whether or not to support reusing parameters (optional) (default: false)
     *
     * @return Generator Yields each row from the result set as an array
     */
    public function getRows(string $sql, array $parameters = [], bool $allowParameterReuse = false): Generator;

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * Example:
     * ```
     * $sqlService->query('INSERT INTO table SET column = "value"');
     * var_dump($sqlService->lastInsertId()); // The ID of the newly inserted row
     * ```
     *
     * @param ?string $name Name of the sequence object from which the ID should be returned
     *
     * @return string|false If a sequence name was not specified for the name parameter, lastInsertId() returns a string representing the row ID of the last row that was inserted into the database. If a sequence name was specified for the name parameter, lastInsertId() returns a string representing the last value retrieved from the specified sequence object.
     */
    public function lastInsertId(?string $name = null): string|false; // TEMPORARY: ANDREW:: is return false an anti-pattern? Andrew needs to discuss with Chris

    /**
     * Initialize a transaction
     *
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Commit the current transaction
     *
     * @return void
     */
    public function commit(): void;

    /**
     * Roll back the current transaction
     *
     * @return void
     */
    public function rollBack(): void;

    /**
     * Determine if there is a current transaction
     *
     * @return bool Returns true if a transaction is currently active or false if not
     */
    public function inTransaction(): bool;
}
