<?php

declare(strict_types=1);

namespace Ubix\Service\Migration;

use InvalidArgumentException;
use Psr\Log\LoggerInterface as Logger;
use Ubix\DataTransferObject\Migration\MigrationFile;

/**
 * Scans `sql/migrations/` for migration files and returns them
 * parsed and ordered.
 *
 * Per `docs/standards/migrations.md` §2 the filename format is
 * `YYYYMMDDHHMMSS_<snake_case_description>.sql`. The reserved
 * bootstrap prefix `00000000000000_` is also accepted (matches the
 * 14-digit-prefix regex naturally). Anything else in the directory
 * is rejected — the scanner refuses to silently skip files with
 * malformed names so the operator notices.
 *
 * Sort order is filename ascending — i.e. timestamp ascending,
 * so the bootstrap migration always sorts first and subsequent
 * migrations apply in chronological author-order.
 *
 * @see \Ubix\Tests\Service\Migration\MigrationFileScannerServiceTest PHPUnit test case
 */
final class MigrationFileScannerService
{
    /**
     * Migration filename pattern. 14-digit timestamp prefix + `_`
     * + snake_case description + `.sql`.
     *
     * The 14-digit prefix matches both the reserved bootstrap
     * value `00000000000000` and any real `YYYYMMDDHHMMSS`
     * authored at runtime.
     */
    private const string FILENAME_REGEX = '/^\d{14}_[a-z0-9_]+\.sql$/';

    /**
     * Constructor
     *
     * @param Logger                     $logger         PSR-3 logger
     * @param MigrationFileParserService $parserService  Per-file header parser
     * @param string                     $migrationsPath Absolute path to the `sql/migrations/` directory
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private MigrationFileParserService $parserService,
        private string $migrationsPath,
    ) {
    }

    /**
     * Scan the migrations directory and return every parsed
     * migration file in filename order.
     *
     * @return MigrationFile[]
     *
     * @throws InvalidArgumentException When the directory is missing, unreadable, or contains a file whose name doesn't match the standard format
     */
    public function scan(): array
    {
        if (! is_dir($this->migrationsPath)) {
            throw new InvalidArgumentException(sprintf(
                'Migrations directory `%s` does not exist.',
                $this->migrationsPath,
            ));
        }
        if (! is_readable($this->migrationsPath)) {
            throw new InvalidArgumentException(sprintf(
                'Migrations directory `%s` is not readable.',
                $this->migrationsPath,
            ));
        }

        $entries = scandir($this->migrationsPath);
        if ($entries === false) {
            throw new InvalidArgumentException(sprintf(
                'Migrations directory `%s` could not be scanned.',
                $this->migrationsPath,
            ));
        }

        $files = [];
        foreach ($entries as $entry) {
            if (str_starts_with($entry, '.')) { // `.`, `..`, and placeholders such as the skeleton's `.gitkeep` or editor droppings
                continue;
            }
            $fullPath = rtrim($this->migrationsPath, '/') . '/' . $entry;
            if (! is_file($fullPath)) {
                continue;
            }
            if (preg_match(self::FILENAME_REGEX, $entry) !== 1) {
                throw new InvalidArgumentException(sprintf(
                    'Migration file `%s` does not match the standard filename format `YYYYMMDDHHMMSS_<snake_case>.sql`.',
                    $fullPath,
                ));
            }
            $files[] = $fullPath;
        }

        sort($files);

        $parsed = [];
        foreach ($files as $filePath) {
            $parsed[] = $this->parserService->parse($filePath);
        }

        return $parsed;
    }
}
