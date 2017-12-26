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
 * @copyright       Copyright (c)2017
 * @license         https://www.oddwick.com
 *
 * @todo
 *
 */


class NerbError extends \Exception
{

    /**
     * _trace
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $_trace = array();
    
    /**
     * _msg
     *
     * (default value: "")
     *
     * @var string
     * @access protected
     */
    protected $_msg = "";
    
    /**
     * _content
     * 
     * (default value: array( 
     *     	"title" => "Nerb Application Error",
     * 		"css" => "",
     * 		"js" => "",
     * 		"content" => ""
     * 		))
     * 
     * @var string
     * @access protected
     */
    protected $_content = array( 
    	"title" => "Nerb Application Error",
		"css" => "",
		"js" => "",
		"content" => ""
		);





	/**
     *  Constructor for Error class
     *
     *  @access     public
     *  @param      string $msg error message
     *  @return     void
     */
    public function __construct($msg)
    {

        // fire the constructor for the default error class
        parent::__construct($msg, $code);

        // sets the error message
        $this->_msg = $msg;

        // for verbose errors,
        // ERROR_LEVEL is set in the Nerb_conf file.

        //gets the trace data that lead up to the error
        $this->_trace = debug_backtrace();
        //array_shift ( $this->_trace );
        $this->_trace = array_reverse($this->_trace);

        // set page elements
        // include the default nerb framework css sheet wrapped in a style tag
        // for inserting inline into the header
        $this->_content["css"] = "<style type='text/css'>".file_get_contents(FRAMEWORK."/resources/nerb.css")."</style>";

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
        if (file_exists(FRAMEWORK."/resources/template.php")) {
            // Starts output buffering
            ob_start();

            //Extracts vars to current view scope
            extract($this->_content);

            // builds the error body block
            $content = $this->_header();
            $content .=  $this->_error();
            $content .= $this->_trace();
            $content .= $this->_footer();

            // Includes the template contents
            include FRAMEWORK."/resources/template.php";

            // fetch, kill, and return the output buffer
            $buffer = ob_get_contents();
            ob_end_clean();
            return $buffer;
        } else {
            echo "Holy shit, what the <em>FUCK</em> did you do?!?";
        }
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





	/**
     *  returns formatted error message
     *
     *  @access     protected
     *  @return     string
     */
    protected function _error(): string
    {
        // encapsulate and return error message
        $error = "<h2>Details</h2>"
           . "<p>".$this->_msg."</p>";
        return $error;
        
    }// end function





	/**
     *  returns the formatted backtrace for the error
     *
     *  @access     protected
     *  @return     string
     *
     */
    protected function _trace(): string
    {

        switch (ERROR_LEVEL) {
            case 2:
                $count = 1;
                $trace = "<h2>Trace</h2>"
                    . "<code><ul class='no-bullet'>"
                    . "<li>//0: {INIT}</li>";

                foreach ($this->_trace as $node) {
                    $trace .= "<li>//".$count++.":&nbsp;".str_replace(APP_PATH, "", $node['file'])." (".$node['line'].")";

                    //adds a suffix until the last line call to preven NerbError->__construct() from being displayed
                    if ($count <= count($this->_trace)) {
                        $trace .= " &mdash; ".$node['class'].$node['type'].$node['function']."()</li>";
                    } else {
                        $trace .= "<- error";
                    }// end if
                }

                $trace .= '</ul></code>';
                break;

            case 1:
                $node = end($this->_trace);
                $trace = "<h2>Trace</h2>"
                    . "<p>"
                    . $node['class'].$node['type'].$node['function']."() &mdash; <em>".str_replace(APP_PATH, "", $node['file'])." on line <strong>".$node['line']."</strong></em>"
                    . '</p>';
                break;

            default:
                $trace = "<br />";
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
    protected function _header():string
    {
        return  "<header>"
            . "<h1><i class='material-icons md-dark md-48'>error</i> Nerb Application Error</h1>"
            . "<p>This application has been terminated because of the following error:</p>"
            . "</header>";
            
    }// end function





	/**
     *  generates a footer block for an error
     *
     *  @access     protected
     *  @return     string
     *
     */
    protected function _footer(): string
    {
        // footer
        $footer = "<footer>"
        . "<p><strong>".SOFTWARE." v".VERSION."</strong></p>"
        . "<p><em>".COPYRIGHT."</em>&nbsp;&nbsp;&nbsp;<a href='https://www.github.com/oddwick/nerb' target='_blank'>https://www.github.com/oddwick/nerb</a></p>"
        . "</footer>";
        return $footer;
        
    }// end function
    
    
    
    /**
    *   creates a debug object for syntax checking
    *
    *   @access     private
    *   @param      string $class (class being evaluated)
    *   @param      string $function (name of specific function)
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
            throw new NerbError( "Class '<code>".$class."</code>'has not been defined" );
        }

        if ( $method && !method_exists( $class, $method ) ) {
            throw new NerbError( "Method '<code>".$method."</code>'does not exist in class '<code>".$class."</code>'" );
        }

        Nerb::loadClass( "NerbDebug" );
        $debug = new NerbDebug;
        return $debug->syntax( $class, $method );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
    
} /* end class */
