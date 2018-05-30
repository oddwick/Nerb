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
 * @license         https://www.oddwick.com
 *
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
    protected $database;
    
    /**
     * database_handle
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $database_handle = ''; // the name of the database for other classes to retrieve it
    
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
    *   Constructor initiates database connection
    *
    *   @access     public
    *   @param      array $params connection parameters [host|user|pass|name]
    *   @return     void
    */
    public function __construct( $database_handle, $params )
    {
	    // if debugging mode is on, then return a database_debug object which is the same as a 
	    // database object, but with extended polling and debugging capacity.
	    if( $params['debug'] == true ){
			Nerb::loadClass('NerbDatabaseDebug');
		    return new NerbDatabaseDebug( $database_handle, $params );
	    }

        // set credentials for connecting
        $this->params['connection'] = $params;

        // ofuscate password
        $this->params['connection']['pass'] =  base64_encode($this->params['connection']['pass']);
        
        // give this database connection a name for other classes to retrieve it
        $this->database_handle = $database_handle;

        // establish connection to the table
        $this->connect();

        // map tables in database
        $this->tables = $this->tables();
        
        // register this database so that other classes can access it
        Nerb::register( $this, $database_handle );
        
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
        $this->database->close();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Connection string, connects to database with credentials given
    *
    *   @access     public
    *   @return     object self
    *   @throws     NerbError
    */
    protected function connect(): self
    {

		// error checking 
		$this->database = new NerbSqli(
            $this->params['connection']['host'],
            $this->params['connection']['user'],
            base64_decode( $this->params['connection']['pass'] ),
            $this->params['connection']['name'],
            $this->params['connection']['port'],
            $this->params['connection']['socket']
	    );
			
		if ( $this->database->connect_error ) {
            throw new NerbError(
            	'Could not connect to Database host <strong>'.$this->params['connection']['host'].'</strong>. Database said:<br>'.
            	$this->database->connect_errno.' - '.
            	$this->database->connect_error
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
    protected function disconnect(): void
    {
        $this->database->close();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns this databases registerd name
    *
    *   @access     public
    *   @return     void
    */
    public function handle(): string 
    {
        return $this->database_handle;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   alias of handle
    *
    *   @access     public
    *   @return     void
    */
    public function name(): string
    {
        return $this->handle();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   this will convert a sqli result to an array for manipulation and free results
    *
    *   @access     protected
    *   @param      mysqli_result $result 
    *   @return     array
    */
    public function resultsToArray( $result ): array
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
        if ( !$result = $this->database->query( $query )) {
	        $error = mysqli_error( $this->database );
            throw new NerbError('<p>'.$error.'</p><p><code>['.$query.']</code></p>');
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
                throw new NerbError( 'Invalid object passed.  $query object does not contain a <CODE>__toString()</CODE> method' );
            }// end if method_exists
        }// end if is_object

        // returns mysqli_result object
        return $this->_query( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   quotes a value into a comma seperated string from either an array or given string 
    *
    *   @access     public
    *   @param      mixed $string
    *   @return     mixed same as original input
    */
    public static function quote( $string ): string
    {

        // if the value given is an array, quote it and return it
        if ( is_array( $string )) {
            for ( $i = 0; $i < count( $string ); $i++ ) {
                // if the $string is a string, quote and slash it
                if ( is_string($string[$i] )) {
                    $string[$i] = "'".addslashes( $string[$i] )."'";
                } // end if

                // if the field is empty, null it
                elseif ( empty( $string[$i] )) {
                    $string[$i] = "''";
                }// end elseif
            } // end foreach

            // returns the processed string
            return implode( $string, ',' );
        } // end if

        // otherwise it returns the an individual slashed and quoted string
        return "'".addslashes( $string )."'";
        
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
	public function sanitize( array $data ): array
    {
		foreach( $data as $key => $value ){
			
			// replace 4byte UTF secquences 
			$value = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '\xEF\xBF\xBD', $value);		
			
			// escape illegal characters
			$value = preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]~u', '\\\$0', $value);
			
			// escape sql characters
			$data[$key] = $this->database->real_escape_string( $value );
		}

		return $data;
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //              !PREPARED STATEMENTS

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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
		return $this->database->prepare( $query_string );
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   quotes a value into a string replacing a '?' with a quoted string
    *
    *   @access     public
    *   @param      mixed $string string to be quoted into
    *   @param      string $value replacement value
    *   @return     string
    *   @example    $database->quoteInto('value = ?', 'new value') returns 'value = 'new value' '
    */
    public static function quoteInto( $string, $value )
    {
        return str_replace('?', self::quote($value), $string);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns an array of table names in the current database
    *
    *   @access     public
    *   @return     array
    */
    public function tables()
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
    public function isTable( $table )
    {
        return in_array( $table, $this->tables ) ? true : false ;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns the name of the current database
    *
    *   @access     public
    *   @return     string
    */
    public function getDatabaseName()
    {
        return $this->_connection['name'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns an array of table field names and descriptions
    *
    *   @access     public
    *   @param      string $table table name
    *   @return     array
    */
    public function info( $table )
    {
        // get tabel data from database
        $result = $this->resultsToArray( $this->query( 'SHOW COLUMNS FROM `'.$table.'` ' ));

        // iterate and pass data with a little better formatting and lower case names
        // to the info array
        foreach ( $result as $columns ) {
            $info[$columns['Field']] = array(
	            'name' => $columns['Field'],
	            'full_name' => $table.'.'.$columns['Field'],
	            'type' => $columns['Type'],
	            'null' => $columns['Null']=='NO'?'NOT NULL':'',
	            'default' => $columns['Default'],
	            'extra' => $columns['Extra'],
	            'key' => $columns['Key']
            );
        }

        return $info;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
