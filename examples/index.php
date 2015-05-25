<?php

include_once '../vendor/autoload.php';

use Millennium\Router;
use Millennium\RouterCollection;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(file_get_contents("./config/config.yml"));

$collection = new RouterCollection();
$routers = $collection->collectRouters($config['router']);

$router = new Router();

$urls = array(
    '/',
    '/admin/users',
    '/admin/users/5/edit',
    '/user/5/view',
    '/not-found'
);

foreach ($urls AS $url) {
    try {
        $route = $router->findRoute($url, $routers);
        echo "{$url} found route <b>{$route['router']['action']}</b><br />";
    } catch (\Exception $e) {
        echo "{$url} => ".$e->getMessage()."<hr/>";
    }
}