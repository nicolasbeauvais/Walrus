<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 21:00 15/01/14
 */

namespace Walrus\core;

class WalrusAutoload
{

    /**
     * Contain all directory to watch for autoload.
     */
    private static $classesPath = array(
        'Walrus/controllers/',
        'Walrus/models/',
        'Walrus/core/',
        'Walrus/core/class',
        'engine/controllers/',
        'engine/models/',
        'vendors/',
        'vendors/Smarty/',
        "database/php-activerecord/lib/"
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
     * @param $class_with_namespace
     * @return bool
     */
    private function autoload($class_with_namespace)
    {
        $path = ROOT_PATH . str_replace('\\', '/', $class_with_namespace) . '.php';

        if (file_exists($path)) {
            include_once($path);
            return true;
        } else {

            if (!strrpos($class_with_namespace, "\\")) {
                $class_with_namespace = $class_with_namespace . '\\' . $class_with_namespace;
            }

            $vendors_path = ROOT_PATH . 'vendors/' . str_replace('\\', '/', $class_with_namespace) . '.php';

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
     * @param $class
     * @return bool|mixed
     */
    public static function getNamespace($class)
    {
        $vendors_path = ROOT_PATH . 'vendors/' . $class . '/' . str_replace('\\', '/', $class) . '.php';

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

            if (file_exists(ROOT_PATH . $classPath . $class_name . '.php')) {
                return str_replace('/', '\\', $classPath . $class_name);
            }
        }
    }
}
