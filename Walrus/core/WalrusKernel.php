<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:10 13/12/13
 */

namespace Walrus\core;

use ActiveRecord\Config;
use Walrus\core\route;
use Spyc\Spyc;
use Exception;

class WalrusKernel
{

    /**
     * Main Kernel function, start config and routing.
     */
    public static function execute()
    {
        self::bootstrap();
        try {
            $WalrusRouter = WalrusRouter::getInstance();
            $WalrusRouter->execute();
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
        // Initializing php-activerecord
        Config::initialize(function ($cfg) {
            $cfg->set_model_directory(ROOT_PATH . 'Walrus/models/');
            $cfg->set_connections(
                array(
                    'development' => 'mysql://root:root@localhost/walrus_dev'
                )
            );
        });
    }

    private static function bootstrapConfig()
    {
        $config_file = "../config/config.yml";

        if (file_exists($config_file)) {

            $array_info = Spyc::YAMLLoad($config_file);
            $error = false;
            $errorArray = array();
            $WalrusConfig = array();

            $templating = array('haml', 'twig', 'smarty');
            $databases = array('mysql', 'sqlite', 'postgresql', 'oracle');
            $environment = array('dev', 'prod');

            if (in_array(strtolower($array_info['templating']), $templating)) {
                $WalrusConfig['templating'] = $array_info['templating'];
            } else {
                $error = true;
                $errorArray['templating'] = "Templating must be HAML, Twig or Smarty";
            }

            if (in_array(strtolower($array_info['database']['language']), $databases)) {
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
            if (in_array(strtolower($array_info['environment']), $environment)) {
                $WalrusConfig['environment'] = $array_info['environment'];
            } else {
                $error = true;
                $errorArray['environment'] = "Environment must be dev or prod";
            }

            if ($error == true) {
                throw new Exception($errorArray);
            }

            global $WalrusConfig;

        } else {
            $info_content = "
            #Please feed those information : \n
            #database can be MySQL | SQLite | PostgreSQL | Oracle \n
            #templating can be HAML | Twig | Smarty \n
            #environment can be dev or prod \n
            #config and routing are yml files \n \n";

            $php_content = array(
                "database" => array("language" => "",
                    "host" => "",
                    "name" => "",
                    "password" => ""),
                "templating" => "",
                "environment" => ""
            );

            $yml_content = Spyc::YAMLDump($php_content);

            file_put_contents($config_file, $info_content.$yml_content, FILE_APPEND);

        }
    }

}
