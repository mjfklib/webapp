<?php

declare(strict_types=1);

namespace mjfklib\WebApp;

use mjfklib\Utils\ArrayValue;
use mjfklib\Container\Env;

class WebAppConfig
{
    public const WEBAPP_BASE_PATH = 'WEBAPP_BASE_PATH';
    public const WEBAPP_DISPLAY_ERROR_DETAILS = 'WEBAPP_DISPLAY_ERROR_DETAILS';
    public const WEBAPP_LOG_ERRORS = 'WEBAPP_LOG_ERRORS';
    public const WEBAPP_LOG_ERROR_DETAILS = 'WEBAPP_LOG_ERROR_DETAILS';


    /**
     * @param Env $env
     * @return self
     */
    public static function create(Env $env): self
    {
        $values = $env->getArrayCopy();
        return new self(
            basePath: ArrayValue::getStringNull($values, static::WEBAPP_BASE_PATH) ?? '',
            displayErrorDetails: ArrayValue::getBoolNull($values, static::WEBAPP_DISPLAY_ERROR_DETAILS) ?? false,
            logErrors: ArrayValue::getBoolNull($values, static::WEBAPP_LOG_ERRORS) ?? true,
            logErrorDetails: ArrayValue::getBoolNull($values, static::WEBAPP_LOG_ERROR_DETAILS) ?? true
        );
    }


    /**
     * @param string $basePath
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     */
    public function __construct(
        public string $basePath = '',
        public bool $displayErrorDetails = false,
        public bool $logErrors = true,
        public bool $logErrorDetails = true
    ) {
    }
}
