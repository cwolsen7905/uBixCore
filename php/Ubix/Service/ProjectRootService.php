<?php

declare(strict_types=1);

namespace Ubix\Service;

use Exception;
use Psr\Log\LoggerInterface as Logger;

/**
 * Resolve paths inside the host project that uses uBixCore
 *
 * Composer installs uBixCore into `vendor/ubixsys/ubixcore`, so nothing in the
 * framework may derive the project root from its own `__DIR__`. The host binds the
 * root once in its DI definitions (`app/<App>/src/Dependencies.php`, overridable
 * with `UBIX_PROJECT_ROOT`) and every command or service that needs `app/`, `sql/`,
 * `vendor/bin/` or the like asks this service instead.
 *
 * @see \Ubix\Tests\Service\ProjectRootServiceTest PHPUnit test case
 */
final class ProjectRootService
{
    /**
     * Constructor
     *
     * @param Logger $logger Logger
     * @param string $root   Absolute path of the host project root (the directory holding composer.json, app/, sql/ and vendor/)
     *
     * @throws Exception When `$root` is not an existing directory
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
        private string $root,
    ) {
        $realpath = realpath($this->root);
        if ($realpath === false || !is_dir($realpath)) {
            throw new Exception('Project root `' . $this->root . '` is not a directory');
        }
        $this->root = $realpath;
    }

    /**
     * Get the absolute, normalised project root (no trailing slash)
     *
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * Get an absolute path under the project root
     *
     * `getPath('sql', 'SOWINGME.sql')` → `<root>/sql/SOWINGME.sql`. Segments may
     * themselves contain slashes; leading and trailing slashes are normalised.
     *
     * @param string ...$segments Path segments relative to the root
     *
     * @return string
     */
    public function getPath(string ...$segments): string
    {
        $path = $this->root;
        foreach ($segments as $segment) {
            $segment = trim($segment, '/');
            if ($segment !== '') {
                $path .= '/' . $segment;
            }
        }
        return $path;
    }

    /**
     * Get the absolute path of a Composer-installed binary (`<root>/vendor/bin/<tool>`)
     *
     * @param string $tool Binary name, e.g. `phpstan`
     *
     * @return string
     */
    public function getVendorBinPath(string $tool): string
    {
        return $this->getPath('vendor', 'bin', $tool);
    }
}
