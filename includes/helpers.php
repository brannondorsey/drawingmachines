<?php
function commas_to_array($string) {

	$raw_output = explode(",", $string);
	$output = array();
	foreach ($raw_output as $list_item) {
		$output[] = trim($list_item);
	}
	return $output;
}

?>