<?php
/**
 * Author: Walrus Team
 * "Created: 16:10 13/12/13
 */

namespace Walrus\core\Kernel;

use Walrus\core\route;

class WalrusKernel
{

    public static function execute()
    {

        self::bootstrap();

        $mux = new route\Route();

        $mux->add('/product', array('HelloController','doHelloWorld'));
        $mux->add('/product/:id', array('HelloController','doHelloWorld'), array(
            'require' => array('id' => '\d+', ),
            'default' => array( 'id' => '1', )
        ));
        $route = $mux->dispatch('/product/1');
        route\Executor::execute($route);
    }

    private static function bootstrap()
    {
      $config_file = "../../config/config.yml";
      if (exists($config_file))
      {
	$array_info = yaml_parse_file($config_file);
      }
      else
      {
	$content = "";
	file_put_contents($config_file, $content, FILE_APPEND);
      }
	
        //configuration here
    }
}
