<?php

declare(strict_types=1);

namespace Ubix\Service\DatabaseEnvironment;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\DatabaseEnvironment\DatabaseEnvironment;
use Ubix\Enum\Env;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentReaderInterface as DatabaseEnvironmentReader;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentWriterInterface as DatabaseEnvironmentWriter;

/**
 * The database's own answer to "which environment are you?"
 *
 * A process's `ENV` and `MYSQL_WRITE_HOST` say where it *thinks* it is
 * connected; a misconfigured manifest, pipeline or laptop can make that
 * wrong. The label lives in the data, so it is right about the server the
 * connection actually reached -- and a clone of production carries
 * production's label until someone deliberately changes it.
 *
 * - `migrate:up --target=X` refuses a database labelled anything but X, and
 *   labels an unlabelled one X.
 * - Destructive tools (a host's data reset, a scrub) require a non-production
 *   label that matches the process's `ENV`, and stamp "sanitised".
 * - `database:label` changes a label on purpose (e.g. staging after a
 *   re-clone from production), and clears "sanitised".
 *
 * Only dev, staging and prod take part. `test` and `sandbox` targets are
 * scratch databases that several pipelines share on purpose, so they are
 * never labelled and never refused.
 *
 * @see \Ubix\Tests\Service\DatabaseEnvironment\DatabaseEnvironmentServiceTest PHPUnit test case
 */
final class DatabaseEnvironmentService
{
    /**
     * Constructor
     *
     * @param Logger                    $logger Logger
     * @param DatabaseEnvironmentReader $reader Reads the label
     * @param DatabaseEnvironmentWriter $writer Writes it
     */
    public function __construct(
        private Logger $logger,
        private DatabaseEnvironmentReader $reader,
        private DatabaseEnvironmentWriter $writer,
    ) {
    }

    /**
     * The database's label, or null when it has none
     *
     * @return ?DatabaseEnvironment The label
     */
    public function current(): ?DatabaseEnvironment
    {
        return $this->reader->current();
    }

    /**
     * Whether an environment takes part in labelling at all
     *
     * @param Env $environment The environment
     *
     * @return bool True for dev, staging and prod
     */
    public function participates(Env $environment): bool
    {
        return in_array($environment, [Env::DEV, Env::STAGING, Env::PROD], true);
    }

    /**
     * The label if it names a different environment than `$target`, else null
     *
     * An unlabelled database conflicts with nothing, and neither does a
     * `test` or `sandbox` target.
     *
     * @param Env $target The environment the caller means to act on
     *
     * @return ?Env The conflicting label
     */
    public function conflictWith(Env $target): ?Env
    {
        if (!$this->participates($target)) {
            return null;
        }

        $current = $this->reader->current();

        return $current !== null && $current->environment !== $target ? $current->environment : null;
    }

    /**
     * Label an unlabelled database; leaves a labelled one alone, and gives test/sandbox the table only
     *
     * @param Env    $environment The environment
     * @param string $actor       Who
     *
     * @return bool Whether a label was written
     */
    public function labelIfUnlabelled(Env $environment, string $actor): bool
    {
        $this->writer->ensureTable();

        // The test and sandbox targets get the (empty) table but never a label. The table
        // must exist on a replay built with `migrate:up --target=test`, or a
        // schema-drift check that compares a tier against that replay reports
        // the table as drift on every tier.
        if (!$this->participates($environment)) {
            return false;
        }

        if ($this->reader->current() !== null) {
            return false;
        }

        $this->writer->label($environment, $actor);
        $this->logger->notice('Database labelled', ['actor' => $actor, 'environment' => $environment->value]);

        return true;
    }

    /**
     * Change a label on purpose; the caller must name the current one
     *
     * Naming it is the confirmation: relabelling a production clone as staging
     * is exactly the operation that must not happen by accident.
     *
     * @param Env    $from  The label the caller believes is there
     * @param Env    $to    The new label
     * @param string $actor Who
     *
     * @throws InvalidArgumentException When `$from` is not the current label, or `$to` does not take part
     *
     * @return void
     */
    public function relabel(Env $from, Env $to, string $actor): void
    {
        if (!$this->participates($to)) {
            throw new InvalidArgumentException('Only dev, staging and prod are labelled', ExceptionCode::VALIDATION_FAILED->value);
        }

        $this->writer->ensureTable();
        $current = $this->reader->current()?->environment;
        if ($current !== $from) {
            throw new InvalidArgumentException(sprintf('This database is labelled %s, not %s', $current->value ?? 'nothing', $from->value), ExceptionCode::VALIDATION_FAILED->value);
        }

        $this->writer->label($to, $actor);
        $this->logger->warning('Database relabelled', ['actor' => $actor, 'from' => $from->value, 'to' => $to->value]);
    }

    /**
     * Stamp the database as sanitised; never a production one
     *
     * @param Env    $expected The label the caller checked before sanitising
     * @param string $actor    Who
     *
     * @throws InvalidArgumentException When the label is missing, production, or not `$expected`
     *
     * @return void
     */
    public function markSanitised(Env $expected, string $actor): void
    {
        $current = $this->reader->current()?->environment;
        if ($current === null || $current === Env::PROD || $current !== $expected) {
            throw new InvalidArgumentException('Only a labelled, non-production database can be marked sanitised', ExceptionCode::VALIDATION_FAILED->value);
        }

        $this->writer->markSanitised($actor);
    }
}
