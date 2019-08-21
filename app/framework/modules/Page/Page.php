<?php
// Nerb Application Framework
Namespace nerb\framework;


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
 * @class           Page
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


class Page
{
    /**
     * params
     *
     * The page data is stored in page.ini
     * 
     * @var NerbParams
     * @access protected
     */
    protected $params;

    /**
     * asynch_scripts
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $asynch_scripts = false;     
    
    /**
     * scripts_in_header
     * 
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $scripts_in_header = true;

    /**
     * cache_control
     *
     * html cache control
     * 
     * (default value: 'nocache')
     * 
     * @var string
     * @access protected
     */
    protected $cache_control = 'nocache'; 

    /**
     * browser_check
     *
     * Initiates a browser check if true.  In order for this to work, 
     * the server must be properly configured
     * 
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $browser_check = true;

    /**
     * preprocess
     *
     * flag that determines if page is processed when it is added or if when page is rendered
     * 
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $page_preprocess = true;

    /**
     * browser_fail
     * 
     * (default value: 'warn')
     * 
     * @var string
     * @access protected
     */
    protected $browser_fail = 'warn'; 
    
    /**
     * browser
     * 
     * @var array
     * @access protected
     */
    protected $browser = array();

    /**
     * title
     * 
     * (default value: 'Nerb Application Framework')
     * 
     * @var string
     * @access protected
     */
    protected $title = 'Nerb Application Framework'; 

    /**
     * charset
     * 
     * (default value: 'UTF-8')
     * 
     * @var string
     * @access protected
     */
    protected $charset = 'UTF-8'; 

    /**
     * language
     * 
     * (default value: 'EN')
     * 
     * @var string
     * @access protected
     */
    protected $language = 'EN'; 

    /**
     * viewport
     * 
     * (default value: 'width=device-width, initial-scale=1.0')
     * 
     * @var string
     * @access protected
     */
    protected $viewport = 'width=device-width, initial-scale=1.0'; 

    /**
     * meta
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $meta = array();

    /**
     * http_equiv
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $http_equiv = array();

    /**
     * rel
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $rel = array();
    
    /**
     * icon
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $icon = array();

    /**
     * script
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $script = array();

    /**
     * style
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $style = array();	

	/**
	 * header
	 * 
	 * (default value: MODULES.'/Page/includes/header.phtml')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $header = MODULES.'/Page/includes/header.phtml';

	/**
	 * footer
	 * 
	 * (default value: MODULES.'/Page/includes/footer.phtml')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $footer = MODULES.'/Page/includes/footer.phtml';

    /**
     * content_header
     * 
     * @var string
     * @access protected
     */
    protected $content_header; 

    /**
     * content_footer
     * 
     * @var string
     * @access protected
     */
    protected $content_footer;

	/**
	 * content
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $content = array();
	
    /**
     * use_error_pages
     * 
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $use_error_pages = true;

	/**
	 * error (error code)
	 * 
	 * (default value: null)
	 * 
	 * @var int
	 * @access protected
	 */
	protected $error = null;
	
	/**
	 * error_page
	 * 
	 * (default value: array(
	 * 		'100' => MODULES.'/Page/includes/100.phtml', // unsupported browser   
	 * 		'400' => MODULES.'/Page/includes/400.phtml', // bad request
	 * 		'401' => MODULES.'/Page/includes/401.phtml', // unauthorized   
	 * 		'403' => MODULES.'/Page/includes/403.phtml', // forbiden   
	 * 		'404' => MODULES.'/Page/includes/404.phtml', // page not found	   
	 * 		'500' => MODULES.'/Page/includes/500.phtml', // service error and unspecified errors
	 * 		'503' => MODULES.'/Page/includes/503.phtml', // service unavailable
	 * 	))
	 * 
	 * @var string
	 * @access protected
	 */
	protected $error_page = array(
		'100' => MODULES.'/Page/includes/100.phtml', // unsupported browser   
		'400' => MODULES.'/Page/includes/400.phtml', // bad request
		'401' => MODULES.'/Page/includes/401.phtml', // unauthorized   
		'403' => MODULES.'/Page/includes/403.phtml', // forbiden   
		'404' => MODULES.'/Page/includes/404.phtml', // page not found	   
		'500' => MODULES.'/Page/includes/500.phtml', // service error and unspecified errors
		'503' => MODULES.'/Page/includes/503.phtml', // service unavailable
	);
	
	/**
	 * page_caching
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access protected
	 */
	protected $page_caching = false;
	
	/**
	 * cache_dir
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $cache_dir = '';
	
	/**
	 * cache_ttl
	 * 
	 * (default value: 86400)
	 * 
	 * @var int
	 * @access protected
	 */
	protected $cache_ttl = 86400;
	
