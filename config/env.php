<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 08:20 10/04/14
 */

/**
 * Declaration for the $_ENV global variable.
 * $_ENV contain everything needed to run Walrus Framework
 * $_ENV is filed with path vars in this file,
 * configuration info in WalrusKernel,
 * and routing info in WalrusRouter
 */
$_ENV['W'] = array();

// VERSION, the current version of Walrus.
$_ENV['W']['VERSION'] = '1.0.0';

// PUBLIC_PATH, the acces directory of walrus "app/www/".
$_ENV['W']['PUBLIC_PATH'] = dirname(__FILE__) . DIRECTORY_SEPARATOR;

// ROOT_PATH, the root directory of walrus.
$_ENV['W']['ROOT_PATH'] = substr(dirname(__FILE__), 0, -7) . DIRECTORY_SEPARATOR;

// APP_PATH, the app directory of walrus "app/", relative to ROOT_PATH.
$_ENV['W']['APP_PATH'] = $_ENV['W']['ROOT_PATH'] . 'app' . DIRECTORY_SEPARATOR;

// FRONT_PATH, the templates directory, relative to APP_PATH.
$_ENV['W']['FRONT_PATH'] = $_ENV['W']['APP_PATH'] . 'templates' . DIRECTORY_SEPARATOR;

// HELPERS_PATH, the helpers directory, relative to APP_PATH.
$_ENV['W']['HELPERS_PATH'] = $_ENV['W']['APP_PATH'] . 'helpers' . DIRECTORY_SEPARATOR;

// WALRUS_PATH, the Walrus directory, relative to ROOT_PATH.
$_ENV['W']['WALRUS_PATH'] = $_ENV['W']['ROOT_PATH'] . 'Walrus' . DIRECTORY_SEPARATOR;

// TMP_PATH, the temporary files directory, relative to ROOT_PATH.
$_ENV['W']['TMP_PATH'] = $_ENV['W']['ROOT_PATH'] . 'tmp' . DIRECTORY_SEPARATOR;

// LOGS_PATH, the logs directory, relative to TMP_PATH.
$_ENV['W']['LOGS_PATH'] = $_ENV['W']['TMP_PATH'] . 'logs' . DIRECTORY_SEPARATOR;

// CACHE_PATH, the cache directory, relative to TMP_PATH.
$_ENV['W']['CACHE_PATH'] = $_ENV['W']['TMP_PATH'] . 'cache' . DIRECTORY_SEPARATOR;

// CONFIG_PATH, the config directory, relative to ROOT_PATH.
$_ENV['W']['CONFIG_PATH'] = $_ENV['W']['ROOT_PATH'] . 'config' . DIRECTORY_SEPARATOR;

// const START_TIME, microtime for the walrus launch
$_ENV['W']['START_TIME'] = microtime(true);
