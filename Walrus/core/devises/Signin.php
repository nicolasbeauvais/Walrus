<?php

/**
 * Walrus Framework
 */

namespace Walrus\core\devises;


class Signin
{
    public static function getRoutes($options)
    {
        $routes = array();

        $path = str_replace('/','',$options['path']);

        $routes['_getSignin'] = array(
            'controller' => 'WalrusSigninController',
            'action' => 'getSignin',
            'path' => $path,
            'method' => 'GET'
        );

        $routes['_postSignin'] = array(
            'controller' => 'WalrusSigninController',
            'action' => 'postSignin',
            'path' => $path,
            'method' => 'POST'
        );

        return $routes;
    }
}