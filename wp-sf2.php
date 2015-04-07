<?php

/*
  Plugin Name: Wordpress Symfony2 Wedlock
  Version: 1.0
  Plugin URI: http://www.craftitonline.com
  Description: Wedlock your apps with Symfony2 :)
  Author: Luis Cordova - cordoval@gmail.com http://www.craftitonline.com
  Author URI: http://www.craftitonline.com
 */
use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once __DIR__.'/lib/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/SafeFunction.php';
require_once __DIR__.'/PathFixer.php';


function sf2AutoLoad() {

    $loader = new UniversalClassLoader();

    $loader->registerNamespaces(array(
        'Sf2Plugins'           => __DIR__.'/src',
		'Symfony'			=>	__DIR__.'\..\..\..\tn-.3.1.3\vendor\symfony',
		'phpbb'				=>	__DIR__.'\..\..\..\tn-.3.1.3\phpbb'
    ));

    $loader->registerPrefixes(array(
        'Twig_Extensions_' => __DIR__.'/lib/Twig-extensions/lib',
        'Twig_'            => __DIR__.'/lib/Twig/lib',
    ));
    $loader->register();

}
function testphpbb(){


	define('IN_PHPBB', true);
	//minimal conf
	//putenv('PHPBB_NO_COMPOSER_AUTOLOAD=1');
	
	//hardpatch, w/e
	$phpbb_root_path = '../tn-3.1.3/';	
	$phpEx = 'php';
	$GLOBALS['phpbb_root_path'] = '../tn-3.1.3/';	
	$GLOBALS['phpEx'] = 'php';
	
	//fix make_clickable
	
	$parser = new PhpParser\Parser(new PhpParser\Lexer);
	$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
	if(!file_exists(__DIR__.'/cache/functions_content.'.$phpEx)){
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
	}
	if(!file_exists(__DIR__.'/cache/functions_user.'.$phpEx)){
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
	}
	
	//This code is actually useless, im bored. At least, it was a good exercise
	/* 
	try{
		$traverser_path     = new PhpParser\NodeTraverser;
		//dont forget to escape the path, = preq_quote?
		$mypath = __DIR__;
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

		file_put_contents(__DIR__.'\\cache\\common.'.$phpEx,'<?php '.$code.' ?>');
	} catch (PhpParser\Error $e) {
		echo 'Parse Error: ', $e->getMessage();
	}
	
	*/
	global $db;
	global $phpbb_container;
	global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template, $table_prefix;
	global $request;
	global $phpbb_dispatcher;
	global $symfony_request;
	global $phpbb_filesystem;
	require(__DIR__ . '\cache\common.'. $phpEx );
	
	require($phpbb_root_path . 'includes/utf/utf_normalizer.' . $phpEx);
	//require($phpbb_root_path . 'includes/utf/utf_tools.' . $phpEx);
	require(__DIR__ .'\cache\functions_user.'.$phpEx);
	
/*	$u = [];
	$usernames[] = 'Wardormeur';

	$nb_user = user_get_id_name($u, $usernames, false);
	var_dump($u);	*/
	
	$request->enable_super_globals();
}

add_action('init', 'sf2AutoLoad');
add_action('init','testphpbb');
?>