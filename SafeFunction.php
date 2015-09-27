<?php

class SafeFunction extends PhpParser\NodeVisitorAbstract
{
	private $functions;
	public function __construct(array $functions){
		$this->functions = $functions;
	}



    public function leaveNode(PhpParser\Node $node) {
		if($node instanceof PhpParser\Node\Stmt\Function_){
		$is_function = array_search($node->name,$this->functions);
			if ( $is_function!== FALSE){
				$node = $this->encapsulate($node);
				$this->removeFunction($is_function);
				return $node;
			}
		}
	}

	private function encapsulate($node){
		$encapsulated_node =
			new PhpParser\Node\Stmt\If_(
				new PhpParser\Node\Expr\BooleanNot(
					new PhpParser\Node\Expr\FuncCall(
						new PhpParser\Node\Name\FullyQualified('function_exists'),
						[
							new PhpParser\Node\Arg(new PhpParser\Node\Scalar\String_($node->name))
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
