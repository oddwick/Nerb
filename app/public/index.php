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
    	error_reporting( E_ERROR | E_WARNING | E_PARSE );
    //	error_reporting( E_ERROR | E_PARSE );
    //	error_reporting(E_ALL );
	
    // define namespace
    use nerb\framework\Nerb as Nerb;
    use nerb\framework as App;
    
    // begin session
    session_start();
	
    // define constants
    // set app path to the current parent folder
    // this can be set either manually or by a relative directive
    define( 'APP_PATH', realpath(__DIR__.'/..' ) );
	
    // include required config data
    require_once APP_PATH.'/framework/core/Init.php';
	require_once APP_PATH.'/data/credentials.php';

    // initialize framework and include application config as app.ini
    $app = App\Init::begin();

    // create a page object with an ini file
    Nerb::registry()->register( $page = new App\Page(), 'Page' );
    
    // connect to the database
    // initialize & register tables
	Nerb::registry()->register( $database = new App\Database( 'Database', $DB ), 'Database' );
	
    // -- include logic page for testing
    require_once APP_PATH.'/testing/test.php';
	
    // create a controller
    App\ClassManager::setPath( CONTROLLERS, 'controllers' );
    $controller = App\Node::controller( CONTROLLERS, URL_MODE, 0 );
    // rout and display page
    $controller->route();
	
	// -- force the page to display an error for testing
    //$page->error(503);
    // display the page
    $page->render();
    
    //Nerb::session();
	
    exit();
