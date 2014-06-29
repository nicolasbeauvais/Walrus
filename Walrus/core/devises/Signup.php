<?php

/**
 * Walrus Framework
 */

namespace Walrus\core\devises;


class Signup
{
    public static $options;

    public static function getRoutes($options)
    {
        self::$options = $options;
        $routes = array();

        $path = str_replace('/','',$options['path']);

        $routes['_getSignup'] = array(
            'controller' => 'WalrusSignupController',
            'action' => 'getSignup',
            'path' => $path,
            'method' => 'GET'
        );

        $routes['_postSignup'] = array(
            'controller' => 'WalrusSignupController',
            'action' => 'postSignup',
            'path' => $path,
            'method' => 'POST'
        );

        return $routes;
    }
}