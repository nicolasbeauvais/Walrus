<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais
 * Created: 08:20 10/04/14
 */

// const VERSION, the current version of Walrus.
$_ENV['W']['VERSION'] = '1.0.0b';

// const APP_PATH, the acces directory of walrus "./www/".
$_ENV['W']['APP_PATH'] = dirname(__FILE__) . DIRECTORY_SEPARATOR;

// const ROOT_PATH, the root directory of walrus.
$_ENV['W']['ROOT_PATH'] = substr(dirname(__FILE__), 0, -7) . DIRECTORY_SEPARATOR;

// const FRONT_PATH, the templates directory, relative to ROOT_PATH.
$_ENV['W']['FRONT_PATH'] = $_ENV['W']['ROOT_PATH'] . 'templates' . DIRECTORY_SEPARATOR;

// const START_TIME, microtime for the walrus launch
$_ENV['W']['START_TIME'] = microtime(true);
