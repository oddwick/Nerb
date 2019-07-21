<?php
// Nerb Application Framework


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
 * @class           NerbPage
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */


class NerbPage
{
    /**
     * params
     * 
     * (default value: array(
     * 	    'browser_check' => false,
     * 	    'use_error_pages' => false,
     * 	    'cache_control' => "public", 
     * 		'page_caching' => false,
     * 		'cache_dir' => '/temp',
     * 		'cache_ttl' => 3600,
     * 	    'asynch_scripts' => false,
     * 	    'scripts_in_header' => true,
     * 	    'preprocess' => false,   
     * 	    'header' => MODULES.'/NerbPage/includes/header.phtml',    
     * 	    'footer' => MODULES.'/NerbPage/includes/footer.phtml',    
     * 	    'error_100' => MODULES.'/NerbPage/includes/100.phtml',    
     * 	    'error_403' => MODULES.'/NerbPage/includes/403.phtml',    
     * 	    'error_404' => MODULES.'/NerbPage/includes/404.phtml',    
     * 	    'error_500' => MODULES.'/NerbPage/includes/500.phtml',
     * 	    'title' => '',
     * 	    'charset' => 'UTF-8',
     * 		'language' => 'EN',
     * 		'icon' => '/images/favorite.ico',
     *         'crossorigin' => 'anonymous',
     *         'viewport' => 'width=device-width, initial-scale=1.0',
     * 	    'meta' => array(
     *             'description' => '',
     *             'keywords' => array(),
     *             'author' => '',
     *             'copyright' => '',
     *             'robots' => 'index, follow',
     *             'application-name' => 'Nerb Framework',
     *             'generator' => '',
     *             'publisher' => '',
     *             'creator' => '',
     *             'generator' => 'Nerb Framework Engine',
     *         ),
     * 	    'http-equiv' => array(
     *             'content-type' => 'width=device-width, initial-scale=1.0',
     * 	        'refresh' => '',
     * 	    ),
     * 	    'rel' => array(
     * 	        'author' => '',
     * 	        'dns-prefetch' => '',
     * 	        'help' => '',
     * 	        'icon' => '',
     * 	        'license' => '',
     * 	        'pingback' => '',
     * 	        'next' => '',
     * 	        'prev' => '',
     * 	        'preconnect' => '', 
     * 	        'prefetch' => '', 
     * 	        'preload' => '', 
     * 	        'search' => '', 
     * 	        'canonical' => '', 
     * 	        'shortcut' => '', 
     * 	        'contents' => '', 
     * 	        'index' => '', 
     * 	    ),
     * 	    'script' => array(),
     * 	    'style' => array(),
     * 	    'base' => null,	        
     * 	    'alternate' => array(),
     *     ))
     * 
     * @var string
     * @access protected
     */
    protected $params = array(
	    'browser_check' => false,
	    'use_error_pages' => false,
	    'cache_control' => "public", 
		'page_caching' => false,
		'cache_dir' => '/temp',
		'cache_ttl' => 3600,
	    'asynch_scripts' => false,
	    'scripts_in_header' => true,
	    'preprocess' => false,   
	    'header' => MODULES.'/NerbPage/includes/header.phtml',    
	    'footer' => MODULES.'/NerbPage/includes/footer.phtml',    
	    'error_100' => MODULES.'/NerbPage/includes/100.phtml',    
	    'error_403' => MODULES.'/NerbPage/includes/403.phtml',    
	    'error_404' => MODULES.'/NerbPage/includes/404.phtml',    
	    'error_500' => MODULES.'/NerbPage/includes/500.phtml',
	    'title' => '',
	    'charset' => 'UTF-8',
		'language' => 'EN',
		'icon' => '/images/favorite.ico',
        'crossorigin' => 'anonymous',
        'viewport' => 'width=device-width, initial-scale=1.0',
	    'meta' => array(
            'description' => '',
            'keywords' => array(),
            'author' => '',
            'copyright' => '',
            'robots' => 'index, follow',
            'application-name' => 'Nerb Framework',
            'generator' => '',
            'publisher' => '',
            'creator' => '',
            'generator' => 'Nerb Framework Engine',
        ),
	    'http-equiv' => array(
            'content-type' => 'width=device-width, initial-scale=1.0',
	        'refresh' => '',
	    ),
	    'rel' => array(
	        'author' => '',
	        'dns-prefetch' => '',
	        'help' => '',
	        'icon' => '',
	        'license' => '',
	        'pingback' => '',
	        'next' => '',
	        'prev' => '',
	        'preconnect' => '', 
	        'prefetch' => '', 
	        'preload' => '', 
	        'search' => '', 
	        'canonical' => '', 
	        'shortcut' => '', 
	        'contents' => '', 
	        'index' => '', 
	    ),
	    'script' => array(),
	    'style' => array(),
	    'base' => null,	        
	    'alternate' => array(),
    );

