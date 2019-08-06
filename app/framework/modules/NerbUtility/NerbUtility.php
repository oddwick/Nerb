<?php

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbUtility
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
Copyright (c)2019 *
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
class NerbUtility
{

    static public 	$url;
    static public 	$config = array();
    static private 	$registry = array();
    static private 	$path = array();


    /**
     * Singleton Pattern prevents multiple instances of NerbUtility.  all calls must be made statically e.g. NerbUtility::function(  args  );
     *
     *   @access     public
     *   @return     void
     */
    final private function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------








    /**
     *   Checks to see if client browser is msie
     *
     *   @access     public
     *   @return     bool
     */
    public static function isMicrosoftBrowser()
    {
        global $HTTP_USER_AGENT;
        if (strstr(strtolower($HTTP_USER_AGENT), 'msie')) {
            return true;
        } else {
            return false;
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   creates a debug object for syntax checking
     *
     *   @access     public
     *   @param      string $class class being evaluated
     *   @param      string $function name of specific function
     *   @return     string
     *   @see        Debug
     *   @throws     Nerb_Error
     */
    public static function syntax( $class, $method = null )
    {

        if ( is_object( $class ) ) {
            $class = get_class( $class );
        }

        if ( !class_exists( $class ) ) {
            throw new Nerb_Error( 'Class <code>['.$class.']</code> has not been defined' );
        }

        if ( $method && !method_exists( $class, $method ) ) {
            throw new Nerb_Error( 'Method <code>['.$method.']</code> does not exist in class <code>['.$class.']</code> ' );
        }

        Nerb::loadClass( 'Nerb_Debug' );
        $debug = new Nerb_Debug;
        return $debug->syntax( $class, $method );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   translates a known file path into a URL
     *
     *   @access     public
     * 	@static
     *   @param      string $path ( the absolute or relative path being translated )
     *   @return     string
     */
    public static function path2url( $path )
    {
        // make path absolute
        $path = str_replace(   realpath(  '.'  ), '', $path  );
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$path;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Returns the max allowable server upload in human readable form
     *   This is set in the php.ini file on the server
     *
     *   @access     public
     *   @return     string
     */
    public static function getMaxUpload()
    {

        $val = ini_get('upload_max_filesize');
        $val = trim($val);
        $last = strtolower($val(strlen($val) - 1));
        $val = substr($val, 0, strlen($val) - 1);
        switch ($last) {
            case 'g':
                $val .= ' Gb';
                break;
            case 'm':
                $val .= ' Mb';
                break;
            case 'k':
                $val .= ' Kb';
                break;
        }

        return $val;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   recursively search an array
     *
     *   @access     public
     *   @param      string $needle
     *   @param      array $haystack
     *   @return     mixed the key of the array if found
     */
    public static function recursive_array_search( $needle, $haystack )
    {
        foreach ( $haystack as $key => $value ) {
            $current_key=$key;
            if ( $needle===$value or (  is_array( $value ) && recursive_array_search( $needle, $value ) !== FALSE  ) ) {
                return $current_key;
            }
        }

        return FALSE;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Debugging function will dump the value of a formatted variable and dies if needed
     *
     *   @access     protected
     *   @param      mixed $var
     *   @param      bool $die if true, then kills the script after printing varible
     *   @param      string $title name to display if using multiple inspections
     *   @return     void
     */
    public static function inspect( $var, $die = false, $title = '' )
    {

        // stop all output buffering and clear contents so hopefully the error is displayed on a clean page
        ob_end_clean();

        // begin outut buffering
        ob_start();

        // gets the trace data that lead up to the error
        $trace_data = debug_backtrace();

        if (SHOW_TRACE) {
            //array_shift (  $trace_data  );
            $trace_data = array_reverse($trace_data);

            $count = count($trace_data);
            $count = 1;
            $trace = '<p><strong>Trace</strong></p>'
                    .'<div>#0 {INIT}</div>';

            foreach ($trace_data as $node) {
                $trace .= '<div>#'.$count++.':&nbsp;'.str_replace(APP_PATH, '', $node['file']).' ( <strong>'.$node['line'].'</strong> ) &mdash; '.$node['class'].$node['type'].$node['function'].'()</div>';
            }
        } else {
            $node = array_shift($trace_data);
            $trace = '<p><strong>Inspect: '.$title.'</strong></p>';
            $trace .= '<div><code>['.str_replace(APP_PATH, '', $node['file']).' ( '.$node['line'].' )]</code></div>';
        }

        echo $trace;
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        echo '<hr>';

        // end outbuffering and clear buffer
        ob_flush();

        if ($die) die;
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    #################################################################

    //            !FORMAT METHODS

    #################################################################



    /**
     *   encloses a string in parenthese
     *
     *   @access     public
     * 	@static
     *   @param      string $string string to be encaplsulated
     *   @return     string
     */
    public static function paren( string $string )
    {
        return '('.$string.')';
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   encloses a string in curly braces
     *
     *   @access     public
     * 	@static
     *   @param      string $string string to be encaplsulated
     *   @return     string
     */
    public static function brace( string $string )
    {
        return '{'.$string.'}';
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   encloses a string in brackets
     *
     *   @access     public
     * 	@static
     *   @param      string $string string to be encaplsulated
     *   @return     string
     */
    public static function bracket( string $string )
    {
        return '['.$string.']';
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !SOCIAL METHODS

    #################################################################




    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = true, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
