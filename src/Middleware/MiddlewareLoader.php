<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Middleware;

use Slim\App;

interface MiddlewareLoader
{
    /**
     * @param App $app
     * @param MiddlewareFactory $middlewareFactory
     * @return void
     */
    public function load(
        App $app,
        MiddlewareFactory $middlewareFactory
    ): void;
}
