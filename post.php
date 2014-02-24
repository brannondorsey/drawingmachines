<?php 

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

					//if file is an image and not the thumbnail
	 				if (strstr(strtolower($image_file), "thumbnail") === false &&
	 					in_array(strtolower($extension), $allowed_extensions)) {
	 					
	 					//if this is the main image
	 					if (strstr( strtolower($image_file), "main") !== false) {

	 						//prepend array
	 						array_unshift($image_paths, $images_dir . "/" . $image_file);
	 					} else $image_paths[] = $images_dir . "/" . $image_file; //add to end of array
	 				 					
	 				}
	 			}
	 		}
	 		

	 	} else {
	 		//error or not found
	 		header("Location: " . $HOSTNAME);
	 		
	 	}
	 } else header("Location: " . $HOSTNAME);

	 require_once "includes/header.php";
	 require_once "includes/menu.php";
	 require_once "includes/helpers.php"; 
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
		<img src="<?php echo $image_path ?>" class="thumbnail <?php if ($i == 0) echo "selected"?>">
	
		<?php endfor ?>
	</div>
	<?php endif ?>
	<div class="sidebar-container">
		<div>
			<h4>Download Images</h4>
			<ul>
				<li><a href="#">Small</a> [56KB .jpg]</li>
				<li><a href="#">Large</a> [1MB .jpg]</li>
				<li><a href="#">Original Resolution</a> [7MB .jpg]</li>
			</ul>
		</div>
		<?php if (isset($machine->categories)):
			$categories = commas_to_array($machine->categories);
		?>
		<div>
			<h4>Categories</h4>
			<?php foreach ($categories as $category):?>
			<span><a href="results.php?categories=<?php echo $category?>&limit=10&order_by=date"><?php echo $category?></a></span>
			<?php endforeach?>

		</div>
		<?php endif ?>

		<?php if (isset($machine->tags)): ?>
		<div>
			<h4>Tags</h4>
			<?php if (isset($machine->tags)): 
				$tags_array = commas_to_array($machine->tags);
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