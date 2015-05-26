<?php



	function is_path_ok(){
		return file_exists( wpphpbbu\Path::prepare_phpbb_path(get_option( 'wpphpbbu_path', false )).'includes/functions_content.php');
	}
	function is_cache_ok(){
		$return = false;
		if(
			file_exists( plugin_dir_path(__FILE__).'cache/functions_content.php') && filesize(plugin_dir_path(__FILE__).'cache/functions_content.php') > 1024 
			&& file_exists( plugin_dir_path(__FILE__).'cache/common.php') && filesize(plugin_dir_path(__FILE__).'cache/common.php') > 1024
			&& file_exists( plugin_dir_path(__FILE__).'cache/functions_user.php') && filesize(plugin_dir_path(__FILE__).'cache/functions_user.php') > 1024
		)
				$return = true;
		return $return;
	}


/**
 * Return the administration link for phpBB
 */
function wpphpbbu_get_admin_link()
{
	global $auth, $user, $phpbb_root_path, $phpEx;

	if(is_object($auth) && method_exists($auth, 'acl_get') && $auth->acl_get('a_') && $user->data['is_registered'])
	{
		$forum_url = get_option('wppphpbbu_path');
		return $forum_url . 'adm/index.php?sid=' . $user->session_id;
	}
	else
	{
		return '';
	}
}

/**
 * Return the user avatar for WordPress from phpBB
 */
function wpphpbbu_get_avatar($ua = "", $tp = null)
{
	global $config, $user,$phpbb_container;
    
	$forum_url = get_option('wppphpbbu_path');
	
	$ret= phpbb_get_user_avatar($user->data);
	
	return $ret;
}

/**
 * Getting the avatar from phpBB for WordPress internal use
 */
function get_forum_avatar($avatar, $comment, $size)
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
				src="' . wpphpbbu_get_avatar($i['user_avatar'], $i['user_avatar_type']) . '" 
				class="avatar avatar-' . $size . ' photo" 
				height="' . $size . '" 
				width="' . $size . '"
			/>';
		}
    }
    
    return $avatar;
}

if(trim(get_option('wpphpbbu_avatars', 'no')) == 'yes')
{
    add_filter('get_avatar', 'get_forum_avatar', 1, 3);
}

/**
 * Generate Moderator Control Panel Link
 */
