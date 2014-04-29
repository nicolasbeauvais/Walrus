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
    private static $helpersNamespace = 'Walrus\core\helpers\\';

    /**
     * Register all Helpers to load.
     *
     * A helper name should pass this regex: [A-Za-z_]+
     */
    private static $helpers = array(
        'Url' => array('class' => 'Url','type' => 0),
        'Form' => array('class' => 'Form', 'type' => 0),
        'Tag' => array('class' => 'Tag', 'type' => 1)
    );

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
     * Main function to call to get an instance of WalrusHelpers.
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

        if ((isset(self::$helpersInstances[$helper]) && $newInstance == false)
            && self::test(self::$helpers[$helper]['class'], 'back')) {
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
     * Test if a helper can be called by the function.
     *
     * @param int $helper can be 0, 1 or 2
     * @param string $frontOrBack can be Front or Back
     *
     * @throws WalrusException if the helper have a bad type
     *
     * @return bool
     */
    private static function test($helper, $frontOrBack)
    {
        $type = self::$helpers[$helper];

        $type = abs((int)$type);
        if ($type > 2) {
            throw new WalrusException('The Helper ' . $helper .' hasn\'t a correct type (0, 1 or 2)');
        }

        if ($frontOrBack == 'front') {
            return $type !== 1;
        } elseif ($frontOrBack == 'back') {
            return $type !== 2;
        } else {
            throw new WalrusException('$frontOrBack is set to a bad value, must be the string "front" or "back"');
        }
    }

    /**
     * Register a helper to the WalrusHelpers class.
     *
     * $type values:
     * 0: accessibility from backend and frontend
     * 1: accessibility from backend only (controllers/models)
     * 2: accessibility from frontend only (views/templates)
     *
     * @param string $class the class name of the helper
     * @param int $type security type
     *
     * @throws WalrusException if a helper with this $class name already exist
     */
    public static function registerHelper($class, $type = 0)
    {
        $type = abs((int)$type);
        if ($type > 2) {
            throw new WalrusException('A Walrus Helper must have a correct type: 0, 1 or 2');
        }

        if (!array_key_exists($class, self::$helpers)) {
            self::setHelper($class, $type);
        } else {
            throw new WalrusException('A Walrus helper with the name ' . $class . ' already exist');
        }
    }

    /**
     * Add a new Helper to the helpers array
     *
     * @param string $class the class name of the helper
     * @param int $type security type
     */
    private static function setHelper($class, $type)
    {
        self::$helpers[$class] = array('class' => $class, 'type' => $type);
    }
}
