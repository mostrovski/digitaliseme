<?php

namespace Digitaliseme\Core\Http;

use Digitaliseme\Core\Application;
use Digitaliseme\Core\Contracts\Middleware;
use Digitaliseme\Core\Contracts\Response;
use Digitaliseme\Core\Routing\Route;
use RuntimeException;
use Throwable;

class Http
{
    protected Response $response;

    public function __construct(
        protected Application $app,
    ) {}

    public function handleRequest(): static
    {
        $request = $this->walkThroughMiddleware(Request::resolve(), $this->app->middleware());

        if (! $request instanceof Request) {
            return $this;
        }

        $route = $this->app->router()->match($request);

        if (! $route instanceof Route) {
            return $this->setResponse(redirectResponse('404'));
        }

        $request = $this->walkThroughMiddleware($request, $route->middleware());

        if (! $request instanceof Request) {
            return $this;
        }

        try {
            $response = call_user_func_array(
                [new ($route->controller()), $route->action()],
                $route->params()
            );

            if (! $response instanceof Response) {
                throw new RuntimeException('Controller action must return a Response');
            }
        } catch (Throwable $e) {
            logger()->error($e->getMessage());

            return $this->setResponse(redirectResponse('500'));
        }

        return $this->setResponse($response);
    }

    public function sendResponse(): void
    {
        $this->response()->send();
    }

    public function response(): Response
    {
        return $this->response;
    }

    /**
     * @param Middleware[] $layers
     */
    protected function walkThroughMiddleware(Request $request, array $layers): false|Request
    {
        $result = $request;

        foreach ($layers as $middleware) {
            $response = $middleware::handle($result);

            if ($response instanceof Response) {
                $this->setResponse($response);

                return false;
            }

            $result = $response;
        }

        return $result;
    }

    protected function setResponse(Response $response): static
    {
        $this->response = $response;

        return $this;
    }
}
