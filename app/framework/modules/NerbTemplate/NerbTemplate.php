<?php

/**
 * Nerb System Framework
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * @category        Nerb
 * @package         Nerb
 * @class           NerbTemplate
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
Copyright (c)2019 *
 * @todo
 * @requires        NerbError
 * @requires        ~/config.ini
 *
 */


/**
 *
 * Simple template class for using templates.  
 * Can be used as a standalone module or in conjunction with NerbPage
 *
 */
class NerbTemplate
{

    /**
     * params
     *
     * @var array
     * @access protected
     */
    protected $params = array(
    );

    /**
     * tags
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $tags = array();

    /**
     * template
     * 
     * (default value: '')
     * 
     * @var string
     * @access protected
     */
    protected $template = '';

    /**
     * error
     *
     * (default value: false)
     *
     * @var bool
     * @access protected
     */
    protected $error = FALSE;

    /**
     * msg
     *
     * (default value: "")
     *
     * @var string
     * @access protected
     */
    protected $msg = "";



    /**
     * __construct function.
     *
     * 
     *
     * @access public
     * @param string $filename
     * @throws NerbError
     * @return void
     */
    public function __construct( string $filename )
    {
        
        if( !file_exists($filename)){
            throw new NerbError( "" );
        }
        
        // process search_string
        // trim off spaces from search string
        $this->search_string = trim($search_string);

        // split keywords
        $this->keywords = $this->_splitKeywords($search_string);

        // catch html special characters if not allowed
        if ($this->params['allow_html'] == FALSE) {
            $this->keywords = $this->_htmlChars($this->keywords);
        }

        // table name to search in
        if ($table) {
            $this->table = $table;
        }

        return void;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !GETTERS AND SETTERS

    #################################################################




    /**
     *  setter function.
     *
     *  @access public
     *  @param string $key
     *  @param string $value
     *  @return string old value
     *  @throws NerbError
     */
    public function __set(string $key, string $value) : string
    {
        // error checking to ensure key exists
        if (!array_key_exists($key, $this->params)){
            throw new NerbError( 'The key <code>['.$key.']</code> is not a valid parameter' );
        } // end if
        
        // get original value
        $old = $this->params[$key];

        // set new value
        $this->params[$key] = $value;

        // return old value
        return $old;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     *  getter function.
     *
     *  @access public
     *  @param string $key
     *  @return mixed
     */
    public function __get(string $key)
    {
        // returns value
        return $this->params[$key];
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------





    /**
     *  to string function returns search sql statement
     *
     *  @access public
     *  @return string
     */
    public function __toString() : string
    {
        // returns value
        return $this->render();
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !INTERFACE FUNCTIONS

    #################################################################



    /**
     * stopWords function.  adds user defined words to list of predefined common words
     *
     * @access public
     * @param array $words
     * @param bool $replace (default = false)
     * @return NerbTemplate
     */
    public function tags(array $tags, bool $replace = FALSE) : NerbTemplate
    {
        // replace list
        if ($replace) {
            $this->excluded_words = $words;
        } // merge to existing list
        else {
            $this->excluded_words = array_merge($words, $this->excluded_words);
        }
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * stopWord function.
     *
     * @access public
     * @param string $word
     * @return NerbTemplate
     */
    public function tag(string $tag) : NerbTemplate
    {
        // add to list
        $this->excluded_words[] = $words;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



    /**
     * renders the page template
     *
     * @access public
     * @return string
     */
/*
    public function render()
    {
        // error checking
        // check to make sure search string is not empty
        if (!$this->search_string) {
            return $this->_err("Nothing to search for");

        // make sure that minimum search string length is achieved
        } elseif (strlen($this->search_string) <= $this->keyword_min_length) {
            return $this->_err("Search must be greater than ".$this->keyword_min_length." characters");
        }

        // strip stop words
        $this->keywords = $this->_stripStopWords($this->keywords);

        // format keywords into a usable sql statement
        $search = $this->_formatSearch($this->keywords);

        // this sets the conditions, eg if a search field or other match must be made
        if ($this->conditions) {
            // If there are keyword(s) AND required condition(s)
            foreach ($this->conditions as $field => $value) {
                if (!empty($value)) {
                    $condition .= "`$field` LIKE '$value' AND ";
                }
            }
        } // end if conditions

        // pass the statement to $this
        $this->sql = $sql;
        return;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------
*/




    #################################################################

    //            !ERRORS AND MESSAGES

    #################################################################



    /**
     * error function.
     *
     * returns error message if present, or false if no error
     *
     * @access public
     * @return mixed
     */
    public function error()
    {
        if ($this->error) {
            return $this->msg;
        } else {
            return FALSE;
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * err function.
     *
     * creates an error condition with message
     *
     * @access protected
     * @param mixed $msg
     * @return bool
     */
    protected function _err($msg): bool
    {
        $this->error = TRUE;
        $this->msg = $msg;
        return FALSE;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !

    #################################################################
    
    
    /**
     * replaceTags function.
     * 
     * @access protected
     * @return void
     */
    protected function replaceTags()
    {
        foreach ($this->tags as $tag => $value) {
            $this->template = str_replace('{'.$tag.'}', $value, $this->template);
        }
	 
        return true;
    }



} /* end class */
