<?php
class phpParser{
	static function parseParams($str, $start = 0){
		$params = [];
		$i = $start;
		$lastChar = '';
		while(isset($str{$i})){
			switch($str{$i}){
			case "'":
			case '"':
				list($param, $i ) = self::parseString($str, $i, $str{$i});
				//var_dump([$param,'string']);
				self::pushParams($params, $param, $lastChar);
				$i--;
				break;
			case '.':
				$lastChar = '.';
				break;
			case "(":
				list($innerParams, $i) = self::parseParams($str, $i+1);
				//var_dump(['inner'=>$innerParams]);
				$params[] = implode(',', $innerParams);
				break;
			case ")":
				$i++;
				break 2;
			default:
				$c = $str{$i};
				if(ctype_alnum($c) || strspn($c, '$_') === 1){
					list($param, $i) = self::parseSymbol($str, $i);
					self::pushParams($params, $param, $lastChar);
					$i--;
				}
				break;
			}
			//var_dump(['params'=>$params, 'remain'=>substr($str, $i), 'nextChar'=>@$str{$i+1}, 'lastChar'=>$lastChar]);
			$i++;
		}
		return [$params, $i];//$i points to the next pos of params, the char after ')'
	}
	static function pushParams(&$params, $param, &$lastChar){
		if($lastChar === '.'){
			$lastChar = '';
			$params[count($params)-1] .= "." .$param;
		}else{
			$params[] = $param;
		}
	}
	static function parseString($str, $start, $delimiter = "'"){
		$i = $start+1;
		//$end = ($delimiter === '.') ? true : false;
		$end = false;
		//$lastChar = ' ';
		while(isset($str{$i})){
			if($end === false){
				//$lastChar = '';
				if($str{$i} === '\\'){
					$i+=1;
				}elseif($str{$i} === $delimiter){
					$end = true;
				}
			}else{
				switch($str{$i}){
					case " ":
						break;
					case "'":
					case '"':
						$delimiter = $str{$i};
						$end = false;
						break;
					case ".":
						//$lastChar = '.';
						break 2;
					default:
						break 2;
				}
			}
			$i++;
		}
		$param = substr($str, $start, $i - $start);
		return [$param, $i]; //$i points to char after last delimiter
	}
	static function parseSymbol($str, $start){
		$i = $start+1;
		$symbol = '';
		while(isset($str{$i})){
			$char = $str{$i};
			switch($str{$i}){
			case "(":
				$params = [];
				list($params[], $i) = self::parseParams($str, $i+1, $str{$i});
				$i--;
				break;
			default:
				$c = strtolower($str{$i});
				//var_export("symbol::::$c:::\n");
				if(($c >= 'a' && $c <= 'z') || strspn($c, ': $_->')){
					$symbol .= $char;
				}else{
					//var_export("-----------".$char."------------end\n");
					break 2;
				}
			}
			$i++;
		}
		$symbol = substr($str, $start, $i - $start);
		return [$symbol, $i];
	}
}
