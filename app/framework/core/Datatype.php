<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Nerb Application Framework
 *
 * Creates simple object for data typing 
 * the string will be matched against datatypes and all non allowed 
 * characters will be stripped out
 *
 * string - alphanum and certain special chars
 * aplha - only a-z A-Z
 * alpannum - A-Z a-z 0-9
 * int - 0-9
 * float - 0-9.
 * phonetic - converts keyword to metaphone and ignores special characters
 * bool - 0|1 true|false
 *
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Datatype
 * @version         1.0
 * @requires        Database
 * @requires        Error
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019 
 *
 * @todo
 *
 */

class Datatype
{
    /**
     * invalid_char
     *
     * list of characters to filter out of search keywords
     *
     * (default value: array(
     *      '(', ')', '=', '~', '`', '@', '#', '^', '&', '[', ']','{', '}',':', '<', '>', '|',
     *  ))
     *
     * @var array
     * @static
     * @access protected
     */
    protected static $invalid_char = array(
        '(', ')', '=', '~', '`', '@', '#', '^', '&', '[', ']', '{', '}', ':', '<', '>', '|', '$',
    );




    /**
     * __construct function.
     * 
     * @access public
     * @return Datatype
     */
    public function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * int function.
     *
     * clears any non numeric character
     * 
     * @access public
     * @param string $string
     * @static
     * @return string
     */
    public static function int( string $string ) : string
    {
        return preg_replace('/([^0-9])/', '', $string);
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * float function.
     *
     * clears any non numeric character or period
     * 
     * @access public
     * @param string $string
     * @static
     * @return string
     */
    public static function float( string $string ) : string
    {
        return preg_replace( '/([^0-9\.])/', '', $string );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * alphanum function.
     * 
     * clears any non alphanumeric or space character
     *
     * @access public
     * @param string $string
     * @static
     * @return string
     */
    public static function alphanum( string $string ) : string
    {
        return self::whitespace( preg_replace( '/([^0-9a-zA-Z ])/', '', $string ) );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * alpha function.
     *
     * clears any non alpha or space character
     * 
     * @access public
     * @param string $string
     * @static
     * @return string
     */
    public static function alpha( string $string ) : string
    {
        return self::whitespace( preg_replace('/([^a-zA-Z ])/', '', $string));
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * metaphone function.
     *
     * clears any non alpha character and returns a metaphone
     * 
     * @access public
     * @param string $string 
     * @static
     * @return string
     */
    public function metaphone( string $string ) : string
    {
        return metaphone( self::alpha( $string ) );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * string function.
     *
     * kills any invalid characters defined in $invalid_char
     * 
     * @access public
     * @param string $string
     * @static
     * @return string
     */
    public static function string( string $string ) : string
    {
        $replace = '\\'.implode( '\\', self::$invalid_char );
        return self::whitespace( preg_replace( '/(['.$replace.'])/', '', $string ) );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

 
 
                
    /**
     * whitespace function.
     *
     * removes any extra whitespace as a result of replaced characters
     * 
     * @access protected
     * @param string $string
     * @static
     * @return string
     */
    protected static function whitespace( string $string ) : string
    {
        // replace any extra whitespace with a single space
        return trim(preg_replace('/\s+/', ' ', $string));
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

                
                
} // end NerbDatatype
