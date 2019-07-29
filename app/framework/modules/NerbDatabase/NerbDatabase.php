<?php
// Nerb Application Framework

/**
 * Nerb Application Framework
 *
 *	This is the core of the Nerb database package and manages all of the basic connections and 
 *	querying of the database.  All results are returned in the form of a mysqli result object. 
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      NerbDatabase
 * @class           NerbDatabase
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c ) 2017
Copyright (c)2019 *
 * @todo
 *
 */


// load required libraries
Nerb::loadClass('NerbSqli');
Nerb::loadClass('NerbDatabaseTable');
Nerb::loadClass('NerbDatabaseRow');
Nerb::loadClass('NerbDatabaseRowset');



class NerbDatabase
{
    /**
     * params
     * 
     * (default value: array(
     *         'connection' => array(
     *             'host' => 'localhost',
     *             'name' => null,
     *             'user' => null,
     *             'pass' => null,
     *         ),
     *         'debug' => false,
     *         'verbose' => false,
     *     ))
     * 
     * @var string
     * @access protected
     */
    protected $params = array(
        'connection' => array(
            'host' => 'localhost',
            'name' => null,
            'user' => null,
            'pass' => null,
        ),
        'debug' => false,
        'verbose' => false,
    );

    /**
     * database
     * 
     * @var mysqli
     * @access protected
     */
    protected $connection;
    
