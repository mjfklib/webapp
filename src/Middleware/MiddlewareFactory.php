<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Middleware;

use Psr\Http\Server\MiddlewareInterface;

class MiddlewareFactory
{
    /**
     * @param (\Closure(class-string<MiddlewareInterface> $className):MiddlewareInterface) $factory
     */
    public function __construct(protected \Closure $factory)
    {
    }


    /**
     * @param class-string<MiddlewareInterface> $className
     * @return MiddlewareInterface
     */
    public function create(string $className): MiddlewareInterface
    {
        return ($this->factory)($className);
    }
}
