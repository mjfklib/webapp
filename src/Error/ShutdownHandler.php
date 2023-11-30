<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Error;

use mjfklib\WebApp\WebAppConfig;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\ResponseEmitter;

class ShutdownHandler
{
    /**
     * @param WebAppConfig $config
     * @param ServerRequestInterface $request
     * @param ErrorHandler $errorHandler
     */
    public function __construct(
        protected WebAppConfig $config,
        protected ServerRequestInterface $request,
        protected ErrorHandler $errorHandler
    ) {
    }


    /**
     * @return void
     */
    public function register(): void
    {
        register_shutdown_function($this);
    }


    /**
     * @return void
     */
    public function __invoke(): void
    {
        $error = error_get_last();
        if (!is_array($error)) {
            return;
        }

        $errorFile = $error['file'];
        $errorLine = $error['line'];
        $errorMessage = $error['message'];
        $errorType = $error['type'];
        $message = 'An error while processing your request. Please try again later.';

        if ($this->config->displayErrorDetails) {
            switch ($errorType) {
                case E_USER_ERROR:
                    $message = "FATAL ERROR: {$errorMessage}. ";
                    $message .= " on line {$errorLine} in file {$errorFile}.";
                    break;

                case E_USER_WARNING:
                    $message = "WARNING: {$errorMessage}";
                    break;

                case E_USER_NOTICE:
                    $message = "NOTICE: {$errorMessage}";
                    break;

                default:
                    $message = "ERROR: {$errorMessage}";
                    $message .= " on line {$errorLine} in file {$errorFile}.";
                    break;
            }
        }

        $exception = new HttpInternalServerErrorException($this->request, $message);
        $response = $this->errorHandler->__invoke(
            $this->request,
            $exception,
            $this->config->displayErrorDetails,
            $this->config->logErrors,
            $this->config->logErrorDetails
        );

        if (ob_get_length() !== false) {
            ob_clean();
        }

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }
}