	/**
	 * autofetch_cache
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access protected
	 */
	protected $autofetch_cache = true;
	
	/**
	 * nocache
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $nocache = array();
	
	/**
	 * cache_filename (name of file used for caching)
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $cache_filename = '';
	
	/**
	 * cache (holds the cached page)
	 * 
	 * @var PageCache $cache
	 * @access protected
	 */
	protected $cache = null;




    /**
     * __construct function.
     * 
     * Constructor initiates Page object
     *
     * @access public
     * @throws Error
     * @return self
     */
    public function __construct()
    {
		// process the ini file
		// the configuration file must be present in the configuration directory to work
		$this->process_ini(  CONFIG.'/Page.ini'  );
		
		// check browser if necessary
		// Warning, this method is a bit slow and 
		// the server must be configured for it to work properly
        if( $this->browser_check ) $this->browser_check();
        
		// this sends header commands to prevent the browser from caching contents and 
	    // must revalidate.  this is best for pages that one must be logged in to view
	    if( $this->cache_control ){
		    session_cache_limiter( $this->cache_control );
		    
		    // -- alternate method --
		    //header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			//header("Pragma: no-cache"); // HTTP 1.0.
			//header("Expires: 0"); // Proxies.
		}

	    // if page caching is used and page is cached, 
	    // then return cached content, otherwise procede with rendering
	    if( PAGE_CACHING ){

			// set caching flag and add meta tags for cached content
			$this->cache();
			
			// ------> if the page is cached, this is where the page processing ends <-------//
			if( AUTOFETCH_CACHE ) $this->get_cached_page();
		
	    } // end if page caching
	    
		// auto add content headers and content footer to page
		if( $this->content_header ) $this->header( $this->content_header );
		if( $this->content_footer ) $this->footer( $this->content_footer );
	   
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




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
     * @return void
     */
    protected function browser_check()
    {
	    // if the check has already been conducted and passed, return
	    if( $_SESSION['browser_check'] == 'pass' ){
		    return;
		} 
		
	    // if a check has previously failed, and browser_fail is set set to kill, return error 100 bad browser
		if( $_SESSION['browser_check'] == 'fail' && $this->browser_fail == 'error' ){
			// set error to serve bad browser page
			$this->error = 100;
			return;
	    } 
	    
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
		
		if( $this->browser[ $browser->browser ] > $browser->version ){
			// set session var to indicate browser failure
			$_SESSION['browser_check'] = 'fail';
			
			// set error to serve bad browser page
			if( $this->browser_fail ){	
				$this->error = 100;
			}
			return;
		} 
			
		$_SESSION['browser_check'] = 'pass';
		return;
			
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //      !Cache control

    #################################################################



    /**
     * cache function.
     *
     * This method turns on caching for the page.
     *
     * DO NOT USE ON AUTHENTICATED PAGES!!
     * 
     * Pages that are cached can not be user authenticated
     *
     * @access protected
     * @return self
     */
    public function cache() : self
    {
        $this->page_caching = true;
        
		// the filename is a md5 hash of the full url of the page.
	    // this makes it easier to store the full path of the url
	    // and obfuscates the page in the cache directory and if
	    // multiple sites are using the same temp dir, prevents 
	    // cross site scripting
	    $this->cache_filename = md5( $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ).'.cache';
	    
	    // create cache object
	    $this->cache = new PageCache( $this->cache_filename );

	    // add meta cache data so that page can be identified as cached content
	    $this->meta['cached'] = date("F d, Y - h:i:s a");
	    $this->meta['cached_url'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * nocache function.
     *
     * This method turns off caching for the page.
     * This method MUST be used in controllers that require a login or validated data
     * 
     * CACHED PAGES CAN NOT BE USER AUTHENTICATED!
     *
     * This means that any user can see a cached page INCLUDING accounts, etc.
     * 
     * @access protected
     * @return self
     */
    public function nocache() : self
    {
        //set flag
        $this->page_caching = false;
        
        // remove cached content if it exists
        $this->cache->uncache();
	    
	    // add meta cache data so that page can be identified as cached content
	    unset( $this->meta['cached'] );
	    unset( $this->meta['cached_url'] );
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * get_cached_page function.
     * 
     * @access public
     * @return void
     */
    public function get_cached_page()
    {
	    // cycle through the ignored cache directories and return if they exist
	    // this prevents nocached directories and pages from being cached
	    foreach( NOCACHE as $value ){
		    // format expression
		    $value = '/^('.str_replace('/', '\/', $value).')/i';
		    if( preg_match( $value , $_SERVER['REQUEST_URI'] ) ){
			    return;
			} 
	    } // end foreach
	    
	    // check to see if the page is cached
	    // if it is, then fetch the cache and exit
	    if( !empty($this->cache) && $this->cache->isCached() ) {
		    $this->cache->fetchCache();
		    exit;
		}
	    
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
		$params = new ParamsIni( $ini_file, true );
		
		// dump the params into array
        $dump = $params->dump();
        
        // cycle through and add them to class properties
        foreach( $dump['params'] as $key => $value ){
	        if( !property_exists( $this, $key ) ){
				throw new Error( "The property <code>[$key]</code> does not exist.  Check your page.ini for proper spelling and syntax." ); 
	        }		        
			$this->$key = $value;
        }
	}  // end function -----------------------------------------------------------------------------------------------------------------------------------------------

    
    
    
    #################################################################

    //      !Page Building

    #################################################################



    /**
    *   actually produces the page from the parameters
    *
    *   @access     public
    *   @return     void
    */
    public function render()
    {
	    // clear any buffers
	    ob_end_clean();
	    ob_start();
	    
	    // include page header
	    require $this->header;
	    
	    // if the page has a content header, include it
	    if( !empty($this->content_header) ) require $this->content_header;
	    
	    // add page content
	    $this->includePageContent();
	    
	    // add content footer
	    if( !empty($this->content_footer) ) require $this->content_footer;
	    
	    // add html footer
	    require $this->footer;
        
		// if page caching is used, capture content an cache it
	    if( $this->page_caching ){
			$this->cache->write( ob_get_contents() );
	    }
	    
        // output contents of buffer and clear
        ob_flush();
        ob_end_clean();
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !Content

    #################################################################



    /**
     * includePageContent function.
     * 
     * @access protected
     * @return void
     */
    protected function includePageContent() 
    {
		// if there is an error or there is no content, then include the error page 
	    if( $this->error || ( empty( $this->content ) && $this->use_error_pages )){
	    	$this->error_page( $this->error );
	    	return;
	    } 
	    
		foreach( $this->content as $value ){
	    	if( $this->page_preprocess ){
				echo $value;
		    } else {
			    require $value;
		    } 
		}
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * content function.
     * 
	 * Adds content to the page.
	 * if preprocess flag is true, then the content is processed immediately
	 * otherwise the content will be processed during render
	 * 
     * @access public
     * @param string $content
     * @return self
     */
    public function content( string $content ) : self
    {
	    // error check to make sure content exists and is not a directory
		if( is_dir( $content ) || !file_exists( $content )){
		    $this->error = 404;
			return $this;
		}
	    
	    // add content to the array
		$this->content[] = $this->page_preprocess ? $this->preprocess( $content ) : $content;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * preprocess function.
	 * 
	 * @access protected
	 * @param string $content
	 * @return string
	 */
	protected function preprocess( string $content ) : string
	{
		    // catch the output buffer
		    ob_start();		    
		    
			@require_once($content);
			
			// add contents of output buffer to content array 
		    $processed = ob_get_contents();
		    
		    // clear buffer
		    ob_end_clean();
		    
		    // return results
		    return $processed;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
    /**
     * header function.
     *
     * This adds the site header to the page.  This should not be confused with
     * the html header which includes styles and scripts.  this is for sites with structure like:
     * 	HTML header
     *    header - (content header)
     *	  content
     *	  footer - (content footer)
     *	HTML footer
     * 
     * @access public
     * @param string $filename
     * @throws Error
     * @return self
     */
    public function header( string $filename ) : self
    {
		if( !file_exists($filename) ){
			throw new Error( 'Could not locate resource <code>'.$filename.'</code>' );
		}
		
		$this->content_header = $filename;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * footer function. (see content_header)
     * 
     * @access public
     * @param string $filename
     * @throws Error
     * @return self
     * @see content_header
     */
    public function footer( string $filename ) : self
    {
		if( !file_exists($filename) ){
			throw new Error( 'Could not locate resource <code>'.$filename.'</code>' );
		}
		
		$this->content_footer = $filename;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
	/**
	 * error_page function.
	 *
	 * if an error is called, then an error page is included
	 * 
	 * @access protected
	 * @param int $error
	 * @return void
	 */
	protected function error_page( int $error )
	{
		$error_page = empty( $this->error_page[$error] ) ? 500 : $error;
    	require $this->error_page[ $error_page ];
    	return;
    	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * error function.
     * 
     * @access public
     * @param int $error (default: 404)
     * @return self
     */
    public function error( int $error = 404 ) : self
    {
 		$this->error = $error;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * title function.
	 * 
	 * @access public
	 * @param string $title
	 * @return self
	 */
	public function title( string $title ) : self
	{
		$this->title = $title;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------





} // end class
