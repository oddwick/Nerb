<?php  /*

/**
 *	Base class for the Stamp album professional that generates high quality pdf
 *	pages for stamp albums
 *
 *
 * @package    		Stamp Album Pro Admin
 * @class    		DefaultController
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


class DefaultController extends NerbController
{

	/**
	 * title
	 *
	 * This is the default value for the page title
	 * 
	 * (default value: 'Open Source Philately')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $title = 'Open Source Philately';
	
	
	
    /**
    *   Container function for executing domain logic for this module
    *
    *   @access		public
    */
    public function route()
    {

        
        // this is a public controller
        
        $title = '';
        
        $this->defineStructure( array( 'page' ));
        
        //Nerb::inspect( $this->params, true );
        // the new terms
        if ( $this->action ) $this->action(); 
        
                
		// fetch user object
		$user = Nerb::fetch( 'user' );
		
		// if user is logged in, allow access to private sections, 
		// otherwise kick out to registration page
/*
		if( !$user->verify() ) 
		{
            $content = $this->publicPages();
        } else {
            $nav = true;
            $content = $this->privatePages();
        }
*/
        $content = $this->publicPages();

        // fetch page object and add content to it
        $page = Nerb::fetch( 'Page' );
        $page->title( $this->title );
        $page->contentHeader( PAGES.'/header.php' );
        $page->contentFooter( PAGES.'/footer.php' );
        $page->content( PAGES.'/'.$content );
        
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * The pages called here require the user to be logged in to view them
     *
     * @access         protected
     * @return         string
     */
    protected function privatePages()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * The pages called here are public and can be seen by anyone
     * 
     * @access protected
     * @return void
     */
    protected function publicPages(): string
    {
        switch ( $this->page ) {
            case 'forgotPass':
            default:
                $page = 'default.php';
        }// end switch
        return $page;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * The pages called here are public and can be seen by anyone
     * 
     * @access protected
     * @return void
     */
    protected function action()
    {
        switch ( $this->action ) {
            case 'login':
		        $page = $this->login($_POST['user_name'], $_POST['user_pass']);
				break;
            
            
            case 'logout':
		        $this->logout();
            
            default:
                $page = '/';
        }// end switch
        
        // jump to action endpoint
        Nerb::jump( $content );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * login function.
	 * 
	 * @access protected
	 * @param mixed $user_name
	 * @param mixed $user_pass
	 * @return void
	 */
	protected function login( string $user_name, string $user_pass ): string
	{
		
		$user = Nerb::fetch( 'user' );
		
		// authenticate user
		$status =  $user->authenticate( $user_name, $user_pass );
		
		// sucessful authentication
		if( $status[0] == true ){
			$page = '/?msg='.urlencode( 'Welcome Back' );
		} 
		
		// failed authentication
		else {
			$page = $this->return_page.'?error='.urlencode( $status[1] );
		}
		
		return $page;	
				
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	*	logs the user out by destroying their session
	*
	*	@access		protected
	*	@return		string
	*/
	public function logout(): string
	{
		$user = Nerb::fetch( 'user' );
		$user->destroySession();
		session_unset($_SESSION);
		return( '/?msg=You+have+been+logged+out' );
		
	}// end function		


} /* end class */
