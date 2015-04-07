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
			new PhpParser\Node\Stmt\If_(
				new Expr\BooleanNot(
					new PhpParser\Node\Expr\FuncCall(
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
			/*Saved for memory, will be removed next commit  =
			//looks like smthing's wrong with reinjecting a Stmt\Function with the enterNode event (infinite loop) and 
				//in this event, adding the [$node] as a stmts return the content of the function, not the declaration itself
				//so we patch it :)
				,[
					'stmts' =>[new PhpParser\Node\Stmt\Function_(
									$node->name,
									['byRef'=>$node->byRef,
									'params'=>$node->params,
									'returnType'=>$node->returnType,
									'stmts'=>$node->stmts]
								)
							]
				]
			
			
			*/
			
			
		return $encapsulated_node;
	}
	
	private function removeFunction($function_index){
		unset($this->function[$function_index]);
	}
}
?>