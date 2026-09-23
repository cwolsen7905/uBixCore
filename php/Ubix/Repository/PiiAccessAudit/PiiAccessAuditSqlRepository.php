<?php

declare(strict_types=1);

namespace Ubix\Repository\PiiAccessAudit;

use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Audit\PiiAccess;
use Ubix\Enum\UbixDatabase;
use Ubix\Repository\PiiAccessAudit\PiiAccessAuditWriterInterface as PiiAccessAuditWriter;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;

/**
 * SQL-backed writer for `SYSTEMS.Pii_Access_Audits`
 *
 * In `SYSTEMS` rather than a host's product database, per the standard: the
 * trail sits above every subject domain, so no single product's schema owns it.
 * The database name goes through `UbixDatabase::SYSTEMS->databaseName()` so a
 * prefixed test run writes to its own tracker, exactly as the migration
 * tracker does.
 *
 * @see \Ubix\Tests\Repository\PiiAccessAudit\PiiAccessAuditSqlRepositoryTest PHPUnit test case
 */
final class PiiAccessAuditSqlRepository implements PiiAccessAuditWriter
{
    /**
     * Constructor
     *
     * @param Logger     $logger     The Monolog logger
     * @param SqlService $sqlService The SQL service
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private SqlService $sqlService,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function recordAccess(PiiAccess $access): void
    {
        if ($access->subjectIds === []) {
            return;
        }

        $rows       = [];
        $parameters = [
            'admin_id'    => $access->actorId,
            'entity_type' => $access->entityType,
            'reason'      => $access->reason,
            'search_term' => $access->searchTerm,
        ];

        // One statement for the whole access, one row per subject. The
        // shared values are bound once and reused, so a page of fifty
        // subjects is one round trip rather than fifty.
        foreach (array_values($access->subjectIds) as $index => $subjectId) {
            $rows[]                         = '(:admin_id, :entity_type, :subject' . $index . ', :search_term, :reason)';
            $parameters['subject' . $index] = $subjectId;
        }

        $sql = 'INSERT INTO ' . UbixDatabase::SYSTEMS->databaseName() . '.Pii_Access_Audits
                    (admin_id, entity_type, subject_id, search_term, reason)
                VALUES ' . implode(', ', $rows);

        $this->sqlService->query($sql, $parameters, true);
    }

    /**
     * {@inheritDoc}
     */
    public function purgeAccessesBefore(string $cutoff, int $limit = 10000): void
    {
        // Bounded by LIMIT rather than deleting a whole history at once: the
        // first run after a retention policy is introduced can face years of
        // rows, and one unbounded DELETE would lock the table for all of them.
        // A caller with a backlog calls this again; writers return void by
        // house rule, so "how many were left" is not this method's to answer.
        $sql = 'DELETE FROM ' . UbixDatabase::SYSTEMS->databaseName() . '.Pii_Access_Audits
                 WHERE date_created < :cutoff
                 LIMIT ' . max(1, $limit);

        $this->sqlService->query($sql, ['cutoff' => $cutoff]);
    }
}
