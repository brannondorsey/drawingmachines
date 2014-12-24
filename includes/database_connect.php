<?php

	 //include the API Builder mini lib
	 require_once "includes/classes/class.API.inc.php";
	 require_once "includes/config.php";

	  	//specify the columns that will be output by the api as a comma-delimited list
	  	$columns = "id,
	  				device_name,
	  				inventor,
	  				inventor_line_2,
	  				year,
	  				circa,
	  				category,
	  				post_content,
	  				tags,
	  				source,
	  				source_line_2";

	  	//setup the API
	  	$api = new API("localhost", 
	  				   $DATABASE, 
	  				   "machines", 
	  				   $DATABASE_USER, 
	  				   $DATABASE_PASSWORD);

	  	$api->setup($columns);
	  	$api->set_default_order("year");
	  	// $api->set_searchable("column_name, column_name, etc...");
	  	// $api->set_default_search_order("column_name");
	  	$api->set_pretty_print(true);

	  	//sanitize the contents of $_GET to insure that 
	  	//malicious strings cannot disrupt your database
	 	// $get_array = Database::clean($_GET);

	 	// //output the results of the http request
	 	// echo $api->get_json_from_assoc($get_array);
	
?>