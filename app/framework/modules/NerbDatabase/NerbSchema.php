<?php
// Nerb Application Framework

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
 * @subpackage      NerbDatabase
 * @class           NerbSchema
 * @version         1.0
 * @requires        NerbDatabase
 * @requires        NerbError
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019 
 *
 * @todo
 *
 */



class NerbSchema
{
    /**
     * database
     * 
     * @var mysqli
     * @access protected
     */
    protected $database;
    



	/**
	*   Constructor initiates object
    *
	*   @access     public
	*   @param      NerbDatabase $database
	*   @return     NerbDatabaseSchema
	*   @throws     NerbError
	*/
	public function __construct( NerbDatabase &$database )
    {
        // if a NerbDatabase is given, bind to it and contiune
        if ( $database instanceof NerbDatabase || is_subclass_of( $database, 'NerbDatabase' ) ) {
			// fetch the database name for later retrival
			$this->database = $database;
		} else {
	       	throw new NerbError( 'Database adaptor <code>[$database]</code> must be a <code>[NerbDatabase]</code> object.  <code>['.get_class( $database ).']</code> object was passed.' );
		} // end if

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * sql function.
     *
     * allows the execution of raw sql file or sql dump
     * 
     * @access public
     * @param string $sql_file
     * @return bool
     * @throws NerbError
     */
    public function sqlFromFile( string $sql_file ) : int
    {
    	// error checking
    	if( !file_exists($sql_file) ){
	    	throw new NerbError('File <code>'.$sql_file.'</code> does not exist');
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
     * @throws NerbError
     */
    public function backup( string $table, string $dir )
    {
	    // error check to make sure table exists
	    if( !$this->isTable( $table ) ) throw new NerbError( "Table <code>[$table]</code> does not exist in database" );
	    
	    // make sure directory is valid 
	    if( !is_dir( $dir ) ) throw new NerbError( "Directory <code>[$dir]</code> does not exist" );
		
		// $file is $dir/$table.sql    
		$filename = $table.'_'.date("Ymd_His", time()).'.sql';
		$file = $dir.'/'.$filename;
		
		if( USE_EXEC_BACKUP ){
			// create credentials
	        $host = $this->params['connection']['host'];
	        $user = $this->params['connection']['user'];
	        $pass = base64_decode( $this->params['connection']['pass'] );
	        $database = $this->params['connection']['name'];
			
			// esecute mysql command line dump
			exec("mysqldump --user={$user} --password={$pass} --host={$host} {$database} {$table} --result-file={$file} 2>&1", $output);
			return true;
			
		}else{
			// run query and return the contrived filename on success
			if( $this->database->query( "SELECT * INTO OUTFILE '$file' FROM $table" ) ){
				return $filename;
			} else {
				return false;
			}
		} // end if
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * loadCvs function.
     *
     * load a CVS file and insert it into the table
     * 
     * @access public
     * @param string $table
     * @param string $file
     * @return bool
     * @throws NerbError
     */
    public function loadCvs( string $table, string $file ) : bool
    {
	    // error check to make sure table exists
	    if( !$this->database->isTable( $table ) ) throw new NerbError( "Table <code>[$table]</code> does not exist in database" );
	    
	    // make sure file exists 
	    if( !file_exists( $file ) ) throw new NerbError( "File <code>[$file]</code> does not exist" );
		
		// run query and return
		return $this->database->query( "LOAD DATA INFILE '$file' INTO TABLE $table" ) ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * showCreateTable function.
     *
     * returns the create table statement from a specified table
     * 
     * @access public
     * @param string $table
     * @return string
     * @throws NerbError
     */
    public function showCreateTable( string $table ) : string
    {
	    // error check to make sure table exists
	    if( $this->database->isTable( $table ) ){
		    // fetch result and return create table statement
			$result = mysqli_fetch_assoc( $this->database->query("SHOW CREATE TABLE `$table`") );
			return $result['Create Table'];
	    } else {
		    throw new NerbError( "Table <code>[$table]</code> does not exist in database" );
	    }
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
     * @param string $length (default: NULL)
     * @param string $default (default: NULL)
     * @param bool $null (default: true)
     * @param string $after (default: NULL)
     * @return bool
     */
    public function addColumn( string $table, string $column, string $type, $length = NULL, $default = NULL, bool $null = true, string $after = NULL ) : bool
    {
		// put parenthesis arount the length is specified
		$length = $length ? "($length)" : NULL;
		
		// add DEFAULT keyword and quote 
		$default = $default ? "DEFAULT '".addslashes($default)."' " : NULL;
		
		// create query
		$query = "ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS `$column` ".strtoupper( $type )."$length $default".($null?'':'NOT NULL');
		
		// append AFTER if set
		$query .= $after ? " AFTER `$after`" : '';

		// execute and return
		$result = $this->database->query( $query );
		
		return $result ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dropColumn function.
     * 
     * @access public
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function dropColumn( string $table, string $column ) : bool
    {
		// execute and return
		$result = $this->database->query( "ALTER TABLE `$table` DROP `$column`" );
		return $result ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * renameColumn function.
     * 
     * @access public
     * @param string $table
     * @param string $column
     * @param string $new_name
     * @return bool
     */
    public function renameColumn( string $table, string $column, string $new_name ) : bool
    {
		// get column info
		$description =  $this->describeColumn( $table, $column );

		// execute and return
		$result = $this->database->query( "ALTER TABLE `$table` CHANGE COLUMN `$column` `$new_name` ".$description['type'].' '.$description['null'] );
		return $result ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * renameTable function.
     * 
     * @access public
     * @param string $table
     * @param string $new_name
     * @return bool
     */
    public function renameTable( string $table, string $new_name ) : bool
    {
		// execute and return
		$result = $this->database->query( "ALTER TABLE `$table` RENAME `$new_name`" );
		return $result ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dropTable function.
     * 
     * @access public
     * @param string $table
     * @return bool
     */
    public function dropTable( string $table ) : bool
    {
		// execute and return
		$result = $this->database->query( "DROP TABLE IF EXISTS $table" );
		return $result ? true : false;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * addIndex function.
     * 
     * @access public
     * @param string $table
     * @param string $index
     * @param string $column
     * @param string $type (default: NULL)
     * @param string $using (default: NULL)
     * @return bool
     */
    public function addIndex( string $table, string $index, string $column, string $type = NULL, string $using = NULL ) : bool
    {
		// make uppercase keywords
		$type = strtoupper($type); 
		$using = strtoupper($using); 
		
		// error checking
		if( $type && ($type != 'UNIQUE' && $type != 'FULLTEXT' && $type != 'SPATIAL') ){
			throw new NerbError( '<code>['.$type.']</code> is not a valid value.  <code>$type</code> must be <code>[UNIQUE|FULLTEXT|SPATIAL]</code>' );	
		}
		
		if( $using && ($using != 'BTREE' && $using != 'HASH' && $using != 'RTREE') ){
			throw new NerbError( '<code>['.$using.']</code> is not a valid value.  <code>$using</code> must be <code>[BTREE|HASH|RTREE]</code>' );	
		}
		
		// execute and return
		$result = $this->database->query( "CREATE $type INDEX `$index` ".($using ? "USING $using" : NULL)." ON $table($column)" );
		return $result ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dropIndex function.
     * 
     * @access public
     * @param string $table
     * @param string $index
     * @return bool
     */
    public function dropIndex( string $table, string $index ) : bool
    {
		// execute and return
		$result = $this->database->query( "ALTER TABLE `$table` DROP INDEX IF EXISTS `$index`" );
		return $result ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

    
    
    
    /**
    *   returns an array of table names in the current database
    *
    *   @access     public
    *   @return     array
    */
    public function showTables() : array
    {
        // build query string
        $query = 'SHOW TABLES FROM `'.$this->database->use().'` ';
		$results = $this->database->resultsToArray( $this->database->query( $query ));
		
		foreach( $results as $result ){
			$this->tables[] = current($result);
		} // end foreach
		
        // query database
        return $this->tables;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns a description array for a single column
    *
    *   @access     public
    *   @param      string $table
    *   @param      string $column
    *   @throws     NerbError
    *   @return     array
    */
    public function describeColumn( string $table, string $column ) : array
    {
        // get table data from database
        $result = $this->describe( $table );
        if( array_key_exists( $column, $result) ){
	        return $result[ $column ];
        } else {
	        throw new NerbError( "Column <code>[$column]</code> does not exist in table <code>[$table]</code>" );
        }
        
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
        $result = $this->database->resultsToArray( $this->database->query( 'SHOW FULL COLUMNS FROM `'.$table.'` ' ));

        // iterate and change key case into array
        foreach ( $result as $columns ) {
	        // change key case
	        $columns = array_change_key_case( $columns );
            $info[ $columns['field'] ] = $columns;
	        $info[ $columns['field'] ]['full_name'] = $table.'.'.$columns['field'];
	        $info[ $columns['field'] ]['null'] = $columns['Null']=='YES'?'NULL':'NOT NULL';
        }// end foreach

        return $info;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
