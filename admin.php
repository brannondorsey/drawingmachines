<?php 
	
	//class and helper includes
	require_once 'includes/classes/class.Session.inc.php';
	require_once 'includes/classes/class.FormValidator.php';
	require_once 'includes/classes/class.Autocomplete.php';
	require_once 'includes/helpers.php';
	
	require_once 'includes/database_connect.php';

	Session::start();

	// this should be checked first
	if (isset($_GET['image_error']) &&
		!empty($_GET['image_error'])) {
		$image_error = true;
	}

	if(isset($_POST) &&
	   !empty($_POST)){

		$post = Database::clean($_POST);

		//handles loggin if not logged in and autorization code is posted and correct
		if (!Session::is_logged_in() &&
			isset($post['auth_user']) &&
			isset($post['auth_code']) &&
			$post['auth_user'] == $ADMIN_USER &&
			$post['auth_code'] == $ADMIN_PASSWORD){
			Session::login();

		} else { //admin is logged in
		
			$rules = array();
			//if the post is from a machine post
			if(isset($post['device_name'])){

			      	$rules['device_name'] = array('display'=>'Name of Device', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>50, 'trim'=>true);
			        $rules['inventor'] = array('display'=>'Inventor', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>50, 'trim'=>true);
			        $rules['inventor_line_2'] = array('display'=>'Inventor line 2', 'type'=>'string',  'required'=> false, 'min'=>2, 'max'=>50, 'trim'=>true);
			        $rules['year'] = array('display'=>'Year', 'type'=>'numeric',  'required'=> true, 'min'=>1, 'max'=>9999, 'trim'=>true);
			        $rules['category'] = array('display'=>'Category', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>50, 'trim'=>true);
			        $rules['post_content'] = array('display'=>'Post Content', 'type'=>'string', 'min'=>1, 'max'=>999999, 'required'=> true, 'trim'=>true);
			        $rules['tags'] = array('display'=>'tags', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>255, 'trim'=>true);
			        $rules['source'] = array('display'=>'Source', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>255, 'trim'=>true);
			        $rules['source_line_2'] = array('display'=>'Source line 2', 'type'=>'string', 'required'=> false, 'min'=>1, 'max'=>255, 'trim'=>true);
			}

			//if post is from a new categories
			if(isset($post['add_categories']) &&
			   !empty($post['add_categories'])){
				$rules['add_categories'] = array('display' => 'Add Categories', 'type'=>'string',  'required'=>false, 'min'=>2, 'max'=>99999, 'trim'=>true);
			}

			//if post is to remove a new categories
			if(isset($post['delete_categories']) &&
			   !empty($post['delete_categories'])){
				$rules['delete_categories'] = array('display' => 'Remove Categories', 'type'=>'string',  'required'=>false, 'min'=>2, 'max'=>99999, 'trim'=>true);
			}
			
			$validator = new FormValidator();
			$validator->addSource($post);
			$validator->addRules($rules);
			$validator->run();

			//if form validation fails
			if(sizeof($validator->errors) <= 0){ //form meets validation rules
				
				//add new tags to database
				if(isset($post['tags'])){
					$autocomplete = new Autocomplete('tag', 'tags');
			        $autocomplete->add_list_to_table($post['tags']);
				}

				//add new categories to database
				if(isset($post['add_categories'])){
					$autocomplete = new Autocomplete('category', 'categories');
			        $autocomplete->add_list_to_table($categories);
			        $categories_saved = true;
				}

				//delete categories from database
				if(isset($post['delete_categories'])){
					$autocomplete = new Autocomplete('category', 'categories');
			        $autocomplete->add_list_to_table($categories);
			        $categories_saved = true;
				}

				//add post content to database
				if(isset($post['device_name'])){

					$id = NULL;

					//if this post was loaded instead of new
					if(intval($post['id']) != 0){

						$id = $post['id'];
						unset($post['id']);
						$query = "UPDATE " . Database::$table . " SET ";
						foreach($post as $key => $value){
							$query .= $key . "=\"" . $value . "\", ";
						}
						$query = rtrim($query, ", ");
						$query .= " WHERE id=\"" . $id . "\"";
						// echo $query;
						
						if(Database::execute_sql($query)){
							//results saved...
							$post_saved = true;
						}
					} else { //if this is a new post

						unset($post['id']);
						if(Database::execute_from_assoc($post, Database::$table)){
							//results saved...
							$post_saved = true;
							$query = 'SELECT id FROM ' . Database::$table . ' ORDER BY id DESC LIMIT 1';
							$results = Database::get_all_results($query);
							$id = (int) $results[0]['id'];
						}
					}

					//save images
					if ($id != NULL &&
						isset($_FILES) &&
						!empty($_FILES)) {

						foreach ($_FILES as $key => $value) {

							$file_upload_success = false;

							if ($_FILES[$key]["error"] == 0) {

								$images_dir = "images/machine_images";
								if (!file_exists($images_dir . "/" . $id)) mkdir($images_dir . "/" . $id);
								$file_upload_success = move_uploaded_file($_FILES[$key]["tmp_name"], $images_dir . "/" . $id . "/" . $_FILES[$key]["name"]);
								
							} else $file_upload_success = false;

							if ($file_upload_success) unset($image_error);
							else header("Location: " . $HOSTNAME . "/admin.php?post=" . $id . "&image_error=true");
						}
					}
				}
			}
		}
	}

	if (Session::is_logged_in()) {

		if (isset($_GET['post']) &&
		   !empty($_GET['post'])) {
		
		 	$api_array = array(
		 		'id' => intval($_GET['post']),
		 		'limit' => '1'
			);

		 	$results = json_decode($api->get_json_from_assoc($api_array));
		 	
		 	if (isset($results->data)) {
		 		$loaded_post_obj = $results->data[0];
		 	}
		}

		if (isset($_GET['delete']) &&
		   !empty($_GET['delete'])) {

			$query = "DELETE FROM " . Database::$table . " WHERE id='" . (int) $_GET['delete'] . "' LIMIT 1";
			if (Database::execute_sql($query)) {
				$post_deleted = true;
				shell_exec('rm -rf images/machine_images/' . (int) $_GET['delete']);
			} else $post_delete_error = true;
		}
	}

	//content includes
	require_once 'includes/header.php';
	require_once 'includes/menu.php';

	if (Session::is_logged_in()) {
?>

<link rel="stylesheet" type="text/css" href="styles/autosuggest.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
<script type="text/javascript" src="scripts/jquery.autosuggest.minified.js"></script>
<script>
	var hostname = <?php echo '"' . $HOSTNAME . '"'; ?>;
	var tagsPreFill = <?php if (isset($post['tags'])) echo '"' . $post['tags'] . '"';
							else if (isset($loaded_post_obj)) echo  '"' . $loaded_post_obj->tags . '"';
							else echo '""'; ?>;
	var postSaved = <?php echo (isset($post_saved)) ? "true" : "false"?>;

	$(document).ready(function(){

		//tags autosuggest
		$('input[name="tags"]').autoSuggest(hostname + "/api/autocomplete.php", 
			{
				asHtmlID: "tags-input",
				queryParam: "chars",
				extraParams: "&column_name=tag&table=tags",
				startText: "",
				preFill: tagsPreFill,
				resultsHighlight: false,
				retrieveComplete: function(data){
					return data;
				}
			}
		);

		//categories autosuggest
		$('input[name="add_categories"]').autoSuggest(hostname + "/api/autocomplete.php",
			{
				asHtmlID: "add-categories-input",
				queryParam: "chars",
				extraParams: "&column_name=category&table=categories",
				startText: "",
				resultsHighlight: false,
				retrieveComplete: function(data){
					return data;
			}
		});

		//categories autosuggest
		$('input[name="delete_categories"]').autoSuggest(hostname + "/api/autocomplete.php",
			{
				asHtmlID: "delete-categories-input",
				queryParam: "chars",
				extraParams: "&column_name=category&table=categories",
				startText: "",
				resultsHighlight: false,
				retrieveComplete: function(data){
					return data;
			}
		});

		$('#as-values-tags-input').attr('name', 'tags');
		$('#as-values-add-categories-input').attr('name', 'add_categories');
		$('#as-values-delete-categories-input').attr('name', 'delete_categories');

		if(postSaved){
			$('#machine-post').find('input[type="text"], input[type="hidden"], input[type="number"], textarea').val('');
			$('ul.as-selections li.as-selection-item').remove(); // reset autoSuggest fields
			console.log('done');
		}

	});

	function addImage(){

		var imageFieldset = $('.image-upload').last();
		var imageUploadContainer = $('#image-upload-container');
		var imageNumber = imageUploadContainer.children().length;
		imageNumber++;
		var html = '<fieldset><input id="image-' + imageNumber + '" type="file" name="image-' + imageNumber + '"></fieldset>';
		$('#image-upload-container').append(html);
	}

	function loadPost(){
		var val = $('#form-load-post #load-post').val();
		if(val != ''){
			var url = window.location.href;
			var queryIndex = url.indexOf('?');
			if(queryIndex != -1){
				url = url.substring(0, queryIndex); //remove old get params	
			}
			window.location.href = url + '?post=' + val;
		}else return false;
	}

	function deletePost(){
		var val = $('#machine-post input[name="id"]').val();
		if(val != ''){
			var url = window.location.href;
			var queryIndex = url.indexOf('?');
			if(queryIndex != -1){
				url = url.substring(0, queryIndex); //remove old get params	
			}
			window.location.href = url + '?delete=' + val;
		} else { //clear the current form
			console.log('got here');
			window.location = window.location.href.split("?")[0]; //reload
		}

		return false; //don't submit the form
	}
</script>

<div class="content">

	<?php 
		if (isset($validator->errors) &&
			 sizeof($validator->errors) > 0) {
			echo "<p class='error' style='text-align:center'>Oops, looks like there were some errors with your post. Check out the asterisks.</p>";
		}
		if (isset($post_saved)) {
			echo "<p class='success' style='text-align:center'>Post Saved, click <a href='post.php?id=" . $id . "' target='_blank' style='color:inherit;'>here</a> to view.</p>";
		}
		if (isset($categories_saved)) {
			echo "<p class='success' style='text-align:center'>Categories Updated</p>";
		}
		if (isset($post_deleted)) {
			echo "<p class='success' style='text-align:center'>Post Deleted</p>";
		}
		if (isset($post_delete_error)) {
			echo "<p class='error' style='text-align:center'>Post Not Deleted</p>";
		}
		if (isset($image_error)) {
			echo "<p class='error' style='text-align:center'>Error saving images</p>";
		}
	?>

	<h2>Machine Post</h2>

	<form method="post" target="" id="form-load-post"  class="admin" style="border:none">
		<fieldset id="form-load-post" class="label-side" >
			<label for="load-post">Load Post #</label>
			<input id="load-post" type="number" min="1" name="load_post" style="width: 70px; margin-right: 5px;">
			<input type="button" value="Load" onclick="return loadPost();">
		</fieldset>

		<button id="form-delete" onclick="return deletePost()">Delete</button>
	</form>

	<form method="post" enctype="multipart/form-data" target="" id="machine-post" class="admin">

		<input type="number" name="id" value="<?php echo isset($loaded_post_obj) ? $loaded_post_obj->id : "" ; ?>" hidden>

		<fieldset>
			<label for="form-name">Name of Device <?php if(isset($validator->erros['device_name'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-name"  type="text" name="device_name" data-id="0" value="<?php if(isset($post['device_name'])) echo $post['device_name']; else if(isset($loaded_post_obj)) echo $loaded_post_obj->device_name?>">
		</fieldset>

		<fieldset>
			<label for="form-inventor">Inventor <?php if(isset($validator->errors['inventor'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-inventor" type="text" name="inventor" value="<?php if(isset($post['inventor'])) echo $post['inventor']; else if(isset($loaded_post_obj)) echo $loaded_post_obj->inventor?>">
		</fieldset>

		<fieldset>
			<label for="form-inventor-2">Inventor line 2 (optional) <?php if(isset($validator->errors['inventor_line_2'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-inventor-2" type="text" name="inventor_line_2" value="<?php if(isset($post['inventor_line_2'])) echo $post['inventor_line_2']; else if(isset($loaded_post_obj->inventor_line_2)) echo $loaded_post_obj->inventor_line_2?>">
		</fieldset>

		<fieldset class="label-side">
			<label for="form-circa">Circa <?php if(isset($validator->errors['circa'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-circa" type="checkbox" name="circa" value="1" <?php if(isset($post['circa']) && $post['circa'] == '1') echo "checked"; else if(isset($loaded_post_obj->circa) && $loaded_post_obj->circa == '1') echo "checked"?> >
		
			<label for="form-year">Year <?php if(isset($validator->errors['year'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-year" type="number" min="1" max="9999" name="year" value="<?php if(isset($post['year'])) echo $post['year']; else if(isset($loaded_post_obj)) echo $loaded_post_obj->year?>">
		</fieldset>

		<?php 
			$categories = json_decode(file_get_contents($HOSTNAME . "/api/autocomplete.php?column_name=category&table=categories&chars="));
		?>
		<fieldset class="label-side">
			<label for="form-category">Category <?php if(isset($validator->errors['category'])) echo "<spand class='error'>*</span>"; ?></label>
			<select id="form-category" name="category" value="<?php if(isset($post['category'])) echo $post['category']?>" >
				<?php foreach($categories as $category_obj){ ?>
				<option value="<?php echo $category_obj->value ?>" <?php if(isset($post['category']) && $post['category'] == $category_obj->value) echo "selected"; else if(isset($loaded_post_obj) && $loaded_post_obj->category == $category_obj->value) echo "selected"; ?> > <?php echo $category_obj->name; ?></option> 
				<?php }?>
			</select>
		</fieldset>
		<?php ?>
		<fieldset style="width: 96.5%">
			<label for="form-post-content">Post Content (in markdown) <?php if(isset($validator->errors['post_content'])) echo "<spand class='error'>*</span>"; ?></label>
			<textarea id="form-post-content" name="post_content"><?php if(isset($post['post_content'])) echo $post['post_content']; else if(isset($loaded_post_obj)) echo $loaded_post_obj->post_content; ?></textarea>
		</fieldset>

		<fieldset>
			<label for="form-tags">Tags (seperated by commas) <?php if(isset($validator->errors['tags'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-tags" type="text" name="tags">
		</fieldset>

		<fieldset>
			<label for="form-source">Source <?php if(isset($validator->errors['source'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-source" type="text" name="source" value="<?php if(isset($post['source'])) echo $post['source']; else if(isset($loaded_post_obj)) echo $loaded_post_obj->source; ?>">
		</fieldset>

		<fieldset>
			<label for="form-source-2">Source line 2 (optional) <?php if(isset($validator->errors['source_line_2'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-source-2" type="text" name="source_line_2" value="<?php if(isset($post['source_line_2'])) echo $post['source_line_2']; else if(isset($loaded_post_obj->source_line_2)) echo $loaded_post_obj->source_line_2; ?>">
		</fieldset>

		<div id="image-upload-container">

			<fieldset class="image-upload">
				<label for="image-1">Images</label>
				<!--<input id="image-1" type="file" name="image-1">-->
			</fieldset>

				<?php 
			if (isset($_GET['post']) &&
				!empty($_GET['post'])) {

				$image_dir = 'images/machine_images/' . (int) $_GET['post'];
				
				if (file_exists($image_dir)) {
				
				$image_names = preg_grep('/^([^.])/', scandir($image_dir));
				?>
				<div>
					<?php foreach ($image_names as $image_name) { ?>
					<img src="<?php echo $image_dir . "/" . $image_name?>" class="machine-image">
					<?php } ?>
				</div>
			<?php }
			} ?>
		</div>

		<button onclick="addImage(); return false;">Add Image</button>

		<input type="submit" value="Save">

	</form>

	<!-- <form id="image-upload" enctype="multipart/form-data" class="admin" action="">
		<div id="image-upload-container">

			<fieldset class="image-upload">
				<label for="image-1">Images</label> -->
				<!--<input id="image-1" type="file" name="image-1">-->
<!-- 			</fieldset>
		</div>
		<button onclick="addImage(); return false;">Add Image</button>
	</form> -->

	<!-- <form method="post" target="" id="manage-categories" class="admin">

		<h2>Manage Categories</h2>

		<p>Fields accept single values or comma-delimited lists</p>

		<fieldset class="half">
			<label for="form-new-category">Add Categories</label>
			<input id="form-new-category"type="text" name="add_categories" autocomplete="off">
		</fieldset>

		<fieldset class="half">
			<label for="form-delete-category">Remove Categories</label>
			<input id="form-delete-category" type="text" name="delete_categories">
		</fieldset>

		<input type="submit" style="margin-top: 20px;" value="Update Categories">
	</form> -->
</div>
<?php } else require_once 'includes/login_form.php'; ?>
<?php require_once 'includes/footer.php' ?>
