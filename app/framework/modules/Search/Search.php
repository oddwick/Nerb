<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Search
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 * @requires        ~/config.ini
 *
 */


/**
 *
 * Class for generating sql search statements and cleaning up search data using a
 * PHP search class created by GitFr33 as a starting point
 *
 */
class Search
{

    /**
     * excluded_words  list of common words not to be searched
     *
     * (default value: array('a','an','are','as','at','be','by','com','for','from','how','in','is','it','of','on','or','that','the','this','to','was','what','when','who','with','the'))
     *
     * @var array
     * @access protected
     */
    protected $excluded_words = array(
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'com', 'for', 
        'from', 'how', 'in', 'is', 'it', 'of', 'on', 'or', 'that', 'the', 
        'this', 'to', 'was', 'what', 'when', 'who', 'with', 'the'
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
    protected $error = FALSE;

	/**
	 * greedy
	 * 
	 * (default value: TRUE)
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $greedy = TRUE;
	
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
     * __construct function.
     * 
     * @access public
     * @param string $search_string
     * @param array $search_fields
     * @return void
     */
    public function __construct( string $search_string, array $search_fields, $sanitize=true )
    {
		// --guard contions--
		// by default, pre sanitizes search string as a STRING datatype
		if( $sanitize ){
			$search_string = Datatype::string($search_string);
		}
		
        // error checking
        // check to make sure search string is not empty
        if ( empty($search_string) ) {
            return $this->err("Nothing to search for");
        } 
        
        // make sure that minimum search string length is achieved
        if (strlen($search_string) < KEYWORD_MIN_LENGTH) {
            return $this->err("Search must ".KEYWORD_MIN_LENGTH." or more characters");
        }

        // process search_string
        // trim off spaces from search string and split keywords
        $keywords = $this->splitKeywords( $this->search_string = trim($search_string) );
        
        // create search strings from fields and keywords
        $this->search_fields = $this->addSearchField( $search_fields, $keywords );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  to string function returns search sql statement
     *
     *  @access public
     *  @return string
     */
    public function __toString() : string
    {
        return $this->search();
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * search function executes search.
     *
     * @access public
     * @return string
     */
    public function search() : string
    {
        // set greedy flag for each search fields
        foreach( $this->search_fields as $field ){
        	$field->greedy( $this->greedy );
        } // end foreach
        
        // create the search strings
        // (field1 = keyword1 [AND|OR] ...) [OR (field2 = keyword1]...
        //$search = '( '.implode(') OR ( ', $this->search_fields).' )';
        $search = implode(' OR ', $this->search_fields);
        
        // make sure that a string is being returned if after processing 
        // a search string is processed into nothing to keep __toString from creating an error
        if( empty($search) ){
	        $this->err('Search string is empty');
	        return '';
        }
        // returns value
        return $search;
    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * error function.
     *
     * returns error message if present, or false if no error
     *
     * @access public
     * @return string
     */
    public function error() : string
    {
        return !empty($this->error_msg) ? $this->error_msg : '' ;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * stopword function.
     * 
     * @access public
     * @param string $word
     * @return void
     */
    public function stopword( string $word ) : void
    {
		$this->excluded_words[] = $word;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * greedy function.
     * 
     * @access public
     * @return void
     */
    public function greedy() : void 
    {
		$this->greedy = TRUE;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * lazy function.
     * 
     * @access public
     * @return void
     */
    public function lazy() : void
    {
		$this->greedy = FALSE;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * err function.
     *
     * creates an error condition with message
     *
     * @access protected
     * @param string $msg
     * @return void
     */
    protected function err(string $msg)
    {
        $this->error_msg = $msg;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Creates the search field objects
     * 
     * @access protected
     * @param array $search_fields
     * @param array $keywords
     * @return array
     */
    protected function addSearchField( array $search_fields, array $keywords ) : array
    {
	   $fields = array();
       foreach( $search_fields as $field => $datatype ){
	       $fields[] = new SearchField( $field, $datatype, $keywords );
       }
       return $fields;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Actually splits the search string into keywords and adds placeholders for wildcards etc.
     *
     * @access protected
     * @param string $search_string
     * @return array
     */
    protected function splitKeywords(string $search_string) : array
    {
        // wildcard searches: Replace * or ? with {WILDCARD} and ! with {NOT}
        $search_string = str_replace('!', '{NOT}', 
						 str_replace('*', '{WILDCARD}', 
						 str_replace('?', '{WILDCARD}', 
						 str_replace('_', '{CHAR}', $search_string
		))));

        // Send anything between quotes to transform() which replaces commas and whitespace with {PLACEHOLDERS}
        $search_string = preg_replace_callback("/\"(.*?)\"/", [$this, 'transform'], $search_string);

        // Split $this->keywords by spaces and commas and Populate $this->keywords with parts
        $keywords = preg_split("/\s+|,/", $search_string);
        
        // eliminate stop words
        $keywords = $this->stripStopWords($keywords);
        
        return $keywords;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Removes common words to prevent searches from returning too many results
     *
     * @access protected
     * @param array $keywords
     * @return array
     */
    protected function stripStopWords(array $keywords) : array
    {
        // loop through each keyword and kill common words
        foreach ($keywords as $key => $value) {
            if (in_array( strtolower($value), $this->excluded_words)) {
                unset($keywords[$key]);
            } // end if
        }// end foreach

        return $keywords;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * replaces commas and whitespace with {PLACEHOLDERS} inside quoted strings
     *
     * @access protected
     * @param array $keyword
     * @return string
     */
    protected function transform(array $keywords) : string
    {
        // replace commas and whitespace with {PLACEHOLDERS}
        $keywords[1] = preg_replace_callback("/(\s)/", function($match) {
            return '{WHITESPACE}';
        }, $keywords[1]);
        $keyword = preg_replace("/,/", "{COMMA}", $keywords[1]);
        return $keyword;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





} /* end class */
