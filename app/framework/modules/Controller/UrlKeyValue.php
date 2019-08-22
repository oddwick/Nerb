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
     * @class           NerbUrlKeyValue
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
class UrlKeyValue extends Url
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
        $parts = explode( '/', $this->url);
        
        $hold = array();
        
        for( $i = 0; $i < count($parts); $i += 2){
	        $hold[ $parts[$i] ] = $parts[ $i+1 ];
        }
        $this->attribs = $hold;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
