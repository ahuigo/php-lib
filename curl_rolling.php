<?php
//include "curl.php";
$url = 'http://xxx.com?t=';
echo "start at ".date('r')."\n";
$res = rolling_curl(["${url}2", "${url}3"]);
var_dump($res); 
echo "end at ".date('r')."\n";

function rolling_curl($urls ) {
	$queue = curl_multi_init();
	$map = array();

	foreach ($urls as $url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_multi_add_handle($queue, $ch);
		$map[] = $ch;
	}

	$active = null;

	// execute the handles
	$responses = [];
	do{
		//If curl_multi_exec returns CURLM_CALL_MULTI_PERFORM, select simply blocks for $timeout and returns -1. So we shoud made a full_curl_multi_exec before multi_select
		while (($mrc = curl_multi_exec($queue, $active)) === CURLM_CALL_MULTI_PERFORM);//get new state(new activity connection)

			//Error occurs
			if($mrc != CURLM_OK){ break; }

		//a request was just completed -- find out which one
		while ($done = curl_multi_info_read($queue)) {
			$ch = $done['handle'];
			$res = curl_multi_getcontent($ch);
			$responses[] = $res;
			curl_multi_remove_handle($queue, $ch);
		}

		//Blocks for state change . Wait for activity on any curl-connection
		if ( ($num = curl_multi_select($queue, 0.2)) != -1 ) { 
			usleep(1);//Wait a while and call multi_exec
		}
	} while ($active > 0 ) ;

	curl_multi_close($queue);
	return $responses;
}
