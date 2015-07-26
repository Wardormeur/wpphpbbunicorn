<?php
namespace wpphpbbu;
class Session{

    // Initiate the phpBB session
    public function load_session_id()
    {
  		global $user, $auth, $phpbb_container, $phpbb_extension_manager;
  		$user->session_begin();
      $auth->acl($user->data);


  		if(!\wpphpbbu\User::is_user_logged_in()){

  			$userid = \wpphpbbu\User::get_userid();                                          // Get user ID

        // $user->setup(false,false);
  			if($userid > 0 )                                         // If user ID is bigger than 0 and user ID is not equal with the current user ID
  			{
  				wp_clear_auth_cookie();
  				$wpuser = wp_set_current_user($userid);                                       // Set the current user
  				wp_set_auth_cookie($userid, true, false);
  			}
      }
      // Return current user session id
      return $user->session_id;
    }

    /**
     * Returning the session id
     */
    static function get_sessionid()
    {
    	global $user, $auth;

    	return $user->session_id;
    }

}
