<?php

/*
  Plugin Name: WP PHPBB Unicorn
  Version: 1.0
  Plugin URI: http://github.com/wardormeur/wpphpbbunicorn
  Description: Wordpress + Phpbb + unicornz attacks the Web
  Author: Wardormeur
  Author URI: uheuheuheuhe
 */


use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once __DIR__.'/inc/helpers/wpbb_functions.php';
use wpphpbbu\helpers;

require_once __DIR__.'/inc/Path.php';
require_once __DIR__.'/inc/Session.php';
require_once __DIR__.'/inc/User.php';

//needed for classes proxy

require_once __DIR__.'/lib/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/autoload.php';
//We had to exclude from composer it to allow a better export of the plugin (sub-tree)
require_once __DIR__.'/vendor/nikic/php-parser/lib/bootstrap.php';
require_once __DIR__.'/SafeFunction.php';
require_once __DIR__.'/PathFixer.php';

require_once __DIR__.'/inc/Proxy.php';




class Unicorn{

  /**
   * @var strung Plugin name
  */
  var $name = "WP phpBB Unicorn";

	var $version = "0.1";

	var $path = "";

	public function __construct()
  {
      add_action('plugins_loaded', array($this, 'init'), 8);
	    $unicorn = $this;
  }


  /**
   * Initiate the plugin
   */
  function init()
  {
    // Do actions before run the plugin

		// in case path is wrong (or unset), we still want to be able to access the admin panel
		add_action('wpphpbbu_changed', array($this,'changed'));

		if( wpphpbbu\Path::is_path_ok() && wpphpbbu\Proxy::is_cache_ok() && !defined('SHORT_INIT') ){
			try{
				$this->start();
				$this->phpbb_includes();
				$this->init_widget();

				// Do init actions
				add_action('init', array($this, 'start_integration'));      // Start application integration
				//redirect pages
				add_action('init', function(){ wpphpbbu\Path::login_page();});
				add_action('wp_logout', function(){ wpphpbbu\Path::logout_page();});
				add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );


			}catch(Exception $e){
        var_dump('DUH');
			}
		}else{
      if(wpphpbbu\Path::is_path_ok())
      {
        //set initial cache
        (new wpphpbbu\Proxy())->setCache();
      }
    }
		$this->admin_includes();

	}
  /**
   * Start the plugin
   */
  function start()
  {
      // Load text domain for plugin
      load_plugin_textdomain('wpbb', false, 'i18n/');
      $this->register_events();
  }



	function register_events(){
    // Call add_post when creating new WordPress post, to create a new forum topic
    add_action('wp_insert_post', 'add_post', 10, 2);
	}


	/**
   * Include external files
   */
  function admin_includes()
  {
     if(is_admin())
      {
        // Load admin panel
        require_once(__DIR__.'/inc/wpbb_admin.php');
      }
  }
  /**
   * Include external files
   */
  function phpbb_includes()
  {
  	//Symfo part; do we really need it?

  	//PHPBB init part
  	define('IN_PHPBB', true);
  	//minimal conf
  	//putenv('PHPBB_NO_COMPOSER_AUTOLOAD=1');
  	global $phpbb_container;
  	global $phpbb_root_path, $phpEx,  $user,$auth, $db, $config, $cache, $template,$table_prefix;
  	global $request;
  	global $phpbb_dispatcher;
  	global $symfony_request;
  	global $phpbb_filesystem;

  	$phpEx = 'php';
    $phpbb_root_path = get_option('wpphpbbu_path');
  	require_once(__DIR__ . '\inc\cache\common.php');

  	require_once($phpbb_root_path . 'includes/utf/utf_normalizer.php');
  	require_once($phpbb_root_path.'phpbb/session.php');
  	require_once($phpbb_root_path.'phpbb/user.php');
  	require_once($phpbb_root_path.'phpbb/auth/auth.php');
  	require_once(__DIR__ .'\inc\cache\functions_user.php');

  	$request->enable_super_globals();
  }
  /*
 * Adds the meta box container.
 */
	public function add_meta_box( $post_type ) {
    $post_types = array('post', 'page');     //limit meta box to certain post types
    if ( in_array( $post_type, $post_types )) {
  		add_meta_box(
  			'some_meta_box_name'
  			,__( 'Some Meta Box Headline', 'myplugin_textdomain' )
  			,array( $this, 'render_meta_box_content' )
  			,$post_type
  			,'advanced'
  			,'high'
  		);
    }
  }



	function init_widget(){

		// Load WP phpBB Bridge widget
    require_once(__DIR__.'/inc/widgets/UsersWidget.php');

    // Initiate WP phpBB Bridge widget
    add_action(
        'widgets_init',
        array('\wpphpbbu\widgets\UsersWidget','register')
    );

    // Load WP phpBB Links widget
    require_once(__DIR__.'/inc/widgets/LinksWidget.php');

    // Initiate WP phpBB Links widget
    add_action(
        'widgets_init',
        array('\wpphpbbu\widgets\LinksWidget','register')
    );

    // Load WP phpBB Meta widget
    require_once(__DIR__.'/inc/widgets/MetaWidget.php');

    // Initiate WP phpBB Meta widget
    add_action(
        'widgets_init',
        array('\wpphpbbu\widgets\MetaWidget','register')
    );

    // Load WP phpBB Topics widget
    require_once(__DIR__.'/inc/widgets/TopicsWidget.php');

    // Initiate WP phpBB Topics widget
    add_action(
        'widgets_init',
      array('\wpphpbbu\widgets\TopicsWidget','register')
    );
	}

	function start_integration()
  {
  	// Get session ID
  	$session_id = (new wpphpbbu\Session())->load_session_id();
  }


	function uninstall()
	{
		delete_option('wpphpbbu_path');
		delete_option('wpphpbbu_path_ok');
		delete_option('wpphpbbu_url');
		delete_option('wpphpbbu_version');
		delete_option('wpphpbbu_post_locked');
		delete_option('wpphpbbu_post_posts');
	}

  function activate()
	{
		do_action('wpphpbbu_activated');
	}

	function deactivate()
	{
		do_action('wpphpbbu_deactivated');
	}
	function changed()
	{
		if(get_option( 'wpphpbbu_path_ok', false ))
			( new wpphpbbu\Proxy())->setCache();
	}

}

global $unicorn;
$unicorn = new Unicorn();

register_activation_hook(__FILE__, array($unicorn, 'activate'));
register_deactivation_hook(__FILE__, array($unicorn, 'deactivate'));
register_uninstall_hook(__FILE__, array($unicorn, 'uninstall'));



?>
