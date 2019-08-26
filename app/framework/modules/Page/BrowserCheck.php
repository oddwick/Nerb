<?php
// Nerb Application Framework
namespace nerb\framework;


/**
 *  This is a page generation class that allows one to quickly generate a page from a psuedo templage
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           BrowserCheck
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @property string MODULES
 * @requires Error
 * @requires NerbCache
 * @requires page.ini
 * @todo
 *
 */


class BrowserCheck
{
    
    /**
     * browser
     * 
     * @var array
     * @access protected
     */
    protected $browser = array();

 



    /**
     * __construct function.
     * 
     * @access private
     * @final
     * @return void
     */
    private final function __construct() {} // end function 



    /**
     * browser_check function.
     * 
     * This function checks to see if the browser passes the minimum reqirements of the site and is set using the following
     * variables set in the page.ini file:
     *	(bool) browser_check
     *	(bool) browser_fail
     *
     * minimum versions are set on a per browser basis in page.ini and a cookie/session variable is set once the initial 
     * browser check has been conducted.  	
     * 
     * @access protected
     * @return bool
     */
    public static function check()
    {
	    $browsers = self::process_ini( CONFIG.'/PageBrowser.ini');
	    
	    // get browser information
	    // this requires browsercap.ini to be set up on the server and can be
	    // checked through the phpinfo() function
		$browser = get_browser();

		// set flags to indicate what type of device the user is on
		// to determine if you want to serve mobile specific versions of the site
		$_SESSION['browser'] = $browser->browser;
		$_SESSION['browser_version'] = $browser->version;
		$_SESSION['browser_device'] = $browser->device;
		$_SESSION['browser_platform'] = $browser->platform;
		
		if( $browsers[ $browser->browser ] > $browser->version ){
			// set session var to indicate browser failure
			$_SESSION['browser_check'] = 'fail';
			return false;
		} 
			
		$_SESSION['browser_check'] = 'pass';
		return true;
			
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 *	Function 
	 *
	 *	@access		protected
	 *	@param		string $ini_file
	 *	@property	array $params->browser
	 *	@return		self
	*/
	protected static function process_ini( string $ini_file ) 
	{
		// process the ini file and merge it to params array
		$params = new Ini( $ini_file, false );
		
		// dump the params into array
        return $params->browser;
        
	}  // end function -----------------------------------------------------------------------------------------------------------------------------------------------

    
 

} // end class
