<?php
// Nerb Application Framework
namespace nerb\framework;


/**
 *  This is a container class for Nerb for using ini file to generate parameters
 *  from a config file.
 *  the parameters are read from a .ini file and the file MUST contain
 *  a [params] section for the default getters and setters to work
 *
 *  config.ini must contain a section named [setup] with a key named default_key=[some_value]
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Params
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 *
 */


class Params
{


    /**
     *  params
     *
     *  ( default value: array() )
     *
     *  @var array
     *  @access protected
     */
    protected $params = array();


    /**
     *  Constructor initiates Param object
     * 
     * @access public
     * @param string $ini_file
     * @param bool $read_sections (default: false)
     * @param array $additional_params (default: array())
     * @return self
     */
    public function __construct( array $params )
    {
        
        // transfer the params into the params property
        $this->addParams($params);

		// for debugging
		//$this->debug();
		//NerbDebug::inspect(  $this->params, true  );
		
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  setter function.
     *
     *  @access public
     *  @param string $key
     *  @param mixed $value
     *  @return mixed
     */
    public function __set( string $key, string $value )
    {
        // get original value
        $old = $this->params[$key];

        // set new value
        if( is_array($this->params[$key])){
            $this->params[$key][] = $value;
        } else {
            $this->params[$key] = $value;
        }

        // return old value
        return $old;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  getter function.
     *
     *  @access public
     *  @param string $key
     *  @return mixed
     */
    public function __get(string $key)
    {
        // returns value
        return $this->params[$key];
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * get function that can be used to access arrays.
     * 
     * @access public
     * @param string $key
     * @param string $subkey (default: '')
     * @return mixed
     */
    public function get( string $key, string $subkey = '' )
    {
        // returns subkey value
        return empty( $subkey ) ? $this->params[$key] : $this->params[$key][$subkey];

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * set function that can set array value.
     * 
     * @access public
     * @param string $key
     * @param string $subkey (default: '')
     * @return string
     */
    public function set( string $key, string $value, string $subkey = '' ) : string
    {
        // get original value
        $old = $this->params[$key];

        // set new value
        if( $subkey ){
            $this->params[$key][$subkey] = $value;
        } else {
            $this->__set( $key, $value );
        }

        // return old value
        return $old;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  sets the param values from an array
     *
     *  @access     public
     *  @param      array $params
     *  @param      bool $replace (default = false)
     *  @return     self
     */
    public function addParams( array $params, bool $replace = FALSE ) : self
    {
        // overwrite array if replace is true, otherwise merges arrays
        $this->params = $replace ? $params : array_merge($this->params, $params);

        // return this
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  sets and individual parameter
     *
     *  @access     public
     *  @param      string $key
     *  @param      string $value
     *  @return     self
     */
    public function addParam(string $key, string $value) : self
    {
        // overwrite defaults with changed values if a params array is given
        $this->params[$key] = $value;

        // return this
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Get all parameters at once
     *
     *   @access     public
     *   @param      string $section
     *   @return     array (the entire parameter array is returned)
     */
    public function dump( string $section = '' ) : array
    {
        // if section is given, return specific section, otherwise the whole params
        return empty($section) ? $this->params : $this->params[$section];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
    /**
     * globalize function.
     *
     * turns the non array elements of the params array into global constants
     * 
     * @access public
     * @return self
     */
    public function globalize( $section = '' ) : self
    {
	    // determine which section to globalize
	    $section = $section ? $this->params[$section] : $this->params;
	    
        // cycle through ini and convert each key to UPERCASE constant
        foreach( $section as $key => $value ){
            if( !is_array( $value ) ){
                define( strtoupper($key), $value ); 
            }
        } // end foreach
        return $this;
    }




    /**
     * debug function.
     * 
     * @access protected
     * @return void
     */
    protected function debug(){
        echo "<pre>";
        print_r($this->params);
        print_r($this->defaults);
        echo "</pre>";
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
} // end class
