<?php

namespace wpphpbbu\helpers;

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
