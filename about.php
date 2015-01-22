<?php 
	 require_once "includes/classes/markdown/Markdown.inc.php";

	 require_once "includes/header.php";
	 require_once "includes/menu.php";

	 $markdown_content = Michelf\Markdown::defaultTransform(file_get_contents("markdown_content/about.md"));
?>
<link rel="stylesheet" type="text/css" href="styles/markdown_content.css">
<div class="content">
	<?php echo $markdown_content ?>
</div>
<?php require_once "includes/footer.php" ?>