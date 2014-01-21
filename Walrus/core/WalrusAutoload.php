<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 21:00 15/01/14
 */

namespace Walrus\core;

class WalrusAutoload
{

    private $classesPath = array(
        'Walrus/controllers/',
        'Walrus/models/',
        'Walrus/core/',
        'Walrus/core/route',
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
        } else {

            $vendors_path = ROOT_PATH . 'vendors/' . str_replace('\\', '/', $class_with_namespace) . '.php';

            if (file_exists($vendors_path)) {
                require_once($vendors_path);
                return;
            }

            if (strrpos($class_with_namespace, "\\")) {
                $exploded_class = explode('\\', $class_with_namespace);
                $class_name = array_pop($exploded_class);
            } else {
                $class_name = $class_with_namespace;
            }

            foreach ($this->classesPath as $classPath) {

                if (file_exists(ROOT_PATH . $classPath . $class_name . '.php')) {
                    require_once(ROOT_PATH . $classPath . $class_name . '.php');
                    return;
                }
            }
        }
    }
}
