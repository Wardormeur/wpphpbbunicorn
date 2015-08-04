<div id="overlay"><div id="loading"></div></div>
<?php
    global $wpdb;
?>
<div class="wrap">
    <div id="wpphpbbu_admin_dashboard" class="icon32"></div>
    <h2><?php _e('WP phpBB Bridge', 'wpphpbbu'); ?> - <?php _e('Settings', 'wpphpbbu'); ?></h2>
    <?php
        if(isset($e) && sizeof($e->get_error_messages()) > 0)
        {
    ?>
    <div class="error">
        <br />
        <?php
            foreach($e->get_error_messages() as $er)
            {
                echo $er;
                echo "<br />";
            }
        ?>
        <br />
    </div>
    <?php
        }
    ?>
    <form method="post" action="">
        <input type="hidden" name="action" value="update" />
        <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('wpphpbbu_settings_page') ?>" />

        <h3>
            <?php

                _e('Files options', 'wpphpbbu');

            ?>
        </h3>

        <table class="form-table">
            <tbody>
              <tr valign="top">
                  <th scope="row">
                      <label for="wpphpbbu_path">
                          <?php
                              _e('phpbb path', 'wpphpbbu');
                          ?>
                      </label>
                  </th>
                  <td>
                      <input name="wpphpbbu_path" type="text" id="wpphpbbu_path" value="<?php echo $wpphpbbu_path; ?>" class="regular-text" />
                      <br />
                      <span class="description">
                          <?php _e('Enter the absolute path to phpBB directory ending with a slash', 'wpphpbbu'); ?>
                      </span>
                  </td>
              </tr>
              <tr valign="top">
                    <th scope="row">
                        <label for="wpphpbbu_url">
                            <?php
                                _e('phpbb url', 'wpphpbbu');
                            ?>
                        </label>
                    </th>
                    <td>
                        <input name="wpphpbbu_url" type="text" id="wpphpbbu_url" value="<?php echo $wpphpbbu_url; ?>" class="regular-text" />
                        <br />
                        <span class="description">
                            <?php _e('Enter the url of phpBB directory ending with a slash', 'wpphpbbu'); ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>


        <h3>
            <?php

                _e('Forum posts options', 'wpphpbbu');

            ?>
        </h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label>
                            <?php
                                _e('New forum posts on post creation', 'wpphpbbu');
                            ?>
                        </label>
                    </th>
                    <td>
                        <label for="wpphpbbu_post_posts">
                            <input name="wpphpbbu_post_posts" type="checkbox" id="wpphpbbu_post_posts" <?php echo $wpphpbbu_post_posts == "yes" ? 'checked="checked"' : ''; ?> />
                            <?php _e('Enable', 'wpphpbbu'); ?>
                        </label>
                        <br />
                        <span class="description">
                            <?php _e('Check that option if you like to enable the posting of new WordPress posts on specific forums.', 'wpphpbbu'); ?>
                        </span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label>
                            <?php
                                _e('Post on locked forums', 'wpphpbbu');
                            ?>
                        </label>
                    </th>
                    <td>
                        <label for="wpphpbbu_post_locked">
                            <input name="wpphpbbu_post_locked" type="checkbox" id="wpphpbbu_post_locked" <?php echo $wpphpbbu_post_locked == "yes" ? 'checked="checked"' : ''; ?> />
                            <?php _e('Enable', 'wpphpbbu'); ?>
                        </label>
                        <br />
                        <span class="description">
                            <?php _e('By checking that option you will be able to choose locked posts on witch the plugin will posting.', 'wpphpbbu'); ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>


        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save options', 'wpphpbbu'); ?>" />
        </p>
    </form>
</div>
