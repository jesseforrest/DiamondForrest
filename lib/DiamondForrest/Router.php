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
 * This class is in charge of interpreting the current URL and
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
     * Holds an associative array of available routes in the following format:
     * <code>
     * $this->routes['|/payments/[A-Za-z0-9]*|'] = array(
     *    'match' => '|/payments/[A-Za-z0-9]*|',
     *    'is_regex' => true,
     *    'controller' => 'modules/offers/controllers/Payments.php',
     *    'class' => $class,
     *    'function' => 'show'
     * );
     * </code>
     * 
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
        $this->routes[$match] = array(
            'match' => $match,
            'is_regex' => $isRegex,
            'controller' => $controller,
            'class' => $class,
            'function' => $function
        );
    }
     
    /**
     * This class will loop over the available routes and attempt to instantiate
     * the controller.
     *
     * @return void
     */
    public function route()
    {
        $path = Url::getPath();

        // If no routes exist, set 404
        if (count($this->routes) == 0)
        {
            return;
        }

        // Check if exact match (cheap)
        if ((isset($this->routes[$path])) 
            && (!$this->routes[$path]['is_regex']))
        {
            $this->dispatch($this->routes[$path]);
            exit;
        }

        // Loop through all routes and attempt to find a match. (expensive)
        foreach ($this->routes as $i => $route)
        {
            $matches = null;

            // If route found
            if (($route['is_regex']) 
                && (preg_match($route['match'], $path, $matches)))
            {
                // Remove full match from array list
                array_shift($matches);

                $this->dispatch($route, $matches);
                exit;
            }
        }
    }
    
    /**
     * This function will dispatch the current route
     *
     * @param array      $route      The route to be dispatched
     * @param array|null $parameters An array of parameters to be passed to the
     *                               controller's function
     *
     * @return void
     */
    public function dispatch($route, $parameters = null)
    {
        include_once $route['controller'];

        if ($route['class'] !== null)
        {
            $controller = new $route['class']();
            if ($route['function'] !== null)
            {
                // If parameters should be passed to controller's function
                if (($parameters !== null) && (count($parameters) > 0))
                {
                    call_user_func_array(
                        array($controller, $route['function']),
                        $parameters);
                }
                // No parameters need to be passed
                else
                {
                    $controller->$route['function']();
                }
            }
        }
    }
}
