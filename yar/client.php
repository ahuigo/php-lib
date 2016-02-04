<?php
/*
$c = new Yar_Client("http://localhost:8000/api.php");
$result = $c->test("first", 'second');
var_dump($result);
die;
 */
function callback($retval, $callinfo) {
     var_dump(['rtn'=> $retval, 'callinfo'=>$callinfo]);
}

/**
 * 并行时不可以使用全部使用localhost , 它不支持本地hosts 解析?
 */
Yar_Concurrent_Client::call("http://localhost:8000/api.php","test", array("param1"), "callback");
Yar_Concurrent_Client::call("http://localhost:8000/api.php","test", array("param2"), "callback");
Yar_Concurrent_Client::call("http://localhost:8000/api.php","test", array("param3"), "callback");
Yar_Concurrent_Client::call("http://127.0.0.1:8000/api.php","test", array("param4", "2rd"), "callback");
Yar_Concurrent_Client::loop(); //send
