<?php
// Nerb Application Framework
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
 * @class           User
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2017
 * @requires Error
 * @requires Database
 * @todo
 *
 */


/**
 *
 * Base class for user management
 *
 */
class Recovery
{

    /**
     * params
     * 
     * (default value: array())
     * 
     * @var array
     * @access protected
     */
    protected $params = array();
    
    /**
     * database
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $database = '';
	
    /**
     * users_table
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $users_table = '';
	
    /**
     * user_name_field
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $user_name_field = '';
	
    /**
     * pass_field
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $pass_field = '';
	
    /**
     * id_field
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $id_field = '';


    /**
     * __construct function.
     * 
     * @access public
     * @param string $userstable
     * @param string $id_field
     * @param string $user_name_field
     * @param string $pass_field
     * @param array $params (default: array())
     * @return void
     */
    public function __construct( string $users_table, string $id_field, string $user_name_field, string $pass_field )
    {
	    // check to see if a database is registered
        if( !$database = Nerb::registry()->isClassRegistered( ClassManager::namespaceWrap('Database') ) ){
			throw new Error( 'Could not find a registered database' );
        }
		
        // fetch database and check to see if token table exists
        if( null == TOKEN_TABLE ) {
            throw new Error( '<code>[TOKEN_TABLE]</code> was not defined.' );
        }
		
        // fetch database and check to see if table exists
        $this->database = $database ;
        $database = Nerb::registry()->fetch( $this->database );
		
        if( !$database->isTable( TOKEN_TABLE ) ){
	        $this->createSessionTable();
        } // end if
        
        // pass parameters
        $this->users_table = $users_table;
        $this->id_field = $id_field;
        $this->user_name_field = $user_name_field;
        $this->pass_field = $pass_field;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    #################################################################

    //      !TABLE CREATION

    #################################################################



	/**
	*	creates the password recovery table
	*
	*	@access	protected
	*	@return	void
	*/
	protected function createRecoveryTable()
	{
        $query = '
			CREATE TABLE `'.PASSWORD_RECOVERY_TABLE.'` (
			  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
			  `account_id` int(12) DEFAULT NULL,
			  `user_email` varchar(255) DEFAULT NULL,
			  `key` varchar(255) DEFAULT NULL,
			  `time` int(20) DEFAULT NULL,
			  `ip_address` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
			';
			
        $database = Nerb::registry()->fetch( $this->database );
        $database->execute( $query );
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



	/**
	*	sends an email with a recovery key 
	*
	*	@access	public
	*	@return	string
	*/
	public function sendRecoveryKey( $user_email )
	{
		$Database = Nerb::registry()->fetch( $this->database );
		$Users = new TableRead( $Database, $this->users_table);
		
		$user = $Users->fetchRow( $this->user_name_field." = '".$user_email."'");
		//::inspect($user);
		
		//echo $key = $this->createRecoveryKey();
		$user_first_name = 'dexter';
		$user_email = 'dexter@oddwick.com';
		
		
		$key = $this->createRecoveryKey();
		$message = $this->makeRecoveryEmail( $key, $user_first_name, $user_email );
	
		echo $message;
			//die;
		$to      = $user_email;
		$subject = 'Password recovery';
		//$message = 'hello, your password has been reset';
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.SITE_ADMIN_EMAIL . "\r\n" .
			       'Reply-To: '.SITE_ADMIN_EMAIL . "\r\n" .
				   'X-Mailer: PHP/' . phpversion();

		
		//imap_mail($to, $subject, $message, $headers);
		
		die;
		
		
		return;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	sends an email with a recovery key 
	*
	*	@access	protected
	*	@return	string
	*/
	protected function createRecoveryKey() : string
	{
		//$key = str_pad( mt_rand(1, ( 10 ^ RECOVERY_KEY_LENGTH ) - 1 ), RECOVERY_KEY_LENGTH, STR_PAD_LEFT);
		$key = str_pad( mt_rand(1, ( 10 ** RECOVERY_KEY_LENGTH ) - 1 ), RECOVERY_KEY_LENGTH, STR_PAD_LEFT);
		return $key;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	sends an email with a recovery key 
	*
	*	@access	protected
	*	@return	string
	*/
	protected function expireRecoveryKey( $key ) : string
	{
		//$key = str_pad( mt_rand(1, ( 10 ^ RECOVERY_KEY_LENGTH ) - 1 ), RECOVERY_KEY_LENGTH, STR_PAD_LEFT);
		$key = str_pad( mt_rand(1, ( 10 ** RECOVERY_KEY_LENGTH ) - 1 ), RECOVERY_KEY_LENGTH, STR_PAD_LEFT);
		return $key;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	sends an email with a recovery key 
	*
	*	@access	protected
	*	@return	string
	*/
	protected function makeRecoveryEmail( int $key, string $user_first_name, string $user_email ) : string
	{
		// start an output buffer
		ob_start();
		
		// include the template, fetch the buffer and end buffering
		require RECOVERY_EMAIL_TEMPLATE;
	    $message = ob_get_contents();
	    ob_end_clean();	
		
		// return message
		return $message;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

} // end class

