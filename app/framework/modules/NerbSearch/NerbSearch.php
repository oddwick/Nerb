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
 * @class           NerbSearch
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
Copyright (c)2019 *
 * @todo
 * @requires        ~/config.ini
 *
 */


/**
 *
 * Class for generating sql search statements and cleaning up search data using a
 * PHP search class created by GitFr33 as a starting point
 *
 */
class NerbSearch
{

    /**
     * params
     *
     * (default value: array(
     *      'greedy' => true,
     *      'phonetic' => false, // set to true to use metaphone() searching
     *      'keyword_min_length' => 3,
     *      'html' => true, // allows html chars in search -- setting to false will also kill wildcard chars
     *     ))
     *
     * @var array
     * @access protected
     */
    protected $params = array(
        'greedy_search' => GREEDY_SEARCH,
        'keyword_min_length' => KEYWORD_MIN_LENGTH,
        'allow_html' => ALLOW_HTML, // allows html chars in search -- setting to false will also kill wildcard chars
        'use_datatyping' => USE_DATATYPING, // this forces strict use_datatyping for keywords
    );


    /**
     * excluded_words  list of common words not to be searched
     *
     * (default value: array('a','an','are','as','at','be','by','com','for','from','how','in','is','it','of','on','or','that','the','this','to','was','what','when','who','with','the'))
     *
     * @var string
     * @access protected
     */
    protected $excluded_words =  array(
        'a','an','are','as','at','be','by','com','for','from','how',
        'in','is','it','of','on','or','that','the','this','to','was','what','when','who','with','the'
    );

    /**
     * invalid_char
     *
     * list of characters to filter out of search keywords
     *
     * (default value: array(
     *      '(', ')', '=', '~', '`', '@', '#', '^', '&', '[', ']','{', '}',':', '<', '>', '|',
     *  ))
     *
     * @var string
     * @access protected
     */
    protected $invalid_char = array(
        '(', ')', '=', '~', '`', '@', '#', '^', '&', '[', ']','{', '}',':', '<', '>', '|',
    );

    /**
     * keywords_array
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $keywords = array();

    /**
     * sql
     *
     * (default value: "")
     *
     * @var string
     * @access protected
     */
    protected $sql = "";

    /**
     * keywords
     *
     * @var mixed
     * @access protected
     */
    protected $search_string;

    /**
     * table
     *
     * @var mixed
     * @access protected
     */
    protected $table;

    /**
     * sort_field
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $sort_field = array();

    /**
     * search_fields
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $search_fields = array();

    /**
     * conditions
     *
     * (default value: array())
     *
     * @var array
     * @access protected
     */
    protected $conditions = array();

