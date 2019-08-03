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
     * (default value: 'nocache')
     * 
     * @var string
     * @access protected
     */
    protected $cache_control = 'nocache'; 

    /**
     * browser_check
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
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $preprocess = true;

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
     * use_error_pages
     * 
     * (default value: true)
     * 
     * @var bool
     * @access protected
     */
    protected $use_error_pages = true;

    /**
     * content_header
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $content_header; 

    /**
     * content_footer
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $content_footer;

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
	 * contentHeader
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $contentHeader;
	
	/**
	 * contentFooter
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $contentFooter;
	
	/**
	 * header
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $header = MODULES.'/NerbPage/includes/header.phtml';

	/**
	 * footer
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $footer = MODULES.'/NerbPage/includes/footer.phtml';

	/**
	 * error_pages
	 * 
	 * (default value: array(
	 * 		'100' => MODULES.'/NerbPage/includes/100.phtml',    
	 * 		'403' => MODULES.'/NerbPage/includes/403.phtml',    
	 * 		'404' => MODULES.'/NerbPage/includes/404.phtml',    
	 * 		'500' => MODULES.'/NerbPage/includes/500.phtml',
	 * 	))
	 * 
	 * @var string
	 * @access protected
	 */
	protected $error_pages = array(
		'100' => MODULES.'/NerbPage/includes/100.phtml',    
		'403' => MODULES.'/NerbPage/includes/403.phtml',    
		'404' => MODULES.'/NerbPage/includes/404.phtml',    
		'500' => MODULES.'/NerbPage/includes/500.phtml',
	);

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
	protected $cache_data;

	
	/**
	 * cache_filename (name of file used for caching)
	 * 
	 * (default value: null)
	 * 
	 * @var string
	 * @access protected
	 */
	protected $cache_filename = null;
	



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
		// process the ini file
		$this->ini( $ini_file );
		
		// check browser if necessary
		// Warning, this method is a bit slow and 
		// the server must be configured for it to work properly
        if( $this->browser_check ) $this->browserCheck();
        
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
		
			// the filename is a md5 hash of the full url of the page.
		    // this makes it easier to store the full path of the url
		    // and obfuscates the page in the cache directory and if
		    // multiple sites are using the same temp dir, prevents 
		    // cross site scripting
		    $this->cache_filename = md5( $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ).'.cache';
		    
		    // create the cache
		    $this->cache_data = new NerbPageCache( $this->cache_filename );
		    
		    // check to see if the page is cached
		    // if it is, then fetch the cache and exit
		    if( $this->cache_data->isCached() && $this->cache_data->fetchCache() ) {
			    if( DEBUG ) echo 'Cached content';
			    exit;
			}
	    } // end if page caching
	    
		// auto add content headers and content footer to page
		if( $this->content_header ) $this->contentHeader( $this->content_header );
		if( $this->content_footer ) $this->contentFooter( $this->content_footer );
	   
        return $this;
        
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
		    
		} elseif( $_SESSION['browser_check'] == 'fail' && $this->browser_fail == 'error' ){
			// set error to serve bad browser page
			$this->error = 100;
			return;
			
	    } else {
		    // get browser information
		    // this requires browsercap.ini to be set up on the server and can be
		    // checked through the phpinfo() function
			$browser = get_browser();
			if( $this->browser[ $browser->browser ] > $browser->version ){
				
				// set session var to indicate browser failure
				$_SESSION['browser_check'] = 'fail';
				// set error to serve bad browser page
				if( $this->browser_kill_on_fail )	$this->error = 100;
				
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
    public function cache() : self
    {
        $this->page_caching = true;
	    
	    // add meta cache data so that page can be identified as cached content
	    $meta = array( 
	    		'cached' => date("F d, Y - h:i:s a"),
	    		'cached_url' => $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
			);
	    $this->meta( $meta );
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
    public function nocache() : self
    {
        $this->page_caching = false;
	    
	    // remove meta cache data
	    $meta = array( 
	    		'cached' => '',
	    		'cached_url' => '',
			);
	    $this->meta( $meta );
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 *	Function 
	 *
	 *	@access		protected
	 *	@param		string $ini_file
	 *	@return		self
	*/
	protected function ini( string $ini_file ) 
	{
		// process the ini file and merge it to params array
		$params = new NerbParams( $ini_file );
		
		// dump the params into array
        $dump = $params->dump();
        
        // cycle through and add them to class properties
        foreach( $dump as $key => $value ){
	        if( property_exists( $this, $key ) ){
		        $this->$key = $value;
	        } else {
		       throw new NerbError( "The property <code>[$key]</code> does not exist.  Check your page.ini for proper spelling and syntax." ); 
	        }
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
	    if( $this->contentHeader ) require $this->contentHeader;
	    
	    // if there is an error or there is no content, then include the error page 
	    if( $this->error || ( empty( $this->content ) && $this->use_error_pages )){
	    	$this->errorPage();
	    } else {   
			foreach( $this->content as $value ){
		    	if( $this->preprocess ){
					echo $value;
			    } else {
				    require $value;
			    } // end if
			} // end foreach
	    }// end if
	    
	    // add content footer
	    if( $this->contentFooter ) require $this->contentFooter;
	    
	    // add html footer
	    require $this->footer;
        
		// if page caching is used, capture content an cache it
	    if( $this->page_caching ){
			$this->cache_data->write( ob_get_contents() );
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
    public function content( string $content ) : self
    {
	    // error check to make sure content exists and is not a directory
		if( is_dir( $content ) || !file_exists( $content )){
		    $this->error = 404;
			return;
		}
	    
	    // add content to the array
		$this->content[] = $this->preprocess ? $this->preprocess( $content ) : $content;
 		
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
		    
		    // include the content or produce 404 error
		    // this is so that the page has the opprotunity to die
		    // gracefully.  other files will throw errors
	    	if( !is_dir( $content ) && file_exists( $content )){
			    require $content;
	    	} else {
		    	require $this->error_page['404'];
	    	}
		    
			// add contents of output buffer to content array 
		    $processed = ob_get_contents();
		    
		    // clear buffer
		    ob_end_clean();
		    
		    // return results
		    return $processed;
		
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
    public function contentHeader( string $filename ) : self
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
    public function contentFooter( string $filename ) : self
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
	public function data( $data, string $value = '' ) : self
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
		    	require $this->error_page['100'];
	    		break;
	    		
	    	// bad request
	    	case 400:
	    	// unauthorized
	    	case 401:
	    	// forbiden
	    	case 403:
		    	require $this->error_page['403'];
	    		break;
	    		
	    	// page not found	
	    	case 404:
		    	require $this->error_page['404'];
	    		break;
	    		
	    	// service error and unspecified errors
	    	case 500:
	    	// service unavailable
	    	case 503:
	    	default:
		    	require $this->error_page['500'];
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
    public function header( string $filename ) : self
    {
		if( file_exists($filename) ){
	 		$this->header = $filename;
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
    public function footer( string $filename ) : self
    {
		if( file_exists($filename) ){
	 		$this->footer = $filename;
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
    public function notFound() : self
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
    public function unauth() : self
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
	public function title( string $title ) : self
	{
		$this->title = $title;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * charset function.
	 * 
	 * @access public
	 * @param string $charset
	 * @return NerbPage
	 */
	public function charset( string $charset ) : self
	{
		$this->charset = $charset;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * lang function.
	 * 
	 * @access public
	 * @param string $lang
	 * @return NerbPage
	 */
	public function lang( string $lang ) : self
	{
		$this->lang = $lang;
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
    public function equiv( string $title, string $value ) : self
    {
	    
	    $this->http_equiv[$title] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * meta function.
     * 
     * @access public
     * @param array $meta
     * @param bool $merge (default: true)
     * @return NerbPage
     */
    public function meta( array $meta, bool $merge = true ) : self
    {
	    if( $merge ){
		    $this->meta = array_merge( $this->meta, $meta );
	    } else {
		    $this->meta = $meta;
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
    public function viewport( string $value ) : self
    {
	    $this->viewport = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * description function.
     * 
     * @access public
     * @param string $value
     * @return NerbPage
     */
    public function description( string $value ) : self
    {
	    $this->meta['description'] = $value;
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
	public function keywords( array $values, bool $merge = true ) : self
    {
	    if( $merge ){
		    $this->meta['keywords'] = array_merge( $this->meta['keywords'], $values );
	    } else {
		    $this->meta['keywords'] = $values;
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
	public function keyword( $value ) : self
    {
		$this->meta['keywords'][] = $value;
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
   public function author( string $value ) : self
    {
	    $this->meta['author'] = $value;
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
    public function copyright( string $value ) : self
    {
	    $this->meta['copyright'] = $value;
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
    public function robots( string $value ) : self
    {
	    $this->meta['robots'] = $value;
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
    public function appname( string $value ) : self
    {
	    $this->meta['application-name'] = $value;
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
    public function generator( string $value ) : self
    {
	    $this->meta['generator'] = $value;
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
    public function publisher( string $value ) : self
    {
	    $this->meta['publisher'] = $value;
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
    public function creator( string $value ) : self
    {
	    $this->meta['creator'] = $value;
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
    public function alt( string $title, string $value ) : self
    {
	    $this->alt[$title] = $value;
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
    public function style( string $style ) : self
    {
	    $this->style[] = $style;
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
    public function styles( array $style ) : self
    {
	    $this->style = $style;
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
	public function script( string $script ) : self
	{
		$this->script[] = $script;
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
	public function scripts( array $script ) : self
	{
		$this->script = $script;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * rel function.
	 * 
	 * Add link rel statement to header
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return NerbPage
	 */
	public function rel( string $title, string $link ) : self
	{
		$this->rel[$title] = $link;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * icon function.
	 * 
	 * Add link to page icons
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return NerbPage
	 */
	public function icon( string $title, string $link ) : self
	{
		$this->icon[$title] = $link;
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
	public function base( string $url ) : self
	{
		$this->base = $url;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} // end class
