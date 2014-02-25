<?php

class Session {

	public static $auth_code = "";

	public static function start(){
		$session_cookie_lifetime = 72; //in hours
		$lifetime_in_seconds = 60 * 60 * $session_cookie_lifetime;
		session_set_cookie_params($lifetime_in_seconds);
	    session_start();
	    session_regenerate_id(true);
	}

	public static function destroy(){
		session_destroy();
		session_unset();
		$_SESSION = array(); //uncomment this if session is persisting on page
	}

	public static function check_loggin_credentials($_auth_code){
		return ($_auth_code == $auth_code) ? true : false;
	}

	//adds assoc array values to $_SESSION superglobals on success, returns false on failure
	public static function login(){
		$_SESSION['logged_in'] = "true";
	}

	public static function is_logged_in(){
		return (isset($_SESSION['logged_in'])) ? true : false;
	}
}

?>