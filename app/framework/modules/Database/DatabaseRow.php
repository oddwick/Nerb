<?php
// Nerb Application Framework
namespace nerb\framework;


/**
 *  Container object for database data allowing a table row to be treated like an object or an array
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      Database
 * @class 			DatabaseRow
 * @implements 		Iterator
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019 * 
 * @see             Database
 * @see             DatabaseTable
 * @see             DatabaseRowset
 *
 * @todo
 *
 */

class DatabaseRow implements \Iterator
{

    /**
     * params
     *
     * ( default value: array() )
     *
     * @var array
     * @access protected
     */
    protected $params = array();

    /**
     * data
     *
     * ( default value: array() )
     *
     * @var array
     * @access protected
     */
    protected $data = array();

    /**
     * columns
     *
     * ( default value: array() )
     *
     * @var array
     * @access protected
     */
    protected $columns = array();

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
     * table
     *
     * ( default value: null )
     *
     * @var mixed
     * @access protected
     */
    protected $table = NULL;

    /**
     * primary_key_lock
     *
     * ( default value: true )
     *
     * @var bool
     * @access protected
     */
    protected $primary_key_lock = TRUE;

    /**
     * read_only
     * 
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $read_only = TRUE;

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
     *   @param      string $database handle
     *   @param      string $table handle
     *   @param      array $columns table column as column=>table.column
     *   @param      array $data
     *   @return     void
     */
    public function __construct( string $database, string $table, array $columns, array $data )
    {
        $this->database = $database;
        $this->table = $table;
        $this->columns = $columns;
        $this->data = $data;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  returns an array of values
     * 
     * 	@access public
     * 	@param bool $return_all_columns (default: false)
     * 	@return array
     */
    public function __toArray(bool $return_all_columns = false): array
    {
        if ($return_all_columns) {
            return $this->data;
        } else {
            return array_filter($this->data, function($value) {
                return $value !== '';
            } );
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    #################################################################

    //            !GET AND SET

    #################################################################



    /**
     *   returns a value by key
     *
     *   @access     public
     *   @param      string $field (field name)
     *   @return     mixed
     *   @throws     Error
     */
    public function __get( string $field ) 
    {
        // check to see if field exists
        if ( !array_key_exists( $field, $this->columns ) ) {
            throw new Error( 'Column <code>'.$field.'</code> does not exist.<br /><br /><code>['.implode( ', ', $this->columns() ).']</code>' );
        }
        return $this->data[$field];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   seter that changes value of dataset
     *
     *   @access     public
     *   @param      string $field (field name)
     *   @return     string $value (old value)
     *   @throws     Error
     */
    public function __set( string $field, string $value )
    {
        // error checking
        // ensure the field is a valid column
        if ( !array_key_exists( $field, $this->columns ) ) {
            throw new Error( 'Column <code>'.$field.'</code> does not exist.<br /><br /><code>['.implode( ', ', $this->columns() ).']</code>' );
        }

        // primary key cant be modified
        if ( $field == $this->table->primary && !$this->primary_key_lock ) {
            throw new Error( 'Primary key <code>'.$field.'</code> value cannot be changed' );
        }

        // capture old value
        $old = $this->data[$field];

        // new value
        $this->data[$field] = $value;

        // return old value
        return $old;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    #################################################################

    //            !ITERATOR

    #################################################################



    /**
     *   returns the current element of the row
     *
     *   @access     public
     *   @return     mixed
     */
    public function current()
    {
        // make sure that the pointer points to a valid row
        if ($this->valid()) {
            return $this->data[$this->pointer];
        } else {
            return FALSE;
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     *   validates the position of the pointer
     *
     *   @access     public
     *   @return     bool
     */
    public function valid(): bool
    {
        return $this->pointer < count($this->data);
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   advances the pointer
     *
     *   @access     public
     *   @return     int
     */
    public function next(): int
    {
        return ++$this->pointer;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   decrements the pointer
     *
     *   @access     public
     *   @return     int
     */
    public function prev(): int
    {
        return --$this->pointer;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns the current position of the pointer
     *
     *   @access     public
     *   @return     int
     */
    public function key(): int
    {
        return $this->pointer;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   resets the pointer to 0
     *
     *   @access     public
     *   @return     int
     */
    public function rewind(): int
    {
        return $this->pointer = 0;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !DATA MANIPULATION

    #################################################################



    /**
     *   sets the primary_key_lock flag preventing primary key from being changed
     *
     *   @access     public
     *   @return     object self
     */
    public function lock() : self
    {
        $this->primary_key_lock = TRUE;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * sets the primary_key_lock flag allowing the primary key to be changed
     *
     * @access public
     * @return void
     */
    public function unlock() : self
    {
        $this->primary_key_lock = FALSE;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   saves changes made to a row
     *
     *   @access     public
     *   @return     object self
     */
    public function save() : self
    {
        // fetch objects
        $table = Nerb::registry()->fetch($this->database.'.'.$this->table);

        // send data to table for saving
        $table->save($this->data);

        // return
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

} /* end class */
