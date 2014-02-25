<?php
	require_once 'includes/classes/class.Session.inc.php';
	require_once 'includes/classes/class.Database.inc.php';
	Session::start();
	Session::destroy();
	header("Location: index.php", TRUE);
?>