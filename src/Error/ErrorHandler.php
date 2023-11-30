<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Slim\Interfaces\ErrorHandlerInterface;

class ErrorHandler extends SlimErrorHandler implements ErrorHandlerInterface, LoggerAwareInterface
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';


    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct(
            $app->getCallableResolver(),
            $app->getResponseFactory()
        );
    }


    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


    /**
     * @return ResponseInterface
     */
    protected function respond(): ResponseInterface
    {
        $code = 500;
        $reason = 'Internal Server Error';

        if ($this->exception instanceof HttpException) {
            $code = $this->exception->getCode();
            $reason = substr($this->exception->getTitle(), 4);
        }

        /** @var string $payload */
        $payload = json_encode([
            'error' => [
                'code' => $code,
                'reason' => $reason
            ],
        ]);

        $response = $this->responseFactory
            ->createResponse($code, $reason)
            ->withHeader("Content-Type", "application/json");
        $response->getBody()->write($payload);

        return $response;
    }


    /**
     * Write to the error log if $logErrors has been set to true
     */
    protected function writeToErrorLog(): void
    {
        if ($this->logErrorDetails) {
            $this->logThrowable($this->exception);
        } else {
            $renderer = $this->callableResolver->resolve($this->logErrorRenderer);
            $error = $renderer($this->exception, $this->logErrorDetails);
            $this->logError($error);
        }
    }


    /**
     * @param \Throwable $t
     * @param string[]|null $seen
     * @return void
     */
    protected function logThrowable(
        \Throwable $t,
        ?array $seen = null
    ): void {
        $result = [];
        $starter = is_array($seen)
            ? 'Caused by: '
            : '';
        if ($seen === null) {
            $seen = [];
        }

        $this->logError(sprintf(
            '%s%s: %s',
            $starter,
            get_class($t),
            $t->getMessage()
        ));

        $file = $t->getFile();
        $line = $t->getLine();
        /** @var array<int,array<string,string>> $trace */
        $trace = $t->getTrace();
        $prev = $t->getPrevious();

        do {
            $current = "{$file}:{$line}";
            if (in_array($current, $seen, true)) {
                $result[] = sprintf(' ... %d more', count($trace) + 1);
                break;
            } else {
                $seen[] = $current;
            }

            $traceFile =  $trace[0]['file'] ?? 'Unknown Source';
            $traceLine = intval(isset($trace[0]['file']) ? ($trace[0]['line'] ?? 0) : 0);
            if ($traceLine < 1) {
                $traceLine = null;
            }
            $traceClass = $trace[0]['class'] ?? null;
            if (is_string($traceClass)) {
                $traceClass = str_replace('\\', '.', explode("@", $traceClass)[0]);
            }
            $traceFunction = $trace[0]['function'] ?? null;

            $this->logError(sprintf(
                ' at %s%s%s(%s%s%s)',
                $traceClass ?? '',
                is_string($traceClass) && is_string($traceFunction) ? '.' : '',
                str_replace('\\', '.', ($traceFunction ?? '(main)')),
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line
            ));

            $file = $traceFile;
            $line = $traceLine;
            array_shift($trace);
        } while (count($trace) > 0);

        if ($prev !== null) {
            $this->logThrowable($prev, $seen);
        }
    }
}
