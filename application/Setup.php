<?php
/**
 * This file holds the Setup class
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
 * This class is the initial point of entry for the application. It will start
 * by including those libraries that are necessary for the file itself to
 * function correctly. This involves setting general directory and file paths,
 * loading configuration files, loading the database object, and doing any
 * additional configuration based on what environment is being ran (local,
 * development, staging, or production).
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Setup
{
   /**
    * Holds the current environment.  It will be 'local', 'development',
    * 'staging', 'production', or <var>null</var>.
    * @var string|null
    */
   static $environment = null;
    
   /**
    * This function will setup the necessary include path. It will add the
    * application and lib directory to the
    *
    * @return void
    */
   static public function setupIncludePath()
   {
      // Determine root, application, and lib directory paths
      $cwd = realpath($_SERVER['DOCUMENT_ROOT']);
      $parts = explode('/', $cwd);
      $count = count($parts);
      $rootDirectory = '';

      if ($count > 2)
      {
         for ($i = 1; $i < $count - 2; $i++)
         {
            $rootDirectory .= '/' . $parts[$i];
         }
      }

      // Add directories to include path
      set_include_path(get_include_path()
         . PATH_SEPARATOR . $rootDirectory);
   }
    
   /**
    * This will setup any settings that make local, development, staging,
    * and production unique.
    *
    * @return void
    */
   static public function setupEnvironmentSettings()
   {
      // If production or staging
      if ((self::$environment == 'production')
         || (self::$environment == 'staging'))
      {
         // Do not display errors
         ini_set('display_errors', 'Off');
      }
      // If development or local
      else
      {
         // Display errors
         ini_set('display_errors', 'On');
      }
   }
    
   /**
    * Returns a string for the environment: 'local', 'development', 'staging',
    * or 'production'. It will return 'production' if it cannot determine
    * the environment.
    *
    * @return string
    */
   static public function getEnvironment()
   {
      // Return the environment if we already determined it
      if (self::$environment !== null)
      {
         return self::$environment;
      }
       
      // Get the host name
      $host = Url::getHost();

      // Is local environment
      if (strpos($host, 'local.') !== false)
      {
         self::$environment = 'local';
      }
      // Is development environment
      else if (strpos($host, 'dev.') !== false)
      {
         self::$environment = 'development';
      }
      // Is staging environment
      else if (strpos($host, 'staging.') !== false)
      {
         self::$environment = 'staging';
      }
      // Is production environment
      else
      {
         self::$environment = 'production';
      }
      return self::$environment;
   }
}

// Setup the include path
Setup::setupIncludePath();

// Include Url class
require_once 'lib/DiamondForrest/Url.php';

// Include necessary config file or exit if invalid host name was passed in
$environment = Setup::getEnvironment();
switch ($environment)
{
   case 'production':
      include_once 'application/configurations/configProduction.php';
      break;
   case 'staging':
      include_once 'application/configurations/configStaging.php';
      break;
   case 'development':
      include_once 'application/configurations/configDevelopment.php';
      break;
   case 'local':
      include_once 'application/configurations/configLocal.php';
      break;
   case null:
      header('HTTP/1.0 400 Bad Request');
      exit;
}

// Setup environment related settings
Setup::setupEnvironmentSettings();

// Instantiate the Routes class to route to the necessary controller
require_once 'application/Routes.php';
$routes = new Routes();
$routes->setRoutes();
$routes->route();

// Instantiate the Redirects to redirect to the necessary URL if necessary
require_once 'application/Redirects.php';
$redirects = new Redirects();
$redirects->setRedirects();
$redirects->redirect();

// Set 404 if no route or redirect was found
header('HTTP/1.0 404 Not Found');
exit;
