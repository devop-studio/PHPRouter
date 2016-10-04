<?php

namespace Millennium\Router\Interfaces;

interface IRouterInterface
{
    /**
     * @param string $url
     * @param array  $routes
     */
    public function findRoute($url, $routes);

    /**
     * @param array $parameters
     * @param array $routes
     * @param array $params
     *
     * @throws Exceptions\RouteNotFoundException
     *
     * @return array
     */
    public function checkRouter($parameters, $routes, $params = []);

    /**
     * @param array $router
     *
     * @throws Exceptions\MethodNotAllowedException
     * @throws Exceptions\AccessDeniedException
     *
     * @return array
     */
    public function validateRouter($router);
}
