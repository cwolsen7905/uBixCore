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
use SessionHandlerInterface as SessionHandler;
use Slim\Psr7\Factory\ResponseFactory as SlimResponseFactory;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\HttpClient\CurlHttpClient;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenReaderInterface as TokenReader;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenWriterInterface as TokenWriter;
use Ubix\Repository\EmailConfirmationToken\EmailConfirmationTokenSqlRepository;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserWriterInterface as UserWriter;
use Ubix\Repository\User\UserSqlRepository;
use Ubix\Service\Sql\MysqlPdoSqlService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Middleware\CorsMiddleware;
use Ubix\Middleware\SessionAuthenticationMiddleware;
use Ubix\SessionHandler\SimpleCacheLegacySessionHandler;
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

    $allowedLevels = ['Alert', 'Critical', 'Debug', 'Emergency', 'Error', 'Info', 'Notice', 'Warning'];
    $logLevel      = ucfirst(getenv('LOG_LEVEL') ?: 'Info');

    $logLevel = in_array($logLevel, $allowedLevels, true) ? $logLevel : 'Info';
    assert(in_array($logLevel, $allowedLevels, true));

	$logFile = getenv('LOGGER_PATH') . '/' . strtolower(getenv('ENV') ?: 'sandbox') . '/' . $appName . '.log';

    $container->addDefinitions([

        Engine::class                            => autowire()->method('setAutoRefresh', true)->method('setTempDirectory', $cacheDir)->method('setLoader', new FileLoader($templateDir . $theme)),
        HttpClient::class                        => autowire(CurlHttpClient::class),
        Logger::class                            => autowire(MonologLogger::class)->constructorParameter('name', $appName)->constructorParameter('handlers', [new StreamHandler($logFile, Level::fromName($logLevel), true, 0777)])->constructorParameter('processors', [new UidProcessor()]),
        MailerInterface::class                   => static function (): MailerInterface {
            $dsn = 'smtp://10.50.50.61:616';
            $transport = Transport::fromDsn($dsn);
            return new Mailer($transport);
        },
        Psr17Factory::class                      => autowire(Psr17Factory::class),
        RequestFactory::class                    => get(Psr17Factory::class),
        ResponseFactory::class                   => autowire(SlimResponseFactory::class),
        TokenReader::class                       => autowire(EmailConfirmationTokenSqlRepository::class),
        TokenWriter::class                       => autowire(EmailConfirmationTokenSqlRepository::class),
        UserReader::class                        => autowire(UserSqlRepository::class),
        UserWriter::class                        => autowire(UserSqlRepository::class),
        SessionAuthenticationMiddleware::class   => autowire()->constructorParameter('excludedRoutes', [
			['method' => 'POST', 'path' => '/auth'],
			['method' => 'OPTIONS', 'path' => '/auth'],
			['method' => 'POST', 'path' => '/register'],
			['method' => 'GET', 'path' => '/confirm-email']
		]),
        SessionHandler::class                    => autowire(SimpleCacheLegacySessionHandler::class),
        SimpleCache::class                       => autowire(MemcachedLegacySimpleCache::class)->constructorParameter('servers', $memcacheServers),
        SqlService::class                        => autowire(MysqlPdoSqlService::class),
        StreamFactory::class                     => get(Psr17Factory::class),
        CorsMiddleware::class                    => autowire()->constructorParameter('allowedOrigins', [
            '127.0.0.1',
            'localhost',
            '.ubixsys.com',
            '.sowing.me',
        ]),
    ]);

    return $container->build();
};
