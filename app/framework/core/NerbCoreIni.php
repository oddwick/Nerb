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
 * @class           Core
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
class NerbCoreIni extends Core
{

	/**
	 * config
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $config = array();
	
	

    /**
     * Singleton Pattern prevents multiple instances of NerbUtility.  all calls must be made statically e.g. NerbUtility::function(  args  );
     *
     *   @access     public
     *   @return     void
     */
    public function __construct( $ini_file )
    {
    	$this->process_ini( $ini_file );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	/**
	 *	Function 
	 *
	 *	@access		protected
	 *	@param		string $ini_file
	 *	@return		self
	*/
	protected function process_ini( string $ini_file ) 
	{
		// process the ini file and merge it to params array
		$config = new Ini( $ini_file );
		
		// dump the params into array
        $this->config = $config->dump();
        		
	}  // end function -----------------------------------------------------------------------------------------------------------------------------------------------





	/**
	 *	Function 
	 *
	 *	@access		protected
	 *	@param		string $ini_file
	 *	@return		self
	*/
	protected function process_properties( string $ini_file ) 
	{
		// process the ini file and merge it to params array
		$config = new Ini( $ini_file );
		
		// dump the params into array
        $this->config = $config->dump();
        
        // cycle through and add them to class properties
        foreach( $this->config as $key => $value ){
	        if( !property_exists( $this, $key ) ){
				throw new Error( "The property <code>[$key]</code> does not exist.  Check your page.ini for proper spelling and syntax." ); 
	        }		        
			$this->$key = $value;
        }
		
	}  // end function -----------------------------------------------------------------------------------------------------------------------------------------------





} /* end class */
