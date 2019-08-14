<?php
// Nerb application library 

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

require_once(APP_PATH.'/framework/const.php');

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
    static public $url;

    /**
     * config
     * 
     * (default value: array())
     * 
     * @var array
     * @access public
     * @static
     */
    static public $config = array();

    /**
     * registry
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     * @static
     */
    static private $registry = array();

    /**
     * path
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     * @static
     */
    static private $path = array();


    /**
     * Singleton Pattern prevents multiple instances of Nerb.  all calls must be made statically e.g. Nerb::function(  args  );
     *
     * @access     private
     * @final
     * @return     void
     */
    final private function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    /**
     * init function.
     * 
     * Initializes the framework and is essentially the constructor
     *
     * @access public
     * @global string FRAMEWORK
     * @global string MODULES
     * @global string LIBRARY
     * @global bool AUTOLOAD
     * @global bool CATCH_EXCEPTIONS
     * @global bool CATCH_ERRORS
     * @global bool CATCH_FATAL_ERRORS
     * @static
     * @return void
     */
    public static function init()
    {
        // define FRAMEWORK constant
        define( 'FRAMEWORK', realpath(__DIR__) );
        
        //load configuration file
        self::loadConfig( FRAMEWORK.'/config.ini' );

        // add the reqired directories to the path
        self::setPath( FRAMEWORK, 'root' );
        self::setPath( MODULES, 'modules' );
        self::setPath( LIBRARY, 'library' );
        self::getModules();
		
        // load required classes
        self::loadClass( 'NerbError' );
		
        // if autoload is defined, set autoloader function
        if( AUTOLOAD ){
            // autoloader function
            spl_autoload_register( __NAMESPACE__ .'\Nerb::autoload' );
        } // end if
		
        // set the current url and path root
        self::$url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].(  str_replace( $_SERVER['DOCUMENT_ROOT'], '', self::$path['root'] )  );
        
        // sets the default exception handler for all uncaught errors
        set_exception_handler( ['NerbHandler', 'exception_handler']);
		
        // sets the default error handler to catch all errors
        set_error_handler( ['NerbHandler', 'error_handler']);
		
        // sets the default error handler to catch fatal errors
        register_shutdown_function( ['Nerb', 'fatal_handler']);
		
        // begin output buffering
        ob_start();

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * loadConfig function.
     * 
     * loads and parses .ini file
     *
     * @access private
     * @static
     * @param string $config
     * @return bool
     */
    public static function loadConfig( string $ini_file ) : bool
    {
        // error checking to ensure file exists
        if( !is_file( $ini_file ) && !is_file( APP_PATH.$ini_file ) ) {
            self::halt( 'Framework Initialization Error <br>Config file <code>['.str_replace(APP_PATH, '..', $ini_file).']</code> could not be found.' );
        }
        
  		// this must be called here because this is before any of the paths
		// have been defined
		self::loadClass( 'NerbParams', FRAMEWORK.'/modules/NerbParams/'); 
		
		// process the ini file and merge it to params array
		$ini_file = is_file( $ini_file ) ? $ini_file : APP_PATH.$ini_file;
        $params = new NerbParams( $ini_file );
        
        // turn params into global constants
        $params->globalize();
        return true;	
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * halt function.
     * 
     * stops execution of the Nerb framework 
     *
     * @access public
     * @static
     * @param string $msg mesage to display in the event 
     * @return void
     */
    public static function halt( string $msg = null ) : void
    {
        ob_end_clean();
        echo '<H1>Nerb Application Framework</H1>';
        echo '<p>'.$msg.'</p>';
        echo '<br />';
        echo '<p>'.SOFTWARE.' v'.VERSION.' build '.BUILD.'<br/>';
        echo COPYRIGHT.'</p>';
        exit;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		


    /**
     * fatal_handler function.
     *
     * This function is called at shutdown and checks to make sure an error has not been
     * thrown.  If shutdown has been caused by a uncaught or fatal error, a NerbError is thrown
     * 
     * @access public
     * @static
     * @return void
     */
    public static function fatal_handler()
    {
        // check to see if shutdown is because of an error
        if( !empty( $error = error_get_last() ) ) {
	        
            // send the array to NerbError for formatting
            $error = NerbError::format( $error ); 
			
            // throw error and exit
            throw new NerbError( $error['message'], $error['trace'] );
            exit;
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //      !REGISTRY METHODS

    #################################################################



    /**
     * register function.
     * 
     * places an object in the registry
     *
     * @access public
     * @static
     * @param object $object
     * @param string $handle
     * @return bool
     * @throws NerbError
     */
    public static function register( $object, string $handle ) : bool
    {
        // error checking
        // invalid object passed
        if ( !is_object( $object ) ) {
            throw new NerbError( 'Can not register <code>['.$handle.']</code> because is not an object.' );
        }
        
        // duplicate handle
        if ( array_key_exists( $handle, self::$registry ) ) {
            // pass on object gracefully
            return true;
            
            // throw error on strict
            //throw new NerbError( 'An object named <code>['.$handle.'::'.get_class( self::$registry[ $handle ] ).']</code> already exists in the registry' );
        }
        
        //add to registry array
        self::$registry[ $handle ] = $object;

        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * listRegisteredObjects function.
     * 
     * returns a list of registered classes in the registry
     *
     * @access public
     * @static
     * @return array
     */
    public static function listRegisteredObjects() : array
    {
        $registry = self::$registry;
        
		$reg = array_map( function( $registry ) {
			return get_class($registry);
		}, $registry );

        return $reg;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * isRegistered function.
     * 
     * determines if an object has been placed in the registry
     *
     * @access public
     * @static
     * @param string $handle
     * @return bool
     */
    public static function isRegistered( string $handle ) : bool
    {
        return array_key_exists( $handle, self::$registry ) ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * determines if class has been registered and returns the name of the object
     *
     * @access public
     * @static
     * @param string $class
     * @return mixed
     */
    public static function isClassRegistered( string $class )
    {
        foreach ( self::$registry as $handle => $object ) {
            if ( is_a( $object, $class ) ) 
                return $handle;
        }

        return false;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * fetch function.
     * 
     * retrieves an object from the registry
     *
     * @access public
     * @static
     * @param string $handle
     * @return object
     * @throws NerbError
     */
    public static function fetch( string $handle )
    {
        // check to see if the object is registered
        if ( !self::isRegistered( $handle ) ) {
            throw new NerbError( 'Object <code>['.$handle.']</code> is not registered' );
        } // end if
        
        return self::$registry[ $handle ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !CLASS LOADING METHODS

    #################################################################




    /**
     * loadClass function.
     * 
     * loads a class definition for use
     *
     * A class must be formatted in the form of 'Class.php' and contain class 'Class' as specified by
     * $class, otherwise an exception will be thrown.
     *
     * If a directory is specified, loadClass will first search that directory, then append the directory to the path, an if not found,
     * it will search the paths specified in Nerb::$path.   If still not found, an exception will be thrown.
     *
     * @access public
     * @static
     * @param string $class (class name)
     * @param string $dir (directory default is ./app/lib if none is given)
     * @return bool
     * @throws NerbError
     */
    public static function loadClass( $class, $dir = null ) : bool
    {
        // Ensure singularity of the class
        if ( class_exists( $class, false ) ) {
            return true;
        }

        // load class file
        self::loadFile( $class.'.php', $dir );

        // if the class was contained in the file, return true otherwise throw an error
        if ( !class_exists( $class ) ) {
            throw new NerbError( 'Class <code>['.$class.']</code> was not contained in $class.php' );
        }
        
        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * loadFile function.
     * 
     * loads and includes a file
     *
     * If a directory is specified, loadClass will first search that directory, then append the directory to the path, an if not found,
     * it will search the paths specified in Nerb::$path.   If still not found, an exception will be thrown.
     *
     * @access public
     * @static
     * @param string $file (class name)
     * @param string $dir (directory default is ./app/lib if none is given)
     * @return mixed
     */
    public static function loadFile( string $file, string $dir = null )
    {
        //   if directory is given, directly look in directory, otherwise it will attempt to autodiscover
        //   classes using the paths listed in $path

        // set relative path
        if ( $dir ) {
            $file = $dir.( substr($file, 0, 1) == '/' ? '' : '/').$file;
        }
        
        if ( file_exists( $file ) ) {
            return require_once $file;
        } 
        
        if ( $included = self::searchPath( $file ) ) {
            return include $included;
        } 
        
        echo $file;
        $error = 'Could not load file <code>'.$file.'</code> using the following include paths:<br /><code>';
        foreach ( self::$path as $path ) {
            $error .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$path.'<br />';
        }
        $error .= '</code> Please check to ensure the path specified is correct or that the file exists';
        throw new NerbError( $error );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * autoload function.
     * 
     * Autoloads a class definition for use
     *
     * @access public
     * @static
     * @param string $class
     * @return void
     * @see loadClass()
     */
    public static function autoload( string $class )
    {
        self::loadClass( $class );
               
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * namespaceWrap function.
     * 
     * 	wraps a class name in a namespace wrapper
     * 
     * 	@access public
     * 	@static
     * 	@param string $name
     * 	@return string
     */
    private static function namespaceWrap( string $name ) 
    {
        // namespace wrap
        return strpos( $name, '\\' ) !== false ? $name : ( __NAMESPACE__ . '\\' . $name );
	  
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	






    #################################################################

    //            !PATH METHODS

    #################################################################


    /**
     * setPath function.
     * 
     * adds a system path to the global registry
     * if a path alias is present, it will be added as $path[ $alias ]
     * else it will be appended to the end of the $path array
     *
     * @access public
     * @static
     * @param string $dir
     * @param string $alias
     * @throws NerbError
     * @return bool
     */
    public static function setPath( string $dir, string $alias = null ) : bool
    {
        // check to see if the path is a valid directory
        if ( !is_dir( $dir ) ) {
	        throw new NerbError( 'Set path error -- <code>['.$path.']</code> is not a valid path.' );
        }
        
        self::$path[$alias] = $dir;
        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * getPath function.
     * 
     * returns the application path
     *
     * @access public
     * @static
     * @param string $alias
     * @return string
     */
    public static function getPath( string $alias = null ) : string
    {
        return $alias ? self::$path[ $alias ] : self::$path;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * searchPath function.
     * 
     * searches Nerb::$path for a file and returns the location of that file
     *
     * @access public
     * @static
     * @param string $file (file name)
     * @return mixed (file name on success, false on failure)
     */
    private static function searchPath( string $file )
    {
        foreach ( self::$path as $value ) {
            if ( file_exists( $value.'/'.$file ) ) {
                return realpath( $value.'/'.$file );
            }// end if
        }// end foreach

        return false;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * getModules function.
     * 
     * @access protected
     * @static
     * @return void
     */
    protected static function getModules()
    {
        // scan for available modules
        $files = array_diff( scandir(MODULES), ['..', '.'] );	

        // set include paths
        foreach( $files as $file ){
            self::setPath( MODULES .'/'. $file, $file );
        } // end foreach
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //            !JUMP METHODS

    #################################################################


    /**
     * jump function.
     * 
     * Jumps to a new page.  probably the single most important function in this file
     *
     * @access public
     * @static
     * @param string $url (The url of page that is being jumpped to)
     * @return void
     */
    public static function jump( string $url )
    {
        global $HTTP_USER_AGENT;

       // if no url is given, the it is assumed that a jump to root url ( / ) is intended
        $url = (  !$url  ) ? '/' : $url;
        Header( "Location: $url" );
        exit;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} /* end class */
