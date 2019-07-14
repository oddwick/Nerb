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
     * 	    'cache_control' => public,  // public | private | private_no_expire | nocache
     * 	    'asynch_scripts' => false,
     * 	    'scripts_in_header' => true,   
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
     * 
     *     ))
     * 
     * @var array
     * @access protected
     */
    protected $params = array(
	    'browser_check' => false,
	    'use_error_pages' => false,
	    'cache_control' => "public", 
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
     *  Constructor initiates Page object
     *
     * @access public
     * @param string $ini
     * @param string $path
     * @param array $params (default: array())
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
        	
        // blew it
	    } else {
            throw new NerbError( 'Could not locate given configuration file <code>'.$ini.'</code>' );
        }

        // load and parse ini file and distribute variables
        // the user changeable variables will end up in $params and the defaults will be kept in $defaults
        try {
            // if the config.ini file is read, it loads the values into the params
            $data = parse_ini_file( $ini_file, false );
            $data = $this->parse( $data );
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

        // for debugging
        //Nerb::inspect(  $this->params[ $this->params_key ], true  );
        
        
        if( $this->params['browser_check'] ){
	        $this->browserCheck();
        }
        
		// this sends header commands to prevent the browser from caching contents and 
	    // must revalidate.  this is best for pages that one must be logged in to view
	    if( $this->params['cache_control'] ){
		    session_cache_limiter( $this->params['cache_control'] );
		    //header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			//header("Pragma: no-cache"); // HTTP 1.0.
			//header("Expires: 0"); // Proxies.
		}

	    
	    

        
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * parse function.
     * 
     * @access protected
     * @param array $data
     * @return void
     */
    protected function parse( array $data )
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
    public function __set( string $key, string $value ): string
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
    public function __get( $key )
    {
        // returns value
        return $this->params[ $key ];
        
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
	    
	    // include header
/*
	    $file = file_get_contents( $this->params['header'] );
	    
	    echo htmlentities( $file );
	    
	    $file = file_get_contents( $this->params['footer'] );
	    
	    echo htmlentities( $file );
	    
*/
	    require $this->params['header'];
	    
	    if( $this->contentHeader ) require $this->contentHeader;
	    
	    if( $this->error || ( empty( $this->content ) && $this->params['use_error_pages'] )){
	    	
	    	switch( $this->error ){
		    	
		    	// forbiden overrides a 404
		    	// unsupported browser	
		    	case 100:
			    	require $this->params['error_100'];
		    		break;
		    		
		    	case 403:
			    	require $this->params['error_403'];
		    		break;
		    		
		    	// page not found	
		    	case 404:
			    	require $this->params['error_404'];
		    		break;
		    		
		    	// service error and unspecified errors
		    	case 500:
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
        ob_flush();
        ob_end_clean();
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * cache function.
     * 
     * @access public
     * @return void
     */
    public function cache()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * write function.
     * 
     * @access public
     * @param string $filename
     * @param bool $overwrite (default: true)
     * @return void
     */
    public function write( string $filename, bool $overwrite = true )
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !Content

    #################################################################




    /**
     * content function.
     * 
     * @access public
     * @param mixed $content
     * @return void
     */
    public function content( $content )
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
     * @return void
     */
    public function contentHeader( string $content )
    {
 		$this->contentHeader = $content;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * contentFooter function.
     * 
     * @access public
     * @param string $content
     * @return void
     */
    public function contentFooter( string $content )
    {
 		$this->contentFooter = $content;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
	/**
	 * data function.
	 * 
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function data( $data, string $value = '' )
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
     * @return void
     */
    public function header( string $file )
    {
 		$this->params['header'] = $file;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     * footer function.
     * 
     * @access public
     * @param string $file
     * @return void
     */
    public function footer( string $file )
    {
 		$this->params['footer'] = $file;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * error function (500 page error).
     * 
     * @access public
     * @return void
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
     * @return void
     */
    public function notFound()
    {
 		$this->error = 404;
		return $this;
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






    /**
     * unauth function (403 error).
     * 
     * @access public
     * @return void
     */
    public function unauth()
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
	 * @return void
	 */
	public function title( string $title )
	{
		$this->params['title'] = $title;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * charset function.
	 * 
	 * @access public
	 * @param string $charset
	 * @return void
	 */
	public function charset( string $charset )
	{
		$this->params['charset'] = $charset;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * lang function.
	 * 
	 * @access public
	 * @param string $lang
	 * @return void
	 */
	public function lang( string $lang )
	{
		$this->params['lang'] = $lang;
		return $this;
		
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * icon function.
     * 
     * @access public
     * @param string $icon
     * @return void
     */
    public function icon( string $icon )
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
     * @return void
     */
    public function equiv( string $title, string $value )
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
     * @return void
     */
    public function meta( array $meta, bool $merge = false )
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
     * @return void
     */
    public function viewport( string $value )
    {
	    $this->params['viewport'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * description function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function description( string $value )
    {
	    $this->params['meta']['description'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * keywords function.
	 * 
	 * @access public
	 * @param array $values
	 * @param bool $merge (default: true)
	 * @return void
	 */
	public function keywords( array $values, bool $merge = true )
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
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function keyword( $value )
    {
		$this->params['meta']['keywords'][] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




   /**
    * author function.
    * 
    * @access public
    * @param string $value
    * @return void
    */
   public function author( string $value )
    {
	    $this->params['meta']['author'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * copyright function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function copyright( string $value )
    {
	    $this->params['meta']['copyright'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * robots function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function robots( string $value )
    {
	    $this->params['meta']['robots'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * appname function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function appname( string $value )
    {
	    $this->params['meta']['application-name'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * generator function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function generator( string $value )
    {
	    $this->params['meta']['generator'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * publisher function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function publisher( string $value )
    {
	    $this->params['meta']['publisher'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * creator function.
     * 
     * @access public
     * @param string $value
     * @return void
     */
    public function creator( string $value )
    {
	    $this->params['meta']['creator'] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * alt function.
     * 
     * @access public
     * @param string $title
     * @param string $value
     * @return void
     */
    public function alt( string $title, string $value )
    {
	    $this->params['alt'][$title] = $value;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * style function.
     * 
     * @access public
     * @param string $style
     * @return void
     */
    public function style( string $style )
    {
	    $this->params['style'][] = $style;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * styles function.
     * 
     * @access public
     * @param array $style
     * @return void
     */
    public function styles( array $style )
    {
	    $this->params['style'] = $style;
	    return $this;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * script function.
	 * 
	 * @access public
	 * @param string $script
	 * @return void
	 */
	public function script( string $script )
	{
		$this->params['script'][] = $script;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * scripts function.
	 * 
	 * @access public
	 * @param array $script
	 * @return void
	 */
	public function scripts( array $script )
	{
		$this->params['script'] = $script;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * rel function.
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return void
	 */
	public function rel( string $title, string $link )
	{
		$this->link( $title, $link );
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * link function.
	 * 
	 * @access public
	 * @param string $title
	 * @param string $link
	 * @return void
	 */
	public function link( string $title, string $link )
	{
		$this->params['rel'][$title] = $link;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * base function.
	 * 
	 * @access public
	 * @param string $url
	 * @return void
	 */
	public function base( string $url )
	{
		$this->params['base'] = $url;
		return $this;
	
	} // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} // end class
