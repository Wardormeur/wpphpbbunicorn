<?php
namespace wpphpbbu;
class Session{

    // Initiate the phpBB session
    public function load_session_id()
    {
  		global $user, $auth,$phpbb_container;

  		$user->session_begin();


  		if(!is_user_logged_in()){

  			$userid = $this->get_userid();                                          // Get user ID

  			if($userid > 0 )                                         // If user ID is bigger than 0 and user ID is not equal with the current user ID
  			{
  				wp_clear_auth_cookie();
  				$wpuser = wp_set_current_user($userid);                                       // Set the current user
  				wp_set_auth_cookie($userid, true, false);
  			}
          }else{
  			//session_pagestart();
  		}
          // Return current user session id
          return $user->session_id;
    }

    /**
     * Reterrning the session id
     */
    static function get_sessionid()
    {
    	global $user;
    	return $user->session_id;
    }

}
