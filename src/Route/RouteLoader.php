<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Route;

use mjfklib\WebApp\WebAppConfig;
use Slim\App;

interface RouteLoader
{
    /**
     * @param WebAppConfig $config
     * @param App $app
     * @return void
     */
    public function load(
        WebAppConfig $config,
        App $app
    ): void;
}
