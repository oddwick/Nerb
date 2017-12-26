<?php
// Nerb Application Framework


 /**
 *  Main class for creating a front controller for processing page requests
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Nerb_router
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */

class NerbRouter
{

    /**
     * controller
     * 
     * ( default value: "DefaultController" )
     * 
     * @var string
     * @access protected
     */
    protected $controller = "DefaultController";
    
    /**
     * node
     * 
     * ( default value: "default" )
     * 
     * @var string
     * @access protected
     */
    protected $node = "default";
    
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
     * params
     * 
     * ( default value: array() )
     * 
     * @var array
     * @access protected
     */
    protected $params = array();
    
    /**
     * options
     * 
     * ( default value: array() )
     * 
     * @var array
     * @access protected
     */
    protected $options = array();
    
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
     * path_to_controller
     * 
     * ( default value: "/" )
     * 
     * @var string
     * @access protected
     */
    protected $path_to_controller = "/";
    
    /**
     * base_path
     * 
     * @var mixed
     * @access protected
     */
    protected $base_path;
    
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
     * use_clean_urls
     *
     *	true - use restful urls, false - ordered pair parameters eg. /node/name/value/name/value
     * 
     * ( default value: true )
     * 
     * @var bool
     * @access protected
     */
    protected $use_clean_urls = true;
    
    /**
     * verbose
     *
     *	determines if scrip dies gracefully or loudly
     * 
     * ( default value: false )
     * 
     * @var bool
     * @access protected
     */
    protected $verbose = false;




    /**
    *   Constructor initiates Router
    *
    *   @access     public
    *   @param      string $path_to_controller
    *   @param      array $options
    *   @return     NerbRouter
    */
    public function __construct( string $path_to_controller, array $options = array() )
    {
        // load required controller class for the router
        Nerb::loadclass( "NerbRouterController" );

        // sets the path to the controller
        $this->path_to_controller = $path_to_controller;

        //add options array to
        $this->setOptions( $options );

        $this->parseURL();

        if ( $this->debug == true ) {
            $this->debug();
        }

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   parses the url string
    *
    *   @access     protected
    *   @return     object self
    */
    protected function parseURL()
    {
        // get the path from the server[request_uri]
        $path = trim( parse_url( $_SERVER["REQUEST_URI"], PHP_URL_PATH ), "/" );

        // replace non alphanumeric characters
        $path = preg_replace( "/[^a-zA-Z0-9]\//", "", $path );

        // turn the url into an array by nodes
        $this->url = explode( "/", $path );

        // cut the path up
        if ( strpos( $path, $this->base_path ) === 0 ) {
            $path = substr( $path, strlen( $this->base_path ) );
        }

        /* 	
	        determine what kind of urls are being used
        
         	restful urls - domain.com/controller/param/param/param
         	non-restful - domain.com/controller/node/key/value/key/value
          
		 	for ordered pair urls, the parameters need to be passed as key-value pairs
        */	
        if ( $this->use_clean_urls ) {
            // break the path apart into its segments
            // controller - params ( node will always be empty )
            @list( $controller , $params ) = explode( "/", $path, 2 );

            if ( !empty( $controller ) ) {
                $this->setController( $controller );
            }

            if ( !empty( $params ) ) {
                $this->setParams( explode( "/", $params ) );
            }
        } else {
            // break the path apart into its segments
            // controller - params ( key/value ) pairs

            @list( $controller ,  $params ) = explode( "/", $path, 2 );

            if ( !empty( $controller ) ) {
                $this->setController( $controller );
            }

            if ( !empty( $params ) ) {
                $this->setParams( explode( "/", $params ) );
            }
        }// end if restful
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   sets the controller
    *
    *   @access     public
    *   @param      string controller
    *   @return     object self
    */
    public function setController( string $controller )
    {
        // make sure that controller name is in the format SomeController.php
        $controller = ucfirst( strtolower( $controller ) ) . "Controller";

        //checks to see if the file exists, if not revert to default controller
        if ( @file_exists( $this->path_to_controller."/".$controller.".".DEFAULT_FILE_EXTENSION ) ) {
            $this->controller = $controller;
        }

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   passes the options array to the $_options array and sets the passed
    *   options to the class properties
    *
    *   @access     public
    *   @param      array options
    *   @throws     NerbError
	*   @return     object self
    */
    public function setOptions( array $options )
    {
        // transfer the options
        $this->options = $options;

        //iterate and set properties
        foreach ( $options as $key => $value ) {
            if ( property_exists( $this, $key ) ) {
                $this->$key = $value;
            } else {
                throw new NerbError( "The property '<code>".$key."</code>' is not a valid option" );
            }
        } // end foreach

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   sets the node
    *
    *   @access     public
    *   @param      string node
    *   @return     object self
    */
    public function setNode( string $node ) :self
    {
        $this->node = $node;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   sets the action
    *
    *   @access     public
    *   @param      string action
    *   @return     object self
    */
    public function setAction( string $action ) :self
    {
        $this->action = $action;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Sets the parameters based on given array
    *
    *   @access     public
    *   @param      array $params
    *   @return     NerbRouter
    */
    public function setParams( array $params ) :self
    {

        if ( $this->use_clean_urls == true ) {
            for ( $i=0; $i < count( $params ); $i++ ) {
                $this->params[$i] = $params[$i];
            }
            // by default the node and first param are the same
            $this->setNode( $params[0] );
        } else {
            for ( $i = $this->node_index; $i < count( $params ); $i+=2 ) {
                $this->params[ $params[$i] ] = $params[$i+1];
            }
        }
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Returns the parsed url
    *
    *   @access     public
    *   @return     array
    */
    public function getURL() :array
    {
        // return url array
        return $this->url;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   loads controller class
    *
    *   @access     private
    *   @return     void
    */
    private function loadClass()
    {
        if ( $this->verbose ) {
            require_once( $this->path_to_controller."/".$this->controller.".".DEFAULT_FILE_EXTENSION );
        } else {
            require_once( $this->path_to_controller."/".$this->controller.".".DEFAULT_FILE_EXTENSION );
        }
        return;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
    *   Creates a new token from the appropriate controller
    *
    *   @access     public
    *   @return     NerbRouterController ( new token node )
    */
    public function route()
    {

        // load token class
        $this->loadClass();

        // returns a new token
        return new $this->controller( $this->node, $this->action, $this->params );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   sets the verbose flag
    *
    *   @access     public
    *   @return     object self
    */
    public function verbose( $flag = true ) :self
    {
        $this->_verbose = $flag;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   prints out the debugging variables
    *   - controller
    *   - node
    *   - params
    *
    *   @access     public
	*   @return     object self
    */
    public function debug() :self
    {
        echo "<pre><code>";
        echo "<strong>Controller</strong> - ".$this->controller."\n";
        echo "<strong>Node</strong> - ".$this->node."\n";
        echo "<strong>Options:</strong>\n";
        print_r( $this->options );
        echo "</code></pre>";

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
} /* end class */
