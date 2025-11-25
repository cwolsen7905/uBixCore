<?php

declare(strict_types=1);

use DI\Container;
use DI\ContainerBuilder;
use Latte\Engine;
use Latte\Loaders\FileLoader;
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
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\HttpClient\CurlHttpClient;
use Ubix\Service\FilterService;
use Ubix\Service\Sql\MysqlPdoSqlService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\SimpleCache\MemcachedLegacySimpleCache;

use function DI\autowire;
use function DI\get;

return static function (): Container {
    $appName = getenv('APP_NAME');
    if ($appName === false || !is_string($appName) || trim($appName) === '') {
        throw new Exception('No app name found', ExceptionCode::APP_NAME_MISSING->value);
    }

    $theme = (require __DIR__ . '/Theme.php');
    assert(is_string($theme));

    $memcacheServers = getenv('MEMCACHE_SERVERS');
    $memcacheServers = explode(',', is_string($memcacheServers) ? $memcacheServers : '');

    $container = new ContainerBuilder();

    $rootFolder = __DIR__ . '/../../../';
    $cacheDir   = str_starts_with(getenv('LATTE_CACHE_DIRECTORY') ?: '', '/') ? getenv('LATTE_CACHE_DIRECTORY') : $rootFolder . getenv('LATTE_CACHE_DIRECTORY');
    assert(is_string($cacheDir));
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }
    $templateDir = str_starts_with(getenv('LATTE_TEMPLATES_DIRECTORY') ?: '', '/') ? getenv('LATTE_TEMPLATES_DIRECTORY') : $rootFolder . (getenv('LATTE_TEMPLATES_DIRECTORY') ?: '');

    $container->addDefinitions([

        Engine::class                            => autowire()->method('setAutoRefresh', true)->method('setTempDirectory', $cacheDir)->method('setLoader', new FileLoader($templateDir . $theme)),
        FilterService::class                     => autowire()->constructorParameter('bearerToken', getenv('VSM_FILTER_API_BEARER_TOKEN_BROADCASTING')), 
        HttpClient::class                        => autowire(CurlHttpClient::class),
        Logger::class                            => autowire(MonologLogger::class)->constructorParameter('name', $appName)->constructorParameter('handlers', [new StreamHandler(getenv('LOGGER_PATH') . '/' . $appName . '.log', getenv('IS_SANDBOX') === 'true' || getenv('IS_DEV') === 'true' ? Level::Debug : Level::Info, true, 0777)])->constructorParameter('processors', [new UidProcessor()]),
        Psr17Factory::class                      => autowire(Psr17Factory::class),
        RequestFactory::class                    => get(Psr17Factory::class),
        ResponseFactory::class                   => autowire(SlimResponseFactory::class),
        SimpleCache::class                       => autowire(MemcachedLegacySimpleCache::class)->constructorParameter('servers', $memcacheServers),
        SqlService::class                        => autowire(MysqlPdoSqlService::class),
        StreamFactory::class                     => get(Psr17Factory::class),
    ]);

    return $container->build();
};
