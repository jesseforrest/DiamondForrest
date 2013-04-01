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
    *   'query' => 'SELECT * FROM table_name',
    *   'is_error' => false,
    *   'error_number' => 0,
    *   'error_message' => '',
    *   'affected' => 0, // number of rows affected or returned
    *   'time' => '20.0',
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
    * @param string  $user    The username credentials
    * @param string  $pass    The password credentials
    * @param string  $database The name of the database to connect to
    * @param integer $port    The port the database should connect to
    * @param boolean $log     Whether or not to log queries
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
    *                     This can be <var>self::FETCH_BOTH</var>, 
    *                     <var>self::FETCH_ASSOC</var>, 
    *                     <var>self::FETCH_NUM</var>
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
    * @param integer $index     The index to reset the pointer to
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
    * This function will attempt to select records from the database table
    * based on the <var>$tableName</var> and <var>$whereHash</var> array.
    *
    * @param string     $tableName The name of the table
    * @param array|null $whereHash A hash array of key/value pairs to
    *                              be used in the where clause part of the 
    *                              update. The key is the column name and the 
    *                              value is theactual value to match against. 
    *                              Each item in the array will be added to a 
    *                              MySQL "AND" clause. If you need to use an 
    *                              "OR" clause or more complex expression, you 
    *                              will need to write your own query. This 
    *                              parameter is optional if you want to select 
    *                              all records.
    *
    * @return array|null If one item is selected it will return an associative 
    * array of the key/value pairs.  If multiple items are selected it will
    * return an array of arrays. If no items were found it will return 
    * <var>null</var>.
    */
   public function select($tableName, $whereHash = null)
   {   
      // Build where clause
      $whereClause = '';
      if (is_array($whereHash))
      {
         foreach ($whereHash as $column => $value)
         {
            if ($whereClause != '')
            {
               $whereClause .= ' AND ';
            }
            $whereClause .= $column . ' = "' . $this->escape($value) . '"';
         }
      }
   
      $q = 'SELECT * '
         . 'FROM ' . $tableName . ' '
         . (($whereClause != '')
            ? 'WHERE ' . $whereClause
            : '');
      $r = $this->query($q);
      
      // If invalid query
      if (!$r) 
      {
         return null;
      }
      
      // If no records
      $numRecords = $this->getNumberOfRecords($r);
      if ($numRecords == 0)
      {
         return null;
      }
      
      // If one record
      if ($numRecords == 1)
      {
         return $this->getNextRecord($r, self::FETCH_ASSOC);
      }
      
      // If multiple records
      $resultArray = array();
      while ($row = $this->getNextRecord($r, self::FETCH_ASSOC))
      {
         $resultArray[] = $row;
      }
      return $resultArray;
   }
    
   /**
    * This function will attempt to insert records into the database table
    * based on the <var>$tableName</var> and <var>$insertHash</var> array.
    *
    * @param string $tableName  The name of the table
    * @param array  $insertHash A hash array of key/value pairs to be
    *                     inserted into the table. The key is the
    *                     column name and the value is the actual
    *                     value. If you have a database column called
    *                     'created', 'updated', or 'modified' this function 
    *                     will automatically set it's value to be the MySQL 
    *                     expression <var>NOW()</var>.
    *
    * @return boolean Returns true on success or false otherwise.
    */
   public function insert($tableName, $insertHash)
   {
      $columns = $this->getTableColumns($tableName);
      if (!$columns)
      {
         return false;
      }
   
      $keys = '';
      $values = '';
      foreach ($columns as $column)
      {
         if (!isset($insertHash[$column]))
         {
            if (($column != 'created') 
               && ($column != 'modified') 
               && ($column != 'updated'))
            {
               continue;
            }
         }
   
         if ($keys != '')
         {
            $keys .= ', ';
            $values .= ', ';
         }
   
         $keys .= $column;
   
         if (($column == 'created') 
            || ($column == 'modified') 
            || ($column == 'updated'))
         {
            $values .= 'NOW()';
         }
         else
         {
            $values .= '"' . $this->escape($insertHash[$column]) . '"';
         }
      }
   
      $q = 'INSERT INTO ' . $tableName . ' (' . $keys . ') '
         . 'VALUES (' . $values . ')';
      return $this->query($q);
   }
   
   
   /**
    * This function will attempt to update records into the database table
    * based on the <var>$tableName</var> and <var>$updateHash</var> array and
    * <var>$whereHash</var> array.
    *
    * @param string     $tableName  The name of the table
    * @param array      $updateHash A hash array of key/value pairs to
    *                               update the table with. The key is the
    *                               column name and the value is the actual
    *                               value. If you have a database column called
    *                               'updated' or 'modified', this function will
    *                               automatically set it's value to be the MySQL
    *                               expression <var>NOW()</var>.
    * @param array|null $whereHash  A hash array of key/value pairs to
    *                               be used in the where clause part of the 
    *                               update. The key is the column name and the 
    *                               value is the actual value to match 
    *                               against. Each item in the array will be 
    *                               added to a MySQL "AND" clause. If you need 
    *                               to use an "OR" clause or more complex
    *                               expression, you will need to write your own
    *                               query. This parameter is optional if you 
    *                               want to update all records.
    *
    * @return boolean Returns true on success or false otherwise.
    */
   public function update($tableName, $updateHash, $whereHash = null)
   {
      $columns = $this->getTableColumns($tableName);
      if (!$columns)
      {
         return false;
      }
       
      // Build set clause
      $setClause = '';
      foreach ($columns as $column)
      {
         if (!isset($updateHash[$column]))
         {
            if (($column != 'modified') && ($column != 'updated'))
            {
               continue;
            }
         }
         
         if ($setClause != '')
         {
            $setClause .= ', ';
         }
          
         if (($column == 'modified') || ($column == 'updated'))
         {
            $setClause .= $column . ' = NOW()';
         }
         else
         {
            $setClause .= $column . ' = '
               . '"' . $this->escape($updateHash[$column]) . '"';
         }
      }
      
      // Build where clause
      $whereClause = '';
      if (is_array($whereHash))
      {
         foreach ($whereHash as $column => $value)
         {
            if ($whereClause != '')
            {
               $whereClause .= ' AND ';
            }
            $whereClause .= $column . ' = "' . $this->escape($value) . '"';
         }
      }
      
      $q = 'UPDATE ' . $tableName . ' '
         . 'SET ' . $setClause . ' '
         . (($whereClause != '')
            ? 'WHERE ' . $whereClause
            : '');
      return $this->query($q);
   }
    
   /**
    * Returns an array of column names for the specified table
    *
    * @param string $tableName The name of the table
    *
    * @return array|null Returns an array on success or null if no table
    * found.
    */
   public function getTableColumns($tableName)
   {
      $q = 'SELECT `COLUMN_NAME` AS `column_name` '
      . 'FROM `INFORMATION_SCHEMA`.`COLUMNS` '
      . 'WHERE `TABLE_NAME` = "' . $this->escape($tableName) . '"';

      $r = $this->query($q);
      if ((!$r) || ($this->getNumberOfRecords($r) == 0))
      {
         return null;
      }

      $columns = array();
      while ($column = $this->getNextRecord($r))
      {
         $columns[] = $column['column_name'];
      }
      return $columns;
   }

   /**
    * Insert a record to the query log.  This will only add up to
    * <var>QUERY_LOG_MAX_SIZE</var> records and then no longer add to the
    * <var>$queryLog</var> member variable
    *
    * @param string  $query      The query ran
    * @param boolean $isError     If there was an error
    * @param integer $errorNumber  If an error it contains the MySQL error
    *                       number.
    * @param string  $errorMessage If an error it contains the MySQL error
    *                       message.
    * @param integer $affected    The number of rows affected
    * @param string  $time       The time it took to run the query.
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
