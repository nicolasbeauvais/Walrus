<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 16:10 13/12/13
 */

namespace Walrus\core;

use MtHaml\Exception;

class WalrusKernel
{

    public static function execute()
    {
        //self::bootstrap();
        try {
            $WalrusRoute = new WalrusRouter();
            $WalrusRoute->execute();
        } catch (Exception $e) {

        }
    }

    private static function bootstrap()
    {
        $config_file = "../config/config.yml";

        if (file_exists($config_file)) {

            $array_info = \Spyc::YAMLLoad($config_file);
            // \Spyc::YAMLDump($array, 4, 60)

            if ($array_info['templating']['default'] == 'haml') {
                //installer haml
            } elseif ($array_info['templating']['default'] == 'smarty') {
                //installer smarty
            } else {
                //installer twig
            }

            if ($array_info['database']['language'] == 'MySQL') {
                //installer MySQL
                $dbname = $array_info['database']['name'];
                $dbpwd = $array_info['database']['password'];
            } else {
                // autre db ?
            }
        } else {
            $php_content = array(
                "database" => array("language" => "MySQL",
                                    "name" => "project",
                                    "password" => ""),
                "templating" => array("default" => "haml")
            );

            $yml_content = \Spyc::YAMLDump($php_content);

            file_put_contents($config_file, $yml_content, FILE_APPEND);
        }

        //configuration here
    }
}

