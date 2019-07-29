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
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @property string MODULES
 * @requires NerbError
 * @requires NerbCache
 * @requires page.ini
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
     * @var array
     * @property string MODULES
     * @access protected
     */
    protected $params = array(
	    'browser_check' => false,
	    'use_error_pages' => false,
	    'cache_control' => "public", 
		'page_caching' => false,
		'cache_dir' => '/temp',
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
	 * @var NerbCache $cache
	 * @access protected
	 */
	protected $cache;

	
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
     * @param string $ini_file
     * @throws NerbError
     * @return void
     */
    public function __construct( string $ini_file )
    {
		// process the ini file and merge it to params array
        $data = $this->parse( $ini_file );
        $this->params = array_merge( $this->params, $data );
        
		// check browser if necessary
		// Warning, this method is a bit slow and 
		// the server must be configured for it to work properly
        if( $this->params['browser_check'] ) $this->browserCheck();
        
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
	    if( PAGE_CACHING ){
		    $this->page_caching = true;
			// the filename is a md5 hash of the full url of the page.
		    // this makes it easier to store the full path of the url
		    // and obfuscates the page in the cache directory and if
		    // multiple sites are using the same temp dir, prevents 
		    // cross site scripting
		    $this->filename = md5( $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ).'.cache';
		    
		    // create the cache
		    $this->cache = new NerbPageCache( $this->filename );
		    
		    // check to see if the page is cached
		    // if it is, then fetch the cache and exit
		    if( $this->cache->isCached() && $this->cache->fetchCache() ) {
			    if( DEBUG ) echo 'Cached content';
			    exit;
			}
	    } // end if page caching
	    
		// auto add content headers and content footer to page
		if( $this->params['content_header'] ) $this->contentHeader( $this->params['content_header'] );
		if( $this->params['content_footer'] ) $this->contentFooter( $this->params['content_footer'] );
	   
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * parse function.
     *
     * Processes ini file and turns it into an array and takes .(dot) notation and makes
     * subkeys out of it
     * 
     * @access protected
     * @param string $ini_file
     * @return array
     */
    protected function parse( string $ini_file ) : array
    {
		// error checking to make sure file exists
		// if a relative path is given	
		if( file_exists( APP_PATH . $ini_file ) ){
			$ini_file = APP_PATH . $ini_file;
		}
        
        // if the full path is given...
        if ( !file_exists( $ini_file ) ) {
            throw new NerbError( 'Could not locate given configuration file <code>'.$ini_file.'</code>' );
        }

        // load and parse ini file and distribute variables
        // the user changeable variables will end up in $params and the defaults will be kept in $defaults
        try {
            // if the config.ini file is read, it loads the values into the params
            $data = parse_ini_file( $ini_file, false );
        } catch ( Exception $e ) {
            throw new NerbError( 
                'Could not parse page ini file <code>'.$ini_file.'</code>.<br /> 
					Make that it is formatted properly and conforms to required standards. '
             );
        }// end try

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
		return $array;
		
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
     *  @param string $key
     *  @param string $value
     *  @return string old value
     *  @property array $params
     *  @throws NerbError
     */
    public function __set(string $key, string $value) : string
    {
        // error checking to ensure key exists
        if (!array_key_exists($key, $this->params)){
	        throw new NerbError( 'The key <code>['.$key.']</code> is not a valid parameter' );
        } // end if
        
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
     *  @property array $params
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
     * Get all parameters at once
     *
     * @access public
     * @param string $section (default: null)
     * @return array (the entire parameter array is returned)
     */
    public function dump( string $section = null ) : array
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
     * @return NerbPage
     */
    public function cache() : NerbPage
    {
        $this->params['page_caching'] = true;
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
        $this->params['page_caching'] = false;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * metaCache function.
     * 
     * if page caching is used, add meta 'cached' tag so that 
     * the page can be identified as cached content
     *
     * @access public
     * @return void
     */
    public function metaCache()
    {
	    $meta = array( 
	    		'cached' => date("F d, Y - h:i:s a"),
	    		'cached_url' => $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
			);
	    $this->meta( $meta , true );
	    return;
	    
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
	    // add meta tags for cached content
	    if( $this->page_caching ) $this->metaCache();
		
	    // clear any buffers
	    ob_end_clean();
	    ob_start();
	    
	    // include page header
	    require $this->params['header'];
	    
	    // if the page has a content header, include it
	    if( $this->contentHeader ) require $this->contentHeader;
	    
	    // if there is an error or there is no content, then include the error page 
	    if( $this->error || ( empty( $this->content ) && $this->params['use_error_pages'] )){
	    	$this->errorPage();
	    } else {   
		    		    
			foreach( $this->content as $value ){
		    	if( $this->params['preprocess'] ){
					echo $value;
			    } else {
				    require $value;
			    } // end if
			} // end foreach
		    		    
	    }// end if
	    
	    // add content footer
	    if( $this->contentFooter ) require $this->contentFooter;
	    
	    // add html footer
	    require $this->params['footer'];
        
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
     * content function.
     * 
	 * Adds content to the page.
	 * if preprocess flag is true, then the content is processed immediately
	 * otherwise the content will be processed during render
	 * 
     * @access public
     * @param string $content
     * @return NerbPage
     */
    public function content( string $content ) : NerbPage
    {
	    
	    if( $this->preprocess ){
		    
		    // catch the output buffer
		    ob_start();		    
		    
		    // include the content or produce 404 error
		    // this is so that the page has the opprotunity to die
		    // gracefully.  other files will throw errors
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
	    	// add to the content array, which will be processed
	    	// in the order that was added
	    	if( is_file( $content ) ){
			 	$this->content[] = $content;
	    	} else {
		    	$this->error = 404;
	    	}
	    }
 		
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * contentHeader function.
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
     * @throws NerbError
     * @return NerbPage
     */
    public function contentHeader( string $filename ) : NerbPage
    {
		if( file_exists($filename) ){
			$this->contentHeader = $filename;
			return $this;
		} else {
			throw new NerbError( 'Could not locate resource <code>'.$filename.'</code>' );
		}
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * contentFooter function. (see contentHeader)
     * 
     * @access public
     * @param string $filename
     * @throws NerbError
     * @return NerbPage
     * @see contentHeader
     */
    public function contentFooter( string $filename ) : NerbPage
    {
		if( file_exists($filename) ){
			$this->contentFooter = $filename;
			return $this;
		} else {
			throw new NerbError( 'Could not locate resource <code>'.$filename.'</code>' );
		}
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
	/**
	 * data function.
	 * 
	 * @access public
     * @throws NerbError
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


	
	
	/**
	 * errorPage function.
	 *
	 * if an error is called, then an error page is included
	 * 
	 * @access public
	 * @return void
	 */
	public function errorPage(){
	    	
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
    	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    #################################################################

    //      !Files

    #################################################################



    /**
     * header function.
     * 
     * @access public
     * @param string $filename
     * @throws NerbError
     * @return NerbPage
     */
    public function header( string $filename ) : NerbPage
    {
		if( file_exists($filename) ){
	 		$this->params['header'] = $filename;
			return $this;
		} else {
			throw new NerbError( 'Could not locate resource <code>'.$filename.'</code>' );
		}
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * footer function.
     * 
     * @access public
     * @param string $filename
     * @throws NerbError
     * @return NerbPage
     */
    public function footer( string $filename ) : NerbPage
    {
		if( file_exists($filename) ){
	 		$this->params['footer'] = $filename;
			return $this;
		} else {
			throw new NerbError( 'Could not locate resource <code>'.$filename.'</code>' );
		}
	
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
	  The following functions add HTML attributes to the page
	  they can all be set in the page.ini file and any subsequent calls
	  will be APPENDED to those set in  page.ini
	 */



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
