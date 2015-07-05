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

    public function get_userid()
    {
      global $wpdb, $user;
	    global $phpbb_container;

      $userid = 0;                                            // Set userid to 0;
      //var_dump($s_user);
  		// If current user type is normal user or the current user type is founder
  		if($user->data['user_type'] == USER_NORMAL || $user->data['user_type'] == USER_FOUNDER)
      {
        // List all users ID's where having meta_key of phpbb_userid and meta_value equal to current user id
        $stat= $wpdb->prepare(
            "SELECT ID FROM $wpdb->users WHERE user_nicename = %s",
            $user->data['username_clean'] );

  			$id_list = $wpdb->get_col($stat);

  			//should return only 1, this is a security failure
  			if(!empty($id_list))
        {
                  $userid = $id_list[0];
        }
      }
		  return $userid;
    }
}
