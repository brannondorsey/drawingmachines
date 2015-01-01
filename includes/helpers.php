<?php
require_once 'classes/class.Upload.php';

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

function get_machine_thumbnail($machine) {

	// dynamically read a thumbnail image filename
 	$thumbnail_dir = "images/machine/" . $machine->id . "/thumbnail";
 	$thumbnail_filename = NULL;

 	if (file_exists($thumbnail_dir)) {
 		$thumbnail_files = scandir($thumbnail_dir);
	 	if (!empty($thumbnail_files)) {
	 		$thumbnail_filename = $thumbnail_dir . "/" . $thumbnail_files[2]; // 2 is the first image
	 	}
 	}

 	return ($thumbnail_filename != NULL) ? $thumbnail_filename : "";
}

function upload_file($destination_folder, $file, $size, $mime_types) {

	$upload = Upload::factory($destination_folder, "./");
	$upload->file($file);
	$upload->set_max_file_size($size);
	$upload->set_allowed_mime_types($mime_types);
	$upload->set_filename($file['name']);
	return $upload->upload();
}

/**
 * Formats filesize in human readable way.
 *
 * @param file $file
 * @return string Formatted Filesize, e.g. "113.24 MB".
 */
function filesize_formatted($file)
{
    $bytes = filesize($file);

    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

?>