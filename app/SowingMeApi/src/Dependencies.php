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
use Ubix\Enum\Exception\ExceptionCode;
use Ubix\HttpClient\CurlHttpClient;
use Ubix\Repository\Affiliate\AffiliateReaderInterface as AffiliateReader;
use Ubix\Repository\Affiliate\AffiliateSqlRepository;
use Ubix\Repository\Affiliate\AffiliateWriterInterface as AffiliateWriter;
use Ubix\Repository\AttributionLog\AttributionLogReaderInterface as AttributionLogReader;
use Ubix\Repository\AttributionLog\AttributionLogSqlRepository;
use Ubix\Repository\AttributionLog\AttributionLogWriterInterface as AttributionLogWriter;
use Ubix\Repository\BillingTransaction\BillingTransactionReaderInterface as BillingTransactionReader;
use Ubix\Repository\BillingTransaction\BillingTransactionSqlRepository;
use Ubix\Repository\CommissionPlan\CommissionPlanReaderInterface as CommissionPlanReader;
use Ubix\Repository\CommissionPlan\CommissionPlanSqlRepository;
use Ubix\Repository\Deal\DealReaderInterface as DealReader;
use Ubix\Repository\Deal\DealSqlRepository;
use Ubix\Repository\Message\MessageSqlRepository;
use Ubix\Repository\Message\MessageWriterInterface as MessageWriter;
use Ubix\Repository\PendingPlatformUser\PendingPlatformUserReaderInterface as PendingPlatformUserReader;
use Ubix\Repository\PendingPlatformUser\PendingPlatformUserSqlRepository;
use Ubix\Repository\Performer\PerformerReaderInterface as PerformerReader;
use Ubix\Repository\Performer\PerformerSqlRepository;
use Ubix\Repository\PerformerStatistics\PerformerStatisticsReaderInterface as PerformerStatisticsReader;
use Ubix\Repository\PerformerStatistics\PerformerStatisticsSqlRepository;
use Ubix\Repository\PlatformUser\PlatformUserReaderInterface as PlatformUserReader;
use Ubix\Repository\PlatformUser\PlatformUserSqlRepository;
use Ubix\Repository\PlatformUser\PlatformUserWriterInterface as PlatformUserWriter;
use Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationSqlRepository;
use Ubix\Repository\PlatformUserAgeVerification\PlatformUserAgeVerificationWriterInterface as PlatformUserAgeVerificationWriter;
use Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptSqlRepository;
use Ubix\Repository\PlatformUserLoginAttempt\PlatformUserLoginAttemptWriterInterface as PlatformUserLoginAttemptWriter;
use Ubix\Repository\PlatformUserPayment\PlatformUserPaymentReaderInterface as PlatformUserPaymentReader;
use Ubix\Repository\PlatformUserPayment\PlatformUserPaymentSqlRepository;
use Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountReaderInterface as PlatformUserVelocityCountReader;
use Ubix\Repository\PlatformUserVelocityCount\PlatformUserVelocityCountSqlRepository;
use Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelReaderInterface as PlatformWhiteLabelReader;
use Ubix\Repository\PlatformWhiteLabel\PlatformWhiteLabelSqlRepository;
use Ubix\Repository\ScreenName\ScreenNameReaderInterface as ScreenNameReader;
use Ubix\Repository\ScreenName\ScreenNameSqlRepository;
use Ubix\Repository\User\UserReaderInterface as UserReader;
use Ubix\Repository\User\UserSqlRepository;
use Ubix\Service\FilterService;
use Ubix\Service\Geolocation\GeolocationServiceInterface as GeolocationService;
use Ubix\Service\Geolocation\UbixGeolocationService;
use Ubix\Service\Sql\MysqlPdoSqlService;
use Ubix\Service\Sql\SqlServiceInterface as SqlService;
use Ubix\Service\XvtService;
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


    $container->addDefinitions([

        Engine::class                            => autowire()->method('setAutoRefresh', true)->method('setTempDirectory', $cacheDir)->method('setLoader', new FileLoader($templateDir . $theme)),
        FilterService::class                     => autowire()->constructorParameter('bearerToken', getenv('VSM_FILTER_API_BEARER_TOKEN_BROADCASTING')),
        GeolocationService::class                => autowire(UbixGeolocationService::class)->constructorParameter('apiProtocolAndHostname', getenv('VSM_GEOLOCATION_API_PROTOCOL_AND_HOSTNAME')),
        HttpClient::class                        => autowire(CurlHttpClient::class),
        Logger::class                            => autowire(MonologLogger::class)->constructorParameter('name', $appName)->constructorParameter('handlers', [new StreamHandler(getenv('LOGGER_PATH') . '/' . strtolower(getenv('ENV')) . '/' . $appName . '.log', Level::fromName($logLevel), true, 0777)])->constructorParameter('processors', [new UidProcessor()]),
        MessageWriter::class                     => autowire(MessageSqlRepository::class),
        PerformerReader::class                   => autowire(PerformerSqlRepository::class),
        PerformerStatisticsReader::class         => autowire(PerformerStatisticsSqlRepository::class),
        PlatformUserAgeVerificationWriter::class => autowire(PlatformUserAgeVerificationSqlRepository::class),
        PlatformUserLoginAttemptWriter::class    => autowire(PlatformUserLoginAttemptSqlRepository::class),
        PlatformUserPaymentReader::class         => autowire(PlatformUserPaymentSqlRepository::class),
        PlatformUserReader::class                => autowire(PlatformUserSqlRepository::class),
        PlatformUserVelocityCountReader::class   => autowire(PlatformUserVelocityCountSqlRepository::class),
        PlatformUserWriter::class                => autowire(PlatformUserSqlRepository::class),
        PlatformWhiteLabelReader::class          => autowire(PlatformWhiteLabelSqlRepository::class),
        Psr17Factory::class                      => autowire(Psr17Factory::class),
        RequestFactory::class                    => get(Psr17Factory::class),
        ResponseFactory::class                   => autowire(SlimResponseFactory::class),
        ScreenNameReader::class                  => autowire(ScreenNameSqlRepository::class),
        UserReader::class                        => autowire(UserSqlRepository::class),
        SessionAuthenticationMiddleware::class   => autowire()->constructorParameter('excludedRoutes', [['method' => 'POST', 'path' => '/auth'], ['method' => 'OPTIONS', 'path' => '/auth']]),
        SessionHandler::class                    => autowire(SimpleCacheLegacySessionHandler::class),
        SimpleCache::class                       => autowire(MemcachedLegacySimpleCache::class)->constructorParameter('servers', $memcacheServers),
        SqlService::class                        => autowire(MysqlPdoSqlService::class),
        StreamFactory::class                     => get(Psr17Factory::class),
        AffiliateReader::class                   => autowire(AffiliateSqlRepository::class),
        AffiliateWriter::class                   => autowire(AffiliateSqlRepository::class),
        AttributionLogReader::class              => autowire(AttributionLogSqlRepository::class),
        AttributionLogWriter::class              => autowire(AttributionLogSqlRepository::class),
        BillingTransactionReader::class          => autowire(BillingTransactionSqlRepository::class),
        DealReader::class                        => autowire(DealSqlRepository::class),
        CommissionPlanReader::class              => autowire(CommissionPlanSqlRepository::class),
        CorsMiddleware::class                    => autowire()->constructorParameter('allowedOrigins', [
            '127.0.0.1',
            'localhost',
            '.ubixsys.com',
            '.sowing.me',
        ]),
    ]);

    return $container->build();
};
