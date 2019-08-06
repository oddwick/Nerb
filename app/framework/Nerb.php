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

require_once( APP_PATH.'/framework/const.php' );

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
    *   @access     protected
	* 	@final
    *   @return     void
    */
    final protected function __construct()
	{
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    /**
    *   init function.
    * 
    *   Initializes the framework and is essentially the constructor
    *
    *   @access     public
    *   @global     string FRAMEWORK
    *   @global     string MODULES
    *   @global     string LIBRARY
    *   @global     bool AUTOLOAD
    *   @global     bool CATCH_EXCEPTIONS
	*   @global     bool CATCH_ERRORS
	*   @global     bool CATCH_FATAL_ERRORS
	* 	@static
    *   @return     void
    */
    public static function init()
    {
		// define FRAMEWORK constant
		define( 'FRAMEWORK', realpath(__DIR__) );
		
		if( !is_file( FRAMEWORK.'/config.ini'  )){
			self::halt( 'Framework Initialization Error <br><code>[config.ini]</code> could not be found.' );
			exit;
		}
		
		//load configuration file
		self::loadConfig( FRAMEWORK.'/config.ini' );

        // add the reqired directories to the path
        self::$path['root'] = FRAMEWORK;
		self::$path['modules'] = MODULES;
		self::$path['library'] = LIBRARY;
		
		// error check make sure path is valid
		foreach( self::$path as $key => $path ){
			if( !is_dir($path) ){
				self::halt( 'Framework Initialization Error <br>Path <code>{'.$path.'}</code> defined as <code>['.$key.']</code> is not a valid location.' );
			}
		} // end foreach
        
        // scan for available modules
        $files = scandir( MODULES );		

		// set include paths
        foreach( $files as $file ){
			if( $file != '.' && $file !='..' && is_dir( MODULES .'/'. $file )){
		        self::$path[$file] = MODULES .'/'. $file;
			}
        } // end foreach
        
        
		// if autoload is defined, set autoloader function
		if( AUTOLOAD ){
	        // autoloader function
	        spl_autoload_register( __NAMESPACE__ .'\Nerb::autoload' );
		} // end if
		
        
        // set the current url and path root
        self::$url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].(  str_replace( $_SERVER['DOCUMENT_ROOT'], '', self::$path['root'] )  );
        
        
        // Load required classes
        self::loadClass( 'NerbError' );
        
        // sets the default exception handler for all uncaught errors
        if( CATCH_EXCEPTIONS ) set_exception_handler( array('Nerb', 'exception_handler'));
		
        // sets the default error handler to catch all errors
		if( CATCH_ERRORS ) set_error_handler( array('Nerb', 'error_handler'));
		
        // sets the default error handler to catch all errors
		if( CATCH_FATAL_ERRORS ) register_shutdown_function( array('Nerb', 'fatal_handler'));
		
        // begin output buffering
        ob_start();

        return;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




   /**
    *   loadConfig function.
    * 
    *   loads and parses .ini file
    *
    *   @access     private
	* 	@static
	* 	@param string $config
    *   @return     bool
    */
    private static function loadConfig( string $ini_file ) : bool
    {
		// this must be called here because this is before any of the paths
		// have been defined
		self::loadClass( 'NerbParams', FRAMEWORK.'/modules/NerbParams/'); 
		// process the ini file and merge it to params array
		$params = new NerbParams( $ini_file );
		$params->globalize();
		return true;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





   /**
    *   addConfig function.
    * 
    *   loads additional config files specific to the app
    *
    *   @access     public
	* 	@static
    *   @return     bool
    */
    public static function addConfig( string $ini ) : bool
    {
		// error checking to ensure file exists
		// if full path is given
		if( is_file( $ini )) {
			$ini_file = $ini;
			
		// if path is relative to app root
		} else if ( is_file( APP_PATH.$ini )) {
			$ini_file = APP_PATH.$ini ;
	
		// oops. 
		} else {
            throw new NerbError( 'Could not locate given configuration file <code>['.$ini.']</code> using: <br><code>['.$ini.']</code><br><code>[APP_PATH'.$ini.']</code>' );
		}

		$params = new NerbParams( $ini_file );
		$params->globalize();
		
		return true;	
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    #################################################################

    //      !Exceptions, Errors & logs

    #################################################################



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
		echo '<p>'.SOFTWARE.' v'.VERSION.'<br/>';
		echo COPYRIGHT.'</p>';
		exit;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		



    /**
    *   error_handler function.
    * 
    *   class for supressing errors - extends php error_handler
    *
    *   @access     public
	* 	@static
    *   @param      Exception $exception
    *   @return     void
    *   @throws     NerbError
    */
	public static function error_handler( int $error_number, string $errstr, string $errfile, string $errline, array $errcontext )
	{
		if( !USE_ERROR_LOGGING ) return;


		// determines if full path is shown or masked with APP_PATH
		if ( !SHOW_FULL_PATH ) {
			$errfile = str_ireplace(FRAMEWORK, '', $errfile);
		} 
		
		// switch through error number to determine error type and 
		// whether or not it is logged
		switch ( $error_number ){
			
			case 1:
			case 256:
				if( LOG_ALL_ERRORS == false ) return;
				$prefix = 'ERROR ';
				break;
				
			case 2:
			case 512:
				if( LOG_ALL_WARNINGS == false ) return;
				$prefix = 'WARNING ';
				break;
			
			case 4:
				$prefix = 'PARSE ';
				break;
			
			case 8:
			case 1024:
				if( LOG_ALL_NOTICE == false ) return;
				$prefix = 'NOTICE ';
				break;
			
			default:
				$prefix = 'OTHER';
				break;				
		}// end switch
		
		// create error string
		// WARNING | ERROR | NOTICE [date] file (line) string
		$error = $errfile . ' (' . $errline . ') -- ' .$errstr;
		
		
		// log error to file
		$log = new NerbLog( ERROR_LOG );
		$log->write( $error , $prefix );
			
				
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	
    /**
    *   exception_handler function.
    * 
    *   the default error handler to make pretty error messages
    *
    *   @access     public
	* 	@static
    *   @param      Exception $exception
    *   @return     void
    *   @throws     NerbError
    */
	public static function exception_handler( $exception ) : void
	{
		//throws a general error for all uncaught exceptions
		throw new NerbError( '<strong>Uncaught exception -> </strong> '.$exception->getMessage(), $exception->getTrace() );
		
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
	    $error = error_get_last();
	
	    if( $error !== null) {
	    
			// send the array to NerbError for formatting
			$error = NerbError::format( $error ); 
			
			// throw error and die
		    throw new NerbError( $error['message'], $error['trace'] );
	        die;
	    }
	    
	    if( DEBUG ){
		    echo '<pre>Rendered in '.(microtime()-RENDER).'ms</pre>';
	    }
	}


    #################################################################

    //      !REGISTRY METHODS

    #################################################################



    /**
    *   register function.
    * 
    *   places an object in the registry
    *
    *   @access     public
	* 	@static
    *   @param      object $object
    *   @param      string $handle
    *   @return     bool
    *   @throws     NerbError
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
            //throw new NerbError( 'An object named <code>['.$handle.'::'.get_class( self::$registry[ $handle ] ).']</code> already exists in the registry' );
        }
        
        //add to registry array
        self::$registry[ $handle ] = $object;

        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   listRegisteredObjects function.
    * 
    *   returns a list of registered classes in the registry
    *
    *   @access     public
	* 	@static
    *   @return     array
    */
    public static function listRegisteredObjects() : array
    {
        foreach ( self::$registry as $handle => $object ) {
            $reg[$handle] = get_class( $object ); 
        }

        return $reg;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   isRegistered function.
    * 
    *   determines if an object has been placed in the registry
    *
    *   @access     public
	* 	@static
    *   @param      string $handle
    *   @return     bool
    */
    public static function isRegistered( string $handle ) : bool
    {
        return array_key_exists( $handle, self::$registry ) ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   determines if class has been registered and returns the name of the object
    *
    *   @access     public
	* 	@static
    *   @param      string $class
    *   @return     mixed
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
    *   fetch function.
    * 
    *   retrieves an object from the registry
    *
    *   @access     public
	* 	@static
    *   @param      string $handle
    *   @return     object
    *   @throws     NerbError
    */
    public static function fetch( string $handle )
    {
        // check to see if the object is registered
        // and if so, returns the object
        if ( self::isRegistered( $handle ) ) {
            return self::$registry[ $handle ];
        } else {
            throw new NerbError( 'Object <code>['.$handle.']</code> is not registered' );
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !CLASS LOADING METHODS

    #################################################################




    /**
    *   loadClass function.
    * 
    *   loads a class definition for use
    *
    *   A class must be formatted in the form of 'Class.php' and contain class 'Class' as specified by
    *   $class, otherwise an exception will be thrown.
    *
    *   If a directory is specified, loadClass will first search that directory, then append the directory to the path, an if not found,
    *   it will search the paths specified in Nerb::$path.   If still not found, an exception will be thrown.
    *
    *   @access     public
	* 	@static
    *   @param      string $class (class name)
    *   @param      string $dir (directory default is ./app/lib if none is given)
    *   @return     bool
    *   @throws     NerbError
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
        if ( class_exists( $class ) ) {
            return true;
        } else {
            throw new NerbError( 'Class <code>['.$class.']</code> was not contained in $class.php' );
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   loadFile function.
    * 
    *   loads and includes a file
    *
    *   If a directory is specified, loadClass will first search that directory, then append the directory to the path, an if not found,
    *   it will search the paths specified in Nerb::$path.   If still not found, an exception will be thrown.
    *
    *   @access     public
	* 	@static
    *   @param      string $file (class name)
    *   @param      string $dir (directory default is ./app/lib if none is given)
    *   @return     mixed
    */
    public static function loadFile( string $file, string $dir = null )
    {
        //   if directory is given, directly look in directory, otherwise it will attempt to autodiscover
        //   classes using the paths listed in $path

        // set relative path
        if ( $dir ) {
            $file = $dir.'/'.$file;
        }
        
        if ( file_exists( $file ) ) {
            return require_once $file;
        } else {
            if ( $included = self::searchPath( $file ) ) {
                return include $included;
            } else {
                $error = 'Could not load file <code>'.$file.'</code> using the following include paths:<br /><code>';
                foreach ( self::$path as $path ) {
                    $error .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$path.'<br />';
                }
                $error .= '</code> Please check to ensure the path specified is correct or that the file exists';
                throw new NerbError( $error );
            }
        }// end if

        return false;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   autoload function.
    * 
    *   Autoloads a class definition for use
    *
    *   @access     public
	* 	@static
    *   @param      string $class
    *   @return     void
    *	@see		loadClass()
    */
    public static function autoload( string $class )
    {
        self::loadClass( $class );
               
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
    *   namespaceWrap function.
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
	  if ( !is_scalar( $name ))
	    return $name;
	  return strpos( $name, '\\' ) !== false ? $name : ( __NAMESPACE__ . '\\' . $name );
	  
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	







   #################################################################

    //            !PATH METHODS

    #################################################################


    /**
    *   setPath function.
    * 
    *   adds a system path to the global registry
    *   if a path alias is present, it will be added as $path[ $alias ]
    *   else it will be appended to the end of the $path array
    *
    *   @access     public
	* 	@static
    *   @param      string $path
    *   @param      string $alias
    *   @throws     NerbError
    *   @return     void
    */
    public static function setPath( string $path, string $alias = null ) : bool
    {

        // check to see if the path is a valid directory
        if ( !is_dir( $path ) )
            throw new NerbError( 'Set path error -- <code>['.$path.']</code> is not a valid path.' );
        
        self::$path[$alias] = $path;
        
        return true;
       
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   getPath function.
    * 
    *   returns the application path
    *
    *   @access     public
	* 	@static
    *   @param      string $alias
    *   @return     string
    */
    public static function getPath( string $alias = null ) : string
    {
        return $alias ? self::$path[ $alias ] : self::$path;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   searchPath function.
    * 
    *   searches Nerb::$path for a file and returns the location of that file
    *
    *   @access     public
	* 	@static
    *   @param      string $file (file name)
    *   @return     mixed (file name on success, false on failure)
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





    #################################################################

    //            !JUMP METHODS

    #################################################################


    /**
    *   jump function.
    * 
    *   Jumps to a new page.  probably the single most important function in this file
    *
    *   @access     public
	* 	@static
    *   @param      string $url (The url of page that is being jumpped to)
    *   @return     void
    */
    public static function jump( string $url )
    {
        global $HTTP_USER_AGENT;

        // if no url is given, the it is assumed that a jump to root url ( / ) is intended
        $url = (  !$url  ) ? '/' : $url;

        // this is a microsoft refresh BUG
        if ( strstr( strtolower( $HTTP_USER_AGENT ), 'msie' ) ) {
            Header( 'Location: $url' );
        } else {
            echo "<META HTTP-EQUIV='Refresh' CONTENT=\"0; URL='$url' />".PHP_EOL;
        }

        exit;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !DEBUGGING METHODS

    #################################################################


    /**
    *   Returns the current configuration of Nerb and lists current constants
    *
    *   @access     protected
    *   @return     string
    */
    public static function status() : array
    {
    	$config = array();
    	$const = get_defined_constants( true );
    	foreach( $const['user'] as $key => $value){
	    	$config[$key] =  str_replace( APP_PATH, '..', $value );
		}
    	return $config;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	    

    /**
    *   Returns a list of the currently loaded modules
    *
    *   @access     protected
    *   @return     string
    */
    public static function modules() : array
    {
    	$modules = array();
    	// get loaded classes
    	$classes = get_declared_classes();
    	foreach( $classes as $key => $value ){
    		if( stristr($value, 'Nerb') )
    			$modules[] = $value;
    	} // end foreach
    	
    	// sort them in alphabetical order
    	sort($modules);
    	
    	return $modules;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	    

    /**
    *   Debugging function will dump the value of a formatted variable and dies if needed
    *
    *   @access     protected
    *   @param      mixed $var variable to be inspected
    *   @param      bool $die (if true, then kills the script after printing varible)
    *   @param      string $title (name to display if using multiple inspections)
    *   @return     void
    */
    public static function inspect( $var, bool $die = false, string $title = '' )
    {

        // stop all output buffering and clear contents so hopefully the error is displayed on a clean page
        ob_end_clean();

        // begin outut buffering
        ob_start();

        // gets the trace data that lead up to the error
        $trace_data = debug_backtrace();

        if ( SHOW_TRACE ) {
            //array_shift (  $trace_data  );
            $trace_data = array_reverse( $trace_data );

            $count = count( $trace_data );
            $count = 1;
            $trace = '<p><strong>Trace</strong></p>'
                    .'<div>#0 {INIT}</div>';

            foreach ( $trace_data as $node ) {
                $trace .= '<div>#'.$count++.':&nbsp;'.str_replace( APP_PATH, '', $node['file'] ).' ( <strong>'.$node['line'].'</strong> ) &mdash; '.$node['class'].$node['type'].$node['function'].'()</div>';
            }
        } else {
            $node = array_shift( $trace_data );
            $trace = '<p><strong>Inspect: '.$title.'</strong></p>';
            $trace .= '<div><code>['.str_replace( APP_PATH, '', $node['file'] ).' ( '.$node['line'].' )]</code></div>';
        }

        echo $trace;
        echo '<pre>';
        print_r( $var );
        echo '</pre>';
        echo '<hr>';

        // end outbuffering and clear buffer
        ob_flush();

        if ( $die ) die;
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




} /* end class */
