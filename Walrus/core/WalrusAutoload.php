<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 21:00 15/01/14
 */

namespace Walrus\core;

/**
 * Class WalrusAutoload
 * @package Walrus\core
 */
class WalrusAutoload
{

    /**
     * Contain all directory to watch for autoload.
     */
    private static $classesPath = array(
        'engine/controllers/',
        'engine/models/',
        'Walrus/controllers/',
        'Walrus/models/',
        'Walrus/core/',
        'Walrus/core/objects/',
        'vendor/',
    );

    /**
     * Class path for known vendors
     */
    private static $classesKnown = array(
        'R' => 'vendor/RedBean/rb'
    );

    /**
     * Set the autoload.
     */
    public function __construct()
    {
        spl_autoload_register(array($this,'autoload'));
    }

    /**
     * Autoload method.
     *
     * The autoload try to load the class with the given namespace as PSR-0 namespace
     * correspond to directory path. Else, the autoload try to load the class as a vendor.
     *
     * @param string $class_with_namespace
     * @return bool
     */
    private function autoload($class_with_namespace)
    {
        $path = $_ENV['W']['ROOT_PATH'] . str_replace('\\', '/', $class_with_namespace) . '.php';

        if (file_exists($path)) {
            include_once($path);
            return true;
        } else {

            if (array_key_exists($class_with_namespace, self::$classesKnown)) {
                include_once( $_ENV['W']['ROOT_PATH'] . self::$classesKnown[$class_with_namespace] . '.php');
                return true;
            }

            if (!strrpos($class_with_namespace, "\\")) {
                $class_with_namespace = $class_with_namespace . '\\' . $class_with_namespace;
            }

            $vendors_path = $_ENV['W']['ROOT_PATH'] . 'vendor/' . str_replace('\\', '/', $class_with_namespace) . '.php';

            if (file_exists($vendors_path)) {
                include_once($vendors_path);
                return true;
            }
        }
        return false;
    }

    /**
     * Return the full 'namespace path' of a given class in string format.
     *
     * The class MUST be in one of the directory path listed in the $classesPath attribute.
     *
     * @param string $class the class name
     * @return bool|string
     */
    public static function getNamespace($class)
    {
        $vendors_path = $_ENV['W']['ROOT_PATH'] . 'vendor/' . $class . '/' . str_replace('\\', '/', $class) . '.php';

        if (file_exists($vendors_path)) {
            return $class . '\\' . $class;
        }

        if (strrpos($class, "\\")) {
            $exploded_class = explode('\\', $class);
            $class_name = array_pop($exploded_class);
        } else {
            $class_name = $class;
        }

        foreach (self::$classesPath as $classPath) {

            if (file_exists($_ENV['W']['ROOT_PATH'] . $classPath . $class_name . '.php')) {
                return str_replace('/', '\\', $classPath . $class_name);
            }
        }

        return false;
    }
}
