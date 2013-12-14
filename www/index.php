<?php
/**
 * Author: Walrus Team
 * Created: 14:48 13/12/13
 */

session_start();

use Walrus\core\Kernel\WalrusKernel as WalrusKernel;

//a bouger dans la config ?
define("APP_PATH", dirname(__FILE__) . '/');
define("ROOT_PATH", substr(dirname(__FILE__), 0, -4) . '/');

function __autoload ($class_name)
{
    if (strrpos($class_name, "\\")) {
        $exploded_class = explode('\\', $class_name);
        $class_name = array_pop($exploded_class);
    }

    if (is_file(ROOT_PATH . 'Walrus/controllers/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/controllers/' . $class_name . '.php');
    }
    if (is_file(ROOT_PATH . 'Walrus/core/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/core/' . $class_name . '.php');
    }
    if (is_file(ROOT_PATH . 'Walrus/models/' . $class_name . '.php')) {
        require_once(ROOT_PATH . 'Walrus/models/' . $class_name . '.php');
    }
}

WalrusKernel::execute();
