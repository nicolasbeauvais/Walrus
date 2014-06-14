<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 07:42 11/03/14
 */

namespace Test;

use PHPUnit_Framework_TestCase;
use Walrus\core\WalrusRouter;

/**
 * Class WalrusRouterTest
 * @package Test
 */
class WalrusRouterTest extends PHPUnit_Framework_TestCase
{


    /**
     * @return WalrusRouter
     */
    public function testInstance ()
    {
        $router = WalrusRouter::getInstance();

        return $router;
    }

    /**
     * @depends testInstance
     */
    public function testAddTestRoute(WalrusRouter $router)
    {
        $routes = array(
            array(
                'path' => 'hello',
                'action' => 'HelloController:run',
                'params' => array(
                    'name' => '_home',
                    'method' => 'GET'
                )
            ),
            array(
                'path' => 'test/:param1/:param2/:param3/(:param4)',
                'action' => 'HelloController:run',
                'acl' => 'admin',
                'params' => array(
                    'name' => '_test',
                    'filters' => array(
                        'require' => array(
                            'param1' => '\d+',
                            'param2' => '[A-Z]',
                            'param3' => '\w{4}',
                            'param4' => '\w+'
                        ),
                        'default' => array(
                            'param4' => 'hello'
                        )
                    )
                )
            ),
        );

        // route creation
        foreach ($routes as $route) {
            $router->map($route['path'], $route['action'], $route['params']);
        }

        // get the created route object
        foreach ($routes as $route) {
            $Route = $router->generate($route['params']['name']);

            $this->assertArrayHasKey('url', $Route);
            $this->assertEquals($route['path'] . '/', $Route['url']);
            $this->assertArrayHasKey('route', $Route);
        }
    }
}
