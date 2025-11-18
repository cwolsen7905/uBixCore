<?php

declare(strict_types=1);

namespace Ubix\Repository\Performer;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\SqlRepository\PerformerOptions;
use Ubix\Model\Performer;
use Ubix\Repository\Performer\PerformerReaderInterface as PerformerReader;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * Repository for reading performer data
 *
 * @see \Ubix\Tests\Repository\Performer\PerformerSqlRepositoryTest PHPUnit test case
 */
final class PerformerSqlRepository implements PerformerReader
{
    /**
     * Constructor
     *
     * @param Logger     $logger     Logger
     * @param SqlService $sqlService SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getByUsername(string $username): array
    {
        return $this->query(new PerformerOptions(
            username: $username,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getForAutoLogin(string $username, string $passwordMd5): array
    {
        return $this->query(new PerformerOptions(
            passwordMd5: $passwordMd5,
            username:    $username,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getBySlug(string $slug): array
    {
        return $this->query(new PerformerOptions(
            limit: 1,
            slug:  $slug,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): array
    {
        return $this->query(new PerformerOptions(
            id:    $id,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByEmailAddress(string $emailAddress): array
    {
        return $this->query(new PerformerOptions(
            emailAddress: $emailAddress,
            limit:        1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getByName(string $name): array
    {
        return $this->query(new PerformerOptions(
            name:  $name,
            limit: 1,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getPartialMatches(string $name): array
    {
        return $this->query(new PerformerOptions(
            namePartial: $name,
            limit:       1,
        ));
    }

    /**
     * Query the database and transform the result(s) into object(s)
     *
     * @param PerformerOptions $options DTO of options to generate the query
     *
     * @return Performer[] Array of performer object(s)
     */
    private function query(PerformerOptions $options): array
    {
        //
        //  Store all named parameters in an array
        //
        $parameters = [];

        //
        //  Generate our SQL query based on the $options parameter
        //
        $sql = 'SELECT
					m.id,
					m.name,
					REPLACE(m.name, "_", "-") AS slug,
					m.username,
					m.password,
					m.enc_password,
					m.salt,
					m.image,
					m.studio AS studio_code,
					m.harvest_code,
					m.firstname,
					m.middlename,
					m.lastname,
                    m.active,
					s.broadcaster_id
				FROM ntl_db.models m
				INNER JOIN ntl_db.studios s ON s.studio = m.studio
				WHERE 1 = 1';

        if ($options->id !== null) {
            $sql .= ' AND m.id = :id';

            $parameters['id'] = $options->id;
        }

        if ($options->username !== null) {
            $sql .= ' AND m.username = :username';

            $parameters['username'] = $options->username;
        }

        if ($options->passwordMd5 !== null) {
            $sql .= ' AND MD5(m.password) = :passwordMd5';

            $parameters['passwordMd5'] = $options->passwordMd5;
        }

        if ($options->emailAddress !== null) {
            $sql .= ' AND LOWER(m.email) = :emailAddress';

            $parameters['emailAddress'] = strtolower($options->emailAddress);
        }

        if ($options->slug !== null) {
            $sql .= ' AND REPLACE(m.name, "_", "-") = :slug';

            $parameters['slug'] = $options->slug;
        }

        if ($options->name !== null) {
            $sql .= ' AND m.name = :name';

            $parameters['name'] = $options->name;
        }

        if ($options->namePartial !== null) {
            $sql .= ' AND LOWER(CONCAT(TRIM(m.firstname), " ", TRIM(m.lastname))) LIKE :namePartial';

            $parameters['namePartial'] = strtolower($options->namePartial);
        }

        if ($options->limit !== null) {
            $sql .= ' LIMIT ' . $options->limit;
        }

        //
        //  Query the database and return the result(s) as object(s)
        //
        $objects = [];

        /**
         * @var array{
         *     id: ?int,
         *     name: ?string,
         *     slug: ?string,
         *     username: ?string,
         *     password: ?string,
         *     enc_password: ?string,
         *     salt: ?string,
         *     image: ?string,
         *     studio_code: ?string,
         *     harvest_code: ?int,
         *     firstname: ?string,
         *     middlename: ?string,
         *     lastname: ?string,
         *     active: ?string,
         *     broadcaster_id: ?int,
         * } $row
         */
        foreach ($this->sqlService->getRows($sql, $parameters) as $row) {
            $objects[] = new Performer(
                id:            $row['id'],
                name:          $row['name'],
                slug:          $row['slug'],
                username:      $row['username'],
                password:      $row['password'],
                encPassword:   $row['enc_password'],
                salt:          $row['salt'],
                image:         $row['image'],
                studioCode:    $row['studio_code'],
                harvestCode:   $row['harvest_code'],
                firstname:     $row['firstname'],
                middlename:    $row['middlename'],
                lastname:      $row['lastname'],
                active:        $row['active'],
                broadcasterId: $row['broadcaster_id'],
            );
        }

        return $objects;
    }
}
