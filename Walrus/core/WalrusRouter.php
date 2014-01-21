<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 20:46 15/01/14
 */

namespace Walrus\core;

use Spyc\Spyc as Spyc;
use Pux\Mux as Mux;
use Pux\Executor as Executor;
use Exception;

/**
 * Class WalrusRoute
 * @package Walrus\core
 */
class WalrusRoute
{

    /**
     * Special Walrus router.
     * Make use of pux with YAML functionality.
     */
    public static function makeRoutes()
    {
        $url = isset($_GET['url']) ? '/' . rtrim($_GET['url'], "/") : '/';

        //Load YAML routes
        if (file_exists('../config/routes.yml')) {
            $routes = Spyc::YAMLLoad('../config/routes.yml');
        } else {
            throw new Exception("Can't find routes.yml in config directory");
        }

        //Transform YAML routes to valid Pux routes
        $walrusRoutes = new Mux();

        foreach ($routes as $route) {

            $method = isset($route['method']) ? strtolower($route['method']) : 'add';
            $path = isset($route['path']) ? $route['path'] : '';
            $controller = isset($route['controller']) ? $route['controller'] : '';
            $action = isset($route['action']) ? $route['action'] : '';
            $params = isset($route['params']) ? $route['params'] : array();

            $methodConstant = $walrusRoutes->getRequestMethodConstant($method);
            if ($methodConstant != 0) {
                $params['method'] = $methodConstant;
            }

            $walrusRoutes->add($path, array($controller, $action), $params);
        }

        //check current route
        $dispatched = $walrusRoutes->dispatch($url);

        // create the reflection class
        if (class_exists('engine\controllers\\' . $dispatched[2][0], true)) {
            $dispatched[2][0] = 'engine\controllers\\' . $dispatched[2][0];
        } elseif (class_exists('Walrus\controllers\\' . $dispatched[2][0], true)) {
            $dispatched[2][0] = 'Walrus\controllers\\' . $dispatched[2][0];
        } else {
            throw new Exception('Requested route doesn\'t exist');
        }

        //Execute route
        try {
            Executor::execute($dispatched);
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }
    }
}
