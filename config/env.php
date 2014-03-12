<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 08:06 04/02/14
 */

/**
 * const WALRUS_VERSION, the current version of Walrus.
 */
define("WALRUS_VERSION", '1.1.0b');

/**
 * const APP_PATH, the acces directory of walrus "./www/".
 */
define("APP_PATH", dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * const ROOT_PATH, the root directory of walrus.
 */
define("ROOT_PATH", substr(dirname(__FILE__), 0, -7) . DIRECTORY_SEPARATOR);

/**
 * const FRONT_PATH, the templates directory, relative to ROOT_PATH.
 */
define("FRONT_PATH", ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);

/**
 * const START_TIME, microtime for the walrus launch
 */
define("START_TIME", microtime(true));
