<?php
// Nerb application library 
Namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Nerb
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */

/**
 *
 * Base class for generating site framework
 *
 */
class Nerb
{

    /**
     * url
     * 
     * @var mixed
     * @access public
     * @static
     */
    private $url;

    /**
     * config
     * 
     * (default value: array())
     * 
     * @var array
     * @access public
     * @static
     */
    private static $config = array();

    /**
     * registry
     *
     * This is an array of objetcs that have been registered as handle => object
     * so that they can be retrieved anywhere in the framework 
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     * @static
     */
    private static $registry;

    /**
     * path
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     * @static
     */
    protected $path = array();

	/**
	 * instance
	 * 
	 * (default value: null)
	 * 
	 * @var mixed
	 * @access private
	 * @static
	 */
	private static $instance = null;
	


    /**
     * Singleton Pattern prevents multiple instances of Nerb.
     *
     * @access     private
     * @final
     * @return     void
     */
    final private function __construct()
    {
    	$this->init();
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * init function.
     * 
     * Initializes the framework and is essentially the constructor
     *
     * @access public
     * @static
     * @return void
     */
    public function init()
    {
        // Create a registry object
		$this->registry = new Registry();
		
        // set the current url and path root
        $this->url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].(  str_replace( $_SERVER['DOCUMENT_ROOT'], '', $this->path['root'] )  );
        
        // begin output buffering
        ob_start();

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * gatekeeper function that ensures the singularity of the application
	 * 
	 * @access public
	 * @static
	 * @return self
	 */
	public static function app()
	{
		if ( empty(self::$instance) ){
			self::$instance = new Nerb();
		}
		return self::$instance;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * register function.
     * 
     * factory function for passing arguments to the registry
     *
     * @access public
     * @static
     * @param object $object
     * @param string $handle
     * @return bool
     * @throws Error
     */
    public static function registry()
    {
		if ( empty(self::$registry) ){
			self::$registry = new Registry();
		}
		return self::$registry;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
