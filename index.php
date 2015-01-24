<?php 
	require_once "includes/classes/class.API.inc.php";
	require_once "includes/config.php";

	$category_columns = 
				"id,
  				category,
  				class,
  				description,
  				short_description";

  	//setup the API
  	$category_api = new API("localhost", 
  				   $DATABASE, 
  				   "categories", 
  				   $DATABASE_USER, 
  				   $DATABASE_PASSWORD);

  	$category_api->setup($category_columns);
  	$category_api->set_default_order("class");

  	$query_array = array('limit' => 50);

	$resultsObj = json_decode($category_api->get_json_from_assoc($query_array));
	
	$categories = array();
	$class_names = array();

	if (isset($resultsObj->data[0])) {

		$tmp = $resultsObj->data;
		
		for ($i = 0; $i < count($tmp); $i++) {
			array_push($class_names, $tmp[$i]->class);
		}

		$class_names = array_unique($class_names);
		$class_names = array_values($class_names);

		for ($i = 0; $i < count($class_names); $i++) {
			$categories[$class_names[$i]] = array();
		}

		for ($i = 0; $i < count($tmp); $i++) {
			$category = $tmp[$i];
			array_push($categories[$category->class], $category);
		}
	}

	function sub_class_text_hack($class_name) {
		$sub_text = "";
		switch (strtolower($class_name)) {
			case 'plotters':
				$sub_text = ' [Automated drawing output]';
				break;
			case 'virtual images':
				$sub_text = ' [Shadows or projected images for tracing]';
				break;
			case 'delineators':
				$sub_text = ' [Directly trace from life]';
				break;
			case 'coordinate translators':
				$sub_text = ' [Point-by-point translation from life]';
				break;	
		}
		return $sub_text;
	}

	require_once "includes/header.php";
	require_once "includes/menu.php";
?>

<script>
	$(document).ready(function(){
		$('.category-box').on('mouseout mouseover', function(evt){
			$(this).find('.category-short-description').toggleClass('hidden');
			$(this).find('img').toggleClass('image-fade');
		});
	});
</script>

<div class="content categories-layout">
	<?php 

	if (isset($class_names[0]) &&
		isset($class_names[1])) {

		// swap second to last category
		$tmp = $class_names[0];
		$class_names[0] = $class_names[1];
		$class_names[1] = $tmp;
	}
	
	for($i = 0; $i < count($class_names); $i++): 
		$class = $class_names[$i];?>
	<div class="category-class">
		<p class="category-class-name"><strong><?php echo strtoupper($class) ?></strong>&nbsp<?php echo sub_class_text_hack($class)?></p>
		<?php
		for ($j = 0; $j < count($categories[$class]); $j++):
			$category_obj = $categories[$class][$j];
			$image_safe_name = utf8_encode($category_obj->category);
			$image_safe_name = str_replace("/", "-", $category_obj->category);
			$image_safe_name = str_replace("ü", "u", $image_safe_name);
			$image_file = "images/category/thumbnail/" . $image_safe_name . " Thumb.png"?>
		<a href="category.php?id=<?php echo $category_obj->id?>">
			<div class="category-box" <?php if ($j % 4 == 0) echo 'style="margin-left: 0px"'?>>
				<img src="<?php echo $image_file?>">
				<div class="category-name">&nbsp<?php echo $category_obj->category ?></div>
				<div class="category-short-description hidden"><?php echo $category_obj->short_description ?></div>
				
			</div>
		</a>
	<?php endfor; ?>
	</div>
<?php endfor; ?>
</div>

<?php require_once "includes/footer.php" ?>