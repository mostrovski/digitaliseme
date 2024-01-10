<?php

namespace Digitaliseme\Core\Routing;

use Digitaliseme\Core\Http\Request;

class Router
{
    /**
     * @var array<string,Route>
     */
    protected array $routes = [];

    /**
     * @param Route[] $routes
     */
    public function __construct(array $routes) {
        foreach ($routes as $route) {
            $this->routes[$route->key()] = $route;
        }
    }

    public function match(Request $request): ?Route
    {
        $uri = $request->is('/') ? '/' : trim($request->uri(), '/');
        $directMatch = $this->routes[$request->method().'|'.$uri] ?? null;

        if ($directMatch instanceof Route) {
            return $directMatch;
        }

        $segments = explode('/', $uri);
        $candidates = $this->getCandidates(count($segments), $request->method());

        if (empty($candidates)) {
            return null;
        }

        foreach ($candidates as $route) {
            if ($this->routeMatch($route, $segments)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @return Route[]
     */
    protected function getCandidates(int $segmentCount, string $method): array
    {
        return array_filter(
            array_values($this->routes),
            static fn ($route) => (
                ($route->segmentCount() === $segmentCount) && ($route->method() === $method)
            ),
        );
    }

    /**
     * @param string[] $segments
     */
    protected function routeMatch(Route $route, array $segments): false|Route
    {
        $routeSegments = $route->segments();
        $params = [];

        foreach ($routeSegments as $index => $routeSegment) {
            $isParameter = (bool) preg_match('/^{\w+}$/', $routeSegment);

            if ($isParameter) {
                $params[] = $segments[$index];
                continue;
            }

            if ($routeSegment !== $segments[$index]) {
                return false;
            }
        }

        $route->setParams($params);

        return $route;
    }
}
