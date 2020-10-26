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
							   'body' => ''
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
     * @property from
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
    public function __set( string $key, string $value ) : Mail
    {
        // set params key
        $this->params[$key] = $value;
        
        // return old value
        return $this;
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
			empty( $this->params['subject'] ) ||
			empty( $this->params['body'] )
		){
            throw new Error( 'Undefined fields <code>[from] [subject] [body]</code> can not be empty' );
		}

		//header for sender info
		$this->to = $to;

		//attach a file
		if( !empty( $this->file ) && file_exists( $this->file ) ){
			//headers for attachment 
			echo $this->send_with_attachment();
		} else {
			echo $this->send_html();
		}

		return true;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * send_attachment function.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function send_html()
	{
		//header for sender info
		$headers = $this->create_headers();
		
		$headers .= "Content-Type: text/html; charset=\"UTF-8\"\n" 
				 . "Content-Transfer-Encoding: base64\n\n";
		
		// set base 64 encoding
		$message = chunk_split( base64_encode( $this->body ) );

		// set the return path
		$returnpath = "-f" . $this->from;
		
		//send email
		return mail($this->to, $this->subject, $message, $headers, $returnpath ); 

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	
	/**
	 * send_with_attachment function.
	 * 
	 * @access protected
	 * @return bool
	 */
	protected function send_with_attachment() : bool
	{
		//boundary 
		$semi_rand = md5(time()); 
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
		
        // open and read file
        $handle =    @fopen( $this->file, "rb" );
        $data =  @fread( $handle, filesize( $this->file ) );
        @fclose( $handle );
        $data = chunk_split( base64_encode( $data ) );
		
		// create headers
		$headers = $this->create_headers();
		// add attachment header			 
		$headers .= "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\""; 
		
		// create the message
		$message = "--{$mime_boundary}\n" 
				 . "Content-Type: text/html; charset=\"UTF-8\"\n" 
				 . "Content-Transfer-Encoding: base64\n\n" 
				 . chunk_split( base64_encode( $this->body ) ) 
				 . "\n\n"; 
		
        // add attachment
        $message .= "--{$mime_boundary}\n";
        $message .= "Content-Type: ".mime_content_type ( $this->file )."; name=\"".basename($this->file)."\"\n" 
        		 . "Content-Description: ".basename($this->file)."\n" 
        		 . "Content-Disposition: attachment;\n" . " filename=\"".basename($this->file)."\"; size=".filesize($this->file).";\n" 
        		 . "Content-Transfer-Encoding: base64\n" 
				 . "X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n"
        		 . $data."\n\n";

		$message .= "--{$mime_boundary}--";
		$returnpath = "-f" . $this->from;
		
		//send email
		return mail($this->to, $this->subject, $message, $headers, $returnpath); 
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		



    /**
     * create_headers function.
     * 
     * @access protected
     * @return string
     */
    protected function create_headers( ) : string
    {
		// create and return basic headers
		$headers = "To: ".$this->headers['To']."\n"
				 . "From: ".$this->headers['From']."\n"
				 . "MIME-Version: ".$this->headers['MIME-Version']."\n" 
				 . "X-Sender: ".$this->headers['X-Sender']."\n"
				 . "X-Mailer: ".$this->headers['X-Mailer']."\n"
				 . "X-Priority: ".$this->headers['X-Priority']."\n";		 
		return $headers;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		



    /**
     * template function.
     * 
     * @access public
     * @param string $file
     * @param array $data
     * @return void
     */
    public function template( string $file, array $data )
    {
		if( !is_file( $file ) ){
            throw new Error( 'Could not find template' );
		}
		
		ob_start();
		@include( $file );
		$contents = ob_get_contents();
		ob_end_clean();

		$this->body = $contents;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * from function.
     * 
     * @access public
     * @param string $from
     * @return void
     */
    public function from( string $from )
    {
		$this->from = $from;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * subject function.
     * 
     * @access public
     * @param string $subject
     * @return Mail
     */
    public function subject( string $subject )
    {
		$this->subject = $subject;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * content function.
     * 
     * @access public
     * @param string $content
     * @return Mail
     */
    public function body( string $body )
    {
		$this->body = $body;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * cc function.
     * 
     * @access public
     * @param string $cc
     * @return Mail
     */
    public function cc( string $cc )
    {
		$this->cc = $cc;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * bcc function.
     * 
     * @access public
     * @param string $bcc
     * @return Mail
     */
    public function bcc( string $bcc )
    {
		$this->bcc = $bcc;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    
    
    /**
     * attach function.
     * 
     * @access public
     * @param string $file
     * @return Mail
     */
    public function setHeader( string $key, string $value )
    {
		$this->headers[$key] = $value;
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * attach function.
     * 
     * @access public
     * @param string $file
     * @return Mail
     */
    public function attach( string $file )
    {
		// only set the file if the attachment is valid
		if( file_exists($file) ){
			$this->file = $file;
		}
		return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------







} /* end class */
