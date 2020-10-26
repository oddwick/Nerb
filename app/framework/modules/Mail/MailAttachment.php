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
class MailAttachment extends Mail
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
    public function __construct( string $from, string $displayName, string $file)
    {
		// set params
		$this->init( $from, $displayName );

		// only set the file if the attachment is valid
		if( file_exists( $file ) ){
			$this->file = $file;
		} else {
            throw new Error( 'Attachment was not found' );
		}
		return $this;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




	/**
	 * send_with_attachment function.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function create_message() : void
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
		$this->create_headers();
		
		// if a template has been defined, merge the template data		 
		if( $this->template ){
			$this->message = $this->merge();
		}
		
		// add attachment header			 
		$this->header .= "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\""; 
		
		// create the message
		$message = "--{$mime_boundary}\n" 
				 . "Content-Type: text/html; charset=\"UTF-8\"\n" 
				 . "Content-Transfer-Encoding: base64\n\n" 
				 . chunk_split( base64_encode( $this->message ) ) 
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
		
		$this->message = $message;
		$this->returnpath = "-f " . $this->from;

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
		

} /* end class */
