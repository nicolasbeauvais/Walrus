<?php

namespace Walrus\core\route;

use Exception;
use ReflectionClass;

class Executor
{
    /*
     * $route: {pcre flag}, {pattern}, {callback}, {options} 
     */
    public static function execute($route)
    {
        $cb = $route[2]; /* get callback */

        // create the reflection class
        try {
            $rc = new ReflectionClass('engine\controllers\\' . $cb[0]);
        } catch (Exception $e) {
            echo 'Exception: ',  $e->getMessage(), "\n";
            return 0;
        }

        $args = null;

        // if the first argument is a class name string,
        // then create the controller object.
        if (is_string($cb[0])) {
            $cb[0] = $controller = $args ? $rc->newInstanceArgs($args) : $rc->newInstance();
        } else {
            $controller = $cb[0];
        }

        // check controller action method
        if ($controller && ! method_exists($controller, $cb[1])) {
            throw new Exception('Controller exception');
            /*
            throw new Exception('Method ' . 
                get_class($controller) . "->{$cb[1]} does not exist.", $route );
             */
        }

        $rps = $rc->getMethod($cb[1])->getParameters();

        // XXX:

        $vars = isset($route[3]['vars'])
                ? $route[3]['vars']
                : array()
                ;

        $arguments = array();

        foreach ($rps as $param) {
            $n = $param->getName();

            if (isset($vars[ $n ])) {
                $arguments[] = $vars[ $n ];

            } elseif (isset($route['default'][ $n ]) && $default = $route['default'][ $n ]) {
                $arguments[] = $default;

            } elseif (!$param->isOptional() && ! $param->allowsNull()) {
                throw new Exception('parameter is not defined.');
            }
        }

        return call_user_func_array($cb, $arguments);
    }
}

