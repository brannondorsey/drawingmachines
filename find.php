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

	 $tags = file_get_contents($HOSTNAME . "/api/autocomplete.php?table=tags&column_name=tag&chars=#");
	 $tags = json_decode($tags);

	 remove_char_from_tags($tags, "#");
	 
	 $numb_tag_columns = 3;
	 $numb_tag_in_column = ceil(count($tags) / $numb_tag_columns);

	 $numb_category_columns = 5;
	 $numb_categories_in_column = ceil(count($categories) / $numb_category_columns);
?>
<script>

	function combineCheckboxes() {

		var commaSeperated = $('.special-tags-container .checkbox:checked').map(function() {
		    return this.value;
		}).get().join(' OR ');
		$('#form-tags input[type="hidden"]').val(commaSeperated);
		// console.log($('#form-tags input[type="hidden"]').val());
	}
</script>

<div class="content find">
	
	<div class="search-container container">
		<h3>Search</h3>
		<form action="" method="post">
			<input type="text" id="search">
			<input type="submit" value="search">
		</form>
	</div>

	
	<div class="special-tags-container container">
		<h3>"Im looking for a drawing machine that..."</h3>
			<?php if (count($tags) >= $numb_tag_columns): ?>
			<!-- <form action="results.php" method="get"> -->
			<div>
				<ul>
					<?php for ($i = 0; $i < $numb_tag_in_column; $i++):
							$tag = $tags[$i]; ?>
					<li>
						<a href="results.php?tags=<?php echo $tag->name?>"><?php echo $tag->name?></a>
						<!-- <input type="checkbox" name="tags" class="checkbox" value="<?php echo $tag->name?>"> -->
						<?php //echo $tag->name?>
					</li>
					<?php endfor ?>	
				</ul>
			</div>

			<div>
				<ul>
					<?php for (; $i < $numb_tag_in_column * 2; $i++):
							$tag = $tags[$i]; ?>
					<li>
						<a href="results.php?tags=<?php echo $tag->name?>"><?php echo $tag->name?></a>
						<!-- <input type="checkbox" name="tags" class="checkbox" value="<?php echo $tag->name?>"> -->
						<?php //echo $tag->name?>
					</li>
					<?php endfor ?>	
				</ul>
			</div>

			<div>
				<ul>
					<?php for (; $i < count($tags); $i++):
							$tag = $tags[$i]; ?>
					<li>
						<a href="results.php?tags=<?php echo $tag->name?>"><?php echo $tag->name?></a>
						<!-- <input type="checkbox" name="tags" class="checkbox" value="<?php echo $tag->name?>"> -->
						<?php //echo $tag->name?>
					</li>
					<?php endfor ?>	
				</ul>
			</div>

			
			<?php endif ?>

		<!-- <form action="results.php" id="form-tags" method="get" onsubmit="combineCheckboxes()">
			<input type="hidden" name="tags">
			<input type="submit" value="search">
		</form> -->
		
	</div>

	
	<div class="category-container container">
		<h3>Browse by Category</h3>
		<?php 
		sort($categories);
		$j = 0;
		for ($i = 0; $i < $numb_category_columns; $i++):
			$condition = ($i != $numb_category_columns - 1) ? $condition = $numb_categories_in_column * ($i + 1) : count($categories) - 1; 
		?>
		<div>
			<ul>
			<?php 
			for (; $j < $condition; $j++): 
					$category = $categories[$j];
			?>
				<li>
					<a href="results.php?categories=<?php if ($category->name != '') echo $category->name ?>"><?php if ($category->name != '') echo $category->name?></a>
				</li>
			<?php endfor ?>
			</ul>	
		</div>
		<?php endfor ?>
		
	</div>
</div>

<?php require_once "includes/footer.php" ?>