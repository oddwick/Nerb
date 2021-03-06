<?php
// Nerb Application Framework
namespace nerb\framework;

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


class DefaultController extends Controller
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
     *   @access public
     *   @return self
     */
    public function route() : self
    {
        // define page structure for the controller
        $this->url->defineStructure( array( 'page') );
        
        // action calls
        if ( $this->url->action() ) {
            $this->action();
        }
        
        $content = $this->publicPages();

        // fetch page object and add content to it
        $page = Nerb::registry()->fetch( 'Page' );

        //$page->nocache();

        $page->title( $this->title );
        $page->content( PAGES.'/header.php');
        $page->content( PAGES.'/'.$content );
        $page->content( PAGES.'/footer.php');
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
/*
       switch ( $this->page ?? $this->url->node(0) ) {
	        
            case '':
            	
            default:
                $page = 'default.php';
        }// end switch
        
        return $page;
*/
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
       switch ( $this->page ?? $this->url->node(0) ) {
	        
            case 'privacy':
            	$page = '/default/privacy.php';
            	break;
            	
            case 'terms':
            	$page = '/default/terms.php';
            	break;
            	
            default:
                $page = 'default.php';
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
            default:
                $page = '/';
        }// end switch
        
        // jump to action endpoint
        Core::jump($page);
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    #####################################################################
	
    //		!USER DEFINED FUNCTIONS
    
    #####################################################################


} /* end class */
