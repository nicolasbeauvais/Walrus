<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 16:10 13/12/13
 */

namespace Walrus\core;

use Walrus\core\route;
use Walrus\core\WalrusException;
use Spyc\Spyc;
use R;

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
            } catch (WalrusException $exception) {
                $exception->handle();
            }
        }
        WalrusController::execute();
    }

    /**
     * Handle configuration.
     */
    private static function bootstrap()
    {
        $hasConfig = WalrusCompile::launch();

        if ($hasConfig) {
            WalrusKernel::bootstrapOrm();
        }

        new WalrusMonitoring();
        WalrusHelpers::initialise();

        return $hasConfig;
    }

    /**
     * Initialisation of the RedBean orm.
     */
    private static function bootstrapOrm()
    {
        if (empty($_ENV['W']['RDBMS']) || empty($_ENV['W']['host'])
            || empty($_ENV['W']['database']) || empty($_ENV['W']['name'])) {
            return;
        }

        R::setup(
            $_ENV['W']['RDBMS'] . ':host=' . $_ENV['W']['host'] . ';dbname=' . $_ENV['W']['database'],
            $_ENV['W']['name'],
            $_ENV['W']['password']
        );

        if ($_ENV['W']['environment'] == "production") {
            R::freeze(true);
        }
    }
}
