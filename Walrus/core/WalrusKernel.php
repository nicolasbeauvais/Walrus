<?php
/**
 * Author: Walrus Team
 * "Created: 16:10 13/12/13
 */

namespace Walrus\core\Kernel;

use Walrus\core\route\Mux;
use Walrus\core\route\Executor;

class WalrusKernel
{

    public static function execute()
    {

        self::bootstrap();

        $mux = new Mux\Mux();

        $mux->add('/product', array('ProductController','listAction'));
        $mux->add('/product/:id', array('ProductController','itemAction'), array(
            'require' => array('id' => '\d+', ),
            'default' => array( 'id' => '1', )
        ));
        $route = $mux->dispatch('/product/1');
        Executor\Executor::execute($route);
    }

    private static function bootstrap()
    {

        //configuration here
    }
}
