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
 * @class           SearchField
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
class SearchField
{

    /**
     * field
     * 
     * @var string
     * @access protected
     */
    protected $field;
    
    /**
     * datatype
     * 
     * @var string
     * @access protected
     */
    protected $datatype;
    
    /**
     * keywords
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $keywords = array();
    
    /**
     * greedy - flag for greedy or lazy searching
     * 
     * (default value: TRUE)
     * 
     * @var mixed
     * @access protected
     */
    protected $greedy = TRUE;
    
    
    
    /**
     * __construct function.
     * 
     * @access public
     * @param string $field
     * @param string $datatype
     * @param array $keywords
     * @property string USE_DATATYPING
     * @return void
     */
    public function __construct( string $field, string $datatype, array $keywords )
    {
        // transfer parameters
        $this->field = $field;
        $this->datatype = $datatype;
        
        // create the keyword objects
        $this->addKeywords( $keywords );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  to string function returns search sql statement
     *
     *  @access public
     *  @return string
     */
    public function __toString() : string
    {
        return $this->format();
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

	
	
	
    /**
     * greedy function.
     * 
     * @access public
     * @param bool $greedy (default: FALSE)
     * @return void
     */
    public function greedy( bool $greedy = FALSE ) : void
    {
        $this->greedy = $greedy;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

	
	
	
	/**
	 * addKeywords function.
	 * 
	 * @access protected
	 * @param array $keywords
	 * @return void
	 */
	protected function addKeywords( array $keywords )
	{
        // create keyword objects and make sure they are not empty
        foreach ( $keywords as $keyword) {
	       $word = new SearchKeyword( $keyword, $this->datatype );
	       $this->keywords[] = $word->empty() ? "RLIKE '{EMPTY}'" : $word;
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
    
    
    
    
    /**
     *  creates a formatted search field
     *
     *  @access protected
     *  @property string GREEDY_SEARCH
     *  @return string
     */
	protected function format() : string
	{
		$format = array();
		foreach( $this->keywords as $keyword){
			$format[] = '`'.$this->field.'` '.$keyword;
		}
		$format = implode( $this->greedy ? ' OR ' : ' AND ', $format );
		
		// make sure that if only one keyword is present, there is never
		// a NOT statement otherwise the entire database or very large datasets
		// will be returned
		if( count( $this->keywords) == 1 ){
			$format = str_ireplace('NOT ', '', $format );
		}
		return '('.$format.')';
       
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
