<?php

ini_set('display_errors', 1);
ini_set('error_reporting', -1);
$num = isset($argv[1]) ? $argv[1] : 2003.56;
echo Rmb::getRmbNum($num);
/**
 *
 * 	获取人民币的中文表示（>=php 5.3.0）
 */
class Rmb {

	private static $units = array('', '万', '亿', '兆', '京');
	private static $weights = array('', '拾', '佰', '仟');
	private static $numchars = array(
		'零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖',
	);

	static function getRmbNum($num = 12004213.234) {
		$num = strval($num);
		if ('' === $num || substr_count($num, '.') > 1 || strlen($num) != strspn($num, '0123456789.')) {
			return false;
		} elseif ('0' === $num) {
			return '零圆';
		}
		if (false === strpos($num, '.')) {
			$num.='.';
		}
		list($int, $dec) = explode('.', $num);

		$dec = self::getRmbNumDec($dec);
		$int = self::getRmbNumInt($int); 
		$int && $int .= '圆';
		return $int . $dec;
	}

	static function getRmbNumDec($num) {
		$str = '';
		if (isset($num[0])) {
			$str.=self::$numchars[$num{0}] . '角';
			if (isset($num[1])) {
				$str.=self::$numchars[$num{1}] . '分';
			}
		}
		return $str;
	}

	static function getRmbNumInt($num) {
		static $init = false;
		if(empty($init)){
			$init = true;
			$filter = function(&$item, $k) {
					$item = strrev($item);
				};
			array_walk(self::$units, $filter);
			array_walk(self::$weights, $filter);
			array_walk(self::$numchars, $filter);
		}
			
		$num = strrev(strval($num));
		$num = str_split($num, 4);

		$zhNum = '';
		foreach ($num as $k => &$item) {
			$zhNumSub = '';
			for ($i = 0, $len = strlen($item); $i < $len; ++$i) {
				if ($digit = $item{$i}) {
					$weight = self::$weights[$i];
					$zhNumSub .= $weight . self::$numchars[$digit];
				}elseif(!empty($lastDigit)){//仅保留一个零(上一个数字是零时)
					$zhNumSub .= strrev('零');//不用再加单位weight
				}
				$lastDigit = $digit;
			}
			$zhNumSub && $zhNum .= self::$units[$k] . $zhNumSub;//去掉大单位
		}
		return strrev($zhNum);
	}
}

//按拼音首字母排序
$arr = array(
	'成都'=>'028',
	'北京'=>'010',
);
PinYin::ukrsort($arr);
var_dump($arr);

class PinYin{
	/**
	 *
	 * 比较拼音首字母(基于字符是按拼音顺序编码) 
	 */
	static function cmp(&$a, &$b) {
		$a = iconv('utf-8', 'gbk', $a);
		$a = $a[0];
		$b = iconv('utf-8', 'gbk', $b);
		$b = $b[0];
		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? 1 : -1;
	}

	static function ukrsort(&$arr) {
		foreach ($arr as $k => $v) {
			if (is_array($arr[$k])) {
				ukrsort($arr[$k]);
			}
		}
		uksort($arr, array(__CLASS__, 'cmp'));
	}

	/**
	 * 获取汉字拼音首字母(基于字符是按拼音顺序编码)
	 */
	static function getFirstLetter($str) {
		$fchar = ord($str{0});
		if ($fchar >= ord("A") and $fchar <= ord("z"))
			return strtoupper($str{0});
		if (!is_string($str)) {
			var_dump($str);
			return;
		}
		$s1 = @iconv("UTF-8", "gbk", $str);
		$s2 = @iconv("gbk", "UTF-8", $s1);
		if ($s2 == $str) {
			$s = $s1;
		} else {
			$s = $str;
		}

		$asc = ord($s{0}) * 256 + ord($s{1}) ;
		if ($asc >= 45217 and $asc <= 45252)
			return "A";
		if ($asc >= 45253 and $asc <= 45760)
			return "B";
		if ($asc >= 45761 and $asc <= 46317)
			return "C";
		if ($asc >= 46318 and $asc <= 46825)
			return "D";
		if ($asc >= 46826 and $asc <= 47009)
			return "E";
		if ($asc >= 47010 and $asc <= 47296)
			return "F";
		if ($asc >= 47297 and $asc <= 47613)
			return "G";
		if ($asc >= 47614 and $asc <= 48118)
			return "I";
		if ($asc >= 48119 and $asc <= 49061)
			return "J";
		if ($asc >= 49062 and $asc <= 49323)
			return "K";
		if ($asc >= 49324 and $asc <= 49895)
			return "L";
		if ($asc >= 49896 and $asc <= 50370)
			return "M";
		if ($asc >= 50371 and $asc <= 50613)
			return "N";
		if ($asc >= 50614 and $asc <= 50621)
			return "O";
		if ($asc >= 50622 and $asc <= 50905)
			return "P";
		if ($asc >= 50906 and $asc <= 51386)
			return "Q";
		if ($asc >= 51387 and $asc <= 51445)
			return "R";
		if ($asc >= 51446 and $asc <= 52217)
			return "S";
		if ($asc >= 52218 and $asc <= 52697)
			return "T";
		if ($asc >= 52698 and $asc <= 52979)
			return "W";
		if ($asc >= 52980 and $asc <= 53688)
			return "X";
		if ($asc >= 53689 and $asc <= 54480)
			return "Y";
		if ($asc >= 54481 and $asc <= 55289)
			return "Z";
		return null;
	}


}


