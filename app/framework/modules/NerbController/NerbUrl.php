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
     * @class           NerbUrl
     * @version         1.0
     * @author          Dexter Oddwick <dexter@oddwick.com>
     * @copyright       Copyright (c)2019
     * @license         https://www.github.com/oddwick/nerb
     *
     *
     */


/**
 * Abstract NerbUrl class.
 * 
 * @abstract
 */
abstract class NerbUrl
{

    /**
     * url
     * 
     * @var string
     * @access protected
     */
    protected $url = '';
    
    /**
     * node
     * 
     * ( default value: self::DEFAULT_ACTION )
     * 
     * @var mixed
     * @access protected
     */
    protected $node = 'default';
    
    /**
     * node_offset
     * 
     * ( default value: 1 )
     * 
     * @var int
     * @access protected
     */
    protected $node_offset = 0;
    
    /**
     * attribs indexed parameter array
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $attribs = array();

    /**
     * mask_values
     * 
     * ( default value: array() )
     * 
     * @var array
     * @access protected
     */
    protected $mask_values = array();
    
    /**
     * action
     * 
     * ( default value: self::DEFAULT_ACTION )
     * 
     * @var string
     * @access protected
     */
    protected $action;
    
    /**
     * return_page
     * 
     * @var string
     * @access protected
     */
    protected $return_page;
    
    /**
     * controller
     * 
     * (default value: '/default')
     * 
     * @var string
     * @access protected
     */
    protected $controller = 'default';   

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
     * Constructor initiates url
     * 
     * @access public
     * @param string $controller
     * @param int $node
     * @param int $offset (default: 0)
     * @return void
     */
    public function __construct( string $controller, int $node, int $offset = 0 )
    {
	    $this->node = $node;
	    
	    $this->controller = $controller;
	    
        // sets the node_offset for offsetting the params index
        $this->node_offset = $offset;

	    // clean the url
        $this->extractUrl();
        
	    // parse the url
        $this->parse();
        
	    // parse the return page
	    $this->get_return_page();
	    
        if( AUTO_SET_TOGGLE ){
	        $this->bypass();
        }
        
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
     *   @param      mixed $node (node name o index)
     *   @return     mixed
     */
    public function __get( string $node )
    {
        // check to see if structure masking is set and verify that value exists
        if( STRUCTURE_MASKING && !empty( $_SESSION['mask_values'][$node] )){
            return  $_SESSION['mask_values'][$node];
        } else{
            return $this->attribs[$node];
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Cleans and separates the url parts
     *
     *   @access     protected
     *   @return     void
     */
    protected function extractUrl()
    {
        // get the path from the server[request_uri]
        $path = strtolower( urldecode( trim( parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' ) ) );
        
        // turn the url into an array by nodes
        $path = str_replace($this->controller.'/', '', $path );
        
        // replace non alphanumeric characters
        $this->url = preg_replace('/[^a-zA-Z0-9]\//', '', $path);

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * url function.
     * 
     * @access public
     * @return string
     */
    public function url() : string
    {
		return $this->url;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * get_return_page function.
     * 
     * @access protected
     * @return void
     */
    protected function get_return_page()
    {
        // set the return page as the refering page minus the arguments
        // (this only works for clean urls)
        $return = explode('?', $_SERVER['HTTP_REFERER']);
        
        // if there is no referer, then set return page as default conroller
        $this->return_page = empty( $return[0] ) || $return[0] == $this->url ? $this->controller : $return[0];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * return_page function.
     * 
     * @access public
     * @return string
     */
    public function return_page() : string
    {
        return $this->return_page;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   sets the beginning node index for a url
     *   by default the node index is 2 for urls that are configured /module/node/node
     *
     *   e.g if the url scheme is /module/node/var0/var1/var2
     *   then getNode( 0 ) would return var0 with a node index of 2
     *
     *   @access public
     *   @param int offset
     *   @return this
     */
    public function setNodeOffset( int $offset ) : self
    {
        $this->node_offset = $offset;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns the raw node value
     *
     *   @access public
     *   @param mixed $node
     *   @return string
     */
    public function node( $node ) : string
    {
        return $this->attribs[ $node ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   sets the beginning node index for a url
     *
     *   @access public
     *   @return int
     */
    public function getNodeCount() : int
    {
        return count($this->attribs); //[ $node + $this->node_offset ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    //!BYPASS FUNCTIONS
	/*
	    These functions allow for quickly setting or toggling $_SESSION variables and bypass
	    logic sections if AUTO_SET_TOGGLE is true
	*/

    /**
     * bypass function.
     * 
     * @access protected
     * @return void
     */
    protected function bypass()
    {
        // action bypasses
        switch (strtolower($this->attribs[0])) {
            case 'set':
                // set action if given ( /controller/set/key/value )
                $this->set( $this->attribs[1], $this->attribs[2] );
                break;

            case 'toggle':
                // set action if given ( /controller/toggle/key )
                $this->toggle( $this->attribs[1] );
                break;
	        	
            default:
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * handler for set calls
     *
     * This function sets a session variable and then returns to the previous page
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set( string $key, $value )
    {
        $_SESSION[ $key ] = $value;
        Nerb::jump( $this->return_page );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	
    /**
     * toggle function.
     *
     * This will toggle a session value and return to the previous page.
     * if the value is non-boolean, it will be set as false
     * 
     * @access public
     * @param string $key
     * @return void
     */
    public function toggle( string $key )
    {
        $_SESSION[ $key ] = $_SESSION[ $key ] ? false : true;
        Nerb::jump( $this->return_page );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	
    // ! abstract functions 
    
    /**
     *   This parses the url based on the what type of url is being parsed    
     * 
     * 	@access protected
     * 	@abstract
     * 	@return void
     */
    abstract public function parse();
    /*
		this is function parses the actual url based on the type - rest|key-value|qsa
	*/




	
} /* end class */
