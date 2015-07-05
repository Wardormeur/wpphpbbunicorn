<?php

namespace wpphpbbu\widgets;
class UsersWidget extends \WP_Widget
{

	function __construct() {
		$widget_ops = array(
				'classname' => 'phpBB3 Users Widget',
				'description' => __('Use that widget to allow user login on phpBB and WordPress from the WordPress side', 'wpphpbbu')
		);

		$control_ops = array(
				'width' => 250,
				'height' => 250,
				'id_base' => 'phpbb3-users-widget'
		);

		parent::__construct( 				'phpbb3-users-widget',
						'WP phpBB Bridge ' . __('Users', 'wpphpbbu'),
						$widget_ops,
						$control_ops
 				);
	}

    function form($instance)
    {
			$defaults = array(
            'wpphpbbu_user_login_title' => __('Login', 'wpphpbbu'),
            'wpphpbbu_user_info_title' => '{USERNAME}',
            'wpphpbbu_user_show_meta' => 'yes'
        );

        $instance = wp_parse_args(
                        (array)$instance,
                        $defaults
                    );

        ?>
        <div class="widget-content">
            <p>
                <label for="<?php echo $this->get_field_id('wpphpbbu_user_login_title'); ?>">
                    <?php _e('Login box title:', 'wpphpbbu'); ?>
                </label>
                <input class="widefat" id="<?php echo $this->get_field_id('wpphpbbu_user_login_title') ?>" name="<?php echo $this->get_field_name('wpphpbbu_user_login_title') ?>" type="text" value="<?php echo $instance['wpphpbbu_user_login_title']; ?>" />
                <span class="description">
                    <?php
                        _e('The widget title before the user login', 'wpphpbbu');
                    ?>
                </span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('wpphpbbu_user_info_title'); ?>">
                    <?php _e('User info title:', 'wpphpbbu'); ?>
                </label>
                <input class="widefat" id="<?php echo $this->get_field_id('wpphpbbu_user_info_title') ?>" name="<?php echo $this->get_field_name('wpphpbbu_user_info_title') ?>" type="text" value="<?php echo $instance['wpphpbbu_user_info_title']; ?>" />
                <span class="description">
                    <?php
                        _e('The widget title after the user login.<br /><br /><strong>NOTE</strong>: You can use the keyword {USERNAME} to display the current username. In exmple: Welcome {USERNAME}', 'wpphpbbu');
                    ?>
                </span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('wpphpbbu_user_show_meta'); ?>">
                    <input type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_user_show_meta'); ?>" name="<?php echo $this->get_field_name('wpphpbbu_user_show_meta'); ?>" value="yes" <?php echo $instance['wpphpbbu_user_show_meta'] == "yes" ? 'checked="checked"' : ''; ?> /> <?php _e('Display user meta info', 'wpphpbbu'); ?>
                </label>
            </p>
        </div>
        <?php
	}

	function update($new_instance, $old_instance)
    {
		$instance = $old_instance;

        $instance['wpphpbbu_user_login_title'] = $new_instance['wpphpbbu_user_login_title'];
        $instance['wpphpbbu_user_info_title'] = $new_instance['wpphpbbu_user_info_title'];
        $instance['wpphpbbu_user_show_meta'] = $new_instance['wpphpbbu_user_show_meta'];

        return $instance;
	}

