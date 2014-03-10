<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 08:00 07/03/14
 */

/**
 * const ROOT_PATH, the root directory of walrus.
 */
define("ROOT_PATH", substr(dirname(__FILE__), 0, -4));

/**
 * const FRONT_PATH, the templates directory, relative to ROOT_PATH.
 */
define("FRONT_PATH", ROOT_PATH . 'templates' . '/');

/**
 * const TESTBOX_PATH, the testbox directory path.
 */
define("TESTBOX_PATH", ROOT_PATH . 'testbox\\');

// autoload
require_once('Walrus/core/WalrusAutoload.php');
new \Walrus\core\WalrusAutoload();

// directory initialisation
if (!file_exists(ROOT_PATH . '\Test\testbox')) {
    try {
        $filer = new Walrus\core\WalrusFileManager(ROOT_PATH . '\Test');
        $filer->folderCreate('testbox', 777);
    } catch (Exception $exception) {
        echo 'Failed directory initilisation' . "\r\n";
        echo $exception->getMessage();
        die;
    }
}
