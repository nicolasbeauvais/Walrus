<?php
/**
 * Author: Walrus Team
 * Created: 16:10 13/12/13
 */

namespace Walrus\core\Route;

/**
 * Class WalrusRoute
 * @package Walrus\core\Route
 */
class WalrusRoute
{

    private static $route;
    private static $route_with_params;
    private static $params;
    private static $instance;

    private function __construct()
    {

    }

    /**
     * Basic Singleton.
     * @return mixed
     */
    public static function singleton ()
    {
        if (!isset(self::$instance)) {
            $obj = __CLASS__;
            self::route();
            self::$instance = new $obj;
        }

        return self::$instance;
    }


    /**
     * Main routing methode, called in the kernel.
     * Parse routing.yml to check the route and sanitize parameters
     */
    private static function route ()
    {
        //@TODO: yaml stuff
        //walrus.com/controller/method/arg/param1/value1/param2/value2/

        if (!isset($_GET['url'])) {
            self::$route = '';
            self::$route_with_params = '';
            self::$params = array();
            return;
        }

        $route = explode('/', rtrim($_GET['url'], "/"));
        $count = count($route);

        $controller = $route[0];

        $params = array();
        if ($count < 2) {
            return;
        }

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

    /**
     * Return the current route as a string (without GET parameters).
     * @return string
     */
    public function getRoute()
    {
        return self::$route;
    }

    /**
     * Return the currents params as an array
     * @return array
     */
    public function getParams()
    {
        return self::$params;
    }

    /**
     * Return the current route with GET parameters.
     * @return string
     */
    public function getRouteWithParams()
    {
        return isset(self::$route_with_params) && !empty(self::$route_with_params) ? self::$route_with_params : false;
    }

    /**
     * Prevent cloning.
     */
    public function __clone()
    {
        trigger_error('Cloning the Walrus Route class is not permitted', E_USER_ERROR);
    }
}
