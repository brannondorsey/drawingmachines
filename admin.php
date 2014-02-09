<?php 
	
	//class and helper includes
	require_once 'includes/classes/class.FormValidator.php';
	require_once 'includes/classes/class.Autocomplete.php';
	require_once 'includes/helpers.php';


	//content includes
	require_once 'includes/header.php';
	require_once 'includes/menu.php';
	
	require_once 'includes/database_connect.php';

	if(isset($_POST) &&
	   !empty($_POST)){

		$post = Database::clean($_POST);

		//if the post is from a machine post
		if(isset($post['device_name'])){
			$rules = array(

		        'device_name'=>array('display'=>'Name of Device', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>50, 'trim'=>true),
		        'inventor'=>array('display'=>'Inventor', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>50, 'trim'=>true),
		        'inventor_line_2'=>array('display'=>'Inventor line 2', 'type'=>'string',  'required'=> false, 'min'=>2, 'max'=>50, 'trim'=>true),
		        'year'=>array('display'=>'Year', 'type'=>'numeric',  'required'=> true, 'min'=>1, 'max'=>9999, 'trim'=>true),
		        'primary_category'=>array('display'=>'Primary Category', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>50, 'trim'=>true),
		        'secondary_category'=>array('display'=>'Secondary Category', 'type'=>'string','required'=> false, 'min'=>2, 'max'=>50, 'trim'=>true),
		        'post_content'=>array('display'=>'Post Content', 'type'=>'string', 'min'=>1, 'max'=>999999, 'required'=> true, 'trim'=>true),
		        'tags'=>array('display'=>'tags', 'type'=>'string',  'required'=> true, 'min'=>2, 'max'=>255, 'trim'=>true),
		        'source'=>array('display'=>'Source', 'type'=>'string',  'required'=> false, 'min'=>2, 'max'=>255, 'trim'=>true),
		        'source_line_2'=>array('display'=>'Source line 2', 'type'=>'string', 'required'=> true, 'min'=>1, 'max'=>255, 'trim'=>true)
	    	);
		}

		//if post is from a new categories
		if(isset($post['add_categories'])){
			$rules = array(
				'add_categories' => array('display' => 'Add Categories', 'type'=>'string',  'required'=>false, 'min'=>2, 'max'=>99999, 'trim'=>true),
				'delete_categories' => array('display' => 'Remove Categories', 'type'=>'string',  'required'=>false, 'min'=>2, 'max'=>99999, 'trim'=>true)
			);
		}
		
		$validator = new FormValidator();
		$validator->addSource($post);
		$validator->addRules($rules);
		$validator->run();

		//var_dump($post);

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
		        $autocomplete->add_list_to_table($post['add_categories']);
			}

			//add post content to database
			if(isset($post['device_name'])){
				
				if($test = Database::execute_from_assoc($post, Database::$table)){
					//results saved...
					$post_saved = true;
				}
			}
		}
	}
?>

