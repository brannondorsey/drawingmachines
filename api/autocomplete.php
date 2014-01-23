<?php

require_once '../includes/config.php';
require_once '../includes/classes/class.Autocomplete.php';
require_once '../includes/classes/class.Database.inc.php';

if(isset($_GET['table']) &&
   isset($_GET['column_name']) &&
   isset($_GET['chars'])){

	Database::init_connection('localhost', $DATABASE, $_GET['table'], $DATABASE_USER, $DATABASE_PASSWORD);
	$get_array = Database::clean($_GET); //clean the $_GET array
   	$autocomplete = new Autocomplete($get_array['column_name'], $get_array['table']);
   	$results_obj = $autocomplete->get_results_as_JSON($get_array['chars']);
   	echo $results_obj;
   	Database::close_connection();
}	

?>