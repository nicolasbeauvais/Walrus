<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 14:48 13/12/13
 */

use Walrus\core\WalrusKernel;
use Walrus\core\WalrusAutoload;

/**
 * Declaration for the $_ENV global variable.
 * $_ENV is filed with configuration info in WalrusKernel,
 * and routing info in WalrusROuter
 */
$_ENV['Walrus'] = array();

/**
 * Require constant
 */
require_once('../config/env.php');

require_once('../vendor/RedBean/rb.php');

/**
 * Require WalrusAutoload
 */
require_once('../Walrus/core/WalrusAutoload.php');

/**
 * Launch WalrusAutoload, ready to go.
 */
new WalrusAutoload();

/**
 * Launch WalrusKernel
 */
WalrusKernel::execute();
