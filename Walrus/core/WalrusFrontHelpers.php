<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 19:05 17/03/14
 */

namespace Walrus\core;

use Walrus\core\WalrusException;

/**
 * Class WalrusRoute
 * @package Walrus\core
 */
class WalrusFrontHelpers
{
    /**
     * Helpers namespace.
     */
    private static $helpersNamespace = 'Walrus\core\helpers\\';

    /**
     * Register all Helpers to load.
     *
     * A helper name should pass this regex: [A-Za-z_]+
     */
    private static $helpers = array(
        'Url'
    );

    /**
     * Store all helpers once they are instantiated
     */
    private static $helpersInstances = array();

    /**
     * The WalrusFrontHelpers unique instance for singleton.
     * @var WalrusRouter
     */
    protected static $instance;

    /**
     * Private construct to prevent multiples instances
     */
    protected function __construct()
    {
    }

    /**
     * Private clone to prevent multiples instances
     */
    protected function __clone()
    {
    }

    /**
     * Main function to call to get an instance of WalrusFrontHelpers.
     * @return WalrusRouter
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
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
     * Create each helper object.
     */
    private static function create()
    {
        foreach (self::$helpers as $helper) {
            self::$helpersInstances[$helper] = self::instanceClass($helper);
        }
    }

    /**
     * Instantiate a helper class with the helpers namespace.
     *
     * @param $helper
     * @return mixed
     */
    private static function instanceClass($helper)
    {
        $classWithNamespace = self::$helpersNamespace . $helper;
        return new $classWithNamespace();
    }

    /**
     * Add a helper to the WalrusFrontHelper class;
     *
     * @param $class the class name of the helper
     *
     * @throws WalrusException if a helper with this $class name already exist
     */
    private static function registerHelper($class)
    {
        if (!in_array($class, self::$helpers)) {
            self::$helpers[] = $class;
        } else {
            throw new WalrusException('A Walrus helper with the name ' . $class . ' already exist');
        }

    }
}
