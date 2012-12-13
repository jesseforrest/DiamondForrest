<?php
/**
 * This file holds the Hash class
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
 * This class allows the user to store data in a hash array by passing
 * key/value pairs.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Hash
{
    /**
     * Holds the hash array 
     * 
     * @var array
     */
    protected $hash = array();

    /**
     * Returns the value stored in hash array for the specified key
     * 
     * @param string $key The key to search the hash array on
     * 
     * @return mixed|null Returns the value on success or null otherwise
     */
    public function get($key)
    {
        if (!isset($this->hash[$key]))
        {
            return null;
        }
        return $this->hash[$key];
    }
    
    /**
     * Sets content in the hash array based on the passed in parameters.
     * 
     * @param string $key   The key to be used in storing the data
     * @param mixed  $value The content to be stored
     * 
     * @return boolean Returns true on success or false otherwise
     */
    public function set($key, $value)
    {
        if (empty($key)) 
        {
            return false;
        }
        $this->hash[$key] = $value;
        return true;
    }
    
    /**
     * Removes the content stored in Memhash on the specified key.
     * 
     * @param string $key The key that was used to store the data.
     * 
     * @return boolean Returns true on success or false otherwise.
     */
    public function delete($key)
    {
       if ((empty($key)) || (!isset($this->hash[$key])))
       {
          return false;
       }
       
       unset($this->hash[$key]);
       return true;
    }
}
