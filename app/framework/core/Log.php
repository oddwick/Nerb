<?php
// Nerb Application Framework
namespace nerb\framework;

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * Simple but powerful logging class for maintaining copies of errors, and access etc.
 * this class is required by Error
 *
 *
 *
 * @category        Nerb
 * @package         Nerb
 * @class           Log
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @license         https://www.github.com/oddwick/nerb
 *
 * @todo
 * @requires        Error
 * @requires        ~/config.ini
 *
 */


/**
 *
 * Base class for generating site framework
 *
 */
class Log
{
    
    /**
     * log_file
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $log_file = '';



    /**
     * __construct function
     *
     * @access protected
     * @param string $log_file
     * @throws Error
     * @return void
     */
    public function __construct( string $log_file )
    {
        // pass name to class
        $this->log_file = $log_file;

        // if file does not exist, try and create it with a header
        if( !file_exists( $log_file ) ) 
            $this->init();
			
        // if backups are enabled and log size exceeds backup, then perform backup
        if( BACKUP_LOG_AFTER > 0 && filesize( $log_file )/1024 > BACKUP_LOG_AFTER )
            $this->backupLogFile();
		
        return;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * init function.
     * 
     * Creates a blank log file with a simple header
     * 
     * @access protected
     * @param string $log_file
     * @throws Error
     * @return bool
     */
    protected function init() : bool
    {
        // create a simple header for log files
        $status = file_put_contents ( $this->log_file , 'Created by Nerb Application Framework v'.VERSION.' (build '.BUILD.')'.PHP_EOL.PHP_EOL , LOCK_EX );
        if ( !$status ){
            Core::halt( 'Unable to initialize log file <code>['.$this->log_file.']</code>');
        } 
		
        return $status;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * backupLogfile function.
     *
     * this will backup the log file and append _YYYYMMDD_HHMMSS.bak extension to the file in the
     * same directory of the log file, and init() an clean log file.
     *
     * the following constants are set in the config.ini and determine if, when, and how the log is backed up
     *
     * BACKUP_LOG_AFTER - size in Kb for the log to be backed up.  -1 means the log is never backed up
     * COMPRESS_LOG_BACKUP - if set to true, then the log file is gzipped after backup
     * 
     * @access protected
     * @param string $log_file
     * @return void
     */
    protected function backupLogfile() : bool
    {
        // break the $log_file up into path parts
        $file = pathinfo( $this->log_file );
		
        // if extension exists, add a period to the end to prevent double periods in the extensions
        if( isset( $file['extension'] )) $file['extension'] .= '.';
		
        $filename = $file['dirname'].'/'.$file['filename'].'_'.date("Ymd_His", time()).'.'.$file['extension'].( COMPRESS_LOG_BACKUP ? 'gz' : 'bak' );
		
        // rename the log file
        $status = @rename( $this->log_file, $filename );
        if( !$status ){
	        Core::halt('Could not move log file <code>['.$this->log_file.']</code>');
		}
			 
        // if backup is compressed then reopen the file and compress it
        if( COMPRESS_LOG_BACKUP ){
            $status = @file_put_contents("compress.zlib://$filename", file_get_contents($filename));
            if( !$status ){
	            Core::halt('Error compressing backup log. Make sure your permissions are properly set');
	        }
        }
		
        // initialize a new backup with the name of the original
        $this->init();
		
        return true;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * write function.
     * 
     * simple method for adding to log files
     *
     * @access public
     * @static
     * @param string $message
     * @param string $prefix (default: null)
     * @return bool
     */
    public function write( string $message, string $prefix = null ) : bool
    {
        // clean up message
        $message = html_entity_decode( strip_tags( $message ));
		
        // create timestamp object with microtime
        $micro = microtime( true );
        $formated = sprintf( '%06d',( $micro - floor( $micro )) * 1000000 );
        $timestamp = new \DateTime( date( 'Y-m-d H:i:s.'. $formated , $micro ) );
		
        // build entry string with prefix if given
        $entry = ( $prefix ? $prefix.' ' : null ).'['.$timestamp->format( 'D M d Y H:i:s.u' ).'] '.$message.PHP_EOL;
		
        // append contents to file
        $status = file_put_contents ( $this->log_file , $entry , FILE_APPEND | LOCK_EX );
        if ( !$status ) 
            Core::halt( 'Unable to add entry to log');
		
        return $status;
			
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


	
	
    /**
     * download function.
     * 
     * @access public
     * @return void
     */
    public function download()
    {
        // send headers and read file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename( $this->log_file ).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize( $this->log_file ) );
        echo file_get_contents( $this->log_file );
        exit;
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * readLog function.
     * 
     * this is reads the log file as a string.  
     * if $lines is set, then it only returns a specific number of lines
     * 
     * 
     * @access public
     * @param int $lines (default: 0) -  number of lines to return. 0 returns the whole file
     * @param bool $eof (default: true) - whether to read from the end of the file (true) or beginning (false)
     * @return string
     */
    public function readLog( int $lines = 0, $eof = true  ) : string
    {
        // if a number of lines are specified, then only return that many
        if ( $lines ) {
            // get file as array
            $data = file( $this->log_file, FILE_IGNORE_NEW_LINES );
			
            // determine where the first line is
            $start = $eof ? count( $data ) - $lines - 1 : 0;
			
            // loop through and aggregate data and return
            for( $i = $start; $i < $start + $lines; $i++ ){
                $return .= $data[$i].PHP_EOL;
            }
            return $return;
        // check to make sure the logfile exists return the full file
        } else {
            return file_get_contents($this->log_file);
        }
		
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

	
	
	
    /**
     * getLastEntry function.
     * 
     * @access public
     * @return string
     */
    public function getLastEntry() : string
    {
        // return last line of the log file
        return end( file( $this->log_file, FILE_IGNORE_NEW_LINES ) );	
	
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
