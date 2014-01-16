<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais (E-Wok)
 * Created: 14:48 13/12/13
 */
session_start();

use Walrus\core\WalrusKernel as WalrusKernel;

//a bouger dans la config ?
define("APP_PATH", dirname(__FILE__) . '/');
define("ROOT_PATH", substr(dirname(__FILE__), 0, -4) . '/');

function __autoload ($called_class)
{
    if (strrpos($called_class, "\\")) {
        $exploded_class = explode('\\', $called_class);
        $class_name = array_pop($exploded_class);
    } else {
        $class_name = $called_class;
    }

    if (is_file(ROOT_PATH . 'Walrus/controllers/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/controllers/' . $class_name . '.php');

    } elseif (is_file(ROOT_PATH . 'Walrus/core/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/core/' . $class_name . '.php');

    } elseif (is_file(ROOT_PATH . 'Walrus/core/route/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/core/route/' . $class_name . '.php');

    } elseif (is_file(ROOT_PATH . 'Walrus/models/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/models/' . $class_name . '.php');

    } elseif (is_file(ROOT_PATH . 'vendors/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'vendors/' . $class_name . '.php');

    } elseif (is_file(ROOT_PATH . 'engine/controllers/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'engine/controllers/' . $class_name . '.php');

    } else {
        return false;
    }
}

WalrusKernel::execute();
