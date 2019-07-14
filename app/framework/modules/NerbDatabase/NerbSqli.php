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
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
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










 /**
 *  Extends the mysqli_stmt class to allow for multiple binding of values 
 *
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Nerb_Statement
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 *  usage -----\
 *
 *	$search1 = "test1"; 
 *	$search2 = "test2"; 
 * 	
 *	$_db = new db("host","user","pass","database"); 
 *	$query = "SELECT name FROM table WHERE col1=? AND col2=?"; 
 *	$stmt = $_db->prepare($query); 
 *	
 *	$stmt->mbind_param('s',$search1); 
 *	//this second call is the cool thing!!! 
 *	$stmt->mbind_param('s',$search2); 
 *	
 *	$stmt->execute(); 
 *	
 *	//this would still work! 
 *	//$search1 = "test1changed"; 
 *	//$search2 = "test2changed"; 
 *	//$stmt->execute(); 
 *
 */

class NerbStatement extends mysqli_stmt
{
    /**
     * mbind_types
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     */
    private $mbind_types = array();
    
    /**
     * mbind_params
     * 
     * (default value: array())
     * 
     * @var array
     * @access private
     */
    private $mbind_params = array();



    /**
     * __construct function.
     *
     * @access public
     * @param mixed $link
     * @param mixed $query
     * @return void
     */
    public function __construct( $link, $query )
    {
        $this->mbind_reset();
        parent::__construct( $link, $query );
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * mbind_reset function.
     * 
     * @access public
     * @return void
     */
    public function mbind_reset()
    {
        unset( $this->mbind_params );
        unset( $this->mbind_types );
        $this->mbind_params = array();
        $this->mbind_types = array();
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * 	mbind_param function.
     * 	use this one to bind params by reference
     * 
     * 	@access public
     * 	@param mixed $type
     * 	@param mixed &$param
     * 	@return void
     */
    public function mbind_param( $type, &$param )
    {
        $this->mbind_types[0] .= $type;
        $this->mbind_params[] = &$param;
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * mbind_value function.
     * use this one to bin value directly, can be mixed with mbind_param()
     * 
     * @access public
     * @param mixed $type
     * @param mixed $param
     * @return void
     */
    public function mbind_value( $type, $param )
    {
        $this->mbind_types[0] .= $type;
        $this->mbind_params[] = $param;
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * mbind_param_do function.
     * 
     * @access public
     * @return void
     */
    public function mbind_param_do()
    {
        $params = array_merge( $this->mbind_types, $this->mbind_params );
        return call_user_func_array( array( $this, 'bind_param' ), $this->makeValuesReferenced( $params ) );
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * makeValuesReferenced function.
     * 
     * @access private
     * @param mixed $arr
     * @return void
     */
    private function makeValuesReferenced( $arr )
    {
        $refs = array();
        foreach ( $arr as $key => $value ) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * execute function.
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        if ( count( $this->mbind_params ) ) {
            $this->mbind_param_do();
        }

        return parent::execute();
        
    } // end function ---------------------------------------------------------------------------------------------------------------------------------------------



} // end class -----------------------------------------------------------------------------------------------------------------------------------------------------
