<?php
/**
 * This file holds the Url class
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
 * This class provides methods to dissect a URL (Uniform Resource Locator).
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Url
{
    /**
     * Make class constructor private so that class methods must be called
     * statically.
     * 
     * @return void
     */
    private function __construct()
    {
        // Stub
    }
     
    /**
     * Returns the full URL
     * 
     * @return string
     */
    static public function getUrl()
    {
        return Url::getProtocol() . '://' . Url::getHost() 
            . $_SERVER['REQUEST_URI'];
    }
     
    /**
     * Returns the protocol (i.e. http or https)
     * 
     * @return string
     */
    static public function getProtocol()
    {
        if ((isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off')) 
        {
            return 'https';
        }
        return 'http';
    }
     
    /**
     * Returns the host name (i.e. subdomain.example.com)
     * 
     * @return string|null Returns the host name if it is set in 
     * <var>$_SERVER['HTTP_HOST']</var> otherwise it returns null.
     */
    static public function getHost()
    {
        if (!isset($_SERVER['HTTP_HOST']))
        {
            return null;
        }
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Returns the path (also known as the slug) of the URL. For example, if 
     * the URL is 
     * <code>http://www.example.com/folder/example.php?param=true</code>
     * then this function would return 
     * <code>/folder/example.php</code>
     * 
     * @return string|null Returns the path on success or <var>null</var> if
     * it cannot be determined.
     */
    static public function getPath()
    {
        if (!isset($_SERVER['REQUEST_URI']))
        {
            return null;
        }
        
        $uri = $_SERVER['REQUEST_URI'];
        $uriParts = explode('?', $uri);
        return $uriParts[0];
    }
     
    /**
     * Returns an array of the path exploded on the '/' character.  It will also
     * throw away any get parameters. For example, the URL
     * <code>http://www.example.com/part1/part2/example?test=true</code>
     * would return the following array:
     * <code>
     * return array(
     *     'part1',
     *     'part2',
     *     'example'
     * );
     * </code>
     * In the case that it is the actual domain, 
     * <code>http://www.example.com/</code>, this will simply return 
     * <var>null</var>.
     * 
     * @return array|null
     */
    static public function getPathParts()
    {
        $path = self::getPath();
        if ($path == '/')
        {
            return null;
        }
        $pathParts = explode('?', $path);
        return explode('/', substr($pathParts[0], 1));
    }
}