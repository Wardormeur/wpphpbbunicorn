<?php

namespace wpphpbbu;

class ClassProxy{

	private function cleanFolder(){
		unlink(__DIR__.'/cache/functions_content.php');
		unlink(__DIR__.'/cache/common.php');
		unlink(__DIR__.'/cache/functions_user.php');
	}

	public function setCache(){

		//empty the cache folder
		$this->cleanFolder();

		$phpbb_root_path = get_option( 'wpphpbbu_path', false );
		$phpEx = 'php';
		$GLOBALS['phpbb_root_path'] = get_option( 'wpphpbbu_path', false );
		$GLOBALS['phpEx'] = 'php';

		$GLOBALS['phpbb_root_path'] = $phpbb_root_path;


		//fix make_clickable

		$parser = new \PhpParser\Parser(new \PhpParser\Lexer);
		$prettyPrinter = new \PhpParser\PrettyPrinter\Standard;
		try {
			$searched_function[] = "make_clickable";

			$traverser_safety     = new \PhpParser\NodeTraverser;
			$traverser_safety->addVisitor(new \SafeFunction($searched_function));
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

			$traverser_safety     = new \PhpParser\NodeTraverser;
			$traverser_safety->addVisitor(new \SafeFunction($searched_function));
			// parse
			$raw = file_get_contents($phpbb_root_path.'includes/functions_user.'. $phpEx);

			$stmts = $parser->parse($raw);
			// traverse
			$stmts = $traverser_safety->traverse($stmts);
			// pretty print

			$code = $prettyPrinter->prettyPrint($stmts);
			file_put_contents(__DIR__.'/cache/functions_user.'.$phpEx,'<?php '.$code.' ?>');
		} catch (\PhpParser\Error $e) {
			echo 'Parse Error: ', $e->getMessage();
		}

		//unicorn code is actually useless, im bored. At least, it was a good exercise



		try{
			$traverser_path     = new \PhpParser\NodeTraverser;
			//dont forget to escape the path, = preq_quote?
			$mypath = __DIR__;

			$traverser_path->addVisitor(new \PathFixer("\$phpbb_root_path \. \'includes/functions_content.\' \. \$phpEx",
						[ //ALAS we cant predict what kind of data you're gonna replace it with. so, you're gonna have to learn the types :(
						//PLus, we consider you do nothing but concatenating those string
							new \PhpParser\Node\Scalar\String_($mypath.'/cache/functions_content.'),
							new \PhpParser\Node\Expr\Variable('phpEx')
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
		} catch (\PhpParser\Error $e) {
			echo 'Parse Error: ', $e->getMessage();
		}
	}
}
