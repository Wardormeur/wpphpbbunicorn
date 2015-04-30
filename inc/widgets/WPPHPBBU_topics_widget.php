<?php

class WPPHPBBU_topics_widget extends WP_Widget
{
    function WPPHPBBU_topics_widget()
    {
        /* Widget settings. */
        $widget_ops = array(
            'classname' => 'phpBB3 Posts Widget',
            'description' => __('Allows you to display a list of recent topics within a specific forum id\'s.', 'wpphpbbu')
        );

        /* Widget control settings. */
        $control_ops = array(
            'width' => 250,
            'height' => 250,
            'id_base' => 'phpbb3-posts-widget'
        );

        /* Create the widget. */
        $this->WP_Widget(
            'phpbb3-posts-widget',
            'WP phpBB Bridge posts',
            $widget_ops,
            $control_ops
        );
    }
    
    function form($instance)
    {
        $defaults = array(
                        'wpphpbbu_title' => __('Latest posts', 'wpphpbbu'),
        				'wpphpbbu_forums' => 0,
        				'wpphpbbu_total' => '10',
                        'wpphpbbu_show_forum' => 'yes',
                        'wpphpbbu_show_username' => 'yes',
                        'wpphpbbu_show_total_posts' => 'yes',
                        'wpphpbbu_show_total_views' => 'yes'
                    );
            
        $instance = wp_parse_args(
                        (array)$instance,
                        $defaults
                    );
                    
        ?>
            <div class="widget-content">
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_title'); ?>">
                        <?php 
                            echo _e('Title:', 'wpphpbbu'); 
                        ?>
                    </label>
                    <input class="widefat" id="<?php echo $this->get_field_id('wpphpbbu_title'); ?>" name="<?php echo $this->get_field_name('wpphpbbu_title'); ?>" type="text" value="<?php echo $instance['wpphpbbu_title']; ?>" />
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_forums'); ?>">
                        <?php echo _e('Forums:', 'wpphpbbu'); ?>
                    </label>
                    <input class="widefat" name="<?php echo $this->get_field_name('wpphpbbu_forums'); ?>" type="text" id="<?php echo $this->get_field_id('wpphpbbu_forums'); ?>" value="<?php echo $instance['wpphpbbu_forums']; ?>" />
                    <small><?php _e('Enter the id of the forum you like to get topics from. You can get topics from more than one forums by seperating the forums id with commas. ex: 3,5,6,12','wpphpbbu'); ?></small>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('wpphpbbu_total'); ?>">
                        <?php 
                            echo _e('Total posts:', 'wpphpbbu'); 
                        ?>
                    </label>
                    <input class="widefat" name="<?php echo $this->get_field_name('wpphpbbu_total'); ?>" type="text" id="<?php echo $this->get_field_id('wpphpbbu_total'); ?>" value="<?php echo $instance['wpphpbbu_total']; ?>" />
                </p>
                <p>
                    <label>
                        <input name="<?php echo $this->get_field_name('wpphpbbu_show_forum'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_show_forum'); ?>" value="yes" <?php if ($instance['wpphpbbu_show_forum'] == "yes") { echo 'checked="checked" '; } ?> />&nbsp;
                        <?php 
                            echo _e('Display forum name', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input name="<?php echo $this->get_field_name('wpphpbbu_show_username'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_show_username'); ?>" value="yes" <?php if($instance['wpphpbbu_show_username'] == "yes") { echo 'checked="checked" '; } ?> />&nbsp;
                        <?php 
                            echo _e('Display author name', 'wpphpbbu');
                        ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input name="<?php echo $this->get_field_name('wpphpbbu_show_total_views'); ?>" type="checkbox" id="<?php echo $this->get_field_id('wpphpbbu_show_total_views'); ?>" value="yes" <?php if ($instance['wpphpbbu_show_total_views'] == "yes") { echo 'checked="checked" '; } ?> />&nbsp;
                        <?php 
                            echo _e('Display total views', 'wpphpbbu');
                        ?>
                    </label>
                </p>
            </div>
        <?php
    }
    
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['wpphpbbu_title'] = $new_instance['wpphpbbu_title'];
        $instance['wpphpbbu_forums'] = $new_instance['wpphpbbu_forums'];
        $instance['wpphpbbu_total'] = $new_instance['wpphpbbu_total'];
        $instance['wpphpbbu_show_forum'] = $new_instance['wpphpbbu_show_forum'];
        $instance['wpphpbbu_show_username'] = $new_instance['wpphpbbu_show_username'];
        $instance['wpphpbbu_show_total_posts'] = $new_instance['wpphpbbu_show_total_posts'];
        $instance['wpphpbbu_show_total_views'] = $new_instance['wpphpbbu_show_total_views'];
        
        return $instance;
    }
    