    /**
     * database_name
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $name = ''; // the name of the database for other classes to retrieve it
    
    /**
     * database_name
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $database_name = ''; // the name of the database for other classes to retrieve it
    
    /**
     * tables
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $tables = array();
    
    /**
     * query
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $profile = array();




    /**
     * __construct function.
     * 
     * @access public
     * @param string $name
     * @param array $params connection parameters [host|user|pass|name]
     * @return void
     */
    public function __construct( string $name, array $params )
    {
	    // if debugging mode is on, then return a database_debug object which is the same as a 
	    // database object, but with extended polling and debugging capacity.
	    if( $params['debug'] == true ){
			Nerb::loadClass('NerbDatabaseDebug');
		    return new NerbDatabaseDebug( $name, $params );
	    }

        // set credentials for connecting
        $this->params['connection'] = $params;

        // ofuscate password to prevent it from being accidentally displayed
        $this->params['connection']['pass'] =  base64_encode($this->params['connection']['pass']);
        
        // give this database connection a name for other classes to retrieve it
        $this->name = $name;

        // give this database connection a name for other classes to retrieve it
        $this->connection_name = $this->params['connection']['name'];

        // establish connection to the table
        $this->connect();

        // map tables in database
        $this->tables = $this->tables();
        
        // register this database so that other classes can access it
        Nerb::register( $this, $name );
        
        return;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Destructor, if set will output the value of the current query array
    *
    *   @access     public
    *   @param      array $params connection parameters [host|user|pass|name]
    *   @return     void
    */
    public function __destruct()
    {
        $this->connection->close();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Connection string, connects to database with credentials given
    *
    *   @access     public
    *   @return     NerbDatabase
    *   @throws     NerbError
    */
    protected function connect() : self
    {

		// error checking 
		$this->connection = new NerbSqli(
            $this->params['connection']['host'],
            $this->params['connection']['user'],
            base64_decode( $this->params['connection']['pass'] ),
            $this->params['connection']['name'],
            $this->params['connection']['port'],
            $this->params['connection']['socket']
	    );
			
		if ( mysqli_connect_error( $this->connection ) ) {
			$error = mysqli_connect_error( $this->connection );
			$errno = mysqli_connect_errno( $this->connection );
			
            throw new NerbError(
            	'<p>Could not connect to Database host <strong>'.$this->params['connection']['host'].'</strong>. Database said:</p>'
            	.'<p>'.$error.'</p>'
            	.'<p>Error #'.$errno.'</p>'
            );
		}
			
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   disconnect string, disconnects from MySql server
    *
    *   @access     protected
    *   @return     void
    */
    protected function disconnect()
    {
        $this->connection->close();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns name of database object in registry
    *
    *   @access     public
    *   @return     string
    */
    public function name() : string
    {
        return $this->name;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns name of current database being used
    *
    *   @access     public
    *   @return     string
    */
    public function use() : string
    {
        return $this->connection_name;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !QUERIES

    #################################################################



    /**
    *   submits a mysql query to the server and returns result.
    *
    *   if an object is passed as the query, an attempt to call the
    *   function __toString(), otherwise an Error will be thrown
    *
    *   @access     protected
    *   @param      mixed $query sql query string
    *   @return     mysqli_result
    *   @throws     NerbError
    */
    protected function _query( $query )
    {

        // debugging profile
        $profile = array('query' => $query, 'start' => microtime());

        // fetch result
        if ( !$result = $this->connection->query( $query )) {
	        $error = mysqli_error( $this->connection );
	        $error_no = mysqli_errno( $this->connection );
            throw new NerbError('<p>'.$error.'</p><p><code>'.$query.'</code></p><p>Error #'.$error_no.'</p>');
        }
        
        //insert debugging profile into array
        $profile['end'] = microtime();
        $this->profile[] = $profile; 
      
        // returns mysqli_result object
        return $result;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   public wrapper method for _query to handle error checking and extract 
    *	query from objects that support __toString querys.
    *
    *   if an object is passed as the query, an attempt to call the
    *   function __toString(), otherwise an Error will be thrown
    *
    *   @access     public
    *   @param      mixed $query sql query string
    *   @return     array
    *   @throws     NerbError
    */
    public function query( $query )
    {
        // error checking
        // make sure that there is a query
        if ( empty( $query ) ) {
            throw new NerbError('Query string is empty');
        }

        // transform an object into a query string
        // this applies to any supported objects: NerbDatabaseTable, NerbDatabaseInsert NerbDatabaseSelect, NerbDatabaseUpdate
        if ( is_object( $query ) ) {
            if ( method_exists( $query, '__toString' ) ) { 
                $query = $query->__toString();
            } else {
                throw new NerbError( 'Invalid object passed.  <code>$query</code> object does not contain a <code>__toString()</code> method' );
            }// end if method_exists
        }// end if is_object

        // returns mysqli_result object
        return $this->_query( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   performs a direct query on the database and returns a mysqli result
    *
    *   @access     public
    *   @param      string $query
    *   @return     mysqli_result
    *   @throws     NerbError
    */
	public function querytable( string $query )
    {
        // ensure that a table is selected
        if ( !$query ) {
            throw new NerbError( '<code>[$query]</code> is empty.  Expecting string or Select object' );
        }
        
		// fetch database
		$connection = $this->connection;
        
        // return result set
        return $connection->query( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns an array form a query
    *
    *   @access     public
    *   @param      string $query
    *   @return     mysqli_result
    */
	public function queryArray( string $query ): array
    {
		// perform a query
		$result = $this->_query( $query );
		
		// fetch row from result
		$row = mysqli_fetch_assoc( $result );

		// release results
		mysqli_free_result( $result );

        // return first result as array
        return $row;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
	/**
	 * multiQuery function.
	 *
	 * this function can be a little fickle and is easy to throw errors in
	 * and because of the security ramifications of using multi queries should only
	 * be used on santitzed or trusted data, NEVER on user input.  This function
	 * works well with SQL table dumps, etc. and is what it was intended for.
	 * 
	 * @access public
	 * @param string $query
	 * @return int - number of queries processed
	 */
	public function multiQuery( string $query ) : int
	{
		// get connection
		$connection = $this->connection;
		
		// keep track of the number of lines processed
		// this is the total queries processed including comments
		$queries_processed = 0;
		
		// run the query		
    	if ( mysqli_multi_query( $connection, $query )) {
		   do {
		       // process and free results 
		       if ( $result = mysqli_store_result( $connection )) {
		           mysqli_free_result( $result );
		       }
		       $queries_processed++;
		   } while ( mysqli_next_result( $connection ) );
		
		}// end if
	 			
	 	// error catching block		
        if ( $error = mysqli_error( $connection ) ) {
	        $error_no = mysqli_errno( $this->connection );
            throw new NerbError('<p>'.$error.'</p><p><code>'.$query.'</code></p><p>Error #'.$error_no.'</p>');
        } 
        
        return $queries_processed;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




//!check-----
    /**
    *   retrieves values from a table and returns as a NerbDatabaseRowset object
    *
    *   @access     protected
    *   @param      string $query (sql query string)
    *   @return     NerbDatabaseRowset
    *   @throws     NerbError
    */
	public function fetchRows( string $query )
    {
        // ensure that a table is selected
        if ( !$query ) {
            throw new NerbError( '<code>[$query]</code> is empty.  Expecting string or NerbDatabaseSelect object' );
        }
        
		// fetch database
		$connection = $this->connection;
        
        // add query to list for polling and replay
        $this->query[] = $query;
        
        // query the database
        $result =  $connection->query( $query );
       
        // maps the fields as (field=>table.field) for multiple table select statements
        $column_list = mysqli_fetch_fields( $result );
        
		//prepare column list	
        foreach ( $column_list as $column ) {
            $columns[ $column->name ] = $column->table.'.'.$column->name;
        }

		// instantiate new rowset with mapped fields
        $rows = new NerbDatabaseRowset();
        
        // add rows to dataset
        while ( $resultRow = mysqli_fetch_assoc($result) ) {
            $rows->add( new NerbDatabaseRow( $this->database, $this->name, $columns, $resultRow ) );
        }
		
        return $rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   this will convert a sqli result to an array for manipulation and free results
    *
    *   @access     protected
    *   @param      mysqli_result $result 
    *   @return     array
    */
    public function resultsToArray( $result ) : array
    {
        
        // initialize array
        $data = array();
      
        // add table to array 
		while ( $row = mysqli_fetch_assoc( $result) ) {
			$data[] = $row;
		}
		
		mysqli_free_result( $result );

        return $data;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * affected_rows function.
     *
     * returns the number of affected rows from the last database operation
     * this function is a workaround because sometimes mysqli_affected_rows interprets
     * the returned result as bool if 0 or 1 rows were affected
     * 
     * @access public
     * @return int
     */
    public function affected_rows() : int
    {
        return mysqli_affected_rows( $this->connection );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !DATA FORMATING

    #################################################################



    /**
    *   quotes a value into a comma seperated string from either an array or given string 
    *
    *   @access     public
    *   @param      mixed $string
    *   @return     mixed same as original input
    */
    public static function quote( $data ) : string
    {

        // if the value given is an array, quote it and return it
        if ( is_array( $data )) {
            for ( $i = 0; $i < count( $data ); $i++ ) {
                // if the $data is a string, quote and slash it
                if ( is_string($data[$i] )) {
                    $data[$i] = "'".addslashes( $data[$i] )."'";
                } // end if

                // if the field is empty, null it
                elseif ( empty( $data[$i] )) {
                    $data[$i] = "''";
                }// end elseif
            } // end foreach

            // returns the processed string
            return implode( $data, ',' );
        } // end if

        // otherwise it returns the an individual slashed and quoted string
        return "'".addslashes( $data )."'";
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   quotes a value into a string replacing a '?' with a quoted string
    *
    *   @access     public
    *   @param      string $string string to be quoted into
    *   @param      string $value replacement value
    *   @return     string
    *   @example    $database->quoteInto('value = ?', 'new value') returns 'value = 'new value' '
    */
    public static function quoteInto( string $string, string $value ) : string
    {
        return str_replace('?', self::quote($value), $string);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




   /**
    *   cleans and escapes data for insertion into database
    *	
    *	!! this method only provides BASIC cleaning of user submitted data.
    *	   DO NOT rely only on this method for cleaning data.  contextual 
    *	   cleaining is a must!
    *
    *   @access     public
    *   @param     	array $data
    *   @return     array $data
    */
	public function sanitize( array $data ) : array
    {
		foreach( $data as $key => $value ){
			
			// replace 4byte UTF secquences 
			$value = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '\xEF\xBF\xBD', $value);		
			
			// escape illegal characters
			$value = preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]~u', '\\\$0', $value);
			
			// escape sql characters
			$data[$key] = $this->connection->real_escape_string( $value );
		}

		return $data;
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //      !PREPARED STATEMENTS

    #################################################################



    /**
    *   this creates a prepared statement from a query string
    *	the query string must be formatted for prepared statements similar to this
    *
    *	INSERT INTO table VALUES (column, column, column) VALUES (?, ?, ?)
    *
    *   @access     public
    *   @param     	string $query_string (formatted query string)
    *   @return     Nerb_Statement
    */
	public function prepare( $query_string )
    {
		return $this->connection->prepare( $query_string );
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns an array of table names in the current database
    *
    *   @access     public
    *   @return     array
    */
    public function tables() : array
    {
        // build query string
        $query = 'SHOW TABLES FROM `'.$this->params['connection']['name'].'` ';
		$result = $this->resultsToArray( $this->query( $query ));
		
		foreach( $result as $value ){
			$this->tables[] = current($value);
		} // end foreach
		
        // query database
        return $this->tables;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   checks to see if a table exists in the current database
    *
    *   @access     public
    *   @param      string $table
    *   @return     bool
    */
    public function isTable( string $table ) : bool
    {
        return in_array( $table, $this->tables ) ? true : false ;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns the name of the current database
    *
    *   @access     public
    *   @return     string
    */
    public function getDatabaseName() : string
    {
        return $this->_connection['name'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
