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
 * Container class for simple utility functions
 *
 */
class Setup
{

    /**
     * Setup utility for creating framework enviornment
     *
     *   @access     public
     *   @return     void
     */
    public function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * install function.
     * 
     * @access public
     * @static
     * @return bool
     */
    public static function install()
    {
    	return true;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * This funciton builds the sites configuration file that sets all of the 
     * required constants.  The configurations MUST be in the defined CONFIG 
     * directory and contain two sections: [scope] and [params].  [params] contains
     * the data for that particular module and [scope][global_config] determines if
     * it is used as global constants or if it is a local configuration file that only
     * applies to that class 
     * 
     * @access public
     * @static
     * @return void
     */
    public static function reconfigure()
    {
	    // include required classes
	    require_once FRAMEWORK.'/core/Core.php';
	    
        // error checking to make sure that config directory exists
        if( !is_dir(CONFIG)){
	        Core::halt("Configuration directory was not found.");
        }
        
        // get configuration files
        $files = glob( CONFIG.'/*.ini');
        
        $config = '';
        
        // loop and parse config files
        foreach( $files as $file ){
	        $data = self::parse( $file );
	        if( $data['scope']['global_config'] ){
	        	$module = str_replace(CONFIG.'/', '', str_replace('.ini', '', $file));
	        	$config .= "// --{$module}--".PHP_EOL;
		        $config .= self::format( $data['params'] );
	        	$config .= PHP_EOL;
	        }
        } // end foreach
       
        // write out the new configuration
        self::write( $config );
        
        // stop execution with a message to let user know config has been created
        Core::halt( 'Config file has been created, Restart application and reset flags to continue' );
    	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * This writes the configuration out to a file in the CONFIG directory named .Config.cfg
     * 
     * @access protected
     * @static
     * @return void
     */
    protected static function write( string $data )
    {
	    $config = '<?PHP'.PHP_EOL;
	    $config .= self::head();
	    $config .= $data;
	    $config .= PHP_EOL.'?>';
        // append contents to file
        file_put_contents ( CONFIG.DIRECTORY_SEPARATOR.'.Config.cfg'  , $config , LOCK_EX );
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	    


	
	/**
	 * parse function.
	 * 
	 * @access protected
	 * @param string $ini_file
     * @static
	 * @return array
	 */
	protected static function parse( string $ini_file ) : array
	{
        // load and parse ini file and distribute variables
        // the user changeable variables will end up in $params and the defaults will be kept in $defaults
        try {
            // if the config.ini file is read, it loads the values into the params
            $params = parse_ini_file($ini_file, true, INI_SCANNER_TYPED);
        } catch (\Exception $e) {
            Core::halt(
                'Could not parse configuration file <code>'.$ini_file.'</code>. <br /> 
					Make that it is formatted properly and conforms to required standards.'
            );
        }// end try
        
        // make sure that the ini file was read
        if ( empty( $params )) {
            Core::halt(
                'Configuration file <code>[$ini]</code> appears to be empty.'
                );
        }
		
        // and make sure that it was read properly and created an array with a params section
        if ( empty( $params['params'] ) ) {
            Core::halt( 
                'Could not parse configuration file <code>['.$ini_file.']</code>.<br /> 
					Make that it is formatted properly and conforms to required standards. '
                );
        }
		
        return $params;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	/**
	 * format function.
	 * 
	 * @access protected
	 * @param array $data
     * @static
	 * @return string
	 */
	protected static function format( array $data )
	{
		$config = '';
		foreach( $data as $key => $value ){
			if( is_bool($value)){
				$value = $value ? "true" : "false";
			}
			
			elseif(is_string($value)){
				$value = "'{$value}'";
			}
			
			// causes issues with php7
			$config .= "defined('".strtoupper($key)."') or define('".strtoupper($key)."', $value );".PHP_EOL;
			//$config .= "define('".strtoupper($key)."', $value );".PHP_EOL;
		} // end foreach
		
		return $config;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	/**
	 * head function.
	 * 
	 * @access protected
     * @static
	 * @return string
	 */
	protected static function head() : string
	{
		$header =  'namespace nerb\framework;'.PHP_EOL.PHP_EOL
		. '/*'.PHP_EOL
		.' ----------------------------------------------------------------------------------------------------------------'.PHP_EOL
		.' Nerb Application Framework working configuration'.PHP_EOL
		.' '.PHP_EOL
		.' This file contains the current working configuration of your application and any changes made to it'.PHP_EOL
		.' will take effect immediately.  This file will be overwritten the next time Setup->reconfigure() or Core::init(true)'.PHP_EOL
		.' is called and all changes will be lost.'.PHP_EOL
		.' '.PHP_EOL
		.' Created '.date('M d, Y - h:i:s', time() ).PHP_EOL
		.' ----------------------------------------------------------------------------------------------------------------'.PHP_EOL
		.'*/'.PHP_EOL.PHP_EOL;
		return $header;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
