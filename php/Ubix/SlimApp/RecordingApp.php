<?php // phpcs:disable

declare(strict_types=1);

namespace Ubix\SlimApp;

use Psr\Log\LoggerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface as Logger;
use Slim\App;
use Slim\Handlers\ErrorHandler;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Psr7\Factory\ResponseFactory;

final class RecordingApp extends App
{
    /** @var array<int, array<string, mixed>> */
    private array $added = [];

    /**
     * Constructor
     *
     * @param Logger $logger Logger
     */
    public function __construct(
        protected Logger $logger, // @phpstan-ignore property.onlyWritten (Logger is a required dependency of most VSM classes but has not been implemented in this class yet)
    ) {
        // Build with Slim PSR-7's ResponseFactory (commonly installed).
        parent::__construct(new ResponseFactory());
    }

    /** @param callable|string|MiddlewareInterface|array $middleware */
    public function add($middleware): App
    {
        $this->added[] = $this->describe($middleware, 'add');
        return parent::add($middleware);
    }

    public function addMiddleware(MiddlewareInterface $middleware): App
    {
        $this->added[] = $this->describe($middleware, 'addMiddleware');
        return parent::addMiddleware($middleware);
    }

    public function addRoutingMiddleware(): RoutingMiddleware
    {
        $this->added[] = ['kind' => 'routing'];
        return parent::addRoutingMiddleware();
    }

    public function addBodyParsingMiddleware(array $bodyParsers = []): BodyParsingMiddleware
    {
        $this->added[] = ['kind' => 'body-parsing'];
        return parent::addBodyParsingMiddleware($bodyParsers);
    }

    public function addErrorMiddleware(
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ): ErrorMiddleware {
        $em = parent::addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails, $logger ?? $logger);

        // ✅ Ensure a DefaultErrorHandler instance exists so Middleware.php can call registerErrorRenderer() safely.
        // Some setups keep it null until explicitly set.
        if (method_exists($em, 'getDefaultErrorHandler') && $em->getDefaultErrorHandler() === null) {
            $em->setDefaultErrorHandler(
                new ErrorHandler($this->getCallableResolver(), $this->getResponseFactory(), $logger ?? $logger)
            );
        } else {
            // Touch it to trigger lazy init on versions that create it on first access
            $em->getDefaultErrorHandler();
        }

        $this->added[] = [
            'kind'   => 'error',
            'params' => [
                'displayErrorDetails' => $displayErrorDetails,
                'logErrors'           => $logErrors,
                'logErrorDetails'     => $logErrorDetails,
                'logger'              => get_class($logger ?? $logger),
            ],
        ];
        return $em;
    }

    /** @return array<int, array<string, mixed>> */
    public function getRecorded(): array
    {
        return $this->added;
    }

    /** @param mixed $mw */
    private function describe($mw, string $via): array
    {
        // Normalize the many ways middleware can be added
        if (is_string($mw)) {
            return ['kind' => 'custom', 'via' => $via, 'target' => $mw, 'target_type' => 'fqcn-string'];
        }
        if (is_array($mw) && count($mw) === 2) {
            $class = is_object($mw[0]) ? get_class($mw[0]) : (string) $mw[0];
            return ['kind' => 'custom', 'via' => $via, 'target' => $class . '::' . (string) $mw[1], 'target_type' => 'array-callable'];
        }
        if (is_object($mw)) {
            return ['kind' => 'custom', 'via' => $via, 'target' => get_class($mw), 'target_type' => 'object'];
        }
        if (is_callable($mw)) {
            return ['kind' => 'custom', 'via' => $via, 'target' => 'closure', 'target_type' => 'callable'];
        }
        return ['kind' => 'custom', 'via' => $via, 'target' => gettype($mw), 'target_type' => 'unknown'];
    }
}

