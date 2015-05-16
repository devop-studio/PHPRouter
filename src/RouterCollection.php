<?php

namespace Millennium;

use Symfony\Component\Yaml\Yaml;
use Millennium\Interfaces\IRouterCollection;

class RouterCollection implements IRouterCollection
{

    /**
     *
     * @var array $routes
     */
    private $routes = array();

    /**
     *
     * @var array $routerOptions
     */
    private $routerOptions = array(
        'router' => null,
        'methods' => array(),
        'defaults' => array(),
        'requires' => array(),
        'security' => array(),
        'items' => array()
    );

    /**
     * 
     * @param string $filename
     * 
     * @return array
     */
    public function collectRouters($filename)
    {
        $routes = $this->parseFiles($filename);
        foreach ($routes AS $name => $route)
        {
            $node = &$this->routes;
            $parameters = explode('/', mb_substr($route['path'], 1));
            do {
                $segment = array_shift($parameters);
                $parameter = $this->parseRoutesParameter($segment, $route);
                if (!isset($node[$parameter])) {
                    $node[$parameter] = $this->prepareRouter($segment, $name, $node, $route, empty($parameters));
                }
                $node = &$node[$parameter]['items'];
            } while ($parameters);
        }
        return $this->routes;
    }

    /**
     * 
     * @param strig $filename
     * @param array $routes
     * @param array $options
     * 
     * @return array
     */
    private function parseFiles($filename, $routes = array(), $options = array())
    {
        foreach (Yaml::parse($filename) AS $name => $route)
        {
            if (isset($route['import'])) {
                return $this->parseFiles($route['import'], $routes, $route);
            } else {
                if (isset($options['path'])) {
                    $route['path'] = $options['path'] . $route['path'];
                }
                if (isset($options['methods']) && !isset($route['methods'])) {
                    $route['methods'] = $options['methods'];
                }
                if (isset($options['security']) && !isset($route['security'])) {
                    $route['security'] = $options['security'];
                }
                $routes[$name] = $route;
            }
        }
        return $routes;
    }

    /**
     * 
     * @param string $parameter
     * @param string $router
     * @param array $parent
     * @param array $route
     * 
     * @return array
     */
    private function prepareRouter($parameter, $router, $parent, $route, $isLast)
    {
        $options = array();
        $segment = mb_substr($parameter, 0, 1) === ":" ? mb_substr($parameter, 1) : $parameter;
        if ($isLast) {
            $options['action'] = $route['action'];
        }
        if (mb_substr($parameter, 0, 1) === ":") {
            $options['name'] = $segment;
        }
        foreach ($this->routerOptions AS $name => $option)
        {
            if ($name === 'router') {
                $options['router'] = $router;
            } else if (isset($route[$name])) {
                $options[$name] = isset($route[$name][$segment]) ? $route[$name][$segment] : $route[$name];
            } else if (isset($parent[$name])) {
                $options[$name] = isset($parent[$name][$segment]) ? $route[$name][$segment] : $parent[$name];
            } else {
                $options[$name] = $option;
            }
        }
        return $options;
    }

    /**
     * 
     * @param strig $parameter
     * @param array $route
     * 
     * @return string
     */
    private function parseRoutesParameter($parameter, $route)
    {
        if (mb_substr($parameter, 0, 1) === ":") {
            $require = mb_substr($parameter, 1);
            if (isset($route['requires']) && isset($route['requires'][$require]) && !is_array($route['requires'][$require])) {
                return $route['requires'][$require];
            } else {
                return "*";
            }
        }
        return $parameter;
    }

}
