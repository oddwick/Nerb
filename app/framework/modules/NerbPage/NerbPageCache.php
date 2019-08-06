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
 * @property CACHE_DIR defined in config.ini
 * @property CACHE_TTL defined in config.ini
 * @global CACHE_DIR defined in config.ini
 *
 */


class NerbPageCache
{

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
	protected $page = null;

	
	/**
	 * cache_dir
	 * 
	 * (default value: null)
	 * 
	 * @var string
	 * @access protected
	 */
	protected $cache_dir = null;

	
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
     * Constructor initiates Page Cache object
     *
     * @access public
     * @param string $page
     * @throws NerbError
     * @return NerbPageCache
     */
    public function __construct( string $filename )
    {
	    if( !is_dir(CACHE_DIR) ){
		    throw new NerbError( "Cache directory <code>[".CACHE_DIR."]</code> is not a valid directory.  Check <code>[config.ini]</code> for proper configuration." );
	    }
        $this->filename = $filename;
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //      !PAGE CACHING

    #################################################################
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
	    $files = glob( CACHE_DIR.'/*.cache', GLOB_BRACE );
	    
	    // loop through and delete files
	    foreach( $files as $file ){
		    $status = @unlink( $file );
	    	if( !$status ){
				$log = new NerbLog( ERROR_LOG );
				$log->write( 'Could not delete cache file: '.$file, 'ERROR' );
		    	$error = true;
		    }
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
	    $file = CACHE_DIR.'/'.$this->filename;
	    
	    if( file_exists( $file ) ){
		    if ( @unlink( $file ) ){
			    return true;
		    } else {
				$log = new NerbLog( ERROR_LOG );
				$log->write( 'Could not delete cache file: '.$file, 'ERROR' );
			    return false;
		    }
	    }
	    
	    // return false if file does not exist
	    return false;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * write function.
     *
     * writes out the contents of the page cache to a file
     * 
     * @access public
     * @throws NerbError
     * @return bool
     */
    public function write( $content ): bool
    {
	    // ignore user abort to make sure that the cache is fully written to
	    // prevent corrupted cache or code injection
	    ignore_user_abort( true );
	    
	    // error checking
	    if( !is_dir( CACHE_DIR ) ){
		    throw new NerbError( 'Cache directory <code>'.CACHE_DIR.'<code> does not exist' );
	    } elseif( !$this->filename ){
		    throw new NerbError( 'Invalid file name given' );
	    }
	    
	    $file = CACHE_DIR.'/'.$this->filename;
	    
	    // write contents to directory	    
	    $status = @file_put_contents( $file, $content );
	    if( $status ){
		    return true;
	    } else {
		    $log = new NerbLog( ERROR_LOG );
		    $log->write( 'Could not write to cache file: '.$file, 'ERROR' );
		    return false;
	    }
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * is_cached function.
     *
     * checks to see if the current page is cached and returns true if it is
     * if the page is expired, it will try and remove the page from the cache directory for housekeeping
     * 
     * @access public
     * @return bool
     */
    public function isCached() : bool
    {
	    // get file name
	    $file = CACHE_DIR.'/'.$this->filename;
	    
	    // file must exist and mod time must be greater than current time - ttl for cache to be active
	    // or if cache_ttl = -1 (permenant caching)
	    if( 
	    	( file_exists( $file ) && CACHE_TTL == -1 ) || 
			( file_exists( $file ) && filemtime( $file ) > ( time() - CACHE_TTL ) ) 
		){
	    	return true;			
	    } // end if
	    
	    // housekeeping --
	    // clean up expired cache files if past time
	    $this->uncache();
	    
	    return false;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * fetch_cache function.
     *
     * reads the contents of the cache file int output buffer and returns false on fail
     * 
     * @access public
     * @return bool
     */
    public function fetchCache() : bool
    {
	    $filename = CACHE_DIR.'/'.$this->filename;
	    
	    // check to see if the file exists and read it to the output buffer
	    if( file_exists( $filename ) ){
		    
		    // send header
		    header('Content-Type: text/html');
			
			// clear output buffer and start new buffer
			ob_end_clean();
		    ob_start();
		    // get file contents. readfile is more secure than include, prevents 
		    // any embeded php from getting processed
			readfile( $filename );
		    if( DEBUG ){
			    echo '<pre>';
			    echo 'Cached - expires: '.(date( "m.d.y H:i:s", filemtime( $filename ) + CACHE_TTL ));
			    echo '</pre>';
			}
			
			//output buffer and clear
			ob_flush();
			ob_end_clean();
			return true;		    
	    }
	    
	    // return false if the page was not read 
	    return false;
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} // end class
