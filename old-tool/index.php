<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>简版转换器</title>
		<style>
			.gray{color:#9d9d9d;}
			h1{text-align:center;}
		</style>
	</head>
	<body>
		<div style="margin:0 auto;width:90%">
			<form action="" method="post">
				<h1>简版转换器</h1>
				HTML:<input type="submit"/> <a href="http://hilo.sinaapp.com/src.php">查看源代码 </a><br/>
				<textarea style="width:100%" rows="10" name="html"><?php echo htmlspecialchars(@$_POST['html']); ?></textarea>
				<input type="submit"/>
				<input type="checkbox" checked="checked" name="inputRand" value="on"/>
				<span class="gray">带随机数{$exp.inputRand}</span><br/>

				<b>preg_replace()自定义</b><br/>
				<span class="gray">自定义正则pattern，如: #&lt;span\s+[^&gt;]*class="tip"[^&gt;]*&gt;(.*)&lt;/span&gt;#iuU</span><br/>
				<input type="text" name="pattern" size="78" value="<?php echo htmlspecialchars(@$_POST['pattern']); ?>"/><br/>
				<span class="gray">自定义正则replace，如:【$1】</span><br/>
				<input type="text" name="replace" size="78" value="<?php echo htmlspecialchars(@$_POST['replace']); ?>"/><br/>

				<span class="gray"><a href="mailto:xuehui1@staff.sina.com.cn">有Bug?</a></span>
			</form>
			<?php
			if (isset($_GET['debug'])){
				echo '<pre>';
				var_dump($_SERVER);
				echo '</pre>';
			}
			ini_set('display_errors', 1);
			ini_set('error_reporting', -1);
			if (isset($_POST['html'])) {
				$content = $_POST['html'];
				if (!empty($_POST['replace'])) {
					$content = preg_replace($_POST['pattern'], $_POST['replace'], $content);
				}
				$wml = new Wml();
				$inputRand = empty($_POST['inputRand']) ? '' : true;
				$wml->convert($content, $inputRand);
				echo 'WML:<br/><textarea cols="100" rows="80">' . htmlspecialchars($content) . "</textarea>";
			}
			?>
		</div>
	</body>
</html>
<?php
/**
 * WML CLASS 
 */
class Wml {

	var $go; //wml的超链接信息
	var $checkboxes; //存储checkbox选项
	var $radios; //存储radio选项
	var $indent; //控制缩进
	var $tab; //
	var $anchor, $select, $selectSuffix;
	var $inputRand; //加随机数
	var $additionFormLabel = '<addition_form_label />'; //遇到这个标签时，就得执行一下$this->createSelect();
	var $purgeBR; //$this->purgeBR 为是否 应该 清除<br/>的标志，如果它为真，遇到<br/>时就应该把它给清理掉,并且把$this->purgeBR置为false

	/**
	 *
	 * @param type $content
	 * @throws Exception 
	 */

	function convert(&$content, $inputRand = true) {
		if ($inputRand) {
			$this->inputRand = '{$exp.inputRand}';
		}
		try {
			$error = $this->checkwml($content);
			if ($error) {
				throw new Exception($error);
			}
			$content = str_replace("\r\n", "\n", $content);
			$content = preg_replace('#<div\s+class="s"\s*>\s*</div>#iU', '------------<br/>', $content);
			$content = preg_replace('#<div[^>]*>#i', '', $content);
			//$content = preg_replace('#{\*.*\*}#sU','',$content);//干掉注释
			$content = preg_replace('#<br[^>/]*>#i', '<br/>', $content);
			$content = preg_replace('#</div[^>]*>#i', '<br/>', $content);

			$content = preg_replace('#<span\s+[^>]*class="tip"[^>]*>(.*)</span>#iuU', '【$1】', $content);

			$content = preg_replace('#<span[^>]*>#i', '', $content);
			$content = preg_replace('#</span[^>]*>#i', '', $content);
			$content = preg_replace('#(<.*)class=".*"(.*>)#iU', '$1$2', $content);
			//echo htmlspecialchars('iloveu'.$content);
			$content = preg_replace('#(<.*)href(.*>)#iU', '$1href$2', $content);

			//form convert
			$content = preg_replace_callback('#<form[^>]+(?:action="(?<action>[^>]*)"[^>]*|method="(?<method>[^>]*)"[^>]*){2}[^>]*>(?<content>.*)</form>#sU', array($this, 'form_convert'), $content);
			//delete continuation line!
			$content = preg_replace('#(\s*\n){3,}#', "\n\n", $content);

			if ($this->inputRand) {
				$content = preg_replace('#(<input\s+[^>]*\bname=")([^"]+?)"#isU', '$1$2' . $this->inputRand . '"', $content);
			}
		} catch (exception $e) {
			$error = $e->getmessage();
			die('<font color="red">' . htmlspecialchars($error, null, 'utf-8') . '</font>');
		}
	}

	function form_convert($matches) {
		//echo 'asdfasdfahui';print_r($matches);die('d');
		//echo (htmlspecialchars('ahui'.print_r($matches['content'],true)));die('d');
		$this->radios = array();
		$this->checkboxes = array();
		$this->tab = '	';
		$matches['content'] = preg_replace('#{\*.*\*}#sU', '', $matches['content']); //干掉注释
		preg_match('#(\s*)<input#', $matches['content'], $indent);
		$this->indent = $indent[1];
		$this->anchor = $this->indent . '<anchor>' . "\n";
		$this->anchor .= $this->indent . $this->tab . '<go href="' . $matches['action'] . '" accept-charset="utf-8" method="' . $matches['method'] . '">' . "\n";
		//insert a label.
		$content = $matches['content'] . $this->additionFormLabel;
		//textarea convert
		$content = preg_replace_callback('#<textarea(?<params>.*)>(?<text>.*)</textarea>#sU', array($this, 'textarea_to_input'), $content);
		debugging($content, 'content with addtional label');
		preg_match_all('#<(?<label>[^/]\w*)\s+(?<params>.*)>(?<text>[^<\n\r]*?)#sU', $content, $matches);
		debugging(array('match' => $matches, 'content' => $content), 'preg_match_all label');
		//other label convert
		$content = preg_replace_callback('#<(?<label>[^/]\w*)\s+(?<params>.*)>(?<text>[^<\n\r]*?)#sU', array($this, 'label_convert'), $content);
		//delete label
		$content = substr($content, 0, -strlen($this->additionFormLabel));
		$this->anchor .= $this->indent . $this->tab . '</go>' . "{$this->go}\n";
		$this->anchor .= $this->indent . '</anchor>' . "\n";
		return $content . "\n" . $this->anchor;
	}

	/**
	 * 
	 * @param type $str
	 * @return type 
	 */
	function parseParams($str) {
		if (preg_match_all('#(\S+)\s*=\s*"(.*)"#U', $str, $params)) {
			return array_combine($params[1], $params[2]);
		} else {
			die('String:' . htmlspecialchars($str) . ' has no params!');
		}
	}

	/**
	 * 
	 */
	function textarea_to_input($matches) {
		$this->selectSuffix = $content = '';
		if ($params = $this->parseParams($matches['params'])) {
			$str = '<input type="text" name="' . $params['name'] . '" value="' . $matches['text'] . '" />';
		} else {
			die("Couldn't match string:'{$matches[2]}'!");
		}
		//die(htmlspecialchars($str));
		return $str;
	}

	/**
	 * 	将form标签转化成postfield标签
	 * 	$this->purgeBR 为是否 应该 清除<br/>的标志，如果它为真，遇到<br/>时就应该把它给清理掉,并且把$this->purgeBR置为false
	 * 	
	 */
	function label_convert($matches) {
		$this->selectSuffix = ''; //select 前缀
		$content = ''; //转化结果content
		debugging($matches, 'label_convert start label');
		switch ($matches['label']) {
			case 'input':
				if ($params = $this->parseParams($matches['params'])) {
					$params['type'] or die('no type!');
					//radio & checkbox
					if ('radio' != $params['type'] || 'checkbox' != $params['type']) {
						$content .= $this->create_select();
						debugging($content, 'content xx create_select()');
					}
					switch ($params['type']) {
						case 'text':
						case 'password':
							$params['value'] = '$(' . $params['name'] . ')';
							$this->anchor .= $this->create_postfield($params);
							break;
						case 'hidden':
							$this->anchor .= $this->create_postfield($params);
							//$this->purgeBR=true;
							$matches[0] = @$matches['text'];
							break;
						case 'radio':
						case 'checkbox':
							/**
							 * select = array(
							 * 	'name'=>
							 * 	'type'=> checkbox radio or other
							 * 	'data'=>array(
							 * 		'value',
							 * 		'text',
							 * 		.....
							 * 	)
							 * ); 
							 */
							!empty($this->select['name']) or $this->select['name'] = $params['name'];
							!empty($this->select['type']) or $this->select['type'] = $params['type'];
							if (!isset($params['value'])) {
								$params['value'] = 'on'; //default
								//die('<font color="red">Error!No value for checkbox!</font>'.htmlentities($matches[0],null,'utf-8').'</body></html>');
							}
							$this->select['data'][] = array('value' => $params['value'], 'text' => $matches['text']);
							debugging($this->select, 'this->select xx');
							$matches[0] = '';
							break;
						case 'submit':
							$this->go = $params['value'];
							$this->purgeBR = true;
							$matches[0] = '';
							break;
						case 'select':
							$this->anchor .= $this->create_postfield($params);
							break;
						case 'file':
							return; //清除 type="file"
							break;
						default:
							return; //清除
							die('Please add  input type: ' . $params['type']);
					}
				} else {
					die("Couldn't match string:'{$matches[2]}'!");
				}

				$res = preg_match('#<form.+</form>#sU', $content, $match);
				debugging(array('res' => $res, 'match' => $match, 'content' => $content), 'input label');
				break;
			case 'br':
				if ($this->select) {
					$this->selectSuffix = "<br/>\n";
					return;
				}
				if ($this->purgeBR) {
					$this->purgeBR = false;
					return;
				}
				break;
			case 'select':
				$params = $this->parseParams($matches['params']);
				$params['value'] = '$(' . $params['name'] . ')';
				$this->anchor .= $this->create_postfield($params);
				break;
			case 'textarea':
				break;
			default:
				debugging($this->select, 'select');
				$content .= $this->create_select();
		}
		debugging($content . $matches[0], 'label_convert result');
		return $content . $matches[0];
	}

	function create_postfield(&$params) {
		if ($this->inputRand && substr($params['value'], 0, 2) == '$(' && substr($params['value'], -1) == ')') {
			$params['value'] = substr($params['value'], 0, -1) . $this->inputRand . ')';
		}
		return $this->indent . $this->tab . $this->tab . '<postfield name="' . $params['name'] . '" value="' . $params['value'] . '" />' . "\n";
	}

	function create_select() {
		if ($this->select) {
			$type = $this->select['type'];
		} else {
			return;
		}
		$params = &$this->select['data'];
		switch ($type) {
			case 'radio':
				$str = '<select name="' . $this->select['name'] . $this->inputRand . '">' . "\n";
				break;
			case 'checkbox':
				if (1 == count($params)) {
					$str = '<select name="' . $this->select['name'] . $this->inputRand . '" >' . "\n";
					$params[] = array(
						'value' => 1 - intval($params[0]['value']),
						'text' => '不' . $params[0]['text'],
					);
				} else {
					$str = '<select name="' . $this->select['name'] . '" multiple="true">' . "\n";
				}
		}
		foreach ($params as $v) {
			$str .= $this->indent . $this->tab . '<option value="' . $v['value'] . '">' . trim($v['text']) . '</option>' . "\n";
		}
		$str .= $this->indent . '</select>' . $this->selectSuffix . "\n";
		$this->anchor .= $this->create_postfield(
				$params = array(
			'name' => $this->select['name'],
			'value' => '$(' . $this->select['name'] . $this->inputRand . ')',
				));
		$this->select = array();
		return $str;
	}

	/**
	 * 当含有异常标签时，返回错误信息
	 * @param type $content
	 * @return string 
	 */
	function checkWml(&$content) {
		if (strpos($content, '<table'))
			return 'Not support Tag <table>!';
		if (preg_match('#<a[^>]*target=#sU', $content)) {
			return 'Not support Attribute:target!';
		}
	}

}

function debugging($var='',$echo='',$die=false,$force=false){
    $force && $_GET['debug']=1;
    if(isset($_GET['debug'])){
		ob_end_flush();
		ob_end_flush();
		ob_end_flush();
		ob_end_flush();
		if( 'trace' == $echo ){
			dTrace();
		}elseif($die == 2){
			header('Content-type: application/json');
			echo json_encode($var);
		}else{
			echo '<pre>'."\n";
				if($echo){
					echo "$echo:";
				}
				if($echo != 'php') 
					var_dump($var);
				else 
					var_export($var);
			echo '</pre>'."\n";
		}
        $die && die();
    }
}
function dTrace($die=false){
	//return;
    if(isset($_GET['debug'])){
        try{
			global $lastTime;
			$lastTime or $lastTime = $_SERVER['REQUEST_TIME'];
            throw new Exception();
        }catch(Exception $e){
			$currTime = microtime_float();
			$totalTime = $currTime-$_SERVER['REQUEST_TIME'];
			$execTime = $currTime-$lastTime;$lastTime = $currTime;
			echo "execTime: $execTime s.<br/>\n";
			echo "totalTime: $totalTime s.<br/>\n";
			
            debugging($e->getTraceAsString(),'',$die);
        }
    }
}

function toString(&$arr) {
	foreach ($arr as &$v) {
		if (is_array($v)) {
			toString($v);
		} else {
			$v = htmlspecialchars($v);
		}
	}
}



