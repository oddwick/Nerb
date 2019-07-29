<?php
// Nerb Application Framework

/**
 *  Object class for manipulating a table
 *
 *	This class does all of the heavy lifting and provides an interface between the database and user
 *	in the form of an object that can easily be manipulated and returns objects that are easy to use
 * 	in code with minimal additional code.
 *
 * @category    	Nerb
 * @package     	Nerb
 * @subpackage      NerbDatabase
 * @class 			NerbDatabaseTable
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
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
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $database = '';
	
	/**
	 * name
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $name = '';
	
	/**
	 * primary
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $primary = '';
	
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

        // if a NerbDatabase is given, bind to it and contiune
        if ( $database instanceof NerbDatabase || is_subclass_of( $database, 'NerbDatabase' ) ) {
			// fetch the database name for later retrival
			$this->database = $database;
		} else {
	       	throw new NerbError( 'Database adaptor <code>[$database]</code> must be a <code>[NerbDatabase]</code> object.  <code>['.get_class( $database ).']</code> object was passed.' );
		} // end if
	       	

		// bind this object to the table
		$this->bind( $table );
		
        // register this table so that other classes can access it
        Nerb::register( $this, $this->database->name().'.'.$table );
       
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*   returns the last query string
    *
	*   @access     public
	*   @return     string the last element of the query array
	*/
	public function __toString(): string
    {
        //return the last element of the query array
        return end( $this->query );
        
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
        return isset( $this->$var ) ? $this->$var : NULL;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //                !BINDING & MAPPING METHODS

    #################################################################




    /**
    *   returns an array of table field names and descriptions
    *
    *   @access     public
    *   @return     array
    */
    public function info() : array
    {
        // get table data from database
        $result = $this->database->resultsToArray( $this->database->query( 'SHOW COLUMNS FROM `'.$this->name.'` ' ));

        // iterate and pass data with a little better formatting and lower case names
        // to the info array
        foreach ( $result as $columns ) {
	        // change key case
	        $columns = array_change_key_case( $columns );
            $info[ $columns['field'] ] = $columns;
	        $info[ $columns['field'] ]['full_name'] = $table.'.'.$columns['field'];
	        $info[ $columns['field'] ]['null'] = $columns['Null']=='NO'?'NOT NULL':'';
        }// end foreach

        return $info;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



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
        // table must exist to be bound
        if ( !$this->database->isTable( $table ) ) {
            throw new NerbError( 'Cannot bind to table <code>['.$table.']</code> because it does not exist in database.' );
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
        // profile the table
        $this->attribs = $this->info();
        
        // extract columns
        $this->columns = array_keys( $this->attribs );

        foreach ( $this->attribs as $column ) {
            if ( $column['key'] == 'PRI' ) {
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
            throw new NerbError( $this->_errorString( 'Table <code>['.$this->name.']</code> has no primary key' ));
        }
        
        $query = 'SELECT COUNT( * ) FROM `'.$this->name.'` WHERE '.$this->primary.' = \''.$key.'\'';

        return $this->database->query( $query ) > 0 ? true : false;
    
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
	        $extra = '<code>[<ol>';
	        foreach( $this->attribs as $key => $value ){
	        	$extra .='<li>'.$key.' -- '.$value['type'];
	        	if( $value['key'] ) $extra .= '['.$value['key'].']';
	        	$extra .='</li>';
	        } // end foreach
	        
	       $extra .= '</ol>]</code>';
	       $message .= $extra;
        }
        return $message;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    #################################################################

    //            !DATA FETCH AND INSERTION METHODS

    #################################################################




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
            throw new NerbError(  $this->_errorString( 'Table <code>['.$this->name.']</code> has no primary key' ));
        }
        $query = 'SELECT * FROM `'.$this->name.'` WHERE '.$where.( $limit > 0 ? ' LIMIT $limit' : null ).( $offset > 0 ? ' OFFSET $offset' : null );

        return $this->database->fetchRows( $query );
    
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
            throw new NerbError( $this->_errorString( 'Table <code>['.$this->name.']</code> has no primary key' ));
        }
        
        //build query strin and execute
        $query = 'SELECT * FROM `'.$this->name.'` WHERE '.$this->primary.' = \''.$key.'\' LIMIT 1';
       
        $rows = $this->database->fetchRows( $query );
        
        // return first row of the rowset
        return $rows->current();
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   finds a value based on primary key and returns a Row object
    *
    *   @access     public
    *   @param      mixed $key
    *   @return     NerbDatabaseRow
    */
	public function fetchFirstRow( string $where )
    {
        // build query string with forced limit of 1
        $query = 'SELECT * FROM `'.$this->name.'` WHERE '.$where.' LIMIT 1';
        
        // execute query
        $rows = $this->database->fetchRows( $query );
        
        // return first result
        return $rows->current();
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




     /**
    *   returns a Rowset containing multiple Rows specified by a where clause
    *
    *   @access     public
    *   @param      string $where 
    *   @return     NerbDatabaseRowset
    */
	public function fetchAll( string $where = null )
    {
        // build query string
        $query = 'SELECT * FROM `'.$this->name.'`'.( $where ? ' WHERE ' . $where : ' ' );
        
        return $this->database->fetchRows( $query );
    
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
        $query = 'SELECT * FROM `'.$this->name.'` WHERE `'.$column.'` = \''.$value.'\' LIMIT 1 ';
        
        // fetch
        $rows = $this->database->fetchRows( $query );
        
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
            throw new NerbError(  $this->_errorString('Table <code>['.$this->name.']</code> has no primary key' ));
        }
        
        // build query string
        $query = 'SELECT * FROM `'.$this->name.'` WHERE '.$this->primary.' = \''.$key.'\' LIMIT 1';

        $result = $tdatabase->query( $query );

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
            throw new NerbError( $this->_errorString('The column <code>['.$column.']</code> is not in table <code>['.$this->name.']</code><p>' ));
        }
        
        // build query string
        $query ='SELECT '.$column.' FROM '.$this->name.( $where ? ' WHERE '.$where : ' ' ).( $limit ? ' LIMIT '.$limit : ' ' );
  
        // execute query string return array
        return $this->database->resultsToArray( $this->database->query( $query ) );

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
	public function fetchUnique( string $column, string $where = null, string $order = 'ASC' ): array
    {
        // make sure the table has a primary key defined
        if ( !in_array( $column, $this->columns ) ) {
            throw new NerbError( $this->_errorString('The column <code>['.$column.']</code> is not in table <code>['.$this->name.']</code><p>' ));
        }
        $query = 'SELECT DISTINCT ' . $column . ' FROM ' . $this->name . ( $where ? ' WHERE '.$where : null ).( $order ? ' ORDER BY ' . $column . ' ' . $order : null );

        // execute query string return array
        return $this->database->queryArray( $this->database->query( $query ) );
        
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
            $where = ' WHERE '.$where;
        }

        // define query
        $query = 'SELECT * FROM '.$this->name.$where.' LIMIT '.$limit.' OFFSET ' . $start;

        // query and return
		return $this->database->fetchRows( $query );
        
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
            throw new NerbError( 'Table and Column to join are required' );
        }

        // define where block
        $where = $where ? ' WHERE ' . $where : null;

        // define query
        $query = 'SELECT * FROM `'.$this->name.'` a INNER JOIN `'.$table.'` b ON  a.'.$column.' = b.'.$column.' '.$where;

        // query and return
        return $this->fetch( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //              !PREPARED STATEMENTS

    #################################################################


	/**
	*   creates a prepared statement for saving to the database
	*
	* 
	* 	@access 	protected
	* 	@param 		array $values
	* 	@param 		string $mode (default: 'INSERT')
	* 	@return 	string
	*/
	protected function prepare( array $values, $mode = 'INSERT' )
    {
        // create temporary array
        $columns = array_keys( $values );
		
		// begin creating sql statment
		$sql = $mode.' INTO `'.$this->name.'`';
		
		$count=0;
		
		foreach( $columns as $value ){
			$cols .= ( $count > 0 ? ', ' : '' ) . '`' . $value . '`';
			$vals .= ( $count > 0 ? ', ' : '' ) . '?';
			$count++;
		}
		
		$sql .= ' (' . $cols . ')';
		
		//$values = NerbDatabase::quote(  $values  );
		$sql .= ' VALUES (' . $vals . ')';

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
			$type =  explode( '(',  $this->attribs[$key]['type'] );
			
			// use variable variable to hold the value and bind the data type to it
			$$key = $value;
			
			switch ( $type[0] ){
				
				case 'int':
					$data_type = 'i';
					break;
				
				case 'float':
					$data_type = 'd';
					break;
				
				default:
					$data_type = 's';
				
			} // end switch
			
			// actually bind the variables to the statement using variable variable
			$statement->mbind_param( $data_type, $$key);

		} // end foreach
		
		// execute statement
		$statement->execute();

		return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //              !INSERTION & SAVING METHODS

    #################################################################


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
	protected function _save( array $values, $mode = 'REPLACE' )
    {
        // must be an array
        if ( !is_array( $values ) ) {
            throw new NerbError( '<code>[NerbDatabaseTable::save()]</code> requires an array to be passed to it' );
        }

		// filter out empty elements
		$values = $this->filterNullValues( $values );
		
        // clean values 
        $values = $this->database->sanitize( $values );

        // create a new prepared statement
        $query_string = $this->prepare( $values,  $mode );
        
        // pass the statement to the proper database adaptor
        $statement = $this->database->prepare( $query_string );
        
        // execute
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
        $statement = $this->_save( $values, 'REPLACE' );
        
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
	public function insert( array $values ) : int
    {
        $statement = $this->_save( $values, 'INSERT' );
        
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
	public function update( array $values, string $where = '' ) : int
    {
		// build initial query string
        $query = 'UPDATE '.$this->name.' SET ';
        
        // check to see if column exists in the table and format a query string
        $hold = array();
        foreach( $values as $column => $value ){
	        if( !$this->columnExists( $column ) )
	        	throw new NerbError( $this->_errorString( 'The column <code>['.$column.']</code> is not in the table.<br><code>[' ) );
			
			// create a column statement
			$hold[] = '`'.$column.'` = '.( is_string( $value ) ? "'".$value."'" : $value );
        }
        
        // glue parts together
        $query .= implode(', ', $hold );
        
		if( $where ){
			$query .= ' WHERE '.$where;
		}

		// run query
		$result = $this->database->query( $query );
		
        // return number of rows changed
        return $result->affected_rows ? $result->affected_rows : 0;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   allows one to set multiple row values based on a where staetment
    *
    *   @access     public
    *   @param      string $where
    *   @param      string $column
    *   @param      string $value
    *   @return     int rows affected
    *   @throws     NerbError
    */
	public function replace( string $column, string $value, string $where )
    {
		// build initial query string
        $query = 'UPDATE '.$this->name.' SET ';
        
        // check to see if column exists in the table and format a query string
        if( !$this->columnExists( $column ) )
        	throw new NerbError( $this->_errorString( 'The column <code>['.$column.']</code> is not in the table.<br><code>[' ) );

		$query .= $column.' = '.( is_string( $value ) ? '\''.$value.'\'' : $value ).' ';
        
		$query .= ' WHERE '.$where;
		
		// run query
		$result = $this->database->query( $query );

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
	public function replaceSubstr( string $where, string $column, string $value, string $searchString )
    {
        // initialize counter
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
	        //throw new NerbError( 'The key <code>[$from]</code> is not in table ' );
            return false;
        }

        if ( !$row_to = $this->fetchRow( $key_to ) ) {
	        //throw new NerbError( 'The key <code>[$to]</code> is not in table ' );
            return false;
        }

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
    *   return a rowset with a matching column
    *
    *   @access     public
    *   @param      string $column
    *   @param      string $find
    *   @param      int $limit
    *   @return     NerbDatabaseRowset
    */
	public function find( string $column, string $searchString, int $limit = null )
    {
        // error checking block
        $where = "`$column` LIKE '%$searchString%' ";
        if ( !empty( $limit ) ) {
            $where .= 'LIMIT '.$limit;
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
	public function searchAndReplace( string $column, string $search, string $replace, int $limit = null )
    {
		// increase memory limit
        ini_set( 'memory_limit', '128M' );
        
        // error checking block

        $rows = $this->search( $column, $search, $limit );
        
        foreach ( $rows as $row ) {
            $row->$column = str_replace( $search, $replace, $row->$column );
            $row->save();
        }
        
        $count = $rows->count();
		
		unset( $rows );
		
        return $count;
        
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

        return $this->deleteRows( '`'.$this->name.'`.`'.$this->primary.'` = \''.$key.'\' LIMIT 1' );
                
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   deletes a group of selected records from the database
    *
    *   @access     public
    *   @param      string $where WHERE clause to filter by
    *   @return     int number of rows affected
    */
	public function deleteRows( $where )
    {
        $query = 'DELETE FROM `'.$this->name.'` WHERE '.$where;

        $result = $this->database->query( $query );
        
        return $this->database->affected_rows(); // number of rows deleted
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



//!rename?---------
    /**
    *   empties the table data
    *
    *   @access     public
    *   @return     int rows affected
    */
	public function deleteAllRows()
    {
        // sets query string
        $query = 'TRUNCATE `$this->name`';
        
        $result = $this->database->query( $query );
        
        return $this->database->affected_rows();
        
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
            $whereafter = ' AND '.$where;
            $where = ' WHERE '.$where;
        }

        // define query
        $query = 'SELECT MAX( '.$column.' ) AS max FROM '.$this->name.$where;

        $max = $this->database->queryArray( $this );
        
        return first($max);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



//!questionable----------------
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
            $whereafter = ' AND '.$where;
            $where = ' WHERE '.$where;
        }

        // define query
        $query = 'SELECT MAX( '.$column.' ) AS max FROM '.$this->name.$where;

        $max = $this->database->queryArray( $this );
        
        $query = 'SELECT * FROM `'.$this->name.'` WHERE $column = \''.$max['max'].'\''.$whereafter;
        
        return $this->database->queryRow( $this );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Returns the number of records in a table
    *
    *   @access     public
    *   @param      string $where WHERE clause to filter by
    *   @return     int
    */
	public function count( string $where = null ): int
    {
		// create where statement	
        if ( $where ) $where = ' WHERE '.$where;
		$query = 'SELECT COUNT( * ) FROM '.$this->name.$where; 

		// fetch result	
        $result = $this->database->count( $query  );
        $count = mysqli_fetch_array( $result );

        // return count value
        return $count[0];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Returns the sum of a numeric column
    *
    *   @access     public
    *   @param      string $where WHERE clause to filter by
    *   @return     int
    */
	public function sum( string $column, string $where = null )
    {
		// build where statement
        if ( $where ) $where = 'WHERE '.$where;
        
        return $this->database->queryString( 'SELECT SUM( `$col` ) FROM `$this->name` '.$where );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Returns the next index number of an autoincremented table
    *
    *   @access     public
    *   @return     int
    */
	public function autoincrement()
    {
		// build query
        $query = 'SHOW TABLE STATUS LIKE \''.$this->name.'\'';

        // fetch result
        $result = $database( $query );
        
      
        return $result['Auto_increment'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




} /* end class */
