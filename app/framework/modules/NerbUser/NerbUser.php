<?php

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
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */


/**
 *
 * Base class for user management
 *
 */
class NerbUser
{

    /**
     * params
     * 
     * (default value: array(
     * 	    'table' => '',
     * 		'user'  => '',
     * 		'pass'  => '',
     * 		'uid'   => '',
     *     ))
     * 
     * @var string
     * @access protected
     */
    protected $params = array(
	    'database' => '',
	    'table' => '',
		'user'  => '',
		'pass'  => '',
		'uid'   => '',
    );


    /**
     * __construct function.
     * 
     * @access public
     * @param array $params (default: array())
     * @return void
     */
    public function __construct( array $params = array() )
	{
		
		// add params if given
		if( $params ){
			$this->params = array_merge( $this->params, $params);
		}
		
		// check to see if a database is registered
		if( $database = Nerb::isClassRegistered( 'NerbDatabase' ) ){
			$this->params['database'] = $database;
		} else {
			throw new NerbError( 'Could not find a registered database' );
		}
		
		// fetch database and check to see if token table exists
		if( null == TOKEN_TABLE ) {
			throw new NerbError( '<code>[TOKEN_TABLE]</code> was not defined.' );
		}
		
		// fetch database and check to see if table exists
		$db = Nerb::fetch( $database );
		
		if( !$db->isTable( TOKEN_TABLE ) ){
			// attempt to create a token table otherwise throw error
			if( CREATE_TOKEN_TABLE ){
				$this->createSessionTable();
			} else {
				throw new NerbError( 'Could not find table <code>['.TOKEN_TABLE.']</code> in database.' );
			} // end if
		} // end if
		
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
		$this->params['table'] = $table_data['table'];
		$this->params['user'] = $table_data['user'];
		$this->params['pass'] = $table_data['pass'];
		$this->params['uid'] = $table_data['uid'];
   
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
			
		$database = Nerb::fetch( $this->params['database'] );
		$database->query( $query );
   
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
			
		$database = Nerb::fetch( $this->params['database'] );
		$database->query( $query );
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    #################################################################

    //      !SESSIONS

    #################################################################


	/**
	 * createSession function.
	 * 
	 * @access protected
	 * @param mixed $user_id
	 * @return void
	 */
	protected function createSession( $user_id )
	{
		// fetch database
		$database = Nerb::fetch( $this->params['database'] );
		
		// create session table
		$sessions = new NerbDatabaseTable( $database, TOKEN_TABLE );
		
		// check to see if there is already a session for this user
		//if( $this->isSessionActive( $user_id ) ) $this->destroySession( $user_id );
		
		// generate selector and validator
		$validator = $this->generateToken();
		$selector = $this->generateToken( 12 );
		
		// build session data
		$data = array(
			'selector' => $selector,
			'hash' => hash( 'sha256', $validator ),
			'user_id' => $user_id,
			'expires' => time() + SESSION_EXPIRES,
   		);
   		
   		// save data to table
   		$sessions->insert( $data );
   		
   		
   		// set cookie variables
		if( SESSION_TYPE == 'cookie' ){
			setcookie('token', $validator, time() + SESSION_EXPIRES, '/'); 
			setcookie('auth', $selector, time() + SESSION_EXPIRES, '/');
		}
		
		// set session variables
		$_SESSION['auth'] = $selector;
		$_SESSION['token'] = $validator;
		$_SESSION[ $this->params['uid'] ] = $user_id;
		$_SESSION[ 'uid'] = $user_id;
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
		$database = Nerb::fetch( $this->params['database'] );
		
		// create session table
		$sessions = new NerbDatabaseTable( $database, TOKEN_TABLE );
		
		$sessions->deleteRows( "`selector` = '".$_SESSION['auth']."'" );
		
		
		// unset the cookies
		setcookie('token', '', time() - 3600, '/'); 
		setcookie('auth', '', time() -3600, '/');
		
		// unset the session tokens
		unset( $_SESSION['auth'] );
		unset( $_SESSION['user_id'] );
		unset( $_SESSION['begin'] );
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * createSessionTable function.
	 * 
	 * @access public
	 * @return void
	 */
	protected function isSessionActive()
	{
   
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * generateToken function.
	 * 
	 * @access protected
	 * @param int $length (default: 20)
	 * @return void
	 */
	protected function generateToken( int $length = 20 )
	{
	    return bin2hex( random_bytes( $length ) );
	    
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

	//hash_equals();
	
	
	
	
	/**
	*	logs all login attempts to the database
	*
	*	@access		protected
	*	@param 		string user_id
	*	@param 		string userName
	*	@param 		string msg
	*	@param 		bool status default is false
	*	@return		string
	*/
	protected function logAttempt( $user_id, $user_name, $msg, $status = false )
	{
		if( LOG_ATTEMPTS == 'db' || LOG_ATTEMPTS == 'both' ){
			// fetch database and bind to userlog table
			$database = Nerb::fetch( $this->params['database'] );
			$log = new NerbDatabaseTable( $database, ACCESS_LOG_TABLE );
			
			// setup log array
			$data = array( 
				'user_id' => (int)$user_id,
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

				 
		} elseif ( LOG_ATTEMPTS == 'file' || LOG_ATTEMPTS == 'both'  ){
			
			if( !$status ) $msg = 'FAIL '.$msg;
			$msg .= '; uid='.$user_id.' uname='.$user_name.' ['.$_SERVER['REMOTE_ADDR'].']';
			$status = Nerb::log( ACCESS_LOG, $msg );
		}
		return;
			
	}// end function		



	/**
	*	The pages called here are public and can be seen by anyone
	*
	*	@access		protected
	*	@param		array $data
	*	@return		array
	*/
	public function authenticate( $user_name, $user_pass )
	{
		
		// inport table data
		extract( $this->params );
		
		// validation
		// no user name
		if( empty( $user_name ) ){ 
			$this->logAttempt( 0, $user_name, $msg = 'empty username');					
			return array( false, $msg );
		} 
		
		// empty password
		if ( empty( $user_pass ) ){
			$this->logAttempt( 0, $user_name, $msg = 'empty password'  );
			return array( false, $msg );
		} 
		
		// fetch database and tables		
		$Users = Nerb::fetch( $table );
		
		// user not found
		if( !$user = $Users->fetchFirstRow( $user.' = \''.$user_name.'\'') ){
			$this->logAttempt( 0, $user_name, $msg = 'user not found'  );
			return array( false, $msg );
		}

		// successful login
		if( password_verify( $user_pass,  $user->$pass )  ){
			
			// log attempt to the log file
			$this->logAttempt( $user->user_id, $user_name, 'user authenticated', true );
			
			$session_id = $this->createSession( $user->user_id );
			// jump to home page
			return array( true, $session_id );
			
		} else {
			$this->logAttempt( $user->user_id, $user_name, $msg = 'invalid password' );
		}
		
		return array( false, $msg );
				
	}// end function		




	/**
	*	The pages called here are public and can be seen by anyone
	*
	*	@access		protected
	*	@return		string
	*/
	public function authorize()
	{

	}// end function		




	/**
	 * verify function.
	 * 
	 * @access public
	 * @return bool
	 */
	public function verify()//: bool
	{
		// find and unset session in database
		// fetch database
		$database = Nerb::fetch( $this->params['database'] );
		
		// create session table
		$sessions = new NerbDatabaseTable( $database, TOKEN_TABLE );
		
		$session = $sessions->fetchFirstRow( '`selector` = \''.$_SESSION['auth'].'\'' );

		if( $session->expires > time() && hash_equals( $session->hash, hash( 'sha256', $_COOKIE['token'] ))){
			return true;
		} else {
			return false;
		}
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	logs a user out
	*
	*	@access		protected
	*	@return		string
	*/
	public function logout()
	{
		session_unset($_SESSION);
		setcookie('token', 0, time()-3600, '/');
		return( '/?msg=You+have+been+logged+out' );
	}// end function		


} // end class






?>