<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 14:48 13/12/13
 */

use Walrus\core\WalrusKernel;
use Walrus\core\WalrusAutoload;

/**
 * Require constant
 */
require_once('../../config/env.php');

/**
 * Require WalrusAutoload
 */
require_once($_ENV['W']['WALRUS_PATH'] . '/core/WalrusAutoload.php');

/**
 * Launch WalrusAutoload, ready to go.
 */
new WalrusAutoload();

/**
 * Launch WalrusKernel
 */
WalrusKernel::execute();
