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
 * @class           NerbUtility
 * @version         1.0
 * @author          Dexter Oddwick <dexter@oddwick.com>
 * @copyright       Copyright (c)2019
 * @todo
 * @requires        ~/config.ini
 * @requires        ~/lib
 *
 */


/**
 *
 * Container class for simple utility functions
 *
 */
class Util
{

    /**
     * Singleton Pattern prevents multiple instances of NerbUtility.  all calls must be made statically e.g. NerbUtility::function(  args  );
     *
     *   @access     public
     *   @return     void
     */
    private function __construct()
    {
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------


    /**
     *   Returns the max allowable server upload in human readable form
     *   This is set in the php.ini file on the server
     *
     *   @access     public
     *   @return     string
     */
    public static function getMaxUpload()
    {

        $val = ini_get('upload_max_filesize');
        $val = trim($val);
        $last = strtolower($val(strlen($val) - 1));
        $val = substr($val, 0, strlen($val) - 1);
        switch ($last) {
            case 'g':
                $val .= ' Gb';
                break;
            case 'm':
                $val .= ' Mb';
                break;
            case 'k':
                $val .= ' Kb';
                break;
        }

        return $val;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     *   recursively search an array
     *
     *   @access     public
     *   @param      string $needle
     *   @param      array $haystack
     *   @return     mixed the key of the array if found
     */
    public static function recursive_array_search( $needle, $haystack )
    {
        foreach ( $haystack as $key => $value ) {
            $current_key=$key;
            if ( $needle===$value or (  is_array( $value ) && recursive_array_search( $needle, $value ) !== FALSE  ) ) {
                return $current_key;
            }
        }

        return FALSE;
        
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    /**
     * returns list of US states.
     * 
     * @access public
     * @static
     * @return array
     */
    public static function us_states() : array
    {
		$states = array(
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'Washington DC',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'PR' => 'Puerto Rico',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VI' => 'Virgin Islands',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
            //-- canadian provences
			'AB' => 'Alberta', 
			'BC' => 'British Columbia',
			'MB' => 'Manitoba',
			'NB' => 'New Brunswick',
			'NL' => 'Newfoundland and Labrador',
			'NS' => 'Nova Scotia',
			'ON' => 'Ontario',
			'PE' => 'Prince Edward Island',
			'SK' => 'Saskatchewan',
			'QC' => 'Quebec'

        );
        return $states;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------




    #################################################################

    //            !SOCIAL METHODS

    #################################################################




    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = true, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    } // end function -----------------------------------------------------------------------------------------------------------------------------------------------



} /* end class */
