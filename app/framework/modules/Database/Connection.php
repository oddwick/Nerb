<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Nerb Application Framework
 *
 *	This is the core of the Nerb database package and manages all of the basic connections and 
 *	querying of the database.  All results are returned in the form of a mysqli result object. 
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      Database
 * @class           Connection
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c ) 2017
 * @todo
 *
 */


class Connection
{
    /**
     * __construct function.
     * 
     * @access private
     * @final
     * @return void
     */
    private final function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   Connection string, connects to database with credentials given
     *
     *   @access     public
     *   @return     void
     *   @throws     Error
     */
    public static function create( string $name, string $user, string $pass, string $host = 'localhost', $port = 3306, $socket = null )
    {
        // error checking 
        $connection = new Sqli( $host, $user, $pass, $name, $port, $socket );
			
        if ( mysqli_connect_error() ) {
            $error = mysqli_connect_error();
            $errno = mysqli_connect_errno();
			
            throw new Error(
                '<p>Could not connect to Database host <strong>'.$host.'</strong>. Database said:</p>'
                .'<p>'.$error.'</p>'
                .'<p>Error #'.$errno.'</p>'
            );
        }
			
		return $connection;
	
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




} /* end class */
