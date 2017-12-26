<?PHP
// Nerb Application Framework

/**
 * Nerb System Configuration file
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 *
 * 	@category   	Nerb
 * 	@package    	Nerb
 *	@author			Dexter Oddwick <dexter@oddwick.com>
 * 	@copyright  	Copyright �2017  Oddwick Industries, Ltd. (https://www.oddwick.com)
 * 	@license    	https://www.oddwick.com/docs/license
 */


# [Nerb]
#	
#   SHOW_TRACE
#   BOOL true: show trace info when inspecting variables 
#
define( "SHOW_TRACE", false ); 



# [Files]
#	
#   SHOW_TRACE
#   BOOL true: show trace info when inspecting variables 
#
define( "MODULES", FRAMEWORK."/modules" ); 
define( "AUTOLOAD", false ); 



# [NerbError]
#
# 	ERROR_LEVEL
#   [0|1|2] 
#   0: simple error (for production enviornments)
#   1: show detail error
#   2: shows verbose debug and trace info with full paths
#
define( "ERROR_LEVEL", 2 ); 

#   ERROR_LOGGING
#   BOOL: TRUE - log errors into a log file
#
define( "ERROR_LOGGING", false );

#   IGNORE_INVALID_KEYS
#   BOOL: TRUE - log ignores keys that do not exist.  throws error on false
#
define( "IGNORE_INVALID_KEYS", true ); 



# [NerbRouter]
#
#   USE_CLEAN_URLS
#   BOOL: TRUE - lean urls /query/x/ | FALSE - dirty urls (.php?query=x) 
#
define( "USE_CLEAN_URLS", false );

#   DEFAULT_FILE_EXTENSION
#   STRING: - the default extension of the controllers for a nerb router
#
define( "DEFAULT_FILE_EXTENSION", "php" ); 



# [libraries]
# load required libraries and modules
//require_once   FRAMEWORK."/library.php";



	
	
	
?>