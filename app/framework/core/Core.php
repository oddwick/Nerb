<?php
// Nerb application library 
namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Core
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */

/**
 *
 * Base class for the site framework
 *
 */
class Core
{

    /**
     * halt function.
     * 
     * stops execution of the Nerb framework 
     *
     * @access public
     * @static
     * @param string $msg mesage to display in the event 
     * @return void
     */
    public static function halt( string $msg = null ) : void
    {
        ob_end_clean();
        echo '<H3>Nerb Application Framework</H3>';
        echo '<p>'.$msg.'</p>';
        echo '<br />';
        echo '<p>'.SOFTWARE.' v'.VERSION.' build '.BUILD.'<br/>';
        echo COPYRIGHT.'</p>';
        exit;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		


    #################################################################

    //            !JUMP METHODS

    #################################################################


    /**
     * jump function.
     * 
     * Jumps to a new page.  probably the single most important function in this file
     *
     * @access public
     * @static
     * @param string $url (The url of page that is being jumpped to)
     * @return void
     */
    public static function jump( string $url )
    {
        global $HTTP_USER_AGENT;

        // if no url is given, the it is assumed that a jump to root url ( / ) is intended
        $url = (  !$url  ) ? '/' : $url;
        Header( "Location: $url" );
        exit;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * filecheck function.
     * 
     * @access public
     * @static
     * @param string $file
     * @return bool
     */
    public static function filecheck( string $file ) : bool
    {
		return ( is_dir( $file ) || !file_exists( $file )) ? false : true;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





} /* end class */
