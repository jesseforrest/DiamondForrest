<?php
/**
 * This script is the bootstrap file that all web requests are redirected to
 * if the file or folder requested does not exist in web root.  This is handled
 * either by an .htaccess file (when running on Apache) or Mod_Rewrite (when
 * running on lighttpd).
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

require_once '../../application/Setup.php';
