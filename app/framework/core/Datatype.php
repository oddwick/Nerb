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
 * int - 0-9 -
 * digit - 0-9
 * num - 0-9 .?_*-
 * float - 0-9.-
 * email - filters email
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
     * datatype
     * 
     * @var string
     * @access protected
     */
    protected $datatype;
    
    /**
     * types
     * 
     * (default value: array(
     *         'str',
     *         'alpha',
     *         'alphanum',
     *         'int',
     *         'float',
     *         'metaphone',
     *         'bool',
     *     ))
     * 
     * @var string
     * @access protected
     */
    protected static $types = array(
        'string',
        'alpha',
        'alphanum',
        'int',
        'num',
        'float',
        'metaphone',
        'email',
        //'bool',
    );
    
    /**
     * regex
     * 
     * @var mixed
     * @access protected
     * @static
     */
    protected static $regex = array(
	    'int' => '([^0-9\-])',
	    'alpha' => '([^a-zA-Z ])',
	    'alphanum' => '([^0-9a-zA-Z ])',
	    'float' => '([^0-9\.\-])',
	    'num' => '([^0-9\.\?\_\*\-])',
	    'digit' => '([^0-9])',
    );
    
    /**
     * invalid_char
     *
     * list of characters to filter out of search keywords
     * allowed characters are:
     *  - * ? _ . , !
     * 
     * (default value: array(
     *         '(', ')', '[', ']', '{', '}', '~', '`', '@', '#', '$', '%', '^', '&', ':', ')
     * 
     * @var string
     * @access protected
     * @static
     */
    protected static $invalid_char = array(
        '(', ')', '[', ']', '~', '`', '@', '#', '$', '%', '^', '&', ':', ';', '<', '>', '|', '=', '\\', '/',
    );




    /**
     * __construct function.
     * 
     * @access public
     * @param string $datatype
     * @return Datatype
     */
    public function __construct( string $datatype )
    {
        // sets the datatype with error checking
        $this->set( $datatype );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * set function.
     *
     * sets the datatype with which to compare the string
     * 
     * @access public
     * @param string $datatype
     * @throws Error
     * @return Datatype
     */
    public function set( string $datatype ) : Datatype
    {
        // force lowercase
        $datatype = strtolower($datatype);
        if ( !in_array($datatype, self::$types) ) {
                throw new Error('Invalid datatype.  Datatypes must be <code>['.implode('|', self::$types).']</code>');
        }

        $this->datatype = $datatype;
        return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * check function.
     *
     * performs the actual datacheck
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public function check( string $string ) : ?string
    {
        $method = $this->datatype;
        return $this->$method( $string );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * int function.
     *
     * clears any non numeric character
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public static function int( string $string ) : ?string
    {
        return preg_replace('/'.self::$regex['int'].'/', '', $string);
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * float function.
     *
     * clears any non numeric character or period
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public static function float( string $string ) : ?string
    {
        return preg_replace( '/'.self::$regex['float'].'/', '', $string );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * num function.
     *
     * this is used for database searches which allows the wildcards [-|_|*|?] to be used
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public static function num( string $string ) : ?string
    {
        // replace and make sure that there is at least a single digit before returning
        return preg_replace( '/'.self::$regex['num'].'/', '', $string );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * ditig function.
     *
     * returns only 0-9
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public static function digit( string $string ) : ?string
    {
        return preg_replace( '/'.self::$regex['digit'].'/', '', $string );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * alphanum function.
     * 
     * clears any non alphanumeric or space character
     *
     * @access public
     * @param string $string
     * @return string
     */
    public static function alphanum( string $string ) : ?string
    {
        return self::whitespace( preg_replace( '/'.self::$regex['alphanum'].'/', '', $string ) );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * alpha function.
     *
     * clears any non alpha or space character
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public static function alpha( string $string ) : ?string
    {
        return self::whitespace( preg_replace('/'.self::$regex['alpha'].'/', '', $string));
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * metaphone function.
     *
     * clears any non alpha character and returns a metaphone
     * 
     * @access public
     * @param string $string 
     * @return string
     */
    public static function metaphone( string $string ) : ?string
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
     * @return string
     */
    public static function string( string $string ) : ?string
    {
        $replace = '\\'.implode( '\\', self::$invalid_char );
        return self::whitespace( preg_replace( '/(['.$replace.'])/', '', $string ) );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

 
 
                
    /**
     * email function.
     * 
     * @access public
     * @static
     * @param string $email
     * @return string
     */
    public static function email( string $email ) : ?string
    {
		return filter_var($email, FILTER_SANITIZE_EMAIL);

	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------

 
 
                
    /**
     * whitespace function.
     *
     * removes any extra whitespace as a result of replaced characters
     * 
     * @access protected
     * @param string $string
     * @return string
     */
    protected static function whitespace( string $string ) : ?string
    {
        // replace any extra whitespace with a single space
        return trim(preg_replace('/\s+/', ' ', $string));
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

                
} // end Datatype
