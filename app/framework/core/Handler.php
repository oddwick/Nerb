<?php
// Nerb application library 
namespace nerb\framework;

/**
 * Extends php Exception for Nerb specific error messages
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Handler
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */


class Handler
{

    protected static $prefix = array(
	    E_ERROR => 'FATAL',
	    E_CORE_ERROR => 'FATAL',
	    E_COMPILE_ERROR => 'FATAL',
	    E_PARSE => 'FATAL',
	    E_USER_ERROR => 'ERROR',
	    E_RECOVERABLE_ERROR => 'ERROR',
	    E_WARNING => 'WARNING',
	    E_CORE_WARNING => 'WARNING',
	    E_COMPILE_WARNING => 'WARNING',
	    E_USER_WARNING => 'WARNING',
	    E_NOTICE => 'NOTICE',
	    E_USER_NOTICE => 'NOTICE',
	    E_STRICT => 'DEBUG',
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
        if( !LOG_ALL_ERRORS ) return;
        
        // determines if full path is shown or masked with APP_PATH
        $error_file = self::cleanPath( $error_file );
		
        // create error string
        $error = $error_file . ' (' . $error_line . ') -- ' .$error_message;
        
		self::log($error, self::$prefix[$error_number]);
		
		
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
     *   @throws     Error
     */
    public static function exception_handler( $exception )
    {
        //throws a general error for all uncaught exceptions
        throw new Error( '<strong>Uncaught exception -> </strong> '.$exception->getMessage(), $exception->getTrace() );
		
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
        Nerb::loadClass( 'NerbLog' );
        $log = new Log( ERROR_LOG );
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
