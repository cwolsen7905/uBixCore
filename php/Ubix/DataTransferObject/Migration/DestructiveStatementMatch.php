<?php

declare(strict_types=1);

namespace Ubix\DataTransferObject\Migration;

use Ubix\DataTransferObject\DtoInterface as Dto;
use Ubix\Enum\Migration\DestructiveStatementKind;

/**
 * One destructive-statement hit inside a migration body. Produced by
 * `DestructiveStatementDetectorService::detect()`.
 *
 * Per `docs/standards/migrations.md` §11.1. Carries the statement
 * `kind`, the 1-indexed line where the statement begins, and the
 * matched substring so the parser's failure message can quote it
 * back to the engineer.
 *
 * @see \Ubix\Tests\DataTransferObject\Migration\DestructiveStatementMatchTest PHPUnit test case
 */
final readonly class DestructiveStatementMatch implements Dto
{
    /**
     * Constructor
     *
     * @param DestructiveStatementKind $kind        Statement category — one of the patterns enumerated in `migrations.md` §11.1
     * @param int                      $lineNumber  Source line where the matched statement begins (1-indexed)
     * @param string                   $matchedText Verbatim substring captured by the detector regex (e.g. `DROP TABLE`)
     */
    public function __construct(
        public DestructiveStatementKind $kind,
        public int $lineNumber,
        public string $matchedText,
    ) {
    }
}
