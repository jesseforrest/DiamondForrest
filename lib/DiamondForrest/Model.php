<?php
/**
 * This class is the base class for all models. It contains common functionality
 * that is shared across all models.
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
require_once 'Cache.php';

/**
 * This class holds the Model
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
abstract class Model
{
   /**
    * Holds an instance of the Database
    *
    * @var Database|null
    */
   static protected $database = null;

   /**
    * This function attempts to connect to the database. If it cannot connect
    * it will set a 503 error and exit. This function must be called prior to
    * making any database calls.
    *
    * @return void
    */
   static public function connect()
   {
      try
      {
         if (self::$database === null)
         {
            self::$database = new Database(
               DB_SERVER,
               DB_USER,
               DB_PASS,
               DB_NAME,
               DB_PORT);
         }
      }
      catch (Exception $e)
      {
         // Set a 503 error and exit because no connection to MySQL server
         header('HTTP/1.1 503 Service Temporarily Unavailable');
         header('Status: 503 Service Temporarily Unavailable');
         header('Retry-After: 60');
         exit;
      }
   }

   /**
    * Returns the name of the database table based on the model.
    *
    * @return string
    */
   static public function getTableName()
   {
      // Strip "Model" from class name
      $name = substr(get_called_class(), strlen('Model'));
      // Make plural
      if (substr($name, -1) == 'y')
      {
         $name = substr($name, 0, strlen($name) - 1) . 'ies';
      }
      else
      {
         $name .= 's';
      }
      // Convert camel case to lower case with underscores
      $name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
      return $name;
   }
   
   
   /**
    * This will attempt to insert a record into the table returned via the
    * <code>getTableName()</code> function.  It will attempt to insert
    *
    * @param array $keyValuePairs A hash array of key/value pairs to be
    *                             inserted into the table. The key is the
    *                             column name and the value is the actual
    *                             value. If you have a database column called
    *                             'created', this function will automatically
    *                             set it's value to be the MySQL expression
    *                             <var>NOW()</var>.
    *
    * @return boolean
    */
   static public function insert($keyValuePairs)
   {
      return self::$database->insert(self::getTableName(), $keyValuePairs);
   }
}
