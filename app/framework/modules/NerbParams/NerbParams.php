<?php
// Nerb Application Framework


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
 * @class           NerbParams
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
Copyright (c)2019 *
 * @todo
 *
 */


class NerbParams
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
     *  defaults
     *
     *  ( default value: array() )
     *
     *  @var array
     *  @access protected
     *
     */
    protected $defaults = array();



    /**
     *  Constructor initiates Param object
     * 
     * @access public
     * @param string $ini
     * @param bool $read_sections (default: false)
     * @param array $additional_params (default: array())
     * @return void
     */
    public function __construct( string $ini, bool $read_sections = false, array $additional_params = array() )
    {
        // error checking to make sure file exists
        // if the full path is given...
        if (file_exists($ini)) {
            $ini_file = $ini;

        // if a relative path is given
        } elseif (file_exists(APP_PATH.$path .'/'. $ini)) {
            $ini_file = APP_PATH .'/'. $ini;

        // blew it
        } else {
            throw new NerbError('Could not locate given configuration file <code>'.$ini.'</code> using: <br><code>'.$path.'</code><br><code>APP_PATH'.$path.'</code>');
        }

        // load and parse ini file and distribute variables
        // the user changeable variables will end up in $params and the defaults will be kept in $defaults
        try {
            // if the config.ini file is read, it loads the values into the params
            $this->params = parse_ini_file($ini_file, $read_sections, INI_SCANNER_TYPED);
        } catch (Exception $e) {
            throw new NerbError(
                'Could not parse configuration file <code>'.$ini.'</code>. <br /> 
					Make that it is formatted properly and conforms to required standards.'
            );
        }// end try
        
		// make sure that the ini file was read
		if ( empty( $this->params )) {
            throw new NerbError( 
                'Configuration file <code>[$ini]</code> appears to be empty.'
             );
		}
		
		// and make sure that it was read properly and created an array
		if ( !is_array( $this->params )) {
            throw new NerbError( 
                'Could not parse configuration file <code>['.$ini.']</code>.<br /> 
					Make that it is formatted properly and conforms to required standards. '
             );
		}
		
        // convert dot notation to arrays and assign inital values to default array so that 
        // the default value can be retrieved if changed
        $this->defaults = $this->params = $this->parse($this->params);
        
        //$this->debug();
        
        // auto loading array at construction
        // -----------------------------------------------------------------------
        // if an array is given during instantiation, once the ini has been parsed
        // the array will be merged with the initial values
        if (!empty($additional_params)) {
            // pass the array along for injection
            $this->addParams($additional_params);
        } // end if empty array

        // for debugging
        //Nerb::inspect(  $this->params, true  );

        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  setter function.
     *
     *  @access public
     *  @param mixed $key
     *  @param mixed $value
     *  @return old
     */
    public function __set( string $key, string $value ) : string
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
    public function __get( string $key )
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
     * @return string
     */
    public function get( string $key, string $subkey = '' ) : string
    {
        // returns subkey value
        if( $subkey ){
	        return $this->params[$key][$subkey];
        }
        return $this->params[$key];
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
     *  @return     NerbParams
     */
    public function addParams( array $params, bool $replace = false ) : self
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
     *  @return     NerbParams
     */
    public function addParam(string $key, string $value) : self
    {
        // overwrite defaults with changed values if a params array is given
        $this->params[$key] = $value;

        // return this
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * parse ini files with dot(.) domain notation.
     *
     * @access public
     * @param array $data
     * @return array
     */
    public function parse(array $data) : array
    {
        $array = array();

        foreach ($data as $path => $value) {
            $temp = &$array;
            foreach (explode('.', $path) as $key) {
                $temp =& $temp[$key];
            }
            $temp = $value;
 	        //if( is_array( $value ) ) $value = $this->parse( $value );
       }
        return $config = $array;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Get all parameters at once
    *
    *   @access     public
    *   @param      string $section
    *   @return     array (the entire parameter array is returned)
    */
    public function dump(string $section = '') : array
    {
        // if section is given
        if ($section) {
            return $this->params[ $section ];
        } // return all values
        else {
            return $this->params;
        } // end if
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
	/**
	 * globalize function.
	 *
	 * turns the non array elements of the params array into global constants
	 * 
	 * @access public
	 * @return self
	 */
	public function globalize() : self
	{
		// cycle through ini and convert each key to UPERCASE constant
		foreach( $this->params as $key => $value ){
			if( !is_array( $value ) ){
				define( strtoupper($key), $value ); 
			}
		} // end foreach
		return $this;
	}




    /**
    *   Gets the default value for an option
    *
    *   @access     public
    *   @param      string $key the key of the default value
    *   @param      string $subkey the key of a secondary array
    *   @return     mixed
    */
    public function default( string $key, string $subkey = '' )
    {
        // with subkeys
        if ($subkey) {
            return $this->defaults[ $key ][ $subkey ];
        } else {
            return $this->defaults[ $key ];
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




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
		die;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
} // end class
