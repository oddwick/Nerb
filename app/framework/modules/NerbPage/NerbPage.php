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
     * 	    'header' => MODULES.'/NerbPage/header.phtml',    
     * 	    'footer' => MODULES.'/NerbPage/footer.phtml',    
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
     * @var string
     * @access protected
     */
    protected $params = array(
	    'header' => MODULES.'/NerbPage/header.phtml',    
	    'footer' => MODULES.'/NerbPage/footer.phtml',    
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
	    
	    foreach( $this->content as $key => $value ){
		    require $value;
	    } // end foreach
	    
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
 		if( is_array( $content )){
	 		$this->content = $content;
 		} else {
	 		$this->content[] = $content;
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
