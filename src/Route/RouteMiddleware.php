<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Route;

use Slim\App;

class RouteMiddleware extends \Slim\Middleware\RoutingMiddleware
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct(
            $app->getRouteResolver(),
            $app->getRouteCollector()->getRouteParser()
        );
    }
}
