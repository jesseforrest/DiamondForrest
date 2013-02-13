<?php
/**
 * This file holds the Routes class
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

require_once 'lib/DiamondForrest/Router.php';

/**
 * This class is in charge of interpreting the current URL and determining which
 * controller to instantiate. It also determines which method to call
 * (in MVC this is considered the "action") within the controller's class.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Routes
{
   /**
    * This class holds all the routes
    *
    * @var Router|null
    */
   protected $router = null;

   /**
    * Class constructor
    *
    * @return void
    */
   public function __construct()
   {
      $this->router = new Router();
   }

   /**
    * This function sets all applicable URL routes
    *
    * @return void
    */
   public function setRoutes()
   {
      $this->routeHome();
   }

   /**
    * This will loop over the available routes and attempt to instantiate
    * the controller.
    *
    * @return void
    */
   public function route()
   {
      $this->router->route();
   }

   /**
    * This function defines the route to the home page
    *
    * @return void
    */
   protected function routeHome()
   {
      // Route to the Home controller
      // Example URL: example.com/
      $match       = '/';
      $isRegex     = false;
      $controller  = 'application/controllers/ControllerHome.php';
      $class       = 'ControllerHome';
      $function    = 'show';
      $this->router->setRoute(
         $match,
         $isRegex,
         $controller,
         $class,
         $function);
   }
}
