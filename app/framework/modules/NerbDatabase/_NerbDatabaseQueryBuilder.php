<?php
// Nerb Application Framework


/**
 *  Companion object for creating SQL select statements for a database
 *
 * @category    	Database
 * @package     	Nerb
 * @subpackage  	NerbDatabase
 * @access      	public
 * @version         1.0
 * @author      	Derrick Haggerty <dhaggerty@gnert.com>
 * @copyright   	Copyright �2012  Gnert Software Studios, Inc. ( http://www.gnert.com )
 * @license     	http://www.gnert.com/docs/license
 * @see         	NerbDatabase
 * @see        		NerbDatabaseTable
 */
class NerbDatabaseJoin
{

    /**
    *   parameters for creating a select statement
    *
    *   @var    array $params
    */
    protected $params=array( 
            'type' => null,
            'from' => array(),
            'on' => array(),
            'using' => array()
     );


    /**
    *   Constructor initiates object
    *
    *   @access     public
    *   @return     object
    */
    public function __construct()
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   formats the parameters given into a SQL join statement
    *
    *   @access     public
    *   @return     string
    */
    public function __toString()
    {

        // [LEFT...] JOIN
        $sql = ( $this->params['type']?$this->params['type']:"" )." JOIN ";

        // [( ]table [, table... )]
        if ( is_array( $this->params['from'] ) ) {
            $sql .= "( ".implode( ", ", $this->params['from'] )." ) ";
        } else {
            $sql .= $this->params['from']." ";
        }

        // ON ( condition [,  condition...] )
        if ( is_array( $this->params['on'] ) ) {
            $sql .= " ON ( ".implode( ", ", $this->params['on'] )." ) ";
        } else {
            $sql .= " ON ( ".$this->params['on']." ) ";
        }

        // USING ( column [,  column...] )
        if ( is_array( $this->params['using'] ) ) {
            $sql .= " USING ( ".implode( ", ", $this->params['using'] )." ) ";
        } else {
            $sql .= " USING ( ".$this->params['using']." ) ";
        }

            return $sql;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   defines the type of join
    *
    *   @access     public
    *   @param      string $type join type [LEFT|RIGHT|INNER|OUTER|CROSS|NATURAL|STRAIGHT]
    *   @return     object
    */
    public function set( $type )
    {

            //validate integrity of variables
        switch ( strtoupper( $type ) ) {
            case "LEFT":
            case "RIGHT":
            case "INNER":
            case "OUTER":
            case "CROSS":
            case "NATURAL":
            case "STRAIGHT":
                $this->params['type'] = strtoupper( $type );
                break;
            default:
                throw new NerbError( "Invalid JOIN specification.  Expecting LEFT | RIGHT | INNER | OUTER | CROSS | NATURAL | STRAIGHT" );
        }
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   list of tables that are joined
    *
    *   If $params are given, will add to the Select->$params
    *
    *   @access     public
    *   @param      string $from table to join from
    *   @return     object
    */
    public function from( $from = null )
    {
        $this->params['from'] = $from;

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   sets the ON conditions
    *
    *   @access     public
    *   @param      string $conditions conditions of the join
    *   @return     object
    */
    public function on( $conditions )
    {
        $this->params['on'] = $conditions;

        return $this;
            
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   sets the ON conditions
    *
    *   @access     public
    *   @param      string $cols conditions of the join
    *   @return     object
    */
    public function using( $columns )
    {
        $this->params['using'] = $columns;

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
<?php

/**
 *  Companion object for creating SQL select statements for a database
 *
 * @category    	Nerb
 * @package     	Nerb
 * @subpackage      NerbDatabase
 * @class 			
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 * @see         	NerbDatabase
 * @see         	NerbDatabaseTable
 *
 * @todo
 *
 */


class NerbDatabaseSelect
{

    /**
     * params
     * 
     * (default value: array(
     *             'distinct'=> false,
     *             'forUpdate'     => false,
     *             'cols'=> array(),
     *             'from'=> array(),
     *             'join'=> array(),
     *             'where'=> array(),
     *             'group'=> "",
     *             'having'=> "",
     *             'order'=> "",
     *             'dir'=> 'ASC',
     *             'limit'=> "",
     *             'offset'=> "",
     *     ))
     * 
     * @var string
     * @access protected
     */
    protected $params=array(
            'distinct'=> false,
            'forUpdate'     => false,
            'cols'=> array(),
            'from'=> array(),
            'join'=> array(),
            'where'=> array(),
            'group'=> "",
            'having'=> "",
            'order'=> "",
            'dir'=> 'ASC',
            'limit'=> "",
            'offset'=> "",
    );


    /**
    *   Constructor initiates object
    *
    *   If $params are given, will add to the Select->$params
    *
    *   @access     public
    *   @param      array $params
    *   @return     Select
    */
    public function __construct($params = array())
    {
        if ($params) {
            array_merge($this->params, $params);
        }
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   formats the parameters given into a SQL select statement
    *
    *   @access     public
    *   @return     string
    *   @throws     NerbError
    */
    public function __toString()
    {
        //validate integrity of variables
        if (!$this->params['from']) {
            throw new NerbError("No table was defined");
        }
        // SELECT [DISTINCT]
        $sql = "SELECT ".($this->params['distinct']?"DISTINCT ":"");

        // {alias.}column [, {alias.}column...]
        if (count($this->params['cols'])  > 0) {
            $sql .= implode(",\n ", $this->params['cols']);
        } else {
            $sql .= " * ";
        }

        // FROM {alias.}table [, {alias.}table...] [USE|FORCE|IGNORE] INDEX (key1 [,key2...])
        if (is_array($this->params['from'])) {
            $sql .= " FROM ".implode(", ", $this->params['from']);
        }

        // JOIN clause
        if (is_array($this->params['join'])) {
            $sql .= " ".implode(" ", $this->params['join']);
        }

        // WHERE condition [{AND|OR|NOT} conditions...]
        if ($this->params['where']) {
            $sql .= " WHERE ".$this->params['where'];
        }

            // GROUP BY column [, column...]
        if (is_array($this->params['group'])) {
            $sql .= " GROUP BY ".implode(", ", $this->params['group']);
        } elseif ($this->params['group']) {
            $sql .= " GROUP BY ".$this->params['group'];
        }

            // HAVING condition
        if ($this->params['having']) {
            $sql .= " HAVING ".$this->params['having'];
        }

            // ORDER BY
        if ($this->params['order']) {
            $sql .= " ORDER BY ".$this->params['order'];
            if ($this->params['dir']) {
                $sql .= " ".$this->params['dir'];
            }
        }

            // LIMIT int OFFSET int
        if ($this->params['limit']) {
            $sql .= " LIMIT ".$this->params['limit'];
        }
        if (isset($this->params['offset'])) {
            $sql .= " OFFSET ".$this->params['offset'];
        }

            // FOR UPDATE
        if ($this->params['forUpdate']) {
            $sql .= " FOR UPDATE ";
        }

            return $sql;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   generates a SQL CONCAT() statement and appends it to column list
    *
    *   @access     public
    *   @param      string $cols columns to be concatinated
    *   @param      string $alias name
    *   @return     Select
    *   @throws     NerbError
    */
    public function concat($cols, $alias)
    {
        if (is_array($cols)) {
            $this->params['cols'][] = "CONCAT(".implode(", ", $cols).") AS $alias ";
        } else {
            throw new NerbError('Expecting array for <strong>$cols</strong>');
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   takes a list of columns and returns them as a list of aliased tables.  eg. alias.col1, alias.col2
    *
    *   @access     public
    *   @param      array $list listing of columns to be aliased
    *   @param      string $alias name of alias
    *   @return     array
    *   @throws     NerbError
    */
    public function alias($list, $alias)
    {
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                $list[$key] = $alias.".".$value;
            }
        } else {
            throw new NerbError('Expecting array for <strong>$list</strong>');
        }
            return $list;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'SELECT [DISTINCT]' statement
    *
    *   @access     public
    *   @param      bool $flag sets distinct
    *   @return     Select
    */
    public function distinct($flag = true)
    {
            $this->params['distinct'] = (bool) $flag;
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'FOR UPDATE' statement
    *
    *   @access     public
    *   @param      bool $flag sets for update
    *   @return     Select
    */
    public function forUpdate($flag = true)
    {
            $this->params['forUpdate'] = (bool) $flag;
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   creates 'SELECT field [,field...] FROM table [,table...]' statement
    *
    *   @access     public
    *   @param      mixed $from table name
    *   @param      bool $append true|false appends the from clause or
    *   @return     Select
    */
    public function from($from, $append = true)
    {
        if (!$append) {
            unset($this->params['from']);
        }
        if ($from) {
            $this->params['from'][] = $from;
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   creates 'SELECT {alias.}field [,{alias.}field...] FROM table [,table...]' statement
    *
    *   if $cols is already populated, the new value value will be appended to column list.  this is useful when trying to alias
    *   a long list of fields from two tables.  ex selecting 12 columns from table 1 and selecting 8 columns from table 2
    *   SELECT t1.col1...t1.col12, t2.col1...t2.col8 FROM table1 AS t1, table2 AS t2 could be done with 2 cols calls
    *   $select->cols($col1_list, "t1")->cols($col2_list, "t2").  if $append is FALSE, $col2_list will overwrite $col1_list
    *
    *   @access     public
    *   @param      mixed $cols columns selected default is '*'
    *   @param      string $alias
    *   @param      bool $append
    *   @return     Select
    */
    public function cols($cols = '*', $alias = null, $append = true)
    {
        if ($alias && is_array($cols)) {
            $cols=$this->alias($cols, $alias);
        }
        if (is_array($cols) && $append) {
            $this->params['cols'] = array_merge($this->params['cols'], $cols);
        } else {
            if (!$append) {
                unset($this->params['cols']);
            }
            if ($cols) {
                $this->params['cols'][] = $cols;
            }
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   adds count(*) to cols field
    *
    *   @access     public
    *   @return     Select
    */
    public function count($column = null)
    {
            unset($this->params['cols']);
        if ($column) {
            $this->params['cols'][] = "count(`$column`)";
        } else {
            $this->params['cols'][] = "count(*)";
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   adds JOIN clause to select statement.
    *
    *   If $join is an instance of Join, the write function will be called, otherwise it is assumed
    *   that the join statement is written manually
    *
    *   @access     public
    *   @param      string|NerbDatabaseJoin  $join conditions
    *   @return     Select
    */
    public function join($join)
    {
        if ($join instanceof Join) {
            $this->params['join'][] = $join->write();
        } else {
            $this->params['join'][] = $join;
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'WHERE conditions'
    *
    *   @access     public
    *   @param      string $where conditions
    *   @return     Select
    */
    public function where($where)
    {
        if ($where) {
            $this->params['where'] = $where;
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'ORDER BY field [,field...] direction'
    *
    *   @access     public
    *   @param      mixed $order field to order by
    *   @param      string $direction [ASC|DESC] direction to sort
    *   @return     Select
    *   @throws     NerbError
    */
    public function order($order, $direction = "ASC")
    {
        if ($order) {
            $this->params['order'] = $order;
        }
        if ($order && $direction) {
            if (strtoupper($direction) != "ASC" && strtoupper($direction) != "DESC") {
                throw new NerbError('Expecting \'<strong>ASC</strong>\' or \'<strong>DESC</strong>\'');
            }
            $this->params['dir'] = strtoupper($direction);
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'LIMIT int OFFSET int'
    *
    *   @access     public
    *   @param      int $limit number of records to retrieve
    *   @param      int $page number of pages to skip.  this is defined by $limit*$page
    *   @return     Select
    */
    public function limit($limit, $page = null)
    {
        if ($limit) {
            $this->params['limit'] = (int) $limit;
            if ($page >= 0) {
                $this->params['offset'] = (int) ($limit*$page);
            }
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'GROUP BY field [,field...]'
    *
    *   @access     public
    *   @param      mixed $group fields to group by
    *   @return     Select
    */
    public function group($group)
    {
        if ($group) {
            $this->params['group'] = $group;
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   defines 'HAVING condition [,condition...]'
    *
    *   @access     public
    *   @param      string $having fields to group by
    *   @return     Select
    */
    public function having($having)
    {
        if ($having) {
            $this->params['having'] = $having;
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   sets the USE INDEX conditions and appends it to the appropirate table
    *
    *   If a table array exists, it will attempt to find the index of the table and append
    *   the index string on the appropirate table.  If the table is not found or no table is
    *   given, it will append it to index[0] of the table list.   If table list is a single table or
    *   a string, it will be appended to the end of it.
    *
    *   @access     public
    *   @param      mixed $index list
    *   @param      string $table table to be indexed
    *   @param      string $flag [USE|IGNORE|FORCE] index mode default is USE
    *   @return     Select
    *   @throws     NerbError
    */
    public function index($index, $table = null, $flag = "USE")
    {
        if ($index) {
            $flag = strtoupper($flag);

            // allowable flags
            if ($flag != "USE" && $flag != "IGNORE" && $flag != "FORCE") {
                throw new NerbError("Invalid flag specification.  Expecting USE | IGNORE | FORCE");
            }

            // append to array
            if (is_array($this->params['from'])) {
                    $key = array_search($table, $this->params['from']);
                    $this->params['from'][$key] .= " ".strtoupper($flag)." INDEX (".(is_array($index)?implode(", ", $index):$index).")";

                // append to string
            } else {
                $this->params['from'] .= " ".strtoupper($flag)." INDEX (".(is_array($index)?implode(", ", $index):$index).")";
            }
        }
            return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
    
    
    
} /* end class */
<?php

/**
 *	Object class extends Table for manipulating both the structure of a table and the data
 *
 * @category   		Nerb
 * @package    		Nerb
 * @subpackage      NerbDatabase
 * @class 			
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 * @see    			NerbDatabase
 * @see    			NerbDatabaseTable
 *
 * @todo
 *
 */
 


class NerbDatabaseStructure extends NerbDatabaseTable{
		
		/**
		*	default table variables
		*	@var	string $_type new table creation type
		*	@var	string $_charSet 
		*	@var	string $_collate
		*/
		protected $_type ='MYISAM';
		protected $_charSet ='latin1';
		protected $_collate ='latin1_swedish_ci';
		


		#################################################################
		                                                                                                                                           
		//                      DELETION METHODS
		
		#################################################################



		/**
		*	Drops the table from the database and kills table object
		*
		*	@access		public
		*	@return 	void
		*	@throws 	NerbError
		*/
		public function drop(){
			// error checking block
			if(!$this->isBound())
				throw new NerbError('Cannot drop table.  This structure has not been bound to a table');
			$this->_database->query("DROP TABLE `$this->_table` ");
			unset($this);
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		#################################################################
		                                                                                                                                           
		//                      NEW TABLE CREATION METHODS
		
		#################################################################


			
		/**
		*	writes the table values to the database and creates a table
		*
		*	@access		public
		*	@return 	bool
		*	@throws 	NerbError
		*/
		public function commit(){
		
			// error checking
			// table cannot be bound
			if($this->isBound())
				throw new NerbError('Cannot create table because it is already bound');
			// no columns defined
			if(empty($this->_columns))
				throw new NerbError('No columns defined');
		
			$q = "CREATE TABLE `$this->_table` (\n\t";
			
			$count = 0;
			foreach($this->_columns as $column){
				$q.= ($count>0?", \n\t":'')."`$column` ".$this->_attribs[$column]['type']." ";
				$q.= $this->_attribs[$column]['default']?"DEFAULT '".$this->_attribs[$column]['default']."' ":'';
				$q.= $this->_attribs[$column]['null'];
				$q.= " ".$this->_attribs[$column]['extra'];
				if($this->_attribs[$column]['key']) $keys[] = $this->_attribs[$column]['key'];
				++$count;
			}
			
			$count=0;
			if(!empty($keys)){
				$q.=",\n";
				foreach($keys as $key){
					$q.= ($count>0?", \n\t":'').$key." ";
					++$count;
				}
			}
			
			$q .= "\n) TYPE = $this->_type ";
			// char set
			$q.=$this->_charSet?"CHARACTER SET $this->_charSet ":NULL;
			// collation
			$q.=$this->_collate?"COLLATE $this->_collate ":NULL;
			
			$this->_query[] = $q;
			$this->_database->query($this);
			// bind to table
			$this->_map();
			$this->_database->updateTableList();
			return true;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	Defines a new table name for creation.
		*
		*	@access		public
		*	@param 		string $table
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function createTable($table){
		
			// error checking
			// this method can only be used for bound tables and is intended to rename an existing table
			if($this->isBound())
				throw new NerbError("This table object is already bound to '<b>$this->_table</b>'.   "
										.'Did you mean to call <code>[<b>rename()]</code></b>?');
			if($this->_database->isTable($table))
				throw new NerbError("Cannot create table.   Table '<b>$table</b>' already exists in the database. ");
			
			$this->_table = $table;
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	adds a field to a new table.  fields will be added to the table in the order that they were added to the table object
		*
		*	@access		public
		*	@param 		string $field
		*	@param 		int $length
		*	@param 		string $type
		*	@param 		string $default
		*	@param 		bool $null
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function addField($field, $length, $type="VARCHAR", $default=NULL, $null=true){
		
			// error checking
			// this method can only be used for adding fields to unbound new tables
			if($this->isBound())
				throw new NerbError('This table object is bound.  '.
										'For bound table objects use <code>[<b>insertField()</b>]</code>?');

			// if length, put in parens
			$length=$length>0?"( $length )":NULL;
			// extract null value
			$null=$null?"":"NOT NULL";
			
			// set $_attribs
			$this->_attribs[$field]=array('name'=>$field,
											   'type'=>strtoupper($type).$length,
											   'default'=>$default,
											   'null'=>$null,
											   'key'=>NULL,
												);
			$this->_columns[]=$field;
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	appends the table name in front of the fields in underscore format to easily identify the 
		*	origin of a table column.  eg. table.table_column_1 table.table_column1
		*
		*	@access		public
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function alias(){
		
			// error checking
			// this method can only be used for aliasing fields in unbound new tables
			if($this->isBound())
				throw new NerbError('This table object is bound.  '.
										'For bound table objects use <code>[<b>rename()</b>]</code>?');
			// sure that this is a new table
			if(empty($this->_table))
				throw new NerbError('Cannot alias fields because no table has been declared.  Use <code>[<b>createTable()</b>]</code> to define a new table');
			// make sure that some columns have been defined
			if(empty($this->_columns))
				throw new NerbError('Cannot alias fields because no fields have been declared');

			$count = 0;
			foreach($this->_columns as $column){
				$this->_columns[$count] = $this->_table."_".$column;
				$this->_attribs[$column]['field'] = $this->_table."_".$column;
				$this->_attribs[$this->_table."_".$column] = $this->_attribs[$column];
				unset($this->_attribs[$column]);
				++$count;
			}
			print_r($this->_columns);
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


		/**
		*	sets the primary key for new tables
		*
		*	@access		public
		*	@param 		string $field
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function setPrimary($field){
		
			// error checking
			// this method can only be used for new tables
			if($this->isBound())
				throw new NerbError('The primary key for bound tables cannot be changed.');
			
			if(empty($this->_attribs[$field]))
				throw new NerbError("Cannot set primary key.  Field '<b>$field</b>' has not been declared.");
				
			$this->_attribs[$field]['key'] = "PRIMARY KEY ( `$field` ) ";
			$this->_primary = $field;
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	sets the autoincrement value of a field
		*
		*	@access		public
		*	@param 		string $field
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function setAutoIncrement($field){
		
			// error checking
			// this method can only be used for new tables
			if($this->isBound())
				throw new NerbError('The autoincrement field of a bound table cannot be changed.');
			
			if(empty($this->_attribs[$field]))
				throw new NerbError("Cannot set autoIncrement. Field '<b>$field</b>' has not been declared.");
				
			if(!stristr($this->_attribs[$field]['type'], 'int'))
				throw new NerbError("Field '<b>$field</b>' must be declared as an <b>INT</b> to be autoincremented. 
										Field is <b>".$this->_attribs[$field][type]."</b>");
				
			$this->_attribs[$field]['extra'] = 'AUTO_INCREMENT';
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	Adds an index to a table
		*
		*	if table is bound, an index will be added immediately, otherwise it will be appended to the sql string 
		*	pending a commit() call
		*
		*	@access		public
		*	@param 		string $field
		*	@param 		string $name
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function addIndex($field, $name=NULL){
		
			// error checking
			if(empty($this->_attribs[$field]))
				throw new NerbError("Field '<b>$field</b>' has not been declared or does not exist.");
			
			// if table is bound, table will be altered
			if($this->isBound()) {
				$this->_query[] = "ALTER TABLE `$this->_table` ADD INDEX ".($name?"`$name`":'')." (`$field`)";
				$this->_database->query($this);
			} else {
				$this->_attribs[$field]['key'] = "INDEX ".($name?"`$name`":'')." ( `$field` )";
			}
			
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	Adds an fulltext index to a table
		*
		*	if table is bound, a fulltext index will be added immediately, otherwise it will be appended to the sql string 
		*	pending a commit() call
		*
		*	@access		public
		*	@param 		string $name
		*	@param 		string $field
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function addFulltext($field, $name=NULL){
		
			// error checking
			if(empty($this->_attribs[$field]))
				throw new NerbError("Field '<b>$field</b>' has not been declared or does not exist.");
			
			// if table is bound, table will be altered
			if($this->isBound()) {
					$this->_query[] = "ALTER TABLE `$this->_table` ADD FULLTEXT ".($name?"`$name`":'')." (`$field`)";
					$this->_database->query($this);
			} else {
				$this->_attribs[$field]['key'] = "FULLTEXT ".($name?"`$name`":'')." ( `$field` )";
			}
			
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	Adds an unique index to a table
		*
		*	if table is bound, a unique index will be added immediately, otherwise it will be appended to the sql string 
		*	pending a commit() call
		*
		*	@access		public
		*	@param 		string $field
		*	@param 		string $name
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function addUnique($field, $name=NULL){
		
			// error checking
			if(empty($this->_attribs[$field]))
				throw new NerbError("Field '<b>$field</b>' has not been declared or does not exist.");
				
			// if table is bound, table will be altered otherwise the $_attribs[key] value will be set for writing
			if($this->isBound()) {
				$this->_query[] = "ALTER TABLE `$this->_table` ADD UNIQUE ".($name?"`$name`":'')." (`$field`)";
				$this->_database->query($this);
			} else {									
				$this->_attribs[$field]['key'] = "UNIQUE ".($name?"`$name`":'')." ( `$field` )";
			}
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		#################################################################
		                                                                                                                                           
		//                      EXISTING TABLE MODIFICATION METHODS
		
		#################################################################


		/**
		*	Adds a foreign key constraint to a table
		*
		*	@access		public
		*	@param 		string $key
		*	@param 		string $table
		*	@param 		string $field
		*	@param 		string $name
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function addForeignKey($key, $table, $field, $name=NULL){
		
			// error checking
			if(empty($this->_attribs[$key]))
				throw new NerbError("Field '<b>$key</b>' has not been declared.");
				
			// a foreign key can only be bound to existing table
			if(!$this->isBound())
				throw new NerbError("A foreign key can only be applied to bound tables.");
				
			// a foreign key can only be bound to existing table
			if(!$this->_database->isTable($table))
				throw new NerbError("Foreign key references table '<b>$table which does not exist</b>'.");
				
			$this->_query[] = "ALTER TABLE `$this->_table` 
											ADD CONSTRAINT `".($name?$name:$key)."` 
											FOREIGN KEY (`$key`) 
											REFERENCES $table(`$field`)";
			$this->_database->query($this);
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		/**
		*	drops an index from a bound table.
		*
		*	@access		public
		*	@param 		string $field
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function dropIndex($index){
		
			// error checking
			// this method can only be used for bound tables and is intended to rename an existing table
			if(!$this->isBound())
				throw new NerbError('This table object is unbound.  '.
										'Did you mean to call <code>[<b>addIndex()]</code></b>?');
		
			$this->_query[] = "ALTER TABLE `$this->_table` DROP INDEX `$field` ";
			$this->_database->query($this);
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Clones a table.
		*
		*	@access		public
		*	@param 		string $newName
		*	@param 		bool $overwrite
		*	@return 	NerbDatabaseStructure this is a new Structure object that is bound to the cloned table on success
		*	@throws 	NerbError
		*
		*	@todo 		add support for cloning across multiple databases
		*/
		public function cloneTable($newName, $overwrite = false){
			
			// error checking
			// this method can only be used for bound tables and is intended to rename an existing table
			if(!$this->isBound())
				throw new NerbError('This table object is unbound.  '.
										'Did you mean to call <code>[<b>createTable()]</code></b>?');

			if(!$overwrite && $this->_database->isTable($newName))
				throw new NerbError("Table '<b>$newName</b>' already exists in the database.".
										"  If you wish to overwrite it, you must set the overwrite flag to 'true'.");

			if($overwrite==true) $this->_database->query($this->_query[] = "DROP TABLE IF EXISTS `$newName`");

			$q = "SHOW CREATE TABLE $this->_table";
			$this->_query[] = $q;
			$result = $this->_database->queryArray($this);
					
			$this->_query[] = preg_replace("/TABLE .* \(/", "TABLE `$newName` (", $result[1]);
			$this->_database->query($this);
			$this->_query[] = "INSERT INTO `$newName` SELECT * FROM `$this->_table`";
			$this->_database->query($this);
			
			// returns a new bound Structure of the cloned table
			return new Structure($this->_database, $newName);
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Renames a table.
		*
		*	@access		public
		*	@param 		string $newName
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function rename($newName){
			
			// error checking
			// this method can only be used for bound tables and is intended to rename an existing table
			if(!$this->isBound())
				throw new NerbError('This table object is unbound.  '.
										'Did you mean to call <code>[<b>createTable()]</code></b>?');
		
			$this->_query[] = "ALTER TABLE `$this->_table` RENAME `$newName` ";
			$this->_database->query($this);
			$this->_table = $newName;
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Renames a field.
		*
		*	@access		public
		*	@param 		string $field
		*	@param 		string $newName
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function renameField($field, $newName){
			
			// error checking
			// this method can only be used for bound tables and is intended to rename an existing table
			if(!$this->isBound())
				throw new NerbError('This table object is unbound.  '.
										'Did you mean to call <code>[<b>createTable()]</code></b>?');
										
			if(!$this->_attribs[$field])
				throw new NerbError("Cannot rename field.  Field '<b>$field</b>' is not defined <p>".
					Nerb_Format::tag(Nerb_Format::tag(implode(', ', $this->columns), 'span', array('style'=>'color: red')), 'code')."</p>"
					);

			$this->_query[] = "ALTER TABLE `$this->_table` CHANGE `$field` `$newName` ".strtoupper($this->_attribs[$field]['type']);
			$this->_database->query($this);
			$this->_map();
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Drops a field from a bound database
		*
		*	@access		public
		*	@param 		string $field
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function dropField($field){
			
			// error checking
			// this method can only be used for bound tables and is intended to rename an existing table
			if(!$this->isBound())
				throw new NerbError('Cannot drop field from an unbound table');
			// cannot drop undefined fields
			if(empty($this->_attribs[$key]))
				throw new NerbError("Cannot drop field '<b>$field</b>' because it is not declared in this table.");
				
			$this->_query[] = "ALTER TABLE `$this->_table` DROP `$field` ";
			$this->_database->query($this);
			return $this;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Sets the autoincrement to an arbitrary value.  If no value is given, it will reset the autoincrement.
		*
		*	@access		public
		*	@param 		int $value
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function setAutoIncrement($value=1){
			// error checking
			// this method can only be used for bound tables
			if(!$this->isBound())
				throw new NerbError('Cannot set autoincrement from unbound table objects'.
										'Did you mean to call <code>[<b>setAutoincrement()]</code></b>?');
		
			$this->_query[] = "ALTER TABLE `$this->_table` PACK_KEYS =0 CHECKSUM =0 DELAY_KEY_WRITE =0 AUTO_INCREMENT =$value";
			$this->_database->query($this);
			return $this;
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Adds a new column to an existing table.  This function will only work on bound tables
		*
		*	@access		public
		*	@param 		string $field
		*	@param 		int $length
		*	@param 		string $type
		*	@param 		string $default
		*	@param 		bool $null
		*	@param 		string $pos
		*	@return 	NerbDatabaseStructure
		*	@throws 	NerbError
		*/
		public function insertField($field, $length, $type="VARCHAR", $default=NULL, $null=true, $pos=NULL){
			// error checking
			// this method can only be used for bound tables and is intended to alter table structure
			if(!$this->isBound())
				throw new NerbError('This table object is unbound.  '.
										'Did you mean to call <code>[<b>addField()]</code></b>?');
			
			$length = $length>0?"($length)":NULL;
			if($default) $default = $this->_database->quoteInto("DEFAULT ? ", $default);
			$null=$null?'NULL':'NOT NULL';
			
			$this->_query[] =  "ALTER TABLE `$this->_table` "
									."ADD `$field` "
									.strtoupper($type).$length
									." $default $null $pos ";
			 
			$result = $this->_database->query($this);
			// remap the new table column to $_table variables
			$this->_map();
			return $this;
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
			
	} /* end class */
?>
<?php

/**
 *  Generates SQL update statement
 *
 * @category    	Nerb
 * @package     	Nerb
 * @subpackage      NerbDatabase
 * @class 			
 * @access      	public
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 * @see         	NerbDatabase
 * @see         	NerbDatabaseTable
 *
 * @todo
 *
 */

class NerbDatabaseUpdate
{

    /**
    *   @var    array $params
    */
    protected $_params=array(
            'set'   => null,
            'cols'  => array(),
            'values'=> array(),
            'where' => array()
    );

    /**
    *   Constructor
    *
    *   @access         public
    *   @param      array $params
    *   @return     void
    */
    public function __construct($params = array())
    {
        array_merge($this->_params, $params);
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Returns formatted SQL string
    *
    *   @access         public
    *   @return     string
    */
    public function __toString()
    {
        // error checking block
        if (!$this->_params['set']) {
            throw new NerbError("No table was selected for updating");
        }
        if (count($this->_params['cols']) != count($this->_params['values'])) {
            throw new NerbError("Column/value mismatch! <b>".count($this->_params['cols'])."</b> columns given with <b>".count($this->_params['values'])."</b> values.");
        }

        // write sql statement
        $sql = "UPDATE ".$this->_params['set']." SET ";
        $count=0;
        foreach ($this->_params['cols'] as $value) {
            $cols .= ($count>0?', `':'`').$value."` = ".NerbDatabase::quote($this->_params['values'][$count]);
            $count++;
        }

        $sql .= " ".$cols." WHERE ".$this->_params['where'];

        return $sql;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   defines table for UPDATE $table SET
    *
    *   @access         public
    *   @param      string $table
    *   @return     NerbDatabaseInsert
    */
    public function set($table)
    {
        $this->_params['set'] = "`".$table."`";
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   columns being updated
    *
    *   @access         public
    *   @param      array $cols
    *   @return     NerbDatabaseInsert
    */
    public function cols($cols)
    {
        $this->_params['cols'] = $cols;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Values to be inserted.
    *
    *   If the values are given in array(value, value, ...) format, they will be added to $param[values] and cols() must be called
    *   If given as array(field=>value, field=>value, ...), the keys willl be extracted to params[cols] while the values will
    *   be extracted to params[values] automatically.  This is intended so that if the fields on a form correspond to fields in
    *   the database, the $_POST data can be passed directly to the values function
    *
    *   @access         public
    *   @param      array $values
    *   @return     NerbDatabaseInsert
    *   @example    $update->set('table')->values($_POST)->where($where);
    */
    public function values($values)
    {
        $keys = array_keys($values);
        if (is_string($keys[0])) {
            $this->_params['cols'] = $keys;
            $this->_params['values'] = array_values($values);
        } else {
            $this->_params['values'] = $values;
        }
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   sets table insertion
    *
    *   @access         public
    *   @param      string $table
    *   @return     NerbDatabaseInsert
    */
    public function where($where)
    {
        $this->_params['where'] = $where;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
<?php

/**
 *	Generates SQL insert & replace statements
 *
 * @category   		Nerb
 * @package    		Nerb
 * @subpackage		NerbDatabase
 * @class 			
 * @access			public
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 * @see    			NerbDatabase
 * @see    			NerbDatabaseTable
 *
 * @todo
 *
 */


class NerbDatabaseInsert {
		
		/**
		 * params
		 * 
		 * (default value: array( 
		 * 				'mode' => "INSERT",
		 * 				'into' => "",
		 * 				'cols' => array(),
		 * 				'values' => array(),
		 * 				'select' => "",
		 * 				'where'	 => array()
		 * 		 ))
		 * 
		 * @var string
		 * @access protected
		 */
		protected $params = array( 
				'mode' => "INSERT",
				'into' => "",
				'cols' => array(),
				'values' => array(),
				'select' => "",
				'where'	 => array()
		 );
		
		/**
		*	Constructor
		*
		*	@access		public
		*	@param		array $params
		*	@return 	void
		*/
		public function __construct(  $params = array()  ){
			if(  $params  ){
				array_merge( $this->params, $params );
			}
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	Returns formatted SQL string
		*
		*	@access		public
		*	@throws 	NerbError
		*	@return 	string
		*/
		public function __toString(){
		
			// error checking
			if( $error_message = $this->errorCheck() )
				throw new NerbError( $error_message );
			
			$sql = $this->params['mode']." INTO `".$this->params['into']."`";
			$count=0;
			
			foreach( $this->params['cols'] as $value ){
				$cols .= ( $count>0?', ':'' ).'`'.$value.'`';
				$count++;
			}
			
			$sql .= " ".Nerb::paren( $cols );
			
			$values = NerbDatabase::quote(  $this->params['values']  );
			$sql .= " VALUES ".Nerb::paren(  $values  );

			return $sql;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		
		
		
		/**
		*	sets table mode to REPLACE
		*
		*	@access		public
		*	@return 	NerbDatabaseInsert 
		*/
		public function replace(){	
			$this->params['mode'] = "REPLACE";
			return $this;
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


			
		/**
		*	sets table insertion 
		*
		*	@access		public
		*	@param 		string $table
		*	@return 	NerbDatabaseInsert 
		*/
		public function into( $table ){	
			$this->params['into'] = $table;
			return $this;
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


			
		/**
		*	columns being inserted
		*
		*	@access		public
		*	@param 		array $cols
		*	@return 	NerbDatabaseInsert
		*/
		public function cols( $cols ){	
			$this->params['cols'] = $cols;
			return $this;
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


			
		/**
		*	Values to be inserted. 
		*
		*	If the values are given in array( value, value, ... ) format, they will be added to $param[values] and cols() must be called
		*	If given as array( field=>value, field=>value, ... ), the keys willl be extracted to params[cols] while the values will
		*	be extracted to params[values] automatically.  This is intended so that if the fields on a form correspond to fields in 
		*	the database, the $_POST data can be passed directly to the values function
		*
		*	@access		public
		*	@param 		array $values
		*	@return 	NerbDatabaseInsert
		*	@example 	$insert->into( 'table' )->values( $_POST );
		*/
		public function values( $values ){
			$keys = array_keys( $values );	
			if( is_string( $keys[0] ) ){
				$this->params['cols'] = $keys;
				$this->params['values'] = array_values( $values );
			} else {
				$this->params['values'] = $values;
			}
			return $this;
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


		
		
		/**
		*	checks to ensure that all data is provided before outputing string
		*	returns a string if there was an error, and false if none.  sounds counterintuitive
		*	but it is basically "are there any errors? no, good" otherwise "yes? what happened?"
		*
		*	@access		protected
		*	@return 	mixed
		*/
		protected function errorCheck(){

			// error checking block
			// no table selected
			if( !$this->params['into'] )
				return "No table was selected for insert";
				
			// empty columns
			if( !$this->params['cols'] )
				return "No columns marked for insertion";
				
			// empty values
			if( !$this->params['values'] )
				return "No values for insertion";
				
			// column mismatch
			if( count( $this->params['cols'] ) != count( $this->params['values'] ) )
				return "Column/value mismatch! <b>".count( $this->params['cols'] )."</b> columns given with <b>".count( $this->params['values'] )."</b> values.";
			
			return false;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


		
		
	} /* end class */
?>
