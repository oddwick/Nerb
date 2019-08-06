<?php  /*

/**
 * Default router controller for the site which handles 
 * common page calls or uncaught page calls 
 *
 *
 * @package    		Nerb Application Framework
 * @class    		DefaultController
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


class DefaultController extends NerbController
{

	/**
	 * title
	 *
	 * This is the default value for the page title
	 * 
	 * (default value: 'Nerb Application Framework')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $title = 'Nerb Application Framework';
	
	
	
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
        
        // action calls
        if ( $this->action ) $this->action(); 
        
        $content = $this->publicPages();

        // fetch page object and add content to it
        $page = Nerb::fetch( 'Page' );

        //$page->nocache();

        $page->title( $this->title );
        $page->content( PAGES.'/'.$content );
        
        return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * The pages called here require the user to be logged in to view them
     *
     * @access         protected
     * @return         string
     */
    protected function privatePages() : string
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * The pages called here are public and can be seen by anyone
     * 
     * @access protected
     * @return void
     */
    protected function publicPages() : string
    {
        switch ( $this->page ) {
            case 'forgotPass':
            default:
                $page = 'default.php';
        }// end switch
        
        return $page;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * This is where actions are performed. a jump is performed on the completion of an action
     * 
     * @access protected
     * @return void
     */
    protected function action()
    {
        switch ( $this->action ) {
            default:
                $page = '/';
        }// end switch
        
        // jump to action endpoint
        Nerb::jump( $content );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    #####################################################################
	
    //		!USER DEFINED FUNCTIONS
    
    #####################################################################


} /* end class */
