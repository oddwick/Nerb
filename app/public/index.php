<?PHP 
	
// ! Nerb framework bootstrap file


	// error reporting for debugging 
	//ini_set('display_errors', 'On' );	
	//ini_set('display_errors', 1);
	//error_reporting(E_ERROR | E_WARNING | E_PARSE );
	
	
	//ini_set('display_errors', 'On' );	
	
	// 
	session_start();
	
	define( "APP_PATH", realpath(__DIR__."/.." ));
	require_once( APP_PATH."/config/config.php" );

	// set include paths
	set_include_path( get_include_path() . PATH_SEPARATOR . APP_PATH . PATH_SEPARATOR . APP_PATH."/framework" . PATH_SEPARATOR . LIB );

	// load static class
	require_once( APP_PATH."/framework/Nerb.php" );
	
	// initialize framework
	Nerb::init();
	
	//load base classes	
	Nerb::loadclass( 'NerbDatabase' );
	Nerb::loadclass( 'NerbRouter' );

	// connect to the database
	Nerb::register( $db = new NerbDatabase( "databaseName",  $DB ) , 'database' );
	
	//load and register database tables
	Nerb::register( new NerbDatabaseTable( $db, 'some_table_in_database' ) , 'table_name' );

	// set the token and parse the url from html strings
	$options = array(
		'path_to_controller' =>   CONTROLLERS,
		'use_clean_urls' =>   USE_CLEAN_URLS,
		'node_index' => 0
	);
	
	$router = new NerbRouter( CONTROLLERS, $options );
	
	// begin routing 
	$router->route();
	
	exit();

	?>