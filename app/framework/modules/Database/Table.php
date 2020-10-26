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
 * @class 			Table
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */



#todo: fetchRow (  where, limit ); instead of fetch first row
class Table
{

    /**
     * database
     * 
     * (default value: '')
     * 
     * @var Database
     * @access protected
     */
    protected $database;
	
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
     * data_type - default is 's'
     * 
     * (default value: array( 's', 'int' => 'i','float' => 'd','string' => 's'))
     * 
     * @var string
     * @access protected
     */
    protected $data_type = array( 
	    'tinyint' => 'i',
	    'smallint' => 'i',
	    'mediumint' => 'i',
	    'int' => 'i',
	    'bigint' => 'i',
	    'date' => 'i',
	    'datetime' => 'i',
	    'timestamp' => 'i',
	    'float' => 'd',
	    'double' => 'd',
	    'decimal' => 'd',
	    'string' => 's', 
	    'char' => 's', 
	    'varchar' => 's', 
	    'text' => 's', 
	    'mediumtext' => 's',
	    'longtext' => 's',
    );



    /**
     *   Constructor initiates object
     *
     *   Creates a table instance and if a table is given, will automatically map the columns and metadata to variables for
     *   easy access.
     *
     *   @access     public
     *   @param      Database $database
     *   @param      string $table
     *   @return     Table
     */
    public function __construct( Database $database, string $table, bool $register = false )
    {
        // bind this object to the table
        $this->bind( $database, $table );
        
        // register this table so that other classes can access it
        if( $register ) Nerb::registry()->register( $this, $database->handle().'.'.$table );

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
     *   @throws     Error
     */
    protected function bind( Database $database, string $table ) : self
    {
        // asign database table
        $this->database = $database;
		
        // errorchecking to make sure table exists to be bound
        if ( !$this->database->isTable( $table ) ) {
            throw new Error( 'Cannot bind to table <code>['.$table.']</code> because it does not exist in database.' );
        } // end if

        // set the table name
        $this->name = $table;
		
        // create a schema and get table attributes
        $schema = new Schema( $this->database );
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
    protected function errorString( string $message ): string 
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
     * where function.
     * 
     * @access protected
     * @param string $where
     * @param int $limit (default: null)
     * @param int $offset (default: null)
     * @return string
     */
    protected function where( string $where, int $limit = NULL, int $offset = NULL ): string 
    {
        $where = $where ? ' WHERE '.$where : NULL;
        $limit = $limit > 0 ? ' LIMIT '.$limit.( $offset ? " OFFSET ".$offset : NULL ) : NULL;
       
        return $where.$limit;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //                !TABLE INFO METHODS

    #################################################################



    /**
     *   returns the primary key of the table
     *
     *   @access     public
     *   @return     string
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




} /* end class */
