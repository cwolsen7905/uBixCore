<?php

declare(strict_types=1);

/**
 * Bootstrap functions for uBixCore host projects
 *
 * The functions in this file are what a host project's thin entry points call:
 * `bin/ubix` runs `environment()` then `console()`, `public/index.php` runs
 * `environment()` then `http()`. They are loaded through Composer's `files`
 * autoload, so a host never needs to know where the package is installed.
 *
 * Kept procedural (like `vault.php`) on purpose: the bootstrap runs before the DI
 * container exists, so it cannot follow the class conventions (logger-first
 * constructors, container-built) the rest of the framework is held to.
 */

namespace Ubix\Bootstrap;

use Composer\Autoload\ClassLoader;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use Dotenv\Dotenv;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\App;
use SplFileInfo;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Ubix\Console\Command\Cron\ListCommand;
use Ubix\Enum\Exception\ExceptionCode;

/**
 * Prepare the process environment for a host project
 *
 * Sets the timezone, loads the host's `.env`, exports `UBIX_PROJECT_ROOT` for
 * the DI definitions (see `Ubix\Service\ProjectRootService`), runs the uBix Vault
 * credential hook, and turns on error display in sandbox/dev.
 *
 * @param string $projectRoot The host project root (the directory holding composer.json, app/, vendor/)
 *
 * @return string The normalised project root
 *
 * @throws Exception When `$projectRoot` is not a directory
 */
function environment(string $projectRoot): string
{
    $root = realpath($projectRoot);
    if ($root === false || !is_dir($root)) {
        throw new Exception('Project root `' . $projectRoot . '` is not a directory');
    }

    date_default_timezone_set('America/New_York');

    if (file_exists($root . '/.env')) {
        Dotenv::createUnsafeImmutable($root)->load();
    }

    if (getenv('UBIX_PROJECT_ROOT') === false) {
        putenv('UBIX_PROJECT_ROOT=' . $root);
    }

    //
    //  Resolve database credentials from uBix Vault when configured (a no-op
    //  unless VAULT_ADDR is set - local dev keeps using the git-ignored .env).
    //
    $vaultBootstrapper = require __DIR__ . '/vault.php';
    if (is_callable($vaultBootstrapper)) {
        $vaultBootstrapper();
    }

    if (getenv('IS_SANDBOX') === 'true' || getenv('IS_DEV') === 'true') {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        register_shutdown_function(static function (): void {
            $error = error_get_last();
            if ($error !== null) {
                echo '<pre>Fatal error in ', $error['file'], ' on line ', $error['line'], ':', PHP_EOL, $error['message'], '</pre>';
            }
        });
    }

    return $root;
}

/**
 * Build the Symfony Console application for a host project
 *
 * Commands are discovered by namespace: every class under each namespace in
 * `$commandNamespaces` (resolved through Composer's PSR-4 map, so host namespaces
 * work the same as `Ubix\Console\Command`) that extends the Symfony command
 * class is pulled from the container and registered. Abstract classes are
 * skipped; `cron:*` commands are only loaded when a `cron:` argument is present
 * (except `cron:list`).
 *
 * @param string   $dependenciesFile  Path to the host's CLI `Dependencies.php` (returns a container-building closure)
 * @param string[] $commandNamespaces Namespaces to scan for commands, e.g. `['Ubix\Console\Command', 'Acme\Console\Command']`
 * @param string[] $argv              The process arguments (`$_SERVER['argv']`)
 *
 * @return Application
 *
 * @throws Exception When a discovered class is missing or is not a command
 */
