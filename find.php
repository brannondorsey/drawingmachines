<?php 

	 require_once "includes/database_connect.php";
	 require_once "includes/config.php";
	 require_once "includes/helpers.php";
	 require_once "includes/classes/markdown/Markdown.inc.php";

	 require_once "includes/header.php";
	 require_once "includes/menu.php";
	 require_once "includes/helpers.php"; 

	 $categories = file_get_contents($HOSTNAME . "/api/autocomplete.php?table=categories&column_name=category&chars=");
	 $categories = json_decode($categories);
	 
?>


<div class="content find">

	
	<div class"search-container">
		<h3>Search</h3>
		<form action="" method="post">
			<input type="text" id="search">
			<input type="submit" value="search">
		</form>
	</div>

	
	<div class="special-tags-container">
		<h3>Im looking for a drawing machine that...</h3>
		
	</div>

	
	<div class="category-container">
		<h3>Browse by Category</h3>
		<div>
			<?php 

			for ($i = 0; $i < count($categories); $i++): 
					$category = $categories[$i];
			?>
				<input type="checkbox" name="categories" value="<?php if ($category->name !== "") echo $category->name?>">
				<?php if ($category->name !== "") echo $category->name; ?>
			
			<?php endfor ?>
		</div>
		
	</div>
</div>

<?php require_once "includes/footer.php" ?>