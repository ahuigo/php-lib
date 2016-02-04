<?php
/**
 *
	$a = new ReflectionVclass(new a);
	$a = new ReflectionVclass('a');
	echo $a->getMethodCode('b');
 */

class ReflectionVclass extends ReflectionClass{
	function getMethodCode($name, $comment = false){
		$fun = $this->getMethod($name);
		$start = $fun->getStartLine();
		$end = $fun->getEndLine();

		$fileName = $fun->getFileName();
		$file = new SplFileObject($fileName);
		$file->seek($start-1);

		$i=0;
		$str = $comment ? $fun->getDocComment() : '';
		while($i++ < $end+1 - $start){
			$str .= $file->current();
			$file->next();
		}
		return $str;
	}
	function getCode($comment = false){
		$start = $this->getStartLine();
		$end = $this->getEndLine();
		$fileName = $this->getFileName();
		return `sed -n '$start,{$end}p' $fileName`;
	}
}
