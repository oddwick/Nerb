<?php  /*

/**
 *	Base class for the Stamp album professional that generates high quality pdf
 *	pages for stamp albums
 *
 *
 * @package    		Stamp Album Pro Admin
 * @class    		DefaultController
 * @extends    		NerbRouterController
 * @version			1.0
 * @author			Dexter Oddwick <dexter@oddwick.com>
 * @copyright  		Copyright ( c )2017
 * @license    		http://www.oddwick.com
 *
 *
 * @todo
 *
 */


class DefaultController extends NerbRouterController
{

        /**
        *   Container function for executing domain logic for this module
        *
        *   @access         public
        */
    protected function NerbRouterController()
    {

        // the new terms
        if ( $this->params["action"] == "login" ) {
            $page = $this->login( $_POST['user_name'], $_POST['user_pass'] );
            Nerb::jump( $page );
        } elseif ( !$this->isLogged() ) {
            $noNav = true;
            $page = $this->publicPages();
        } else {
            $page = $this->privatePages();
        }
        include PAGES."/header.php";
        include PAGES."/".$page;
        include PAGES."/footer.php";
    }// end function



    /**
    *   The pages called here private require the user to be logged in to view them
    *
    *   @access         protected
    *   @return         string
    */
    protected function privatePages()
    {

        switch ( $this->params["page"] ) {

            case 'page':
            	$page = "page.php"
            	break
            	
            default:
                $page = "default_private_page.php";
                
        }// end switch
        
        
        return $page;
        
    }// end function



    /**
    *   The pages called here are public and can be seen by anyone
    *
    *   @access         protected
    *   @return         string
    */
    protected function publicPages()
    {
        switch ( $this->action ) {
            case 'page':
            	$page = "page.php"
            	break
            	
            default:
                $page = "default_public_page.php";
                
        }// end switch
        
        // return the public page
        return $page;
        
    }// end function




    /**
    *   This is where actions are performed and are terminated with a jump
    *
    *   @access         protected
    *   @return         string
    */
    protected function actions()
    {
        switch ( $this->action ) {
            case 'page':
            	$page = "page.php"
            	break
            	
            // the default fallback action
            default:
                $page = "default_public_page.php";
                
        }// end switch
        
        // return the public page
        Nerb::jump( $page );
        
    }// end function




	// contoller logic methods goes here



} /* end class */
