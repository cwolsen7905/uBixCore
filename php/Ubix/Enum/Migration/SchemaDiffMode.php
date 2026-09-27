<?php

declare(strict_types=1);

namespace Ubix\Enum\Migration;

/**
 * How `migrate:diff` builds the schema it compares the live cluster against
 *
 * See `docs/standards/migrations.md` §9. The two modes answer different
 * questions, and only one of them is safe to gate CI on.
 *
 * @see \Ubix\Tests\Enum\Migration\SchemaDiffModeTest PHPUnit test case
 */
enum SchemaDiffMode: string
{
    /**
     * Diff live against the checked-in `sql/<DB>.sql` baseline.
     *
     * Advisory only. Under the frozen-baseline policy that file is the schema as
     * it stood *before* the migration runner existed, so every table added by a
     * migration since then reads as drift. Useful for a human asking "did anything
     * land that is in no migration?", structurally unusable as a gate.
     */
    case REFERENCE_DUMP = 'reference-dump';

    /**
     * Diff live against baseline + every applied migration, replayed into a
     * scratch server.
     *
     * The canonical mode: the migration history IS the expected schema, so a
     * difference here is real drift by definition and can gate a pipeline.
     */
    case REPLAY = 'replay';

    /**
     * Whether a difference in this mode is trustworthy enough to fail a build
     *
     * Exists so a caller cannot accidentally gate on the advisory mode: the
     * question "may I block on this?" is answered here rather than re-derived,
     * and wrongly, at each call site.
     *
     * @return bool True when drift in this mode means real drift
     */
    public function isGateable(): bool
    {
        return $this === self::REPLAY;
    }
}
