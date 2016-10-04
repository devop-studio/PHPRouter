<?php

namespace Millennium\Router;

use Millennium\Router\Interfaces\IRouterInterface;

class Router implements IRouterInterface
{
    /**
     * @param string $url
     * @param array  $routes
     *
     * @return array
     */
    public function findRoute($url, $routes)
    {
        $parameters = explode('/', mb_substr($url, 1));
        $router = $this->checkRouter($parameters, $routes);

        return $this->validateRouter($router);
    }

    /**
     * @param array $parameters
     * @param array $routes
     * @param array $params
     *
     * @throws Exceptions\RouteNotFoundException
     *
     * @return array
     */
    public function checkRouter($parameters, $routes, $params = [])
    {
        $segment = array_shift($parameters);

        $router = null;

        if (isset($routes[$segment])) { // exact match
            $router = $routes[$segment];
        } elseif (isset($routes['digit']) && ctype_digit($segment)) { // match only number tokens
            $router = $routes['digit'];
        } elseif (isset($routes['alpha']) && ctype_alpha($segment)) {
            $router = $routes['alpha'];
        } elseif (isset($routes['alnum']) && ctype_alnum($segment)) {
            $router = $routes['alnum'];
        } elseif (isset($routes['*'])) { // if requires only few values
            if (isset($routes['*']['requires']) && is_array($routes['*']['requires'])) {
                if (in_array($segment, $routes['*']['requires'])) {
                    $router = $routes['*'];
                }
            }
        }

        if (isset($router['name'])) {
            $params[$router['name']] = $segment;
        }

        if (!empty($parameters) && !empty($router['items'])) {
            return $this->checkRouter($parameters, $router['items'], $params);
        }

        if (!empty($router)) {
            return ['router' => $router, 'parameters' => $params];
        }

        throw new Exceptions\RouteNotFoundException();
    }

    /**
     * @param array $router
     *
     * @throws Exceptions\MethodNotAllowedException
     * @throws Exceptions\AccessDeniedException
     *
     * @return array
     */
    public function validateRouter($router)
    {
        if (!empty($router['router']['methods']) && !in_array(filter_input(INPUT_SERVER, 'REQUEST_METHOD'), $router['methods'])) {
            throw new Exceptions\MethodNotAllowedException();
        }
        if (!empty($router['router']['security'])) {
            if (isset($router['router']['security']['ip'])) {
                if (!in_array(filter_input(INPUT_SERVER, 'REMOTE_ADDR'), $router['router']['security']['ip'])) {
                    throw new Exceptions\AccessDeniedException();
                }
            }
        }

        return $router;
    }
}
