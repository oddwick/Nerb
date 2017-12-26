<?php
// Nerb Application Framework

/**
 *  Object class for manipulating a table
 *
 * @category    	Nerb
 * @package     	Nerb
 * @subpackage      NerbDatabase
 * @class 			NerbDatabaseTable
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */



#todo: fetchRow (  where, limit ); instead of fetch first row
class NerbDatabaseTable
{

	/**
	 * database
	 * 
	 * (default value: "")
	 * 
	 * @var string
	 * @access protected
	 */
	protected $database = "";
	
	/**
	 * name
	 * 
	 * (default value: "")
	 * 
	 * @var string
	 * @access protected
	 */
	protected $name = "";
	
	/**
	 * primary
	 * 
	 * (default value: "")
	 * 
	 * @var string
	 * @access protected
	 */
	protected $primary = "";
	
	/**
	 * attribs
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $attribs = array();
	
	/**
	 * columns
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $columns = array();
	
	/**
	 * query
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $query = array();





	/**
	*   Constructor initiates object
    *
	*   Creates a table instance and if a table is given, will automatically map the columns and metadata to variables for
	*   easy access.
    *
	*   @access     public
	*   @param      mixed $database (database can either be string or Nerbdatabase object)
	*   @param      string $table
	*   @return     NerbDatabaseTable
	*   @throws     NerbError
	*/
	public function __construct( $database, string $table )
    {

       	// check to see what was passed in $database
       	if( is_object($database) ){
	       	
	        // if a NerbDatabase is given, bind to it and contiune
	        if ( $database instanceof NerbDatabase || is_subclass_of( $database, "NerbDatabase" ) ) {
				// fetch the database name for later retrival
				$this->database = $database->name();
			} else {
		       	throw new NerbError( 'Database adaptor "<code>$database</code>" must be a <code>NerbDatabase</code> object.  <code>'.get_class( $database ).'</code> object was passed.' );
			} // end if
	       	
        // if the database name is giveen
        } else {
	        $this->database = $database;
        }// end if is_object

		// bind this object to the table
		$this->bind( $table );
		
        // register this database so that other classes can access it
        Nerb::register( $this, $this->database.".".$table );
       
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*   returns the current query string
    *
	*   @access     public
	*   @return     string the last element of the query array
	*/
	public function __toString(): string
    {
        //return the last element of the query array
        return $this->query[count( $this->query )-1];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*   returns table variable element
    *
	*   @access     public
	*   @param      string $var
	*   @return     array data row found
	*/
	public function __get( string $var )
    {
        return isset( $this->$var ) ? $this->$var : false;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //                !BINDING & MAPPING METHODS

    #################################################################



	/**
	*   returns a listing of columns
    *
	*   @access     public
	*   @return     array
	*/
	public function columns(): array
    {
        return $this->columns;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*   checks to see that a column exists in the table
    *
	*   @access     public
	*   @param      string $column
	*   @return     bool
	*/
	public function columnExists( string $column ): bool
    {
        return in_array( $column, $this->columns );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*   makes sure that the table exists in the database and initiates the mapping process
    *
	*   @access     public
	*   @param      string $table
	*   @return     object self
	*   @throws     NerbError
	*/
	protected function bind( string $table )
    {
		// fetch database
		$database = Nerb::fetch( $this->database );
		
        // table must exist to be bound
        if ( !$database->isTable( $table ) ) {
            throw new NerbError( "Cannot bind to table '<code>".$table."</code>' because it does not exist in database." );
        } // end if

		// set the table name
		$this->name = $table;
		
		// map out columns
		$this->map();
		
		return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*  Gets a listing of all the columns in a table and returns it as an array with the columns name as the key
	*  and the default value as the value
    *
	*   @access     protected
	*   @return     void
	*/
	protected function map()
    {

		// fetch database
		$database = Nerb::fetch( $this->database );

        // profile the table
        $this->attribs = $database->info( $this->name );
        
        // extract columns
        $this->columns = array_keys( $this->attribs );

        foreach ( $this->attribs as $column ) {
            if ( $column['key'] == "PRI" ) {
                $this->primary = $column['name'];
                break;
            }
        }// end for each
        
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   checks to see if a row exists in the table from a primary key
    *
    *   @access     public
    *   @param      mixed $key unique primary key value
    *   @return     bool
    *   @throws     NerbError
    */
	public function exists( $key ): bool
    {
        // make sure the table has a primary key defined
        if ( !$this->primary ) {
            throw new NerbError( $this->_errorString( "Table '<code>".$this->name."</code>' has no primary key" ));
        }
        
        $query = "SELECT COUNT( * ) FROM `".$this->name."` WHERE ".$this->primary." = '$key' ";

        // fetch database
		$database = Nerb::fetch( $this->database );

        return $database->queryString( $this ) > 0 ? true : false;
    
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






    /**
    *   formats an error message based on error reporting levels set in the cfg file
    *
    *   @access     protected
    *   @param      string $message
    *   @return     string
    */
    protected function _errorString( string $message ): string 
    {
        // if the ERROR_LEVEL = 2 then show the table columns and structure
        if( ERROR_LEVEL == 2 ){
	        $extra = "<code><ol>";
	        foreach( $this->attribs as $key => $value ){
	        	$extra .="<li>".$key." -- ".$value["type"];
	        	if( $value["key"] ) $extra .= "[".$value["key"]."]";
	        	$extra .="</li>";
	        } // end foreach
	        
	       $extra .= "</ol></code>";
	       $message .= $extra;
        }
        return $message;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    #################################################################

    //            !DATA FETCH AND INSERTION METHODS

    #################################################################



    /**
    *   performs a direct query on the database and returns a mysqli result
    *
    *   @access     protected
    *   @param      string $query
    *   @return     mysqli_result
    *   @throws     NerbError
    */
	protected function _query( string $query )
    {
        // ensure that a table is selected
        if ( !$query ) {
            throw new NerbError( '<code>$query</code> is empty.  Expecting string or Select object' );
        }
        
		// fetch database
		$database = Nerb::fetch( $this->database );
        
        // return result set
        return $database->query( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   converts a mysql result to a populated array
    *
    *   @access     protected
    *   @param      mysqli_result $result
    *   @param      string $column
    *   @return     mysqli_result
    */
	protected function _query2array( $result, string $column = null ): array
    {
		// transfer the result to a temp array to free results
		while( $row = mysqli_fetch_assoc( $result ) ){
			
			// if a column has been declared, then just that element, otherwise the whole array will be added
			if( $val = $column ? $row[ $column ] : $row ) 
				$array[] = $val;
		}

		// release results
		mysqli_free_result( $result );

        // return first result as array
        return $array;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   retrieves values from a table and returns as a NerbDatabaseRowset object
    *
    *   @access     protected
    *   @param      string $query (sql query string)
    *   @return     NerbDatabaseRowset
    *   @throws     NerbError
    */
	protected function _fetch( string $query )
    {
        // ensure that a table is selected
        if ( !$query ) {
            throw new NerbError( '<code>$query</code> is empty.  Expecting string or Select object' );
        }
        
			// fetch database
		$database = Nerb::fetch( $this->database );
        
        	// add query to list for polling and replay
        $this->query[] = $query;
        
        	// query the database
        $result =  $database->query( $query );
       
        	// maps the fields as (field=>table.field) for multiple table select statements
        $column_list = mysqli_fetch_fields( $result );
        
			//prepare column list	
        foreach ( $column_list as $column ) {
            $columns[ $column->name ] = $column->table.".".$column->name;
        }

			// instantiate new rowset with mapped fields
        $rows = new NerbDatabaseRowset();
        
        while ( $resultRow = mysqli_fetch_assoc($result) ) {
            $rows->add( new NerbDatabaseRow( $this->database, $this->name, $columns, $resultRow ) );
        }
		
        return $rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns a Rowset containing multiple Rows specified by a select clause
    *
    *   @access     public
    *   @param      string $where
    *   @param      int $limit
    *   @param      int $offset
    *   @return     NerbDatabaseRowset
    *   @throws     NerbError
    */
	public function fetch( string $where, int $limit = null, int $offset = null )
    {
        // make sure the table has a primary key defined
        if ( !$this->primary ) {
            throw new NerbError(  $this->_errorString("Table '<code>".$this->name."</code>' has no primary key" ));
        }
        $query = "SELECT * FROM `".$this->name."` WHERE ".$where.( $limit > 0 ? " LIMIT $limit" : null ).( $offset > 0 ? " OFFSET $offset" : null );
        return $this->_fetch( $query );
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   finds a value based on primary key and returns a Row object
    *
    *   @access     public
    *   @param      mixed $key
    *   @return     NerbDatabaseRow
    *   @throws     NerbError
    */
	public function fetchRow( string $key )
    {
        // make sure the table has a primary key defined
        if ( !$this->primary ) {
            throw new NerbError( $this->_errorString("Table '<code>".$this->name."</code>' has no primary key" ));
        }
        
        //build query strin and execute
        $query = "SELECT * FROM `".$this->name."` WHERE ".$this->primary." = '$key' LIMIT 1";
        $rows = $this->_fetch( $query );
        
        // return first row of the rowset
        return $rows->current();
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




   /**
    *   alias of fetch
    *
    *   @access     public
    *   @param      string $where
    *   @param      int $limit
    *   @param      int $offset
    *   @return     NerbDatabaseRowset
    */
	public function fetchRows( string $where, int $limit = null, int $offset = null )
    {
        return $this->fetch( $where, $limit, $offset );
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   finds a value based on primary key and returns a Row object
    *
    *   @access     public
    *   @param      mixed $key
    *   @return     NerbDatabaseRow
    *   @throws     NerbError
    */
	public function fetchFirstRow( string $where )
    {
        // build query string with forced limit of 1
        $query = "SELECT * FROM `".$this->name."` WHERE ".$where." LIMIT 1";
        
        // execute query
        $rows = $this->_fetch( $query );
        
        // return first result
        return $rows->current();
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




     /**
    *   returns a Rowset containing multiple Rows specified by a where clause
    *
    *   @access     public
    *   @param      string $where 
    *   @return     NerbDatabaseRowset
    *   @throws     NerbError
    */
	public function fetchAll( string $where = null )
    {
        // build query string
        $query = "SELECT * FROM `".$this->name."`".( $where ? " WHERE " . $where : " " );
        
        return $this->_fetch( $query );
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   finds a single row by value that is not a key value
    *
    *   @access     public
    *   @param      string $column column name
    *   @param      string $value
    *   @return     NerbDatabaseRow
    */
	public function fetchRowByValue( string $column, string $value )
    {
        // build query string
        $query = 'SELECT * FROM `'.$this->name.'` WHERE `'.$column.'` = "'.$value.'" LIMIT 1 ';
        
        // fetch
        $rows = $this->_fetch( $query );
        
        // return first row
        return $rows->current();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   finds a single row based on key and returns the row as an array if it exists otherwise returns false
    *
    *   @access     public
    *   @param      string $key unique primary key value
    *   @return     array
    *   @throws     NerbError
    */
	public function fetchArray( string $key )
    {
        // make sure the table has a primary key defined
        if ( !$this->primary ) {
            throw new NerbError(  $this->_errorString("Table '<code>".$this->name."</code>' has no primary key" ));
        }
        
        // build query string
        $query = "SELECT * FROM `".$this->name."` WHERE ".$this->primary." = '$key' LIMIT 1";
        $result = $this->_query( $query );

		// transfer the result to a temp array to free results
		$array = mysqli_fetch_assoc( $result );

		// release results
		mysqli_free_result( $result );

        // return first result as array
        return $array;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns an array containing a column of values which can be limited by a WHERE statement
    *
    *   @access     public
    *   @param      string $column column in the table
    *   @param      string $where SQL where statement
    *   @return     array
    *   @throws     NerbError
    */
	public function fetchColumn( string $column, string $where = null, int $limit = null ): array
    {
        // make sure the column exists
        if ( !in_array( $column, $this->columns ) ) {
            throw new NerbError( $this->_errorString("The column '<code>".$column."</code>' is not in table '<code>".$this->name."</code>'<p>" ));
        }
        
        // build query string
        $query ="SELECT ".$column." FROM ".$this->name.( $where ? " WHERE ".$where : " " ).( $limit ? " LIMIT ".$limit : " " );
        
        // execute query string return array
        return $this->_query2array( $this->_query( $query ), $column );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns an array containing unique values from a column.
    *   ideal for populating select boxes
    *
    *   @access     public
    *   @param      string $column column in the table
    *   @param      string $where SQL where statement
    *   @return     array
    *   @throws     NerbError
    */
	public function fetchUnique( string $column, string $where = null, string $order = "ASC" ): array
    {

        // make sure the table has a primary key defined
        if ( !in_array( $column, $this->columns ) ) {
            throw new NerbError( $this->_errorString("The column '<code>".$column."</code>' is not in table '<code>".$this->name."</code>'<p>" ));
        }
        $query = "SELECT DISTINCT " . $column . " FROM " . $this->name . ( $where ? " WHERE ".$where : null ).( $order ? " ORDER BY " . $column . " " . $order : null );

        // execute query string return array
        return $this->_query2array( $this->_query( $query ), $column );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns a sequential block of rows from a starting point and with a limit
    *
    *   @access     public
    *   @param      int $start (sql offset)
    *   @param      int $limit
    *   @param      string $where
    *   @return     NerbDatabaseRowset
    */
	public function fetchBlock( int $start, int $limit, $where = null )
    {
        // define where block
        if ( !is_null( $where ) ) {
            $where = " WHERE ".$where;
        }

        // define query
        $query = "SELECT * FROM ".$this->name.$where." LIMIT ".$limit." OFFSET " . $start;

        // query and return
		return $this->_fetch( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   alias of fetch Block
    *
    *   @access     public
    *   @param      int $start (sql offset)
    *   @param      int $limit
    *   @param      string $where
    *   @return     NerbDatabaseRowset
    */
	public function fetchPage( int $start, int $limit, $where = null )
    {
	    return $this->fetchBlock( $start, $limit, $where );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   alias of fetch Block
    *
    *   @access     public
    *   @param      int $start (sql offset)
    *   @param      int $limit
    *   @param      string $where
    *   @return     NerbDatabaseRowset
    */
	public function page( int $start, int $limit, $where = null )
    {
	    return $this->fetchBlock( $start, $limit, $where );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns a sequential block of rows from a starting point and with a limit
    *
    *   @access     public
    *   @param      int $start the starting row
    *   @param      int $limit
    *   @param      string $where
    *   @return     NerbDatabaseRowset
    *   @throws     NerbError
    */
	public function fetchJoinedRows( $table, $column, $where = null )
    {

        // error checking block
       if ( !$table || !$column ) {
            throw new NerbError( "Table and Column to join are required" );
        }

        // define where block
        $where = $where ? " WHERE " . $where : null;

        // define query
        $query = "SELECT * FROM `".$this->name."` a INNER JOIN `".$table."` b ON  a.".$column." = b.".$column." ".$where;

        // query and return
        return $this->fetch( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //              !INSERTION & SAVING METHODS

    #################################################################


	/**
	*   creates a prepared statement for saving to the database
	*
	* 
	* 	@access 	protected
	* 	@param 		array $values
	* 	@param 		string $mode (default: "INSERT")
	* 	@return 	string
	*/
	protected function prepare( array $values, $mode = "INSERT" )
    {
        // create temporary array
        $columns = array_keys( $values );
		
		// begin creating sql statment
		$sql = $mode." INTO `".$this->name."`";
		
		$count=0;
		
		foreach( $columns as $value ){
			$cols .= ( $count > 0 ? ', ' : '' ) . '`' . $value . '`';
			$vals .= ( $count > 0 ? ', ' : '' ) . '?';
			$count++;
		}
		
		$sql .= " (" . $cols . ")";
		
		//$values = NerbDatabase::quote(  $values  );
		$sql .= " VALUES (" . $vals . ")";

		return $sql;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*   creates a prepared statement for saving to the database
	*
	* 
	* 	@access 	protected
	* 	@param 		array $values
	* 	@return 	string
	*/
	protected function execute( NerbStatement $statement, array $values )
    {

		// iterate trhrough the $values array 
		foreach( $values as $key => $value ){
			
			// trim off the parenthes 
			$type =  explode( "(",  $this->attribs[$key]["type"] );
			
			// use variable variable to hold the value and bind the data type to it
			$$key = $value;
			
			switch ( $type[0] ){
				
				case "int":
					$data_type = "i";
					break;
				
				case "float":
					$data_type = "d";
					break;
				
				default:
					$data_type = "s";
				
			} // end switch
			
			// actually bind the variables to the statement using variable variable
			$statement->mbind_param( $data_type, $$key);

		} // end foreach
		
		// execute statement
		$statement->execute();

		return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   creates an Update object and saves an array of data into a table
    *
    *   this is designed for saving post variables with the field names mapped to the table names
    *
    *   @access     protected
    *   @param      array $values (values as array field=>value )
    *   @throws     NerbError
    *   @return     int rows affected
    */
	protected function _save( array $values, $mode = "REPLACE" )
    {

        // must be an array
        if ( !is_array( $values ) ) {
            throw new NerbError( "<code>NerbDatabaseTable::save()</code> requires an array to be passed to it" );
        }

		// filter out empty elements
		$values = $this->filterNullValues( $values );
		
        // fetch database
		$database = Nerb::fetch( $this->database );

        // clean values 
        $values = $database->sanitize( $values );

        // create a new prepared statement
        $query_string = $this->prepare( $values,  $mode );
        
        $statement = $database->prepare( $query_string );
        
        $this->execute( $statement, $values );
        
        return $statement;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   wrapper method for saving data
    *
    *   @access     public
    *   @param      array $values values as array( field=>value )
    *   @return     int rows affected
    */
	public function save( array $values )
    {

        $statement = $this->_save( $values, "REPLACE" );
        
        $affected_rows = $statement->affected_rows;
        
        $statement->close();
        
        return $affected_rows;
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   creates an Insert object and populates it and then inserts it into the table
    *
    *   @access     public
    *   @param      array $values values as array( field=>value )
    *   @return     int (insert_id)
    */
	public function insert( array $values ): int
    {
        $statement = $this->_save( $values, "INSERT" );
        
        $insert_id = $statement->insert_id;
        
        $statement->close();
        
        return $insert_id;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   updates a dataset values based on a where statement
    *
    *	the $values array MUST be given in column=>value format and column
    *	MUST exist otherwise it will fail
    *
    *   @access     public
    *   @param      array $values (column => value)
    *   @param      string $where
    *   @throws     NerbError
    *   @return     int rows affected
    */
	public function update( array $values, string $where = "" ): int
    {
		// build initial query string
        $query = "UPDATE ".$this->name." SET ";
        
        // check to see if column exists in the table and format a query string
        foreach( $values as $column => $value ){
	        if( !$this->columnExists( $column ) )
	        	throw new NerbError( $this->_errorString( "The column <code>".$column."</code> is not in the table.<br><code>" ) );

			$query .= $column." = ".( is_string( $value ) ? "'".$value."'" : $value )." ";
        }
        
		if( $where ){
			$query .= " WHERE ".$where;
		}
		
		// run query
		$result = $this->_query( $query );

        // return number of rows changed
        return $result->affected_rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    /**
    *   allows one to set multiple row values based on a where staetment
    *
    *   @access     public
    *   @param      string $where
    *   @param      string $column
    *   @param      string $value
    *   @return     int rows affected
    */
	public function replace( string $column, string $value, string $where )
    {

		// build initial query string
        $query = "UPDATE ".$this->name." SET ";
        
        // check to see if column exists in the table and format a query string
        if( !$this->columnExists( $column ) )
        	throw new NerbError( $this->_errorString( "The column <code>".$column."</code> is not in the table.<br><code>" ) );

		$query .= $column." = ".( is_string( $value ) ? "'".$value."'" : $value )." ";
        
		$query .= " WHERE ".$where;
		
		// run query
		$result = $this->_query( $query );

        // return number of rows changed
        return $result->affected_rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   does a search and replace of all values in a column matching a search string
    *
    *   @access     public
    *   @param      string $where
    *   @param      string $column
    *   @param      string $search
    *   @param      string $replace
    *   @return     int instances replaced
    */
	public function replaceSubstr( $where, $column, $value, $searchString )
    {

        // error checking block
        $count = 0;

        // fetch rows, change values and save
        $rows = $this->fetchRows( $where );
        foreach ( $rows as $row ) {
            $value = str_replace( $search, $replace, $row->$column );
            if ( $value != $row->$column ) {
                $row->$column = $value;
                $row->save();
                $count++;
            }
        }// end foreach

        // return number of rows changed
        return $count;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   swaps the position of two records in a database
    *
    *   @access     public
    *   @param      string $from
    *   @param      string $to
    *   @return     bool true on success
    *   @throws     NerbError
    */
	public function swap( $key_from, $key_to ): bool
    {

		// check to see if the keys are in the table
        if ( !$row_from = $this->fetchRow( $key_from ) ) {
            return false;
        }
        //throw new NerbError( "The key '<code>$from</code>' is not in table " );

        if ( !$row_to = $this->fetchRow( $key_to ) ) {
            return false;
        }
        //throw new NerbError( "The key '<code>$to</code>' is not in table " );

        // extract primary
        $primary = $this->primary;

        // set overwrite and changes primary key
        $row_from->overwrite()->$primary = $key_to;
        $row_to->overwrite()->$primary = $key_from;

        // save changed rows
        $row_from->save();
        $row_to->save();

        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //              !SEARCH METHODS

    #################################################################


// !------------current


    /**
    *   return a rowset with a quick matching column
    *
    *   @access     public
    *   @param      string $column
    *   @param      string $find
    *   @param      int $limit
    *   @return     NerbDatabaseRowset
    */
	public function search( $column, $find, int $limit = null )
    {
        // error checking block
        $where = "`".$column."` LIKE '%".$find."%' ";
        if ( $limit > 0 ) {
            $where .= "LIMIT ".$limit;
        }

        return $this->fetchAll( $where );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   replace values into a table
    *
    *   @access     public
    *   @param      string $column
    *   @param      string $find
    *   @param      string $replace
    *   @param      int $limit
    *   @return     NerbDatabaseRowset
    */
	public function searchReplace( $column, $find, $replace, int $limit = null )
    {

        ini_set( "memory_limit", "128M" );
        // error checking block

        $rows = $this->search( $column, $find, $limit );
        foreach ( $rows as $row ) {
            $row->$column = str_replace( $find, $replace, $row->$column );
            $row->save();
        }

        return $rows->count();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   takes post data and maps the values to valid fields in the table.
    *   e.g. dropping count and other non-table fields
    *
    *
    *   @access     public
    *   @param      array $data values as array( field=>value )
    *   @return     array clean data
    */
	public function extract( $data )
    {
        // create temporary array
        $map = array_flip( $this->columns );
        // merge arrays by key
        $map = array_intersect_key( $data, $map );
        // kill null / empty fields
//			foreach( $map as $key => $value ){
//				if( empty( $value ) ) unset( $map[$key] );
//			}
        return $map;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //                      !DELETION METHODS

    #################################################################



    /**
    *   deletes a selected record from the table based on primary key
    *
    *   @access     public
    *   @param      mixed $key primary key value
    *   @return     int number of rows affected
    *   @throws     NerbError
    */
	public function deleteRow( $key )
    {
        // must have a primary key
        if ( !$this->primary ) {
            throw new NerbError( $this->_errorString( 'This table does not have a primary key defined' ));
        }

        return $this->delete( "`".$this->name."`.`".$this->primary."` = '".$key."' LIMIT 1" );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   deletes a group of selected records from the database
    *
    *   @access     public
    *   @param      string $where WHERE clause to filter by
    *   @return     int number of rows affected
    *   @throws     NerbError
    */
	public function delete( $where )
    {
        $query = "DELETE FROM `".$this->name."` WHERE ".$where;

        // fetch database
		$database = Nerb::fetch( $this->database );

        $result = $database->query( $query );
        
        return mysqli_num_rows( $result ); // number of rows deleted
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   empties the table data
    *
    *   @access     public
    *   @throws     NerbError
    *   @return     int rows affected
    */
	public function deleteAllRows()
    {
        // sets query string
        $query = "TRUNCATE `$this->name`";
        

        // fetch database
		$database = Nerb::fetch( $this->database );

        $result = $database->query( $query );
        
        return mysqli_num_rows( $result );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    #################################################################

    //                   !METADATA METHODS

    #################################################################



    /**
    *   returns the maximum value of a column within specified where clause
    *
    *   @access     public
    *   @param      string $column
    *   @param      string $where
    *   @return     mixed
    */
	public function max( $column, $where = null )
    {
        // define where block
        if ( !is_null( $where ) ) {
            $whereafter = " AND ".$where;
            $where = " WHERE ".$where;
        }

        // define query
        $query = "SELECT MAX( ".$column." ) AS max FROM ".$this->name.$where;

        // fetch database
		$database = Nerb::fetch( $this->database );

        $max = $database->queryArray( $this );
        
        return $max[0];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns row containing the maximum value of a column within specified where clause
    *
    *   @access     	public
    *   @param      	string $column
    *   @param      	string $where
    *   @return         NerbDatabaseRow
    */
	public function maxRow( $column, $where = null )
    {
        // define where block
        if ( !is_null( $where ) ) {
            $whereafter = " AND ".$where;
            $where = " WHERE ".$where;
        }

        // define query
        $query = "SELECT MAX( ".$column." ) AS max FROM ".$this->name.$where;

        // fetch database
		$database = Nerb::fetch( $this->database );

        $max = $database->queryArray( $this );
        
        $query = "SELECT * FROM `".$this->name."` WHERE $column = '".$max['max']."'".$whereafter;
        
        return $database->queryRow( $this );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Returns the number of records in a table
    *
    *   @access     public
    *   @param      string $where WHERE clause to filter by
    *   @throws     NerbError
    *   @return     int
    */
	public function count( $where = null )
    {

        if ( $where ) {
            $where = "WHERE ".$where;
        }

        return $this->queryString( "SELECT COUNT( * ) FROM $this->name $where" );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Returns the number of records in a table
    *
    *   @access     public
    *   @param      string $where WHERE clause to filter by
    *   @throws     NerbError
    *   @return     int
    */
	public function sum( $column, $where = null )
    {

        if ( $where ) {
            $where = "WHERE ".$where;
        }

        // fetch database
		$database = Nerb::fetch( $this->database );

        return $database->queryString( "SELECT SUM( `$col` ) FROM `$this->name` $where" );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Returns the next index number of an autoincremented table
    *
    *   @access     public
    *   @return     int
    */
	public function autoincrement()
    {
        // fetch database
		$database = Nerb::fetch( $this->database );

        $query = "SHOW TABLE STATUS LIKE '$this->name'";

        $row = $database->queryArray( $this );
        
        return $row['Auto_increment'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




} /* end class */