	/**
	 * contentHeader
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $contentHeader = '';
	
	/**
	 * contentFooter
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $contentFooter = '';
	
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
	 * contentLink
	 * 
	 * (default value: false)
	 * 
	 * @var bool
	 * @access protected
	 */
	protected $contentLink = false;
	
	/**
	 * data
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $data = array();

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
	 * cache (holds the cached page)
	 * 
	 * (default value: null)
	 * 
	 * @var string
	 * @access protected
	 */
	protected $cache = null;

	
	/**
	 * filename (name of file used for caching)
	 * 
	 * (default value: null)
	 * 
	 * @var string
	 * @access protected
	 */
	protected $filename = null;
	

    /**
     * __construct function.
     * 
     * Constructor initiates Page object
     *
     * @access public
     * @param string $ini
     * @param string $path
     * @param array $params (default: array())
     * @throws NerbError
     * @return void
     */
    public function __construct( string $ini = '', array $params = array() )
    {
        
        // error checking to make sure file exists
        // if the full path is given...
        if ( file_exists( $ini ) ) {
        	$ini_file = $ini;
        	
        // if a relative path is given	
        } else if( file_exists( APP_PATH . $ini ) ){
        	$ini_file = APP_PATH . $ini;
        	
        // you blew it
	    } else {
            throw new NerbError( 'Could not locate given configuration file <code>'.$ini.'</code>' );
        }

        // load and parse ini file and distribute variables
        // the user changeable variables will end up in $params and the defaults will be kept in $defaults
        try {
            // if the config.ini file is read, it loads the values into the params
            $data = parse_ini_file( $ini_file, false );
            $data = $this->_parse( $data );
            $this->params = array_merge( $this->params, $data );
            
        } catch ( Exception $e ) {
            throw new NerbError( 
                'Could not parse page ini file <code>'.$ini.'</code>.<br /> 
					Make that it is formatted properly and conforms to required standards. '
             );
        }// end try

		

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

		// check browser if necessary
		// Warning, this method is a bit slow and 
		// the server must be configured for it to work properly
        if( $this->params['browser_check'] ){
	        $this->browserCheck();
        }
        
		// this sends header commands to prevent the browser from caching contents and 
	    // must revalidate.  this is best for pages that one must be logged in to view
	    if( $this->params['cache_control'] ){
		    session_cache_limiter( $this->params['cache_control'] );
		    
		    // -- alternate method --
		    //header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			//header("Pragma: no-cache"); // HTTP 1.0.
			//header("Expires: 0"); // Proxies.
		}

	    // if page caching is used and page is cached, 
	    // then return cached content, otherwise procede with rendering
	    if( $this->page_caching ){
		    
			// the filename is a md5 hash of the full url of the page.
		    // this makes it easier to store the full path of the url
		    // and obfuscates the page in the cache directory and if
		    // multiple sites are using the same temp dir, prevents 
		    // cross site scripting
		    $this->filename = md5( $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ).'.cache';
		    
		    // check to see if the page is cached
		    // if it is, then fetch the cache and exit
		    if( $this->_isCached() ){
			    if( $this->_fetchCache() ){
			    exit;
			    }
		    }
		    
	    } // end if page caching
        
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * parse function.
     * 
     * @access protected
     * @param array $data
     * @return array
     */
    protected function _parse( array $data ) : array
    {
		// initialize array
		$array = array();
		
		// cycle through data and seperate . notation into key/value pairs
		foreach( $data as $path => $value ) {
		    $temp = &$array;
		    foreach( explode('.', $path) as $key ) {
		        $temp =& $temp[$key];
		    }
		    $temp = $value;
		}
		return $config = $array;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * browserCheck function.
     * 
     * This function checks to see if the browser passes the minimum reqirements of the site and is set using the following
     * variables set in the page.ini file:
     *	(bool) browser_check
     *	(bool) browser_kill_on_fail
     *
     * minimum versions are set on a per browser basis in page.ini and a cookie/session variable is set once the initial 
     * browser check has been conducted.  	
     * 
     * @access protected
     * @return void
     */
    protected function browserCheck()
    {
	    // if the check has already been conducted and passed, return
	    // if a check has failed, and browser_fail is set set to kill, do not recheck
	    // otherwise 
	    if( $_SESSION['browser_check'] == 'pass' ){
		    return;
		    
		} elseif( $_SESSION['browser_check'] == 'fail' && $this->params['browser_fail'] == 'error' ){
			// set error to serve bad browser page
			$this->error = 100;
			return;
			
	    } else {
		    // get browser information
		    // this requires browsercap.ini to be set up on the server and can be
		    // checked through the phpinfo() function
			$browser = get_browser();
			if( $this->params[ 'browser' ][ $browser->browser ] > $browser->version ){
				
				// set session var to indicate browser failure
				$_SESSION['browser_check'] = 'fail';
				// set error to serve bad browser page
				if( $this->params['browser_kill_on_fail'] )	$this->error = 100;
				
			} else {
				$_SESSION['browser_check'] = 'pass';
			}
			
			// set flags to indicate what type of device the user is on
			// to determine if you want to serve mobile specific versions of the site
			$_SESSION['browser'] = $browser->browser;
			$_SESSION['browser_version'] = $browser->version;
			$_SESSION['browser_device'] = $browser->device;
			$_SESSION['browser_platform'] = $browser->platform;
			return;
			
	    } // end if
	    
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
        $this->params[$key] = $value;

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
        return $this->params[ $key ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * dump function.
     * 
     *   Get all parameters at once
     *
     * @access public
     * @param string $section (default: null)
     * @return array (the entire parameter array is returned)
     */
    public function dump( string $section = null )
    {
        // if section is given
        if ( $section ) {
            return $this->params[ $section ];
        } // return all values
        else {
            return $this->params;
        } // end if
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




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
	    
	    // if page caching is used, add meta 'cached' tag so that page can be identified as cached content
	    if( $this->page_caching ){
		    $meta = array( 
		    		'cached' => date("F d, Y - h:i:s a"),
		    		'cached_url' => $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
				);
		    $this->meta( $meta , true );
		}
		
	    // clear any buffers
	    ob_end_clean();
	    ob_start();
	    
	    // include page header
	    require $this->params['header'];
	    
	    // if the page has a content header, include it
	    if( $this->contentHeader ) require $this->contentHeader;
	    
	    // if there is an error or there is no content, then include the error page 
	    if( $this->error || ( empty( $this->content ) && $this->params['use_error_pages'] )){
	    	
	    	switch( $this->error ){
		    	
		    	// unsupported browser	
		    	case 100:
			    	require $this->params['error_100'];
		    		break;
		    		
		    	// bad request
		    	case 400:
		    	// unauthorized
		    	case 401:
		    	// forbiden
		    	case 403:
			    	require $this->params['error_403'];
		    		break;
		    		
		    	// page not found	
		    	case 404:
			    	require $this->params['error_404'];
		    		break;
		    		
		    	// service error and unspecified errors
		    	case 500:
		    	// service unavailable
		    	case 503:
		    	default:
			    	require $this->params['error_500'];
		    	
	    	} // end swich
	    	
	    } else {
		    
		    if( $this->params['preprocess'] ){
			    
			    if( is_array( $this->content ) && count( $this->content ) > 0 ){ 
				    foreach( $this->content as $value ){
						echo $value;
				    } // end foreach
			    } else {
				    require $this->params['error_404'];
			    } // end if
			    
		    } else {
			    foreach( $this->content as $key => $value ){
			    	if( !is_dir( $value ) && file_exists( $value )){
					    require $value;
			    	} else {
				    	require $this->params['error_404'];
			    	}
			    } // end foreach
		    }
	    }// end if
	    
	    
	    if( $this->contentFooter ) require $this->contentFooter;
	    require $this->params['footer'];
        //Nerb::inspect( $this->params, true, '' );
        
        
		// if page caching is used, capture content an cache it
	    if( $this->page_caching ){
			$this->cache = ob_get_contents();
			$this->_cache();
	    }
	    
        // output contents of buffer and clear
        ob_flush();
        ob_end_clean();
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !Caching

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
     * @return NerbPage
     */
    public function cache() : NerbPage
    {
        $page->page_caching = true;
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
     * @return NerbPage
     */
    public function nocache() : NerbPage
    {
        $this->page_caching = false;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * display_cache function.
     *
     * returns the contents of the page cache
     * 
     * @access public
     * @return string
     */
    public function displayCache() : string
    {
	   return $this->cache;
	   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * clear_cache function.
     *
     * This clears all cached pages in cache directory
     * 
     * @access public
     * @return bool
     */
    public function clearCache() : bool
    {
	    // find all files with *.cache 
	    $files = glob( $this->cache_dir.'/*.cache', GLOB_BRACE );
	    
	    // loop through and delete files
	    foreach( $files as $file ){
	    	if( !$status = @unlink( $file ) ) $error = true;
	    } // end foreach
	    
	    return $error ? false : true;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * uncache function.
     * 
     * clears the current page from cache
     *	
     * @access public
     * @return bool
     */
    public function uncache() : bool
    {
	    if( file_exists( $this->cache_dir.'/'.$this->filename ) ){
		    @unlink( $this->cache_dir.'/'.$this->filename );
		    return true;
	    }
	    
	    return false;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * cache function.
     *
     * writes out the contents of the page cache to a file
     * 
     * @access protected
     * @throws NerbError
     * @return bool
     */
    protected function _cache(): bool
    {
	    // ignore user abort to make sure that the cache is fully written to
	    // prevent corrupted cache or code injection
	    ignore_user_abort( true );
	    
	    // error checking
	    if( !is_dir( $this->cache_dir ) ){
		    throw new NerbError( 'Cache directory <code>'.$this->cache_dir.'<code> does not exist' );
	    } elseif( !$this->filename ){
		    throw new NerbError( 'Invalid file name given' );
	    }
	    
	    // write contents to directory	    
	    return $status = @file_put_contents( $this->cache_dir.'/'.$this->filename, $this->cache );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _is_cached function.
     *
     * checks to see if the current page is cached and returns true if it is
     * if the page is expired, it will try and remove the page from the cache directory for housekeeping
     * 
     * @access protected
     * @return bool
     */
    protected function _isCached() : bool
    {
	    // get file name
	    $file = $this->cache_dir.'/'.$this->filename;
	    
	    // file must exist and mod time must be greater than current time - ttl for cache to be active
	    if( file_exists( $file ) && filemtime( $file ) > ( time() - $this->cache_ttl ) ) {
	    	return true;			
	    } // end if
	    
	    // housekeeping
	    // clean up expired cache files
	    $this->uncache();
	    
	    return false;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _fetch_cache function.
     *
     * reads the contents of the cache file int output buffer and returns false on fail
     * 
     * @access protected
     * @return bool
     */
    protected function _fetchCache() : bool
    {
	    $file = $this->cache_dir.'/'.$this->filename;
	    
	    // check to see if the file exists and read it to the output buffer
	    if( file_exists( $file ) ){
		    
		    // send header
		    header('Content-Type: text/html');
			
			// clear output buffer and start new buffer
			ob_end_clean();
		    ob_start();
		    
		    // get file contents. readfile is more secure than include, prevents 
		    // any embeded php from getting processed
			readfile( $file );
			
			//output buffer and clear
			ob_flush();
			ob_end_clean();
			return true;		    
	    }
	    
	    // return false if the page was not read 
	    return false;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	
    #################################################################

    //      !Content

    #################################################################




    /**
     * content function.
     * 
	 * Adds content to the page.
	 * if preprocess flag is true, then the content is processed immediately
	 * otherwise the content will be processed during render
	 * 
     * @access public
     * @param mixed $content
     * @return NerbPage
     */
    public function content( $content ) : NerbPage
    {
	    
	    if( $this->preprocess ){
		    
		    // catch the output buffer
		    ob_start();		    
		    
		    // include the content or produce 404 error
	    	if( !is_dir( $content ) && file_exists( $content )){
			    require $content;
	    	} else {
		    	require $this->params['error_404'];
	    	}
		    
			// add contents of output buffer to content array 
		    $this->content[] = ob_get_contents();
		    
		    // clear buffer
		    ob_end_clean();
		    
	    } else {
	 		if( is_array( $content )){
		 		$this->content = $content;
	 		} else {
		 		$this->content[] = $content;
	 		}
	    }
 		
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * contentHeader function.
     * 
     * @access public
     * @param string $content
     * @return NerbPage
     */
    public function contentHeader( string $content ) : NerbPage
    {
 		$this->contentHeader = $content;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * contentFooter function.
     * 
     * @access public
     * @param string $content
     * @return NerbPage
     */
    public function contentFooter( string $content ) : NerbPage
    {
 		$this->contentFooter = $content;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
	/**
	 * data function.
	 * 
	 * @access public
	 * @param array $data
	 * @return NerbPage
	 */
	public function data( $data, string $value = '' ) : NerbPage
	{
		if( is_scalar( $data ) ){
			$this->data[$data] = $value;
		} elseif( is_array( $data ) ){
			$this->data = array_merge( $this->data, $data );
		}
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	



    #################################################################

    //      !Files

    #################################################################



    /**
     * header function.
     * 
     * @access public
     * @param string $file
     * @return NerbPage
     */
    public function header( string $file ) : NerbPage
    {
 		$this->params['header'] = $file;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     * footer function.
     * 
     * @access public
     * @param string $file
     * @return NerbPage
     */
    public function footer( string $file ) : NerbPage
    {
 		$this->params['footer'] = $file;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * error function (default is 500 page error).
     * 
     * @access public
     * @return NerbPage
     */
    public function error()
    {
 		$this->error = 500;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    /**
     * pageNotFound function (404 page error).
     * 
     * @access public
     * @return NerbPage
     */
    public function notFound() : NerbPage
    {
 		$this->error = 404;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    /**
     * unauth function (403 error).
     * 
     * @access public
     * @return NerbPage
     */
    public function unauth() : NerbPage
    {
 		$this->error = 403;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    #################################################################

    //      !Attributes

    #################################################################




	/**
	 * title function.
	 * 
	 * @access public
	 * @param string $title
	 * @return NerbPage
	 */
	public function title( string $title ) : NerbPage
	{
		$this->params['title'] = $title;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * charset function.
	 * 
	 * @access public
	 * @param string $charset
	 * @return NerbPage
	 */
	public function charset( string $charset ) : NerbPage
	{
		$this->params['charset'] = $charset;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * lang function.
	 * 
	 * @access public
	 * @param string $lang
	 * @return NerbPage
	 */
	public function lang( string $lang ) : NerbPage
	{
		$this->params['lang'] = $lang;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * icon function.
     * 
     * @access public
     * @param string $icon
     * @return NerbPage
     */
    public function icon( string $icon ) : NerbPage
    {
	    $this->params['icon'] = $icon;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * equiv function.
     * 
     * @access public
     * @param string $title
     * @param string $value
     * @return NerbPage
     */
    public function equiv( string $title, string $value ) : NerbPage
    {
	    $this->params['http-equiv'][$title] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * meta function.
     * 
     * @access public
     * @param array $meta
     * @param bool $merge (default: false)
     * @return NerbPage
     */
    public function meta( array $meta, bool $merge = false ) : NerbPage
    {
	    if( $merge ){
		    $this->params['meta'] = array_merge( $this->params['meta'], $meta );
	    } else {
		    $this->params['meta'] = $meta;
	    }
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * viewport function.
     * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function viewport( string $value ) : NerbPage
    {
	    $this->params['viewport'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * description function.
     * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function description( string $value ) : NerbPage
    {
	    $this->params['meta']['description'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * keywords function.
	 * 
	 * Add or merge keyword array to meta keywords
	 * 
	 * @access public
	 * @param array $values
	 * @param bool $merge (default: true)
	 * @return NerbPage
	 */
	public function keywords( array $values, bool $merge = true ) : NerbPage
    {
	    if( $merge ){
		    $this->params['meta']['keywords'] = array_merge( $this->params['meta']['keywords'], $values );
	    } else {
		    $this->params['meta']['keywords'] = $values;
	    }
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * keyword function.
	 *
	 * Add single keyword to meta keyword
	 * 
	 * @access public
	 * @param mixed $value
	 * @return NerbPage
	 */
	public function keyword( $value ) : NerbPage
    {
		$this->params['meta']['keywords'][] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * author function.
     * 
	 * Add author value to meta author
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
   public function author( string $value ) : NerbPage
    {
	    $this->params['meta']['author'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * copyright function.
     * 
	 * Add copyright to meta copyright
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function copyright( string $value ) : NerbPage
    {
	    $this->params['meta']['copyright'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * robots function.
     * 
	 * Add robots text to meta robots
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function robots( string $value ) : NerbPage
    {
	    $this->params['meta']['robots'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * appname function.
     * 
	 * Add meta application-name value 
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function appname( string $value ) : NerbPage
    {
	    $this->params['meta']['application-name'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * generator function.
     * 
	 * Add meta generator value
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function generator( string $value ) : NerbPage
    {
	    $this->params['meta']['generator'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * publisher function.
     * 
	 * Add meta publisher value
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function publisher( string $value ) : NerbPage
    {
	    $this->params['meta']['publisher'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * creator function.
     * 
	 * Add meta creator value
	 * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function creator( string $value ) : NerbPage
    {
	    $this->params['meta']['creator'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * alt function.
     * 
	 * Add adds alt value
	 * 
     * @access public
     * @param string $title
     * @param string $value
     * @return NerbPage
     */
    public function alt( string $title, string $value ) : NerbPage
    {
	    $this->params['alt'][$title] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * style function.
     * 
	 * Add stylesheet url to header
	 * 
     * @access public
     * @param string $style (url of stylesheet)
     * @return NerbPage
     */
    public function style( string $style ) : NerbPage
    {
	    $this->params['style'][] = $style;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * styles function.
     * 
	 * Same as style() but allows array of styles to be added
	 * 
     * @access public
     * @param array $style
     * @return NerbPage
     */
    public function styles( array $style ) : NerbPage
    {
	    $this->params['style'] = $style;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * script function.
	 * 
	 * Add script url to header
	 * 
	 * @access public
	 * @param string $script
	 * @return NerbPage
	 */
	public function script( string $script ) : NerbPage
	{
		$this->params['script'][] = $script;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * scripts function.
	 * 
	 * Same as stript(), but allows array of scripts to be added 
	 * 
	 * @access public
	 * @param array $script
	 * @return NerbPage
	 */
	public function scripts( array $script ) : NerbPage
	{
		$this->params['script'] = $script;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * rel function.
	 * 
	 * Alias of link()
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return NerbPage
	 */
	public function rel( string $title, string $link ) : NerbPage
	{
		$this->link( $title, $link );
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * link function.
	 * 
	 * Add link rel statement to header
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return NerbPage
	 */
	public function link( string $title, string $link ) : NerbPage
	{
		$this->params['rel'][$title] = $link;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * base function.
	 * 
	 * Add base statment to header
	 * 
	 * @access public
	 * @param string $url
	 * @return NerbPage
	 */
	public function base( string $url ) : NerbPage
	{
		$this->params['base'] = $url;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} // end class