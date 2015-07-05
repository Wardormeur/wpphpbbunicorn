<div class="wrap">
    <div id="wpphpbbu_admin_dashboard" class="icon32"></div>
    <h2><?php _e('WP phpBB Unicorn', 'wpphpbbu'); ?></h2>

    <div id="dashboard-widgets-wrap" class="ngg-overview">
        <div id="dashboard-widgets" class="metabox-holder">
            <div id="post-body">
                <div id="dashboard-widgets-main-content">
                    <div class="postbox-container" style="width:75%;">
                        <div id="left-sortables" class="meta-box-sortables ui-sortable">
                            <div id="wpphpbbu_status" class="postbox ">
                                <div class="handlediv" title="<?php _e('Toggle', 'wpphpbbu'); ?>"><br /></div>
                                <h3 class="hndle">
                                    <span>
                                        <?php _e('WP phpBB Bridge status', 'wpphpbbu'); ?>
                                    </span>
                                </h3>
                                <div class="inside">
                                    <div class="table table_content">
                                        <p>
                                            <?php _e('Fast preview of WP phpBB Bridge status &amp; configuration', 'wpphpbbu'); ?>
                                        </p>
                                    </div>
                                    <table>
                                      <tr>
                                          <td valign="top">
                                              <?php _e('phpBB absolute path', 'wpphpbbu'); ?> :
                                          </td>
                                          <td valign="top">
                                              <strong>
                                                  <?php
                                                      echo get_option('wpphpbbu_path_ok',false)?
                              get_option('wpphpbbu_path','') :
                              __('The path to phpbb directory is not set yet', 'wpphpbbu') ;
                                                  ?>
                                              </strong>
                                              <br />
                                              <br />
                                          </td>
                                      </tr>
                                      <tr>
                                          <td valign="top">
                                              <?php _e('phpBB url', 'wpphpbbu'); ?> :
                                          </td>
                                          <td valign="top">
                                              <strong>
                                                  <?php
                                                      echo get_option('wpphpbbu_path_ok',false)?
                              get_option('wpphpbbu_url','') :
                              __('The phpbb url is not set yet', 'wpphpbbu') ;
                                                  ?>
                                              </strong>
                                              <br />
                                              <br />
                                          </td>
                                      </tr>
                                       
                                        <tr>
                                            <td valign="top">
                                                <?php _e('Autocreate forum topics', 'wpphpbbu'); ?> :
                                            </td>
                                            <td valign="top">
                                                <strong>
                                                    <?php
                                                        get_option('wpphpbbu_post_posts', 'no') == "no" ? _e('No', 'wpphpbbu') : _e('Yes', 'wpphpbbu');
                                                    ?>
                                                </strong>
                                                <br />
                                                <br />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top">
                                                <?php _e('Autocreate forum topics on locked forums', 'wpphpbbu'); ?> :
                                            </td>
                                            <td valign="top">
                                                <strong>
                                                    <?php
                                                        get_option('wpphpbbu_post_locked', 'no') == "no" ? _e('No', 'wpphpbbu') : _e('Yes', 'wpphpbbu');
                                                    ?>
                                                </strong>
                                                <br />
                                                <br />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="postbox-container" style="width:24%;">
                        <div id="right-sortables" class="meta-box-sortables ui-sortable">
                            <div id="wpphpbbu_plugin_info" class="postbox ">
                                <div class="handlediv" title="<?php _e('Toggle', 'wpphpbbu'); ?>">
                                    <br />
                                </div>
                                <h3 class="hndle">
                                    <span>
                                        <?php _e('Do you like WP phpBB Bridge?', 'wpphpbbu'); ?>
                                    </span>
                                </h3>
                                <div class="inside">
                                    <p>
                                        <?php
                                            _e('The WP phpBB Unicorn is the redevelopment of the WP phpBB Bridge; which was the development of «WordPress to phpBB3 Bridge» ', 'wpphpbbu');
                                        ?>
                                    </p>
                                    <p>
                                        <?php
                                            _e('The first version of the plugin was designed to synchronize users of phpBB users to WordPress so that the user is once in phpBB and be simultaneously connected and WordPress.', 'wpphpbbu');
                                        ?>
                                    </p>
									<p>
                                        <?php
                                            _e('In now days the plugin is re-re-writed from scratch again to adapt to WP 4.1, to fix errors from the past, and add new functionality in case to make the usage of phpBB in compination with WordPress a peace of cake.', 'wpphpbbu')
                                        ?>
                                    </p>
                                    <p>
                                        <?php
                                            _e('Some functionnalities were however removed in order to split what belongs to WP and what belongs to phpbb. A plugin called <a href="github.com/wardormeur/phpbbwpunicorn">phpbbwpunicorn</a> allows you the syncrhonization of users, whereas this plugin only allows session sharing ', 'wpphpbbu')
                                        ?>
                                    </p>
                                    <h4>
                                        <?php _e('Do you like to help us?', 'wpphpbbu'); ?>
                                    </h4>
                                    <ul>
                                        <li>
                                            <a href="http://wordpress.org/extend/plugins/wp-phpbb-bridge/" target="_blank" title="<?php _e('Give a possitive rating', 'wpphpbbu'); ?>">
                                                <?php _e('Give it a good rating on WordPress.org', 'wpphpbbu'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div id="wpphpbbu_locale" class="postbox">
                                <div class="handlediv" title="Εναλλαγή">
                                    <br />
                                </div>
                                <h3 class="hndle">
                                    <span>
                                        <?php _e('Translation', 'wpphpbbu'); ?>
                                    </span>
                                </h3>
                                <div class="inside" style="">
                                    <p>
                                        <?php
                                            _e(
                                                'If you cannot find the plugin in your native language, you may like to translated and send the translation back to us. Then a new plugin version will be available in a few days with your language package installed',
                                                'wpphpbbu'
                                            );
                                        ?>
                                    </p>
                                    <p>
                                        <?php
                                            _e(
                                                'Click the download button below to get the latest English translation file. Use the poEdit, save the file in the form of wpphpbbu-YourLocale.po and send us the *.po and *.mo files with e-mail here: support@wordpress-gr.org',
                                                'wpphpbbu'
                                            );
                                        ?>
                                    </p>
                                    <p class="textright">
                                        <a class="button" href="<?php echo wpphpbbu_URL . '/i18n/wpphpbbu-en.po' ?>" title="<?php _e('Translation file', 'wpphpbbu'); ?>" target="_blank">
                                            <?php _e('Download', 'wpphpbbu'); ?>
                                        </a>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
