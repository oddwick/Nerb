<?php
// Nerb Application Framework
namespace nerb\framework;


/**
 *  This is a page generation class that allows one to quickly generate a page from a psuedo templage
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           DyanmicPage
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @property string MODULES
 * @requires Error
 * @requires NerbCache
 * @requires page.ini
 * @todo
 *
 */


class DyanmicPage extends Page
{


    #################################################################

    //      !Attributes

    #################################################################
	
	/**
	  The following functions add HTML attributes to the page
	  they can all be set in the page.ini file and any subsequent calls
	  will be APPENDED to those set in  page.ini
	 */


    /**
     * style function.
     * 
	 * Add stylesheet url to header
	 * 
     * @access public
     * @param string $style (url of stylesheet)
     * @return self
     */
    public function style( string $style ) : self
    {
	    $this->style[] = $style;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * script function.
	 * 
	 * Add script url to header
	 * 
	 * @access public
	 * @param string $script
	 * @return self
	 */
	public function script( string $script ) : self
	{
		$this->script[] = $script;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * meta function.
     * 
     * @access public
     * @param string $title
     * @param string $value
     * @return self
     */
    public function meta( string $title, string $value ) : self
    {
		$this->meta[$title] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	/**
	 * keyword function.
	 *
	 * Add single keyword to meta keyword
	 * 
	 * @access public
	 * @param mixed $value
	 * @return self
	 */
	public function keywords( $value ) : self
    {
		$this->meta['keywords'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * attrib function.
	 * 
	 * @access public
	 * @param string $property
	 * @param string $value
	 * @throws Error
	 * @return self
	 */
	public function attrib( string $property, string $value ) : self
	{
        if( !property_exists( $this, $property ) ){
	       throw new Error( "The property <code>[$property]</code> does not exist.  Check your page.ini for proper spelling and syntax." ); 
        }
        
		$this->$property = $value;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * equiv function.
     * 
     * @access public
     * @param string $title
     * @param string $value
     * @return self
     */
    public function equiv( string $title, string $value ) : self
    {
	    $this->http_equiv[$title] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * rel function.
	 * 
	 * Add link rel statement to header
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return self
	 */
	public function rel( string $title, string $link ) : self
	{
		$this->rel[$title] = $link;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * icon function.
	 * 
	 * Add link to page icons
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return self
	 */
	public function icon( string $title, string $link ) : self
	{
		$this->icon[$title] = $link;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * base function.
	 * 
	 * Add base statment to header
	 * 
	 * @access public
	 * @param string $url
	 * @return self
	 */
	public function base( string $url ) : self
	{
		$this->base = $url;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} // end class
