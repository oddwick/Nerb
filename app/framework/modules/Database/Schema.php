<?php
// Nerb Application Framework
Namespace nerb\framework;

/**
 * Nerb Application Framework
 *
 * Creates a schema manipulation class for creating, dropping, backups, and general database manipulation 
 *
 *
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      Database
 * @class           NerbSchema
 * @version         1.0
 * @requires        Database
 * @requires        Error
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019 
 *
 * @todo
 *
 */



class Schema
{
    /**
     * database
     * 
     * @var Database
     * @access protected
     */
    protected $database;
    
    /**
     * tables
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $tables = array();



    /**
     *   Constructor initiates object
     *
     *   @access     public
     *   @param      Database $database
     *   @return     self
     *   @throws     Error
     */
    public function __construct( Database &$database )
    {
        $this->database = $database;
        $this->tables = $this->showTables();
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * sql function.
     *
     * allows the execution of raw sql file or sql dump
     * 
     * @access public
     * @param string $sql_file
     * @return int
     * @throws Error
     */
    public function sqlFromFile( string $sql_file ) : int
    {
        // error checking
        if( !file_exists($sql_file) ){
            throw new Error('File <code>'.$sql_file.'</code> does not exist');
        }
    	
        // read file to variable
        $sql = file_get_contents( $sql_file );

        // execute result and return number of queries processed, throw error on fail
        return $this->database->multiQuery( $sql );
    	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * backup function.
     *
     * back up a specified table from the database
     * 
     * @access public
     * @param string $table
     * @param string $dir
     * @return string
     * @throws Error
     */
    public function backup( string $table, string $dir )
    {
	    // error check to make sure table exists
	    if( !$this->database->isTable( $table ) ) {
	        throw new Error( "Table <code>[$table]</code> does not exist in database" );
	    }
	    
	    // make sure directory is valid 
	    if( !is_dir( $dir ) ) {
	        throw new Error( "Directory <code>[$dir]</code> does not exist" );
	    }
		
		// $file is $dir/$table.sql    
		$filename = $table.'_'.date("Ymd_His", time()).'.sql';
		$file = $dir.'/'.$filename;
		
		if( USE_EXEC_BACKUP ){
			$credentials = $this->database->credentials();
			// create credentials
	        $host = $credentials['host'];
	        $user = $credentials['user'];
	        $pass = base64_decode( $credentials['pass'] );
	        $database = $credentials['name'];
			
			// esecute mysql command line dump
			exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} --result-file={$file} 2>&1", $output);
			return $filename;
			
		} 
		
		// run query and return the contrived filename on success
		$this->database->execute( "SELECT * INTO OUTFILE '$file' FROM $table" );
		return $filename;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * loadCvs function.
     *
     * load a CVS file and insert it into the table
     * 
     * @access public
     * @param string $table
     * @param string $file
     * @return int
     * @throws Error
     */
    public function loadCvs( string $table, string $file ) : int
    {
	    // error check to make sure table exists
	    if( !$this->database->isTable( $table ) ) {
	        throw new Error( "Table <code>[$table]</code> does not exist in database" );
	    }
	    
	    // make sure file exists 
	    if( !file_exists( $file ) ) {
	        throw new Error( "File <code>[$file]</code> does not exist" );
	    }
		
		// run query and return
		return $this->database->execute( "LOAD DATA INFILE '$file' INTO TABLE $table" );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * showCreateTable function.
     *
     * returns the create table statement from a specified table
     * 
     * @access public
     * @param string $table
     * @return string
     * @throws Error
     */
    public function showCreateTable( string $table ) : string
    {
        // error check to make sure table exists
        if( !$this->database->isTable( $table ) ){
            throw new Error( "Table <code>[$table]</code> does not exist in database" );
        }
        
        // fetch result and return create table statement
        $result = mysqli_fetch_assoc( $this->database->query("SHOW CREATE TABLE `$table`") );
        return $result['Create Table'];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !TABLE MANIPULATION

    #################################################################


    /**
     * addColumn function.
     * 
     * @access public
     * @param string $table
     * @param string $column
     * @param string $type
     * @param string $length (default: null)
     * @param string $default (default: null)
     * @param bool $null (default: true)
     * @param string $after (default: null)
     * @return int
     */
    public function addColumn(string $table, string $column, string $type, $length = null, $default = null, bool $null = true, string $after = null) : int
    {
        // put parenthesis arount the length is specified
        $length = $length ? "($length)" : null;
		
        // add DEFAULT keyword and quote 
        $default = $default ? "DEFAULT '".addslashes($default)."' " : null;
		
        // create query
        $query = "ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS `$column` ".strtoupper( $type )."$length $default".($null?'':'NOT null');
		
        // append AFTER if set
        $query .= $after ? " AFTER `$after`" : '';

        // execute and return
        return $this->database->execute( $query );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dropColumn function.
     * 
     * @access public
     * @param string $table
     * @param string $column
     * @return int
     */
    public function dropColumn( string $table, string $column ) : int
    {
        // execute and return
        return $this->database->execute( "ALTER TABLE `$table` DROP `$column`" );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * renameColumn function.
     * 
     * @access public
     * @param string $table
     * @param string $column
     * @param string $new_name
     * @return int
     */
    public function renameColumn( string $table, string $column, string $new_name ) : int
    {
        // get column info
        $description =  $this->describeColumn( $table, $column );

        // execute and return
        return $this->database->execute( "ALTER TABLE `$table` CHANGE COLUMN `$column` `$new_name` ".$description['type'].' '.$description['null'] );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * renameTable function.
     * 
     * @access public
     * @param string $table
     * @param string $new_name
     * @return int
     */
    public function renameTable( string $table, string $new_name ) : int
    {
        // execute and return
        return $this->database->execute( "ALTER TABLE `$table` RENAME `$new_name`" );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dropTable function.
     * 
     * @access public
     * @param string $table
     * @return int
     */
    public function dropTable( string $table ) : int
    {
        // execute and return
        return $this->database->execute( "DROP TABLE IF EXISTS $table" );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * emptyTable function.
     * 
     *   empties the table data
     *
     * @access public
     * @param string $table
     * @return int (rows affected)
     */
    public function emptyTable( string $table ) : int
    {
        // sets query string
        $query = "TRUNCATE `$table`";
        
        return $this->database->execute( $query );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * addIndex function.
     * 
     * @access public
     * @param string $table
     * @param string $index
     * @param string $column
     * @param string $type (default: null)
     * @param string $using (default: null)
     * @return int
     */
    public function addIndex( string $table, string $index, string $column, string $type = NULL, string $using = NULL ) : int
    {
        // make uppercase keywords
        $type = strtoupper($type); 
        $using = strtoupper($using); 
		
        // error checking
        if( $type && !preg_match('/^(UNIQUE|FULLTEXT|SPATIAL)$/', $type ) ){
            throw new Error( 'Index type <code>['.$type.']</code> is not a valid value.  <code>$type</code> must be <code>[UNIQUE|FULLTEXT|SPATIAL]</code>' );	
        }
		
        if( $using && !preg_match('/^(BTREE|HASH|RTREE)$/', $using ) ){
            throw new Error( '<code>['.$using.']</code> is not a valid value.  <code>$using</code> must be <code>[BTREE|HASH|RTREE]</code>' );	
        }
        
        // execute and return
        return $this->database->execute( "CREATE $type INDEX `$index` ".($using ? "USING $using" : null)." ON $table($column)" );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dropIndex function.
     * 
     * @access public
     * @param string $table
     * @param string $index
     * @return int
     */
    public function dropIndex( string $table, string $index ) : int
    {
        // execute and return
        return $this->database->execute( "ALTER TABLE `$table` DROP INDEX IF EXISTS `$index`" );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

    
    
    
    /**
     * primary function.
     * 
     * @access public
     * @param string $table
     * @throws Error
     * @return string
     */
    public function primary( string $table ) : string
    {
        if( !in_array( $table, $this->tables )){
            $msg = "Table <code>[$table]</code> is not in database";
            throw new Error( $msg );
        }
        //$result = mysqli_fetch_assoc($this->database->query( "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'" ));
        $result = $this->database->queryArray( "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'" );

        // execute and return
        return $result['Column_name'];

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns an array of table names in the current database
     *
     *   @access     public
     *   @return     array
     */
    public function showTables() : array
    {
        return $this->database->tables();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns a description array for a single column
     *
     *   @access     public
     *   @param      string $table
     *   @param      string $column
     *   @throws     Error
     *   @return     array
     */
    public function describeColumn( string $table, string $column ) : array
    {
        // get table data from database
        $result = $this->describe( $table );
        if( !array_key_exists( $column, $result) ){
            throw new Error( "Column <code>[$column]</code> does not exist in table <code>[$table]</code>" );
        }
        
        return $result[ $column ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     *   returns an array of table field names and descriptions
     *
     *   @access     public
     *   @param      string $table table name
     *   @return     array
     */
    public function describe( string $table ) : array
    {
        // get table data from database
        $result = $this->database->queryArray( 'SHOW FULL COLUMNS FROM `'.$table.'`' );

		$info = array();
        // iterate and change key case into array
        foreach ( $result as $columns ) {
            // change key case
            $columns = array_change_key_case( $columns );
            $info[ $columns['field'] ] = $columns;
            $info[ $columns['field'] ]['full_name'] = $table.'.'.$columns['field'];
            $info[ $columns['field'] ]['null'] = $columns['Null']=='YES'?'null':'NOT null';
        }// end foreach

        return $info;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