function wpphpbbu_get_mcp_link()
{
	global $auth, $user, $phpbb_root_path, $phpEx;

	if(is_object($auth) && method_exists($auth, 'acl_get') && $auth->acl_get('m_') && $user->data['is_registered'] && page_exists(trim(get_option('wpphpbbu_ucp_path'))))
	{
		$forum_url = get_option('wpphpbbu_path');
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
function wpphpbbu_get_restore_permissions_link()
{
	global $user, $auth;

	if($user->data['user_perm_from'] && is_object($auth) && method_exists($auth, 'acl_get') && $auth->acl_get('a_switchperm') && page_exists(trim(get_option('wpphpbbu_ucp_path'))))
	{
		return trim(get_option('wpphpbbu_ucp_path')) . '?mode=restore_perm&amp;sid=' . $user->session_id;
	}
	else
	{
		return '';
	}
}

/**
 * Reterrning the session id
 */
function wpphpbbu_get_sessionid()
{
	global $user;
	return $user->session_id;
}

/**
 * Return current time
 */
function wpphpbbu_get_time_current()
{
	global $user;

	if(wpphpbbu_is_active())
	{
		return sprintf($user->lang['CURRENT_TIME'], $user->format_date(time(), false, true));
	}

	return '';
}
/**
* Obtain either the members of a specified group, the groups the specified user is subscribed to
* or checking if a specified user is in a specified group. This function does not return pending memberships.
*
* Note: Never use this more than once... first group your users/groups
*/
function wpphpbbu_group_memberships($group_id_ary = false, $user_id_ary = false, $return_bool = false)
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
function wpphpbbu_get_time_last_visit()
{
	global $user;

	if (wpphpbbu_is_active() && wpphpbbu_is_user_logged_in())
	{
		return sprintf($user->lang['YOU_LAST_VISIT'], $user->format_date($user->data['session_last_visit']));
	}

	return '';
}

function is_200($url = "")
{
    if($url == "")
    {
        return false;
    }
    
    $options['http'] = array(
        'method' => "HEAD",
        'ignore_errors' => 1,
        'max_redirects' => 0
    );
    
    $body = file_get_contents($url, NULL, stream_context_create($options));
    sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $code);
    
    return $code === 200;
}


/**
 * Check if the user is logged in 
 */
function wpphpbbu_is_user_logged_in()
{
	global $user;

	if($user->data['user_id'] == ANONYMOUS)
	{
		return false;
	}

	return is_user_logged_in();
}

function aasort(&$array, $key, $r = false)
{
    $sorter = array();
    $ret = array();
    reset($array);
    
    foreach($array as $ii => $va)
    {
        $sorter[$ii] = $va[$key];
    }
    
    $r == true ? arsort($sorter) : asort($sorter);  
    
    foreach($sorter as $ii => $va)
    {
        $ret[$ii] = $array[$ii];
    }
    
    $array=$ret;
}

function print_forum($forums = null)
{
    global $wpphpbbu_categories;
    global $forums_categories;
    $forums_categories = (array)$forums_categories;
    
    static $b = 0;
    ++$b;
    
    static $times = 1;
    static $current_parent = 0;
    $current_parent = $forums['PARENT'];
    
    ?>
        <tr id="forum_<?php echo $forums['ID']; ?>" class="<?php echo $b % 2 == 0 ? "alternate " : "" ?>format-default" valign="top">
            <th scope="row" class="<?php echo $forums['TYPE'] == 0 ? "wpphpbbu_category" : "wpphpbbu_forum" ?>">
                <?php 
                    echo $forums['TYPE'] == 0 ? "" : '<div class="wpphpbbu_display_categories wpphpbbu_display_open"></div>'; 
                    echo ($forums['TYPE'] == 0 ? '' : str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $times)) . $forums['NAME'];
                    
                    if($forums['TYPE'] == 1)
                    {
                    ?>
                    <div class="wpphpbbu_categories">
                        <br />
                        <br />
                        <?php
                            $val = "";
                            
                            foreach($forums_categories as $fc)
                            {
                                if($fc['forum'] == $forums['ID'])
                                {
                                    $c_counter = 0;
                                    
                                    foreach($fc['categories'] as $c)
                                    {
                                        ++$c_counter;
                                        
                                        if($c_counter == 1)
                                        {
                                            $val = $c;
                                        }
                                        else
                                        {
                                            $val .= ", " . $c;
                                        }
                                    }
                                }
                            }
                        ?>
                        <input type="hidden" name="forum_categories" value="<?php echo $val; ?>" />
                        <input type="hidden" name="forum_id" value="<?php echo $forums['ID']; ?>" />
                        <?php
                            global $wpphpbbu_w;
                            print_array($wpphpbbu_w, $forums['ID']);
                        ?>
                        <br />
                    </div>
                    <?php
                    }
                    
                    foreach($forums as &$forum)
                    {
                        if(is_array($forum))
                        {
                            if($current_parent == 0)
                            {
                                $times = 1;
                            }
                            elseif($current_parent < $forum['PARENT'])
                            {
                                $times++;
                            }
                            elseif($current_parent > $forum['PARENT'])
                            {
                                $times--;
                            }
                                            
                            print_forum($forum);
                        }
                    }
                ?>
            </th>
        </tr>
    <?php
}

function print_array($array, $forum_id, $level = 0)
{
    if(!is_array($array)) return;
    
    $printed = false;

    foreach($array as $key => $value)
    {
        if(is_array($value))
        {
            print_array($value, $forum_id, $level);
    	}
        else
        {
            if($printed){continue;}
            
            global $forums_categories;
            
            foreach($forums_categories as $fc)
            {
                if($fc['forum'] == $forum_id)
                {
                    if(in_array($array['term_id'], $fc['categories']))
                    {
                        $ch = 'checked="checked"';
                    }
                }
            }
            
            $printed = true;
            $level++;
    	    echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
    ?>
        <label>
            <input type="checkbox" value="<?php echo $array['term_id']; ?>" <?php echo $ch; ?> /> <?php echo $array['name']; ?>
        </label>
        <br />
    <?php
        }
    }
}

function print_category_tree($c, $forum_id)
{
    global $forums_categories;
    
    static $times = 0;
    static $last_id = 0;
    
    foreach($c as $cat)
    {
        ?>
        <label>
            <input type="checkbox" value="<?php echo $cat['term_id']; ?>" <?php echo $ch; ?> /> <?php echo $cat['name']; ?>
        </label>
        <br />
        <?php
        
        foreach($cat as $categ)
        {
            if(is_array($categ))
            {
                $f = fopen(ABSPATH . '/log.txt');
                print_category_tree($categ, $forum_id);
            }
        }
    }
}

/**
 * Adding new post into Forum
 */
