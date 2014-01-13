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
	$array_info = \Spyc::YAMLLoad($config_file);
	// \Spyc::YAMLDump($array, 4, 60)

	if ($array_info->templating->default == 'haml')
	  //installer haml
	elseif ($array_info->templating->default == 'smarty')
	  //installer smarty
	else
	  //installer twig

	if ($array_info->database->language == 'MySQL')
	{
	  //installer MySQL
	  $dbname = $array_info->database->name;
	  $dbpwd = $array_info->database->password;
	}

      }
      else
      {
	$content = '
database:
    language:  "MySQL"
    name: "project"
    password: ""

templating:
    default: "haml"
';
	file_put_contents($config_file, $content, FILE_APPEND);
      }
	
        //configuration here
    }
}

