<?PHP  
	/*
	*
	*
	*	Stamp Album Pro Bootstrap file
	*	(c) 2017 Dexter Oddwick
	*
	*	script begin
	*/
	
	
	/*
	 *
	 *  Debugging block
	 *
	 *
	*/
	//echo 'Unauthorized access';
	//die;
	
	ini_set( 'display_errors', 'On' );	
	error_reporting( E_ERROR | E_WARNING | E_PARSE );
	//error_reporting(E_ALL );
	
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
	
	// initialize framework and include supplental config as app.ini
	Nerb::init();
	Nerb::addConfig( 'app.ini', '/config' );
	
	//load base classes	
	Nerb::loadclass( 'NerbDatabase' );
	Nerb::loadclass( 'NerbUser' );
	Nerb::loadclass( 'NerbNode' );
	Nerb::loadclass( 'NerbPage' );
	Nerb::loadclass( 'NerbParams' );
	
	// connect to the database
	// initialize & register tables
	Nerb::register( $db = new NerbDatabase( 'database', $DB ), 'data' );
	Nerb::register( new NerbDatabaseTable( $db, 'users' ), 'user_table' );
	Nerb::register( new NerbDatabaseTable( $db, 'stamps' ), 'stamp_table' );
	Nerb::register( new NerbDatabaseTable( $db, 'variants' ), 'variant_table' );
	Nerb::register( new NerbDatabaseTable( $db, 'images' ), 'image_table' );
	Nerb::register( new NerbDatabaseTable( $db, 'countries' ), 'country_table' );
	Nerb::register( new NerbDatabaseTable( $db, 'classes' ), 'class_table' );
	Nerb::register( new NerbDatabaseTable( $db, 'log_change' ), 'log_change' );
	
	
	// create a page object with an ini file
	Nerb::register( $page = new NerbPage( '/config/page.ini' ), 'Page' );
	
	// create and register user data
	$user_data = array( 
		'table' => 'user_table', 
		'user' => 'user_name', 
		'pass' => 'user_pass', 
		'uid' => 'user_id' 
	);
	
	Nerb::register( $user = new NerbUser( $user_data ), 'user' );
	
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
	
	
	//echo '<pre>';
	//print_r( $_SESSION );
	//print_r($_COOKIE);
	exit();

?>