    /**
     * error
     *
     * (default value: false)
     *
     * @var bool
     * @access protected
     */
    protected $error = false;

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
     * if a table is not given, only a where statement is returned
     *
     * @access public
     * @param string $table (default: NULL) name of the table searched
     * @return void
     */
    public function __construct(string $search_string, string $table = null)
    {
        // process search_string
        // trim off spaces from search string
        $this->search_string = trim($search_string);

        // split keywords
        $this->keywords = $this->_splitKeywords($search_string);

        // catch html special characters if not allowed
        if ($this->params['allow_html'] == false) {
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
        return $this->params[ $key ];
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
        return $this->sql;
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
     * @return NerbSearch
     */
    public function stopWords(array $words, bool $replace = false) : NerbSearch
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
     * @return NerbSearch
     */
    public function stopWord(string $word) : NerbSearch
    {
        // add to list
        $this->excluded_words[] = $words;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * invalidChars function.  adds user defined array of characters to exclude from search
     * if replace is true, then the user list will replace the existing char list, otherwise
     * it will be merged to existing
     *
     * @access public
     * @param array $chars
     * @param bool $replace (default: 'false')
     * @return NerbSearch
     */
    public function invalidChars(array $chars, bool $replace = false) : NerbSearch
    {
        if ($replace) {
            // replace existing list
            $this->invalid_char = $this->invalid_chars;
        } else {
            // merge to existing list
            $this->invalid_char = array_merge($chars, $this->invalid_chars);
        }
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * invalidChar function. appends single invalid character to list
     *
     * @access public
     * @param string $char
     * @return NerbSearch
     */
    public function invalidChar(string $char) : NerbSearch
    {
        $this->invalid_char[] = $char;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * where function.
     *
     * sets search condition,  eg `field` > '[condition]'
     *
     * @access public
     * @param string $field
     * @param string $condition
     * @return NerbSearch
     */
    public function where(string $field, string $condition) : NerbSearch
    {
        $this->conditions[ $field ] = $condition;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




   /**
     * sort function.  sets the sort field and direction
     *
     * @access public
     * @param string $field
     * @param string $dir (default: 'DESC')
     * @return NerbSearch
     */
    public function sort(string $field, string $dir = 'DESC') : NerbSearch
    {
        $this->sort_field[ $field ] = $dir;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * page function.
     *
     * creates the mysql limit result for pagination.
     * LIMIT ( max_results * page, page )
     *
     * @access public
     * @param int $page
     * @return NerbSearch
     */
    public function page(int $page) : NerbSearch
    {
        $this->page = $page;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * field function.
     *
     * this is the actual field that is searched in.
     *
     * $datatype must be string|alpha|alphanum|int|float|bool
     * the keywords will be matched against datatypes and all non allowed characters will be stripped out
     * disallowed characters are stored in invalid_char
     *
     * string - alphanum and certain special chars
     * aplha - only a-z A-Z
     * alpannum - A-Z a-z 0-9
     * int - 0-9
     * float - 0-9.
     * phonetic - converts keyword to metaphone and ignores special characters
     * bool - 0|1 true|false
     *
     * @access public
     * @param string $field
     * @param string $datatype (default: 'string')
     * @throws NerbError
     * @return NerbSearch
     */
    public function field(string $field, string $datatype = 'string') : NerbSearch
    {
        // force lowercase
        $datatype = strtolower($datatype);

        if ($datatype != 'string' &&
            $datatype != 'alpha' &&
            $datatype != 'alphanum' &&
            $datatype != 'int' &&
            $datatype != 'float' &&
            $datatype != 'phonetic' &&
            $datatype != 'bool'
         ) {
             throw new NerbError("Invalid datatype.  Datatypes must be <code>[string|alpha|alphanum|int|float|bool]</code>");
        }

        $this->search_fields[ $field ] = $datatype;
        return $this;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * search function executes search.
     *
     * @access public
     * @return string bool on error
     */
    public function search()
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

        // create sql statement
        $sql = $condition.' ( '.$search.' )';

        // if a table is given, returns a full sql statement, otherwise just the where clause
        if ($this->error) {
            return false;
        } elseif ($this->table) {
            $sql .= "SELECT * FROM `".$this->table."` WHERE ";
        }

        // append sort field if given
        if ($this->sort_field) {
            $sql .= $this->_orderBy();
        }

        // pass the statement to $this
        $this->sql = $sql;
        return true;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _orderBy function.
     *
     * @access protected
     * @return string
     */
    protected function _orderBy() : string
    {
        // initialize temp array
        $sort = array();

        // loop through sort field array and collapse it
        foreach ($this->sort_field as $field => $dir) {
            $sort[] = $field." ".strtoupper($dir);
        }

        // implode with order by statement
        $sort = " ORDER BY ".implode(', ', $sort);

        return $sort;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




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
            return false;
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
        $this->error = true;
        $this->msg = $msg;
        return false;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !KEYWORD MANIPULATION

    #################################################################



    protected function _formatSearch(array $keywords) : string
    {
        // Greedy search (match any keywords)
        // note boolean NOT will not work with greedy searches, otherwise
        // ALL possible results will be returned, so NOT keywords will be eliminated
        // from search parameters

        // loop through search fields
        foreach ($this->search_fields as $field => $datatype) {
            // loop through keywords
            foreach ($keywords as $keyword) {
                // datatype the keyword
                $keyword = $this->_datatype($keyword, $datatype);

                // if the keyword still exists after use_datatyping and does not contain {NOT} if greedy searching
                if (!empty($keyword) &&
                    ( !$this->greedy_search || ( $this->greedy_search && !preg_match('/{NOT}/', $keyword) ) )
                ) {
                    $hold[] = $this->_formatKeyword($field, $keyword);
                }
            }// end foreach search_fields

            // implode the remaining results
            $search[] = implode($this->greedy_search?' OR ':' AND ', $hold);
            unset($hold);
        }// end foreach keywords

        // if no keywords made it past use_datatyping return error
        if (count($search) < 1) {
            return $this->_err('Search returned no results');
        }

        // group fields together with implode and return
        return '( '.implode(') OR ( ', $search).' )';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _formatKeyword function.
     *
     * @access protected
     * @param string keyword
     * @return string
     */
    protected function _formatKeyword(string $field, string $keyword) : string
    {
        // if keyword is {NOT} create NOT RLIKE statement else return rlike
        if (preg_match('/{NOT}/', $keyword)) {
            $keyword = preg_replace('/{NOT}/', '', $keyword);
            $not = 'NOT ';
        }
        return "`".$field."` ".$not."RLIKE '".$keyword."'";
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _datatype function.
     *
     * @access protected
     * @param string keyword
     * @param string datatype
     * @return string
     *
     * @todo figure out bool datatype
     */
    protected function _datatype(string $keyword, string $datatype) : string
    {
        // if use_datatyping is not used, then escape the keyword and return
        if (!$this->use_datatyping) {
            return $this->_escapeDB($keyword);
        }

        // inelegant, but checks to see if keyword is NOT
        if (preg_match('/{NOT}/', $keyword)) {
            $keyword = preg_replace('/{NOT}/', '', $keyword);
            $bool =     '{NOT}';
        }

        // check to see if a wildcard was used
        if (preg_match('/{\?}/', $keyword)) {
            $keyword = preg_replace('/{\?}/', '', $keyword);
            $wildcard = '{?}';
        }

        // switch datatype and run keyword through replacement regex
        switch (strtolower($datatype)) {
            case 'int':
                // clears any non numeric character
                $keyword = preg_replace('/([^0-9])/', '', $keyword);
                break;

            case 'alphanum':
                // clears any non alphanumeric character
                $keyword = preg_replace('/([^0-9a-zA-Z])/', '', $keyword);
                break;

            case 'alpha':
                // clears any non alpha character
                $keyword = preg_replace('/([^a-zA-Z])/', '', $keyword);
                break;

            case 'float':
                // clears any non alpha character
                $keyword = preg_replace('/([^0-9\.])/', '', $keyword);
                break;

            case 'phonetic':
                // clears any non alpha character
                $keyword = metaphone(preg_replace('/([^0-9a-zA-Z])/', '', $keyword));
				// phonetic always searches with wild card operator
				//$wildcard = '{?}';
                break;

            case 'string':
            default:
                // replace any invalid chars for searching
                $replace = implode('\\', $this->invalid_char);
                $keyword = preg_replace('/(['.$replace.'])/', '', $keyword);
                break;
        } // end switch

        if (empty($keyword)) {
            return $keyword;
        } else {
            return $this->_escapeDB($bool.$keyword.$wildcard);
        }
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _splitKeywords function.
     *
     * @access protected
     * @param string $search_string
     * @return array
     */
    protected function _splitKeywords(string $search_string) : array
    {
        // wildcard searches: Replace * or ? with %
        $search_string = str_replace('*', '{?}', str_replace('?', '{?}', $search_string));

        // boolean searches
        $search_string = str_replace('!', '{NOT}', $search_string);

        // Send anything between quotes to transform() which replaces commas and whitespace with {PLACEHOLDERS}
        $search_string = preg_replace_callback("~\"(.*?)\"~", "NerbSearch::transform", $search_string);

        // Split $this->keywords by spaces and commas and Populate $this->keywords with parts
        $keywords = preg_split("/\s+|,/", $search_string);

        // convert the {COMMA} and {WHITESPACE} back within each row of $this->keywords
        foreach ($keywords as $key => $keyword) {
            $keyword = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function ($plit) {
                return chr($plit[1]);
            }, $keyword);
            $keyword = preg_replace("/\{COMMA\}/", ",", $keyword);
            $keywords[$key] = $keyword;
        }

        // convert the {COMMA} and {WHITESPACE} back in $this->keywords
        $keywords = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function ($plit) {
            return chr($plit[1]);
        }, $keywords);
        $keywords = preg_replace("/\{COMMA\}/", ",", $keywords);

        return $keywords;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _stripStopWords function.
     *
     * @access protected
     * @param array $keywords
     * @return array
     */
    protected function _stripStopWords(array $keywords) : array
    {
        // loop through each keyword and kill common words
        foreach ($keywords as $key => $value) {
            if (in_array($value, $this->excluded_words)) {
                unset($keywords[$key]);
            } // end if
        }// end foreach

        return $keywords;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !ESCAPE AND TRANFORM

    #################################################################




    /**
     * transform function.
     *
     * replaces commas and whitespace with {PLACEHOLDERS}
     *
     * @access protected
     * @param array $keyword
     * @return void
     */
    protected static function transform(array $keyword) : string
    {
        // replace commas and whitespace with {PLACEHOLDERS}
        $keyword[1] = preg_replace_callback("~(\s)~", function ($match) {
            return '{WHITESPACE-'.ord($match[1]).'}';
        }, $keyword[1]);
        $keyword = preg_replace("/,/", "{COMMA}", $keyword[1]);
        return $keyword;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _escapeRlike function.
     *
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function _escapeRlike(string $keyword) : string
    {
        return preg_replace("~([.\[\]*^\$])~", '\\\$1', $keyword);
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _escapeDb function.
     *
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function _escapeDb(string $keyword) : string
    {
        return str_replace('{?}[[:>:]]', '', str_replace('[[:<:]]{?}', '', '[[:<:]]'.AddSlashes($this->_escapeRlike($keyword)).'[[:>:]]'));
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _escapeRegex function.
     *
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function _escapeRegex(string $keyword) : string
    {
        return '\b'.preg_quote($keyword, '/').'\b';
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * _htmlChars function.
     *
     * @access protected
     * @param array $keywords
     * @return array
     */
    protected function _htmlChars(array $keywords) : array
    {
        $out = array();
        foreach ($keywords as $keyword) {
            // wildcard searches
            $keyword = str_replace('%', '*', $keyword);

            if (preg_match("/\s|,/", $keyword)) {
                $out[] = '"'.htmlspecialchars($keyword).'"';
            } else {
                $out[] = htmlspecialchars($keyword);
            }
        }
        return $out;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


} /* end class */
