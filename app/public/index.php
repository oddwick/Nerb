<?PHP  
	/*
	*
	*
	*	Nerb Framework Bootstrap file
	*	(c) 2009-2019 Dexter Oddwick
	*
	*	script begin
	*/
	
	
	/*
	 *
	 *  Debugging block
	 *
	 *  set error reporting
	 *  disable for production site
	 *
	*/
	//	ini_set( 'display_errors', 'On' );	
	//	error_reporting( E_ERROR | E_WARNING | E_PARSE );
	//	error_reporting(E_ALL );
	
	// begin session
	session_start();
	
	// define constants
	// set app path to the current parent folder
	// this can be set either manually or by a relative directive
	define( 'APP_PATH', realpath(__DIR__.'/..' ) );
	
	// include required config data
	require_once APP_PATH.'/config/credentials.php' ;

	// load static class
	require_once APP_PATH.'/framework/Nerb.php';
	
	// initialize framework and include application config as app.ini
	Nerb::init();
	Nerb::addConfig( '/config/app.ini' );
	
	// create a page object with an ini file
	Nerb::register( $page = new NerbPage( '/config/page.ini' ), 'Page' );
	
	// connect to the database
	// initialize & register tables
	Nerb::register( $database = new NerbDatabase( 'Database', $DB ), 'Database' );
	$Users = $database->table('users');
	
	//require_once APP_PATH.'/public/playground.php';
	
	// set the token and parse the url from html strings
	$options = array(
		'node_index' => 0
	);

	// create a controller
	$controller = NerbNode::controller( CONTROLLERS, URL_MODE, 0, $options );

	// rout and display page
	$controller->route();
	
	// display the page
	$page->render();
	
	exit();

?>