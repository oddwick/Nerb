<?php
// Nerb Application Framework


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
abstract class NerbController
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
    protected $mode = 'key-value';

    /**
     * controller
     * 
     * @var mixed
     * @access protected
     */
    protected $controller; // the name of the controller
    
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
     * action
     * 
     * ( default value: self::DEFAULT_ACTION )
     * 
     * @var mixed
     * @access protected
     */
    protected $action = null;
    
    /**
     * set
     * 
     * (default value: null)
     * 
     * @var mixed
     * @access protected
     */
    protected $set = null;
    
    /**
     * toggle
     * 
     * (default value: null)
     * 
     * @var mixed
     * @access protected
     */
    protected $toggle = null;
    
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
     * node_index
     * 
     * ( default value: 1 )
     * 
     * @var int
     * @access protected
     */
    protected $node_index = 0;
    
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
     * title
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $title = ''; //true;
        



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
    public function __construct(string $mode, int $node, array $options = array())
    {
        $this->mode = $mode;
        // parse the url
        $this->parseUrl();

        // sets the node_index for offsetting the params index
        if (isset($params['node_index'])) $this->node_index = $params['node_index'];
        
        // set the return page as the refering page minus the arguments
        // (this only works for clean urls
        $return = explode('?', $_SERVER['HTTP_REFERER']);
        $this->return_page = $return[0];

        return $this;
        
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
        // check to see if structure masking is set and verify that value exists
        if( STRUCTURE_MASKING && !empty( $_SESSION['mask_values'][$node] )){
            return  $_SESSION['mask_values'][$node];
        } elseif( !empty( $this->attribs[$node] )){
            return $this->attribs[$node];
        } else  {
            return $this->params[$node];
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   seter that changes value of url
     *
     *   @access     public
     *   @param      string $field (field name)
     *   @return     string $value (old value)
     */
    //public function __set( string $field, string $value )
    //{
    //} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Container function for executing domain logic for this module     
     * 
     * 	@access protected
     * 	@abstract
     * 	@return void
     */
    abstract public function route();
    /*
		this is where your domain logic goes in the router controller class.
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
     *   parses the url string
     *
     *   @access     protected
     *   @return     object self
     */
    protected function parseURL()
    {
        // get the path from the server[request_uri]
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // replace non alphanumeric characters
        $path = preg_replace('/[^a-zA-Z0-9]\//', '', $path);

        // turn the url into an array by nodes
        $this->url = explode('/', $path);
        
        switch (strtolower($this->url[1])) {
	        
            case 'action':
                // set action if given ( /controller/action/actionNode )
                $this->action = $this->url[2];
                break;

            case 'set':
                // set action if given ( /controller/set/key/value )
                $this->set = $this->url[2];
                break;

            case 'toggle':
                // set action if given ( /controller/toggle/key )
                $this->toggle = $this->url[2];
                break;
	        	
            default:
        }



        /* 	
	        determine what kind of urls are being used
        
         	rest - domain.com/controller/node/node/node
         	key-value - domain.com/controller/node/key/value/key/value
         	qsa url - domain.com/controller?key=value&key=value
          
		 	for key-value urls, the parameters MUST be passed as key-value pairs
        */
        switch ( URL_MODE  ){
	        
            case 'rest':
            case 'restful':
            case 'clean':
                // break the path apart into its segments
                // controller -  params ( node will always be empty for restful urls )
                @list( $this->controller,  $params ) = explode( '/', $path, 2 );
	        
                // add params to the to the params array
                if ( !empty( $params ) ) {
		            
                    // break apart the params
                    $params = explode( '/', $params );
		
                    for ( $i = 0; $i < count( $params ); $i++ ) {
                        $this->params[] = str_replace( KEYWORD_SEPARATOR, ' ', $params[$i]);
                    }
                }
                break;
	        	
	        	
            case 'key':
            case 'keys':
            case 'key-val':
            case 'key-value':
                // break the path apart into its segments
                // controller - node - params
                @list( $this->controller, $this->node,  $params ) = explode( '/', $path, 3 );
	
                if ( !empty( $params ) ) {
			        
                    // break apart the params
                    $params = explode( '/', $params );
		            
                    // iterate and match value pairs
                    for ( $i = 0 + $this->node_index; $i < count( $params ); $i += 2 ) {
                        $this->params[ $params[$i] ] = $params[$i+1];
                    }
                }
                break;
	        	
	        	
            case 'qsa':
            default:
#TODO: finish qsa @Dexter Oddwick [12/31/17]
                break;
	        
        }
        
        //Nerb::inspect( $this, true, 'params' );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   sets the beginning node index for a url
     *   by default the node index is 2 for urls that are configured /module/node/node
     *
     *   e.g if the url scheme is /module/node/var0/var1/var2
     *   then getNode( 0 ) would return var0 with a node index of 2
     *
     *   @access	public
     *   @param     int node
     *   @return    this
     */
    public function setNodeIndex( int $node ) : self
    {
        $this->node_index = $node;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns the raw node value
     *
     *   @access public
     *   @param int node
     *   @return string
     */
    public function node( int $node = 0 )
    {
        return $this->url[ $node ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   alias of $this->node() returns indexed nodes
     *
     *   @access	public
     *   @param     int node
     *   @return    string
     */
    public function getNode( int $node = 0 )
    {
        return $this->node( $node + $this->node_index );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   sets the beginning node index for a url
     *
     *   @access	public
     *   @return    int
     */
    public function getNodeCount() :int
    {
        return count($this->url); //[ $node + $this->node_index ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   passes the options array to the $_options array and sets the passed
     *   options to the class properties
     *
     *   @access     public
     *   @param      array options
     *   @throws     NerbError
     *   @return     self
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
                throw new NerbError( 'The property <code>['.$key.']</code> is not a valid option' );
            }
        } // end foreach

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   sets the node
     *
     *   @access     public
     *   @param      string node
     *   @return     self
     */
    public function setNode( string $node ) : self
    {
        $this->node = $node;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   sets the action
     *
     *   @access     public
     *   @param      string action
     *   @return     self
     */
    public function setAction( string $action ) : self
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
    public function setParams( array $params ) : self
    {
#TODO: finish setParams @Dexter Oddwick [1/21/18]
/*
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
*/
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * handler for set calls
     *
     * This function sets a session variable and then returns to the previous page
     * 
     * @access protected
     * @param string $key
     * @param string $value (default: '')
     * @return void
     */
    protected function set( $value = '' )
    {
        $_SESSION[ $this->set ] = $value;
        Nerb::jump( $this->return_page );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	
    /**
     * toggle function.
     *
     * This will toggle a session value and return to the previous page.
     * if the value is non-boolean, it will be set as false
     * 
     * @access protected
     * @param string $key
     * @return void
     */
    protected function toggle()
    {
        $_SESSION[ $this->toggle ] = $_SESSION[ $this->toggle ] ? false : true;
        Nerb::jump( $this->return_page );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	
    /**
     *   assigns a structure to the parsed url so that it can be called by name vs node#
     *
     *   @access     public
     *   @param      array $params
     *   @return     NerbRouter
     */
    public function defineStructure( array $structure ) : self
    {
        //add additional index so that params can be accessed by index and name
        for( $i = 0;  $i < count( $structure); $i++ ){
            $this->attribs[ $structure[$i] ] = $this->params[$i];
        }

        return $this;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Sets the parameters based on given array
     *
     *   @access     public
     *   @param      array $params
     *   @return     array
     */
    public function urlReady( array $input ) : array
    {
        $output = array();
        
        // loop through input and replace spaces with keyword separator
        foreach ( $input as $key => $value ) {
            $output[ $key ] = str_replace( ' ', KEYWORD_SEPARATOR, $value );
        }
        return $output;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    // ! abstract functions 
    
    /**
     *   handler for public pages to be displayed
     *
     *   @access		public
     */
    //abstract protected function publicPages();
    /*
		this is where your domain logic goes.
	*/




    /**
     *   handler for private pages to be displayed
     *
     *   @access		public
     */
    //abstract function privatePages();
    /*
		this is where your domain logic goes.
	*/




    /**
     *   handler for action calls
     *
     *   @access		public
     */
    //abstract protected function action();
    /*
		this is where your domain logic goes.
	*/



	
} /* end class */