    function widget($args, $instance)
    {
        global $wpdb, $phpbb_root_path, $phpEx, $auth, $user, $db, $config, $cache, $template, $table_prefix;
        
        extract($args);

        $title = $instance['wpphpbbu_title'];
        $forums = $instance['wpphpbbu_forums'];
        $total = $instance['wpphpbbu_total'];
        $showForum = $instance['wpphpbbu_show_forum'];
        $showUsername = $instance['wpphpbbu_show_username'];
        $showTotalPosts = $instance['wpphpbbu_show_total_posts'];
        $showTotalViews = $instance['wpphpbbu_show_total_views'];
        
		
        echo $before_widget . $before_title . $title . $after_title;
        
		$forum_url = get_option('wpphpbbu_path');
		
    	
        // TODO : Next Version, to get connected with phpBB $db object in order to retrive the
        //        forum posts. Also to remove the database encoding from plugin options
        
        
		$qr = "SELECT p.`topic_id` AS `ID`, p2.`forum_name` AS `FORUM`, p.`forum_id` AS `FORUM_ID`, p.`topic_title` AS `TITLE`, p.`topic_views` AS `VIEWS`, `username_clean` AS `USERNAME`, p.`topic_poster` AS `USER_ID`, p.`topic_time` AS `TIME`
		FROM " . $table_prefix . "topics p
		LEFT JOIN " . $table_prefix . "forums p2 ON(p.`forum_id` = p2.`forum_id`)
		LEFT JOIN " . $table_prefix . "users p3 ON(p.`topic_poster` = p3.`user_id`)
		WHERE p.`forum_id` IN (" . $forums . ") ORDER BY p.`topic_time` DESC LIMIT " . $total;

		$rs = $db->sql_query($qr);
        ?>
            <ul>
                <?php
				while($get_info = $rs->fetch_assoc())
                    { 
                ?>
                    <li>
                        <a rel="nofollow" href="<?php echo $forum_url; ?>viewtopic.php?f=<?php echo $get_info['FORUM_ID'] ?>&t=<?php echo $get_info['ID']; ?>" title="<?php echo __('View post','wpphpbbu'); ?>">
                            <?php echo $get_info['TITLE']; ?>
                        </a>
                        <br />
                        <?php
                            if($showForum == 'yes')
                            {
                        ?>
                            <small>
                                <?php 
                                    echo __('Forum:', 'wpphpbbu');
                                ?> 
                                <a rel="nofollow" href="<?php echo $forum_url; ?>viewforum.php?f=<?php echo $get_info['FORUM_ID']; ?>" title="<?php echo __('Go to forum', 'wpphpbbu'); ?>">
                                    <?php 
                                        echo $get_info['FORUM']; 
                                    ?>
                                </a>
                            </small>
                            <br />
                        <?php
                            }
                            
                            if($showUsername == 'yes')
                            {
                        ?>
                            <small>
                                <?php 
                                    echo __('By:', 'wpphpbbu'); 
                                ?>
                                <a rel="nofollow" href="<?php echo $forum_url; ?>memberlist.php?mode=viewprofile&u=<?php echo $get_info['USER_ID']; ?>" title="<?php echo __('View user info', 'wpphpbbu'); ?>">
                                    <?php 
                                        echo $get_info['USERNAME']; 
                                    ?>
                                </a>
                            </small>
                            <br />
                        <?php
                            }
                            
                            if($showTotalViews == 'yes')
                            {
                        ?>
                            <small>
                                <?php 
                                    _e('Views:', 'wpphpbbu'); 
                                ?> 
                                <strong>
                                    <?php 
                                        echo $get_info['VIEWS']; 
                                    ?>
                                </strong>
                            </small>
                        <?php
                            }
                            
                            if($showTotalViews == 'yes' && $showTotalPosts == 'yes')
                            {
                                echo "<small>&nbsp;|&nbsp;</small>";
                            }
                            
                            if($showTotalPosts == 'yes')
                            {
                        ?>
                        <?php
                            }
                        ?>
                    </li>
                <?php
                    }
                ?>
            </ul>
        <?php
        
        echo $after_widget;
    }
}

?>