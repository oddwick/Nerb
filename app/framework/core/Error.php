<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Extends php Exception for Nerb specific error messages
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Error
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 *
 * @todo
 *
 */


class Error extends \Exception
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
     * @var array
     * @access protected
     */
    protected $content = array( 
        'title' => 'Nerb Application Error',
        'css' => '',
        'js' => '',
        'content' => ''
    );



    /**
     * __construct function.
     * 
     * Constructor for Error class
     *
     * @access public
     * @param string $message
     * @param array $trace (default: array())
     * @return void
     */
    public function __construct( string $message, array $trace = array() )
    {
		//die;
        // fire the constructor for the default error class
        parent::__construct( $message );
        //trigger_error( strip_tags( $message ), E_USER_ERROR );
        
        // trip error for logging purposes

        // sets the error message
        $this->message = $message;
        
        // for verbose errors,
        // ERROR_LEVEL is set in the Nerb_conf file.

        //gets the trace data that lead up to the error
        $this->trace = $trace ? $trace : $this->getTrace();
        //$this->trace = $trace ? $trace : debug_backtrace();
        
        //array_shift ( $this->trace );
        $this->trace = array_reverse( $this->trace );

        // cleans out the paths from the message if set
        if( !SHOW_FULL_PATH ){
            $this->message = $this->cleanPath( $this->message );
        }
		
        $this->log( $this->trace[0], $this->message);
        
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
     * render function.
     * 
     * returns the error msg as a string
     *
     * @access     protected
     * @return     string
     * @todo       create link to help manual
     */
    public function render() : string
    {
        // stop all outputbuffering and clear contents so hopefully the error is displayed on a clean page
        ob_end_clean();

        //  include the template and inject the content
        if ( !file_exists(FRAMEWORK.'/resources/template.php')) {
			return 'Holy shit, what the <em>FUCK</em> did you do?!?';
		}
       
        // Starts output buffering
        ob_start();

        //Extracts vars to current view scope
        extract( $this->content );

        // builds the error body block
        $content = $this->header();
        $content .= $this->msg();
        $content .= $this->code();
        $content .= $this->trace();
        $content .= $this->footer();

        // Includes the template contents
        include FRAMEWORK.'/resources/template.php';

        // fetch, kill, and return the output buffer
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * error function.
     * 
     * returns formatted error message
     *
     * @access     protected
     * @return     string
     */
    protected function msg() : string
    {
        // encapsulate and return error message
        return '<h2>Message</h2><p>'.$this->message.'</p>';        
        
    }// end function




    /**
     * log function.
     * 
     * @access protected
     * @param array $trace
     * @param string $msg
     * @param string $prefix (default: 'ERROR')
     * @return void
     */
    protected function log( array $trace, string $msg, string $prefix = 'ERROR' )
	{
        // create error string
        $error = $this->cleanPath($trace['file']) . ' (' . $trace['line'] . ') -- ' .$msg;
        
        // log error to file
        ClassManager::loadClass( 'Log' );
        
        // WARNING | ERROR | NOTICE [date] file (line) string
        $log = new Log( ERROR_LOG );
        $log->write( $error , $prefix );
			

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     * code function.
     * 
     * returns formatted  code
     *
     * @access     protected
     * @return     string
     */
    protected function code() : string
    {
        // if show error line is true, then display the 
        // actual line that caused the error
        $error = '<h2>Details</h2>';
        	
        foreach( $this->trace as $trace){
            // read offending file into an array
            $file = file( $trace['file'], FILE_IGNORE_NEW_LINES );
        	
            // because if an error was thrown, technically it is the previous line
            // that caused the error, so this only shows code line if it is not a 
            // thrown error.  makes the code list a little easier to see real error
            if( !stristr( $file[ $trace['line']-1 ], "throw" ) ){
                $error .= '<p>'.end(preg_split('/\//', $trace['file'])).' ('.$trace['line'].')<br />';
                $error .= '<code>'.$file[ $trace['line']-1 ].'</code></p>';
            }
            // housekeeping for large array
            unset( $file );
        }// end foreach
        
        return $error;
        
    }// end function



    /**
     * trace function.
     * 
     * returns the formatted backtrace for the error
     *
     * @access     protected
     * @return     string
     */
    protected function trace() : string
    {
        switch (ERROR_LEVEL) {
            case 2:
                $count = 1;
                $trace = '<h2>Trace</h2>'
                    . '<code><ul class="no-bullet">'
                    . '<li>#0: {INIT}</li>';

                foreach ($this->trace as $node) {
                    $trace .= '<li>#'.$count++.':&nbsp;'.$this->cleanPath($node['file']).' ( '.$node['line'].' )';

                    //adds a suffix until the last line call to prevent Error->__construct() from being displayed
                    if ($count <= count($this->trace)) {
                        $trace .= ' &mdash; '.$node['class'].$node['type'].$node['function'].'()</li>';
                    } else {
                        $trace .= '<strong><- ERROR</strong>';
                    }// end if
                }

                $trace .= '</ul></code>';
                break;

            case 1:
                $node = end($this->trace);
                $trace = '<h2>Trace</h2>'
                    . '<p>'
                    . $node['class'].$node['type'].$node['function'].'() &mdash; <em>'.$this->cleanPath($node['file']).' on line <strong>'.$node['line'].'</strong></em>'
                    . '</p>';
                break;

            default:
                $trace = '<br />';
        }

        return $trace;
        
    } // end function




    /**
     * cleanPath function.
     * 
     * @access protected
     * @param string $path
     * @return string
     */
    protected function cleanPath($path) : string
    {
        return  str_replace(APP_PATH, '..', $path);
                    
    }// end function




    /**
     * header function.
     * 
     * creates a header and title for an error
     *
     * @access     protected
     * @return     string
     */
    protected function header() : string
    {
        return  '<header>'
            . '<h1><i class="material-icons md-dark md-48">error</i> Nerb Application Error</h1>'
            . '<p>This application has been terminated because of the following error:</p>'
            . '</header>';
            
    }// end function




    /**
     * footer function.
     * 
     * generates a footer block for an error
     *
     * @access     protected
     * @return     string
     */
    protected function footer() : string
    {
        // footer
        $footer = '<footer>'
        . '<p><strong>'.SOFTWARE.' v'.VERSION.' (build '.BUILD.')</strong></p>'
        . '<p><em>'.COPYRIGHT.'</em>&nbsp;&nbsp;&nbsp;<a href="'.GIT.'" target="_blank">Github</a> | <a href="'.SCRUTINIZER.'" target="_blank">Scrutinizer</a></p>'
        . '</footer>';
        return $footer;
        
    }// end function
    
        
} /* end class */
