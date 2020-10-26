<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 *  Object class for manipulating a table
 *
 *	This class does all of the heavy lifting and provides an interface between the database and user
 *	in the form of an object that can easily be manipulated and returns objects that are easy to use
 * 	in code with minimal additional code.
 *
 * @category    	Nerb
 * @package     	Nerb
 * @subpackage      Database
 * @class 			TableWrite
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */



#todo: fetchRow (  where, limit ); instead of fetch first row
class TableWrite extends TableRead
{



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
		
        //$values = Database::quote(  $values  );
        $sql .= ' VALUES (' . $vals . ')';

        return $sql;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   creates a prepared statement for saving to the database
     *
     * 
     * 	@access 	protected
     * 	@param 		array $values
     * 	@return 	self
     */
    protected function execute( Statement $statement, array $values ) : self
    {
        // iterate trhrough the $values array 
        foreach( $values as $key => $value ){
			
            // trim off the parenthes - eg varchar(255) etc
            $type =  explode( '(',  $this->attribs[$key]['type'] );

            // use variable variable to hold the value and bind the data type to it
            ${$key} = $value;
			
            // actually bind the variables to the statement using variable variable
            $statement->mbind_param( $this->data_type[ $type[0] ], ${$key});

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
     *   @throws     Error
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
     *   updates data set
     *
     *   @access     public
     *   @param      string $values
     *   @param      string $where
     *   @return     int rows affected
     */
    public function update( string $values, string $where ) : int
    {
        // create sql statement
        $sql = 'UPDATE `'.$this->name.'` SET '.$values.' WHERE '.$where;
        
        // execute and return rows affected
        return $this->database->execute( $sql );
        
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
     *   alias of deleteRow
     *
     *   @access     public
     *   @param      mixed $key primary key value
     *   @return     int number of rows affected
     *   @throws     Error
     */
    public function delete( $key ) : int
    {
        return $this->deleteRow( $key );
                
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   deletes a selected record from the table based on primary key
     *
     *   @access     public
     *   @param      mixed $key primary key value
     *   @return     int number of rows affected
     *   @throws     Error
     */
    public function deleteRow( $key ) : int
    {
        // must have a primary key
        if ( !$this->primary ) {
            throw new Error( $this->_errorString( 'This table does not have a primary key defined' ));
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
