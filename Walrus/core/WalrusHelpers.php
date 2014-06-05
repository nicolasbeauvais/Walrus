<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 19:05 17/03/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;

/**
 * Class WalrusHelpers
 * @package Walrus\core
 */
class WalrusHelpers
{
    /**
     * Helpers namespace.
     */
    private static $helpersNamespace = 'app\helpers\\';

    /**
     * Register all Helpers to load.
     *
     * A helper name should pass this regex: [A-Za-z_]+
     */
    private static $helpers = array();

    /**
     * Store all helpers once they are instantiated
     */
    private static $helpersInstances = array();

    /**
     * The WalrusHelpers unique instance for singleton.
     * @var WalrusRouter
     */
    protected static $instance;

    /**
     * Private construct to prevent multiples instances
     */
    private function __construct()
    {
    }

    /**
     * Private clone to prevent multiples instances
     */
    protected function __clone()
    {
    }

    public static function initialise()
    {
        self::$helpers = $_ENV['W']['HELPERS'];
    }

    /**
     * Return all created helpers
     */
    public static function execute()
    {
        self::create();
        return self::$helpersInstances;
    }

    /**
     * Return a helper class
     *
     * @param string $helper the wanted helper name
     * @param bool $newInstance return a new instance or previously instancied if exist
     *
     * @throws WalrusException in case the required helper doesn't exist
     *
     * @return helper instance
     */
    public static function getHelper($helper, $newInstance = true)
    {
        if (!array_key_exists($helper, self::$helpers)) {
            throw new WalrusException('The helper: ' . $helper . ' doesn\'t exist or not registered to WalrusHelpers');
        }

        if ((isset(self::$helpersInstances[$helper]) && $newInstance == false)) {
            return self::$helpersInstances[$helper];
        } else {
            $instance = self::instanceClass($helper);

            if (!isset(self::$helpersInstances[$helper]) || $newInstance) {
                self::$helpersInstances[$helper] = $instance;
            }
        }

        return self::instanceClass($helper);
    }

    /**
     * Instantiate a helper class with the helpers namespace.
     *
     * @param $helper
     * @return mixed
     */
    private static function instanceClass($helper)
    {
        $instance = self::$helpersNamespace . $helper;
        return new $instance();
    }

    /**
     * Return all helper with front-end type to use in WalrusController
     */
    public static function create()
    {
        foreach (self::$helpers as $helper) {
            if (isset(self::$helpersInstances[$helper['class']])) {
                continue;
            }
            self::$helpersInstances[$helper['class']] = self::instanceClass($helper['class']);
        }

        return self::$helpersInstances;
    }

    /**
     * Register a helper to the WalrusHelpers class.
     *
     * @param string $class the class name of the helper
     *
     * @throws WalrusException if a helper with this $class name already exist
     */
    public static function registerHelper($class)
    {
        if (!array_key_exists($class, self::$helpers)) {
            self::setHelper($class);
        } else {
            throw new WalrusException('A Walrus helper with the name ' . $class . ' already exist');
        }
    }

    /**
     * Add a new Helper to the helpers array
     *
     * @param string $class the class name of the helper
     */
    private static function setHelper($class)
    {
        self::$helpers[$class] = array('class' => $class);
    }
}
