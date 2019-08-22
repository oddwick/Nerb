<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * 	Simple router controller for managing users
 *
 *
 * @package    		Nerb Application Framework
 * @class    		UserController
 * @extends    		NerbController
 * @version			1.0
 * @author			Dexter Oddwick <dexter@oddwick.com>
 * @copyright  		Copyright (c)2017
 * @license         https://www.github.com/oddwick/nerb
 *
 *
 * @todo
 *
 */


class UserController extends Controller
{

    /**
     * title
     *
     * This is the default value for the page title
     * 
     * (default value: 'Sample User Controller')
     * 
     * @var string
     * @access protected
     */
    protected $title = 'Sample User Controller';
    
    /**
     * user
     * 
     * @var User
     * @access protected
     */
    protected $user;
	
	
	
    /**
     *   Container function for executing domain logic for this module
     *
     *   @access public
     *   @return self
    */
    public function route() : self
    {
        // define page structure for the controller
        $this->url->defineStructure( array( 'page') );
        
        // create user object
        $this->user = new User( 'user_table', 'user_id', 'user_name', 'user_pass' );
		
        // action calls
        if ( $this->url->action() ) {
            $this->action();
        }
        
        // if user is logged in, allow access to private sections, 
        // otherwise kick out to registration page
        $content = !$this->user->verify() ? $this->publicPages() : $this->privatePages();
        
        // fetch page object and add content to it
        $page = Nerb::registry()->fetch('Page');
        $page->title($this->title);
        $page->content(PAGES.'/'.$content);
        
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * This is a switchboard for private pages and require the user to be logged in to view them
     *
     * @access protected
     * @property string $page
     * @return string
     */
    protected function privatePages() : string
    {
        switch ($this->page) {
            case 'forgotPass':
            default:
                $page = $this->module.'/login.php';
        }// end switch
        return $page;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * This is a switchboard for public pages and can be seen by anyone
     * 
     * @access protected
     * @property string $page
     * @return string
     */
    protected function publicPages()
    {
        switch ($this->page) {
            case 'forgotPass':
            default:
                $page = $this->module.'/login.php';
        }// end switch
        return $page;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * This is a switchboard where actions are performed. a jump is performed on the completion of an action
     * 
     * @access protected
     * @property string $action
     * @return void
     */
    protected function action()
    {
        switch ( $this->url->action() ) {
            case 'login':
                $page = $this->login( $_REQUEST['user_name'], $_REQUEST['user_pass'] );
                break;
            
            case 'logout':
				$this->user->destroySession();
				$page = '/?msg='.urlencode('You have been logged out');
				break;
            
            default:
                $page = '/';
        }// end switch
        
        // jump to action endpoint
        Core::jump($page);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #####################################################################
	
    //		!USER DEFINED FUNCTIONS
    
    #####################################################################




	/**
	 * login function.
	 * 
	 * @access protected
	 * @param mixed $user_name
	 * @param mixed $user_pass
	 * @return string
	 */
	protected function login( string $user_name, string $user_pass ) : string
	{
		
		// authenticate user
		$status =  $this->user->authenticate( $user_name, $user_pass );
		
		// sucessful authentication
		if( $status[0] == TRUE ){
			$page = '/?msg='.urlencode( 'Welcome Back' );
		} 
		
		// failed authentication
		else {
			$page = $this->url->return_page().'?error='.urlencode( $status[1] );
		}
		
		return $page;	
				
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





} /* end class */
