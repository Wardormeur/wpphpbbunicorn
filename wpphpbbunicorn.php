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

//needed for classes proxy
require_once __DIR__.'/lib/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/SafeFunction.php';
require_once __DIR__.'/PathFixer.php';


class Unicorn{
	
    /**
     * @var strung Plugin name
     */
    var $name = "WP phpBB Unicorn";
	
	var $version = "0.1";

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
		if( $this->is_path_ok()  ){
		
			try{
				$this->set_cache();
				$this->start();
				$this->phpbb_includes();
				$this->init_widget();
				
				// Do init actions
				add_action('init', array($this, 'start_integration'));      // Start application integration
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
    
		
	function prepare_phpbb_path($phpbb_root_path){
			$thispath = explode('\\', str_replace('/','\\', dirname(__FILE__)));
			$rootpath = explode('\\', str_replace('/','\\', dirname($_SERVER["SCRIPT_FILENAME"])));
			$relpath = array();
			$dotted = 0;
			for ($i = 0; $i < count($rootpath); $i++) {
				if ($i >= count($thispath)) {
					$dotted++;
				}
				elseif ($thispath[$i] != $rootpath[$i]) {
					$relpath[] = $thispath[$i]; 
					$dotted++;
				}
			}
		return $GLOBALS['phpbb_root_path'] = $phpbb_root_path =str_repeat('../', $dotted).$phpbb_root_path;
		
	}
	
	function register_events(){
		 // Call add_post when creating new WordPress post, to create a new forum topic
        add_action('wp_insert_post', 'add_post', 10, 2);
	
	}
	
	function set_cache(){
		
		define('IN_PHPBB', true);
		//hardpatch, w/e
		$phpbb_root_path = get_option( 'wpphpbbu_path', false );	
		$phpEx = 'php';
		$GLOBALS['phpbb_root_path'] = get_option( 'wpphpbbu_path', false );	
		$GLOBALS['phpEx'] = 'php';
		
				
		$GLOBALS['phpbb_root_path'] = $phpbb_root_path = $this->prepare_phpbb_path($phpbb_root_path);
		
		//fix make_clickable
		
		$parser = new PhpParser\Parser(new PhpParser\Lexer);
		$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
			try {
				$searched_function[] = "make_clickable"; 
			
				$traverser_safety     = new PhpParser\NodeTraverser;
				$traverser_safety->addVisitor(new SafeFunction($searched_function));
				// parse
				$raw = file_get_contents($phpbb_root_path.'includes/functions_content.'. $phpEx);
				
				$stmts = $parser->parse($raw);

				// traverse
				$stmts = $traverser_safety->traverse($stmts);

				// pretty print
				
				$code = $prettyPrinter->prettyPrint($stmts);

				file_put_contents(__DIR__.'/cache/functions_content.'.$phpEx,'<?php '.$code.' ?>');
			} catch (PhpParser\Error $e) {
				echo 'Parse Error: ', $e->getMessage();
			}
			
			try {
				$searched_function[] = "validate_username"; 
			
				$traverser_safety     = new PhpParser\NodeTraverser;
				$traverser_safety->addVisitor(new SafeFunction($searched_function));
				// parse
				$raw = file_get_contents($phpbb_root_path.'includes/functions_user.'. $phpEx);
				
				$stmts = $parser->parse($raw);

				// traverse
				$stmts = $traverser_safety->traverse($stmts);

				// pretty print
				
				$code = $prettyPrinter->prettyPrint($stmts);

				file_put_contents(__DIR__.'/cache/functions_user.'.$phpEx,'<?php '.$code.' ?>');
			} catch (PhpParser\Error $e) {
				echo 'Parse Error: ', $e->getMessage();
			}
		
		//unicorn code is actually useless, im bored. At least, it was a good exercise
		
		
		
		try{
			$traverser_path     = new PhpParser\NodeTraverser;
			//dont forget to escape the path, = preq_quote?
			$mypath = __DIR__;
			$phpbb_path = $GLOBALS['phpbb_root_path'];
			
			
			$traverser_path->addVisitor(new PathFixer("\$phpbb_root_path \. \'includes/functions_content.\' \. \$phpEx",
													[ //ALAS we cant predict what kind of data you're gonna replace it with. so, you're gonna have to learn the types :(
													//PLus, we consider you do nothing but concatenating those string 
														new PhpParser\Node\Scalar\String_($mypath.'/cache/functions_content.'),
														new PhpParser\Node\Expr\Variable('phpEx')
													])
										); 
			
			//fix path to functions_content
			$raw = file_get_contents($phpbb_root_path.'common.'. $phpEx);
			// parse
			$stmts = $parser->parse($raw);

			// traverse
			$stmts = $traverser_path->traverse($stmts);

			// pretty print
			
			$code = $prettyPrinter->prettyPrint($stmts);

			file_put_contents(__DIR__.'/cache/common.'.$phpEx,'<?php '.$code.' ?>');
		} catch (PhpParser\Error $e) {
			echo 'Parse Error: ', $e->getMessage();
		}
		
		
		
		
		
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
	
		require_once(__DIR__ . '\cache\common.php');
		
		require_once($phpbb_root_path . 'includes/utf/utf_normalizer.php');
		require_once($phpbb_root_path.'phpbb/user.php');
		require_once($phpbb_root_path.'phpbb/session.php');
		require_once($phpbb_root_path.'phpbb/auth/auth.php');
		require_once(__DIR__ .'\cache\functions_user.php');
		
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
		$session_id = $this->load_session_id();
		
		// Check redirect
		$this->check_redirect($session_id);
	
    }
    
    // Initiate the phpBB session
    private function load_session_id()
    {
		global $user, $auth,$phpbb_container;
        
        define('DEBUG',true);
		
        $phpbb_config = trim(get_option('wpphpbbu_path'));       // Get config path from options  


		$user->session_begin();
			
		 
		if(!is_user_logged_in()){
		
			$userid = $this->get_userid();                                          // Get user ID
			
			if($userid > 0 )                                         // If user ID is bigger than 0 and user ID is not equal with the current user ID
			{
				wp_clear_auth_cookie();
				$wpuser = wp_set_current_user($userid);                                       // Set the current user
				wp_set_auth_cookie($userid, true, false);  
			}
        }else{
			//session_pagestart();
		}
        // Return current user session id
        return $user->session_id;
    }
    
    private function check_redirect($session_id)
    {
		global $phpbb_root_path, $user;
        // Get current file name
		//var_dump($user);
    	$filename = strtolower(basename($_SERVER['SCRIPT_FILENAME']));
    	
        // If file name is wp-login.php and user is logged in
    	/*if($filename == "wp-login.php" && is_user_logged_in())
    	{
            // Redirect user on phpBB UCP file with logout mode
    		wp_redirect($phpbb_root_path .'ucp.php?mode=logout&sid=' . $session_id);
    	}*/
        // If file name is wp-login.php and user is not logged in
    	if($filename == "wp-login.php" && !is_user_logged_in())
    	{
			// Redirect user on phpBB UCP file with login mode
    		wp_redirect($phpbb_root_path .'ucp.php?mode=login&redirect=' . urlencode(get_bloginfo('home')));
    	}
        // If file name is wp-signup.php and user is not logged in
    	else if($filename == "wp-signup.php" && !is_user_logged_in())
    	{
            // Redirect user on phpBB UCP file with register mode
    		wp_redirect($phpbb_root_path .'ucp.php?mode=register');
    	}
    }
    
    private function get_userid()
    { 
        global $wpdb, $user;
		global $phpbb_container;
           
        $userid = 0;                                            // Set userid to 0;
        //var_dump($s_user);
		// If current user type is normal user or the current user type is founder
		if($user->data['user_type'] == USER_NORMAL || $user->data['user_type'] == USER_FOUNDER)
        {
            // List all users ID's where having meta_key of phpbb_userid and meta_value equal to current user id
            $stat= $wpdb->prepare(
                                "SELECT ID FROM $wpdb->users WHERE user_nicename = %s", 
                                $user->data['username_clean'] );
			
			$id_list = $wpdb->get_col($stat);
			
			//should return only 1, this is a security failure
			if(!empty($id_list))
            {
                $userid = $id_list[0];
            }
        }     
		
		return $userid;
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
	
	function is_path_ok(){
		return file_exists( $this->prepare_phpbb_path(get_option( 'wpphpbbu_path', false )).'includes/functions_content.php');
	}

	
}

global $unicorn;
$unicorn = new Unicorn();

register_activation_hook(__FILE__, array($unicorn, 'activate'));
register_deactivation_hook(__FILE__, array($unicorn, 'deactivate'));
register_uninstall_hook(__FILE__, array($unicorn, 'uninstall'));



?>