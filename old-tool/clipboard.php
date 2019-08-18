<?php
if(isset($_GET['debug_headers'])){
	var_dump(array('server'=>$_SERVER, 'post'=>$_POST, '_file'=>$_FILE , 'raw'=>$GLOBALS["HTTP_RAW_POST_DATA"]));
	die;
}

new clipboard();
class clipboard{
	var $mc;
	var $cacheTime = 86400;
	function __construct(){
		$this->mc = memcache_init();

		$act = isset($_GET['act']) ? $_GET['act'] : 'get';
		switch($act){
		case "get": $this->get();break;
		case "set": $this->set();break;
		}
	}

	function get(){
		$ver = isset($_GET['ver']) ? $_GET['ver'] : $this->mc->get('clipboard_v');
		$rtn = $this->mc->get("clipboard_$ver");
		
		if(isset($_GET['view'])){
			if(isset($_GET['source'])){
				header('Content-Type:text/plain');
			}
			echo $rtn['code'];
		}else{
			$rtn['ip'] = $_SERVER['REMOTE_ADDR'];
			echo json_encode($rtn);	
		}
	}
	function set(){
		$_POST['setTime'] = $_SERVER['REQUEST_TIME'];
		$data = array(
			'setTime' => $_SERVER['REQUEST_TIME'],
			'expireTime' => $_SERVER['REQUEST_TIME'] + $this->cacheTime,
			'code' => $_POST['code'],
		);
		$ver = 1 + $this->mc->get('clipboard_v');
		$ver = $ver % 30;
		$rtn = $this->mc->set("clipboard_$ver", $data,0,$this->cacheTime);
		if($rtn){
			$rtn = $this->mc->set("clipboard_v", $ver,0,$this->cacheTime);
		}
		echo $ver;
	}
}

function debuging($var){
	if(isset($_GET['debug'])){
		var_dump($var);
	}
}
