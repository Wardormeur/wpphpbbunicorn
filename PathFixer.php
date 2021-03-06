<?php

class PathFixer extends PhpParser\NodeVisitorAbstract
{
	private $path_to_fix;
	private $fixed_paths;
	private $temp_path_string;

	public function __construct( $path_to_fix, array $fixed_paths){
		$this->path_to_fix = $this->clean($path_to_fix);

		$this->fixed_paths = $fixed_paths;
	}



    public function enterNode(PhpParser\Node $node) {
		//We want to limit this fix to path for function that make use of paths
		//this is achoice for my use case, be free to modify/remove it
		//var_dump($node);
		if ( $node instanceof PhpParser\Node\Expr\Include_ ){
				//We flush the string content
				$this->temp_path_string = '';
		}
		if( $node instanceof PhpParser\Node\Expr\Variable ){
			$this->temp_path_string = $this->temp_path_string. $node->name;
		}
		if( $node instanceof PhpParser\Node\Scalar\String_){
			$this->temp_path_string = $this->temp_path_string. $node->value;
		}
		return $node;
	}

	public function leaveNode(PhpParser\Node $node){
	//on leave node, if the full string have been found, we replace its content by our new one
		if ( $node instanceof PhpParser\Node\Expr\Include_ ){
			if($this->temp_path_string == $this->path_to_fix){
				$node = $this->fixpath($node);
				//$this->removePath($is_path);
				return $node;
			}
		}

	}

	private function clean($path){

		//preg_quote reverse?
		//trim, watfor? :D
		$cleaned = str_replace(['$','\\\'','\\.',' '],'',$path);

		return $cleaned;
	}

	private function fixpath($node){
		//$s_node = new Node\Expr\Include_(;
		for ($i=count($this->fixed_paths)-1;$i>0;$i--){
			if($i==(count($this->fixed_paths)-1)){
				$s_node = new PhpParser\Node\Expr\BinaryOp\Concat($this->fixed_paths[count($this->fixed_paths)-$i-1], $this->fixed_paths[count($this->fixed_paths)-$i],[]) ;
			}else{
				$s_node = new PhpParser\Node\Expr\BinaryOp\Concat($s_node, $this->fixed_paths[count($this->fixed_paths)-$i],[]) ;
			}
		}

		return new PhpParser\Node\Expr\Include_($s_node,$node->type,[]);
	}

	private function removePath($path_index){
		unset($this->function[$path_index]);
	}
}
?>
