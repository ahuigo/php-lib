<?php
#$fp = fopen('php://input', 'r');
#stream_set_blocking($fp, false);
$count = $_COOKIE['count'] ??0;
setcookie('count', ++$count, time()+3600);
$a = [
    'cookie'=>$count,
    'get'=>$_GET,
    'post'=>$_POST,
    'files'=>$_FILES,
    'input'=>file_get_contents("php://input"),
    'server'=>$_SERVER,
    'headers'=>getallheaders(),
];
var_dump($a);
