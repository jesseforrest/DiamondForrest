<?php
/**
 * This file is used to define paths and database configurations for your 
 * production environment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */

/**
 * Holds whether or not the current code base is running on your local machine.
 * 
 * @var boolean
 */
define('IS_LOCAL', false);

/**
 * Holds whether or not the current code base is running on a development 
 * machine.
 * 
 * @var boolean
 */
define('IS_DEVELOPMENT', false);

/**
 * Holds whether or not the current code base is running on a staging 
 * machine.
 * 
 * @var boolean
 */
define('IS_STAGING', false);

/**
 * Holds whether or not the current code base is running on a production 
 * machine.
 * 
 * @var boolean
 */
define('IS_PRODUCTION', true);

/**
 * The IP address of the MySQL database server
 *
 * @var string
 */
define('DB_SERVER', '127.0.0.1');

/**
 * The username of the user that can connect to the MySQL database server
 *
 * @var string
 */
define('DB_USER', 'username');

/**
 * The password of the user that can connect to the MySQL database server
 *
 * @var string
 */
define('DB_PASS', 'password');

/**
 * The name of the database on the MySQL database server
 *
 * @var string
 */
define('DB_NAME', 'database_name');

/**
 * The port number that the MySQL database server is listening on
 *
 * @var string
 */
define('DB_PORT', '3306');
