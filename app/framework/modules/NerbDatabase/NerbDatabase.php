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
/*
Nerb::loadClass('NerbSqli');
Nerb::loadClass('NerbDatabaseTable');
Nerb::loadClass('NerbDatabaseRow');
Nerb::loadClass('NerbDatabaseRowset');
*/



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
            'name' => NULL,
            'user' => NULL,
            'pass' => NULL,
        ),
        'debug' => FALSE,
        'verbose' => FALSE,
    );

    /**
     * database
     * 
     * @var mysqli
     * @access protected
     */
    protected $connection;
    
    /**
     * handle
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $handle = ''; // the name of the database for other classes to retrieve it
    
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
     * @param string $handle
     * @param array $params connection parameters [host|user|pass|name]
     * @return void
     */
    public function __construct( string $handle, array $params )
    {
        $this->init( $handle, $params );

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
     * init function.
     * 
     * @access protected
     * @param string $handle
     * @param array $params
     * @return void
     */
    protected function init( string $handle, array $params )
    {
        // set credentials for connecting
        $this->params['connection'] = $params;

        // ofuscate password to prevent it from being accidentally displayed
        $this->params['connection']['pass'] =  base64_encode($this->params['connection']['pass']);
        
        // give this database connection a name for other classes to retrieve it
        $this->handle = $handle;

        // the name of the current database being used
        $this->database_name = $this->params['connection']['name'];

        // establish connection to the table
        $this->connect();

        // map tables in database
        $this->tables = $this->listTables();
        
        // register this database so that other classes can access it
        if( !Nerb::isRegistered( $handle )){
            Nerb::register( $this, $handle );
        }
        
        return;
        
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
			
        if ( mysqli_connect_error() ) {
            $error = mysqli_connect_error();
            $errno = mysqli_connect_errno();
			
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
    public function handle() : string
    {
        return $this->handle;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns name of current database being used
     *
     *   @access     public
     *   @return     string
     */
    public function database() : string
    {
        return $this->database_name;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns database login credentials
     *
     *   @access     public
     *   @return     array
     */
    public function credentials() : array
    {
        return $this->params['connection'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   factory function that reates a NerbDatabaseTable.
     * 	fisrt it checks the registry to see if there ia a copy of this table already
     *	registered, and if not create a new instance.
     *
     *   @access     public
     *   @throws     NerbError
     *   @throws     NerbError
     *   @return     NerbDatabaseTable
     */
    public function table( $table )
    {
        // error checking to make sure the table exists first
        if( !$this->isTable( $table ) ){
            throw new NerbError( "Table <code>[$table]</code> does not exist in the database.");
        }
        // check the registry to see if this table exists
        if( Nerb::isRegistered( $this->handle.'.'.$table ) ){
            return Nerb::fetch( $this->handle.'.'.$table );
        } else {
            return new NerbDatabaseTable( $this, $table );
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   debugging method that outputs all queries made from this object during the
     *   execution of the script
     *
     *   @access     public
     *   @return     array
     */
    public function poll() : array
    {
        //return the the query array
        return $this->profile;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !QUERIES

    #################################################################



    /**
     *   this performs a query where nothing is returned except for the number of affected rows
     *
     *   @access     public
     *   @param      string $query
     *   @return     int
     *   @throws     NerbError
     */
    public function execute( string $query ) : int
    {
        // error checking
        // make sure that there is a query
        if ( empty( $query ) ) {
            throw new NerbError('Query string is empty');
        }

        // debugging profile
        $profile = array('query' => $query, 'start' => microtime());

        // fetch result
        if ( !$this->connection->real_query( $query )) {
            $error = mysqli_error( $this->connection );
            $error_no = mysqli_errno( $this->connection );
            throw new NerbError('<p>'.$error.'</p><p><code>'.$query.'</code></p><p>Error #'.$error_no.'</p>');
        }
        
        //insert debugging profile into array
        $profile['end'] = microtime();
        $this->profile[] = $profile; 
      
        // returns mysqli_result object
        return $this->_affected_rows();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   performs the actual query of the connection
     *
     *   @access     public
     *   @param      string $query
     *   @return     mixed
     *   @throws     NerbError
     */
    public function query( string $query )
    {
        // error checking
        // make sure that there is a query
        if ( empty( $query ) ) {
            throw new NerbError('Query string is empty');
        }

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
     *   for results that only have one answer such as max, min sum etc
     *
     *   @access     public
     *   @param      string $query
     *   @return     mixed $result
     */
    public function simpleQuery( string $query )
    {
        $result = $this->query( $query );
        $row = mysqli_fetch_array( $result );
        return $row[0];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns an array from a query
     *
     *   @access     public
     *   @param      string $query
     *   @return     array
     */
    public function queryArray( string $query ): array
    {
        // perform a query
        $result = $this->query( $query );
		
        // initialize array
        $data = array();
      
        // add table to array 
        while ( $row = mysqli_fetch_assoc( $result) ) {
            // if array only contains one element, then add it to data, otherwise add data array
            $data[] = count( $row ) > 1 ? $row : current( $row );
        }
        mysqli_free_result( $result );

        // if the result only has one entry, return it as an array, otherwise return full array
        return count( $data ) > 1 ? $data : $this->filterNullValues( current( $data ) );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
    /**
     * multiQuery function.
     *
     * this function can be a little fickle and is easy to throw errors in
     * and because of the security ramifications of using multi queries should only
     * be used on santitzed or trusted data, NEVER ON USER INPUT!  This function
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




    /**
     *   retrieves values from a table and returns as a NerbDatabaseRowset object
     *
     *   @access     protected
     *   @param      string $query (sql query string)
     *   @return     NerbDatabaseRowset
     *   @throws     NerbError
     */
    public function fetch( string $query ) : NerbDatabaseRowset
    {
        // ensure that a table is selected
        if ( !$query ) {
            throw new NerbError( '<code>[$query]</code> is empty.' );
        }
        
        // query the database
        $result = $this->query( $query );
       
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
            $rows->add( new NerbDatabaseRow( $this->handle, $column->table, $columns, $resultRow ) );
        }
		
        return $rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _affected_rows function.
     *
     * returns the number of affected rows from the last database operation
     * this function is a workaround because sometimes mysqli_affected_rows interprets
     * the returned result as bool if 0 or 1 rows were affected
     * 
     * @access protected
     * @return int
     */
    protected function _affected_rows() : int
    {
        return mysqli_affected_rows($this->connection);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   filters out empty array elements and returns an array of non null values
     *
     *   @access     public
     *   @param      array $data
     *   @return     array
     */
    public function filterNullValues( array $data ): array
    {
        return array_filter( $data, function ( $value ) {
            return $value !== '';
        });
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
    #################################################################

    //      !PREPARED STATEMENTS

    #################################################################



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





    /**
     *  this creates a prepared statement from a query string
     *	the query string must be formatted for prepared statements similar to this
     *
     *	INSERT INTO table VALUES (column, column, column) VALUES (?, ?, ?)
     *
     *   @access public
     *   @param string $query_string (formatted query string)
     *   @returnNerbStatement
     */
    public function prepare( $query_string )
    {
        return $this->connection->prepare( $query_string );
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns an array of table names in the current database
     *
     *   @access protected
     *   @return array
     */
    protected function listTables() : array
    {
        // build query string and return
        return $this->queryArray( 'SHOW TABLES FROM `'.$this->database().'`' );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   return the current table list from the database
     *
     *   @access public
     *   @return array
     */
    public function tables() : array
    {
        return $this->tables;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     *   checks to see if a table exists in the current database
     *
     *   @access public
     *   @param string $table
     *   @return bool
     */
    public function isTable( string $table ) : bool
    {
        return in_array( $table, $this->tables ) ? TRUE : FALSE ;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
