<?php

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Nerb
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */


/**
 *
 * Class for generating sql search statements and cleaning up search data
 *
 */
class NerbSearch
{

    /**
     * params
     * 
     * (default value: array(
     *     ))
     * 
     * @var array
     * @access protected
     */
    protected $params = array(
	    'greedy' => true,
	    'phonetic' => false,
	    'minLength' => 3,
	    'html' => true, // allows html chars in search -- setting to false will also kill wildcard chars
    );
    
	/**
	 * stop_words  list of common words not to be searched
	 * 
	 * (default value: array('a','an','are','as','at','be','by','com','for','from','how','in','is','it','of','on','or','that','the','this','to','was','what','when','who','with','the'))
	 * 
	 * @var string
	 * @access protected
	 */
	protected $stop_words =  array(
		'a','an','are','as','at','be','by','com','for','from','how',
		'in','is','it','of','on','or','that','the','this','to','was','what','when','who','with','the'
	);

    /**
     * keywords_array
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $keywords = array();

    /**
     * sql
     * 
     * (default value: "")
     * 
     * @var string
     * @access protected
     */
    protected $sql = "";
    
    /**
     * keywords
     * 
     * @var mixed
     * @access protected
     */
    protected $search_string;
    
    /**
     * table
     * 
     * @var mixed
     * @access protected
     */
    protected $table;
   
    /**
     * sort_field
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $sort_field = array();
    
    /**
     * search_fields
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $search_fields = array();
    
    /**
     * conditions
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $conditions = array();
    
    /**
     * error
     * 
     * (default value: false)
     * 
     * @var bool
     * @access protected
     */
    protected $error = false;

    /**
     * msg
     * 
     * (default value: "")
     * 
     * @var string
     * @access protected
     */
    protected $msg = "";







