<?php
/**
 * This file holds the Cache class
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
 * This class helps handle typical Memcache functionality
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Cache
{
    /**
     * Holds the instance of the Memcache object
     * 
     *  @var Memcache
     */
    public $memcache = null;
    
    /**
     * This function is the constructor that creates the memcache instance
     * 
     * @return void
     */
    public function __construct()
    {
        $this->memcache = New Memcache();
    }
    
    /**
     * Adds a server to the memcache instance
     * 
     * @param string  $host The host name of the Memcache serer
     * @param integer $port The port of the Memcache server
     * 
     * @return void
     */
    public function addServer($host, $port)
    {
        $this->memcache->addServer($host, $port, false);
    }
    
    /**
     * Returns the value stored in Memcache for the specified key
     * 
     * @param string $key The key to search Memcache on
     * 
     * @return mixed|null Returns the value on success or null otherwise
     */
    public function get($key)
    {
        if (empty($key))
        {
            return null;
        }
        return $this->memcache->get($key);
    }
    
    /**
     * Sets content in Memcache based on the passed in parameters.
     * 
     * @param string  $key     The key to be used in storing the data
     * @param mixed   $value   The content to be stored
     * @param integer $expires Expiration time of the item. If it's equal to 
     *                         zero, the item will never expire. You can also 
     *                         use Unix timestamp or a number of seconds 
     *                         starting from current time, but in the latter 
     *                         case the number of seconds may not exceed 
     *                         2592000 (30 days).
     * 
     * @return boolean Returns true on success or false otherwise
     */
    public function set($key, $value, $expires = 600)
    {
        if (empty($key)) 
        {
            return false;
        }
        
        if (!$this->memcache->replace($key, $value, 0, $expires))
        {
           if (!$this->memcache->set($key, $value, 0, $expires))
           {
               return false;   
           }        
        }
        return true;
    }
    
    /**
     * Removes the content stored in Memcache on the specified key.
     * 
     * @param string $key The key that was used to store the data.
     * 
     * @return boolean Returns true on success or false otherwise.
     */
    public function delete($key)
    {
       if (empty($key))
       {
          return false;
       }
       
       return $this->memcache->delete($key);
    }
}
