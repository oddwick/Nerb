<?php
// Nerb Application Framework
namespace nerb\framework;


/**
 *  Container object for holding Row objects and accessing them
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      Database
 * @class           Rowset
 * @implements      Iterator
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019 
 * @see             Database
 * @see             Table
 * @see             Row
 *
 * @todo
 *
 */

class Rowset implements \Iterator
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
     *   @param      int $index
     *   @return     Row
     */
    public function __get( int $index )
    {
        return $this->rows[$index];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns a listing of columns
     *
     *   @access     public
     *   @return     array
     */
    public function columns() : array
    {
        return $this->_columns;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Adds a row to the rowset
     *
     *   @access     public
     *   @param      Row $row Row object
     *   @throws     Error
     *   @return     self
     */
    public function add( $row ) : self
    {
       
        if ( $row instanceof Row || is_subclass_of( $row, 'Row' ) ) {
            $this->rows[] = $row;
            return $this;
        } else {
            throw new Error( '<code>$row</code> must be an instance of Row object' );
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns count of rows in rowset
     *
     *   @access     public
     *   @return     int
     */
    public function count() : int
    {
        return count($this->rows);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns the current element of the rowset
     *
     *   @access     public
     *   @return     Row
     */
    public function current()
    {
        // make sure that the pointer points to a valid row
        if ($this->valid()) {
            return $this->rows[$this->pointer];
        }
                
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns the first element of the rowset
     *
     *   @access     public
     *   @return     Row
     */
    public function first()
    {
        $this->pointer = 0;
        // make sure that the pointer points to a valid row
    
        return $this->current();
        
                
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   validates the position of the pointer
     *
     *   @access     public
     *   @return     bool
     */
    public function valid() : bool
    {
        return $this->pointer < count($this->rows);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   advances the pointer
     *
     *   @access     public
     *   @return     int
     */
    public function next() : int
    {
        return ++$this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   decrements the pointer
     *
     *   @access     public
     *   @return     int
     */
    public function prev() : int
    {
        return --$this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns the current position of the pointer
     *
     *   @access     public
     *   @return     int
     */
    public function key() : int
    {
        return $this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   resets the pointer to 0
     *
     *   @access     public
     *   @return     int
     */
    public function rewind() : int
    {
        return $this->pointer = 0;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   fetches a specific row
     *
     *   @access     public
     *   @param      int $key
     *   @return     Row
     */
    public function fetch( int $key )
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
    public function fetchAll() : array
    {
        return $this->rows;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns all data as an array
     *
     *   @access     public
     *   @return     array
     */
    public function fetchArray() : array
    {
        $rows = $this->fetchAll();
        foreach ($rows as $row) {
            $data[] = $row->__toArray();
        }
        return $data;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
} /* end class */
