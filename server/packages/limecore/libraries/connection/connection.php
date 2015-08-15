<?php
/**
 * Create a connection to a database and query it
 *
 * Allows easy use of MySQLi prepared statements and connection to the database
 *
 * @author Tom Barham <me@mrfishie.com>
 * @version 1.0
 * @copyright Copyright (c) 2013, Tom Barham
 * @package Libraries.Connection
 */
class Connection {
    private static $con;
    private static $id = -1;

    public static $persistant = true;
    public static $host = "localhost";
    public static $username = "the-problem";
    public static $password = "Passw0rd1";
    public static $database = "the-problem";
    
    /**
     * Connect to the database
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     */
    public static function connect() {        
        // Database connection

        if (empty(self::$host)) return;

        self::$con = new MySQLi(
            (self::$persistant ? "p:" : "") . self::$host,
            self::$username,
            self::$password,
            self::$database);
    }
    
    /**
     * Get underlying MySQLi connection object
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @return MySQLi Connection to database
     */
    public static function &getconnection() {
        return self::$con;
    }
    
    /**
     * Close the database connection
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     */
    public static function close() {
        if (!empty(self::$host) && !self::$con->connect_errno) self::$con->close();
    }
    
    /**
     * Query the database
     *
     * Allows prepared statements and multi-queries
     *
     * @author Darren
     * @version 1.0
     * @param string $sql The query to execute
     * @param string $typeDef Specify the type of each parameter
     * @param array $params Parameters to place in query
     * @return array Results from query
     */
    public static function query($sql, $typeDef = false, $params = false) {
      if (!self::$con->connect_errno) {
        $multiQuery = FALSE;
        if($stmt = mysqli_prepare(self::$con,$sql)){
          if(count($params) == count($params,1)){
            $params = array($params);
            $multiQuery = FALSE;
          } else {
            $multiQuery = TRUE;
          } 
          $bindParams = null;
          if($typeDef){
            $bindParams = array();
            $bindParamsReferences = array();
            $bindParams = array_pad($bindParams,(count($params,1)-count($params))/count($params),"");        
            foreach($bindParams as $key => $value){
                  $bindParamsReferences[$key] = &$bindParams[$key]; 
            }
            array_unshift($bindParamsReferences,$typeDef);
            $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param');
            $bindParamsMethod->invokeArgs($stmt,$bindParamsReferences);
          }
     
          $result = array();
          foreach($params as $queryKey => $query){
            if ($bindParams) {
              foreach($bindParams as $paramKey => $value){
                    $bindParams[$paramKey] = $query[$paramKey];
              }
            }
            $queryResult = array();
            if(mysqli_stmt_execute($stmt)){
                  self::$id = $stmt->insert_id;
                  $resultMetaData = mysqli_stmt_result_metadata($stmt);
                  if($resultMetaData){                                                                              
                    $stmtRow = array();  
                    $rowReferences = array();
                    while ($field = mysqli_fetch_field($resultMetaData)) {
                          $rowReferences[] = &$stmtRow[$field->name];
                    }                               
                    mysqli_free_result($resultMetaData);
                    $bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result');
                    $bindResultMethod->invokeArgs($stmt, $rowReferences);
                    while(mysqli_stmt_fetch($stmt)){
                          $row = array();
                          foreach($stmtRow as $key => $value){
                            $row[$key] = $value;          
                          }
                          $queryResult[] = $row;
                    }
                    mysqli_stmt_free_result($stmt);
                  } else {
                    $queryResult[] = mysqli_stmt_affected_rows($stmt);
                  }
            } else {
                  $queryResult[] = FALSE;
            }
            $result[$queryKey] = $queryResult;
          }
          mysqli_stmt_close($stmt);  
        } else {
              $result = FALSE;
        }
        if (self::$con->error) {
          Error::report(E_USER_WARNING, self::$con->error . " with query '" . $sql . "'", NULL, NULL, self::$con->errno);
        }
        if($multiQuery){
              return $result;
        } else {
              return $result[0];
        }
      }
    }
    
    /**
     * Returns the latest insert primary key
     *
     * @author Tom Barham <me@mrfishie.com>
     * @version 1.0
     * @copyright Copyright (c) 2013, Tom Barham
     * @return integer Latest insert primary key
     */
    public static function insertid() {
        return self::$id;
    }
}
