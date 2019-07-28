<?php
// Nerb Application Framework

 /**
 *  Extends mysqli to return Nerb_Statement instead of standard class
 *
 *	This class has been adapted from a comment at 
 *	http://php.net/manual/en/mysqli-stmt.bind-param.php#110363
 *	by a brilliant person named Guido
 *
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      NerbDatabase
 * @class           Nerb_Sqli
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */

class NerbSqli extends mysqli
{
    /**
     * prepare function.
     *
     * @access public
     * @param mixed $query
     * @return void
     */
    public function prepare( $query )
    {
        return new NerbStatement( $this, $query );
    }
} // end class -----------------------------------------------------------------------------------------------------------------------------------------------------
