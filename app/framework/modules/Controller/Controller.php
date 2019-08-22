<?php
// Nerb Application Framework
Namespace nerb\framework;


/**
 *  Abstract class for creating a controller for the node
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbController
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 *
 */


/**
 * Abstract NerbController class.
 * 
 * @abstract
 */
abstract class Controller
{

    /**
     * mode
     *
     *	true - use restful urls, false - ordered pair parameters eg. /node/name/value/name/value
     * 
     * ( default value: key-value )
     * 
     * @var string
     * @access protected
     */
    protected $mode = 'rest';

    /**
     * controller
     * 
     * @var mixed
     * @access protected
     */
    protected $controller;
    
    /**
     * url
     * 
     * @var Url
     * @access protected
     */
    protected $url;
    
    /**
     * params
     * 
     * ( default value: array() )
     * 
     * @var array
     * @access protected
     */
    protected $params = array();
    
    /**
     * base_directory
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $base_dir = '';

    /**
     * debug
     * 
     * ( default value: false )
     * 
     * @var bool
     * @access protected
     */
    protected $debug = false; //true;
    


    /**
     * Constructor initiates node
     * 
     * @access public
     * @param string $mode
     * @param int $node
     * @param int $offset (default: 0)
     * @throws Error
     * @return void
     */
    public function __construct( string $mode, int $node, int $offset = 0 )
    {
        // error check
        if( 
        	$mode != 'rest' && 
        	$mode != 'key-value' && 
        	$mode != 'keyword' && 
        	$mode != 'qsa'  
        ){
	        throw new Error( "The value <code>[$mode]</code> is not a valid url mode.  Expecting <code>[REST|KEY-VALUE|KEYWORD|QSA]</code>" );
        }
        
        $this->mode = strtolower( $mode );
        $this->getController();
        
        switch( $mode ){
	        case 'rest':
		        $this->url = new UrlRest( $this->controller, $node, $offset );
		        break;
	        
	        case 'key-value':
		        $this->url = new UrlKeyValue( $this->controller, $node, $offset );
		        break;
	        
	        case 'keyword':
		        $this->url = new UrlKeyword( $this->controller, $node, $offset );
		        break;
	        
	        default:
		        $this->url = new UrlQsa( $this->controller, $node, $offset );
        }// end switch

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !GET AND SET

    #################################################################



    /**
     *   returns a value by key
     *	if structure masking is enabled, then it will check $_SESSION['mask_values'] to see if
     *	it has been set and will return that instead of the node value
     *
     *   @access     public
     *   @param      string $field (field name)
     *   @return     string
     */
    public function __get( string $node )
    {
        return $this->url->$node;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * getController function.
     * 
     * @access public
     * @return string
     */
    public function getController() : string
    {
        return $this->controller = str_replace('controller', '', strtolower( get_class($this) ) );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    // ! abstract functions 
    
    /**
     *   Container function for executing domain logic for this module     
     * 
     * 	@access protected
     * 	@abstract
     */
    abstract public function route();
    /*
		this is where your domain logic goes in the router controller class.
	*/



    /**
     *   handler for public pages to be displayed
     *
     *   @access		public
     */
    abstract protected function publicPages();
    /*
		this is where your domain logic goes.
	*/




    /**
     *   handler for private pages to be displayed
     *
     *   @access		public
     */
    abstract protected function privatePages();
    /*
		this is where your domain logic goes.
	*/




    /**
     *   handler for action calls
     *
     *   @access		public
     */
    abstract protected function action();
    /*
		this is where your domain logic goes.
	*/



	
} /* end class */
