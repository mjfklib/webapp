<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Error;

use mjfklib\WebApp\WebAppConfig;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Slim\App;

class ErrorMiddleware extends \Slim\Middleware\ErrorMiddleware implements LoggerAwareInterface
{
    use LoggerAwareTrait;


    /**
     * @param WebAppConfig $config
     * @param App $app
     * @param ErrorHandler $errorHandler
     */
    public function __construct(
        WebAppConfig $config,
        App $app,
        ErrorHandler $errorHandler
    ) {
        parent::__construct(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            $config->displayErrorDetails,
            $config->logErrors,
            $config->logErrorDetails
        );
        $this->setDefaultErrorHandler($errorHandler);
    }
}
