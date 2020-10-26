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
 * @class           Setup
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */


/**
 *
 * Simple class for creating page numbers
 *
 */
class PageNumber
{


    
    /**
     * params
     * 
     * (default value: array(
     * 	    'divider' => '|',
     * 	    'anchor_class' => 'page-number-anchor',
     * 	    'current_class' => 'page-number-current',
     * 	    'ul_class' => 'page-number',
     * 	    'li_class' => 'page-number-item',
     *     ))
     * 
     * @var string
     * @access protected
     */
    protected $params = array(
	    'divider' => '|',
	    'anchor_class' => 'page-number-anchor',
	    'current_class' => 'page-number-current',
	    'ul_class' => 'page-number',
	    'li_class' => 'page-number-item',
    );
    
    /**
     * url
     * 
     * @var mixed
     * @access protected
     */
    protected $url;
    
    /**
     * currentPage
     * 
     * @var mixed
     * @access protected
     */
    protected $currentPage;
    
    /**
     * index
     * 
     * @var mixed
     * @access protected
     */
    protected $index;
    
	/**
	 * resultsPerPage
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $resultsPerPage;
	
	/**
	 * results
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $results;
	
	/**
	 * pages
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $pages;
	
	/**
	 * range
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access protected
	 */
	protected $range = false;
	
	
	
	
	

    /**
     * construct a page number schema
     *
     * @access public
     * @param string $url
     * @param int $currentPage
     * @param int $resultsPerPage
     * @param int $results
     * @return void
     */
    public function __construct( string $url, int $currentPage = 1, int $resultsPerPage, int $results )
    {
    	$this->url = $url;
    	$this->results = $results;
    	$this->resultsPerPage = $resultsPerPage;
    	$this->currentPage = $currentPage ?? 1;
    	$this->index = $this->currentPage - 1;
    	$this->pages = ceil( $results / $resultsPerPage );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	Set object operating parameters
	*
	*	@access		public
	*	@param	 	string $key
	*	@param	 	mixed $value
	*	@return 	void
	*/
	public function __set( string $key, string $value ) : void
	{
		if( array_key_exists( $key, $this->params ) ){
			$this->params[$key] = $value;
		}
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


		
	/**
	*	Get object operating parameters
	*
	*	@access		public
	*	@param	 	string $key
	*	@return 	mixed
	*/
	public function __get($key)
	{
		return isset( $this->params[$key] ) ? $this->params[$key] : NULL;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		
    /**
     * __toString function.
     *
     * if extending this class, then overwrite the format function
     * 
     * @access public
     * @return string
     */
    public function __toString() : string
    {
    	return $this->format();
    	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * range function.
     *
     * sets range flag to determine if pages are displayed as page or range [N-Nresults]
     * 
     * @access public
     * @param bool $range
     * @return void
     */
    public function range( bool $range = true ) : void
    {
    	$this->range = $range;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * offset function.
     *  
     * returns a sql offset string for where statemnts
     * 
     * @access public
     * @return string
     */
    public function offset() : string
    {
    	$offset = ' LIMIT '.$this->resultsPerPage;
    	
    	if( $this->currentPage > 1 ){
	    	$offset .= ' OFFSET '.( $this->index * $this->resultsPerPage );
	    }
	    
	    return $offset;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	

    /**
     * results function. 
     *
     * returns a string with 'N of N' results
     * 
     * @access public
     * @return string
     */
    public function results() : string
    {
    	$begin = ( $this->index * $this->resultsPerPage ) + 1;
    	$end = $begin + $this->resultsPerPage - 1;
    	if( $end > $this->results ) $end = $this->results;
    	return $begin.'-'.$end.' of '.$this->results;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
    /**
     * pages function. 
     *
     * returns the total number of pages
     * 
     * @access public
     * @return int
     */
    public function pages() : int
    {
    	return $this->pages;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	

    /**
     * format function.
     *
     * this actually builds the page numbers.  it is setup as:
     *	UL class = ul_class
     *		LI class = li_class
     *			A | SPAN class = anchor_class | current_class
     *		[LI divider]
     * 
     * @access protected
     * @return string
     */
    protected function format() : string
    {
    	// initialize array
    	$pages = array();
    	
    	
    	for( $i = 0; $i < $this->pages; $i++ ){
	    
	    	// if pages are a range, then create a range otherwise just the page number	
	    	$page = $this->range ? (( $i * $this->resultsPerPage ) + 1).'-'.( ( $i * $this->resultsPerPage ) + $this->resultsPerPage) : $i + 1; 
	    	
	    	// decide if the page is the current page
	    	// if current, it will be a span
	    	// otherwise it will be an anchor
	    	if( $i  == $this->index ){
		    	$page = '<span class="'.$this->current_class.'">'.$page.'</span>';
	    	} else {
		    	$page = '<a href="'.str_ireplace('{PAGE}', $i+1, $this->url).'" class="'.$this->anchor_class.'">'.$page.'</a>';
	    	}
	    	
	    	// add page to pages array
	    	$pages[] = '<li class="'.$this->li_class.'">'.$page.'</li>';
    	}
    	
    	// collapse the array into a string and return it
    	return '<ul class="'.$this->ul_class.'">'.implode( $this->divider ? '<li class="'.$this->li_class.'">'.$this->divider.'</li>' : '', $pages ).'</ul>';

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