function console(string $dependenciesFile, array $commandNamespaces, array $argv): Application
{
    $buildContainer = require $dependenciesFile;
    assert(is_callable($buildContainer));
    $container = $buildContainer();
    assert($container instanceof Container);

    $application = new Application();

    $includeCrons = false;
    foreach ($argv as $argument) {
        if (str_contains($argument, 'cron:')) {
            $includeCrons = true;
            break;
        }
    }

    foreach ($commandNamespaces as $namespace) {
        foreach (discoverClasses($namespace) as $className) {
            if (!class_exists($className)) {
                throw new Exception('Command class `' . $className . '` does not exist', ExceptionCode::MISSING_COMMAND_CLASS->value);
            }

            if (!is_subclass_of($className, SymfonyCommand::class)) {
                throw new Exception('Command class `' . $className . '` does not extend `' . SymfonyCommand::class . '`', ExceptionCode::INVALID_COMMAND_CLASS->value);
            }

            if (str_starts_with(substr($className, (strrpos($className, '\\') ?: 0) + 1), 'Abstract')) {
                continue;
            }

            if (str_starts_with($className, 'Ubix\\Console\\Command\\Cron\\') && !$includeCrons && $className !== ListCommand::class) {
                continue;
            }

            $command = $container->get($className);
            assert($command instanceof SymfonyCommand);

            $application->add($command);
        }
    }

    return $application;
}

/**
 * Build the Slim application for one of a host project's apps
 *
 * Loads `app/<AppName>/src/{Dependencies,Middleware,Routes}.php` from the host,
 * each of which returns a closure, exactly as the apps in this repo do.
 *
 * @param string $projectRoot The host project root
 * @param string $appName     The app to serve (`APP_NAME`), a folder under `app/`
 *
 * @return App<Container>
 *
 * @throws Exception When the app name is empty or the app folder does not exist
 */
function http(string $projectRoot, string $appName): App
{
    if (trim($appName) === '') {
        throw new Exception('No app name found', ExceptionCode::APP_NAME_MISSING->value);
    }

    $appFolder = $projectRoot . '/app/' . $appName;
    if (!is_dir($appFolder)) {
        throw new Exception('App folder `' . $appFolder . '` does not exist', ExceptionCode::APP_NAME_MISSING->value);
    }

    /**
     * @var callable():?Container $buildContainer
     */
    $buildContainer = require $appFolder . '/src/Dependencies.php';
    assert(is_callable($buildContainer));

    $container = $buildContainer();
    assert($container === null || $container instanceof Container);

    /**
     * @var App<Container> $slimApp
     */
    $slimApp = Bridge::create($container);

    /**
     * @var callable(App<Container>):void $applyMiddleware
     */
    $applyMiddleware = require $appFolder . '/src/Middleware.php';
    assert(is_callable($applyMiddleware));
    $applyMiddleware($slimApp);

    /**
     * @var callable(App<Container>):void $applyRoutes
     */
    $applyRoutes = require $appFolder . '/src/Routes.php';
    assert(is_callable($applyRoutes));
    $applyRoutes($slimApp);

    return $slimApp;
}

/**
 * List the fully-qualified class names under a namespace, using Composer's PSR-4 map
 *
 * Walks every directory Composer has registered for the longest matching PSR-4
 * prefix, recursively, and maps each `.php` file back to a class name. Files are
 * returned sorted so registration order is deterministic.
 *
 * @param string $namespace Namespace to scan, without a trailing backslash
 *
 * @return string[]
 *
 * @throws Exception When no registered PSR-4 prefix covers the namespace
 */
function discoverClasses(string $namespace): array
{
    $namespace = trim($namespace, '\\') . '\\';
    $classes   = [];
    $matched   = false;

    foreach (ClassLoader::getRegisteredLoaders() as $loader) {
        foreach ($loader->getPrefixesPsr4() as $prefix => $directories) {
            if (!str_starts_with($namespace, $prefix)) {
                continue;
            }
            $matched  = true;
            $relative = str_replace('\\', '/', substr($namespace, strlen($prefix)));

            foreach ($directories as $directory) {
                $base = realpath($directory . '/' . $relative);
                if ($base === false || !is_dir($base)) {
                    continue;
                }

                /**
                 * @var iterable<string, SplFileInfo> $files
                 */
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($files as $file) {
                    if ($file->getExtension() !== 'php') {
                        continue;
                    }
                    $classPath = substr($file->getPathname(), strlen($base) + 1, -4);
                    $classes[] = $namespace . str_replace('/', '\\', $classPath);
                }
            }
        }
    }

    if (!$matched) {
        throw new Exception('No PSR-4 autoload prefix covers the namespace `' . $namespace . '`', ExceptionCode::MISSING_COMMAND_CLASS->value);
    }

    sort($classes);

    return array_values(array_unique($classes));
}
