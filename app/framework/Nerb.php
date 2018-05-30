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
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */


// defines the software constants
define( 'SOFTWARE', 'Nerb Application Framework' );
define( 'VERSION', '1.0' );
define( 'COPYRIGHT', 'Copyright &copy;2001-'.date( 'Y' ).' Oddwick Ltd.' );
define( 'LICENSE', 'https://www.oddwick.com/docs/license' );


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
    *   @access     public
	* 	@final
    *   @return     void
    */
    final private function __construct()
	{
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Initializes the framework
    *
    *   @access     public
	* 	@static
    *   @param      array $params
    *   @return     void
    */
    public static function init( array  $params = array()  )
    {
		
		//load configuration file
		self::loadConfig();

        // add directories to the path
        self::$path['root'] = FRAMEWORK;
		self::$path['modules'] = MODULES;
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
        self::$url = $params['URL'] ?? $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].(  str_replace( $_SERVER['DOCUMENT_ROOT'], '', self::$path['root'] )  );
        
        
        // Load required classes
        self::loadClass( 'NerbError' );
        
        // sets the default exception handler for all uncaught errors
        if( CATCH_EXCEPTIONS ) set_exception_handler( array('Nerb', 'exception_handler'));
		
        // sets the default error handler to catch all errors
		if( CATCH_ERRORS ) set_error_handler( array('Nerb', 'error_handler'));

        // begin output buffering
        ob_start();

        return;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    #################################################################

    //      !Exceptions, Errors & logs

    #################################################################



    /**
     * simple method for adding to log files
     *
	 * @access public
	 * @static
	 * @param mixed $log_file
	 * @param mixed $message
	 * @param mixed $prefix (default: null)
	 * @return void
	 */
	public static function log( string $log_file, string $message, string $prefix = null )
	{
		
		// clean up message
		$message = html_entity_decode( strip_tags( $message ));
		
		// create timestamp object with microtime
		$micro = microtime( true );
		$formated = sprintf( '%06d',( $micro - floor( $micro )) * 1000000 );
		$timestamp = new DateTime( date( 'Y-m-d H:i:s.'. $formated , $micro ) );
		
		// build entry string with prefix if given
		$entry = ( $prefix ? $prefix.' ' : null ).'['.$timestamp->format( 'D M d h:m:s.u Y' ).'] '.$message.PHP_EOL;
		
		// append contents to file
		$status = file_put_contents ( $log_file , $entry , FILE_APPEND | LOCK_EX );
		if ( !$status ) 
			throw new NerbError( 'Unable to add entry to log');
		return $status;
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   the default error handler to make pretty error messages
    *
    *   @access     public
	* 	@static
    *   @param      Exception $exception
    *   @return     void
    *   @throws     NerbError
    */
	public static function exception_handler( $exception ): void
	{
		//throws a general error for all uncaught exceptions
		throw new NerbError( '<strong>Uncaught exception -> </strong> '.$exception->getMessage(), $exception->getTrace() );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   class for logging or supressing errors
    *
    *   @access     public
	* 	@static
    *   @param      Exception $exception
    *   @return     void
    *   @throws     NerbError
    */
	public static function error_handler( int $error_number, string $errstr, string $errfile, string $errline, array $errcontext )
	{
		if( USE_ERROR_LOGGING ){

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
			self::log( ERROR_LOG , $error , $prefix );
			
		} // end if
				
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    #################################################################

    //      !REGISTRY METHODS

    #################################################################



    /**
    *   places an object in the registry
    *
    *   @access     public
	* 	@static
    *   @param      object $object
    *   @param      string $handle
    *   @return     bool
    *   @throws     NerbError
    */
    public static function register( $object, string $handle )
    {

        if ( !is_string( $handle ) ) {
            throw new NerbError( $handle.' must be a string.' );
        }
        if ( array_key_exists( $handle, self::$registry ) ) {
            throw new NerbError( 'An object named <code>['.$handle.'::'.get_class( self::$registry[ $handle ] ).']</code> already exists in the registry' );
        }
        if ( !is_object( $object ) ) {
            throw new NerbError( 'Can not register <code>['.$handle.']</code> because is not an object.' );
        }
        
        self::$registry[ $handle ] = $object;

        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   determines if an object has been placed in the registry
    *
    *   @access     public
	* 	@static
    *   @param      string $handle
    *   @return     bool
    */
    public static function isRegistered( string $handle )
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
    public static function loadClass( $class, $dir = null ) :bool
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
    *   Autoloads a class definition for use
    *
    *   @access     public
	* 	@static
    *   @param      string $className
    *   @return     void
    *	@see		loadClass()
    */
    public static function autoload( string $name )
    {
        self::loadClass( $name );
               
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * 	wraps a class name in a namespace wrapper
	 * 
	 * 	@access public
	 * 	@static
	 * 	@param mixed $name
	 * 	@return string
	 */
	private static function namespaceWrap( $name ) 
	{
	  if ( !is_scalar( $name ))
	    return $name;
	  return strpos( $name, '\\' ) !== false ? $name : ( __NAMESPACE__ . '\\' . $name );
	  
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	

   /**
    *   loads and parses the config.ini file
    *
    *   @access     private
	* 	@static
    *   @return     void
    */
    private static function loadConfig(): bool
    {
		
		define( 'FRAMEWORK', realpath(__DIR__) );
		
		if( !is_file( FRAMEWORK.'/config.ini'  )){
			echo 'Execution terminated -<br>A required configuration file could not be found.';
			exit;
		}

		// load and parse ini file
		$data = parse_ini_file( FRAMEWORK.'/config.ini', false, INI_SCANNER_TYPED );
		
		if ( !is_array( $data )){
			echo 'Execution terminated -<br>Invalid file given for configuration.';
			exit;
		}
		
		// cycle through ini and convert each key to UPERCASE constant
		foreach( $data as $key => $value ){
			define( strtoupper($key), $value ); 
		} // end foreach
		
		return true;	
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





   /**
    *   loads additional config files specific to the app
    *
    *   @access     public
	* 	@static
    *   @return     bool
    */
    public static function addConfig( string $ini, string $path ): bool
    {
		
		// error checking to ensure file exists
		// if full path is given
		if( is_file( $path.'/'.$ini )) {
			$ini_file = $ini;
			
		// if path is relative to app root
		} else if ( is_file( APP_PATH.$path.'/'.$ini )) {
			$ini_file = APP_PATH.$path.'/'.$ini ;
	
		// oops. 
		} else {
            throw new NerbError( 'Could not locate given configuration file <code>['.$ini.']</code> using: <br><code>['.$path.']</code><br><code>[APP_PATH'.$path.']</code>' );
		}

		// load and parse ini file
		$data = parse_ini_file( $ini_file, false, INI_SCANNER_TYPED );
		
		if ( empty( $data )) {
            throw new NerbError( 
                'Configuration file <code>[$ini]</code> appears to be empty.'
             );
		}
		
		if ( !is_array( $data )) {
            throw new NerbError( 
                'Could not parse configuration file <code>['.$ini.']</code>.<br /> 
					Make that it is formatted properly and conforms to required standards. '
             );
		}
		
		// cycle through ini and convert each key to UPERCASE constant
		foreach( $data as $key => $value ){
			define( strtoupper($key), $value ); 
		} // end foreach
		
		return true;	
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






   #################################################################

    //            !PATH METHODS

    #################################################################


    /**
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
    public static function setPath( string $path, string $alias = null ) :bool
    {

        // check to see if the path is a valid directory
        if ( !is_dir( $path ) )
            throw new NerbError( 'Set path error -- <code>[$path]</code> is not a valid path.' );
        
        self::$path[$alias] = $path;
        
        return true;
       
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns the application path
    *
    *   @access     public
	* 	@static
    *   @param      string $alias
    *   @return     string
    */
    public static function getPath( string $alias = null ) :string
    {
        return $alias ? self::$path[ $alias ] : self::$path;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   searches Nerb::$path for a file and returns the location of that file
    *
    *   @access     public
	* 	@static
    *   @param      string $file file name
    *   @return     mixed file name on success, false on failure
    */
    private static function searchPath( $file )
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
    *   Jumps to a new page.  probably the single most important function in this file
    *
    *   @access     public
	* 	@static
    *   @param      string $url (The url of page that is being jumpped to)
    *   @return     void
    */
    public static function jump( $url )
    {

        // if no url is given, the it is assumed that a jump to root url ( / ) is intended
        $url = (  !$url  ) ? '/' : $url;

        // this is a microsoft refresh BUG
        if ( self::isMicrosoftBrowser() ) {
            Header( 'Location: $url' );
        } else {
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=".$url."' />\n";
        }

        exit;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Checks to see if client browser is msie
    *
    *   @access     public
	* 	@static
    *   @return     bool
    */
    public static function isMicrosoftBrowser()
    {
        global $HTTP_USER_AGENT;
        if ( strstr( strtolower( $HTTP_USER_AGENT ), 'msie' ) ) {
            return true;
        } else {
            return false;
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !DEBUGGING METHODS

    #################################################################


    /**
    *   Debugging function will dump the value of a formatted variable and dies if needed
    *
    *   @access     protected
    *   @param      mixed $var
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
