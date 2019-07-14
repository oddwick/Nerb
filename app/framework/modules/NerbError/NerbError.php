<?php
// Nerb Application Framework

/**
 * Extends php Exception for Nerb specific error messages
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbError
 * @version             1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright ( c )2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */


class NerbError extends \Exception
{

    /**
     * trace
     *
     * ( default value: array() )
     *
     * @var array
     * @access protected
     */
    protected $trace = array();
    
    /**
     * message
     *
     * ( default value: '' )
     *
     * @var string
     * @access protected
     */
    protected $message = '';
    
    /**
     * content
     * 
     * ( default value: array( 
     *     	'title' => 'Nerb Application Error',
     * 		'css' => '',
     * 		'js' => '',
     * 		'content' => ''
     * 		 ) )
     * 
     * @var string
     * @access protected
     */
    protected $content = array( 
    	'title' => 'Nerb Application Error',
		'css' => '',
		'js' => '',
		'content' => ''
	);





	/**
     *  Constructor for Error class
     *
     *  @access     public
     *  @param      string $message error message
     *  @return     void
     */
    public function __construct( string $message, array $trace = array() )
    {

        // fire the constructor for the default error class
        parent::__construct( $message, $code );
        
        // trip error for logging purposes
		trigger_error( strip_tags( $message ), E_USER_ERROR );

        // sets the error message
        $this->message = $message;
        
        // cleans out the paths from the message if set
        
        if( !SHOW_FULL_PATH ){
        	$this->message = str_replace( APP_PATH, '..', $this->message );
		}
        // for verbose errors,
        // ERROR_LEVEL is set in the Nerb_conf file.

        //gets the trace data that lead up to the error
        $this->trace = $trace ? $trace : debug_backtrace();
        
        //array_shift ( $this->trace );
        $this->trace = array_reverse( $this->trace );

        // set page elements
        // include the default nerb framework css sheet wrapped in a style tag
        // for inserting inline into the header
        $this->content['css'] = '<style type="text/css">'.file_get_contents( FRAMEWORK.'/resources/nerb.css' ).'</style>';

        // renders the error
        echo $this->render();

        // mic drop - and we're outta here...
        die();
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
     *  returns the error msg as a string
     *
     *  @access     protected
     *  @return     string
     *  @todo       create link to help manual
     */
    public function render(): string
    {
        // stop all outputbuffering and clear contents so hopefully the error is displayed on a clean page
        ob_end_clean();

        //  include the template and inject the content
        if ( file_exists( FRAMEWORK.'/resources/template.php' ) ) {
            // Starts output buffering
            ob_start();

            //Extracts vars to current view scope
            extract( $this->content );

            // builds the error body block
            $content = $this->header();
            $content .= $this->error();
            $content .= $this->trace();
            $content .= $this->footer();

            // Includes the template contents
            include FRAMEWORK.'/resources/template.php';

            // fetch, kill, and return the output buffer
            $buffer = ob_get_contents();
            ob_end_clean();
            return $buffer;
        } else {
            echo 'Holy shit, what the <em>FUCK</em> did you do?!?';
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
     *  returns formatted error message
     *
     *  @access     protected
     *  @return     string
     */
    protected function error(): string
    {
        // encapsulate and return error message
        $error = '<h2>Details</h2><p>'.$this->message.'</p>';
        return $error;
        
    }// end function




	/**
     *  returns the formatted backtrace for the error
     *
     *  @access     protected
     *  @return     string
     *
     */
    protected function trace(): string
    {

        switch ( ERROR_LEVEL ) {
            case 2:
                $count = 1;
                $trace = '<h2>Trace</h2>'
                    . '<code><ul class="no-bullet">'
                    . '<li>#0: {INIT}</li>';

                foreach ( $this->trace as $node ) {
                    $trace .= '<li>#'.$count++.':&nbsp;'.str_replace( APP_PATH, '', $node['file'] ).' ( '.$node['line'].' )';

                    //adds a suffix until the last line call to preven NerbError->__construct() from being displayed
                    if ( $count <= count( $this->trace ) ) {
                        $trace .= ' &mdash; '.$node['class'].$node['type'].$node['function'].'()</li>';
                    } else {
                        $trace .= '<strong><- ERROR</strong>';
                    }// end if
                }

                $trace .= '</ul></code>';
                break;

            case 1:
                $node = end( $this->trace );
                $trace = '<h2>Trace</h2>'
                    . '<p>'
                    . $node['class'].$node['type'].$node['function'].'() &mdash; <em>'.str_replace( APP_PATH, '', $node['file'] ).' on line <strong>'.$node['line'].'</strong></em>'
                    . '</p>';
                break;

            default:
                $trace = '<br />';
        }

        return $trace;
        
    } // end function




	/**
     *  creates a header and title for an error
     *
     *  @access     protected
     *  @return     string
     *
     */
    protected function header():string
    {
        return  '<header>'
            . '<h1><i class="material-icons md-dark md-48">error</i> Nerb Application Error</h1>'
            . '<p>This application has been terminated because of the following error:</p>'
            . '</header>';
            
    }// end function




	/**
     *  generates a footer block for an error
     *
     *  @access     protected
     *  @return     string
     *
     */
    protected function footer(): string
    {
        // footer
        $footer = '<footer>'
        . '<p><strong>'.SOFTWARE.' v'.VERSION.'</strong></p>'
        . '<p><em>'.COPYRIGHT.'</em>&nbsp;&nbsp;&nbsp;<a href="https://www.github.com/oddwick/nerb" target="_blank">https://www.github.com/oddwick/nerb</a></p>'
        . '</footer>';
        return $footer;
        
    }// end function
    
    
    
    /**
    *   creates a debug object for syntax checking
    *
    *   @access     private
    *   @param      string $class ( class being evaluated )
    *   @param      string $function ( name of specific function )
    *   @return     string
    *   @see        Debug
    *   @throws     NerbError
    */
    private function syntax( $class, $method = null )
    {

        if ( is_object( $class ) ) {
            $class = get_class( $class );
        }

        if ( !class_exists( $class ) ) {
            throw new NerbError( 'Class <code>['.$class.']</code> has not been defined' );
        }

        if ( $method && !method_exists( $class, $method ) ) {
            throw new NerbError( 'Method <code>['.$method.']</code> does not exist in class <code>['.$class.']</code>' );
        }

        Nerb::loadClass( 'NerbDebug' );
        $debug = new NerbDebug;
        return $debug->syntax( $class, $method );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
    
} /* end class */
