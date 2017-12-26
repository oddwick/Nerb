<?php
// Nerb Application Framework


 /**
 *  Abstract class for creating a controller for the router
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbRouterController
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 *
 */


/**
 * Abstract NerbRouterController class.
 * 
 * @abstract
 */
abstract class NerbRouterController
{

    const DEFAULT_ACTION    = "default";
    const BASE_URL          = null;             // the default starting point where pages are served from

    /**
     * controller
     * 
     * @var mixed
     * @access protected
     */
    protected $controller;  // the name of the controller
    
    /**
     * node
     * 
     * ( default value: self::DEFAULT_ACTION )
     * 
     * @var mixed
     * @access protected
     */
    protected $node = self::DEFAULT_ACTION;
    
    /**
     * action
     * 
     * ( default value: self::DEFAULT_ACTION )
     * 
     * @var mixed
     * @access protected
     */
    protected $action = self::DEFAULT_ACTION;
    
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
     * node_index
     * 
     * ( default value: 1 )
     * 
     * @var int
     * @access protected
     */
    protected $node_index = 1;
    
    /**
     * return_page
     * 
     * ( default value: null )
     * 
     * @var mixed
     * @access protected
     */
    protected $return_page = null;
    
    /**
     * url
     * 
     * ( default value: array() )
     * 
     * @var array
     * @access protected
     */
    protected $url = array();


    /**
     *   Constructor initiates node
     *
     * 
     * @access public
     * @param mixed $node (default: null)
     * @param mixed $action (default: null)
     * @param mixed $params (default: null)
     * @return void
     */
    public function __construct( string $node = null, string $action = null, array $params = null )
    {

        // parse the url into componet parts
        $path = trim( parse_url( $_SERVER["REQUEST_URI"], PHP_URL_PATH ), "/" );
        $path = preg_replace( "/[^a-zA-Z0-9]\//", "", $path );
        $this->url = explode( "/", $path );

        // set the controller
        $this->controller = $this->url[0];

		// set nodes and params
        if ( isset( $node ) )  $this->node = $node;
        if ( isset( $node ) )  $this->action = $action ? $action : $node;
        if ( isset( $params ) )  $this->params = $params;
        
        // sets the node_index for offsetting the params index
        if ( isset( $params['node_index'] ) ) $this->node_index = $params['node_index'];
        
        // set the return page as the refering page minus the arguments
        // (this only works for clean urls
        $return = explode( "?", $_SERVER["HTTP_REFERER"] );
        $this->return_page = $return[0];

        // execute control logic
        $this->NerbRouterController();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Container function for executing domain logic for this module     
    * 
    * 	@access protected
    * 	@abstract
    * 	@return void
    */
    abstract protected function NerbRouterController();
    /*
		this is where your domain logic goes.
	*/



    /**
    *   Returns the parsed url
    *
    *   @access     public
    *   @return     Array
    *   @throws     NerbError
    */
    public function getURL() :array
    {
        return $this->url;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   sets the beginning node index for a url
    *   by default the node index is 2 for urls that are configured /module/node/node
    *
    *   e.g if the url scheme is /module/node/var0/var1/var2
    *   then getNode( 0 ) would return var0 with a node index of 2
    *
    *   @access		public
    *   @param      int node
    *   @return     this
    */
    public function setNodeIndex( int $node ) :self
    {
        $this->node_index = $node;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   alias of $this->node() returns indexed nodes
    *
    *   @access		public
    *   @param      int node
    *   @return     string
    */
    public function getNode( int $node = 0 )
    {
        return $this->node( $node + $this->node_index );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns the raw node value
    *
    *   @access		public
    *   @param      int node
    *   @return     string
    */
    public function node( int $node = 0 )
    {
        return $this->url[ $node ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   sets a constant BASE_DIR as the root from which pages are pulled
    *
    *   @access     protected
    *   @param      string $dir
    *   @return     self
    */
    protected function setRoot( string $dir ) :self
    {
        define( BASE_DIR, $dir );
        return $this;
        
    }// end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   sets the beginning node index for a url
    *
    *   @access		public
    *   @param      int node
    *   @return     int
    */
    public function getNodeCount() :int
    {
        return count( $this->url );//[ $node + $this->node_index ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    // ! abstract functions 
    
    /**
    *   Container function for executing domain logic for this module
    *
    *   @access		public
    */
    //abstract protected function publicPages();
    /*
		this is where your domain logic goes.
	*/


    /**
    *   Container function for executing domain logic for this module
    *
    *   @access		public
    */
    //abstract function privatePages();
    /*
		this is where your domain logic goes.
	*/


    /**
    *   Container function for executing domain logic for this module
    *
    *   @access		public
    */
    //abstract protected function doAction(  $node  );
    /*
		this is where your domain logic goes.
	*/
	
} /* end class */
