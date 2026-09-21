<?php

declare(strict_types=1);

namespace Ubix\Service\Audit;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Audit\PiiAccess;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Repository\PiiAccessAudit\PiiAccessAuditWriterInterface as PiiAccessAuditWriter;

/**
 * The single insert path for the personal-data access trail
 *
 * `docs/standards/sensitive-data-access.md`: a surface that returns personal
 * data must record who accessed whose data, when, and why, as a durable row —
 * not a log line, which rotates and cannot be queried by subject. Surfaces call
 * this after producing a result; they never write the table themselves.
 *
 * It refuses an incomplete record rather than writing a partial one: an audit
 * row with no actor or no reason answers none of the questions it exists for,
 * and a surface that cannot supply them has a bug worth failing loudly on.
 *
 * @see \Ubix\Tests\Service\Audit\PiiAccessAuditServiceTest PHPUnit test case
 */
final class PiiAccessAuditService
{
    /**
     * The widest entity type the column accepts
     */
    private const MAX_ENTITY_TYPE_LENGTH = 32;

    /**
     * Constructor
     *
     * @param Logger               $logger Logger
     * @param PiiAccessAuditWriter $writer The trail's writer
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most uBixCore classes but has not been implemented in this class yet)
        private PiiAccessAuditWriter $writer,
    ) {
    }

    /**
     * Record that an operator accessed these subjects' personal data
     *
     * @param PiiAccess $access The access
     *
     * @throws InvalidArgumentException When the actor, entity type or reason is missing or invalid
     *
     * @return void
     */
    public function record(PiiAccess $access): void
    {
        if ($access->actorId <= 0) {
            throw new InvalidArgumentException('A PII access needs the operator who made it', ExceptionCode::VALIDATION_FAILED->value);
        }

        if ($access->entityType === '' || strlen($access->entityType) > self::MAX_ENTITY_TYPE_LENGTH) {
            throw new InvalidArgumentException('A PII access needs an entity type of at most 32 characters', ExceptionCode::VALIDATION_FAILED->value);
        }

        if ($access->reason === '') {
            throw new InvalidArgumentException('A PII access needs a reason', ExceptionCode::VALIDATION_FAILED->value);
        }

        // De-duplicated so a result listing the same person twice does not
        // inflate how often they appear to have been accessed.
        $subjects = array_values(array_unique(array_filter(
            $access->subjectIds,
            static function (int $id): bool {
                return $id > 0;
            },
        )));

        if ($subjects === []) {
            return;
        }

        $this->writer->recordAccess(new PiiAccess(
            actorId:    $access->actorId,
            entityType: $access->entityType,
            subjectIds: $subjects,
            reason:     $access->reason,
            searchTerm: $access->searchTerm,
        ));
    }
}
