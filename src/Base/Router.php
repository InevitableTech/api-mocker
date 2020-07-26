<?php

namespace Genesis\Api\Mocker\Base;

/**
 * Ability to do partial regex.
 * Ability to exactly match.
 *
 * Router class.
 */
class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = include __DIR__ . '/routing.php';
    }

    /**
     * Does dynamic route exist?
     * Match based on exact first on static
     * Match based on regex then on static.
     * 
     * If none found, return the EndpointProvider for further digging.
     *
     * @return string
     * @param  mixed  $url
     * @param  mixed  $routes
     * @param  mixed  $server
     * @param  mixed  $request
     */
    public static function getControllerForUrl($url, $routes, $request): string
    {
        $server = $request->getServerParams();

        try {
            $path = $request->getUri()->getPath() ?? '/';
            $method = strtolower($request->getMethod());
            $contents = (new FileStorage())->get(EndpointProvider::endpoint($path));

            if (isset($contents[$method])) {
                foreach ($contents[$method] as $response) {
                    if ($response['with'] === null) {
                        return EndpointProvider::class;
                    } elseif (preg_match($response['with'], (string) $request->getUri())) {
                        return EndpointProvider::class;
                    }
                }
            }
        } catch (\Exception $e) {
            // If failed, continue with static checks.
        }

        // Check for a static match definition.
        if (isset($routes[$url])) {
            return $routes[$url]['controller'];
        }

        // Preg match with the definitions.
        foreach ($routes as $route => $controller) {
            if (preg_match('|^'. preg_quote($route) . '$|i', $url)) {
                return $controller['controller'];
            }
        }

        // If all matches have failed and a catch all is defined, use that controller.
        if (isset($routes['*'])) {
            return $routes['*']['controller'];
        }

        return EndpointProvider::class;
    }
}
