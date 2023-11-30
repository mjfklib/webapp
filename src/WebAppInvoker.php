<?php

declare(strict_types=1);

namespace mjfklib\WebApp;

use DI\Container;
use mjfklib\Container\ContainerFactory;
use mjfklib\Container\DefinitionSource;
use mjfklib\Container\Env;
use mjfklib\WebApp\Error\ShutdownHandler;
use mjfklib\WebApp\Middleware\MiddlewareLoader;
use mjfklib\WebApp\Route\RouteLoader;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

class WebAppInvoker
{
    /**
     * @param string|null $appDir
     * @param string|null $appNamespace
     * @param string|null $appName
     * @param string|null $appEnv
     */
    final public static function run(
        string|null $appDir = null,
        string|null $appNamespace = null,
        string|null $appName = null,
        string|null $appEnv = null,
    ): void {
        (new static())->runWebApp(
            $appDir,
            $appNamespace,
            $appName,
            $appEnv
        );
    }


    final public function __construct()
    {
    }


    /**
     * @param string|null $appDir
     * @param string|null $appNamespace
     * @param string|null $appName
     * @param string|null $appEnv
     * @return void
     */
    public function runWebApp(
        string|null $appDir = null,
        string|null $appNamespace = null,
        string|null $appName = null,
        string|null $appEnv = null,
    ): void {
        $env = $this->createEnv(
            $appDir,
            $appNamespace,
            $appName,
            $appEnv
        );

        $container = $this->createContainer($env);

        $this->invokeWebApp($env, $container);
    }


    /**
     * @return Env
     */
    protected function createEnv(
        string|null $appDir = null,
        string|null $appNamespace = null,
        string|null $appName = null,
        string|null $appEnv = null,
    ): Env {
        return new Env(
            $appDir,
            $appNamespace,
            $appName,
            $appEnv
        );
    }


    /**
     * @param Env $env
     * @return Container
     */
    protected function createContainer(Env $env): Container
    {
        $container = (new ContainerFactory($env))
            ->create([
                WebAppDefinitionSource::class
            ]);

        $container->set(WebAppInvoker::class, $this);

        return $container;
    }


    /**
     * @param Env $env
     * @param Container $container
     * @return void
     */
    protected function invokeWebApp(
        Env $env,
        Container $container
    ): void {
        chdir($env->appDir);

        $container->call([ShutdownHandler::class, 'register']);
        $container->call([MiddlewareLoader::class, 'load']);
        $container->call([RouteLoader::class, 'load']);
        $container->call([App::class, 'run'], [
            'request' => DefinitionSource::get(ServerRequestInterface::class)
        ]);
    }
}
