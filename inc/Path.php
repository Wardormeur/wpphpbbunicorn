<?php
namespace wpphpbbu;
class Path{

	static function is_path_ok(){
		return file_exists( get_option( 'wpphpbbu_path', false ).'includes/functions_content.php');
	}


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


	/**
	 * Return the administration link for phpBB
	 */
	static function get_admin_link()
	{
		global $auth, $user, $phpbb_root_path, $phpEx;

		if(is_object($auth) && method_exists($auth, 'acl_get') && $auth->acl_get('a_') && $user->data['is_registered'])
		{
			$forum_url = get_option('wppphpbbu_url');
			return $forum_url . 'adm/index.php?sid=' . $user->session_id;
		}
		else
		{
			return '';
		}
	}


	/**
	 * Generate Moderator Control Panel Link
	 */
	static function get_mcp_link()
	{
		global $auth, $user, $phpbb_root_path, $phpEx;

		if(is_object($auth) && method_exists($auth, 'acl_get') && $auth->acl_get('m_') && $user->data['is_registered'] )
		{
			$forum_url = get_option('wpphpbbu_url');
			return $forum_url . 'mcp.php?i=main&amp;mode=front&amp;sid=' . $user->session_id;
		}
		else
		{
			return '';
		}
	}

	/**
	 * Generate restore permissions link
	 */
	static function get_restore_permissions_link()
	{
		global $user, $auth;

		if($user->data['user_perm_from'] && is_object($auth) && method_exists($auth, 'acl_get') && $auth->acl_get('a_switchperm') )
		{
			return trim(get_option('wpphpbbu_ucp_path')) . '?mode=restore_perm&amp;sid=' . $user->session_id;
		}
		else
		{
			return '';
		}
	}


}
