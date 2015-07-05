<?php
namespace wpphpbbu\widgets;
class LinksWidget extends \WP_Widget
{
	function __construct() {
		$widget_ops = array(
				'classname' => 'phpBB3 Links Widget',
				'description' => __('Allows you to display a list of usefull links to your phpBB.', 'wpphpbbu')
		);

		$control_ops = array(
				'width' => 250,
				'height' => 250,
				'id_base' => 'phpbb3-links-widget'
		);

		parent::__construct(   'phpbb3-links-widget',
			'WP phpBB Bridge Links',
			$widget_ops,
			$control_ops );
	}



    function form($instance)
    {
        $defaults = array(
        	'wpphpbbu_links_title' => __('Forum Links', 'wpphpbbu'),
	    		'wpphpbbu_links_index' => 'yes',
	    		'wpphpbbu_faq' => 'yes',
	    		'wpphpbbu_search' => 'yes',
	    		'wpphpbbu_active_topics' => 'yes',
	    		'wpphpbbu_unanswered_posts' => 'yes',
	    		'wpphpbbu_your_posts' => 'yes',
	    		'wpphpbbu_new_posts' => 'yes',
	    		'wpphpbbu_pms' => 'yes',
	    		'wpphpbbu_send_pm' => 'yes',
	    		'wpphpbbu_members' => 'yes',
	    		'wpphpbbu_team' => 'yes',
	    		'wpphpbbu_whos_online' => 'yes',
        );

        $instance = wp_parse_args(
            (array)$instance,
            $defaults
        );

        ?>
            <div class="widget-content">
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_links_title'); ?>">
                        <?php
                            _e('Title:', 'wpphpbbu');
                        ?>
                    </label>
                    <input id="<?php echo $this->get_field_id('wpphpbbu_links_title'); ?>" name="<?php echo $this->get_field_name('wpphpbbu_links_title'); ?>" type="text" value="<?php echo $instance['wpphpbbu_links_title']; ?>" class="widefat" />
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_links_index'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_links_index'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_links_index'); ?>" value="yes" <?php if($instance['wpphpbbu_links_index'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        &nbsp;
                        <?php
                            _e('Display Forum Index Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_faq'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_faq'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_faq'); ?>" value="yes" <?php if($instance['wpphpbbu_faq'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        &nbsp;
                        <?php
                            _e('Display FAQ Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_search'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_search'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_search'); ?>" value="yes" <?php if($instance['wpphpbbu_search'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        &nbsp;
                        <?php
                            _e('Display Forum Search Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_active_topics'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_active_topics'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_active_topics'); ?>" value="yes" <?php if($instance['wpphpbbu_active_topics'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display View Active Topics Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_unanswered_posts'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_unanswered_posts'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_unanswered_posts'); ?>" value="yes" <?php if($instance['wpphpbbu_unanswered_posts'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display View Unanswered Posts Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_your_posts'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_your_posts'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_your_posts'); ?>" value="yes" <?php if($instance['wpphpbbu_your_posts'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display View Your Posts Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_new_posts'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_new_posts'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_new_posts'); ?>" value="yes" <?php if($instance['wpphpbbu_new_posts'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display View New Posts Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_pms'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_pms'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_pms'); ?>" value="yes" <?php if($instance['wpphpbbu_pms'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display Private Messages Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_send_pm'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_send_pm'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_send_pm'); ?>" value="yes" <?php if($instance['wpphpbbu_send_pm'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display Send Private Message Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_members'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_members'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_members'); ?>" value="yes" <?php if($instance['wpphpbbu_members'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display Member List Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_team'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_team'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_team'); ?>" value="yes" <?php if($instance['wpphpbbu_team'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display The Team Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_whos_online'); ?>">
                        <input name="<?php echo $this->get_field_name('wpphpbbu_whos_online'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_whos_online'); ?>" value="yes" <?php if($instance['wpphpbbu_whos_online'] == 'yes'){ echo 'checked="checked" '; } ?> />
                        <?php
                            _e('Display Who is Online Link', 'wpphpbbu');
                        ?>
                    </label>
                </p>
            </div>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['wpphpbbu_links_title'] = $new_instance['wpphpbbu_links_title'];
        $instance['wpphpbbu_links_index'] = $new_instance['wpphpbbu_links_index'];
        $instance['wpphpbbu_faq'] = $new_instance['wpphpbbu_faq'];
        $instance['wpphpbbu_search'] = $new_instance['wpphpbbu_search'];
        $instance['wpphpbbu_active_topics'] = $new_instance['wpphpbbu_active_topics'];
        $instance['wpphpbbu_unanswered_posts'] = $new_instance['wpphpbbu_unanswered_posts'];
        $instance['wpphpbbu_your_posts'] = $new_instance['wpphpbbu_your_posts'];
        $instance['wpphpbbu_new_posts'] = $new_instance['wpphpbbu_new_posts'];
        $instance['wpphpbbu_pms'] = $new_instance['wpphpbbu_pms'];
        $instance['wpphpbbu_send_pm'] = $new_instance['wpphpbbu_send_pm'];
        $instance['wpphpbbu_members'] = $new_instance['wpphpbbu_members'];
        $instance['wpphpbbu_team'] = $new_instance['wpphpbbu_team'];
        $instance['wpphpbbu_whos_online'] = $new_instance['wpphpbbu_whos_online'];

        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);


        $forum_url =  $ucp_url = get_option('wpphpbbu_url');
				$ucp_url .= 'ucp.php';
        $search_url = $forum_url . 'search.php';
        $member_url = $forum_url . 'memberlist.php';
        $session_id = 'sid=' . \wpphpbbu\Session::get_sessionid();

        $title = $instance['wpphpbbu_links_title'];
        $display_index = $instance['wpphpbbu_links_index'];
        $display_faq = $instance['wpphpbbu_faq'];
        $display_search = $instance['wpphpbbu_search'];
        $display_active_topics = $instance['wpphpbbu_active_topics'];
        $display_unanswered_posts = $instance['wpphpbbu_unanswered_posts'];
        $display_your_posts = $instance['wpphpbbu_your_posts'];
        $display_new_posts = $instance['wpphpbbu_new_posts'];
        $display_pms = $instance['wpphpbbu_pms'];
        $display_send_pm = $instance['wpphpbbu_send_pm'];
        $display_members = $instance['wpphpbbu_members'];
        $display_team = $instance['wpphpbbu_team'];
        $display_whos_online = $instance['wpphpbbu_whos_online'];

        echo $before_widget . $before_title . $title . $after_title . '<ul>';

        if($display_index == 'yes')
        {
            echo '<li><a href="' . $forum_url . '?' . $session_id . '">';
            echo _e('Forum index', 'wpphpbbu');
            echo '</a></li>';
        }

        if($display_faq == 'yes')
        {
            echo '<li><a href="' . $forum_url . 'faq.php?' . $session_id . '">';
            echo _e('FAQ', 'wpphpbbu');
            echo '</a></li>';
        }

        if($display_search == 'yes')
        {
            echo '<li><a href="' . $search_url . '?' . $session_id . '">';
            echo _e('Forum search', 'wpphpbbu');
            echo '</a></li>';
        }

        if($display_active_topics == 'yes')
        {
            echo '<li><a href="' . $search_url . '?search_id=active_topics&amp;' . $session_id . '">';
            echo _e('View active topics', 'wpphpbbu');
            echo '</a></li>';
        }

        if($display_unanswered_posts == 'yes')
        {
            echo '<li><a href="' . $search_url . '?search_id=unanswered&amp;' . $session_id . '">';
            echo _e('View unanswered posts', 'wpphpbbu');
            echo '</a></li>';
        }

        if(\wpphpbbu\User::is_user_logged_in())
        {
            if($display_your_posts == 'yes')
            {
                echo '<li><a href="' . $search_url . '?search_id=egosearch&amp;' . $session_id . '">';
                echo _e('View your posts', 'wpphpbbu');
                echo '</a></li>';
            }

            if($display_new_posts == 'yes')
            {
                echo '<li><a href="' . $search_url . '?search_id=newposts&amp;' . $session_id . '">';
                echo _e('View new posts', 'wpphpbbu');
                echo '</a></li>';
            }

            if($display_pms == 'yes')
            {
                echo '<li><a href="' . $ucp_url . '?i=pm&amp;folder=inbox&amp;' . $session_id . '">';
                echo _e('Private messages', 'wpphpbbu');
                echo '</a></li>';
            }

            if($display_send_pm == 'yes')
            {
                echo '<li><a href="' . $ucp_url . '?i=pm&amp;mode=compose&amp;' . $session_id . '">';
                echo _e('Send private message', 'wpphpbbu');
                echo '</a></li>';
            }

            if($display_members == 'yes')
            {
                echo '<li><a href="' . $member_url . '?' . $session_id . '">';
                echo _e('Member list', 'wpphpbbu');
                echo '</a></li>';
            }

            if($display_team == 'yes')
            {
                echo '<li><a href="' . $member_url . '?mode=leaders&amp;' . $session_id . '">';
                echo _e('The team', 'wpphpbbu');
                echo '</a></li>';
            }

            if($display_whos_online == 'yes')
            {
                echo '<li><a href="' . $forum_url . 'viewonline.php?' . $session_id . '">';
                echo _e('Who is online', 'wpphpbbu');
                echo '</a></li>';
            }
        }

        echo '</ul>' . $after_widget;
    }

		static function register(){
			 register_widget( __CLASS__ );
		}
}

?>
