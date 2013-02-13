<?php
/**
 * This file holds the Database class
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

/**
 * This class provides a database wrapper that should be used to make any
 * database calls.
 *
 * @category  PHP
 * @package   DiamondForrest
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2012 DiamondForrest
 * @license   https://github.com/jesseforrest/DiamondForrest License 1.0
 * @link      https://github.com/jesseforrest/DiamondForrest/wiki
 */
class Database
{
   /**
    * The maximum number of records to store in the $queryLog member variable
    *
    * @var integer
    */
   const QUERY_LOG_MAX_SIZE = 200;
    
   /**
    * The <code>getNextRecord</code> function uses this to output an
    * associative array.
    *
    * @var integer
    */
   const FETCH_ASSOC = 1;

   /**
    * The <code>getNextRecord</code> function uses this to output a
    * numerical array.
    *
    * @var integer
    */
   const FETCH_NUM = 2;
    
   /**
    * The <code>getNextRecord</code> function uses this to output both an
    * associative array and a numerical array.
    *
    * @var integer
    */
   const FETCH_BOTH = 3;

   /**
    * The MySQL database connection
    *
    * @var object
    */
   protected $connection;
    
   /**
    * Whether or not to log queries. This gets set in the constructor.
    * @var boolean
    */
   protected $logQueries = true;
    
   /**
    * The log of query calls in the format:
    * <code>
    * $this->queryLog[x] = array(
    *    'query' => 'SELECT * FROM table_name',
    *    'is_error' => false,
    *    'error_number' => 0,
    *    'error_message' => '',
    *    'affected' => 0, // number of rows affected or returned
    *    'time' => '20.0',
    * );
    * </code>
    *
    * @var array
    */
   protected static $queryLog = array();
    
   /**
    * The class constructor creates a Database object
    *
    * @param string  $server   The hostname of the server
    * @param string  $user     The username credentials
    * @param string  $pass     The password credentials
    * @param string  $database The name of the database to connect to
    * @param integer $port     The port the database should connect to
    * @param boolean $log      Whether or not to log queries
    *
    * @return void
    */
   public function __construct($server, $user, $pass, $database, $port = 3306,
         $log = true)
   {
      // Set whether or not to log queries
      $this->logQueries = $log;

      // Make connection to database
      $this->connection = mysqli_connect(
         $server,
         $user,
         $pass,
         $database,
         $port);

      if (!$this->connection)
      {
         throw new Exception('Unable to connect to MySQL server');
      }
   }
    
   /**
    * Returns the next record in the recordset
    *
    * @param array  &$recordset The MySQL recordset
    * @param string $resultType The type of array that is to be fetched.
    *                           This can be <var>self::FETCH_BOTH</var>,
    *                           <var>self::FETCH_ASSOC</var>,
    *                           <var>self::FETCH_NUM</var>
    *
    * @return array|false
    */
   public function getNextRecord(&$recordset, $resultType = self::FETCH_BOTH)
   {
      if (!$recordset)
      {
         return false;
      }
      switch ($resultType)
      {
         case self::FETCH_BOTH:
            return mysqli_fetch_array($recordset);
         case self::FETCH_ASSOC:
            return mysqli_fetch_assoc($recordset);
         case self::FETCH_NUM:
            return mysqli_fetch_row($recordset);
      }
      return false;
   }
    
   /**
    * Returns the number of records in the recordset
    *
    * @param array $recordset The MySQL recordset
    *
    * @return integer
    */
   public function getNumberOfRecords($recordset)
   {
      return mysqli_num_rows($recordset);
   }

   /**
    * Returns a string with backslashes before characters that need to be
    * quoted in database queries.
    *
    * @param string $str The part of the query string
    *
    * @return string
    */
   public function escape($str)
   {
      return mysqli_real_escape_string($this->connection, $str);
   }

   /**
    * Returns the id generated from the previous insert statement
    *
    * @return integer
    */
   public function getLastInsertId()
   {
      return mysqli_insert_id($this->connection);
   }

   /**
    * Takes a MySQL result array and returns it in a PHP aray
    *
    * @param object $recordset The MySQL result array
    *
    * @return array
    */
   public function getArray($recordset)
   {
      $rows = array();
      while ($row = $this->getNextRecord($recordset))
      {
         $rows[] = $row;
      }
      return $rows;
   }
    
   /**
    * Sets the resultset's internal pointer to the specified index,
    * <var>$index</var>.
    *
    * If an invalid index is passed in, this function will return
    * <var>false</var>.
    *
    * @param integer $index      The index to reset the pointer to
    * @param array   &$recordset The recordset
    *
    * @return boolean Returns true on success or false otherwise.
    */
   public function setRecordPointer($index, &$recordset)
   {
      if ($recordset != null)
      {
         if ($index > ($this->getNumberOfRecords($recordset) - 1))
         {
            return false;
         }
         return mysqli_data_seek($recordset, $index);
      }
      return false;
   }
    
   /**
    * Performs the given query on the database and returns the result, which
    * may be false, true or a resource identifier.
    *
    * @param string $query The SQL query to run
    *
    * @return mixed
    */
   public function query($query)
   {
      // If we should not log queries, simply run query
      if (!$this->logQueries)
      {
         return mysqli_query($this->connection, $query);
      }

      // If we should log queries
      $timeStart = microtime(true);
      $r = mysqli_query($this->connection, $query);
      $timeEnd = microtime(true);
      $isError = (!$r) ? true : false;
      $errorNumber = ($isError) ? mysqli_errno($this->connection) : 0;
      $errorMessage = ($isError) ? mysqli_error($this->connection) : '';
      $affected =  mysqli_affected_rows($this->connection);
      $time = number_format((($timeEnd - $timeStart) * 1000), 1, '.', '');
      $this->insertToQueryLog(
         $query,
         $isError,
         $errorNumber,
         $errorMessage,
         $affected,
         $time);
      return $r;
   }
    
   /**
    * Insert a record to the query log.  This will only add up to
    * <var>QUERY_LOG_MAX_SIZE</var> records and then no longer add to the
    * <var>$queryLog</var> member variable
    *
    * @param string  $query        The query ran
    * @param boolean $isError      If there was an error
    * @param integer $errorNumber  If an error it contains the MySQL error
    *                              number.
    * @param string  $errorMessage If an error it contains the MySQL error
    *                              message.
    * @param integer $affected     The number of rows affected
    * @param string  $time         The time it took to run the query.
    *
    * @return void
    */
   protected function insertToQueryLog($query, $isError, $errorNumber,
         $errorMessage, $affected, $time)
   {
      if (count(self::$queryLog) < self::QUERY_LOG_MAX_SIZE)
      {
         self::$queryLog[] =  array(
            'query' => $query,
            'is_error' => $isError,
            'error_number' => $errorNumber,
            'error_message' => $errorMessage,
            'affected' => $affected,
            'time' => $time
         );
      }
   }
    
   /**
    * Returns the query log
    *
    * @return array
    */
   public static function getQueryLog()
   {
      return self::$queryLog;
   }
}
