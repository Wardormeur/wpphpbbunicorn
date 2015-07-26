<?php
namespace wpphpbbu;

class User{

  static public function get_userid()
  {
    global $wpdb, $user;
    global $phpbb_container;

    $userid = 0;                                            // Set userid to 0;
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

    /**
     * Return the user avatar for WordPress from phpBB
     */
    static function get_avatar($ua = "", $tp = null)
    {
    	global $config, $user,$phpbb_container;

    	$forum_url = get_option('wppphpbbu_url');

    	$ret= phpbb_get_user_avatar($user->data);

    	return $ret;
    }

    /**
     * Getting the avatar from phpBB for WordPress internal use
     */
    static function get_forum_avatar($avatar, $comment, $size)
    {

        global $user, $db;

        if($comment->comment_author_email == '' || $comment->user_id == 0 || is_admin())
        {
            return $avatar;
        }
        else
        {
    		$qr = "SELECT u.`user_avatar`, u.`user_avatar_type` FROM `" . $table_prefix  . "users` u WHERE u.`user_email` = '" . $comment->comment_author_email . "'";
    		$rs = $db->sql_query($qr);

    		while($i = $rs->fetch_assoc())
    		{
    			$avatar = '<img
    				alt=""
    				src="' . get_avatar($i['user_avatar'], $i['user_avatar_type']) . '"
    				class="avatar avatar-' . $size . ' photo"
    				height="' . $size . '"
    				width="' . $size . '"
    			/>';
    		}
        }

        return $avatar;
    }


  /*
  * Obtain either the members of a specified group, the groups the specified user is subscribed to
  * or checking if a specified user is in a specified group. This function does not return pending memberships.
  *
  * Note: Never use this more than once... first group your users/groups
  */
  static function group_memberships($group_id_ary = false, $user_id_ary = false, $return_bool = false)
  {
  	global $db;

  	if (!$group_id_ary && !$user_id_ary)
  	{
  		return true;
  	}

  	if ($user_id_ary)
  	{
  		$user_id_ary = (!is_array($user_id_ary)) ? array($user_id_ary) : $user_id_ary;
  	}

  	if ($group_id_ary)
  	{
  		$group_id_ary = (!is_array($group_id_ary)) ? array($group_id_ary) : $group_id_ary;
  	}

  	$sql = 'SELECT ug.*, u.username, u.username_clean, u.user_email
  		FROM ' . USER_GROUP_TABLE . ' ug, ' . USERS_TABLE . ' u
  		WHERE ug.user_id = u.user_id
  			AND ug.user_pending = 0 AND ';

  	if ($group_id_ary)
  	{
  		$sql .= ' ' . $db->sql_in_set('ug.group_id', $group_id_ary);
  	}

  	if ($user_id_ary)
  	{
  		$sql .= ($group_id_ary) ? ' AND ' : ' ';
  		$sql .= $db->sql_in_set('ug.user_id', $user_id_ary);
  	}

  	$result = ($return_bool) ? $db->sql_query_limit($sql, 1) : $db->sql_query($sql);

  	$row = $db->sql_fetchrow($result);

  	if ($return_bool)
  	{
  		$db->sql_freeresult($result);
  		return ($row) ? true : false;
  	}

  	if (!$row)
  	{
  		return false;
  	}

  	$return = array();

  	do
  	{
  		$return[] = $row;
  	}
  	while ($row = $db->sql_fetchrow($result));

  	$db->sql_freeresult($result);

  	return $return;
  }

  /**
   * Return the last visited time
   */
  static function get_time_last_visit()
  {
  	global $user;

  	if (is_active() && is_user_logged_in())
  	{
  		return sprintf($user->lang['YOU_LAST_VISIT'], $user->format_date($user->data['session_last_visit']));
  	}

  	return '';
  }

  /**
   * Check if the user is logged in
   */
  static function is_user_logged_in()
  {
  	global $user;

  	if($user->data['user_id'] == ANONYMOUS)
  	{
  		return false;
  	}

  	return is_user_logged_in();
  }

}
