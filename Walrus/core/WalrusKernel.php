<?php
/**
 * Author: Walrus Team
 * "Created: 16:10 13/12/13
 */

namespace Walrus\core\Kernel;

use Walrus\core\Route\WalrusRoute;

class WalrusKernel
{

    public static function execute()
    {

        self::bootstrap();
        new WalrusRoute();
    }

    private static function bootstrap()
    {

        //configuration here
    }
}