<?php
/**
 * This file holds the Redirect class.
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
 * This class is in charge of storing requested redirects and then handling
 * setting the headers in order for the redirection.
 * 
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Redirect
{
    /**
     * Holds an associative array of available redirects in the following
     * format:
     * <code>
     * $this->redirects['/old-url'] = array(
     *    'match' => '/old-url',
     *    'is_regex' => false,
     *    'redirect' => '/new-url'
     *    'status_code' => 301
     * );
     * $this->redirects['|/old-url/([A-Za-z0-9]*)|'] = array(
     *    'match' => '|/old-url/([A-Za-z0-9]*)|',
     *    'is_regex' => true,
     *    'redirect' => '/new-url/%s'
     *    'status_code' => 301
     * );
     * $this->redirects['|/old-url/([A-Za-z0-9]*)|'] = array(
     *    'match' => '|/old-url/([A-Za-z0-9]*)|',
     *    'is_regex' => true,
     *    'redirect' => function($page) { return '/new-url/' . $page; }
     *    'status_code' => 301
     * );
     * </code>
     *
     * @var array
     */
    protected $redirects = array();

    /**
     * Holds a list of HTTP status codes
     *
     * @var array
     */
    static protected $statusCodes = array(
        100 => 'HTTP/1.1 100 Continue',
        101 => 'HTTP/1.1 101 Switching Protocols',
        200 => 'HTTP/1.1 200 OK',
        201 => 'HTTP/1.1 201 Created',
        202 => 'HTTP/1.1 202 Accepted',
        203 => 'HTTP/1.1 203 Non-Authoritative Information',
        204 => 'HTTP/1.1 204 No Content',
        205 => 'HTTP/1.1 205 Reset Content',
        206 => 'HTTP/1.1 206 Partial Content',
        300 => 'HTTP/1.1 300 Multiple Choices',
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        304 => 'HTTP/1.1 304 Not Modified',
        305 => 'HTTP/1.1 305 Use Proxy',
        307 => 'HTTP/1.1 307 Temporary Redirect',
        400 => 'HTTP/1.1 400 Bad Request',
        401 => 'HTTP/1.1 401 Unauthorized',
        402 => 'HTTP/1.1 402 Payment Required',
        403 => 'HTTP/1.1 403 Forbidden',
        404 => 'HTTP/1.1 404 Not Found',
        405 => 'HTTP/1.1 405 Method Not Allowed',
        406 => 'HTTP/1.1 406 Not Acceptable',
        407 => 'HTTP/1.1 407 Proxy Authentication Required',
        408 => 'HTTP/1.1 408 Request Time-out',
        409 => 'HTTP/1.1 409 Conflict',
        410 => 'HTTP/1.1 410 Gone',
        411 => 'HTTP/1.1 411 Length Required',
        412 => 'HTTP/1.1 412 Precondition Failed',
        413 => 'HTTP/1.1 413 Request Entity Too Large',
        414 => 'HTTP/1.1 414 Request-URI Too Large',
        415 => 'HTTP/1.1 415 Unsupported Media Type',
        416 => 'HTTP/1.1 416 Requested range not satisfiable',
        417 => 'HTTP/1.1 417 Expectation Failed',
        500 => 'HTTP/1.1 500 Internal Server Error',
        501 => 'HTTP/1.1 501 Not Implemented',
        502 => 'HTTP/1.1 502 Bad Gateway',
        503 => 'HTTP/1.1 503 Service Unavailable',
        504 => 'HTTP/1.1 504 Gateway Time-out');

    /**
     * Set a simple redirect.  This function does not allow for regular
     * expression matches.
     *
     * @param string         $match      This is the path portion of the URL that
     *                                   you want to match against. An example
     *                                   string would be:
     *                                   <code>/example/old-url</code>
     * @param string|closure $redirect   If a string is passed in, it should be
     *                                   the URL to redirect to if there
     *                                   is a match.  If a closure is passed in,
     *                                   the closure should return a URL to
     *                                   redirect to. If the closure cannot
     *                                   determine a URL to redirect to, it
     *                                   should return <var>null</var>.
     * @param string         $statusCode The status code that should be set
     *                                   before the redirect. The default is
     *                                   <code>301</code>, which is Moved
     *                                   Permanently.
     *
     * @return void
     */
    public function setRedirect($match, $redirect, $statusCode = 301)
    {
        $this->redirects[$match] = array(
            'match' => $match,
            'is_regex' => false,
            'redirect' => $redirect,
            'status_code' => $statusCode
        );
    }

    /**
     * Set a regular expression redirect.
     *
     * @param string $match      This is the path portion of the URL that you
     *                           want to match against. An example string would
     *                           be: <code>/example/old-url/([0-9]*)</code>
     * @param string $redirect   This is the URL to redirect to if there is a
     *                           match.  This string should be in the format
     *                           that is passed to the <code>sprintf</code>
     *                           function.  All matches found from the regex
     *                           <code>$match</code> will be passed as
     *                           parameters to sprintf, with this string being
     *                           the format string.
     * @param string $statusCode The status code that should be set before the
     *                           redirect. The default is <code>301</code>,
     *                           which is Moved Permanently.
     *
     * @return void
     */
    public function setRegexRedirect($match, $redirect, $statusCode = 301)
    {
        $this->redirects[$match] = array(
            'match' => $match,
            'is_regex' => true,
            'redirect' => $redirect,
            'status_code' => $statusCode
        );
    }

    /**
     * This class will loop over the available redirects and redirect if any
     * are found.
     *
     * @return void
     */
    public function redirect()
    {
        $path = Url::getPath();

        $redirectUrl = '';

        // Return if no redirects exist
        if (count($this->redirects) == 0)
        {
            return;
        }

        // Check if exact match (cheap)
        if ((isset($this->redirects[$path]))
            && (!$this->redirects[$path]['is_regex']))
        {
            $statusCode = $this->redirects[$path]['status_code'];
            $redirect = $this->redirects[$path]['redirect'];

            // Return if status code not found
            if (!isset(self::$statusCodes[$statusCode]))
            {
                return;
            }
             
            // If closure
            if (is_callable($redirect))
            {
                $redirectUrl = $redirect();
            }
            // If exact redirect
            else
            {
                $redirectUrl = $redirect;
            }
             
            if ($redirectUrl !== null)
            {
                // Redirect
                header(self::$statusCodes[$statusCode]);
                header ('Location: ' . $redirectUrl);
                exit;
            }
        }

        // Loop through all regex redirects and attempt to find a match.
        // (expensive)
        foreach ($this->redirects as $redirect)
        {
            $matches = null;

            // Continue if not regular expression match
            if (!$redirect['is_regex'])
            {
                continue;
            }

            // If redirect found
            if (preg_match($redirect['match'], $path, $matches))
            {
                $statusCode = $redirect['status_code'];

                // Return if status code not found
                if (!isset(self::$statusCodes[$statusCode]))
                {
                    return;
                }

                // Remove full match from array list
                array_shift($matches);

                // If closure
                if (is_callable($redirect['redirect']))
                {
                    $closure = $redirect['redirect'];
                    $redirectUrl = call_user_func_array($closure, $matches);
                    if ($redirectUrl === null)
                    {
                        continue;
                    }
                }
                // If sprintf
                else
                {
                    $format = $redirect['redirect'];
                     
                    // Gather parameters array that needs to be passed to 
                    // sprintf
                    $parameters = array_merge(array($format), $matches);

                    // Determine redirect URL
                    $redirectUrl = call_user_func_array('sprintf', $parameters);
                }

                // Redirect
                header(self::$statusCodes[$statusCode]);
                header('Location: ' . $redirectUrl);
                exit;
            }
        }
    }
}
