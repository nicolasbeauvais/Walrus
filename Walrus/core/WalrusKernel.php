<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 16:10 13/12/13
 */

namespace Walrus\core;

use ActiveRecord\Config;
use Walrus\core\route;
use Exception;

class WalrusKernel
{

    /**
     * Main Kernel function, start config and routing.
     */
    public static function execute()
    {
        //self::bootstrap();
        try {
            $WalrusRoute = WalrusRouter::getInstance();
            $WalrusRoute->execute();
        } catch (Exception $e) {
            // @TODO: add Exception
        }

        WalrusFrontController::execute();
    }

    /**
     * Handle configuration.
     */
    private static function bootstrap()
    {
        WalrusKernel::bootstrapConfig();
        WalrusKernel::bootstrapOrm();
    }

    private static function bootstrapOrm()
    {

    }

    private static function bootstrapConfig()
    {
        $config_file = "../config/config.yml";

        if (file_exists($config_file)) {

            $array_info = \Spyc::YAMLLoad($config_file);
            // \Spyc::YAMLDump($array, 4, 60)
            $error = false;
            $errorArray = array();
            $WalrusConfig = array();

            if (strcasecmp($array_info['templating'], 'HAML') == 0 || strcasecmp($array_info['templating'], 'Twig') == 0 || strcasecmp($array_info['templating'], 'Smarty') == 0) {
                $WalrusConfig['templating'] = $array_info['templating'];
            } else {
                $error = true;
                $errorArray['templating'] = "Templating must be HAML, Twig or Smarty";
            }

            if (strcasecmp($array_info['database']['language'], 'MySQL') == 0 || strcasecmp($array_info['database']['language'], 'SQLite') == 0 || strcasecmp($array_info['database']['language'], 'PostgreSQL') == 0 || strcasecmp($array_info['database']['language'], 'Oracle') == 0) {
                $WalrusConfig['dbLanguage'] = $array_info['database']['language'];
            } else {
                $error = true;
                $errorArray['dbLanguage'] = "Database must be either MySQL, SQLite, PostgreSQL or Oracle";
            }

            if ($array_info['database']['host'] != "") {
                $WalrusConfig['dbHost'] = $array_info['database']['host'];
            } else {
                $error = true;
                $errorArray['dbHost'] = "Database host can't be empty (IP address)";
            }

            if ($array_info['database']['name'] != "") {
                $WalrusConfig['dbName'] = $array_info['database']['name'];
            } else {
                $error = true;
                $errorArray['dbName'] = "Database name can't be empty";
            }
            if (strcasecmp($array_info['environment'], 'dev') == 0 || strcasecmp($array_info['environment'], 'prod') == 0) {
                $WalrusConfig['environment'] = $array_info['environment'];
            } else {
                $error = true;
                $errorArray['environment'] = "Environment must be dev or prod";
            }

            if ($error == true) {
                throw new Exception($errorArray);
            }

            $GLOBALS['WalrusConfig']= $WalrusConfig;
            $GLOBALS['errorConfig']= $errorArray;

        } else {
            $info_content = "#Please feed those information : \n #database can be MySQL | SQLite | PostgreSQL | Oracle \n #templating can be HAML | Twig | Smarty \n #environment can be dev or prod \n #config and routing are yml files \n \n";
            $php_content = array(
                "database" => array("language" => "",
                    "host" => "",
                    "name" => "",
                    "password" => ""),
                "templating" => "",
                "environment" => ""                // dev or prod
            );

            $yml_content = \Spyc::YAMLDump($php_content);

            file_put_contents($config_file, $info_content.$yml_content, FILE_APPEND);
        }
    }

}
