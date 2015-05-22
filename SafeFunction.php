<?php 

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;

class SafeFunction extends PhpParser\NodeVisitorAbstract
{
	private $functions;
	public function __construct(array $functions){
		$this->functions = $functions;
	}



    public function leaveNode(Node $node) {
        $is_function = array_search($node->name,$this->functions);
		if ( $node instanceof Stmt\Function_ && $is_function!== FALSE){	
			$node = $this->encapsulate($node);
			$this->removeFunction($is_function);
			return $node;
		}
	}
	
	private function encapsulate($node){
		$encapsulated_node = 
			new Node\Stmt\If_(
				new Expr\BooleanNot(
					new Node\Expr\FuncCall(
						new Name\FullyQualified('function_exists'),
						[
							new Node\Arg(new Scalar\String_($node->name))
						]
					)
				)
				,[	
					'stmts' =>[$node]
				]
			);
			
		return $encapsulated_node;
	}
	
	private function removeFunction($function_index){
		unset($this->function[$function_index]);
	}
}
?>