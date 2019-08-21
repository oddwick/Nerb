<?PHP
    use nerb\framework as App;
    // this file is for testing purposes with a database connection
	
    $string = "searching for sheets";
    $search = new App\Search( $string );
    $search->field('description');
    $search->search();
    
    echo $search;
    
    die;
    //echo $schema->primary('classes');
    //$schema->sqlFromFile( APP_PATH.'/../classes.sql' );
    //print_r( $db->tables() );
    //print_r( $schema->describe( 'classes' ) );
	
	
    //$test = new NerbDatabaseFetch( $db, 'classes' );
    $test = $database->table( 'classes' );
	
	
    $results = $test->fetch('standard = 1 ORDER BY description ASC');
	
    //echo $results->count();
	
    echo $test->deleteRow(91);
	
    foreach( $results as $result ){
        echo $result->class_id." - ".$result->description.PHP_EOL;
        //$result->description.="_";
        //$result->save();
    } // end foreach
	
	
    //echo $test->columns();


    $query = "SELECT MAX(date_modified) FROM classes ";
    die;
    //$schema =  new NerbSchema( $db );

    //echo $schema->sqlFromFile(APP_PATH.'/../classes.sql');

        $schema->dropTable( 'classes' );
        //print_r();

    //$info = $schema->showTables(); 
    //print_r( $info );








    die;




	
/*
	$log = new NerbLog( LOG.'/testlog.log' );
	//$log->write("this is a test of the logging system - ".time() );
	
	echo $log->getLastEntry(  );
	die;
*/

	
/*
	
	$users = Nerb::fetch( "user_table" );
	die;
*/
	
	
	
	
	
	
	
	
?>