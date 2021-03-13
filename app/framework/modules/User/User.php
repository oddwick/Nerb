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
class User
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
     * data
     * 
     * (default value: array() )
     * 
     * @var array
     * @access protected
     */
    protected $data = array();



    /**
     * __construct function.
     * 
     * @access public
     * @param string $user_id
     * @return void
     */
    public function __construct( string $user_id )
    {
	    // check to see if a database is registered
        if( !$database = Nerb::registry()->isClassRegistered( ClassManager::namespaceWrap('Database') ) ){
			throw new Error( 'Could not find a registered database' );
        }
		
        // fetch database and check to see if table exists
        $this->database = $database ;
        $database = Nerb::registry()->fetch( $this->database );
		
        if( !$database->isTable( USER_TABLE ) ){
			throw new Error( 'User table was not defined' );
        } // end if
        
        // get user
        $Users = new TableRead( $database, USER_TABLE, false ); 
        $user = $Users->fetchRow( USER_ID_FIELD." = ".$user_id );
        $this->data = $user->values();
        
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   returns a value by key
     *
     *   @access     public
     *   @param      string $field (field name)
     *   @return     mixed
     */
    public function __get( string $field ) 
    {
        // check to see if field exists
        if ( !array_key_exists( $field, $this->data ) ) {
			return false;
        }
        return $this->data[$field];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------







    #################################################################

    //      !TABLE CREATION

    #################################################################


    /**
     * createSessionTable function.
     * 
     * @access protected
     * @return void
     */
    protected function createSessionTable()
    {
        $query = '
			CREATE TABLE `'.TOKEN_TABLE.'` (
			    `session_id` int(12) UNSIGNED AUTO_INCREMENT not null ,
			    `selector` char(24),
			    `hash` char(64),
			    `user_id` int(12) UNSIGNED not null,
			    `expires` int(12),
			    PRIMARY KEY (`session_id`)
			)';
			
        $database = Nerb::registry()->fetch( $this->database );
        $database->execute( $query );
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * createSessionTable function.
     * 
     * @access protected
     * @return void
     */
    protected function createLogTable()
    {
        $query = '
			CREATE TABLE `'.ACCESS_LOG_TABLE.'` (
			    `log_id` int(12) UNSIGNED AUTO_INCREMENT not null ,
			    `user_id` int(6) not null ,
			    `user_name` varchar(100),
			    `ip_address` varchar(100),
			    `http_user_agent` varchar(100),
			    `proxy` varchar(100),
			    `log_time` int(16),
			    `status` tinyint(1),
			    `reason_for_fail` varchar(100),
			    PRIMARY KEY (`log_id`)
			)';
			
        $database = Nerb::registry()->fetch( $this->database );
        $database->execute( $query );
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




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




    #################################################################

    //      !SESSIONS

    #################################################################


	/**
	 * createSession function.
	 * 
	 * @access protected
	 * @param mixed $user_id
	 * @return string
	 */
	protected function createSession($user_id)
	{
	
		// fetch database
		$database = Nerb::registry()->fetch($this->database);
		
		// create session table
		$sessions = new \nerb\framework\TableWrite($database, TOKEN_TABLE);
		
		// check to see if there is already a session for this user
		//if( $this->isSessionActive( $user_id ) ) $this->destroySession( $user_id );
		
		// generate selector and validator
		$validator = $this->generateToken();
		$selector = $this->generateToken(12);
		
		// build session data
		$data = array(
			'selector' => $selector,
			'hash' => hash('sha256', $validator),
			'user_id' => $user_id,
			'expires' => time() + SESSION_EXPIRES,
   		);
   		
   		// save data to table
   		$sessions->insert($data);
   		
   		// set cookie variables
		if (SESSION_TYPE == 'cookie') {
			setcookie('token', $validator, time() + SESSION_EXPIRES, '/'); 
			setcookie('auth', $selector, time() + SESSION_EXPIRES, '/');
		}
		
		// set session variables
		$_SESSION['auth'] = $selector;
		$_SESSION['token'] = $validator;
		$_SESSION[$this->id_field] = $user_id;
		$_SESSION['uid'] = $user_id;
		$_SESSION['begin'] = time();

		// return session_id
		return $selector;
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * createSessionTable function.
	 * 
	 * @access public
	 * @return void
	 */
	public function destroySession()
	{
		// find and unset session in database
		// fetch database
		$database = Nerb::registry()->fetch($this->database);
		
		// create session table
		$sessions = new \nerb\framework\TableWrite($database, TOKEN_TABLE);
		
		$sessions->deleteRows("`selector` = '".$_SESSION['auth']."'");
		
		// unset the cookies
		setcookie('token', '', time() - 3600, '/'); 
		setcookie('auth', '', time() - 3600, '/');
		
		// unset the session tokens
		unset($_SESSION['auth']);
		unset($_SESSION['user_id']);
		unset($_SESSION['begin']);
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * isSessionActive function.
	 * 
	 * @access protected
	 * @return void
	 * @todo
	 */
	protected function isSessionActive()
	{
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * generateToken function.
	 * 
	 * @access protected
	 * @param int $length (default: 20)
	 * @return string
	 */
	protected function generateToken(int $length = 20) : string
	{
	    return bin2hex(random_bytes($length));
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

	//hash_equals();
	
	
	
	
	/**
	*	logs all login attempts to the database
	*
	*	@access		protected
	*	@param 		int $user_id
	*	@param 		string $user_name
	*	@param 		string $msg
	*	@param 		bool $status (default: false)
	*	@return		void
	*/
	protected function logAttempt(int $user_id, string $user_name, string $msg, bool $status = false)
	{
		if( !$status ) {
		    $msg = 'FAIL '.$msg;
		}
		
		if (LOG_ATTEMPTS == 'db') {
			// fetch database and bind to userlog table
			$database = Nerb::registry()->fetch( $this->database );
			$log = new \nerb\framework\TableWrite($database, ACCESS_LOG_TABLE);
			
			// setup log array
			$data = array( 
				'user_id' => (int) $user_id,
				'user_name' => $user_name,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'proxy' => $_SERVER['HTTP_X_FORWARDED_FOR'],
				'log_time' => time(),
				'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'status' => $status,
				'reason_for_fail' => $msg,
			 );
			 
			// insert log			 
			$log->insert( $data );	
		} 
		$msg .= '; uid='.$user_id.' uname='.$user_name.' ['.$_SERVER['REMOTE_ADDR'].']';
		$log = new \nerb\framework\Log( ACCESS_LOG );
		$log->write( $msg );
		
		return;
			
	}// end function		




	/**
	 * authenticate function.
	 * 
	 * Authenticates the user against the user table and logs authentication attempt
	 *
	 * @access public
	 * @param string $user_name
	 * @param string $user_pass
	 * @return array
	 */
	public function authenticate(string $user_name, string $user_pass) : array
	{
		
		// inport table data
		$pass_field = $this->pass_field;
		$id_field = $this->id_field;
		$user_name_field = $this->user_name_field;
		
		// validation
		// no user name
		if (empty($user_name)) { 
			$this->logAttempt(0, $user_name, $msg = 'empty username');					
			return array(false, $msg);
		} 
		
		// empty password
		if (empty($user_pass)) {
			$this->logAttempt(0, $user_name, $msg = 'empty password');
			return array(false, $msg);
		} 
		
		// fetch database and tables
        $database = Nerb::registry()->fetch( $this->database );
		$Users = new \nerb\framework\TableRead( $database, $this->users_table, false );	
		$user = $Users->fetchRow( '`'.$user_name_field.'` = \''.$user_name.'\'', 1);
				
		// user not found
		if (!$user) {
			$this->logAttempt(0, $user_name, $msg = 'user not found');
			return array(false, $msg);
		}
		
		if ( !password_verify($user_pass, $user->$pass_field)) {
			$this->logAttempt($user->{$this->id_field}, $user_name, $msg = 'invalid password');
			return array(false, $msg);
		}
		
		
		// log attempt to the log file
		$this->logAttempt($user->$id_field, $user_name, 'user authenticated', true);
		$session_id = $this->createSession( $user->$id_field );
		
		
		return array(true, $session_id);
				
	}// end function		




	/**
	 * authorize function.
	 * 
	 * @access public
	 * @return void
	 * @todo
	 */
	public function authorize()
	{

	}// end function		




	/**
	 * verify function.
	 * 
	 * @access public
	 * @return bool
	 * @property expires;
	 * @property hash;
	 */
	public function verify() : bool
	{
		// fetch database
		$database = Nerb::registry()->fetch($this->database);
		
		// create session table
		$sessions = new \nerb\framework\TableRead($database, TOKEN_TABLE);
		
		// fetch session data from table
		$session = $sessions->fetchRow('`selector` = \''.$_SESSION['auth'].'\'');

		return $session->expires > time() && hash_equals($session->hash, hash('sha256', $_COOKIE['token'] ) ) ? true : false; 

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * identifies the user and returns the user's id if session is active.  returns user_id on success and 0 on failure
	 * 
	 * @access public
	 * @return bool
	 * @property expires;
	 * @property hash;
	 */
	public function identify() : int
	{
		// fetch database
		$database = Nerb::registry()->fetch($this->database);
		
		// create session table
		$sessions = new \nerb\framework\TableRead($database, TOKEN_TABLE);
		
		// fetch session data from table
		$session = $sessions->fetchRow('`selector` = \''.$_SESSION['auth'].'\'');

		return count($session) == 1 ? $session->user_id : 0; 

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	logs a user out
	*
	*	@access	protected
	*	@return	string
	*/
	public function logout() : string
	{
		session_unset();
		setcookie('token', 0, time() - 3600, '/');
		return('/?msg=You+have+been+logged+out');
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
		$Users = new TableRead( $Database, $this->users_table, false);
		
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

