<?php
// Nerb Application Framework


/**
 *  Container object for holding Row objects and accessing them
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      NerbDatabase
 * @class           NerbDatabaseRowset
 * @implements      Iterator
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019 
 * @see             NerbDatabase
 * @see             NerbDatabaseTable
 * @see             NerbDatabaseRow
 *
 * @todo
 *
 */

class NerbDatabaseRowset implements Iterator
{

    /**
     * rows
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $rows = array();

    /**
     * pointer
     *
     * ( default value: 0 )
     *
     * @var int
     * @access protected
     */
    protected $pointer = 0;


    /**
    *   Constructor -- this object is only intended to be called by a Database object and is not for standing alone
    *
    *   @access     public
    *   @return     void
    */
    public function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns the current element of the rowset
    *
    *   @access     public
    *   @param      string $index
    *   @return     NerbDatabaseRow
    */
    public function __get( $index )
    {
        return $this->rows[$index];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns a listing of columns
    *
    *   @access     public
    *   @return     array
    */
    public function columns()
    {
        return $this->_columns;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Adds a row to the rowset
    *
    *   @access     public
    *   @param      NerbDatabaseRow $row NerbDatabaseRow object
    *   @throws     NerbError
    *   @return     NerbDatabaseRowset
    */
    public function add( $row )
    {
        if ( $row instanceof NerbDatabaseRow || is_subclass_of( $row, 'NerbDatabaseRow' ) ) {
            $this->rows[] = $row;
            return $this;
        } else {
            throw new NerbError( '<code>$row</code> must be an instance of Row object' );
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns count of rows in rowset
    *
    *   @access     public
    *   @return     int
    */
    public function count()
    {
        return count( $this->rows );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns the current element of the rowset
    *
    *   @access     public
    *   @return     NerbDatabaseRow
    */
    public function current()
    {
        // make sure that the pointer points to a valid row
        if ( $this->valid() ) {
            return $this->rows[$this->pointer];
        } else {
            return false;
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   validates the position of the pointer
    *
    *   @access     public
    *   @return     bool
    */
    public function valid()
    {
        return $this->pointer<count( $this->rows );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   advances the pointer
    *
    *   @access     public
    *   @return     int
    */
    public function next()
    {
        return ++$this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   decrements the pointer
    *
    *   @access     public
    *   @return     int
    */
    public function prev()
    {
        return --$this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns the current position of the pointer
    *
    *   @access     public
    *   @return     int
    */
    public function key()
    {
        return $this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   resets the pointer to 0
    *
    *   @access     public
    *   @return     int
    */
    public function rewind()
    {
        return $this->pointer=0;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   fetches a specific row
    *
    *   @access     public
    *   @param      int $key
    *   @return     NerbDatabaseRow
    */
    public function fetch( $key )
    {
        if ( $this->valid( $key ) ) {
            return $this->rows[$key];
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns the entire $rows as an array
    *
    *   @access     public
    *   @return     array
    */
    public function fetchAll()
    {
        return $this->rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns all data as an array
    *
    *   @access     public
    *   @return     array
    */
    public function fetchArray()
    {
        $rows = $this->fetchAll();
        foreach ( $rows as $row ) {
            $data[] = $row->__toArray();
        }
        return $data;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
} /* end class */