	/**
	 * __construct function.  if a table is not given, only a where statement is returned
	 * 
	 * @access public
	 * @param string $keywords
	 * @param array $search_fields
	 * @param string $table (default: NULL)
	 * @return void
	 */
	public function __construct( array $search_fields, string $table = NULL )
	{
	    
	    $this->table = $table;
	    if( $search_fields ){ 
		    $this->search_fields = $search_fields; // Array of DB field names to look in
		}   	    
		
	    if( $table ){ 
		    $this->table = $table; // table name to search in
		}   	    
		

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  setter function.
     *
     *  @access public
     *  @param string $key
     *  @param string $value
     *  @return string old value
     */
    public function __set( string $key, string $value ) : string
    {
        // get original value
        $old = $this->params[$key];

        // set new value
        $this->params[$key] = $value;

        // return old value
        return $old;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !GETTERS AND SETTERS

    #################################################################



    /**
     *  getter function.
     *
     *  @access public
     *  @param string $key
     *  @return string
     */
    public function __get( string $key ) : string
    {
        // returns value
        return $this->params[ $key ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     *  to string function returns search sql statement
     *
     *  @access public
     *  @return string
     */
    public function __toString() : string
    {
        // returns value
        return $this->sql;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




 	/**
	 * stop_words function.  adds user defined words to list of predefined common words
	 * 
	 * @access public
	 * @param array $words
	 * @return NerbSearch
	 */
	public function stop_words( array $words ) : NerbSearch
	{
	    // merge to existing list
	    $this->stop_words = array_merge( $words, $this->stop_words );
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	

   /**
     * sort function.  sets the sort field and direction
     * 
     * @access public
     * @param string $field
     * @param string $dir (default: 'DESC')
     * @return NerbSearch
     */
    public function sort( string $field, string $dir = 'DESC'): NerbSearch
    {
        $this->sort_field[ $field ] = $dir;
        return $this;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * where function.  sets the required conditons of the search
     * 
     * @access public
     * @param string $field
     * @param mixed $dir (default: 'DESC')
     * @return NerbSearch
     */
    public function where( string $field, $condition ): NerbSearch
    {
        $this->conditions[ $field ] = $condition;
        return $this;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * find function.
     * 
     * @access public
     * @param string $search_string
     * @return NerbSearch
     */
    public function find( string $search_string ): NerbSearch
    {
	    // trim off spaces from search string
	    $this->search_string = trim( $search_string );
	    
	    // split keywords
	    $this->keywords = $this->split_keywords( $search_string );
        
	    // catch html special characters if not allowed
	    if( $this->params['html'] == false ){
		    $this->keywords = $this->html_chars( $this->keywords );
	    }
	    
        return $this;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	/**
	 * search function executes search.
	 * 
	 * @access public
	 * @return string bool on error
	 */
	public function search()
	{
	    // error checking 
	    // check to make sure search string is not empty
	    if( !$this->search_string ) {
			return $this->_err( "Nothing to search for" );
			
	    // error checking to make sure that minimum search string length is achieved
		} elseif ( strlen( $this->search_string ) <= $this->minLength ){
			return $this->_err( "Search must be greater than ".$this->minLength." characters" );
		}

	    // strip stop words	    
	    $this->keywords = $this->strip_stop_words( $this->keywords );
	    
	    // create database terms
	    $keywords_db = $this->escape_db( $this->keywords );
	    
	    // create regex terms
	    $keywords_rx = $this->escape_regex( $keywords_db );
	    
	    
	    $hold = array();
	    
	    // Greedy search (match any keywords)
	    if( $this->params['greedy'] ){
	        foreach( $keywords_db as $keyword_db ){
	            foreach( $this->search_fields as $field ){
	              $hold[] = "`".$field."` RLIKE '".$keyword_db."'";
	            }
	        }
	        $hold = implode(' OR ', $hold);

	    }else{
	        // non greedy search (match all keywords)
	        $intermed = '(';
	        
	        foreach( $keywords_db as $keyword_db ){
	            
	            foreach($this->search_fields as $field){
	                $hold[] = $intermed." `".$field."` RLIKE '".$keyword_db."'";
	                $intermed = ' OR';
	            }
	            $intermed = ') AND (';
	        }
	        $hold = implode('', $hold).')';
	    }
	    
	    
	    if( $this->conditions ){
	       
	       	if(!$this->keywords){
				// if there is no keywords but there is a required condition then delete the $hold and query just based on required conditions
	            unset( $hold );
	            unset( $and_parts );
	            foreach( $this->conditions as $field => $value ){
					// If there are multipuls VALUES of a conditions loop it with an sql OR
	                if( is_array($value) ){
	                    //testit('the RC value is an array', $value);
	                    foreach( $value as $key => $value ){
	                        $value = '[[:<:]]'.AddSlashes( $this->escape_rlike( $value ) ).'[[:>:]]';
	                        $hold =" $hold $or `$field` RLIKE '$value'";
	                        $or = "OR";
	                    }
	                }else{
	                    $value = '[[:<:]]'.AddSlashes( $this->escape_rlike( $value ) ).'[[:>:]]';
	                    $hold = "`$field` RLIKE '$value' $and_parts";
	                    $and_parts = "AND ($hold)";
	                }
	            }
	            
	        }else{
	        
	        	// If there are keyword(s) AND required condition(s)
	            foreach( $this->conditions as $field => $value ){
	            
	                // If there are multiple VALUES of a conditions loop it with an sql OR
	                if( is_array($value) ){

	                    foreach( $value as $key => $value ){
	                        //$value = '[[:<:]]'.AddSlashes($this->escape_rlike($value)).'[[:>:]]';
	                        $rc_or .= "`".$field."` RLIKE '".$value."' AND (".$hold.") ".$or;
	                        $or = "OR";
	                    }
	                    $hold = $rc_or;
	                }else{
	                    if( $value != "" ){
	                        //testit(' $rc[$value] in if($this->keywords){}',$value);
	                        $value = '[[:<:]]'.AddSlashes($this->escape_rlike($value)).'[[:>:]]';
	                        $hold = "`$field` RLIKE '$value' AND ($hold)";
	                    }
	                }
	            }
	        }
	    }
	    
	    // create sql statement
	    $sql = $hold;
	    
	    // if a table is given, returns a full sql statement, otherwise just the where clause
	    if( $this->error ){
		    return true;
		} elseif( $this->table ){
	    	$sql .= "SELECT * FROM `".$this->table."` WHERE ";
	    }
	    
	    // append sort field if given
	    if( $this->sort_field ){
		   $sql .= $this->_set_sort();
	    }

	    $this->sql = $sql;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	
	/**
	 * set_sort function.
	 * 
	 * @access private
	 * @return string
	 */
	private function _set_sort() : string
	{
        // initialize temp array
	    $hold = array();
	    
	    // loop through sort field array and collapse it
	    foreach( $this->sort_field as $field => $dir ){
            $hold[] = $field." ".strtoupper( $dir );
	    }
	    
	    // implode with order by statement
	    $hold = " ORDER BY ".implode(', ', $hold);
	
		return $hold;	    

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
    #################################################################

    //            !ERRORS AND MESSAGES

    #################################################################



	/**
	 * error function.
	 * 
	 * @access public
	 * @return bool
	 */
	public function error(): bool
	{
	    if( $this->error ){
		    return $this->msg;
	    } else {
		    return false;
	    }
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	

	/**
	 * err function.
	 * 
	 * @access protected
	 * @param mixed $msg
	 * @return bool
	 */
	protected function _err( $msg ): bool
	{
	    $this->error = true;
	    $this->msg = $msg;
	    return false;
	    	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	

    #################################################################

    //            !KEYWORD MANIPULATION

    #################################################################



	/**
	 * phonetics function.
	 * 
	 * @access private
	 * @param array $keywords
	 * @return array
	 */
	private function phonetics( array $keywords ) : array
	{
	    // Replace * with %
	    $search_string = str_replace('*', '%' , $search_string);
	    
	    // Send anything between quotes to transform() which replaces commas and whitespace with {PLACEHOLDERS}
	    $search_string = preg_replace_callback("~\"(.*?)\"~", "search::transform", $search_string);
	    
	    // Split $this->keywords by spaces and commas and Populate $this->keywords with parts
	    $keywords = preg_split("/\s+|,/", $search_string );
	   
	    // convert the {COMMA} and {WHITESPACE} back within each row of $this->keywords
	    foreach( $keywords as $key => $keyword ){
	        $keyword = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function ( $stuff ) { return chr($stuff[1]);}, $keyword );
	        $keyword = preg_replace("/\{COMMA\}/", ",", $keyword);
	        $keywords[$key] = $keyword;
	    }
	    
	    
	    // convert the {COMMA} and {WHITESPACE} back in $this->keywords
	    $keywords = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function ( $stuff ) { return chr($stuff[1]);}, $keywords );
	    $keywords = preg_replace("/\{COMMA\}/", ",", $keywords);
	    
	    return $keywords;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	
	/**
	 * split_keywords function.
	 * 
	 * @access private
	 * @param string $search_string
	 * @return array
	 */
	private function split_keywords( string $search_string ) : array
	{
	    // Replace * with %
	    $search_string = str_replace('*', '%' , $search_string);
	    
	    // Send anything between quotes to transform() which replaces commas and whitespace with {PLACEHOLDERS}
	    $search_string = preg_replace_callback("~\"(.*?)\"~", "search::transform", $search_string);
	    
	    // Split $this->keywords by spaces and commas and Populate $this->keywords with parts
	    $keywords = preg_split("/\s+|,/", $search_string );
	   
	    // convert the {COMMA} and {WHITESPACE} back within each row of $this->keywords
	    foreach( $keywords as $key => $keyword ){
	        $keyword = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function ( $stuff ) { return chr($stuff[1]);}, $keyword );
	        $keyword = preg_replace("/\{COMMA\}/", ",", $keyword);
	        $keywords[$key] = $keyword;
	    }
	    
	    
	    // convert the {COMMA} and {WHITESPACE} back in $this->keywords
	    $keywords = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function ( $stuff ) { return chr($stuff[1]);}, $keywords );
	    $keywords = preg_replace("/\{COMMA\}/", ",", $keywords);
	    
	    return $keywords;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	
	/**
	 * strip_stop_words function.
	 * 
	 * @access private
	 * @param array $keywords
	 * @return array
	 */
	private function strip_stop_words( array $keywords ) : array
	{
		// loop through each keyword and kill common words 
	    foreach( $keywords as $key => $value ){
	        if( in_array( $value, $this->stop_words ) ){
	            unset( $keywords[$key] );
	        } // end if
	    }// end foreach
	    
	    return $keywords;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
    #################################################################

    //            !ESCAPE AND TRANFORM

    #################################################################



	/**
	 * transform function.
	 *
	 * replaces commas and whitespace with {PLACEHOLDERS}
	 * 
	 * @access private
	 * @param string $keyword
	 * @return void
	 */
	private function transform( string $keyword ) : string
	{
	  	// replace commas and whitespace with {PLACEHOLDERS}
	    $keyword[1] = preg_replace_callback("~(\s)~", function($match) { return '{WHITESPACE-'.ord($match[1]).'}';}, $keyword[1]);
	    $keyword = preg_replace("/,/", "{COMMA}", $keyword[1]);
	    return $keyword;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	
	/**
	 * escape_rlike function.
	 * 
	 * @access private
	 * @param string $keyword
	 * @return string
	 */
	private function escape_rlike( string $keyword ) : string
	{
	    return preg_replace("~([.\[\]*^\$])~", '\\\$1', $keyword);
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	/**
	 * escape_db function.
	 * 
	 * @access private
	 * @param array $keywords
	 * @return array
	 */
	private function escape_db( array $keywords ) : array
	{
	    foreach($keywords as $keyword){ 
	        $out[] = str_replace( '%[[:>:]]', '', str_replace( '[[:<:]]%', '', '[[:<:]]'.AddSlashes( $this->escape_rlike( $keyword ) ).'[[:>:]]' ));
	    }
	    return $out;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	/**
	 * escape_regex function.
	 * 
	 * @access private
	 * @param array $keywords
	 * @return array
	 */
	private function escape_regex( array $keywords ) : array
	{
	    $out = array();
	    foreach($keywords as $keyword){
	        $out[] = '\b'.preg_quote($keyword, '/').'\b';
	    }
	    return $out;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
		
	
	/**
	 * html_chars function.
	 * 
	 * @access private
	 * @param array $search_array
	 * @return array
	 */
	private function html_chars( array $search_array ) : array
	{
	    
	    $out = array();
	    foreach( $search_array as $keyword ){
	        
	        $keyword = str_replace('%', '*', $keyword);
	        
	        if ( preg_match( "/\s|,/", $keyword ) ){
	            $out[] = '"'.HtmlSpecialChars( $keyword ).'"';
	        }else{
	            $out[] = HtmlSpecialChars($keyword);
	        }
	    }
	    return $out;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	




} /* end class */
