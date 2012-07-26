<?php
/**
 * This file holds the Router class.
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
 * This  class is in charge of interpreting the current URL and
 * determining which controller to instantiate. It also determines which method
 * to call (in MVC this is considered the "action") within the controller's
 * class.
 * 
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */

class Router
{
    /**
     * Holds an associated array of available routes in the following format:
     * <code>
     * $this->routes = array(
     *    'match' => '|/payments/[A-Za-z0-9]*|',
     *    'is_regex' => true,
     *    'controller' => 'modules/offers/controllers/Payments.php',
     *    'class' => $class,
     *    'function' => 'show'
     * );
     * </code>
     * @var array
     */
    protected $routes = array();
     
    /**
     * Sets a permitted route.
     * 
     * @param string  $match      This is the string or regular expression that
     *                            if matched will instantiate the controller and
     *                            call the passed in function.
     * @param boolean $isRegex    Whether or not the <var>$match</var> string is
     *                            a regular expression or hardcoded path that
     *                            should be matched against.
     * @param string  $controller The controller that will be instantiated. This
     *                            should be contain the include path and file
     *                            name.
     * @param string  $class      The name of the controller class.
     * @param string  $function   Once the controller is instantiated we will
     *                            call this function.
     *
     * @return void
     */
    public function setRoute($match, $isRegex, $controller, $class,
        $function)
    {
        $this->routes[] = array(
            'match' => $match,
            'is_regex' => $isRegex,
            'controller' => $controller,
            'class' => $class,
            'function' => $function
        );
    }
     
    /**
     * This function will set a 404 (page not found) header and exit the
     * application.
     * 
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function set404()
    {
        header('HTTP/1.0 404 Not Found');
        exit;
    }
     
    /**
     * This class will loop over the available routes and attempt to instantiate
     * the controller.  If it fails to find an available route it will set a
     * 404 header and exit the application.
     * 
     * @return void
     */
    public function route()
    {
        $routes = $this->routes;
        
        $path = Url::getPath();
        
        // If no routes exist, set 404
        if (count($routes) == 0) 
        {
            $this->set404();
        }
        // Loop through all routes and attempt to find a match.
        foreach ($routes as $i => $route)
        {
            // If route found
            if ((($route['is_regex']) && (preg_match($route['match'], $path)))
                || ((!$route['is_regex']) && ($route['match'] == $path)))
            {
                if (file_exists(dirname(__FILE__)
                    . '/../../application/' . $route['controller']))
                {
                    include_once $route['controller'];
                    $controller = new $route['class']();
                    $controller->$route['function']();
                    return;
                }
                $this->set404();
            }
        }

        // No matches so set 404 error
        $this->set404();
    }
}