<link rel="stylesheet" type="text/css" href="styles/autosuggest.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
<script type="text/javascript" src="scripts/jquery.autosuggest.minified.js"></script>
<script>
	var hostname = <?php echo '"' . $HOSTNAME . '"'; ?>;
	$(document).ready(function(){

		//tags autosuggest
		$('input[name="tags"]').autoSuggest(hostname + "/api/autocomplete.php", 
			{
				asHtmlID: "tags-input",
				queryParam: "chars",
				extraParams: "&column_name=tag&table=tags",
				startText: "",
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
	});

	function addImage(){

		var imageFieldset = $('.image-upload').last();
		var imageUploadContainer = $('#image-upload-container');
		var imageNumber = imageUploadContainer.children().length;
		imageNumber++;
		var html = '<fieldset><input id="image-' + imageNumber + '" type="file" name="image-' + imageNumber + '"></fieldset>';
		$('#image-upload-container').append(html);
	}

</script>

<div class="content">

	<?php 
		if(isset($validator->errors) &&
			 sizeof($validator->errors) > 0){
			echo "<p class='error' style='text-align:center'>Oops, looks like there were some errors with your post. Check out the asterisks.</p>";
		}
		if(isset($post_saved)){
			echo "<p class='success' style='text-align:center'>Post Saved</p>";
		}
	?>

	<h2>Machine Post</h2>

	<form method="post" target="" id="form-load-post" style="border:none">
		<fieldset id="form-load-post" class="label-side" >
			<label for="form-load-post">Load Post #</label>
			<input type="number" min="1" name="load_post" style="width: 70px; margin-right: 5px;">
			<button onclick="return false;">Load</button>
		</fieldset>

		<button id="form-delete" onclick="return false;">Delete</button>
	</form>

	<form method="post" target="" id="machine-post">

		<fieldset>
			<label for="form-name">Name of Device <?php if(isset($validator->erros['device_name'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-name"  type="text" name="device_name" data-id="0" value="<?php if(isset($post['device_name'])) echo $post['device_name']?>">
		</fieldset>

		<fieldset>
			<label for="form-inventor">Inventor <?php if(isset($validator->errors['inventor'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-inventor" type="text" name="inventor" value="<?php if(isset($post['inventor'])) echo $post['inventor']?>">
		</fieldset>

		<fieldset>
			<label for="form-inventor-2">Inventor line 2 (optional) <?php if(isset($validator->errors['inventor_line_2'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-inventor-2" type="text" name="inventor_line_2" value="<?php if(isset($post['inventor_line_2'])) echo $post['inventor_line_2']?>">
		</fieldset>

		<fieldset class="label-side">
			<label for="form-circa">Circa <?php if(isset($validator->errors['circa'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-circa" type="checkbox" name="circa" value="1" <?php if(isset($post['circa']) && $post['circa'] == '1') echo "checked"; ?> >
		
			<label for="form-year">Year <?php if(isset($validator->errors['year'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-year" type="number" min="1" max="9999" name="year" value="<?php if(isset($post['year'])) echo $post['year']?>">
		</fieldset>

		<fieldset class="label-side">
			<label for="form-primary-category">Primary Category <?php if(isset($validator->errors['primary_category'])) echo "<spand class='error'>*</span>"; ?></label>
			<select id="form-primary-category" name="primary_category" value="<?php if(isset($post['primary_category'])) echo $post['primary_category']?>">
				<option value="test">test</option>
			</select>
		</fieldset>

		<fieldset class="label-side">
			<label for="form-secondary-category">Secondary Category (optional) <?php if(isset($validator->errors['secondary_category'])) echo "<spand class='error'>*</span>"; ?></label>
			<select id="form-secondary-category" name="secondary_category" value="<?php if(isset($post['secondary_category'])) echo $post['secondary_category']?>">
				<option value="test">test</option>
			</select>
		</fieldset>
		
		<fieldset style="width: 96.5%">
			<label for="form-post-content">Post Content (in markdown) <?php if(isset($validator->errors['post_content'])) echo "<spand class='error'>*</span>"; ?></label>
			<textarea id="form-post-content" name="post_content" value="<?php if(isset($post['post_content'])) echo $post['post_content']?>"></textarea>
		</fieldset>

		<fieldset>
			<label for="form-tags">Tags (seperated by commas) <?php if(isset($validator->errors['tags'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-tags" type="text" name="tags" value="<?php if(isset($post['tags'])) echo $post['tags']?>">
		</fieldset>

		<fieldset>
			<label for="form-source">Source <?php if(isset($validator->errors['source'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-source" type="text" name="source" value="<?php if(isset($post['source'])) echo $post['source']?>">
		</fieldset>

		<fieldset>
			<label for="form-source-2">Source line 2 (optional) <?php if(isset($validator->errors['source_line_2'])) echo "<spand class='error'>*</span>"; ?></label>
			<input id="form-source-2" type="text" name="source_line_2" value="<?php if(isset($post['source_line_2'])) echo $post['source_line_2']?>">
		</fieldset>

		<div id="image-upload-container">

			<fieldset class="image-upload">
				<label for="image-1">Images</label>
				<input id="image-1" type="file" name="image-1">
			</fieldset>
		</div>
		<button onclick="addImage(); return false;">Add Image</button>
		<input type="submit" value="Save">

	</form>

	<form method="post" target="" id="manage-categories">

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
	</form>
</div>

<?php require_once 'includes/footer.php' ?>
