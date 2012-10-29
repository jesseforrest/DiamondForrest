<?php
/**
 * This file holds the Redirects class
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

require_once 'lib/DiamondForrest/Redirect.php';

/**
 * This class is in charge of interpreting the current URL and determining if
 * it should be redirected to another URL.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Redirects
{
    /**
     * This class holds all the Redirects
     * 
     * @var Redirect|null
     */
    protected $redirect = null;
    
    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->redirect = new Redirect();
    }
    
    /**
     * This function sets all applicable URL redirects
     * 
     * @return void
     */
    public function setRedirects()
    {
        // Example redirect from /old-page to /new-page
        $this->redirect->setRedirect(
            '/old-page', 
            '/new-page');
        
        // Example regular expression redirect from /old-page/123/page 
        // to /new-page/123/page
        $this->redirect->setRegexRedirect(
            '|/old-page/([0-9]*)/page|', 
            '/new-page/%u/page');
    }
    
    /**
     * This will loop over the available redirects and attempt to redirect if
     * a match exists.
     *
     * @return void
     */
    public function redirect()
    {
       $this->redirect->redirect(); 
    }
}
