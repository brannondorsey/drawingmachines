

<?php 
	 require_once "includes/header.php";
	 require_once "includes/menu.php";
	 require_once "includes/helpers.php"; 
	 require_once "includes/database_connect.php";
	 require_once "includes/config.php";
	 require_once "includes/classes/markdown/Markdown.inc.php";

	 if (isset($_GET['id']) &&
	 	 !empty($_GET['id'])) {

	 	$query_array = array(
	 		"id" => (int) $_GET["id"],
	 		"limit" => 1
	 	);

	 	$resultsObj = json_decode($api->get_json_from_assoc($query_array));
	 	if (isset($resultsObj->data[0])) {
	 		
	 		$machine = $resultsObj->data[0];
	 		//var_dump($machine);
	 		$images_dir = "images/machine_images/" . $machine->id;
	 		$has_images = is_dir($images_dir);
	 		
	 		if ($has_images) {

	 			$image_files = scandir($images_dir);
	 			$allowed_extensions = array("jpg", "png", "jpeg");
	 			$image_paths = array();

	 			foreach ($image_files as $image_file) {

	 				$temp = explode(".", $image_file);
					$extension = end($temp);

					//if file is an image
	 				if (in_array(strtolower($extension), $allowed_extensions)) {
	 					

	 					//if this is the main image
	 					if (strstr("main", strtolower($image_file)) !== false) {
	 						//prepend array
	 						array_unshift($image_paths, $images_dir . "/" . $image_file);
	 					} else $image_paths[] = $images_dir . "/" . $image_file; //add to end of array
	 				 					
	 				}
	 			}
	 		}
	 		

	 	} else {
	 		//error or not found
	 		//redirect...
	 	}
	 }
?>

<div class="content">
	<?php if (isset($image_paths)):  ?>
	<div class="image-container">
		<?php for ($i = 0; $i < 1; $i++): //change $i < i to $i < $image_paths
				$image_path = $image_paths[$i];
		?>
		<img src="<?php echo $image_path ?>" id="<?php echo ($i == 0) ? "main-image" : "thumb-image" ;?>">
	
		<?php endfor ?>
	</div>
	<?php endif ?>
	<div class="sidebar-container">
		<div>
			<h4>Download</h4>
			<ul>
				<li><a href="#">Small</a> [56KB .jpg]</li>
				<li><a href="#">Large</a> [1MB .jpg]</li>
				<li><a href="#">Original Resolution</a> [7MB .jpg]</li>
			</ul>
		</div>
		<?php if (isset($machine->primary_category)): ?>
		<div>
			<h4>Categories</h4>
			<span><a href="#"><?php echo $machine->primary_category?></a></span>
			<?php if (isset($machine->secondary_category)): ?><span><a href="#"><?php echo $machine->secondary_category?></a></span> <?php endif ?>

		</div>
		<?php endif ?>

		<?php if (isset($machine->tags)): ?>
		<div>
			<h4>Tags</h4>
			<?php if (isset($machine->tags)): 
				$tags_array = commas_to_array($machine->tags);
				foreach ($tags_array as $tag) { 
			?>
			<span><a href="#"><?php echo $tag;?></a></span>
			<?php
				}
			endif ?>
		</div>
		<?php endif ?>

	</div> 
	<div class="post-content-container">
		<h2><?php if (isset($machine->device_name)) echo $machine->device_name?></h2>
		<h3><?php if (isset($machine->inventor)) echo $machine->inventor; if (isset($machine->inventor_line_2)) echo " & " . $machine->inventor_line_2; ?></h3>
		<h4><?php if (isset($machine->circa)) echo "Circa "; if (isset($machine->year)) echo $machine->year?></h4>
		
		<p><?php if (isset($machine->post_content)) echo Michelf\Markdown::defaultTransform($machine->post_content) ?></p>

		<p class="machine-sources">
		<?php 
		if (isset($machine->source)) echo $machine->source . "<br>";
		if (isset($machine->source_line_2)) echo $machine->source_line_2;
		?>
		</p>

	</div>
</div>

<?php require_once "includes/footer.php" ?>