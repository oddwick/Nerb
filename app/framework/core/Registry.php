<?php
// Nerb application library 
Namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbRegistry
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
class Registry
{

    /**
     * config
     * 
     * (default value: array())
     * 
     * @var array
     * @access public
     * @static
     */
    private $config = array();

    /**
     * registry
     *
     * This is an array of objetcs that have been registered as handle => object
     * so that they can be retrieved anywhere in the framework 
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     * @static
     */
    private $registry = array();

	


    /**
     * Singleton Pattern prevents multiple instances of Nerb.
     *
     * @access     private
     * @final
     * @return     void
     */
    public function __construct()
    {

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * register function.
     * 
     * places an object in the registry
     *
     * @access public
     * @static
     * @param object $object
     * @param string $handle
     * @return bool
     * @throws NerbError
     */
    public function register( $object, string $handle ) : bool
    {
        // error checking
        // invalid object passed
        if ( !is_object( $object ) ) {
            throw new Error( 'Can not register <code>['.$handle.']</code> because is not an object.' );
        }
        
        // duplicate handle
        if ( array_key_exists( $handle, $this->registry ) ) {
            // pass on object gracefully
            return true;
            
            // throw error on strict
            //throw new Error( 'An object named <code>['.$handle.'::'.get_class( $this->registry[ $handle ] ).']</code> already exists in the registry' );
        }
        
        //add to registry array
        $this->registry[ $handle ] = $object;

        return true;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * listRegisteredObjects function.
     * 
     * returns a list of registered classes in the registry
     *
     * @access public
     * @static
     * @return array
     */
    public function listRegisteredObjects() : array
    {
        $registry = $this->registry;
        
		$reg = array_map( function( $registry ) {
			return get_class($registry);
		}, $registry );

        return $reg;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * isRegistered function.
     * 
     * determines if an object has been placed in the registry
     *
     * @access public
     * @static
     * @param string $handle
     * @return bool
     */
    public function isRegistered( string $handle ) : bool
    {
        return array_key_exists( $handle, $this->registry ) ? true : false;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * determines if class has been registered and returns the name of the object
     *
     * @access public
     * @static
     * @param string $class
     * @return mixed
     */
    public function isClassRegistered( string $class )
    {
        foreach ( $this->registry as $handle => $object ) {
            if ( is_a( $object, $class ) ) 
                return $handle;
        }

        return false;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * fetch function.
     * 
     * retrieves an object from the registry
     *
     * @access public
     * @static
     * @param string $handle
     * @return object
     * @throws NerbError
     */
    public function fetch( string $handle )
    {
        // check to see if the object is registered
        if ( !$this->isRegistered( $handle ) ) {
            throw new Error( 'Object <code>['.$handle.']</code> is not registered' );
        } // end if
        
        return $this->registry[ $handle ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------






} /* end class */
