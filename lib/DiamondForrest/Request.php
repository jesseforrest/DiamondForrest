<?php
/**
 * This file holds the Request class.
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
 * This class is in charge of storing requested redirects and then handling
 * setting the headers in order for the redirection.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2013 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Request
{
   /**
    * Returns the real IP address of the request, based on various $_SERVER 
    * variables.
    *
    * @return string ip address for the request
    */
   static public function ip()
   {
      // Default IP Address
      $ip = '0.0.0.0';

      // check for shared servers
      if(isset($_SERVER['HTTP_CLIENT_IP']))
      {
         $ip = $_SERVER['HTTP_CLIENT_IP'];
      }
      // check for proxy servers
      elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      {
         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
      // general method
      elseif(isset($_SERVER['REMOTE_ADDR']))
      {
         $ip = $_SERVER['REMOTE_ADDR'];
      }

      // If $ip is a list, get the first item
      list($ip) = explode(',', $ip);

      // return IP
      return $ip;
   }
   
   /**
    * Returns the 3 digit character country code for an ip address.
    *
    * @param string $ipAddress An optional IP address to be passed in. If no
    * IP address is passed in, it will determine the current user's IP address.
    *
    * @return string The 3 digit character country code
    */
   static public function country3($ipAddress = null)
   {
      if ($ipAddress === null)
      {
         $ipAddress = self::ip();
      }
   
      return geoip_country_code3_by_name($ipAddress);
   }

   /**
    * Returns the value for a single key in the $_POST array
    *
    * @param string $key The key in the $_POST array
    *
    * @return mixed The value for a single key in the $_POST array or 
    * <var>null</var> if it's not set.
    */
   static public function post($key)
   {
      if (!isset($_POST[$key]))
      {
         return null;
      }

      return $_POST[$key];
   }

   /**
    * Returns the value for a single key in the $_GET array
    *
    * @param string $key The key in the $_GET array
    *
    * @return mixed The value for a single key in the $_GET array or 
    * <var>null</var> if it's not set.
    */
   static public function get($key)
   {
      if (!isset($_GET[$key]))
      {
         return null;
      }

      return $_GET[$key];
   }

   /**
    * Returns the value for a single key in the $_SERVER array
    *
    * @param string $key The key in the $_SERVER array
    *
    * @return mixed The value for a single key in the $_SERVER array or 
    * <var>null</var> if it's not set.
    */
   static public function server($key)
   {
      if (!isset($_SERVER[$key]))
      {
         return null;
      }

      return $_SERVER[$key];
   }
}
