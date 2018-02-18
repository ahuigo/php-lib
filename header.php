<?php
#$fp = fopen('php://input', 'r');
#stream_set_blocking($fp, false);
$a = [
    'get'=>$_GET,
    'post'=>$_POST,
    'cookie'=>$_COOKIE,
    'files'=>$_FILES,
    'input'=>file_get_contents("php://input"),
    'server'=>$_SERVER,
    'headers'=>getallheaders(),
];
$count = $_COOKIE['count'] ??0;
setcookie('count', ++$count, time()+3600);
var_dump($a);
