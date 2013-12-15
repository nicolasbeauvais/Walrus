<?php
/**
 * Author: Walrus Team
 * "Created: 16:10 13/12/13
 */

namespace Walrus\core\Route;

class WalrusRoute
{
    private static $route;
    private static $route_with_params;
    private static $params;

    private static $instance;

    private function __construct()
    {

    }

    public static function singleton()
    {
        if (!isset(self::$instance)) {
            $obj = __CLASS__;
            self::route();
            self::$instance = new $obj;
        }

        return self::$instance;
    }

    private static function route ()
    {

        //walrus.com/controller/method/param1/value1/param2/value2/
        if (!isset($_GET['url'])) {
            self::$route = '';
            self::$route_with_params = '';
            self::$params = '';
            return;
        }

        $route = explode('/', rtrim($_GET['url'], "/"));
        $count = count($route);

        if ($count < 2) {
            trigger_error('Unavailable url', E_USER_ERROR);
            return;
        }

        $controller = $route[0];
        $method = $route[1];

        if ($count > 2) {
            $params = array();
            $count -= 1;
            unset($route[0]);
            unset($route[1]);

            for ($count; $count > 2; $count -= 2) {
                $params[(string)$route[$count - 1]] = $route[$count];
            }
        }

        var_dump($controller, $method, isset($params) ? $params : 'no params');
    }

    public function getRoute()
    {
        return self::$route;
    }

    public function getParams()
    {
        return isset(self::$params) && !empty(self::$params) ? self::$params : false;
    }

    public function getRouteWithParams()
    {
        return isset(self::$route_with_params) && !empty(self::$route_with_params) ? self::$route_with_params : false;
    }

    public function __clone()
    {
        trigger_error('Cloning the Walrus Route class is not permitted', E_USER_ERROR);
    }
}