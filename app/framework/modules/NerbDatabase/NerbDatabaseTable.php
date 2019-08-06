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
     *   Constructor initiates object
     *
     *   Creates a table instance and if a table is given, will automatically map the columns and metadata to variables for
     *   easy access.
     *
     *   @access     public
     *   @param      NerbDatabase $database
     *   @param      string $table
     *   @return     NerbDatabaseTable
     */
    public function __construct( NerbDatabase $database, string $table )
    {
        // bind this object to the table
        $this->_bind( $database, $table );
        
        // register this table so that other classes can access it
        Nerb::register( $this, $database->handle().'.'.$table );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //                !BINDING & FORMATTING METHODS

    #################################################################



    /**
     *   makes sure that the table exists in the database and initiates the mapping process
     *
     *   @access     public
     *   @param      string $table
     *   @return     object self
     *   @throws     NerbError
     */
    protected function _bind( NerbDatabase $database, string $table ) : self
    {
        // asign database table
        $this->database = $database;
		
        // errorchecking to make sure table exists to be bound
        if ( !$this->database->isTable( $table ) ) {
            throw new NerbError( 'Cannot bind to table <code>['.$table.']</code> because it does not exist in database.' );
        } // end if

        // set the table name
        $this->name = $table;
		
        // create a schema and get table attributes
        $schema = new NerbSchema( $this->database );
        $this->primary = $schema->primary( $table );
        $this->attribs = $schema->describe( $table );
        
        
        // extract columns
        $this->columns = array_keys( $this->attribs );

        return $this;
        
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
	        $extra = '<code><ol>';
	        foreach( $this->attribs as $key => $value ){
	        	$extra .='<li>'.$key.' -- '.$value['type'];
	        	if( $value['key'] ) {
	        	    $extra .= '['.$value['key'].']';
	        	}
	        	$extra .='</li>';
	        } // end foreach
	        
	       $extra .= '</ol></code>';
	       $message .= $extra;
        }
        return $message;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _where function.
     * 
     * @access protected
     * @param string $where
     * @param int $limit (default: null)
     * @param int $offset (default: null)
     * @return string
     */
    protected function _where( string $where, int $limit = NULL, int $offset = NULL ): string 
    {
        $where = $where ? ' WHERE '.$where : NULL;
        $limit = $limit > 0 ? ' LIMIT '.$limit.( $offset ? " OFFSET ".$offset : NULL ) : NULL;
       
        return $where.$limit;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //                !TABLE INFO METHODS

    #################################################################



    /**
     *   returns a listing of columns
     *
     *   @access     public
     *   @return     array
     */
    public function primary(): string
    {
        return $this->primary;
        
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
    public function isColumn( string $column ): bool
    {
        return in_array( $column, $this->columns );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns a listing of table attributes
     *
     *   @access     public
     *   @return     array
     */
    public function attribs(): array
    {
        return $this->attribs;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Returns the next index number of an autoincremented table
     *
     *   @access     public
     *   @return     int
     */
    public function autoincrement() : int
    {
        // fetch result
        $result = $this->database->queryArray("SHOW TABLE STATUS LIKE '$this->name'");
      
        return $result['Auto_increment'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //   !STANDARD TABLE METHODS

    #################################################################



    /**
     *   returns the minimum value of a column within specified where clause
     *
     *   @access     public
     *   @param      string $column
     *   @param      string $where
     *   @return     mixed
     */
    public function min( $column, $where = '' )
    {
        // define query
        return $this->database->simpleQuery( "SELECT MIN(`$column`) AS min FROM `$this->name`".$this->_where( $where ) );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     *   returns the maximum value of a column within specified where clause
     *
     *   @access     public
     *   @param      string $column
     *   @param      string $where
     *   @return     mixed
     */
    public function max( $column, $where = '' )
    {
        // define query
        return $this->database->simpleQuery( "SELECT MAX(`$column`) AS max FROM `$this->name`".$this->_where( $where ) );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     *   Returns the number of records in a table
     *
     *   @access     public
     *   @param      string $where WHERE clause to filter by
     *   @return     int
     */
    public function count( string $where = '' ): int
    {
        // fetch result	
        return $this->database->simpleQuery( "SELECT COUNT( * ) FROM `$this->name`".$this->_where( $where ) );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * sum function.
     * 
     * Returns the sum of a numeric column
     *
     * @access public
     * @param string $column
     * @param string $where (default: '')
     * @return void
     */
    public function sum( string $column, string $where = '' )
    {
        // build where statement
        return $this->database->simpleQuery( "SELECT SUM( `$column` ) FROM `$this->name`".$this->_where( $where ) );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !DATA FETCH METHODS

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
	public function fetch( string $where = '', int $limit = NULL, int $offset = NULL )
    {
		// build query string
        $query = "SELECT * FROM `$this->name`".$this->_where( $where, $limit, $offset );

        return $this->database->fetch( $query );
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns a single row specified by a select clause
     *
     *   @access     public
     *   @param      string $where
     *   @return     NerbDatabaseRow
     *   @throws     NerbError
     */
    public function fetchRow( string $where = '' )
    {
        // build query string
        $query = "SELECT * FROM `$this->name`".$this->_where( $where, $limit, $offset );

        $result = $this->database->fetch( $query );
        return $result->current();
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   finds a value based on primary key and returns a Row object
     *
     *   @access     public
     *   @param      mixed $key
     *   @return     NerbDatabaseRow
     *   @throws     NerbError
     */
    public function key( string $key )
    {
        // make sure the table has a primary key defined
        if ( !$this->primary ) {
            throw new NerbError( $this->_errorString( "Table <code>[$this->name]</code> has no primary key" ));
        }
        
        //build query strin and execute
        $query = "SELECT * FROM `$this->name` WHERE `$this->primary` = '$key' LIMIT 1";
       
        $rows = $this->database->fetch( $query );
        
        // return first row of the rowset
        return $rows->current();
   
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
	public function fetchColumn( string $column, string $where = '', int $limit = NULL ) : array
    {
        // make sure the column exists
        if ( !$this->isColumn( $column ) ) {
            throw new NerbError( $this->_errorString("The column <code>[$column]</code> is not in table <code>[$this->name]</code><p>" ));
        }
        
        // build query string
        $query ="SELECT $column FROM $this->name".$this->_where( $where, $limit );
  
        // execute query string return array
        return $this->database->queryArray( $query );

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
    public function fetchUnique( string $column, string $where = '', string $order = 'ASC' ) : array
    {
        // make sure the table has a primary key defined
        if ( !$this->isColumn( $column ) ) {
            throw new NerbError( $this->_errorString('The column <code>['.$column.']</code> is not in table <code>['.$this->name.']</code><p>' ));
        }
        $query = "SELECT DISTINCT `$column` FROM `$this->name`".$this->_where( $where, $limit, 0 );
        $order = $order ? " ORDER BY `$column` ".$order : NULL;

        // execute query string return array
        return $this->database->queryArray( $query.$order );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



#TODO: test and add errorchecking @Dexter Oddwick [7/31/19]
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
    public function fetchJoinedRows( string $table, string $column, $where = '' )
    {
        // error checking block
        if ( !$table || !$column ) {
            throw new NerbError( 'Table and Column to join are required' );
        }

        // define where block
        $where = $where ? ' WHERE ' . $where : NULL;

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
        $cols = '';
        $vals = '';
		
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
     *   @return     mysqli statement
     */
    protected function _save( array $values, $mode = 'REPLACE' )
    {
        // filter out empty elements
        $values = $this->database->filterNullValues( $values );
		
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
    public function save( array $values ) : int
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
    public function deleteRow( $key ) : int
    {
        // must have a primary key
        if ( !$this->primary ) {
            throw new NerbError( $this->_errorString( 'This table does not have a primary key defined' ));
        }

        return $this->deleteRows( "`$this->primary` = '$key' LIMIT 1" );
                
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   deletes a group of selected records from the database
     *
     *   @access     public
     *   @param      string $where WHERE clause to filter by
     *   @return     int number of rows affected
     */
    public function deleteRows( string $where ) : int
    {
        // execute and return rows affected
        return $this->database->execute( "DELETE FROM `$this->name` WHERE $where" );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
