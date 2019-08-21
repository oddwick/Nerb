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
 * @class           SearchKeyword
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
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
class SearchKeyword
{


    /**
     * keywords_array
     *
     * (default value: '')
     *
     * @var string
     * @access protected
     */
    protected $keyword = '';



    /**
     * __construct function.
     * 
     * @access public
     * @param string $keyword
     * @return void
     */
    public function __construct( string $keyword)
    {
        return;
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




    /**
     * _formatKeyword function.
     *
     * @access protected
     * @param string $keyword
     * @return string
     */
    protected function _formatKeyword(string $field, string $keyword) : string
    {
        $not = '';
        
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
     * @param string $keyword
     * @param string $datatype
     * @return string
     *
     * @todo figure out bool datatype
     */
    protected function _datatype(string $keyword, string $datatype) : string
    {
        // initialize
        $wildcard = NULL;
        $bool = NULL;
        
        // if use_datatyping is not used, then escape the keyword and return
        if (!$this->params['use_datatyping']) {
            return $this->_escapeDB($keyword);
        }

        // inelegant, but checks to see if keyword is NOT
        if (preg_match('/{NOT}/', $keyword)) {
            $keyword = preg_replace('/{NOT}/', '', $keyword);
            $bool = '{NOT}';
        }

        // check to see if a wildcard was used
        if (preg_match('/{\?}/', $keyword)) {
            $keyword = preg_replace('/{\?}/', '', $keyword);
            $wildcard = '{?}';
        }
        
        // create datatyper
        $type = new Datatype($datatype);
        $keyword = $type->check($keyword);


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
     * @return string
     */
    protected function _splitKeywords(string $search_string) : string
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
            $keyword = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function($plit) {
                return chr($plit[1]);
            }, $keyword);
            $keyword = preg_replace("/\{COMMA\}/", ",", $keyword);
            $keywords[$key] = $keyword;
        }

        // convert the {COMMA} and {WHITESPACE} back in $this->keywords
        $keywords = preg_replace_callback("~\{WHITESPACE-([0-9]+)\}~", function($plit) {
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
     * @return string
     */
    protected static function transform(array $keyword) : string
    {
        // replace commas and whitespace with {PLACEHOLDERS}
        $keyword[1] = preg_replace_callback("~(\s)~", function($match) {
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
