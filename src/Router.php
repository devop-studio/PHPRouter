<?php

namespace Millennium;

use Millennium\Interfaces\IRouter;

class Router implements IRouter
{

    /**
     * 
     * @param string $url
     * @param array $routes
     * 
     * @return array
     */
    public function findRoute($url, $routes)
    {
        $parameters = explode('/', mb_substr($url, 1));
        $router = $this->checkRouter($parameters, $routes);
        return $this->validateRouter($router['router']);
    }

    /**
     * 
     * @param array $parameters
     * @param array $routes
     * @param array $params
     * 
     * @return type
     * 
     * @throws Exceptions\RouteNotFoundException
     */
    public function checkRouter($parameters, $routes, $params = array())
    {

        $segment = array_shift($parameters);

        $router = null;

        if (isset($routes[$segment])) { // exact match
            $router = $routes[$segment];
        } else if (isset($routes['digit']) && ctype_digit($segment)) { // match only number tokens
            $router = $routes['digit'];
        } else if (isset($routes['alpha']) && ctype_alpha($segment)) {
            $router = $routes['alpha'];
        } else if (isset($routes['alnum']) && ctype_alnum($segment)) {
            $router = $routes['alnum'];
        } else if (isset($routes['*'])) { // if requires only few values
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
            return array('router' => $router, 'parameters' => $params);
        }

        throw new Exceptions\RouteNotFoundException;
    }

    /**
     * 
     * @param array $router
     * 
     * @return array
     * 
     * @throws Exceptions\MethodNotAllowedException
     * @throws Exceptions\AccessDeniedException
     */
    public function validateRouter($router)
    {
        if (!empty($router['methods']) && !in_array(filter_input(INPUT_SERVER, 'REQUEST_METHOD'), $router['methods'])) {
            throw new Exceptions\MethodNotAllowedException;
        }
        if (!empty($router['security'])) {
            if (isset($router['security']['ip'])) {
                if (!in_array(filter_input(INPUT_SERVER, 'REMOTE_ADDR'), $router['security']['ip'])) {
                    throw new Exceptions\AccessDeniedException;
                }
            }
        }
        return $router;
    }

}