function add_post($id, $post)
{
    global $user, $phpbb_root_path, $phpEx, $wpdb;
    
    $current_user = wp_get_current_user();              // Get current user info
    
    // If the user cannot create posts on forum then return.
    // This info is comming from WordPress administration panel
    if(!in_array($current_user->data->ID, (array)unserialize(get_option('wpphpbbu_users_posting'))) && $post->post_status == "publish")
    {
        return;
    }
    
    // Check if that status of the current posts is publish
    if($post->post_status == "publish")
    {
        // Get the categories are assigned to that post
        $post_categories = wp_get_post_categories($id);
        // Get the full list of allowed forums to be posted
        $forum_categories = unserialize(get_option('wpphpbbu_forums_categories'));
        // Array that will hold the allowed forums IDs that will be created new post
        $add_to_forum = array();
        
        // Loop through the forums
        foreach($forum_categories as $set)
        {
            // Loop through the categories
            foreach($post_categories as $category)
            {
                // If category is into forums lists
                if(in_array($category, $set['categories']))
                {
                    // Check if the category already exists into the $add_to_forum
                    if(!in_array($set['forum'], $add_to_forum))
                    {
                        // Add the forum id into $add_to_forum
                        $add_to_forum[] = $set['forum'];
                    }
                }
            }
        }
        
        // Create definition in order that is not already defined
        if(!defined('IN_PHPBB'))
        {
            // Creating the definition
            define('IN_PHPBB', true);
        }
        
        // Import the utf tools from phpBB
        require($phpbb_root_path . 'includes/utf/utf_tools.php');
        
        // Check if seo version exists, if phpbb_seo class not already exists or if not $phpbb_seo is set
        if(file_exists($phpbb_root_path . 'phpbb_seo/phpbb_seo_class.php') && class_exists('phpbb_seo') == false && isset($phpbb_seo) == false)
        {
            global $phpbb_seo;                                                  // Create global variable $phpbb_set
            require($phpbb_root_path . 'phpbb_seo/phpbb_seo_class.php');        // Load phpbb seo version class
            $phpbb_seo = new phpbb_seo();                                       // Initiate a new phpbb_seo() object
        }
        
        // Import functions_posting.php
        require($phpbb_root_path . 'includes/functions_posting.php');
        
        $post_content = $post->post_content;                                    // Getting post content
        $post_title = $post->post_title;                                        // Getting post title
        $uid = $bitfield = $options = '';                                       // Set post options
        
        wp_generate_text_for_storage($post_content, $uid, $bitfield, $options, true, true, true);       // Process post content
        wp_generate_text_for_storage($post_title, $uid, $bitfield, $options, true, true, true);         // Process post title
        
        $poll = null;               // There is no poll
        
        $p = get_post($id);
        $current_title = $p->post_title;
        $current_hash = md5($p->post_content);
        unset($p);
        
        $post_exists_sql = "SELECT
          `p`.`topic_id` AS `TOPIC`,
          `p`.`forum_id` AS `FORUM`
        FROM
          `" . POSTS_TABLE . "` `p`
        WHERE
          `p`.`post_subject` = '" . $current_title . "'
        OR
          `p`.`post_checksum` = '" . $current_hash . "'";
          
        $post_exists = $wpdb->get_results($post_exists_sql);
                        
        // Loop through the allowed forums
        foreach($add_to_forum as $forum_id)
        {
            $topicId = 0;
            
            foreach($post_exists as $post_e)
            {
                if($forum_id == $post_e->FORUM)
                {
                    continue 2;
                    $topicId = $post_e->TOPIC;
                }
            }
            
            $data = array(
                'forum_id' => $forum_id,                    // Forum ID
                'topic_id' => $topicId,                     // 0 Create new post, seted ID updates the existing topic
                'icon_id' => false,                         // Set no icon
                'enable_bbcode' => true,                    // Allow bbCode
                'enable_smilies' => true,                   // Allow smilies
                'enable_urls' => true,                      // Allow urls
                'enable_sig' => true,                       // Allow SIG
                'message' => $post_content,                 // Set the post message
                'message_md5' => md5($post_content),        // Set the post hash
                'bbcode_bitfield' => $bitfield,             // Set the bitfield
                'bbcode_uid' => $uid,                       // Set the uid
                'post_edit_locked' => 0,                    // Set the post to unlocked
                'topic_title' => $post_title,               // Set the post title
                'notify_set' => false,                      // Set notify set to false
                'notify' => false,                          // Set notify to false
                'post_time' => 0,                           // Set post time to 0
                'forum_name' => '',                         // Set the name of forum
                'enable_indexing' => true,                  // Set indexing to true
                'force_approved_state' => true              // Set the posts as approved
            );
            
            // Submit the data here
            submit_post(
                'post',
                $post_title,
                $user->data['username'],
                POST_NORMAL,
                $poll,
                $data
            );
        }
    }
}

function wp_generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false)
{
    global $phpbb_root_path, $phpEx;

	$uid = $bitfield = '';
	$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) + (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) + (($allow_urls) ? OPTION_FLAG_LINKS : 0);

	if (!$text)
	{
		return;
	}

	if(!class_exists('parse_message'))
	{
		include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
	}

	$message_parser = new parse_message($text);
	$message_parser->parse($allow_bbcode, $allow_urls, $allow_smilies);

	$text = $message_parser->message;
	$uid = $message_parser->bbcode_uid;

	// If the bbcode_bitfield is empty, there is no need for the uid to be stored.
	if (!$message_parser->bbcode_bitfield)
	{
		$uid = '';
	}

	$bitfield = $message_parser->bbcode_bitfield;

	return;
}