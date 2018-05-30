<?php
// Nerb Application Framework


/**
 *  This is a container class for Nerb for using ini file to generate parameters
 *  from a config file.
 *  the parameters are read from a .ini file and the file MUST contain
 *  a [params] section for the default getters and setters to work
 *
 *	config.ini must contain a section named [setup] with a key named default_key=[some_value]
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
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */


class NerbParams implements iterator
{


    /**
     *  setup_key
     *
     *  ( default value: 'setup' )
     *
     *  @var string
     *  @access protected
     */
    protected $setup_key = 'setup';

    /**
     *  params_key
     *
     *  ( default value: 'params' )
     *
     *  @var string
     *  @access protected
     */
    protected $params_key = 'params';

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
     * pointer
     *
     * ( default value: 0 )
     *
     * @var int
     * @access protected
     */
    protected $pointer = 0;



    /**
     *  Constructor initiates Param object
     *
     * @access public
     * @param string $ini
     * @param string $path
     * @param array $params (default: array())
     * @return void
     */
    public function __construct( string $ini, string $path, array $params = array() )
    {
        // error checking to make sure file exists
        // if the full path is given...
        if ( file_exists( $path .'/'. $ini ) ) {
        	$ini_file = $path .'/'. $ini;
        	
        // if a relative path is given	
        } else if( file_exists( APP_PATH.$path .'/'. $ini ) ){
        	$ini_file = APP_PATH.$path .'/'. $ini;
        	
        // blew it
	    } else {
            throw new NerbError( 'Could not locate given configuration file <code>'.$ini.'</code> using: <br><code>'.$path.'</code><br><code>APP_PATH'.$path.'</code>' );
        }

        // load and parse ini file and distribute variables
        // the user changeable variables will end up in $params and the defaults will be kept in $defaults
        try {
            // if the config.ini file is read, it loads the values into the params
            $this->params = parse_ini_file( $ini_file, true );
        } catch ( Exception $e ) {
            throw new NerbError( 
                'Could not parse configuration file <code>'.$ini.'</code>. <br /> 
					Make that it is formatted properly and conforms to required standards.'
             );
        }// end try

        // error checking to make sure ini structure and keys are correct
        // make sure that the setup section is present
        if ( !$this->params[ $this->setup_key ] ) {
            throw new NerbError( 
                'Required section <code>['.$this->setup_key.']</code> was not found in <code>['.$ini.']</code>.'
             );
        } // end if

        // make sure that a default key is defined
        elseif ( !$this->params[ $this->setup_key ][ 'default_key'] ) {
            throw new NerbError( 
                'Required key <code>[default_key]</code> was not found in <code>['.$this->setup_key.']</code> section in  <code>'.$ini.'</code>.'
             );
        } // end else

        // make sure that the params section is found and contains values
        elseif ( empty( $this->params[ $this->params[ $this->setup_key ]['default_key'] ] ) ) {
            throw new NerbError( 
                'Required default section <code>['.$this->params[ $this->setup_key ]['default_key'].']</code> was not found in <code>'.$ini.'</code>.'
             );
        } // end else if empty params

        // copy the values from [params] to [defaults] so that the original values can be accessed even after changing
        $this->defaults = $this->params[ $this->params_key ];

        // assign the parameters key to a variable for quick access
        $this->params_key = $this->params[ $this->setup_key ]['default_key'];

        // auto loading array at construction
        // -----------------------------------------------------------------------
        // if an array is given during instantiation, once the ini has been parsed
        // the array will be merged with the initial values
        if ( !empty( $params ) ) {
            if ( !is_array( $params ) ) {
                throw new NerbError( 'Variable <code>[$array]</code> is expected to be Array, '.gettype( $params ).' given.' );
            } else {
                // pass the array along for injection
                $this->add( $params );
            } // end if is array
        } // end if empty array

        // for debugging
        //Nerb::inspect(  $this->params[ $this->params_key ], true  );

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
    public function __set( string $key, string $value ): string
    {
        // get original value
        $old = $this->params[ $this->params_key ][$key];

        // set new value
        $this->params[ $this->params_key ][$key] = $value;

        // return old value
        return $old;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  sets the param values from an array
     *
     *  @access 	public
     *  @param 		array $params
     *  @throws 	NerbError
     *  @return 	self
     */
    public function add( array $params )
    {
        // overwrite defaults with changed values if a params array is given
        if ( is_array( $params ) ) {
            $this->params[ $this->params_key ] = array_merge( $this->params[ $this->params_key ], $params );
        } else {
            throw new NerbError( 
                'Variable <code>[$params]</code> is expected to be an array. Type of <code>['.gettype( $params ).']</code> was given.'
             );
        } // end if is array

        // return old value
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  getter function.
     *
     *  @access public
     *  @param string $key
     *  @return mixed
     */
    public function __get( $key )
    {
        // returns value
        return $this->params[ $this->params_key ][ $key ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * parse ini files with dot(.) domain notation.
     * 
     * @access public
     * @param array $data
     * @return array
     */
    public function parse( array $data ): array
    {
		$array = array();
		
		foreach( $data as $path => $value ) {
		    $temp = &$array;
		    foreach(explode('.', $path) as $key) {
		        $temp =& $temp[$key];
		    }
		    $temp = $value;
		}
		return $config = $array;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   returns count of rows in rowset
    *
    *   @access     public
    *   @return     int
    */
    public function count(): int
    {
        // return the number of available elements in the array
        return count( $this->params[ $this->params_key ] );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns the current element of the set
    *
    *   @access     public
    *   @return     mixed
    */
    public function current()
    {
        // make sure that the pointer points to a valid row
        if ( $this->valid() ) {
            return current( $this->params[ $this->params_key ] );
        } else {
            return false;
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   validates the position of the pointer
    *
    *   @access     public
    *   @return     bool
    */
    public function valid(): bool
    {

        // validation check to ensure that pointer is less than available keys
        return $this->pointer < count( $this->params[$this->params_key] );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   advances the pointer
    *
    *   @access     public
    *   @return     int
    */
    public function next()
    {
        // advance the params array to the next element
        next( $this->params[ $this->params_key ] );

        // return incremented pointer
        return ++$this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   decrements the pointer
    *
    *   @access     public
    *   @return     int
    */
    public function prev()
    {
        // rewind the params array to the previous element
        prev( $this->params[ $this->params_key ] );

        // return decremented pointer
        return --$this->pointer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   returns the current position of the pointer
    *
    *   @access     public
    *   @return     string
    */
    public function key(): string
    {
        // return the string of the key of the params array
        return key( $this->params[ $this->params_key ] );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   resets the pointer to 0
    *
    *   @access     public
    *   @return     int
    */
    public function rewind(): int
    {
        // resets the params array to the beginning
        reset( $this->params[ $this->params_key ] );

        // return 0 pointer
        return $this->pointer=0;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Get all parameters at once
    *
    *   @access     public
    *   @param      string $section
    *   @return     array (the entire parameter array is returned)
    */
    public function dump( $section = null ): array
    {
        // if section is given
        if ( $section ) {
            return $this->params[ $section ];
        } // return all values
        else {
            return $this->params;
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * raw function.
     *
     *  Gets a list of raw values from the params array based on key
     *
     * @access public
     * @param string $key (registry key of the value)
     * @param string $subkey ( default: '' ) (the key of a secondary array)
     * @return void
     */
    public function value( string $key, $subkey = '' )
    {
        // returns values for subkey, otherwise just key value
        if ( $subkey ) {
            return $this->params[ $key ][ $subkey ];
        } else {
            return $this->params[ $key ];
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   Gets the default value for an option
    *
    *   @access     public
    *   @param      string $key the key of the default value
    *   @param      string $subkey the key of a secondary array
    *   @return     mixed
    */
    public function getDefault( $key, $subkey = null )
    {

        // with subkeys
        if ( $subkey ) {
            return $this->defaults[ $key ][ $subkey ];
        } else {
            return $this->defaults[ $key ];
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

} // end class
