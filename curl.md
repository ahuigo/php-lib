# Preface

# Curl Multi 
Curl 的并发请求，即不是基于线程thread 也不是基于进程 process 的，而是基于多种io 复用的和一种: select. 参见[](/p/linux-c-socket)

Bug for curl multi_select:
http://jp2.php.net/manual/en/function.curl-multi-select.php#115381

> "When libcurl returns -1 in max_fd, it is because libcurl currently does something that isn't possible for your application to monitor with a socket 
and unfortunately you can then not know exactly when the current action is completed using select(). 

> When max_fd returns with -1, you need to wait a while and then proceed and call curl_multi_perform anyway.
How long to wait? I would suggest 100 milliseconds at least, but you may want to test it out in your own particular conditions to find a suitable value. 


	if ( ($num = curl_multi_select($queue, 50)) === -1 ) { //non-busy (!) wait for state change 
		usleep(1);//wait a while
	}


## Example
Refer to `/curl_rolling.php` and `/curl_classic.php`

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

			//A request was just completed -- find out which one
			while ($done = curl_multi_info_read($queue)) {
				$ch = $done['handle'];
				$res = curl_multi_getcontent($ch);
				$responses[] = $res;
				curl_multi_remove_handle($queue, $ch);
			}

			//Blocks for state change . Wait for activity on any curl-connection
			if ( ($num = curl_multi_select($queue, 50)) === -1 ) { 
				usleep(10);//Wait a while and then call multi_exec , when select return -1
			}
		} while ($active > 0 ) ;

		curl_multi_close($queue);
		return $responses;
	}

Function:

	curl_multi_select($mh, $timeout = 1)
		Blocks until there is activity on any of the curl_multi connections. 
			On success, returns the number of descriptors contained in the the descriptor sets.
			Returns 0 if there was no activity on any of the descriptors.
			Returns -1 on a select failure(underlying ther select system call). 

	curl_multi_exec($mh, $running);
		Processes each of the handles in the stack
		This only return errors regarding the whole multi stack. There might still have occurred errors on individual transfers even when this function returns CURLM_OK.
		该函数仅返回关于整个批处理栈相关的错误。即使返回 CURLM_OK 时单个传输仍可能有问题。
			CURLM_CALL_MULTI_PERFORM	还有一些重要工作要做. 没有任何请求结束
			CURLM_OK	已经有数据要处理

## close
It's not need to call `curl_close` after `curl_multi_close`.

	//close the handles
	curl_multi_remove_handle($mh, $ch1);
	curl_close($ch1);//Not work
	var_dump(curl_exec($ch));//Still work

# Reference
- [php.ref.curl]

[php.ref.curl]: http://php.net/manual/en/ref.curl.php
