<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Search
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 * @requires        ~/config.ini
 *
 */


/**
 *
 * Class for quick mail sending
 *
 */
class Mail
{

    /**
     * path to the file being attached
     * 
     * (default value: "")
     * 
     * @var string
     * @access protected
     */
    protected $file = '';

	/**
	 * path to the template used for email
	 * 
	 * @var string
	 * @access protected
	 */
	protected $template = '';

	/**
	 * data - the values for template replacement
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $data = array();

	/**
	 * headers
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access protected
	 */
	protected $headers = array(
			'MIME-Version' => '1.0',
			'To' => '',
			'From' => '',
			'Reply-To' => '',
			'X-Sender' => '',
			'X-Mailer' => 'PHP/7.2',
			'X-Priority' => '0',	
			'Content-Transfer-Encoding' => 'base64',
			'Content-Type' => 'text/html; charset=UTF-8'
	);
	
	/**
	 * header
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $header = '';

	/**
	 * params
	 * 
	 * @var array
	 * @access protected
	 */
	protected $params = array( 'to' => '',
							   'from' => '',
							   'displayName' => '',
							   'cc' => '',
							   'bcc' => '',
							   'subject' => '',
							   'returnpath' => '',
							   'message' => ''
							   );



    /**
     * __construct function.
     * 
     * @access public
     * @param string $from (default: '')
     * @param string $displayName (default: '')
     * @param string $subject (default: '')
     * @param string $content (default: '')
     * @param string $file (default: '')
     * @return Mail
     */
    public function __construct( string $from, string $displayName = '' )
    {
		// set params
		$this->from = $from;
		$this->displayName = $displayName;
		
		// setup headers
		$this->headers['From'] = $displayName ? $displayName.' <'.$from.'>' : $from;
		$this->headers['X-Mailer'] = 'PHP/'.phpversion();
		$this->headers['X-Sender'] = $this->headers['Reply-To'] = $from;

		return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     * __get function.
     * 
     * @access public
     * @param string $key
     * @return string
     */
    public function __get( string $key )
    {
        return $this->params[ $key ];
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *  setter function.
     *
     *  @access public
     *  @param string $key
     *  @param string $value
     *  @return Mail
     */
    public function __set( string $key, string $value ) : void
    {
        // error checking
        // ensure the field is a valid column
        if ( !array_key_exists( $key, $this->params ) ) {
            throw new Error( 'Key <code>'.$key.'</code> does not exist.<br /><br /><code>['.implode( ', ', $this->params ).']</code>' );
        }

        // set params key
        $this->params[$key] = $value;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * send function.
	 * 
	 * @access public
	 * @param string $to
     * @throws Error
	 * @return bool
	 */
	public function send( string $to ) : bool
	{
		// guard functions
		if( empty( $this->params['from'] ) ||
			empty( $this->params['subject'] ) 
		){
            throw new Error( 'Undefined fields <code>[from] [subject] [message]</code> can not be empty' );
		}

		//header for sender info
		$this->to  = $this->headers['To'] = $to;
		
		// build the message
		$this->create_message();
		
		// send the message
		return @mail($this->to, $this->subject, $this->message, $this->header, $this->returnpath ); 
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * send_attachment function.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function create_message()
	{
		// build header for sender info
		$this->create_headers();
		
		// set header for text html content
		$this->header .= "Content-Type: text/html; charset=\"UTF-8\"\n" 
				 . "Content-Transfer-Encoding: base64\n\n";
		
		// if a template has been defined, merge the template data		 
		if( $this->template ){
			$this->message = $this->merge();
		}
		
		// set base 64 encoding
		$this->message = chunk_split( base64_encode( $this->message ) );

		// set the return path
		$this->returnpath = "-f " . $this->from;
		
		return $this; 

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	//---------- !TEMPLATES ------------------------------------------------->
	
	/**
     * create_headers function.
     * 
     * @access protected
     * @return Mail
     */
    protected function create_headers()
    {
		// create and return basic headers
		$this->header = "To: ".$this->headers['To']."\n"
				 . "From: ".$this->headers['From']."\n"
				 . "MIME-Version: ".$this->headers['MIME-Version']."\n" 
				 . "X-Sender: ".$this->headers['X-Sender']."\n"
				 . "X-Mailer: ".$this->headers['X-Mailer']."\n"
				 . "X-Priority: ".$this->headers['X-Priority']."\n";		 
		return $this;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		



    /**
     * template function.
     * 
     * @access public
     * @param string $file
     * @param array $data
     * @return Mail
     * @throws Error
     */
    public function template( string $file )
    {
		if( !is_file( $file ) ){
            throw new Error( 'Could not find template' );
		}
		
		$this->template = $file;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * data function.  Sets the data used for the template
     * 
     * @access public
     * @param array $data
     * @return Mail
     */
    public function data( array $data )
    {
		$this->data = $data;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * merge function. Loads the template and replaces the data
     * 
     * @access protected
     * @return string
     * @throws Error
     */
    protected function merge() : string
    {
		if( !$this->template ){
            throw new Error( 'No template was defined' );
		}
		
		// transfer the data set tor use in the template
		$data = $this->data;
		
		// start output buffering to capture the template data
		ob_start();
		@include( $this->template );
		$contents = ob_get_contents();
		ob_end_clean();

		// add the buffer contents to the message
		return $contents;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * setHeader function.  Sets a header value
     * 
     * @access public
     * @param string $key
     * @param string $value
     * @return Mail
     */
    public function setHeader( string $key, string $value )
    {
		$this->headers[$key] = $value;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------









} /* end class */
