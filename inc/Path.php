<?php
namespace wpphpbbu;
class Path{
  
	
	static function login_page() {
		global $phpbb_root_path;
		$page = basename($_SERVER['REQUEST_URI']);
		if( strpos($page,"wp-login.php")!== false ) {
			wp_redirect(self::prepare_phpbb_path($phpbb_root_path) .'ucp.php?mode=login&redirect=' . urlencode(get_bloginfo('home')));
		}
	}


	static function logout_page() {
		global $user;
		// we have got the sessions, destroy them all! WP on's are ahndled normally, so we destroy the phpbb one
		$user->session_kill();
		wp_redirect(get_bloginfo('home'));
		exit;//Sorry for anyone trying to plug after this
		
	}
	
	
	
	
	/*Create a ful relative path that works on every page*/
	static function prepare_phpbb_path($phpbb_root_path){
			$thispath = explode('\\', str_replace('/','\\', dirname(__FILE__)));
			$rootpath = explode('\\', str_replace('/','\\', dirname($_SERVER["SCRIPT_FILENAME"])));
			$relpath = array();
			$dotted = 0;
			for ($i = 0; $i < count($rootpath); $i++) {
				if ($i >= count($thispath)) {
					$dotted++;
				}
				elseif ($thispath[$i] != $rootpath[$i]) {
					$relpath[] = $thispath[$i]; 
					$dotted++;
				}
			}
		return $GLOBALS['phpbb_root_path'] = $phpbb_root_path =str_repeat('../', $dotted).$phpbb_root_path;	
	}
}