<?php 
	require_once "includes/classes/class.API.inc.php";
	require_once "includes/config.php";
	require_once "includes/helpers.php";
	require_once "includes/classes/markdown/Markdown.inc.php";

	$category_columns = 
				"id,
  				category,
  				class,
  				description";

  	//setup the API
  	$category_api = new API("localhost", 
  				   $DATABASE, 
  				   "categories", 
  				   $DATABASE_USER, 
  				   $DATABASE_PASSWORD);

  	$category_api->setup($category_columns);
  	$category_api->set_default_order("class");
  	
  	$category_obj = NULL;
  	$machine_results = NULL;

  	if (isset($_GET['id'])&&
  		!empty($_GET['id'])) {

  		$query_array = array('id' => (int) $_GET['id'], 'limit' => 1);
	 	$results_obj = json_decode($category_api->get_json_from_assoc($query_array));

		if (isset($results_obj->data[0])) {
			$category_obj = $results_obj->data[0];
		}

		// now for the machine results using the regular api
		require_once "includes/database_connect.php";

		$query_array = array(
			'category' => $category_obj->category,
			'order_by' => 'year',
			'limit' => 10);

	 	$results = json_decode($api->get_json_from_assoc($query_array));

	 	if (isset($results->data[0])) {
	 		$machine_results = $results->data;
	 	}
	}

	if ($category_obj == NULL) header( 'Location: ' . $HOSTNAME);

	$image_safe_name = str_replace("/", "-", $category_obj->category);
	$image_file = "images/category/thumbnail/" . $image_safe_name . " Thumb.png";

	require_once "includes/header.php";
	require_once "includes/menu.php";

?>

<div class="content category-layout">
	<h3><?php echo $category_obj->category ?></h3>
	<div class="category">
		<img src="<?php echo $image_file?>" class="category-image" />
		<?php echo Michelf\Markdown::defaultTransform($category_obj->description) ?>
		<p>Click <a href="results.php?category=<?php echo $category_obj->category?>&exact=true">here</a> to browse by this category.</p>
	</div>
	<?php if ($machine_results != NULL): ?>
	<div class="machine-results-container">
		<?php foreach($machine_results as $machine):?>
		<div class="result" data-id="<?php echo $machine->id?>">
			<img src="<?php echo get_machine_thumbnail($machine)?>">
			<h3><?php if (isset($machine->device_name)) echo $machine->device_name?></h3>
			<h4><?php if (isset($machine->inventor)) echo $machine->inventor?></h4>
			<h4><?php if (isset($machine->circa)) echo "Circa "; if (isset($machine->year)) echo $machine->year?></h4>
		</div>
		<?php endforeach ?>
		<p>Click <a href="results.php?category=<?php echo $category_obj->category?>&exact=true">here</a>
		 to view more machines in this category.</p>
	</div>
	<?php endif ?>
</div>

<?php require_once "includes/footer.php"; ?>
