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

 require_once __DIR__.'/inc/wpbb_functions.php';

require_once __DIR__.'/inc/Path.php';
require_once __DIR__.'/inc/PhpbbSession.php';

//needed for classes proxy

require_once __DIR__.'/lib/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/autoload.php';
//We had to exclude from composer it to allow a better export of the plugin (sub-tree)
require_once __DIR__.'/vendor/nikic/php-parser/lib/bootstrap.php';
require_once __DIR__.'/SafeFunction.php';
require_once __DIR__.'/PathFixer.php';

require_once __DIR__.'/inc/ClassProxy.php';







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
	
		if( is_path_ok() && is_cache_ok() ){
		
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
    /**
     * Find a path
     */
    function find_phpbbPath()
    {
		$wp_path = get_home_path();
		$i = 0; $found=false;
		do{
			//http://php.net/manual/en/class.recursivedirectoryiterator.php#114504
			$directory = new \RecursiveDirectoryIterator($wp_path, \FilesystemIterator::FOLLOW_SYMLINKS);
			//OH WAIT? this PIECE OF SHI*T doesnt work for recursive directory that arent the parent. GOD. WHY. 
			$filter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
				//in case we take the time to exclude the self from the previous loop
				//well, it's ez, but im lazy
				return true;
			});
			$iterator = new \RecursiveIteratorIterator($filter);
			//directory dependance of the callback request us to ... redefine the whole goddam thing each loop. cmon..
			
			$files = array();
			$iterator->rewind();
			while( $iterator->valid() || !$found)
			{
				$info = $iterator->current();
				$iterator->next();
			  //alasfiltering must be done here cause filter doesnt filter.meh.
				if(strpos($info->getFilename(),'config.php') === 0)
					$files[] = $info->getPath();
					//actually, yeah, we stop once we found one.
					$found = true;
			}
			
			//We got up 1 lvl in hierarchy
			$wp_path = $wp_path.'../';
			$i++;
		}while ($i<2 || !$found);
		return !empty($files)?$files[0]:"";
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
		require_once __DIR__.'/inc/wpbb_functions.php';
	
		// Load WP phpBB Bridge widget
        require_once('inc/widgets/WPPHPBBU_users_widget.php');
        
        // Initiate WP phpBB Bridge widget
        add_action(
            'widgets_init', 
            create_function(
                '', 
                'register_widget("WPPHPBBU_users_widget");'
            )
        );
        
        // Load WP phpBB Links widget
        require_once('inc/widgets/WPPHPBBU_links_widget.php');
        
        // Initiate WP phpBB Links widget
        add_action(
            'widgets_init', 
            create_function(
                '', 
                'register_widget("WPPHPBBU_links_widget");'
            )
        );
        
        // Load WP phpBB Meta widget
        require_once('inc/widgets/WPPHPBBU_meta_widget.php');
        
        // Initiate WP phpBB Meta widget
        add_action(
            'widgets_init', 
            create_function(
                '', 
                'register_widget("WPPHPBBU_meta_widget");'
            )
        );
        
        // Load WP phpBB Topics widget
        require_once('inc/widgets/WPPHPBBU_topics_widget.php');
        
        // Initiate WP phpBB Topics widget
        add_action(
            'widgets_init', 
            create_function(
                '', 
                'register_widget("WPPHPBBU_topics_widget");'
            )
        );
	}
    
	function start_integration()
    {
		// Get session ID
		$session_id = (new wpphpbbu\Session())->load_session_id();
		
    }
    
	
	function uninstall()
	{
		delete_option('wpphpbbu_config_path');
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
			( new wpphpbbu\ClassProxy())->set_cache();
	
	}
}

global $unicorn;
$unicorn = new Unicorn();

register_activation_hook(__FILE__, array($unicorn, 'activate'));
register_deactivation_hook(__FILE__, array($unicorn, 'deactivate'));
register_uninstall_hook(__FILE__, array($unicorn, 'uninstall'));



?>