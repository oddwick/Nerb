<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Setup
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */


/**
 *
 * Simple class for sanitizing user input
 *
 */
class Sanitize
{


    /**
     * regex
     * 
     * @var array
     * @access protected
     * @static
     */
    protected static $regex = array(
	    'string' => '([^0-9\-])',
	    'alpha' => '([^a-zA-Z ])',
	    'alphanum' => '([^0-9a-zA-Z ])',
	    'num' => '([^0-9\.\?\_\*\-])',
	    'bool' => '([^0-9\-])',
	    'date' => '([^0-9\-])',
	    'phone' => '([^0-9\.\-\+\(\)])',
    );
    
    /**
     * datatypes
     * 
     * (default value: array(
     *         'string',
     *         'alpha',
     *         'alphanum',
     *         'num',
     *         'bool',
     *         'date',
     *         'email',
     *         'phone'
     *     ))
     * 
     * @var string
     * @access protected
     * @static
     */
    protected static $datatypes = array(
        'string',
        'alpha',
        'alphanum',
        'num',
        'bool',
        'date',
        'email',
        'phone'
    );
    
    
    /**
     * datatype
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected static $datatype = '';
    
    /**
     * data
     * 
     * @var mixed
     * @access protected
     */
    protected static $data = array();


    /**
     * Setup utility for creating framework enviornment
     *
     *   @access     public
     *   @return     mixed sanitized data
     */
    public function __construct( $data, string $datatype = 'string' )
    {
    	return $data;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * install function.
     * 
     * @access public
     * @static
     * @return bool
     */
    public static function data( string $data, string $datatype = 'string' )
    {
    	return $data;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * install function.
     * 
     * @access public
     * @static
     * @return array
     */
    public static function array( array $data, string $datatype = 'string' ):array
    {
    	if( !in_array( $datatype, self::$datatypes ) ){
	    	throw new Error('<code>'.$datatype.'</code> is not a valid datatype');
    	}   	
    	
    	// trim the whitespace from the array
    	array_walk( $data, ['self', 'trim'] );

    	// sanitize the array by datatype
    	array_walk( $data, ['self', $datatype] );

    	return $data;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	

    /**
     * string function.
     * 
     * @access protected
     * @static
     * @param string $data
     * @return string
     */
    protected static function string( string &$data, string $key )
    {

		$data = filter_var( $data , FILTER_SANITIZE_STRING, !FILTER_FLAG_STRIP_LOW );
		$data = htmlentities($data, ENT_QUOTES );
		$data = nl2br($data);
    	//$data .= '-string';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


   
   
    /**
     * alpha function.
     * 
     * @access protected
     * @static
     * @param string &$data
     * @param string $key
     * @return void
     */
    protected static function alpha( string &$data, string $key )
    {
        $data = preg_replace( '/'.self::$regex['alpha'].'/', '', $data );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


   
   
    /**
     * alphanum function.
     * 
     * @access protected
     * @static
     * @param string &$data
     * @param string $key
     * @return void
     */
    protected static function alphanum( string &$data, string $key )
    {
        $data = preg_replace( '/'.self::$regex['alphanum'].'/', '', $data );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


   
   
    /**
     * num function.
     * 
     * @access protected
     * @static
     * @param string &$data
     * @param string $key
     * @return void
     */
    protected static function num( string &$data, string $key )
    {
        $data = preg_replace( '/'.self::$regex['num'].'/', '', $data );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


   
   
    /**
     * bool function.
     * 
     * @access protected
     * @static
     * @param mixed &$data
     * @param string $key
     * @return void
     */
    protected static function bool( &$data, string $key )
    {
		$data = filter_var($data, FILTER_VALIDATE_BOOLEAN); 
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

    
    
    
    /**
     * date function.
     * 
     * @access protected
     * @static
     * @param string &$data
     * @param string $key
     * @return void
     */
    protected static function date( string &$data, string $key )
    {
    	// ignore empty strings
    	if( !$data ) return;
    	
    	$data = strtotime( $data );
    	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


   
   
    /**
     * email function.
     * 
     * @access protected
     * @static
     * @param mixed &$data
     * @param string $key
     * @return void
     */
    protected static function email( &$data, string $key )
    {
		$data = filter_var($data, FILTER_SANITIZE_EMAIL); 
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     * phone function.
     * 
     * @access protected
     * @static
     * @param mixed &$data
     * @param string $key
     * @return void
     */
    protected static function phone( &$data, string $key )
    {
        $data = preg_replace( '/'.self::$regex['phone'].'/', '', $data );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * trim function.
     * 
     * @access protected
     * @static
     * @param mixed &$data
     * @param string $key
     * @return void
     */
    protected static function trim( &$data, string $key )
    {
		$data = trim($data); 
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





} /* end class */
