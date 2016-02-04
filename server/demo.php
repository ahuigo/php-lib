<?php
server(8000);
function select($rSocks, $wSocks = [], $eSocks = [], $timeout = null){
	return stream_select($rSocks, $wSocks, $eSocks, $timeout);
}
function server($port) {
	echo "Starting server at port $port...\n";

	$socket = @stream_socket_server("tcp://0:$port", $errNo, $errStr);
	if (!$socket) throw new Exception($errStr, $errNo);

	stream_set_blocking($socket, 0);//0: non-blocking mode

	while (true) {
		echo "server:waitForRead\n";
		$rSocks = [$socket];
		if(!select($rSocks)){
			die('select error!');	
		}
		$clientSocket = stream_socket_accept($socket, 0);

		echo "server:handleClient\n";
		handleClient($clientSocket);
	}
}

function handleClient($socket) {
	//yield waitForRead($socket);
	echo "handclient:Read\n";
	$data = fread($socket, 8192);

	//$data = "some data";
	$msg = "Received following request:\n\n$data";
	$msgLength = strlen($msg);

	$response = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg
RES;

	//yield waitForWrite($socket);
	echo "handclient:Write\n";
	fwrite($socket, $response);

	fclose($socket);
}
