<?php
// Nerb Application Framework


/**
 *  Container object for database data allowing a table row to be treated like an object or an array
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      NerbDatabase
 * @class 			NerbDatabaseRow
 * @implements 		Iterator
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2017
 * @license         https://www.oddwick.com
 * @see             NerbDatabase
 * @see             NerbDatabaseTable
 * @see             NerbDatabaseRowset
 *
 * @todo
 *
 */

class NerbDatabaseRow implements Iterator
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
    protected $table = null;

    /**
     * primary_key_lock
     *
     * ( default value: true )
     *
     * @var bool
     * @access protected
     */
    protected $primary_key_lock = true;

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
    public function __toArray( bool $return_all_columns = false ): array
    {
        if ( $return_all_columns ) {
            return $this->data;
        } else {
            return array_filter( $this->data, function ( $value ) {
                return $value !== '';
            } );
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   alias of __toArray()
    *
    *   @access     public
    *   @return     array
    */
    public function dump(): array
    {
        return $this->__toArray();
        
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
    *   @throws     NerbError
    */
    public function __get( string $field )
    {
        // check to see if field exists
        if ( !array_key_exists( $field, $this->columns ) ) {
            throw new NerbError( 'Column <code>'.$field.'</code> does not exist.<br /><br /><code>['.implode( ', ', $this->columns() ).']</code>' );
        }
        return $this->data[$field];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   seter that changes value of dataset
    *
    *   @access     public
    *   @param      string $field (field name)
    *   @return     string $value (old value)
    *   @throws     NerbError
    */
    public function __set( string $field, string $value )
    {
        // error checking
        // ensure the field is a valid column
        if ( !array_key_exists( $field, $this->columns ) ) {
            throw new NerbError( 'Column <code>'.$field.'</code> does not exist.<br /><br /><code>['.implode( ', ', $this->columns() ).']</code>' );
        }

        // primary key cant be modified
        if ( $field == $this->table->primary && !$this->primary_key_lock ) {
            throw new NerbError( 'Primary key <code>'.$field.'</code> value cannot be changed' );
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
        if ( $this->valid() ) {
            return $this->data[$this->pointer];
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
    public function valid(): bool
    {
        return $this->pointer < count( $this->data );
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




    /**
    *   returns an array of the columns of the row
    *
    *   @access     public
    *   @return     array
    */
    public function columns(): array
    {
        return $this->columns;
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
    public function lock()
    {
        $this->primary_key_lock = true;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * sets the primary_key_lock flag allowing the primary key to be changed
     *
     * @access public
     * @return void
     */
    public function unlock()
    {
        $this->primary_key_lock = false;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   saves changes made to a row
    *
    *   @access     public
    *   @return     object self
    */
    public function save()
    {
        // fetch objects
        $database = Nerb::fetch( $this->database );
        $table = Nerb::fetch( $this->database.'.'.$this->table );

        // send data to table for saving
        $table->save( $this->data );

        // return
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Duplicates a row in the table.  The duplicated row is then returned
    *   tables without autoincrement fields must have the key specified
    *
    *   @access     public
    *   @param      string $key allows the key to be set manually
    *   @return     NerbDatabaseRow
    */
    public function duplicate( string $key = null )
    {
		// fetch database
        $table = Nerb::fetch( $this->database.'.'.$this->table );

        // move row data into temp variable
        $data = $this->data;

        // if a key is given, set key value
        if ( $key ) {
            $data[ $table->primary ] = $key;

            // else unset the key and get autoincrement value if no key is
            // specified
        } else {
            unset( $data[ $table->primary ] );
        }
        

        // inserts the data and retrieves the inserted row
        $id = $table->insert( $data );

        // returns the new key of the inserted row
        return $id;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   alias of duplicate
    *
    *   @access     public
    *   @param      string $key allows the key to be set manually
    *   @return     NerbDatabaseRow
    */
    public function insert( string $key = null )
    {
        return $this->duplicate( $key );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




#TODO: finish and test this method.  might need it for data transfers @Dexter Oddwick [12/25/17]
    /**
    *   saves changes made to a row into another table.  if a key already exists, it will overwrite the data, otherwise it will
    *   insert it as a new row with a crisp new key
    *
    *   note:  the table structures MUST match otherwise the operation WILL FAIL
    *
    *   @access     public
    *   @param      string $insert_table ( table to save row into )
    *   @return     mixed ( primary key from insert into new table )
    */
    public function saveInto( string $insert_table )
    {

        // fetch objects
        $database = Nerb::fetch( $this->database );

        $source = Nerb::fetch( $this->table );

        // check to see if table exists or throw an error
        if ( !$database->isTable( $insert_table ) ) {
            throw new NerbError( 'Can not save row to table <code>'.$table.'</code> because table does not exist in database.' );
        } // endif

        // check to see if table is open and registered
        if ( Nerb::isRegistered( $this->database.'.'.$insert_table ) ) {
            $destination = Nerb::fetch( $this->database.'.'.$insert_table );
        } else {
            $destination = new Nerb_database_table( $this->database, $insert_table );
        }

        // save into the new table
        if ( $destination->exists( $this->data[ $source->primary ] ) ) {
            $destination->save( $this->data );
            return $source->primary;
        } else {
            $data = $this->__toArray();
            unset( $data[ $source->primary ] );
            return $destination->insert( $data );
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

} /* end class */
