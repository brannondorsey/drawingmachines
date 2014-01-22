<?php

$get_array = Database::clean($_GET); //clean the $_GET array
if(isset($get_array['table']) &&
   isset($get_array['column_name'] &&)
   isset($get_array['chars']){

   	Database::init_connection($HOSTNAME, $DATABASE, $get_array['table'], $DATABASE_USER, $DATABASE_PASSWORD);
   	$autocomplete = new Autocomplete($get_array['column_name'], $get_array['table']);
   	$results_obj = $autocomplete->get_results_as_JSON($get_array['chars']);
   	echo $results_obj;
   	Database::close_connection();
}	

?>