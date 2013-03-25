<?php
/**
 * This file holds the abstract Controller class
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

require_once 'Page.php';
require_once 'Session.php';

/**
 * This class is the base class for all controllers. It contains common
 * functionality that is shared across all controllers.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
abstract class Controller
{
   /**
    * The Page instance
    *
    * @var Page
    */
   protected $page = null;

   /**
    * The Session instance
    *
    * @var Session
    */
   protected $session = null;

   /**
    * The class constructor
    *
    * @return void
    */
   public function __construct()
   {
      $this->session = Session::getInstance();
      $this->setupPage();
   }

   /**
    * This function instantiates the Page objects and then configures it based
    * on the current environment.
    *
    * @return void
    */
   protected function setupPage()
   {
      $this->page = new Page();

      // If local machine or development machine output debugging information
      if ((defined('IS_LOCAL')) && (defined('IS_DEVELOPMENT')))
      {
         if ((IS_LOCAL) || (IS_DEVELOPMENT))
         {
            $this->page->showQueryLogTable(true);
            $this->page->showViewDataTable(true);
            $this->page->setDisplayArray($_GET, '$_GET');
            $this->page->setDisplayArray($_POST, '$_POST');
            $this->page->setDisplayArray($_SERVER, '$_SERVER');
         }
      }
   }
}