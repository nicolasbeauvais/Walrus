<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 21:00 15/01/14
 */

namespace Walrus\core;

class WalrusAutoload
{

    private static $classesPath = array(
        'Walrus/controllers/',
        'Walrus/models/',
        'Walrus/core/',
        'Walrus/core/entity',
        'engine/controllers/',
        'engine/models/',
        'vendors/'
    );

    /**
     * Constructor
     * Constant contain my full path to Model, View, Controllers and Lobrary-
     * Direcories.
     *
     */
    public function __construct()
    {
        spl_autoload_register(array($this,'autoload'));
    }

    private function autoload($class_with_namespace)
    {
        $path = ROOT_PATH . str_replace('\\', '/', $class_with_namespace) . '.php';

        if (file_exists($path)) {
            require_once($path);
            return true;
        } else {

            $vendors_path = ROOT_PATH . 'vendors/' . str_replace('\\', '/', $class_with_namespace) . '.php';

            if (file_exists($vendors_path)) {
                require_once($vendors_path);
                return true;
            }

            if (strrpos($class_with_namespace, "\\")) {
                $exploded_class = explode('\\', $class_with_namespace);
                $class_name = array_pop($exploded_class);
            } else {
                $class_name = $class_with_namespace;
            }

            foreach (self::$classesPath as $classPath) {

                if (file_exists(ROOT_PATH . $classPath . $class_name . '.php')) {
                    require_once(ROOT_PATH . $classPath . $class_name . '.php');
                    return true;
                }
            }
        }
        return false;
    }

    public static function getNamespace($class)
    {
        $vendors_path = ROOT_PATH . 'vendors/' . str_replace('\\', '/', $class) . '.php';

        if (file_exists($vendors_path)) {
            require_once($vendors_path);
            return true;
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
