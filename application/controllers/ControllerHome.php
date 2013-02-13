<?php
/**
 * This file holds the Home Controller class
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

require_once 'lib/DiamondForrest/Controller.php';

/**
 * This class holds the controller for the home page
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class ControllerHome extends Controller
{
   /**
    * This will show the home page
    *
    * @return void
    */
   public function show()
   {
      $this->page->setTitle('DiamondForrest');
      $this->page->setCssUrls(Revision::getUrl('/css/global.css'));
      $this->page->printPage();
   }
}