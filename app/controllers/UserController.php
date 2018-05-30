<?php  /* 

/**
 *	Base class for the Stamp album professional that generates high quality pdf
 *	pages for stamp albums
 *
 *
 * @package    		Stamp Album Pro Admin
 * @class    		UserController
 * @extends    		NerbController
 * @version			1.0
 * @author			Dexter Oddwick <dexter@oddwick.com>
 * @copyright  		Copyright (c)2017
 * @license    		http://www.oddwick.com
 *
 *
 * @todo    		
 *
 */
 
 

class UserController extends NerbController {
		
		/**
		*	Container function for executing domain logic for this module
		*
		*	@access		public
		*/
		public function route(){
		
				// if user is logged in, allow access to private sections, otherwise kick out
				// and send user to login page
				if( !$this->isLogged() ){
					Nerb::jump("/");
					
				}elseif( $this->params["action"] ){
					$this->actions();
					
				} else {
					$page = $this->privatePages();
					include PAGES."/header.php";
					include PAGES."/user/".$page;
					include PAGES."/footer.php";
				}
		}// end function		




		/**
		*	The pages called here require the user to be logged in to view them
		*
		*	@access		protected
		*	@return		string
		*/
		protected function privatePages(){
				
				
				
				// process request
				switch( $this->params["page"] ){
						
				case 'userDetail':
/*
					if($_GET['user_id']){
						Nerb::jump("/user/userDetail/user_id/".$_GET['user_id']);
					}
*/
					$page = "userDetail.php";
					break;
							
				case 'addAdmin':
					$user = $Users->fetchRow($_POST['user_id']);
					$user->account_type = 2;
					$user->save();					
					Nerb::jump("/user");
					break;
							
				default:
					$page = "userList.php";
							

				}// end switch
					
				return $page;
		}// end function		




		/**
		*	The pages called here are public and can be seen by anyone
		*
		*	@access		protected
		*	@return		string
		*/
		protected function publicPages(){
			return "login.php";
		}// end function	
		
		
		
		/**
		*	these are where the action calls are performed
		*
		*	@access		protected
		*	@return		string
		*/
		protected function actions(){
			
			// process request
			switch( $this->params["action"] ){
				
				
				// !data calls
				// ----------------------------------------------------------------------------------------------------------------------------------------------------------------
				case 'setFilter':
					$_SESSION['filter']['letter'] = $this->params['letter'];
					$page = "/user";
					break;
							
				case 'saveUser':
					$this->_saveUser( $this->params['user_id'], $_POST );					
					$page = "/user?msg=".urlencode("User has been saved");
					break;
							
					
				case 'deleteUser':
					$this->_deleteUser( $this->params['user_id'] );					
					$page = "/user?msg=".urlencode("User has been deleted");
					break;
							
					
				case 'logout':
					$this->logout();
					$page = "/?msg=".urlencode("You have been logged out");
					break;
							
				// default action sends back to stamps page
				default:
				$page = "/users";

			}// end switch
				
			Nerb::jump( $page );
		}
		
		
		
			
		/**
		*	logs the user out
		*
		*	@access		protected
		*	@return		string
		*/
		protected function logout(){
				session_unset($_SESSION);
				setcookie('token', 0, time()-3600, "/");
				return( '/?msg=You+have+been+logged+out' );
		}// end function		



		
		/**
		 * _saveUser function.  saves user data
		 * 
		 * @access protected
		 * @param int $user_id
		 * @param int $account_type (default: 0)
		 * @return void
		 */
		protected function _saveUser( $user_id, $data ){
			

			# get required databases 
			$Users = Nerb::fetch('users');
			
			#fetch user data
			$user = $Users->fetchRow( $user_id );
			
			//
			#save variables
			foreach( $data as $key => $value ){
				$user->$key = $value;
			} // end foreach
			
			#save user
			$user->save();	
			
			return;				

		}// end function		





		/**
		 * _deleteUser function.  deletes a user
		 * 
		 * @access protected
		 * @param int $user_id
		 * @return void
		 */
		protected function _deleteUser( $user_id ){
			

			# get required databases 
			$Users = Nerb::fetch('users');
			
			#fetch user data
			$Users->deleteRow( $user_id );
			
			return;				

		}// end function		





		/** 
		*	determines if user is logged in and authorized to see page
		*
		*	@access		public
		*	@return 	bool
		*/
		public function isLogged(){
			
			#	check to see that the token matches user_id decoded and hashed
			return ( $_COOKIE["token"]  && $_COOKIE["token"] == sha1( base64_decode( $_COOKIE["user_id"] ) ) ) ? true : false;
			
		} // end function -----------------------------------------------------------------------------------------------------------------------------------------------



		

		

	} /* end class */
?>
