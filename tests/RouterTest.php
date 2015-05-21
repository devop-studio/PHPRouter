<?php

use Millennium\Router;
use Millennium\RouterCollection;

class RouterTest extends \PHPUnit_Framework_TestCase
{

    private $router;
    private $collection;

    public function __construct()
    {
        $collectionRouter = new RouterCollection();
        $this->router = new Router();
        $this->collection = $collectionRouter->collectRouters('./tests/config/routes_user.yml');
    }

    public function testRouterMatch()
    {
        $result1 = $this->router->findRoute('/', $this->collection);
        $this->assertEquals('Namespace:Controller:action', $result1['router']['action']);

        $result2 = $this->router->findRoute('/user/5/edit', $this->collection);
        $this->assertEquals('Namespace:Controller:action', $result2['router']['action']);
        $this->assertEquals(array(
            'id' => 5,
            'action' => 'edit'
        ), $result2['parameters']);
    }

    public function testRouterException()
    {
        $this->setExpectedException('\Millennium\Exceptions\RouteNotFoundException', 'Route not found');
        $this->router->findRoute('/only-for-test/error404', $this->collection);
    }

}
