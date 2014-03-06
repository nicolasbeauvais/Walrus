<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:10 13/12/13
 */

namespace Walrus\core;

use Walrus\core\route;
use Spyc\Spyc;
use R;
use Exception;

/**
 * Class WalrusKernel
 * @package Walrus\core
 */
class WalrusKernel
{

    /**
     * Main Kernel function, start config and routing.
     */
    public static function execute()
    {
        if (self::bootstrap()) {
            try {
                $WalrusRouter = WalrusRouter::getInstance();
                $WalrusRouter->execute();
            } catch (Exception $e) {
                // @TODO: add Exception
            }
        }
        WalrusFrontController::execute();
    }

    /**
     * Handle configuration.
     */
    private static function bootstrap()
    {
        if ($config = WalrusKernel::bootstrapConfig()) {
            WalrusKernel::bootstrapOrm();
        }

        new WalrusMonitoring();

        return $config;
    }

    /**
     * Initialisation of the RedBean orm.
     */
    private static function bootstrapOrm()
    {
        R::setup(
            $_ENV['W']['RDBMS'] . ':host=' . $_ENV['W']['host'] . ';dbname=' . $_ENV['W']['database'],
            $_ENV['W']['name'],
            $_ENV['W']['password']
        );

        if ($_ENV['W']['environment'] == "production") {
            R::freeze(true);
        }
    }

    /**
     * Verify Walrus configuration and set it.
     */
    private static function bootstrapConfig()
    {
        $config_file = "../config/config.yml";

        if (file_exists($config_file)) {
            $configs = Spyc::YAMLLoad($config_file);

            foreach ($configs as $key => $option) {
                $_ENV['W'][$key] = $option;
            }
            return true;
        } else {
            $_ENV['W']['templating'] = 'php';
            WalrusRouter::reroute('config', 'config');
            return false;
        }
    }
}
