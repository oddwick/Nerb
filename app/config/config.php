<?PHP  

	/*
	 *	Site configuration file
	 *
	 *  This is where all of the site specific variables are configured	
	*/
	
	// define base directories for framework and pages
	define( "CONTROLLERS", APP_PATH."/controllers" );
	define("__TMP__", "/tmp");
	
	// db connection
	$DB =  array(
		"host" => "localhost",
		"name" => "database",
		"pass" => "yourpassword",
		"user" => "user"
	);
	
	
	// operating variables
	define( "SOME_CONSTANT", 100 ); 
	
	
?>