	function widget($args, $instance)
    {


			global $user, $auth,$phpbb_container;

      extract($args);

      $login_title = $instance['wpphpbbu_user_login_title'];
    	$info_title = $instance['wpphpbbu_user_info_title'];
    	$meta_links = $instance['wpphpbbu_user_show_meta'];

      $user_status = wpphpbbu_is_user_logged_in();

      $title = '';

      if($user_status == false)
      {
          $title = $login_title;
      }
      else
      {
          $title = $info_title;
      }

      $title = str_replace('{USERNAME}', trim($user->data['username']), $title);

      $forum_url = $ucp_url = get_option('wpphpbbu_path');
      $ucp_url .= 'ucp.php';

      echo $before_widget . $before_title . $title . $after_title;

      if($user_status == false)
      {
      	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      	$meta_links = $instance['wpphpbbu_user_show_meta'];

          ?>
              <form action="<?php echo $ucp_url; ?>?mode=login" method="post" class="wp_phpbb_bridge_login" id="login">
                  <?php

                      if(get_option('wpphpbbu_width', '0') !== '0')
                      {

                  ?>
                      <input type="hidden" name="wpphpbbu_elements_width" id="wpphpbbu_elements_width" value="<?php echo get_option('wpphpbbu_width'); ?>" />
                  <?php

                      }

                  ?>
                  <label for="username">
                      <?php
                          echo _e('Username:', 'wpphpbbu');
                      ?>
                  </label>
                  <br />
                  <input type="text" name="username" id="wpphpbbu_username" />
                  <br />
                  <label for="password">
                      <?php
                          echo _e('Password:', 'wpphpbbu');
                      ?>
                  </label>
                  <br />
                  <input type="password" id="wpphpbbu_password" name="password" />
                  <br />
                  <label for="autologin">
                      <input type="checkbox" name="autologin" id="autologin" />
                      <?php
                          echo _e('Remember me', 'wpphpbbu');
                      ?>
                  </label>
                  <br />
                  <label for="viewonline">
                      <input type="checkbox" name="viewonline" id="viewonline" />
                      <?php
                          echo _e('Login as hidden', 'wpphpbbu');
                      ?>
                  </label>
                  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                  <br />
              	<input type="submit" name="login" id="wpphpbbu_login" value="<?php echo _e('Login', 'wpphpbbu'); ?>" />
                  <?php
                      if($meta_links == "yes")
                      {
                  ?>
                      <br />
                      <a href="<?php echo $ucp_url; ?>?mode=sendpassword">
                          <?php
                              echo _e('I forgot my password', 'wpphpbbu');
                          ?>
                      </a>
                      <br />
                      <a href="<?php echo $ucp_url; ?>?mode=register">
                          <?php
                              echo _e('Register new account', 'wpphpbbu');
                          ?>
                      </a>
                  <?php
                      }
                  ?>
              </form>
          <?php
        }
        else
        {
          $avatar = wpphpbbu_get_avatar();
        	$admin_url = wpphpbbu_get_admin_link();
          $mcp_url = wpphpbbu_get_mcp_link();
        	$options = get_option('widget_wpb_user');
        	$meta_links = $instance['wpphpbbu_user_show_meta'];

          if($avatar)
          {
              ?>
                  <a href="<?php echo $ucp_url; ?>" title="<?php _e('User control panel', 'wpphpbbu'); ?>">
                      <?php echo $avatar ?>
                  </a>
              <?php
          }

          ?>
              <ul id="wp_phpbb_bridge_options">
                  <li>
                      <?php _e('Total posts', 'wpphpbbu'); ?> :
                      <a href="<?php echo $forum_url; ?>search.php?search_id=egosearch">
                          <?php
                              echo number_format($user->data['user_posts']);
                          ?>
                      </a>
                  </li>
                  <li>
                      <?php _e("New PM's", 'wpphpbbu'); ?> :
                      <a href="<?php echo $ucp_url; ?>?i=pm&amp;folder=inbox">
                          <?php
                              echo number_format($user->data['user_new_privmsg']);
                          ?>
                      </a>
                  </li>
                  <li>
                      <?php _e("Unread PMs", 'wpphpbbu'); ?> :
                      <a href="<?php echo $ucp_url; ?>?i=pm&amp;folder=inbox">
                          <?php
                              echo number_format($user->data['user_unread_privmsg']);
                          ?>
                      </a>
                  </li>
                  <?php
                      if($meta_links == "yes")
                      {
                          if(!empty($admin_url))
                          {
                              ?>
                                  <li>
                                      <a href="<?php echo $admin_url; ?>">
                                          <?php
                                              _e('Forum administration', 'wpphpbbu');
                                          ?>
                                      </a>
                                  </li>
                              <?php
                          }
                      }

                      if(wpphpbbu_is_user_logged_in())
                      {
                          ?>
                              <li>
                                  <a href="<?php echo get_option('home'); ?>/wp-admin/">
                                      <?php
                                          echo _e('Blog control panel', 'wpphpbbu');
                                      ?>
                                  </a>
                              </li>
                              <li>
                                  <a href="<?php echo $ucp_url; ?>">
                                      <?php
                                          echo _e('User control panel', 'wpphpbbu');
                                      ?>
                                  </a>
                              </li>
                          <?php
                              if(!empty($mcp_url))
                              {
                                  ?>
                                      <li>
                                          <a href="<?php echo $mcp_url; ?>">
                                              <?php
                                                  echo _e('Moderator control panel', 'wpphpbbu');
                                              ?>
                                          </a>
                                      </li>
                                  <?php
                              }

                              if(!empty($permission_url))
                              {
                                  ?>
                                      <li>
                                          <a href="<?php echo $permission_url; ?>">
                                              <?php
                                                  echo _e('Restore permissions', 'wpphpbbu');
                                              ?>
                                          </a>
                                      </li>
                                  <?php
                              }
                          ?>
                              <li>
                                  <a href="<?php echo $ucp_url; ?>?mode=logout&sid=<?php echo wpphpbbu_get_sessionid(); ?>">
                                      <?php
                                          echo _e('Log out', 'wpphpbbu');
                                      ?>
                                  </a>
                              </li>
                          <?php
                      }
                      else
                      {
                          ?>
                              <li>
                                  <a href="<?php echo $ucp_url; ?>?mode=sendpassword">
                                      <?php
                                          echo _e('I forgot my password', 'wpphpbbu');
                                      ?>
                                  </a>
                              </li>
                              <li>
                                  <a href="<?php echo $ucp_url; ?>?mode=resend_act">
                                      <?php
                                          echo _e('Resend activation email', 'wpphpbbu');
                                      ?>
                                  </a>
                              </li>
                              <li>
                                  <a href="<?php echo $ucp_url; ?>?mode=register">
                                      <?php
                                          echo _e('Register new account', 'wpphpbbu');
                                      ?>
                                  </a>
                              </li>
                          <?php
                      }
                  ?>
              </ul>
          <?php
        }

        echo $after_widget;
	}

	static function register(){
		register_widget( __CLASS__ );
	}
}

?>
