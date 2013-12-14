<?php
/**
 * Author: Walrus Team
 * "Created: 16:10 13/12/13
 */

namespace Walrus\core\Route;

class WalrusRoute
{
    private $route_current;
    private $route_params;

    public function __construct()
    {
        if (!isset(self::$instance)) {
            self::route();
            $obj = __CLASS__;
            self::$instance = new $obj;
        }

        return self::$instance;
    }

    private static function route ()
    {

        //walrus.com/controller/method/param1/value1/param2/value2/

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

        var_dump($controller, $method, $params);
    }

    public function getRoute()
    {
        return $this->route_current;
    }

    public function getParams()
    {
        if (!isset($this->route_params)) {
            throw new \Exception("No parameters set");
        }

        return $this->route_params;
    }

    private static $instance;

    public function __clone()
    {
        trigger_error('Cloning the Walrus Route class is not permitted', E_USER_ERROR);
    }
}