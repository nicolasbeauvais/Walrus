<?php
/**
 * Author: Walrus Team
 * Created: 14:48 13/12/13
 */

session_start();

//a bouger dans la config ?
define("APP_PATH", dirname(__FILE__) ."/");
define("ROOT_PATH", preg_replace($pattern, "", dirname(__FILE__)));

var_dump(ROOT_PATH);
function __autoload($class_name)
{
    if (is_file('/engine/controllers/' . $class_name . '.php'))
        require_once('/engine/controllers/' . $class_name . '/' . $class_name . '.php');
}

// require our registry
require_once('/engine/Walrus_registry.php');
$registry = Walrus_registry::singleton();

// print out the frameworks name - just to check everything is working
print $registry->getFrameworkName();

exit();