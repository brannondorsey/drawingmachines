<?php 

	 require_once "includes/database_connect.php";
	 require_once "includes/config.php";
	 require_once "includes/classes/markdown/Markdown.inc.php";
	 require_once "includes/helpers.php";

	 if (isset($_GET['id']) &&
	 	 !empty($_GET['id'])) {

	 	$query_array = array(
	 		"id" => (int) $_GET["id"],
	 		"limit" => 1
	 	);

	 	$results_obj = json_decode($api->get_json_from_assoc($query_array));

	 	if (isset($results_obj->data[0])) {
	 		
	 		$machine = $results_obj->data[0];
	 	
	 		$images_dir = "images/machine/" . $machine->id . "/web";
	 		$has_images = is_dir($images_dir);
	 		
	 		if ($has_images) {

	 			$image_files = scandir($images_dir);
	 			$allowed_extensions = array("jpg", "png", "jpeg");
	 			$image_paths = array();

	 			foreach ($image_files as $image_file) {

	 				$temp = explode(".", $image_file);
					$extension = end($temp);

					//if file is an image and not the thumbnail
	 				if (in_array(strtolower($extension), $allowed_extensions)) {

	 					//if this is the main image
	 					if (strstr( strtolower($image_file), "main") !== false) {
	 						//prepend array
	 						array_unshift($image_paths, $images_dir . "/" . $image_file);

	 					} else $image_paths[] = $images_dir . "/" . $image_file; //add to end of array				
	 				}
	 			}
	 		}

	 		$bundle_dir = "images/machine/" . $machine->id . "/bundle";
	 		$has_bundles = false;
	 		$bundle_names = array();
	 		$bundle_sizes = array();

	 		if (is_dir($bundle_dir)) $bundle_names = preg_grep('/^([^.])/', scandir($bundle_dir));
	 		if (!empty($bundle_names)) { 
	 			$has_bundles = true;
	 			foreach ($bundle_names as $bundle_name) {
	 				$bundle_sizes[$bundle_name] = filesize_formatted($bundle_dir . "/" . $bundle_name);
	 			}
	 		}
	 	
	 	} else {
	 		//error or not found
	 		header("Location: " . $HOSTNAME);
	 		
	 	}
	 } else header("Location: " . $HOSTNAME);

	 require_once "includes/header.php";
	 require_once "includes/menu.php";
?>
<script>
	$(document).ready(function(){
		
		$('.image-container .thumbnail').click(function() {
			
		    var state = $(this).is(':selected');
		    $('.image-container .thumbnail').removeClass('selected');
		    $(this).addClass('selected', state );
		    swapImage($(this).attr('src'));
		});
	});

	function swapImage(src){
		$('.image-container .main').attr('src', src);
	}

</script>
<div class="content">
	<?php if (isset($image_paths)):  ?>
	<div class="image-container">
		<img src="<?php echo $image_paths[0] ?>" class="main">
		<?php for ($i = 0; $i < count($image_paths); $i++): //change $i < i to $i < $image_paths
				$image_path = $image_paths[$i];
		?>
		<?php if (count($image_paths) > 1):?>
		<img src="<?php echo $image_path ?>" class="thumbnail <?php if ($i == 0) echo "selected"?>">
		<?php endif ?>
	
		<?php endfor ?>
	</div>
	<?php endif ?>
	<div class="sidebar-container">
		<?php if (isset($has_bundles) && $has_bundles): ?>
		<div>
			<h4>Download Files</h4>
			<ul>
				<?php foreach($bundle_names as $bundle_name) {
					echo "<li><a href='" . $bundle_dir . "/" . $bundle_name . "'>$bundle_name</a> [" . $bundle_sizes[$bundle_name] . "]</li>";
				} ?>
			</ul>
		</div>
		<?php endif?>
		<?php if (isset($machine->category)):
		?>
		<div>
			<h4>Category</h4>
			<span><a href="results.php?category=<?php echo $machine->category?>&limit=10&order_by=date"><?php echo $machine->category?></a></span>
		</div>
		<?php endif ?>

		<?php if (isset($machine->tags)): ?>
		<div>
			<h4>Tags</h4>
			<?php if (isset($machine->tags)): 
				$tags_array = commas_to_array($machine->tags);

				for ($i = 0; $i < count($tags_array); $i++) {
					 $tags_array[$i] = ucfirst(ltrim(($tags_array[$i]), "#"));
				}

				foreach ($tags_array as $tag) { 
			?>
			<span><a href="results.php?tags=<?php echo $tag?>&limit=10&order_by=date"><?php echo $tag;?></a></span>
			<?php
				}
			endif ?>
		</div>
		<?php endif ?>

	</div> 
	<div class="post-content-container">
		<h2><?php if (isset($machine->device_name)) echo htmlspecialchars($machine->device_name) ?></h2>
		<h3><?php if (isset($machine->inventor)) echo $machine->inventor; if (isset($machine->inventor_line_2)) echo " " . $machine->inventor_line_2; ?></h3>
		<h4><?php if (isset($machine->circa)) echo "Circa "; if (isset($machine->year)) echo $machine->year?></h4>
		
		<p><?php if (isset($machine->post_content)) echo Michelf\Markdown::defaultTransform($machine->post_content) ?></p>

		<p class="machine-sources">
		<?php 
		$sources = "";
		if (isset($machine->source)) $sources .= $machine->source;
		if (isset($machine->source_line_2)) $sources .= "<br>" . $machine->source_line_2;
		if ($sources != "") echo Michelf\Markdown::defaultTransform($sources);
		?>
		</p>

	</div>
</div>

<?php require_once "includes/footer.php" ?>