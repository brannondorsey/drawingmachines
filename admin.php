<?php 
	
	//class includes
	require_once 'includes/classes/class.FormValidator.php';

	//content includes
	require_once 'includes/header.php';
	require_once 'includes/menu.php';
	require_once 'includes/database_connect.php';

	if(isset($_POST) &&
	   !empty($_POST)){

		$post = Database::clean($_POST);

		$rules = array(

	        'device_name'=>array('display'=>'Name of Device', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
	        'inventor'=>array('display'=>'Inventor', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
	        'inventor_line_2'=>array('display'=>'Inventor line 2', 'type'=>'string',  'required'=>false, 'min'=>5, 'max'=>50, 'trim'=>true),
	        'year'=>array('display'=>'Year', 'type'=>'numeric',  'required'=>true, 'min'=>1, 'max'=>9999, 'trim'=>true),
	        'primary_category'=>array('display'=>'Primary Category', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
	        'secondary_category'=>array('display'=>'Secondary Category', 'type'=>'string','required'=>false, 'min'=>6, 'max'=>50, 'trim'=>true),
	        'post_content'=>array('display'=>'Post Content', 'type'=>'string', 'min'=>1, 'max'=>999999, 'required'=>true, 'trim'=>true),
	        'tags'=>array('display'=>'tags', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>255, 'trim'=>true),
	        'source'=>array('display'=>'Source', 'type'=>'string',  'required'=>false, 'min'=>2, 'max'=>255, 'trim'=>true),
	        'source_line_2'=>array('display'=>'Source line 2', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>255, 'trim'=>true)
    	);
		
		$validator = new FormValidator();
		$validator->addSource($post);
		$validator->addRules($rules);
		$validator->run();

		//if form validation fails
		if(sizeof($validator->errors) > 0) {
			var_dump($validator->errors);

			//add new tags to database
			if($isset($post_array['tags'])){
				
				$autocomplete = new Autocomplete('tag', 'tags');
		        $autocomplete->add_list_to_table($post_array['tags']);
			}

			//add new categories to database
			if($isset($post_array['add_categories'])){
				
				$autocomplete = new Autocomplete('category', 'categories');
		        $autocomplete->add_list_to_table($post_array['add_categories']);
			}
			
		}else{ //form meets validation rules

			if(Database::execute_from_assoc($post, Database::$table)){
				//results saved...
				echo "results saved!";
			}
		}
	}
?>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
<script>

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

	<form method="post" target="" id="machine-post">
		<h2>Machine Post</h2>

		<button id="form-delete" onclick="return false;">Delete</button>
		<fieldset id="form-load-post" class="label-side" >
			<label for="form-load-post">Load Post #</label>
			<input type="number" min="1" name="load_post" style="width: 70px; margin-right: 5px;">
			<button onclick="return false;">Load</button>
		</fieldset>

		<fieldset>
			<label for="form-name">Name of Device</label>
			<input id="form-name"  type="text" name="device_name" data-id="0">
		</fieldset>

		<fieldset>
			<label for="form-inventor">Inventor</label>
			<input id="form-inventor" type="text" name="inventor">
		</fieldset>

		<fieldset>
			<label for="form-inventor-2">Inventor line 2 (optional)</label>
			<input id="form-inventor-2" type="text" name="inventor_line_2">
		</fieldset>

		<fieldset class="label-side">
			<label for="form-circa">Circa</label>
			<input id="form-circa" type="checkbox" name="circa" value="true">
		
			<label for="form-year">Year</label>
			<input id="form-year" type="number" min="1" max="9999" name="year">
		</fieldset>

		<fieldset class="label-side">
			<label for="form-primary-category">Primary Category</label>
			<select id="form-primary-category" name="primary_category">
				<option value="test">test</option>
			</select>
		</fieldset>

		<fieldset class="label-side">
			<label for="form-secondary-category">Secondary Category (optional)</label>
			<select id="form-secondary-category" name="secondary_category">
				<option value="test">test</option>
			</select>
		</fieldset>
		
		<fieldset style="width: 96.5%">
			<label for="form-post-content">Post Content (in markdown)</label>
			<textarea id="form-post-content" name="post_content"></textarea>
		</fieldset>

		<fieldset >
			<label for="form-tags">Tags (seperated by commas)</label>
			<input id="form-tags" type="text" name="tags">
		</fieldset>

		<fieldset>
			<label for="form-source">Source</label>
			<input id="form-source" type="text" name="source">
		</fieldset>

		<fieldset>
			<label for="form-source-2">Source line 2 (optional)</label>
			<input id="form-source-2" type="text" name="source_line_2">
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
			<input id="form-new-category"type="text" name="add_categories">
		</fieldset>

		<fieldset class="half">
			<label for="form-delete-category">Remove Categories</label>
			<input id="form-delete-category" type="text" name="delete_categories">
		</fieldset>

		<input type="submit" style="margin-top: 20px;" value="Update Categories">
	</form>
</div>

<?php require_once 'includes/footer.php' ?>
