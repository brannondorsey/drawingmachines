<?php 

	 require_once "includes/database_connect.php";
	 require_once "includes/config.php";
	 require_once "includes/helpers.php";
	 require_once "includes/classes/markdown/Markdown.inc.php";

	 if (isset($_GET) &&
	 	 !empty($_GET)) {

	 	$query_array = Database::clean($_GET);
	 	$results_obj = json_decode($api->get_json_from_assoc($query_array));
	 	$page = (isset($_GET['page']) ? $_GET['page'] : 1);

	 	if (isset($results_obj->data)) {
	 		$numb_results = count($results_obj->data);
	 		$total_numb_results = total_numb_results($query_array, $api); //gives total number of pages
		    $total_pages = ceil($total_numb_results / $numb_results); //calculates total number of pages
		    if ($page > $total_pages) $page = $total_pages; //sets page to max page if page it exceeds it

	 	} else {
	 		//error or no results found
	 		
	 	}
	 } else header("Location: " . $HOSTNAME);

	 require_once "includes/header.php";
	 require_once "includes/menu.php";
	 require_once "includes/helpers.php"; 
?>
<script>
	$(document).ready(function(){
		$('.result').click(function(){
			window.location.href = <?php echo "'" . $HOSTNAME . "/post.php?id=" . "'"?> + $(this).attr('data-id');
		});
	});
</script>

<div class="content results-container">
	<h2>Showing results for "<?php 
		if (isset($query_array["tags"])) echo $query_array["tags"];
		else if (isset($query_array["primary_category"])) echo $query_array["primary_category"];
		else if (isset($query_array["secondary_category"])) echo $query_array["secondary_category"];
	?>"</h2>
	<?php 
	if (isset($results_obj->data)) :
		foreach($results_obj->data as $machine): 
	?>
	<div class="result" data-id="<?php echo $machine->id?>">
		<img src="<?php echo "images/machine_images/" . $machine->id . "/thumbnail.png"?>">
		<h3><?php if (isset($machine->device_name)) echo $machine->device_name?></h3>
		<h4><?php if (isset($machine->inventor)) echo $machine->inventor?></h4>
		<h4><?php if (isset($machine->circa)) echo "Circa "; if (isset($machine->year)) echo $machine->year?></h4>
	</div>
	<?php endforeach;
	else: echo "what";
	endif;?>

    <div class="pagination-container">
        <?php if ($page > 1) { ?>
        <a class="prev" href="results.php?search=<?php echo $search_string; ?>&amp;page=<?php echo ($page - 1); ?>">&lt;</a>
        <?php } ?>
        <span class="count"><?php echo min($page, $total_pages) . " of " . $total_pages ?></span>
        <?php if ($page < $total_pages) { ?>
        <a class="next" href="results.php?search=<?php echo $search_string; ?>&amp;page=<?php echo ($page + 1); ?>">&gt;</a>
        <?php } ?>
    </div>

</div>

<?php require_once "includes/footer.php" ?>