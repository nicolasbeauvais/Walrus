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

