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
 * @class           SearchKeyword
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
class SearchKeyword
{

    /**
     * the raw keyword
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $keyword = '';
	
    /**
     * datatype
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $datatype = '';
	
	/**
	 * formatted_keyword
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $formatted_keyword = '';
    
    /**
     * not
     * 
     * (default value: false)
     * 
     * @var bool
     * @access protected
     */
    protected $not = false;


    /**
     * __construct function.
     * 
     * @access public
     * @param string $keyword
     * @return void
     */
    public function __construct( string $keyword, $datatype = 'string' )
    {
        // cleans the keyword
        $this->keyword = $this->clean($keyword);
        $this->datatype = $datatype;
        
        // check to see if keyword is NOT
        $this->not();
        
        // datatype keyword
        $this->datatype();		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  Returns the keyword as a MySql RLIKE formatted keyword
     *
     *  @access public
     *  @return string
     */
    public function __toString() : string
    {
        if(empty($this->formatted_keyword)){
	        return '';
        }
        
        // replace wildcard operator
        $keyword = ($this->not ? "NOT ":null)."RLIKE '".$this->escapeDb($this->formatted_keyword)."'";
        // returns value
        return str_replace('{CHAR}', '.', $keyword);

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Forces the keword into a datatype
     *
     * @access protected
     * @return void
     */
    protected function datatype()
    {
        if( !USE_DATATYPING ) return;
        
        // create datatyper
        $type = new Datatype( $this->datatype );
        $this->formatted_keyword = $type->check( $this->formatted_keyword );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Flags a NOT statement and removes the placeholder from the keyword
     * 
     * @access protected
     * @return void
     */
    protected function not()
    {
        // checks to see if keyword is NOT
        if (preg_match('/{NOT}/', $this->keyword)) {
            $this->keyword = preg_replace('/{NOT}/', '', $this->keyword);
            $this->not = true;
        }
        $this->formatted_keyword = $this->keyword;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * quick return to make sure that the keyword is not empty
     * 
     * @access public
     * @return void
     */
    public function empty()
    {
		return empty($this->formatted_keyword);        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * escapes DB characters.
     *
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function escapeRlike(string $keyword) : string
    {
        return preg_replace("/([.\[\]*^\$])/", '\\\$1', $keyword);
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Adds the opening and closing brackets for RLIKE statement
     *
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function escapeDb(string $keyword) : string
    {
        $keyword = '[[:<:]]'.AddSlashes( $this->escapeRlike($keyword) ).'[[:>:]]';
        $keyword = $this->wildcard( $keyword );
        return $keyword;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * restores commas and whitespace from placeholders
     * 
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function clean(string $keyword) : string
    {
        return preg_replace("/\{COMMA\}/", " ", preg_replace("/\{WHITESPACE\}/", " ", $keyword));
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
	/**
	 * If a wildcard is present, eliminate the opening or closing RLIKE brackets
	 * 
	 * @access protected
	 * @param string $keyword
	 * @return string
	 */
	protected function wildcard(string $keyword) : string
	{
		// check to see if a wildcard was used
        return str_replace('{WILDCARD}', '_', str_replace('{WILDCARD}[[:>:]]', '', str_replace('[[:<:]]{WILDCARD}', '', $keyword )));
	}




    /**
     * reverts any changes to the keyword
     * 
     * @access public
     * @return void
     */
    public function revert()
    {
        $this->formatted_keyword = $this->keyword;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
