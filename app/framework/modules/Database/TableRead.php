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
 * @class 			TableRead
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */



#todo: fetchRow (  where, limit ); instead of fetch first row
class TableRead extends Table
{


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
        return $this->database->simpleQuery( "SELECT MIN(`$column`) AS min FROM `$this->name`".$this->where( $where ) );
        
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
        return $this->database->simpleQuery( "SELECT MAX(`$column`) AS max FROM `$this->name`".$this->where( $where ) );
        
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
        return $this->database->simpleQuery( "SELECT COUNT( * ) FROM `$this->name`".$this->where( $where ) );
        
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
        return $this->database->simpleQuery( "SELECT SUM( `$column` ) FROM `$this->name`".$this->where( $where ) );
        
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
    *   @return     Rowset
    *   @throws     Error
    */
	public function fetch( string $where = '', int $limit = NULL, int $offset = NULL )
    {
		// build query string
        $query = "SELECT * FROM `$this->name`".$this->where( $where, $limit, $offset );

        return $this->database->fetch( $query );
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns a single row specified by a select clause
     *
     *   @access     public
     *   @param      string $where
     *   @return     Row
     *   @throws     Error
     */
    public function fetchRow( string $where = '' )
    {
        // build query string
        $query = "SELECT * FROM `$this->name`".$this->where( $where, 1 );

        $result = $this->database->fetch( $query );
        
        return $result->current();
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   finds a value based on primary key and returns a Row object
     *
     *   @access     public
     *   @param      mixed $key
     *   @return     Row
     *   @throws     Error
     */
    public function row( string $key )
    {
        // make sure the table has a primary key defined
        if ( !$this->primary ) {
            throw new Error( $this->_errorString( "Table <code>[$this->name]</code> has no primary key" ));
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
    *   @throws     Error
    */
	public function fetchColumn( string $column, string $where = '', int $limit = NULL ) : array
    {
        // make sure the column exists
        if ( !$this->isColumn( $column ) ) {
            throw new Error( $this->_errorString("The column <code>[$column]</code> is not in table <code>[$this->name]</code><p>" ));
        }
        
        // build query string
        $query ="SELECT $column FROM $this->name".$this->where( $where, $limit );
  
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
     *   @throws     Error
     */
    public function fetchUnique( string $column, string $where = '', string $order = 'ASC' ) : array
    {
        // make sure the table has a primary key defined
        if ( !$this->isColumn( $column ) ) {
            throw new Error( $this->_errorString('The column <code>['.$column.']</code> is not in table <code>['.$this->name.']</code><p>' ));
        }
        $query = "SELECT DISTINCT `$column` FROM `$this->name`".$this->where( $where );
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
     *   @return     Rowset
     *   @throws     Error
     */
    public function fetchJoinedRows( string $table, string $column, $where = '' )
    {
        // error checking block
        if ( !$table || !$column ) {
            throw new Error( 'Table and Column to join are required' );
        }

        // define where block
        $where = $where ? ' WHERE ' . $where : NULL;

        // define query
        $query = 'SELECT * FROM `'.$this->name.'` a INNER JOIN `'.$table.'` b ON  a.'.$column.' = b.'.$column.' '.$where;

        // query and return
        return $this->fetch( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
