<?php
// Nerb application library 
Namespace nerb\framework;

/**
 * Nerb System Framework class management and loader module
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           ClassManager
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */

/**
 *
 * Base class for generating site framework
 *
 */
class ClassManager
{

    /**
     * path
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     * @static
     */
    protected static $path = array();
    

    /**
     * Singleton Pattern prevents multiple instances of Nerb.
     *
     * @access     private
     * @final
     * @return     void
     */
    final private function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * init function.
     * 
     * Initializes the ClassManager and defines the default search paths for loading classes
     *
     * @access public
     * @static
     * @return void
     */
    public static function init()
    {
        // add the reqired directories to the path
        self::setPath( FRAMEWORK, 'root' );
        self::setPath( FRAMEWORK.'/core', 'core' );
        self::setPath( MODULES, 'modules' );
        self::setPath( LIBRARY, 'library' );
        self::getModules();
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * loadClass function.
     * 
     * loads a class definition for use
     *
     * A class must be formatted in the form of 'Class.php' and contain class 'Class' as specified by
     * $class, otherwise an exception will be thrown.
     *
     * If a directory is specified, loadClass will first search that directory, then append the directory to the path, an if not found,
     * it will search the paths specified in ClassManager::$path.   If still not found, an exception will be thrown.
     *
     * @access public
     * @static
     * @param string $class (class name)
     * @param string $dir (directory default is ./app/lib if none is given)
     * @return bool
     */
    public static function loadClass( $class, $dir = null ) : bool
    {
        $class = self::namespaceUnwrap( $class );
        
        // Ensure singularity of the class
        if ( class_exists( $class, false ) ) {
            return true;
        }

        // load class file
        self::loadFile( $class.'.php', $dir );
        

        // if the class was contained in the file, return true otherwise throw an error
        if ( !class_exists( $class ) && !class_exists( self::namespaceWrap( $class ) ) ) {
           Core::halt( "Class <code>[{$class}]</code> was not contained in {$class}.php" );
        }
        
        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * loadFile function.
     * 
     * loads and includes a file
     *
     * If a directory is specified, loadClass will first search that directory, then append the directory to the path, an if not found,
     * it will search the paths specified in ClassManager::$path.   If still not found, an exception will be thrown.
     *
     * @access public
     * @static
     * @param string $file
     * @param string $dir (default: null)
     * @return void
     */
    public static function loadFile( string $file, string $dir = null ) : bool
    {
        //   if directory is given, directly look in directory, otherwise it will attempt to autodiscover
        //   classes using the paths listed in $path
        
        // set relative path
        if ( !empty( $dir )) {
            $file = $dir.( substr($file, 0, 1) == '/' ? '' : DIRECTORY_SEPARATOR ).$file;
        }
        
        if ( @file_exists( $file ) ) {
            return require_once $file;
        } 
        
        if ( $included = self::searchPath( $file ) ) {
            return require_once $included;
        } 
        
        $error = 'Could not load file <code>'.$file.'</code> using the following include paths:<br /><code>';
        foreach ( self::$path as $path ) {
            $error .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$path.'<br />';
        }
        
        $error .= '</code> Please check to ensure the path specified is correct or that the file exists';
        Core::halt( $error );

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * autoload function.
     * 
     * Autoloads a class definition for use
     *
     * @access public
     * @static
     * @param string $class
     * @return void
     * @see loadClass()
     */
    public static function autoload( string $class )
    {
        self::loadClass( $class );
               
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * namespaceWrap function.
     * 
     * 	wraps a class name in a namespace wrapper
     * 
     * 	@access public
     * 	@static
     * 	@param string $name
     * 	@return string
     */
    public static function namespaceWrap( string $name ) : string
    {
        // namespace wrap
        return strpos( $name, '\\' ) !== false ? $name : ( __NAMESPACE__ . '\\' . $name );
	  
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	





    public static function namespaceUnwrap( string $name ) : string 
    {
        // namespace wrap
        $name = explode('\\', $name);
        return array_pop( $name );
	  
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	





    #################################################################

    //            !PATH METHODS

    #################################################################


    /**
     * setPath function.
     * 
     * adds a system path to the global registry
     * if a path alias is present, it will be added as $path[ $alias ]
     * else it will be appended to the end of the $path array
     *
     * @access public
     * @static
     * @param string $dir
     * @param string $alias
     * @return void
     */
    public static function setPath( string $dir, string $alias = null )
    {
        // check to see if the path is a valid directory
        if ( !is_dir( $dir ) ) {
	        Core::halt( 'Set path error -- <code>['.$path.']</code> is not a valid path.' );
        }
        
        self::$path[$alias] = $dir;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * getPath function.
     * 
     * returns the application path(s)
     *
     * @access public
     * @static
     * @param string $alias
     * @return string
     */
    private static function getPath( string $alias = null ) : string
    {
        return $alias ? self::$path[ $alias ] : self::$path;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * searchPath function.
     * 
     * searches Nerb::$path for a file and returns the location of that file
     *
     * @access public
     * @static
     * @param string $file (file name)
     * @return mixed (file name on success, false on failure)
     */
    private static function searchPath( string $file )
    {
        foreach ( self::$path as $value ) {
            if ( file_exists( $value.'/'.$file ) ) {
                return realpath( $value.'/'.$file );
            }// end if
        }// end foreach

        return false;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * getModules function.
     * 
     * @access protected
     * @static
     * @return void
     */
    private static function getModules()
    {
        // scan for available modules
        $files = array_diff( scandir( MODULES ), ['..', '.'] );	

        // set include paths
        foreach( $files as $file ){
            self::setPath( MODULES .'/'. $file, $file );
        } // end foreach
        
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * lists the available modules path.
     * 
     * @access public
     * @static
     * @return array
     */
    public static function listAvailableModules() : array
    {
       return array_diff( scandir( MODULES ), ['..', '.'] );	
       
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * lists the currentyly set paths path.
     * 
     * @access public
     * @static
     * @return array
     */
    public static function listPaths() : array
    {
       return self::$path;	
       
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




} /* end class */
