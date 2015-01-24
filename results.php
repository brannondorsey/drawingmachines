<?php

	 require_once "includes/database_connect.php";
	 require_once "includes/helpers.php";
	 require_once "includes/classes/markdown/Markdown.inc.php";

	 if (isset($_GET) &&
	 	 !empty($_GET)) {

	 	$query_array = Database::clean($_GET);
	 	$results_obj = json_decode($api->get_json_from_assoc($query_array));
	 	$page = (isset($_GET['page']) ? $_GET['page'] : 1);

	 	if (isset($results_obj->data)) {

	 		// filter DB results "by hand" to ensure that category and tags lookups were
	 		// not too relaxed. (e.g. a search for "Mirror" should not return "blah blah (Mirror)")
	 		$results_obj->data = filter_results($results_obj->data, $query_array, 'tags');
	 		$results_obj->data = filter_results($results_obj->data, $query_array, 'category');

	 		// $limit = (isset($query_array['limit'])) ? (int) $query_array['limit'] : 0;
	 		// $numb_results = max(count($results_obj->data), $limit);
	 		// $total_numb_results = total_numb_results($query_array, $api); //gives total number of pages
		  // $total_pages = ceil($total_numb_results / $numb_results); //calculates total number of pages
		  // if ($page > $total_pages) $page = $total_pages; //sets page to max page if page it exceeds it

	 	} else {
	 		//error or no results found
	 	}

	 } else header("Location: " . $HOSTNAME);

	 require_once "includes/header.php";
	 require_once "includes/menu.php";
	 require_once "includes/helpers.php";

	// Reduces relaxed search results
	// Use for category and tag columns only
	function filter_results($results, $query_array, $column_name) {
	 	
	 	if (!isset($query_array[$column_name])) {
	 		return $results;
	 	}

	 	$results_to_return = array();
	 	$query_string = preg_quote($query_array[$column_name]);
	 	$regex_prepend = ($column_name == 'tags') ? '/#' : '/^';
	 	// note: when column name is != 'tags' the second preg_match() the second 
	 	$regex_append = ($column_name == 'tags') ? ',/i' : '$/i'; 

	 	foreach ($results as $result) {	
	 		
	 		if (!isset($result->$column_name)) break;

 			if (preg_match($regex_prepend . $query_string . $regex_append, $result->$column_name) == 1) {
 				$results_to_return[] = $result;
 			} else if ($column_name == 'tags' && preg_match($regex_prepend . $query_string . "$/i", $result->$column_name) == 1) {
 				$results_to_return[] = $result;
 			}
 		}

 		return $results_to_return;
	}

?>
<script>
	$(document).ready(function(){
		$('.result').click(function(){
			window.location.href = <?php echo "'" . $HOSTNAME . "/post.php?id=" . "'"?> + $(this).attr('data-id');
		});
	});
</script>

<div class="content results-container">
	<?php if (!isset($results_obj->data) || count($results_obj->data) == 0): ?>
	<h2>No results found</h2>
	<?php elseif (isset($query_array["tags"]) || isset($query_array["category"])): ?>
	<h2>
		Showing results for "<?php
		if (isset($query_array["tags"])) echo $query_array["tags"];
		else if (isset($query_array["category"])) echo $query_array["category"];
		?>"
	</h2>
	<?php endif ?>
	<?php
	if (isset($results_obj->data)) :
		foreach($results_obj->data as $machine):
	?>
	<div class="result" data-id="<?php echo $machine->id?>">
		<img src="<?php echo get_machine_thumbnail($machine)?>">
		<h3><?php if (isset($machine->device_name)) echo $machine->device_name?></h3>
		<h4><?php if (isset($machine->inventor)) echo $machine->inventor?></h4>
		<h4><?php if (isset($machine->circa)) echo "Circa "; if (isset($machine->year)) echo $machine->year?></h4>
	</div>
	<?php endforeach;
	endif;?>

	<!-- The below pagination section is now longer user, however it remains here for reference-->
	<?php if (isset($total_pages)) { ?>
    <div class="pagination-container">
        <?php if ($page > 1 &&
        		  isset($query_array)) {
        	$query_array["page"] = $page - 1;
        	$url_parameters = http_build_query($query_array);
        	?>
        <a class="prev" href="results.php?<?php echo $url_parameters; ?>">&lt;</a>
        <?php } ?>
        <span class="count"><?php echo min($page, $total_pages) . " of " . $total_pages ?></span>
        <?php if ($page < $total_pages &&
        		  isset($query_array)) {

        	$query_array["page"] = $page + 1;
        	$url_parameters = http_build_query($query_array);?>
        <a class="next" href="results.php?<?php echo $url_parameters; ?>">&gt;</a>
        <?php } ?>
    </div>
    <?php } ?>

</div>

<?php require_once "includes/footer.php" ?>
