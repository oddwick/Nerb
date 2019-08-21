<?php
// Nerb Application Framework
Namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * Class with a suite of debugging tools to help with setting up your site
 *
 *
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbDebug
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @todo
 * @requires        NerbError
 * @requires        ~/config.ini
 *
 */


/**
 *
 * Base class for generating site framework
 *
 */
class Debug
{
    
    /**
     * __construct public
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    /**
     *   Returns the current configuration of Nerb and lists current constants
     *
     *   @access     public
     * 	 @static
     *   @return     array
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
     *   Displays the current render time for the page 
     *
     *   @access     public
     * 	 @static
     *   @return     void
     */
    public static function render()
    {
	    echo '<pre>';
        echo 'Rendered in '.(microtime()-RENDER).'ms'.PHP_EOL;
	    echo '</pre>';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	    

    /**
     * showCookies function.
     * 
     * @access public
     * @static
     * @return void
     */
    public static function showCookies()
    {
	    echo '<pre>';
        print_r($_COOKIE);
	    echo '</pre>';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	    

    /**
     * showSession function.
     * 
     * @access public
     * @static
     * @return void
     */
    public static function showSession()
    {
	    echo '<pre>';
        print_r($_SESSION);
	    echo '</pre>';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	    

    /**
     * showServer function.
     * 
     * @access public
     * @static
     * @return void
     */
    public static function showServer()
    {
	    $server = $_SERVER;
		$clean_data = array_map( function( $server ) {
			// clean up paths
		    return str_replace( $_SERVER['DOCUMENT_ROOT'], '..', (str_replace( $_SERVER['PHP_DOCUMENT_ROOT'], '..', $server )));
		}, $server );	    
	    
	    echo '<pre>';
        print_r($clean_data);
	    echo '</pre>';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	    

    /**
     *   Returns a list of the currently loaded modules
     *
     *   @access     public
     * 	 @static
     *   @return     array
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
     *   @access     public
     * 	 @static
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
        $trace_data = array_reverse(debug_backtrace());

        $count = count($trace_data);
        $count = 1;
        $trace = '<p><strong>Trace</strong></p>'
                .'<div>#0 {INIT}</div>';

        foreach ($trace_data as $node) {
            $trace .= '<div>#'.$count++.':&nbsp;'.str_replace(APP_PATH, '', $node['file']).' ( <strong>'.$node['line'].'</strong> ) &mdash; '.$node['class'].$node['type'].$node['function'].'()</div>';
        }

        echo '<h2>Inspect '.$title.'</h2>';
        echo $trace;
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        echo '<hr>';

        // end outbuffering and clear buffer
        ob_flush();

        if ($die) die;
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




} /* end class */
