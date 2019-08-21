<?php
// Nerb Application Framework
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
 * @class           NerbUser
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2017
 * @requires NerbError
 * @requires NerbDatabase
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
     * table
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $table = '';
	
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
     * @param string $table
     * @param string $id_field
     * @param string $user_name_field
     * @param string $pass_field
     * @param array $params (default: array())
     * @return void
     */
    public function __construct( string $table, string $id_field, string $user_name_field, string $pass_field )
    {
		
        // check to see if a database is registered
        if( !$database = Nerb::isClassRegistered( 'NerbDatabase' ) ){
			throw new Error( 'Could not find a registered database' );
        }
		
        // fetch database and check to see if token table exists
        if( null == TOKEN_TABLE ) {
            throw new Error( '<code>[TOKEN_TABLE]</code> was not defined.' );
        }
		
        // fetch database and check to see if table exists
        $this->database = $database ;
        $database = Nerb::fetch( $this->database );
		
        if( !$database->isTable( TOKEN_TABLE ) ){
	        $this->createSessionTable();
        } // end if
        
        // pass parameters
        $this->table = $table;
        $this->id_field = $id_field;
        $this->user_name_field = $user_name_field;
        $this->pass_field = $pass_field;
	       
		
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * setTable function. sets the user table
     * 
     * @access protected
     * @param string $table
     * @return void
     */
    public function setTable( array $table_data )
    {
        $this->table = $table_data['table'];
        $this->user_name_field = $table_data['user'];
        $this->pass_field = $table_data['pass'];
        $this->id_field = $table_data['uid'];
   
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
			
        $database = Nerb::fetch( $this->database );
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
			
        $database = Nerb::fetch( $this->database );
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
		$database = Nerb::fetch($this->database);
		
		// create session table
		$sessions = new DatabaseTable($database, TOKEN_TABLE);
		
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
		$database = Nerb::fetch($this->database);
		
		// create session table
		$sessions = new DatabaseTable($database, TOKEN_TABLE);
		
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
		if (LOG_ATTEMPTS == 'db' || LOG_ATTEMPTS == 'both') {
			// fetch database and bind to userlog table
			$database = Nerb::fetch($this->database);
			$log = new DatabaseTable($database, ACCESS_LOG_TABLE);
			
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
		
		if ( LOG_ATTEMPTS == 'file' || LOG_ATTEMPTS == 'both'  ){
			if( !$status ) {
			    $msg = 'FAIL '.$msg;
			}
			$msg .= '; uid='.$user_id.' uname='.$user_name.' ['.$_SERVER['REMOTE_ADDR'].']';
			$log = new Log( ACCESS_LOG );
			$log->write( $msg );
		}
		
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
		extract($this->params);
		
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
		$Users = Nerb::fetch($table);
		
		// user not found
		if (!$user = $Users->fetchRow($user.' = \''.$user_name.'\'', 1)) {
			$this->logAttempt(0, $user_name, $msg = 'user not found');
			return array(false, $msg);
		}

		if ( !password_verify($user_pass, $user->$pass)) {
			$this->logAttempt($user->user_id, $user_name, $msg = 'invalid password');
			return array(false, $msg);
		}
			
		// log attempt to the log file
		$this->logAttempt($user->user_id, $user_name, 'user authenticated', true);
		$session_id = $this->createSession($user->user_id);
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
		$database = Nerb::fetch($this->database);
		
		// create session table
		$sessions = new DatabaseTable($database, TOKEN_TABLE);
		
		// fetch session data from table
		$session = $sessions->fetchRow('`selector` = \''.$_SESSION['auth'].'\'');

		return $session->expires > time() && hash_equals($session->hash, hash('sha256', $_COOKIE['token'] ) ) ? true : false; 

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
	}// end function		


} // end class

