<?php

namespace Millennium\Router;

use Millennium\Router\Interfaces\IRouterCollectionInterface;
use Symfony\Component\Yaml\Yaml;

class RouterCollection implements IRouterCollectionInterface
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var array
     */
    private $routerOptions = [
        'router'   => null,
        'methods'  => [],
        'defaults' => [],
        'requires' => [],
        'security' => [],
        'items'    => [],
    ];

    /**
     * @param string
     *
     * @return array
     */
    public function collectRouters($filename)
    {
        $routes = $this->parseFiles(stream_resolve_include_path($filename));
        foreach ($routes as $name => $route) {
            $node = &$this->routes;
            $parameters = explode('/', mb_substr($route['path'], 1));
            do {
                $segment = array_shift($parameters);
                $parameter = $this->parseRoutesParameter($segment, $route);
                if (!isset($node[$parameter])) {
                    $node[$parameter] = $this->prepareRouter($segment, $name, $node, $route, empty($parameters));
                }
                $node = &$node[$parameter]['items'];
            } while (!empty($parameters));
        }

        return $this->routes;
    }

    /**
     * @param string $file
     * @param array  $routes
     * @param array  $options
     *
     * @return array
     */
    private function parseFiles($file, $routes = [], $options = [])
    {
        $filename = stream_resolve_include_path($file);
        if (file_exists($filename)) {
            foreach (Yaml::parse(file_get_contents($filename)) as $name => $route) {
                if (isset($route['import'])) {
                    return $this->parseFiles($route['import'], $routes, $route);
                } else {
                    if (isset($options['path'])) {
                        $route['path'] = $options['path'].$route['path'];
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
        }

        return $routes;
    }

    /**
     * @param string $parameter
     * @param string $router
     * @param array  $parent
     * @param array  $route
     *
     * @return array
     */
    private function prepareRouter($parameter, $router, $parent, $route, $isLast)
    {
        $options = [];
        $segment = mb_substr($parameter, 0, 1) === ':' ? mb_substr($parameter, 1) : $parameter;
        if ($isLast) {
            $options['action'] = $route['action'];
        }
        if (mb_substr($parameter, 0, 1) === ':') {
            $options['name'] = $segment;
        }
        foreach ($this->routerOptions as $name => $option) {
            if ($name === 'router') {
                $options['router'] = $router;
            } elseif (isset($route[$name])) {
                $options[$name] = isset($route[$name][$segment]) ? $route[$name][$segment] : $route[$name];
            } elseif (isset($parent[$name])) {
                $options[$name] = isset($parent[$name][$segment]) ? $route[$name][$segment] : $parent[$name];
            } else {
                $options[$name] = $option;
            }
        }

        return $options;
    }

    /**
     * @param string
     * @param array $route
     *
     * @return string
     */
    private function parseRoutesParameter($parameter, $route)
    {
        if (mb_substr($parameter, 0, 1) === ':') {
            $require = mb_substr($parameter, 1);
            if (isset($route['requires']) && isset($route['requires'][$require]) && !is_array($route['requires'][$require])) {
                return $route['requires'][$require];
            } else {
                return '*';
            }
        }

        return $parameter;
    }
}
