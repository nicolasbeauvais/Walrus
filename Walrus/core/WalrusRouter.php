<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 20:46 15/01/14
 */

namespace Walrus\core;


use Spyc\Spyc as Spyc;
use Walrus\core\route\Router as Router;
use Exception;

/**
 * Class WalrusRoute
 * @package Walrus\core
 */
class WalrusRouter
{

    /**
     * Special Walrus router.
     * Make use of pux with YAML functionality.
     */
    public static function makeRoutes()
    {
        $router = new Router();

        $router->setBasePath('/');

        //load routes

        $route = $router->matchCurrentRequest();
    }
}
