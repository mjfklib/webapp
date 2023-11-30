<?php

declare(strict_types=1);

namespace mjfklib\WebApp;

use DI\Bridge\Slim\Bridge;
use mjfklib\Container\DefinitionSource;
use mjfklib\Container\Env;
use mjfklib\Logger\LoggerDefinitionSource;
use mjfklib\WebApp\Logger\WebLogProcessor;
use mjfklib\WebApp\Middleware\MiddlewareFactory;
use mjfklib\WebApp\Middleware\MiddlewareLoader;
use mjfklib\WebApp\Route\RouteLoader;
use mjfklib\WebApp\Session\SessionConfig;
use Monolog\Processor\ProcessorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;

class WebAppDefinitionSource extends DefinitionSource
{
    /**
     * @param Env $env
     * @return array<string,mixed>
     */
    protected function createDefinitions(Env $env): array
    {
        $definitions = [
            App::class => static::factory([Bridge::class, 'create']),
            MiddlewareFactory::class => static::factory(
                function (ContainerInterface $c): MiddlewareFactory {
                    return new MiddlewareFactory(
                        function (string $className) use ($c): MiddlewareInterface {
                            $middleware = $c->get($className);
                            return $middleware instanceof MiddlewareInterface
                                ? $middleware
                                : throw new \RuntimeException();
                        }
                    );
                }
            ),
            ProcessorInterface::class => static::get(WebLogProcessor::class),
            ServerRequestInterface::class => static::factory(
                fn () => (ServerRequestCreatorFactory::create())->createServerRequestFromGlobals()
            ),
            WebAppConfig::class => static::factory([WebAppConfig::class, 'create']),
            SessionConfig::class => static::factory([SessionConfig::class, 'create']),
        ];

        $middlewareLoader = $env->classRepo->getClasses(MiddlewareLoader::class);
        if (count($middlewareLoader) > 0) {
            $definitions[MiddlewareLoader::class] = static::get((reset($middlewareLoader))->getName());
        }

        $routeLoader = $env->classRepo->getClasses(RouteLoader::class);
        if (count($routeLoader) > 0) {
            $definitions[RouteLoader::class] = static::get((reset($routeLoader))->getName());
        }

        return $definitions;
    }


    /**
     * @inheritdoc
     */
    public function getSources(): array
    {
        return [
            LoggerDefinitionSource::class
        ];
    }
}
