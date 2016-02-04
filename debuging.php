<?php
/**
 * debuging('dtrace');          //查看调用栈
 * debuging($var);              //打印$var(var_dump)
 * debuging($var, 'php');       //打印$var(var_export)
 * debuging($var, $echo, 2); 	//以json格式输出$var
 */
function debuging($var = '', $echo = '', $die = false, $force = false){
    static $clear;
    if(0 && $clear === null){
        ob_end_flush();
        $clear = true;
    }

    static $trace;
    if($var === 'dtrace' || isset($_GET['dtrace']) && empty($trace)){
        $trace = 1;
        $echo = Debug::getTrace();
    }

    $force && $_GET['debug'] = 1;
    if(isset($_GET['debug'])){
		echo "================================= <br>\n";
		debugingPos();
        if($die === 2){
            header('Content-type: application/json');
            echo json_encode($var);
        }else{
            echo "<pre>\n";
            if($echo){
                echo "$echo:";
            }
            if($echo === 'php')
                var_export($var);
            else
                var_dump($var);
            echo "</pre>\n";
        }
        $die && die;
    }
}
function debugingPos(){
	//$tmp = debug_backtrace(2, 2)[1];
	$tmp = debug_backtrace();
	$pos = $tmp[1];
    if(isset($pos['class'])){
        echo "{$pos['class']}->{$pos['function']} <br>\n";
    }else{
        echo "{$pos['function']}(): {$pos['file']} . (line:{$pos['line']})<br>\n";
    }
}

/**
 * DEBUG
 */
isset($_GET['DEBUG']) && Debug::start();

class Debug{

	static $start_time = null;
	static function start(){
		self::$start_time = microtime(true);	
		if(function_exists('xhprof_enable')){
			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
		}
		ob_start(function($buf){
			return self::getMsg() . $buf;
		});
	}

	static function getMsg(){
		$msg = self::$CSS;
		$msg .= self::getExtraMsg();
		return $msg;
	}

    static private function getExtraMsg() {
        $extraMsg = [
            ['key', 'value'],
            ["memory_get_peak_usage: ", memory_get_peak_usage()/1000000 . " Mb",],
            ["exec_time", microtime(true) - self::$start_time." s"],
            ["REQUEST_URI", $_SERVER['REQUEST_URI'],],
            ["xhprof", self::getXhprof()],
        ];
        return self::toTable($extraMsg);
    }

    static function getXhprof(){
        if(function_exists('xhprof_disable')){
            $xhprof_data = xhprof_disable();
            $XHPROF_ROOT = '/opt/xhprof';
			if(file_exists($XHPROF_ROOT)){
				include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
				include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
				$source = 'xhprof_debug';
				$run_id = (new XHProfRuns_Default())->save_run($xhprof_data, $source);
				$url = "http://{$_SERVER['SERVER_ADDR']}:8000/index.php?run={$run_id}&source=$source";
				return "<a href='$url'> $url</a>";
			}
        }
    }

    static private function toTable($arr) {
        $table = '<table class="debug"><tr>';
        foreach (array_shift($arr) as $title) {
            $table .= "<th>$title</th>";
        }
        $table .= '</tr>';
        foreach ($arr as $list) {
            $table .= '<tr>';
            foreach ($list as $v) {
                $table .= "<td>$v</td>";
            }
            $table .= '</tr>';
        }
        $table .= '</table>';
        return $table;
    }
	static function getTrace(){
		try{
			static $lastTime;
			$lastTime or $lastTime = $_SERVER['REQUEST_TIME'];
			$currTime = microtime(true);
			$totalTime = $currTime-$_SERVER['REQUEST_TIME'];
			$execTime = $currTime-$lastTime;$lastTime = $currTime;
			$msg = "<hr>\n".
				"execTime: $execTime s.<br/>\n" .
				"totalTime: $totalTime s.<br/>\n".
				"Trace:<br/>\n";
			throw new Exception();
		}catch(Exception $e){
			//debug_print_backtrace();
			return $msg . $e->getTraceAsString();
		}
		/*
        static $lastTime;
        $lastTime or $lastTime = $_SERVER['REQUEST_TIME'];
        $currTime = microtime(true);
        $totalTime = $currTime - $_SERVER['REQUEST_TIME'];
        $execTime = $currTime - $lastTime;
        $lastTime = $currTime;
        $msg = "<hr>\n" .
            "execTime: $execTime s.<br/>\n" .
            "totalTime: $totalTime s.<br/>\n" .
            "Trace:<br/>\n";
        $tmp = debug_backtrace();
        $msg .= var_export($tmp);
        return $msg;
		 */
	}

    static $CSS = <<<MM
    <style>
    table.debug {
        border:2px solid #666;
        border-collapse: collapse;
        margin:15px;
    }

    .debug tr td{
        border:1px solid #666;
    }
    </style>
MM;
}

