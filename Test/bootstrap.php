<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 08:00 07/03/14
 */

use Walrus\core\WalrusCLI as WalrusCLI;

// constante

/**
 * const ROOT_PATH, the root directory of walrus.
 */
define("ROOT_PATH", substr(dirname(__FILE__), 0, -4));

/**
 * const TESTBOX_PATH, the testbox directory path.
 */
define("TESTBOX_PATH", ROOT_PATH . 'testbox\\');

// autoload

require_once('Walrus/core/WalrusAutoload.php');

new \Walrus\core\WalrusAutoload();
