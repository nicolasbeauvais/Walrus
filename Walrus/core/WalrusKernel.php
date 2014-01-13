<?php
/**
 * Author: Walrus Team
 * "Created: 16:10 13/12/13
 */

namespace Walrus\core;

use Walrus\core\route\Route as WalrusRoute;

class WalrusKernel
{

    public static function execute()
    {

        self::bootstrap();
        WalrusRoute::makeRoutes();
    }

    private static function bootstrap()
    {

        //configuration here
    }
}

