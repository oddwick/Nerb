<?php
// Nerb application library 
Namespace nerb\framework;

/**
 * This lays the groundwork and creates the working environment for the framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Init
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


// define FRAMEWORK constants
defined(FRAMEWORK) or define( 'FRAMEWORK', APP_PATH.'/framework' );
defined(CONFIG) or define( 'CONFIG', APP_PATH.'/config' );
        


/**
 *
 * Base class for generating site framework
 *
 */
class Init
{

    /**
     * config
     * 
     * (default value: array())
     * 
     * @var array
     * @access public
     * @static
     */
    static private $config = array();
    
    /**
     * required_classes
     * 
     * @var mixed
     * @access private
     * @static
     */
    static private $required_classes = array(
	    '/core/Data',
	    '/core/Core',
	    '/core/ClassManager',
	    '/core/ErrorManager',
	    '/core/Error',
	    '/core/Registry',
	    '/core/Log',
	    '/Nerb',
    );
    



    /**
     * Singleton
     *
     * @access     private
     * @final
     * @return     void
     */
    final private function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * Prepares the framework environment and returns a copy of initialized Nerb
     *
     * @access public
     * @static
     * @param bool $reconfigure (default: false)
     * @return Nerb
     */
    public static function begin( bool $reconfigure = false ) : Nerb
    {
        //load configuration file
        if( $reconfigure ){
	        self::reconfigure();
        }
        
        self::loadConfig( CONFIG.'/.Config.cfg' );
    	
    	// required classes
    	self::getRequiredClasses();
    	
    	// initialize componets
    	ClassManager::init();
    	
        // if autoload is defined, set autoloader function
        if( AUTOLOAD ){
            // autoloader function
            spl_autoload_register( __NAMESPACE__ .'\ClassManager::autoload' );
        } // end if
		
		self::setHandlers();
		
        // force an instance of Nerb and return it if necessary
        return Nerb::app();

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * Loads the minimum required classes for the site to execute
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function getRequiredClasses()
	{
		// loop through required classes and require file
		foreach( self::$required_classes as $value ){
			$file = FRAMEWORK.$value.'.'.DEFAULT_FILE_EXTENSION;
			if( !file_exists( $file ) ){
				// die hard
				echo "Required file <code>{$value}</code> could not be found";
				exit;
			}
			require_once $file;
		}
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * setHandlers function.
	 * 
	 * @access protected
	 * @static
	 * @return void
	 */
	protected static function setHandlers()
	{
		// sets the default exception handler for all uncaught errors
		set_exception_handler( [__NAMESPACE__.'\ErrorManager', 'exception_handler']);
		
		// sets the default error handler to catch all errors
		set_error_handler( [__NAMESPACE__.'\ErrorManager', 'error_handler'], E_ALL );
		
		// sets the default error handler to catch fatal errors
		register_shutdown_function( [__NAMESPACE__.'\ErrorManager', 'fatal_handler']);
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * loadConfig function.
     * 
     * @access public
     * @static
     * @param string $config
     * @param bool $reconfigure (default: false)
     * @return void
     */
    public static function loadConfig( string $config ) : bool
    {
        // error checking to ensure file exists
        if( !is_file( $config ) ){
        	self::reconfigure();
        }
        
        return require_once $config;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

		
		
		
	/**
	 * reconfigure function.
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function reconfigure()
	{
    	// runs the reconfiguration function and creates a new config file
    	require_once FRAMEWORK.'/core/Setup.php';
        Setup::reconfigure();
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
