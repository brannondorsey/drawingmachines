	  <footer>

	    <p>
	    	A project by <a href="http://pablogarcia.org">Pablo Garcia</a>. Website by <a href="http://brannondorsey.com">Brannon Dorsey</a>.
	    	<?php 

	    	if (method_exists("Session", "is_logged_in") &&
	    		Session::is_logged_in()) { ?>
	    	<a id="admin-login" href="logout.php">Logout</a>
	    	<?php } else { ?>
	    	<a id="admin-login" href="admin.php">Admin</a>
	    	<?php } ?>
	    </p>

	    <span id="copyright">
	    	This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
	    	<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/us/">
	    		<img src="images/copyright_image.jpg">
	    	</a>
	    </span>
	  </footer>

	</body>
</html>