<?php

/**
 * Walrus Framework
 */

namespace Walrus\core\devises;

use Walrus\core\devises\Signup;
use Walrus\core\devises\Signin;

class WalrusDevises
{
    static public function bootsrap()
    {
        $devises = $_ENV['W']['devises'];

        foreach($devises as $name => $devise)
        {
            if($devise['actif'])
            {
                $class = 'Walrus\core\devises\\'.ucwords($name);
                $_ENV['W']['routes'] = array_merge($class::getRoutes($devise), $_ENV['W']['routes']);
            }
        }
    }
}