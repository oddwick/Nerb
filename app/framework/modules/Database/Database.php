<?php
// Nerb Application Framework
namespace nerb\framework;

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
 * @subpackage      Database
 * @class           Database
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c ) 2017
 * @todo
 *
 */


// load required libraries
/*
ClassManager::loadClass('Sqli');
ClassManager::loadClass('Table');
ClassManager::loadClass('Row');
ClassManager::loadClass('Rowset');
*/



class Database
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
     * @var \mysqli
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
        // set credentials for connecting
        $this->params['connection'] = $params;

        // give this database connection a name for other classes to retrieve it
        $this->handle = $handle;

        // the name of the current database being used
        $this->database_name = $this->params['connection']['name'];

        // establish connection to the table
        $this->connection = Connection::create( $params['name'], $params['user'], $params['pass'], $params['host'], $params['port'], $params['socket'] );
        // map tables in database
        $this->tables = $this->listTables();
        
        // register this database so that other classes can access it
        if( !Nerb::registry()->isRegistered( $handle )){
            Nerb::registry()->register( $this, $handle );
        }
        
        return;

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




    // !TABLES ---------->

   /**
     * factory function that reates a Table.
     * fisrt it checks the registry to see if there ia a copy of this table already
     *registered, and if not create a new instance.
     *
     * @access     public
     * @param mixed $table
     * @param bool $writable (default: false)
     * @throws     Error
     * @return     Table
     */
    public function table( $table, $writable = false )
    {
        // error checking to make sure the table exists first
        if( !$this->isTable( $table ) ){
            throw new Error( "Table <code>[$table]</code> does not exist in the database.");
        }
        // check the registry to see if this table exists
        if( Nerb::registry()->isRegistered( $this->handle.'.'.$table ) ){
            return Nerb::registry()->fetch( $this->handle.'.'.$table );
        } 
        
        // if needed, a writeable table is returned
        if( $writable ){
            return new \nerb\framework\TableReadWrite( $this, $table );
        }

		// by default a read only table is returned
        return new \nerb\framework\TableRead( $this, $table );
        
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






    // !QUERIES ---------->

    /**
     *   this performs a query where nothing is returned except for the number of affected rows
     *
     *   @access     public
     *   @param      string $query
     *   @return     int
     *   @throws     Error
     */
    public function execute( string $query ) : int
    {
        // error checking
        // make sure that there is a query
        if ( empty( $query ) ) {
            throw new Error('Query string is empty');
        }

        // debugging profile
        $profile = array('query' => $query, 'start' => microtime());

        // fetch result
        if ( !$this->connection->real_query( $query )) {
            $error = mysqli_error( $this->connection );
            $error_no = mysqli_errno( $this->connection );
            throw new Error('<p>'.$error.'</p><p><code>'.$query.'</code></p><p>Error #'.$error_no.'</p>');
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
     *   @throws     Error
     */
    public function query( string $query )
    {
        // error checking
        // make sure that there is a query
        if ( empty( $query ) ) {
            throw new Error('Query string is empty');
        }

        // debugging profile
        $profile = array('query' => $query, 'start' => microtime());
		
        // fetch result
		$result = $this->connection->query( $query );
		
        //insert debugging profile into array
        $profile['end'] = microtime();
        $this->profile[] = $profile; 
        
        // returns mysqli_result object
        if ( !empty($result) ) {
	        return $result;
        }
        
        $error = mysqli_error( $this->connection );
        $error_no = mysqli_errno( $this->connection );
        throw new Error('<p>'.$error.'</p><p><code>'.$query.'</code></p><p>Error #'.$error_no.'</p>');
      
        
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
            throw new Error('<p>'.$error.'</p><p><code>'.$query.'</code></p><p>Error #'.$error_no.'</p>');
        } 
        
        return $queries_processed;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   retrieves values from a table and returns as a Rowset object
     *
     *   @access     protected
     *   @param      string $query (sql query string)
     *   @return     Rowset
     *   @throws     Error
     */
    public function fetch( string $query ) : Rowset
    {
        // ensure that a table is selected
        if ( !$query ) {
            throw new Error( '<code>[$query]</code> is empty.' );
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
        $rows = new \nerb\framework\Rowset();
        
        // add rows to dataset
        while ( $resultRow = mysqli_fetch_assoc($result) ) {
            $rows->add( new \nerb\framework\Row( $this->handle, $column->table, $columns, $resultRow ) );
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
   
   
   
    // !PREPARED STATEMENTS ---------->
    
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
     *   @return mysqli_stmt
     */
    public function prepare( $query_string )
    {
        return $this->connection->prepare( $query_string );
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
