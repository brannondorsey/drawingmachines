<?php require_once "includes/header.php" ?>
<?php require_once "includes/menu.php";
	
	//get banner images
	$banner_image_dir = "images/banners";
	$banner_images = scandir($banner_image_dir);
	for ($i = 0; $i < count($banner_images); $i++) {
		if (strpos($banner_images[$i], ".png") === false) unset($banner_images[$i]);
	}
	shuffle($banner_images);
	
?>

<div class="content">
	<script>

		$(document).ready(function(){
			// Run our swapImages() function every 5secs
			setInterval('swapImages()', 5000);

			$('#banner-gallery img').click(function(){
				window.location.href = 'post.php?id=' + $(this).attr('data-id');
			});
		});

		function swapImages(){

		  var active = $('#banner-gallery .active');
		  var next = ($('#banner-gallery .active').next().length > 0) 
		  		? $('#banner-gallery .active').next() : $('#banner-gallery img:first');
		
		  active.fadeOut(650, function(){
		    active.removeClass('active');
		    next.fadeIn().addClass('active');
		  });
		}

	</script>
	<div id="banner-gallery">
		<?php for ($i = 0; $i < count($banner_images); $i++):?>
			<img src="<?php echo $banner_image_dir . "/" . $banner_images[$i]?>" 
			<?php if ($i == 0) echo "class=\"active\"" ?> 
			data-id="<?php echo (int) str_replace(".png", "", $banner_images[$i])?>"/>
		<?php endfor?>
	</div>
	<p>Looking for a drawing machine? Search by function or browse by category <a href="find.php">here</a>.</p>
</div>

<?php require_once "includes/footer.php" ?>