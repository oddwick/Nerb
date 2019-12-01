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
 * @class           UrlRest
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
class UrlRest extends Url
{

    // ! abstract functions 
    protected $nodeCount = 0;
    
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
        
        $this->nodeCount = count($this->nodes);
        
		// check to see if action is present
        if( $this->nodes[ $this->node_offset ] == 'action' ){
	        $this->action = $this->nodes[ $this->node_offset + 1 ];
        }

               
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




     /**
     * create a mask for structure masking
     * 
     * @access public
     * @param array $structure
     * @return self
     */
    public function defineMask( array $structure ) : self
    {
        //add additional index so that params can be accessed by index and name
        for( $i = 0;  $i < count( $structure); $i++ ){
            $this->$mask_values[ $structure[$i] ] = $this->nodes[$i];
        }

        return $this;
		
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
