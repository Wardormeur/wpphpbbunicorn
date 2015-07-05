<?php
namespace wpphpbbu;
class Path{


	static function login_page() {
		global $phpbb_root_path;
			$page = basename($_SERVER['REQUEST_URI']);
		if( strpos($page,"wp-login.php")!== false && get_option('wpphpbbu_url',false)) {
			wp_redirect(get_option('wpphpbbu_url',false) .'ucp.php?mode=login&redirect=' . urlencode(get_bloginfo('home')),301);
			exit;//seriously, when documentation requires this one, it demonstrate how much it suxx
		}
	}


	static function logout_page() {
		global $user;
		// we have got the sessions, destroy them all! WP on's are ahndled normally, so we destroy the phpbb one
		$user->session_kill();
		wp_redirect(get_bloginfo('home'));
		exit;//Sorry for anyone trying to plug after this

	}
}
