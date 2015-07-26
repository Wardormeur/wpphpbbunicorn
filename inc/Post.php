<?php

namespace wpphpbbu;
class Post{

  static function add_post($id = null, $add_to_forums = [])
  {
      global $user, $phpbb_root_path, $phpEx, $phpbb_log, $wpdb;

      //we save the forums association
      update_post_meta( $id, 'wpphpbbu_forums', $add_to_forums );

      $post = get_post($id);
      $current_user = wp_get_current_user();              // Get current user info

      // If the user cannot create posts on forum then return.
      // This info is comming from WordPress administration panel
      if(!current_user_can('post_to_forum') || $post->post_status !== "publish")
      {
          return;
      }

      if( $post->post_status == "publish" ){
          // Import functions_posting.php
          require_once($phpbb_root_path . 'includes/functions_posting.php');

          $post_content = $post->post_content;                                    // Getting post content
          $post_title = $post->post_title;                                        // Getting post title
          $uid = $bitfield = $options = '';                                       // Set post options

          generate_text_for_storage($post_content, $uid, $bitfield, $options, true, true, true);       // Process post content
          generate_text_for_storage($post_title, $uid, $bitfield, $options, true, true, true);         // Process post title

          $poll = null;               // There is no poll

          $p = get_post($id);
          $current_title = $p->post_title;
          $current_hash = md5($p->post_content);


          //
          // Loop through the allowed forums
          foreach($add_to_forums as $forum_id)
          {
              $edit = get_post_meta($id ,'wpphpbbu_topic_id',true);
              $topicId = $edit ? (int)$edit : 0 ;
              // var_dump($topicId);
              // die();

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
                  'post_time' => time(),                           // Set post time to 0
                  'forum_name' => '',                         // Set the name of forum
                  'enable_indexing' => true,                  // Set indexing to true
                  'force_approved_state' => true              // Set the posts as approved
              );
              if ($edit){
                $data['post_id'] = get_post_meta($id,'wpphpbbu_post_id',true);
                $data['post_subject'] = $data['topic_title'];
                $data['post_edit_user'] = $data['poster_id'] = $user->data['user_id'];

              }
              // Submit the data here
              submit_post(
                  ($edit ? 'edit' : 'post'),
                  $post_title,
                  $user->data['username'],
                  POST_NORMAL,
                  $poll,
                  $data
              );
              var_dump($data);
              $topic_id = $data['topic_id'];
              update_post_meta($id,'wpphpbbu_topic_id',$topic_id);
              $topic_id = $data['post_id'];
              update_post_meta($id,'wpphpbbu_post_id',$topic_id);
          }
          unset($p);
      }
  }

}
