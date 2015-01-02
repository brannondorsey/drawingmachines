<?php 
	require_once "includes/classes/class.API.inc.php";
	require_once "includes/config.php";

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

	require_once "includes/header.php";
	require_once "includes/menu.php";
?>

<script>
	$(document).ready(function(){
		$('.category-box').on('mouseout mouseover', function(evt){
			// $(this).find('.category-name').toggleClass('hidden');
		});
	});
</script>

<div class="content categories-layout">
	<h3 style="margin-top: 0;">Browse by category</h3>
	<?php 
	for($i = 0; $i < count($class_names); $i++): 
		$class = $class_names[$i];?>
	<div class="category-class">
		<p class="category-class-name"><?php echo $class?></p>
		<?php
		for ($j = 0; $j < count($categories[$class]); $j++):
			$categoryObj = $categories[$class][$j];
			$image_safe_name = utf8_encode($categoryObj->category);
			$image_safe_name = str_replace("/", "-", $categoryObj->category);
			$image_file = "images/category/thumbnail/" . $image_safe_name . " Thumb.png"?>
		<a href="category.php?id=<?php echo $categoryObj->id?>">
			<div class="category-box">
				<div class="category-name">&nbsp<?php echo $categoryObj->category ?></div>
				<img src="<?php echo $image_file?>">
			</div>
		</a>
	<?php endfor; ?>
	</div>
<?php endfor; ?>
</div>

<?php require_once "includes/footer.php" ?>