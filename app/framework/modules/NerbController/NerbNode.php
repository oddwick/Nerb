<?php
// Nerb Application Framework


    /**
     *  Loader class for creating a front controller for processing page requests
     *
     *
     * LICENSE
     *
     * This source file is subject to the license that is bundled
     *
     * @category        Nerb
     * @package         Nerb
     * @class           NerbNode
     * @version         1.0
     * @author          Dexter Oddwick <dexter@oddwick.com>
     * @copyright       Copyright (c)2019
     * @license         https://www.github.com/oddwick/nerb
     *
     * @todo
     *
     */

class NerbNode
{

    /**
     * controller
     * 
     * ( default value: 'DefaultController' )
     * 
     * @var string
     * @access protected
     */
    const default_controller = 'DefaultController';
    


    /**
     *   returns a new Controller
     *
     * 
     * @access public
     * @static
     * @param string $path_to_controller
     * @param string $mode
     * @param int $node
     * @param int $offset (default: 0)
     * @return NerbController
     */
    public static function controller(string $path_to_controller, string $mode, int $node, int $offset = 0 ) : NerbController
    {
        // load required controller class for the router
        Nerb::loadclass('NerbController');

        // preparse url and extract controller and load controller class
        $controller = self::loadController($path_to_controller, self::getController());

        // returns a new controller
        return new $controller( $mode, $node, $offset );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   gets the controller (first element) from the url 
     *
     *   @access     protected
     *   @static
     *   @return     string
     */
    protected static function getController() : string
    {
        // clean up the path
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        
        // if the path is empty, send to default controller
        if (empty($path)) return  self::default_controller;
        
        // turn the path into an array by nodes
        $path = explode('/', $path);

        // return the name of the controller
        return ucfirst($path[0]).'Controller';
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * loadController function.  loads controller class
     * 
     * @access private
     * @static
     * @param string $path_to_controller
     * @param string $controller
     * @return string
     */
    private static function loadController( string $path_to_controller, string $controller ) : string
    {
        // build a path to the controller
        $controller_file = $path_to_controller.'/'.$controller.'.'.DEFAULT_FILE_EXTENSION;
        
        // if the controller is found, includ it and return
        if( is_file( $controller_file )){ 
            require_once $controller_file;
            return $controller;
        	
        	
        // otherwise if default_redirect = true, redirect to default page	
        } elseif ( DEFAULT_REDIRECT ) {
            require_once $path_to_controller.'/'. self::default_controller.'.'.DEFAULT_FILE_EXTENSION;
            return self::default_controller;
        	
        
        // this means you blew it and got a big fat error
        } else {
                throw new Nerb_Error( 'Controller: <code>['.$controller.'Controller]</code> could not be loaded. <br> Hint: try changing DEFAULT_REDIRECT' );	
        }
        
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
    public function debug() : self
    {
        echo '<pre><code>[';
        echo '<strong>Controller</strong> - '.self::controller.'\n';
        echo '<strong>Node</strong> - '.self::node.'\n';
        echo '<strong>Options:</strong>\n';
        print_r(self::options);
        echo ']</code></pre>';

        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
} /* end class */
