<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Ubix\Service\JsonService;
use Ubix\Service\Vault\VaultCredentialResolverService;
use Ubix\Service\Vault\VaultService;
use Ubix\SimpleCache\ApcuSimpleCache;

/**
 * UBix Vault bootstrap hook.
 *
 * Resolves the app's database credentials from uBix Vault when `VAULT_ADDR` is
 * set, injecting them into the environment before the SQL layer reads them.
 * A no-op otherwise, so local development keeps using the git-ignored `.env`.
 *
 * `require`d and invoked by both entry points (`bin/ubix`, `public/index.php`)
 * immediately after Dotenv loads. Under PHP-FPM that is every request, so the
 * resolved values are kept in APCu when it is available (it is in the uBixCore
 * runtime images) for `VAULT_CACHE_TTL` seconds — a pool then logs in to the
 * vault a few times an hour rather than per request. Without APCu (e.g. the CLI,
 * where it is off by default) every run resolves afresh, as before. Kept as a returned closure (mirroring
 * Dependencies.php / Middleware.php / Routes.php) so the self-wiring lives
 * outside the DI container, which is not built until after this runs.
 */

return static function (): void {
    $vaultAddress = getenv('VAULT_ADDR');
    if (! is_string($vaultAddress) || trim($vaultAddress) === '') {
        return;
    }

    $logger = new MonologLogger('vault-bootstrap');
    $logger->pushHandler(new StreamHandler('php://stderr', Level::Warning));

    $cache = new ApcuSimpleCache($logger, 'ubixcore.');

    $bootstrapper = new VaultCredentialResolverService(
        $logger,
        new VaultService($logger, new Client(), new JsonService($logger)),
        $cache->isAvailable() ? $cache : null,
    );

    $bootstrapper->hydrateEnvironment(trim($vaultAddress));
};
