<?php

/**
 * Walrus Framework
 * File maintained by: Nicolas Beauvais (E-Wok)
 * Created: 08:06 04/02/14
 */

/**
 * const APP_PATH, the acces directory of walrus "./www/".
 */
define("APP_PATH", dirname(__FILE__) . '/');

/**
 * const ROOT_PATH, the root directory of walrus.
 */
define("ROOT_PATH", substr(dirname(__FILE__), 0, -7) . '/');
/**
 * const FRONT_PATH, the templates directory, relative to ROOT_PATH.
 */
define("FRONT_PATH", ROOT_PATH . 'www/templates' . '/');


/** RedBeans ORM constants */

/**
 * const RDBMS, Relational database management system : Mysql, PostgreSQL, SQLite...
 */
define("RDBMS", "mysql");
/**
 * const DB_HOST, Host for database in order to connect.
 */
define("DB_HOST", "localhost");
/**
 * const DB_NAME, Name of your database.
 */
define("DB_NAME", "mydatabase");
/**
 * const DB_USER, Username of your database.
 */
define("DB_USER", "root");
/**
 * const DB_PWD, Password for your database's user.
 */
define("DB_PWD", "root");
