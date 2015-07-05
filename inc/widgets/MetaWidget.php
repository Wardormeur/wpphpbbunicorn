<?php

namespace wpphpbbu\widgets;
class MetaWidget extends \WP_Widget
{
	function __construct() {

		/* Widget settings. */
		$widget_ops = array(
				'classname' => 'phpBB3 Meta Widget',
				'description' => __('Allows you to display several usefull informations about the user.', 'wpphpbbu')
		);

		/* Widget control settings. */
		$control_ops = array(
				'width' => 250,
				'height' => 250,
				'id_base' => 'wpphpbbmeta'
		);

		parent::__construct( 	'wpphpbbmeta',
			'WP phpBB Bridge Meta',
			$widget_ops,
			$control_ops );
	}



    function form($instance)
    {
        $defaults = array(
                        'wpphpbbu_meta_title' => __('Forum Meta', 'wpphpbbu')
                    );

        $instance = wp_parse_args(
                        (array)$instance,
                        $defaults
                    );

        ?>
            <div class="widget-content">
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_meta_title') ?>">
                        <?php
                            _e('Title:', 'wpphpbbu');
                        ?>
                    </label>
                    <input id="<?php echo $this->get_field_id('wpphpbbu_meta_title'); ?>" name="<?php echo $this->get_field_name('wpphpbbu_meta_title') ?>" type="text" value="<?php echo $instance['wpphpbbu_meta_title']; ?>" class="widefat" />
                </p>
            </div>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['wpphpbbu_meta_title'] = $new_instance['wpphpbbu_meta_title'];

        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);
				die();

    	$title = $instance['wpphpbbu_meta_title'];

      $ucp_url = get_option('wpphpbbu_url').'ucp.php';
    	$admin_url = wpphpbbu_get_admin_link();
    	$mcp_url = wpphpbbu_get_mcp_link();
    	$permission_url = wpphpbbu_get_restore_permissions_link();

    	if(empty($title))
    	{
    		$title = "&nbsp;";
    	}

        echo $before_widget . $before_title . $title . $after_title;

        ?>
            <ul>
                <?php
                    if(wpphpbbu_is_user_logged_in())
                    {
                ?>
                    <li>
                        <a href="<?php bloginfo('home'); ?>/wp-admin/">
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

                        if(!empty($admin_url))
                        {
                        ?>
                            <li>
                                <a href="<?php echo $admin_url; ?>">
                                    <?php
                                        echo _e('Forum administration', 'wpphpbbu');
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
                <li>
                    <a href="<?php echo $ucp_url; ?>?mode=delete_cookies">
                        <?php
                            echo _e('Delete site cookies', 'wpphpbbu');
                        ?>
                    </a>
                </li>
            </ul>
        <?php

        echo $after_widget;
    }

		static function register(){
			 register_widget( __CLASS__ );
		}
}

?>
