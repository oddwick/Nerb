<?php
// Nerb Application Framework

/**
 * Extends php Exception for Nerb specific error messages
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbHandler
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */


class NerbHandler
{

    protected static $prefix = array( 
        0 => 'OTHER ',
        1 => 'ERROR ',
        2 => 'WARNING ',
        4 => 'PARSE ',
        8 => 'NOTICE ',
        256 => 'ERROR ',
        512 => 'WARNING ',
        1024 => 'NOTICE ',
    );



    /**
     * __construct function.
     * 
     * Constructor for Error class
     *
     * @access public
     * @param string $message
     * @param array $trace (default: array())
     * @return void
     */
    public function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * error_handler function.
     * 
     * @access public
     * @static
     * @param int $error_number
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @return void
     */
    public static function error_handler( int $error_number, string $error_message, string $error_file, string $error_line )
    {
      //return;
        // determines if full path is shown or masked with APP_PATH
        if ( !SHOW_FULL_PATH ) {
            $error_file = self::cleanPath( $error_file );
        } 
		
        // create error string
        $error = $error_file . ' (' . $error_line . ') -- ' .$error_message;
        
		switch ($error_number) {
		    case E_ERROR:
		    case E_CORE_ERROR:
		    case E_COMPILE_ERROR:
		    case E_PARSE:
		        if( LOG_ALL_ERRORS ) self::log($error, "fatal");
		        break;
		        
		    case E_USER_ERROR:
		    case E_RECOVERABLE_ERROR:
		        if( LOG_ALL_ERRORS ) self::log($error, "error");
		        break;
		        
		    case E_WARNING:
		    case E_CORE_WARNING:
		    case E_COMPILE_WARNING:
		    case E_USER_WARNING:
		        if( LOG_ALL_WARNINGS )self::log($error, "warn");
		        break;
		        
		    case E_NOTICE:
		    case E_USER_NOTICE:
		         if( LOG_ALL_NOTICE ) self::log($error, "info");
		        break;
		        
		    case E_STRICT:
		        self::log($error, "debug");
		        break;
		        
		    default:
		        if( LOG_ALL_WARNINGS ) self::log($error, "warn");
		}
		
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   exception_handler function.
     * 
     *   the default error handler to make pretty error messages
     *
     *   @access     public
     * 	 @static
     *   @param      Exception $exception
     *   @return     void
     *   @throws     NerbError
     */
    public static function exception_handler( $exception ) : void
    {
        //throws a general error for all uncaught exceptions
        //throw new NerbError( '<strong>Uncaught exception -> </strong> '.$exception->getMessage(), $exception->getTrace() );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * log function.
     * 
     * @access protected
     * @param array $trace
     * @param string $msg
     * @param string $prefix (default: 'ERROR')
     * @return void
     */
    protected static function log( string $message, string $prefix = 'ERROR' )
	{
        // create error string
        $message = self::cleanPath($message).' -- logged from handler';
        
        // log error to file
        // WARNING | ERROR | NOTICE [date] file (line) string
        $log = new NerbLog( ERROR_LOG );
        $log->write( $message , strtoupper($prefix) );
			

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * cleanPath function.
     * 
     * @access protected
     * @param string $path
     * @return string
     */
    protected static function cleanPath( $path ) : string
    {
        return  str_replace(APP_PATH, '..', $path);
                    
    }// end function



} /* end class */
