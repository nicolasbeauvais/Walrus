<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 14:48 13/12/13
 */

session_start();

use Walrus\core\WalrusKernel;
use Walrus\core\WalrusAutoload;

//a bouger dans la config ?
define("APP_PATH", dirname(__FILE__) . '/');
define("ROOT_PATH", substr(dirname(__FILE__), 0, -4) . '/');
define("FRONT_PATH", ROOT_PATH . 'www/templates' . '/');

require_once('../Walrus/core/WalrusAutoload.php');

new WalrusAutoload();

WalrusKernel::execute();
