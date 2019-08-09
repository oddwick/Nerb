<?php
// Nerb Application Framework


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
     * @class           NerbUrlKeyword
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
class NerbUrlKeyword extends NerbUrl
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
        $this->params = explode( KEYWORD_SEPARATOR, $this->url);   
        
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
        //add additional index so that params can be accessed by index and name
        for( $i = 0;  $i < count( $structure); $i++ ){
            $this->attribs[ $structure[$i] ] = $this->attribs[$i];
        }

        return $this;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
} /* end class */
