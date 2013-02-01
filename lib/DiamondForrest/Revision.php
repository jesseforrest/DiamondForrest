<?php
/**
 * This file holds the Revision class
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

require_once 'Url.php';

/**
 * This class handles returning the URLs that should be used to include
 * static files.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Revision
{
    /**
     * This holds the mapping of physical paths to the URL paths that should be
     * included within HTML.  Example:
     * <code>
     * $map = array(
     *     '/css/global.css' => '/css/global.123.css'
     * );
     * </code>
     *
     * @var array
     */
    static protected $map = array();

    /**
     * Set a mapping of a physical path to a URL path.  For example, you can
     * map the physical path "/css/global.css" to "/css/global.123.css".
     *
     * @param string $physicalPath The physical path
     * @param string $urlPath      The URL path
     *
     * @return void
     */
    static public function setMapping($physicalPath, $urlPath)
    {
        self::$map[$physicalPath] = $urlPath;
    }

    /**
     * Set more than one mapping at once by passing in an associative array
     * of mappings.  The format of <var>$mappings</var> should be:
     * <code>
     * $mappings = array(
     *     '/css/global.css' => '/css/global.123.css'
     *     '/js/global.js' => '/js/global.243.js'
     * );
     * </code>
     *
     * @param array $mappings An associative array of mappings
     *
     * @return void
     */
    static public function setMappings($mappings)
    {
        self::$map = array_merge(self::$map, $mappings);
    }

    /**
     * This returns the URL that should be used.
     *
     * @param string $path The path portion of the string.  For example,
     * "/css/example.css" (without quotes).
     *
     * @return string Returns the URL that should be used.  If the file
     * is not found in the member variable <var>$map</var>, the <var>$path</var>
     * that you passed in will be returned appended to the current domain.
     */
    static public function getUrl($path)
    {
        $domain = Url::getProtocol() . '://' . Url::getHost();
        if (isset(self::$map[$path]))
        {
            return $domain . self::$map[$path];
        }
        return $domain . $path;
    }
}