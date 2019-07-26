<?php
// Nerb Application Framework


/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @subpackage      NerbDatabase
 * @class           NerbDatabaseDebug
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 *
 */



class NerbDatabaseDebug extends NerbDatabase
{


    /**
    *   Constructor initiates database connection
    *
    *   @access     public
    *   @param      array $params connection parameters [host|user|pass|name]
    *   @return     void
    */
    public function __construct( $database_handle, $params )
    {
        // set credentials for connecting
        $this->params['connection'] = $params;

        // give this database connection a name for other classes to retrieve it
        $this->database_handle = $database_handle;

        // establish connection to the table
        $this->__connect();

        // map tables in database
        $this->tables = $this->tables();
        
        // register this database so that other classes can access it
        Nerb::register( $this, $database_handle );
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   In debugging mode, outputs poll on exit
    *
    *   @access     public
    *   @param      array $params connection parameters [host|user|pass|name]
    *   @return     void
    */
    public function __destruct()
    {
            $this->database->close();
            $this->poll(true);

    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
    *   debugging method that outputs all queries made from this object during the
    *   execution of the script
    *
    *   if $print is set to true, this method prints a formatted array of queries,
    *   otherwise this method will just return an array
    *
    *   @access     public
    *   @param      bool $print
    *   @return     array
    */
    public function poll( $print = false )
    {
        if ($print) {
            if (empty($this->query)) {
                $string = '<P>No queries have been made</P>';
            }
            $count = 0;
            print_r($this->_profile);

            foreach ($this->query as $query) {
                $string .= '<Lcode>(<code>'.$query.' [calledBy]</code>) '
                                .nl2br(Nerb_Format::tag($query['string'], 'code'));

                // additional debugging information
                if ($this->_debug) {
                    $string .= '<BR />&nbsp;&nbsp;&nbsp;<CODE>Connect time</CODE>: '.
                        number_format($this->_profile[$count]['connect']-$this->_profile[$count]['start'], 7).' ms';
                    $string .= '<BR />&nbsp;&nbsp;&nbsp;<CODE>Query time</CODE>: '.
                        number_format($this->_profile[$count]['query_time']-$this->_profile[$count]['connect'], 7).' ms';
                    $string .= '<BR />&nbsp;&nbsp;&nbsp;<CODE>Total time</CODE>: '.
                        number_format($this->_profile[$count]['query_time']-$this->_profile[$count]['start'], 7).' ms';
                    $string .= '<BR />&nbsp;&nbsp;&nbsp;<CODE>Rows</CODE>: '.$this->_profile[$count]['affected_rows'];
                    $string .= '<BR /><BR />';
                }
                $string .='</Lcode>';
                ++$count;
            }
            $string = Nerb_Format::tag($string, 'ol');
            echo '<H3>Query made to database <code>'.$this->_connection['name'].'</code>.</H3>';

            echo str_ireplace('<BR />', '<BR />&nbsp;&nbsp;&nbsp;', $string);

                // additional debugging information
            if ($this->_debug) {
                echo 'Total queries:<CODE> $count</CODE><BR />';
            }
        }

        //return the the query array
        return $this->query;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
    *   Fetches a specific query made to the database if an index is given. otherwise the last
    *   query is returned
    *
    *   @access     public
    *   @param      int $index
    *   @return     array
    */
    public function getQuery( $index = false )
    {

        if ($index) {
            //return the last element of the query array
            return $this->query[$index];
        } else {
            //return the last element of the query array
            return end($this->query);
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------

} /* end class */
