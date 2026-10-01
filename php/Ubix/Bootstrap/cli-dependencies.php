<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\UidProcessor;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\StreamFactoryInterface as StreamFactory;
use Psr\Log\LoggerInterface as Logger;
use Psr\SimpleCache\CacheInterface as SimpleCache;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;
use Ubix\HttpClient\CurlHttpClient;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentReaderInterface as DatabaseEnvironmentReader;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentSqlRepository;
use Ubix\Repository\DatabaseEnvironment\DatabaseEnvironmentWriterInterface as DatabaseEnvironmentWriter;
use Ubix\Repository\SchemaMigration\SchemaMigrationReaderInterface as SchemaMigrationReader;
use Ubix\Repository\SchemaMigration\SchemaMigrationSqlRepository;
use Ubix\Repository\SchemaMigration\SchemaMigrationWriterInterface as SchemaMigrationWriter;
use Ubix\Service\Ci\AiReviewService;
use Ubix\Service\Migration\MigrationFileScannerService;
use Ubix\Service\Migration\MigrationNotificationService;
use Ubix\Service\NativeProcessService;
use Ubix\Service\ProcessServiceInterface as ProcessService;
use Ubix\Service\ProjectRootService;
use Ubix\Service\SlackService;
use Ubix\Service\Sql\MigrationPdoSqlService;
use Ubix\Service\Sql\MysqlPdoSqlService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\SimpleCache\MemcachedLegacySimpleCache;

use function DI\autowire;
use function DI\get;

/**
 * Default PHP-DI container for `bin/ubix`. Ships with the framework; a host that
 * needs more bindings (its own repositories/services for its own commands) passes
 * its own file to `Ubix\Bootstrap\console()` instead.
 */

return static function (): Container {
    $appName = 'UbixCli';

    $memcacheServers = getenv('MEMCACHE_SERVERS');
    $memcacheServers = explode(',', is_string($memcacheServers) ? $memcacheServers : '');

    //
    //  Project root: UBIX_PROJECT_ROOT (exported by Ubix\Bootstrap\environment())
    //  or the working directory. This file ships with the framework, so it must
    //  not derive the root from its own __DIR__.
    //
    $projectRoot    = getenv('UBIX_PROJECT_ROOT');
    $projectRoot    = is_string($projectRoot) && $projectRoot !== '' ? $projectRoot : (getcwd() ?: '.');
    $migrationsPath = $projectRoot . '/sql/migrations';

    $container = new ContainerBuilder();

    $container->addDefinitions([
        HttpClient::class                       => autowire(CurlHttpClient::class),
        Logger::class                           => autowire(MonologLogger::class)->constructorParameter('name', $appName)->constructorParameter('handlers', [new StreamHandler(getenv('LOGGER_PATH') . '/' . $appName . '.log', getenv('IS_SANDBOX') === 'true' || getenv('IS_DEV') === 'true' ? Level::Debug : Level::Info)])->constructorParameter('processors', [new UidProcessor()]),
        // Anything that shells out depends on the interface so it can be doubled
        // in a test; `proc_open()` is the only implementation there is.
        ProcessService::class                   => autowire(NativeProcessService::class),
        Psr17Factory::class                     => autowire(Psr17Factory::class),
        RequestFactory::class                   => get(Psr17Factory::class),
        ResponseFactory::class                  => autowire(SlimResponseFactory::class),
        SimpleCache::class                      => autowire(MemcachedLegacySimpleCache::class)->constructorParameter('servers', $memcacheServers),
        StreamFactory::class                    => get(Psr17Factory::class),

        //
        //  Migration engine. The migrate:* commands are auto-discovered by
        //  bin/ubix, but the services they inject need these bindings.
        //
        MigrationFileScannerService::class      => autowire(MigrationFileScannerService::class)->constructorParameter('migrationsPath', $migrationsPath),
        ProjectRootService::class               => autowire()->constructorParameter('root', $projectRoot),
        SqlService::class                       => autowire(MysqlPdoSqlService::class),
        SchemaMigrationSqlRepository::class     => autowire(SchemaMigrationSqlRepository::class)->constructorParameter('sqlService', get(MigrationPdoSqlService::class)),
        SchemaMigrationReader::class            => get(SchemaMigrationSqlRepository::class),
        SchemaMigrationWriter::class            => get(SchemaMigrationSqlRepository::class),
        // The database's own environment label, read and written on the migration
        // connection (MYSQL_MIGRATION_* win) because migrate:up creates the table.
        DatabaseEnvironmentSqlRepository::class => autowire(DatabaseEnvironmentSqlRepository::class)->constructorParameter('sqlService', get(MigrationPdoSqlService::class)),
        DatabaseEnvironmentReader::class        => get(DatabaseEnvironmentSqlRepository::class),
        DatabaseEnvironmentWriter::class        => get(DatabaseEnvironmentSqlRepository::class),
        SlackService::class                     => autowire()
            ->constructorParameter('apiEndpoint', (string) getenv('SLACK_API_ENDPOINT'))
            ->constructorParameter('channelAllowlist', array_values(array_unique(array_filter([
                ...array_map('trim', explode(',', (string) getenv('SLACK_CHANNEL_ALLOWLIST'))),
                ltrim(getenv('SLACK_MIGRATION_CHANNEL') ?: '#databases', '#'), // Always allow the migration notifier's own channel
            ])))),
        MigrationNotificationService::class     => autowire()
            ->constructorParameter('channel', getenv('SLACK_MIGRATION_CHANNEL') ?: '#databases'),
        // A model reading a whole diff takes far longer than the shared client's 5 s
        // default; on that default every review timed out (kitg pipeline 6431).
        AiReviewService::class                  => autowire()
            ->constructorParameter('httpClient', autowire(CurlHttpClient::class)->constructorParameter('timeout', 240)),
    ]);

    return $container->build();
};
