<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Ubix\Service\JsonService;
use Ubix\Service\Vault\VaultCredentialResolverService;
use Ubix\Service\Vault\VaultService;

/**
 * uBix Vault bootstrap hook.
 *
 * Resolves the app's database credentials from uBix Vault when `VAULT_ADDR` is
 * set, injecting them into the environment before the SQL layer reads them.
 * A no-op otherwise, so local development keeps using the git-ignored `.env`.
 *
 * `require`d and invoked by both entry points (`bin/ubix`, `public/index.php`)
 * immediately after Dotenv loads. Kept as a returned closure (mirroring
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

    $bootstrapper = new VaultCredentialResolverService(
        $logger,
        new VaultService($logger, new Client(), new JsonService($logger)),
    );

    $bootstrapper->hydrateEnvironment(trim($vaultAddress));
};
