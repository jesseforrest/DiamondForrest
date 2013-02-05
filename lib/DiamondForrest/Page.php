<?php
/**
 * This file holds the Page class
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

require_once 'Database.php';

/**
 * This class helps simplify typical HTML based web page functionality
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Page
{
    /**
     * The title of the web page
     * 
     * @var string
     */
    private $title = '';

    /**
     * The title of the web page with htmlentities() ran on it
     * 
     * @var string
     */
    private $titleHtml = '';

    /**
     * The description of the web page
     * 
     * @var string
     */
    private $description = '';

    /**
     * The description of the web page with htmlentities() ran on it
     * 
     * @var string
     */
    private $descriptionHtml = '';

    /**
     * An array of strings that are used as keywords for the page
     * 
     * @var array
     */
    private $keywords = array();

    /**
     * An array of strings that are used as keywords for the page,
     * with htmlentities() ran on it.
     * 
     * @var array
     */
    private $keywordsHtml = array();

    /**
     * The URL (relative or absolute) to the favicon image.
     * 
     * @var string
     */
    private $faviconUrl = '';

    /**
     * An array of strings that hold the URLs (relative or absolute)
     * to the necessary Javascript files for the page.
     * 
     * @var array
     */
    private $jsUrls = array();

    /**
     * An array of strings that hold the URLs (relative or absolute)
     * to the necessary CSS files for the page.
     * 
     * @var array
     */
    private $cssUrls = array();
    
    /**
     * A string that contains any additional Javascript content that should be
     * placed before the end <var>body</var> element.  If this is an empty
     * string no Javascript content will be appended.
     * 
     * @var string
     */
    private $jsContent = '';

    /**
     * Holds an array of the views that should be called.
     * 
     * @var array
     */
    private $views = array();
    
    /**
     * Holds an array of all permitted data for the views
     * 
     * @var array
     */
    protected $viewData = array();

    /**
     * Whether or not this page has should display a 404 error page.
     * 
     * @var bool
     */
    private $is404ErrorPage = false;

    /**
     * Whether or not the page is allowed to be indexed by search engines.
     * 
     * @var boolean
     */
    private $isIndexable = true;
    
    /**
     * Whether or not the query log table will be output on PrintPage()
     * 
     * @var boolean
     */
    private $showQueryLogTable = false;
    
    /**
     * Whether or not the view data table will be output on PrintPage()
     * 
     * @var boolean
     */
    private $showViewDataTable = false;
    
    /**
     * Holds the arrays that are going to be display in the HTML along with a 
     * title of the array.
     * <code>
     * return $this->displayArrays(
     *     array(
     *         'array' = $_SERVER,
     *         'title' = 'Server'
     *     ),
     *     array(
     *         'array' = $_GET,
     *         'title' = 'Get'
     *     ),
     *     array(
     *         'array' = $_POST,
     *         'title' = 'Post'
     *     )
     * );
     * </code>
     * 
     * @var array
     */
    private $displayArrays = array();
    
    /**
     * Whether or not the query log table should be output in the HTML
     * 
     * @param boolean $showTable Whether or not you want to show the table.
     * 
     * @return void
     */
    public function showQueryLogTable($showTable)
    {
        $this->showQueryLogTable = $showTable;
    }
    
    /**
     * Whether or not the view data table should be output in the HTML
     * 
     * @param boolean $showTable Whether or not you want to show the table.
     * 
     * @return void
     */
    public function showViewDataTable($showTable)
    {
        $this->showViewDataTable = $showTable;
    }
    
    /**
     * Set an array that you want to display in the HTML output
     * 
     * @param array  $array An associative array you want outputted on the page
     *                      for debugging
     * @param string $title A string that defines the array that will be output
     * 
     * @return void
     */
    public function setDisplayArray($array, $title)
    {
        $this->displayArrays[] = array('array' => $array, 'title' => $title);
    }

    /**
     * Set whether or not this page should display a 404 error page
     * 
     * @param boolean $is404ErrorPage Whether or not the page should be 
     * displayed as a 404 error page
     * 
     * @return void
     */
    public function set404ErrorPage($is404ErrorPage)
    {
        $this->is404ErrorPage = $is404ErrorPage;
    }

    /**
     * This function sends a 404 error header
     * 
     * @return void
     */
    protected function set404ErrorHeader()
    {
        header('HTTP/1.0 404 Not Found');
    }

    /**
     * This function returns whether or not the page is a 404 error page
     * 
     * @return bool This function returns <var>true</var> if the page has been
     * set by <var>set404ErrorPage()</var> to be a 404 error page,
     * <var>false</var> otherwise.
     */
    public function is404ErrorPage()
    {
        return $this->is404ErrorPage;
    }

    /**
     * Returns the <code>doctype</code> of the page
     * 
     * @return string
     */
    protected function getDocTypeHtml()
    {
        return '<!DOCTYPE HTML>';
    }

    /**
     * Returns the content type meta tag for the page
     * 
     * @return string
     */
    protected function getContentTypeHtml()
    {
        return '<meta charset="utf-8" />';
    }

    /**
     * Returns the robots meta tag for the page
     * 
     * @return string
     */
    protected function getRobotsHtml()
    {
        return '<meta name="robots" content="noodp" />'
            . ((!$this->isIndexable())
                ? '<meta name="robots" content="noindex" />'
                : '');
    }

    /**
     * Returns the description meta tag for the page
     * 
     * @return string
     */
    protected function getDescriptionHtml()
    {
        return '<meta name="description" content="' . $this->getDescription() 
            . '" />';
    }

    /**
     * Returns the keywords meta tag for the page
     * 
     * @return string
     */
    protected function getKeywordsHtml()
    {
        return '<meta name="keywords" content="' . $this->getKeywords() 
            . '" />';
    }

    /**
     * Returns the title for the page
     * 
     * @return string
     */
    protected function getTitleHtml()
    {
        return '<title>' . $this->getTitle() . '</title>';
    }

    /**
     * Returns the favicon link for the page
     * 
     * @return string|null If favicon is set it will return a string with the
     * URL. If it is not set it will return <var>null</var>.
     */
    protected function getFaviconHtml()
    {
        if ($this->getFaviconUrl() != '')
        {
            return '<link rel="shortcut icon" '
                . 'href="' . $this->getFaviconUrl() . '" />';
        }
        return null;
    }

    /**
     * Returns the CSS links for the page
     * 
     * @return string
     */
    protected function getCssHtml()
    {
        $alreadyOutput = array();
        $css = '';
        for ($i = 0; $i < count($this->cssUrls); $i++)
        {
            if (isset($alreadyOutput[$this->cssUrls[$i]]))
            {
                continue;
            }
            $alreadyOutput[$this->cssUrls[$i]] = true;
            $css .= '<link href="' . htmlentities($this->cssUrls[$i]) . '" '
                . 'type="text/css" rel="stylesheet" />';
        }
        return $css;
    }

    /**
     * Returns the Javascript links for the page
     * 
     * @return string
     */
    protected function getJavascriptHtml()
    {
        $alreadyOutput = array();
        $js = '';
        for ($i = 0; $i < count($this->jsUrls); $i++)
        {
            if (isset($alreadyOutput[$this->jsUrls[$i]]))
            {
                continue;
            }
            $alreadyOutput[$this->jsUrls[$i]] = true;
            $js .= '<script language="JavaScript" type="text/javascript" '
                . 'src="' . htmlentities($this->jsUrls[$i]) . '"></script>';
        }
        return $js;
    }
    
    /**
     * Returns the Javascript content that should be appended to the bottom of
     * the <var>body</var> element.
     * 
     * @return string
     */
    protected function getJavascriptContent()
    {
       return $this->jsContent;
    }

    /**
     * Prints the page
     * 
     * @return void
     */
    public function printPage()
    {
        if ($this->is404ErrorPage())
        {
            $this->set404ErrorHeader();
            $this->set404ErrorPageSettings();
            exit;
        }

        echo $this->getDocTypeHtml()
            . '<html lang="en">'
            . '<head>'
                . $this->getContentTypeHtml()
                . $this->getRobotsHtml()
                . $this->getDescriptionHtml()
                . $this->getKeywordsHtml()
                . $this->getTitleHtml()
                . $this->getFaviconHtml()
                . $this->getCssHtml()
                . $this->getJavascriptHtml()
            . '</head>';
         
        // Flush after the head tag to allow browser to fetch CSS and JS in
        // parallel while the views are still processing
        flush();
         
        echo '<body>';

        // Include necessary templates
        $view = $this->viewData;
        foreach ($this->views as $viewPath)
        {
            include_once $viewPath;
        }
        
        // Variable used to store javascript
        $jsContent = '';
        
        if (($this->showQueryLogTable) 
            || (count($this->displayArrays) > 0) 
            || ($this->showViewDataTable))
        {
            echo '<div id="debug_details" style="display:none;margin:16px;">';
        
            // Prints the query log
            if ($this->showQueryLogTable)
            {
                $this->printQueryLogHtml();
            }
             
            // If need to output any arrays for debugging
            if (count($this->displayArrays) > 0)
            {
                foreach ($this->displayArrays as $display)
                {
                    $this->printArrayHtml($display['array'], $display['title']);
                }
            }
             
            // Prints the view data table
            if ($this->showViewDataTable)
            {
                $this->printArrayHtml($view, '$view');
            }

            // Print the page data toggle button in the bottom right
            echo '</div>';
            
            // Displays the 
            echo ''
                . '<div id="toggle_button" '
                    . 'style="'
                        . 'z-index: 1000;'
                        . 'font-family:Arial, Verdana, sans-serif;'
                        . 'font-weight:bold;'
                        . 'color:white;'
                        . 'background: blue;'
                        . 'background-image: -webkit-gradient('
                            . 'linear, 0% 0%, 0% 90%, '
                            . 'from(#4697E8), '
                            . 'to(#0F75DB));'
                        . 'background-image: -moz-linear-gradient('
                            . '#4697E8 0%, #0F75DB 90%);'
                        . 'box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);'
                        . 'padding:8px;'
                        . 'cursor:pointer;'
                        . 'position:fixed;'
                        . 'border-radius: 5px;'
                        . 'bottom:16px;'
                        . 'right:16px;" '
                    . 'onclick="showDebugDetails();">'
                    . 'Show Debug Details'
                . '</div>';
            
            // Javascript used to toggle displaying debug details
            $jsContent .= '' 
                . 'function showDebugDetails() {'  
                    . 'var debug_details = document.getElementById('
                        . '"debug_details");'
                    . 'var button = document.getElementById("toggle_button");' 
                    . 'if (debug_details.style.display != "none") {' 
                        . 'debug_details.style.display = "none";'   
                        . 'button.innerHTML = "Show Debug Details";'  
                    . '} else {' 
                        . 'debug_details.style.display = "block";'  
                        . 'button.innerHTML = "Hide Debug Details";'  
                    . '}' 
                . '}';
        }
        
        $jsContent .= $this->getJavascriptContent();
        if ($jsContent != '')
        {
            echo '<script>' . "\n"
                . '//<![CDATA[' . "\n"
                . $jsContent . "\n"
                . '//]]>' . "\n"
                . '</script>';
        }
        
       
        echo '</body></html>';
    }

    /**
     * Set any page settings for a 404 error page
     * 
     * @return void
     */
    protected function set404ErrorPageSettings()
    {
        $this->setIndexable(false);
    }

    /**
     * Set the title of the page
     * 
     * @param string $title The title of the page
     * 
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->titleHtml = htmlentities($title);
    }

    /**
     * Set another view that should be shown within the body of the page. The
     * order in which they are set using this function is the order in which
     * they will appear on the page.
     * 
     * @param string $viewPath The include path (including the filename) to the
     * view that you want displayed.
     * 
     * @return void
     */
    public function setView($viewPath)
    {
        $this->views[] = $viewPath;
    }

    /**
     * Sets the any data that will be sent to the included views
     * 
     * @param string $key   The key 
     * @param mixed  $value Any mixed value to report
     * 
     * @return void
     */
    public function setViewData($key, $value)
    {
        $this->viewData[$key] = $value;
    }
    
    /**
     * Returns any data that is set for the specified key.
     * 
     * @param string $key The key that was used to store information in the 
     * view.
     *  
     * @return null|mixed Returns the value on success or null otherwise.
     */
    public function getViewDataByKey($key)
    {
        if (!isset($this->viewData[$key]))
        {
            return null;
        }
        return $this->viewData[$key];
    }

    /**
     * Set the description for the page
     * 
     * @param string $description The page description
     * 
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $this->descriptionHtml = htmlentities($description);
    }

    /**
     * Set the keywords for the page.  This function takes an unlimited number
     * of parameters, each one should be a keyword.
     * 
     * @return void
     */
    public function setKeywords()
    {
        $keywords = func_get_args();
        for ($i = 0; $i < func_num_args(); $i++)
        {
            $this->keywords[count($this->keywords)] = $keywords[$i];
            $this->keywordsHtml[count($this->keywordsHtml)] = htmlentities(
            $keywords[$i]);
        }
    }

    /**
     * Set the Javascript URLS for the page.  This function takes an unlimited
     * number of parameters, each one should be a URL (relative or absolute) to
     * a Javascript file.
     * 
     * @return void
     */
    public function setJavascriptUrls()
    {
        $urls = func_get_args();
        for ($i = 0; $i < func_num_args(); $i++)
        {
            $this->jsUrls[count($this->jsUrls)] = $urls[$i];
        }
    }
    
    /**
     * Set the Javascript content for the page.  This should be valid Javascript
     * without the opening <var>script</var> tags or <var>cdata</var> tags. If
     * you use this function this class will automatically place it within
     * <var>script</var> and <var>cdata</var> tags and place it before the 
     * end <var>body</var> element. 
     * 
     * @param string $content The valid Javascript to be appended to the page.
     * 
     * @return void
     */
    public function setJavascriptContent($content)
    {
        $this->jsContent = $content;
    }

    /**
     * Set the CSS URLS for the page.  This function takes an unlimited
     * number of parameters, each one should be a URL (relative or absolute) to
     * a CSS file.
     * 
     * @return void
     */
    public function setCssUrls()
    {
        $urls = func_get_args();
        for ($i = 0; $i < func_num_args(); $i++)
        {
            $this->cssUrls[count($this->cssUrls)] = $urls[$i];
        }
    }

    /**
     * Set the URL (absolute or relative) for the favicon to be displayed.
     * 
     * @param string $url The url of the favicon
     * 
     * @return void
     */
    public function setFaviconUrl($url)
    {
        $this->faviconUrl = $url;
    }

    /**
     * Set whether or not this page can be indexed by search engines
     * 
     * @param boolean $isIndexable Whether or not the page is indexable
     * 
     * @return void
     */
    public function setIndexable($isIndexable)
    {
        $this->isIndexable = $isIndexable;
    }

    /**
     * Returns whether this page should be indexed by search engines
     * 
     * @return boolean
     */
    public function isIndexable()
    {
        return $this->isIndexable;
    }

    /**
     * Returns the title of the page
     * 
     * @param boolean $htmlEntities If true, it will be returned with
     * htmlentities() ran on it.
     * 
     * @return string
     */
    public function getTitle($htmlEntities = true)
    {
        return ($htmlEntities) ? $this->titleHtml : $this->title;
    }

    /**
     * Returns the description of the page
     * 
     * @param boolean $htmlEntities If true, it will be returned with
     * htmlentities() ran on it.
     * 
     * @return string
     */
    public function getDescription($htmlEntities = true)
    {
        return ($htmlEntities) ? $this->descriptionHtml : $this->description;
    }

    /**
     * Returns the URL of the favicon
     * 
     * @return string
     */
    public function getFaviconUrl()
    {
        return $this->faviconUrl;
    }

    /**
     * Returns a comma separated list of keywords for the page
     * 
     * @param boolean $htmlEntities If true, it will be returned with
     * htmlentities() ran on it.
     * 
     * @return string
     */
    public function getKeywords($htmlEntities = true)
    {
        $str = '';
        for ($i = 0; $i < count($this->keywords); $i++)
        {
            if ($i != 0)
            {
                $str .= ', ';
            }
            $str .= ($htmlEntities) 
                ? $this->keywordsHtml[$i] 
                : $this->keywords[$i];
        }
        return $str;
    }
    
    /**
     * This function prints the query log HTML content to the screen
     * 
     * @return void
     */
    protected function printQueryLogHtml()
    {
        echo ''
            . '<table style="'
                . 'width:100%;'
                . 'margin-top:8px;'
                . 'border:1px solid #a0a0a0;'
                . 'border-collapse: collapse;'
                . 'text-align:center;'
            . '">'
                . '<thead style="'
                    . 'background-color:#f4f5f7;'
                . '">'
                    . '<tr>'
                        . '<th style="padding-left:6px;padding-right:6px;">'
                            . 'Num'
                        . '</th>'
                        . '<th style="padding-left:6px;padding-right:6px;">'
                            . 'Query'
                        . '</th>'
                        . '<th style="padding-left:6px;padding-right:6px;">'
                            . 'Error'
                        . '</th>'
                        . '<th style="padding-left:6px;padding-right:6px;">'
                            . 'Rows'
                        . '</th>'
                        . '<th style="'
                            . 'padding-left:6px;'
                            . 'padding-right:6px;'
                            . 'white-space:nowrap;">'
                            . 'Time (ms)'
                        . '</th>'
                    . '</tr>'
                . '</thead>';
         
        $log = Database::getQueryLog();
        if (count($log) > 0)
        {
            echo '<tbody>';
             
            $totalTime = 0.0;
            foreach ($log as $k => $v)
            {
                echo ''
                    . '<tr style="'
                        . 'background-color:#ffffff;'
                        . 'border:1px solid #ccc;'
                    . '">'
                        . '<td>'. $k .'</td>'
                        . '<td style="'
                            . 'text-align:left;'
                            . 'font-size:10px;'
                        . '">'
                            . htmlentities($v['query']) 
                        . '</td>'
                        . '<td style="'
                            . 'background-color:#'.(($v['is_error'])
                                ? 'FFCCBA' : 'DFF2BF') . ';'
                        . '">'
                            . ((!$v['is_error'])
                                ? 'None'
                                : $v['error_number'] . ': ' 
                                    . htmlentities($v['error_message']))
                        . '</td>'
                        . '<td>' . $v['affected'] . '</td>'
                        . '<td>' . $v['time'] . '</td>'
                    . '</tr>';
                $totalTime += $v['time'];
            }
            echo ''
                . '<tr style="'
                    . 'background-color:#f8f8f8;'
                    . 'border:1px solid #ccc;'
                    . 'font-weight:bold;'
                . '">'
                    . '<td colspan="4" style="'
                        . 'text-align:right;'
                    . '">'
                        . 'Total Query Time:'
                    . '</td>'
                    . '<td>'
                        . $totalTime
                    . '</td>'
                . '</tr>';
            echo '</tbody>';
        }
         
        echo '</table>';
    }
    
   /**
    * This function prints the array contents to the screen
    * 
    * @param array  $array     The associative array you want to output
    * @param string $arrayName The name of the the array you are outputting. 
    *                          For example, if <var>$array</var> is $_SERVER, 
    *                          then <var>$arrayName</var> should be 'Server' 
    * 
    * @return void
    */
   protected function printArrayHtml($array, $arrayName)
   {
      if (count($array) == 0)
      {
         return;
      }
      echo ''
       . '<table style="'
                . 'width:100%;'
                . 'margin-top:8px;'
                . 'border:1px solid #a0a0a0;'
                . 'border-collapse: collapse;'
                . 'text-align:left;'
            . '">'
                . '<thead style="'
                    . 'background-color:#f4f5f7;'
                . '">'
               . '<tr>'
                  . '<th style="width:40px;">Num</th>'
                  . '<th style="width:250px;">' . $arrayName . ' Key</th>'
                  . '<th>' . $arrayName . ' Value</th>'
               . '</tr>'
            . '</thead>'
            . '<tbody>';
                        
      $count = 1;
      foreach ($array as $key => $value)
      {
         echo ''
                   . '<tr style="'
                        . 'background-color:#ffffff;'
                        . 'border:1px solid #ccc;'
                    . '">'
               . '<td>'. ($count++) .'</td>'
               . '<td>' . $key . '</td>'
               . '<td>' 
                  . (is_array($value) 
                     ? nl2br(htmlentities(print_r($value, true)))
                     : htmlentities($value))
               . '</td>'
            . '</tr>';
      }
      echo '</tbody>'
         . '</table>';
   }
}
