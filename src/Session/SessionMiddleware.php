<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(protected SessionConfig $sessionConfig)
    {
    }


    /** @inheritdoc */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $options = $this->sessionConfig->getOptions();
        $options['cookie_secure'] = $request->getUri()->getScheme() === 'https' ? 1 : 0;

        // start session
        session_start($options);

        $destroyed = $_SESSION['destroyed'] ?? null;
        if (is_int($destroyed) && $destroyed + 300 < time()) {
            throw new \RuntimeException();
        }

        $response = $handler->handle($request);

        if ($response->hasHeader('SESSION_REGEN')) {
            $response = $response->withoutHeader('SESSION_REGEN');

            $_SESSION['destroyed'] = time();
            session_regenerate_id();
            unset($_SESSION['destroyed']);
        }

        // end session
        session_write_close();

        return $response;
    }
}
