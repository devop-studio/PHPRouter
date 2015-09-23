<?php

namespace Millennium\Interfaces;

interface IRouterInterface
{

    /**
     * 
     * @param string $url
     * @param array $routes
     */
    public function findRoute($url, $routes);

    /**
     * 
     * @param array $parameters
     * @param array $routes
     * @param array $params
     * 
     * @return array
     * 
     * @throws Exceptions\RouteNotFoundException
     */
    public function checkRouter($parameters, $routes, $params = array());

    /**
     * 
     * @param array $router
     * 
     * @return array
     * 
     * @throws Exceptions\MethodNotAllowedException
     * @throws Exceptions\AccessDeniedException
     */
    public function validateRouter($router);
}
