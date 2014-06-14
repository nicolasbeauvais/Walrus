<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 08:00 07/03/14
 */

// const ROOT_PATH, the root directory of walrus.
$_ENV['W']['ROOT_PATH'] = substr(dirname(__FILE__), 0, -4);

/**
 * const FRONT_PATH, the templates directory, relative to ROOT_PATH.
 */
$_ENV['W']['FRONT_PATH'] = $_ENV['W']['ROOT_PATH'] . 'templates' . DIRECTORY_SEPARATOR;

/**
 * const TESTBOX_PATH, the testbox directory path.
 */
$_ENV['W']['TESTBOX_PATH'] = $_ENV['W']['ROOT_PATH'] . 'testbox\\';

// autoload
require_once('Walrus/core/WalrusAutoload.php');
new \Walrus\core\WalrusAutoload();

// directory initialisation
if (!file_exists($_ENV['W']['ROOT_PATH'] . '\Test\testbox')) {
    try {
        $filer = new Walrus\core\WalrusFileManager($_ENV['W']['ROOT_PATH'] . '\Test');
        $filer->folderCreate('testbox', 777);
    } catch (Exception $exception) {
        echo 'Failed directory initilisation' . "\r\n";
        echo $exception->getMessage();
        die;
    }
}
