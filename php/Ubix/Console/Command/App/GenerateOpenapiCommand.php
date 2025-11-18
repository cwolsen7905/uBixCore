<?php

declare(strict_types=1);

namespace Ubix\Console\Command\App;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface as Logger;
use RuntimeException;
use Slim\Factory\AppFactory;
use Slim\Routing\Route;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as Output;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Ubix\Console\Command\AbstractCommand as Command;
use Ubix\SlimApp\RecordingApp;

/**
 * Command to generate an OpenAPI file
 *
 * @see \Ubix\Tests\Console\Command\App\GenerateOpenapiCommandTest PHPUnit test case
 */
final class GenerateOpenapiCommand extends Command
{
    private const APPS_PATH = __DIR__ . '/../../../../../app';

    private string $appsPath;

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        private Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        parent::__construct($logger);

        //
        //  Set the apps path
        //
        $appsPath = realpath(self::APPS_PATH);
        if ($appsPath === false) {
            throw new Exception('The apps folder was not found at `' . self::APPS_PATH . '`');
        } else {
            $this->appsPath = $appsPath;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Generate an OpenAPI file')
            ->addOption('app', null, InputOption::VALUE_REQUIRED, 'The app to generate an OpenAPI document for');
    }

    /**
     * {@inheritDoc}
     */
    protected function interact(Input $input, Output $output): void
    {
        $app = $input->getOption('app');
        if ($app === '' || $app === false || $app === null) {
            $io = new SymfonyStyle($input, $output);
            $io->error('No app was specified with the `--app` option');
            exit(SymfonyCommand::FAILURE);
        } elseif (!file_exists($this->appsPath . '/' . $app)) {
            $io = new SymfonyStyle($input, $output);
            $io->error('The `' . $app . '` app was not found in the `' . $this->appsPath . '` folder');
            exit(SymfonyCommand::FAILURE);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(Input $input, Output $output): int // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter -- $input is required but not used
    {
        //
        //  Load the app option
        //
        $appName = $input->getOption('app');
        assert(is_string($appName));

        //
        //  Generate metadata about the app
        //
        $hosts      = $this->getHosts($appName);
        $middleware = $this->getMiddleware($appName);
        $routes     = $this->getRoutes($appName);

        $usesBearerTokenAuthentication = true; // TODO: make this dynamic by reading Middleware.php

        //
        //  Output the OpenAPI document
        //
        $output->writeln('openapi: 3.1.0');
        $output->write(PHP_EOL);
        $output->writeln('info:');
        $output->writeln('  title: ' . $appName);
        $output->writeln('  version: 0.' . (new DateTime())->format('Ymd') . '.' . (new DateTime())->format('U'));
        $output->write(PHP_EOL);
        $output->writeln('servers:');
        foreach ($hosts as $environment => $host) {
            $output->writeln('  - url: https://' . $host);
            $output->writeln('    description: ' . $environment);
        }
        $output->write(PHP_EOL);
        if ($usesBearerTokenAuthentication) {
            $output->writeln('components:');
            $output->writeln('  securitySchemes:');
            $output->writeln('    bearerAuth:');
            $output->writeln('      type: http');
            $output->writeln('      scheme: bearer');
            $output->writeln('      bearerFormat: JWT');
            $output->write(PHP_EOL);
            $output->writeln('security:');
            $output->writeln('  - bearerAuth: []');
            $output->write(PHP_EOL);
        }
        $output->writeln('paths:');
        foreach ($routes as $position => $route) {
            if ($position > 0) {
                $output->write(PHP_EOL);
            }

            $output->writeln('  ' . $route['pattern'] . ':');
            foreach ($route['methods'] as $method) {
                $output->writeln('    ' . strtolower($method) . ':');
                $output->writeln('      summary: ' . $route['pattern']);
                $output->writeln('      responses:');
                $output->writeln('        \'200\':');
                $output->writeln('          description: Successful response');
                $output->writeln('        \'403\':');
                $output->writeln('          description: Forbidden');
            }
        }
        $output->write(PHP_EOL);

        //
        //  Return success
        //
        return SymfonyCommand::SUCCESS;
    }

    private function getHosts(string $appName): array
    {
        $hosts = [];

        foreach (glob($this->appsPath . '/' . $appName . '/*-ingress.yaml') ?: [] as $file) {
            $environment = substr($file, strrpos($file, '/') + 1, -strlen('-ingress.yaml'));
            if ($environment === 'main' || $environment === 'master') {
                $environment = 'prod';
            }

            $data = Yaml::parseFile( // TODO: should this be in a new service called YamlService?
                $file,
                Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_DATETIME
            );
            $host = $data['spec']['rules'][0]['host'] ?? null;
            if ($host !== null) {
                $hosts[$environment] = $host;
            }
        }

        return $hosts;
    }

    private function getMiddleware(string $appName): array
    {
        return []; // TEMPORARY: disable middleware recording until we fix the RecordingApp class

        $app = new RecordingApp();
        
        $middlewareFile = $this->appsPath . '/' . $appName . '/src/Middleware.php';
        var_dump($middlewareFile);
        $middleware     = require $middlewareFile;
        if (!is_callable($middleware)) {
            throw new RuntimeException('Middleware file did not return a callable: ' . $middlewareFile);
        }
        $middleware($app);

        return $app->getRecorded();
    }

    private function getRoutes(string $appName): array
    {
        $app = AppFactory::create();
        
        $routesFile = $this->appsPath . '/' . $appName . '/src/Routes.php';
        $routes     = require $routesFile;
        if (!is_callable($routes)) {
            throw new RuntimeException('Routes file did not return a callable: ' . $routesFile);
        }
        $routes($app);

        $routes = [];
        foreach ($app->getRouteCollector()->getRoutes() as $route) {
            /**
             * @var Route $route
             */
            $pattern = $route->getPattern();

            // Skip your fallback 404 route (pattern '/{routes:.*}')
            if ($pattern === '/{routes:.*}') { // TODO: move this magic string into a class constant
                continue;
            }

            $methods  = $route->getMethods();
            $callable = $route->getCallable(); // often 'Fully\Qualified\Class:method' string in your setup

            $controller = null;
            $action     = null;
            if (is_string($callable) && str_contains($callable, ':')) {
                [$controller, $action] = explode(':', $callable, 2);
            }

            $routes[] = [
                'methods'    => $methods,    // e.g. ['GET']
                'pattern'    => $pattern,    // e.g. '/user/{userId:[0-9]+}[/{sitekey:[a-zA-Z0-9]+}]'
                'controller' => $controller, // e.g. 'Ubix\Controller\FanClubApi\PlatformUserController'
                'action'     => $action,     // e.g. 'get'
                'callable'   => $callable,   // original callable string/value for completeness
            ];
        }
        return $routes;
    }
}
