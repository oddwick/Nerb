<?php
// Nerb Application Framework
Namespace nerb\framework;

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
     *         'string',
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
    protected $types = array(
        'string',
        'alpha',
        'alphanum',
        'int',
        'float',
        'metaphone',
        'bool',
    );
    
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
     * @access protected
     */
    protected $invalid_char = array(
        '(', ')', '=', '~', '`', '@', '#', '^', '&', '[', ']', '{', '}', ':', '<', '>', '|', '$',
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
        if ( !in_array($datatype, $this->types) ) {
                throw new Error('Invalid datatype.  Datatypes must be <code>['.implode('|', $this->types).']</code>');
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
    public function check( string $string )
    {
        $method = $this->datatype;
        return $this->$method( $string );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * invalidChars function.  adds user defined characters to list of predefined invalid characters
     *
     * @access public
     * @param array $chars
     * @param bool $replace (default = false)
     * @return Datatype
     */
    public function invalidChars(array $chars, bool $replace = false) : Datatype
    {
        // replace list
        if ($replace) {
            $this->invalid_char = $chars;
        } 
        
        // merge to existing list
        else {
            $this->invalid_char = array_merge($chars, $this->invalid_char);
        }
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * stopWord function.
     *
     * @access public
     * @param string $char
     * @return Datatype
     */
    public function invalidChar(string $char) : Datatype
    {
        // add to list
        $this->invalid_char[] = $char;
        return $this;
        
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
    public function int( string $string ) : string
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
     * @return string
     */
    public function float( string $string ) : string
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
     * @return string
     */
    public function alphanum( string $string ) : string
    {
        return $this->whitespace( preg_replace( '/([^0-9a-zA-Z ])/', '', $string ) );
		
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
    public function alpha( string $string ) : string
    {
        return $this->whitespace( preg_replace('/([^a-zA-Z ])/', '', $string));
		
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
    public function metaphone( string $string ) : string
    {
        return metaphone( $this->alpha( $string ) );
		
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
    public function string( string $string ) : string
    {
        $replace = '\\'.implode( '\\', $this->invalid_char );
        return $this->whitespace( preg_replace( '/(['.$replace.'])/', '', $string ) );
		
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
    protected function whitespace( string $string ) : string
    {
        // replace any extra whitespace with a single space
        return trim(preg_replace('/\s+/', ' ', $string));
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

                
} // end Datatype
