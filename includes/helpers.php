<?php
function commas_to_array($string) {

	$raw_output = explode(",", $string);
	$output = array();
	foreach ($raw_output as $list_item) {
		$list_item = trim($list_item);
		if ($list_item != "") $output[] = $list_item;

	}
	return $output;
}

//returns total number of results from an assoc array of api parameters
//note: pass in search array unaltered from how it will be searched
function total_numb_results($search_array, $api){
	$search_array['count_only'] = true;
	if(array_key_exists('limit', $search_array)) unset($search_array['limit']);
	if(array_key_exists('page', $search_array)) unset($search_array['page']);
	$obj = json_decode($api->get_json_from_assoc($search_array));
	return $obj->count;
}

// removes $char from the FRONT of each array item and capitalizes them
function remove_char_from_tags(&$array, $char) {

	for ($i = 0; $i < count($array); $i++) {
		 $array[$i]->name = ucfirst(ltrim(($array[$i]->name), $char));
	}
}
?>