<?php

/**
 * Walrus Framework
 */

namespace Walrus\core\devises;


class Signin
{
    public static $options;

    public static function getRoutes($options)
    {
        self::$options = $options;
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

    public static function filter($route)
    {

        $signinDevise = $_ENV['W']['devises']['signin'];
        $sessionKey = $signinDevise['filter']['session_key'];
        $exceptions = $signinDevise['filter']['exceptions'];

        $exceptions[] = '_getSignin';
        $exceptions[] = '_postSignin';
        $exceptions[] = '_getSignup';
        $exceptions[] = '_postSignup';

        if($signinDevise['filter']['actif'])
        {
            if(!in_array($route->getName(), $exceptions))
            {
                if(!isset($_SESSION[$sessionKey]))
                {
                    header('Location: ' . $signinDevise['path']);
                    die();
                }
            }
        }
    }
}