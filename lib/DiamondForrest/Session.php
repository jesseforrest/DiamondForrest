<?php
/**
 * This file holds the Session class.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2013 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */

/**
 * This class helps with PHP session handling
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2013 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Session
{
   /**
    * Stores the single instance of this class object
    *
    * @var Session
    */
   private static $instance;
    
   /**
    * Class constructor
    *
    * By making the constructor private we have prohibited
    * objects of the class from being instantiated from outside the class.
    *
    * @return void
    */
   private function __construct()
   {
      // Start a session if one has not already been started
      if (!session_id()) 
      {
         session_start();
      }
   }
    
   /**
    * Get the singleton Session object.
    *
    * @return Session Returns the singleton Session object
    */
   public static function getInstance()
   {
      if (!self::$instance)
      {
         self::$instance = new Session();
      }
      return self::$instance;
   }

   /**
    * Sets an item into the $_SESSION array based on the passed in parameters.
    *
    * @param string $key   The key to be used in the array
    * @param mixed  $value The value to be stored
    *
    * @return void
    */
   public function set($key, $value)
   {
      $_SESSION[$key] = $value;
   }

   /**
    * Returns the value stored in the session based on the passed in key
    *
    * @param string $key The key used to store the data
    *
    * @return mixed Returns the value stored in the session based on the passed
    * in key or <var>null</var> if nothing was set for that key.
    */
   public function get($key)
   {
      if (!isset($_SESSION[$key]))
      {
         return null;
      }
      return $_SESSION[$key];
   }
}
