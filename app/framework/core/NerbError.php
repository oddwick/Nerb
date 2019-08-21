<?php
// Nerb Application Framework
Namespace nerb\framework;

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
        $error = $this->cleanPath($trace['file']) . ' (' . $trace['line'] . ') -- ' .$msg.' --  nerb error';
        
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
        if( SHOW_ERROR_LINE && ERROR_LEVEL > 0 ){
            $error .= '<h2>Details</h2>';
        }
        	
        if( ERROR_LEVEL == 1 ){
            $trace = end( /** @scrutinizer ignore-type */ $this->trace );
            $file = file( $trace['file'], FILE_IGNORE_NEW_LINES );
            $error .= '<p>'.end(preg_split('/\//', $trace['file'])).' ('.$trace['line'].')<br />';
            $error .= '<code>'.$file[ $trace['line']-1 ].'</code></p>';
            // housekeeping for large array
            unset( $file );
		 	
        } 
        
        if ( ERROR_LEVEL == 2){
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
        }
        
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

                    //adds a suffix until the last line call to prevent NerbError->__construct() from being displayed
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
     * formatTrace function.
     *
     * takes a raw trace array and formats it into php exception class trace
     * 
     * @access public
     * @static
     * @param array $trace
     * @return array
     */
    public static function format( array $error ) : array
    {
        // get error type name in human readable form
        $error['type_name'] = array_search( $error['type'], get_defined_constants() );
	    
        // seperate the trace from the message body
        $msg = preg_split( '/Stack trace:/' , $error['message'] );
	    
        // break the trace apart using line numbers (#0 etc)
        $raw = preg_split( '/\#([0-9]) /' , $msg[1], NULL, PREG_SPLIT_NO_EMPTY );
		
        // add the error line and file to the end of the trace
        array_unshift( /** @scrutinizer ignore-type */ $raw, $error['file'].' ('.$error['line'].'):'); 	
		
        // loop through, trim, and kill null values or messages that contain brackets eg. {main} 	    
        foreach( $raw as $key => $value ){
            $raw[ $key ] = trim( $raw[ $key ] );
            if( empty( $raw[$key] ) || strstr($value ,'{' ) ) unset( $raw[$key] );
        }
	    
        // reindex array
        $raw = array_values( $raw );
        $count = 0;
	    
        foreach( $raw as $value ){
		    
            //$value = str_replace(' ', '~', $value);
		    
            $hold = explode( ':', $value) ;
            $file = explode( '(', $hold[0] );
		    
            $trace[$count]['file'] = trim( $file[0] );
            $trace[$count]['line'] = str_replace(')', '', $file[1]);

            // kill params
            $hold[1] = preg_replace( "/\(.*\)/", "", $hold[1] );
            $plit = explode( '->', $hold[1] );
            if( count( $plit ) < 2 ){
                $trace[ $count ]['function'] = $plit[0];
            } else {
                $trace[ $count ]['class'] = trim( $plit[0] );
                $trace[ $count ]['function'] = trim( $plit[1] );
            }
            $count++;
        } // end foreach

        // transfer variables back to $error
        $error['message'] = $msg[0];
        $error['trace'] = $trace;

        return $error;
        
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
        . '<p><em>'.COPYRIGHT.'</em>&nbsp;&nbsp;&nbsp;<a href="https://www.github.com/oddwick/nerb" target="_blank">https://www.github.com/oddwick/nerb</a></p>'
        . '</footer>';
        return $footer;
        
    }// end function
    
        
} /* end class */
