<?php
// Nerb Application Framework
namespace nerb\framework;


/**
 *  Abstract class for parsing urls for nerb controller
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbUrlQsa
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 *
 */


/**
 * NerbUrl class.
 * 
 */
class UrlQsa extends Url
{

    // ! abstract functions 
    
    /**
     *   This parses the url based on the what type of url is being parsed    
     * 
     * 	@access protected
     * 	@abstract
     * 	@return void
     */
    public function parse()
    {
        // turn the url into an array by nodes
        $this->nodes = explode('/', $this->url);
        
        // slide the controller off the first element
        $this->controller = array_shift($this->nodes);
	    // break the path apart into its segments
	    // controller -  params ( node will always be empty for restful urls )
	    //@list( ,  $this->nodes ) = explode( '/', $this->url, 2 );
	
	    // add params to the to the params array
/*
	    if ( !empty( $params ) ) {
	        
	        // break apart the params
	        $params = explode( '/', $params );
	
	        for ( $i = 0; $i < count( $params ); $i++ ) {
	            $this->params[] = str_replace( KEYWORD_SEPARATOR, ' ', $params[$i]);
	        }
	    }
*/
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Assigns a structure to the parsed url so that it can be called by name vs node#
     * this can only be called for restful urls
     * 
     * @access public
     * @param array $structure
     * @return self
     */
    public function defineStructure( array $structure ) : self
    {
	    $count = count( $structure);
        //add additional index so that params can be accessed by index and name
        for( $i = 0;  $i < $count; $i++ ){
            $this->nodes[ $structure[$i] ] = $this->nodes[$i];
        }

        return $this;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
} /* end class */
