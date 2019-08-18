<?php
header('Access-Control-Allow-Origin:http://m.weibo.cn');
//header('Access-Control-Allow-Headers: x-requested-with');
//header('Access-Control-Allow-Headers: a');
$r =array('server'=>$_SERVER, 'get' => $_GET,'post'=>$_POST,'cookie'=>$_COOKIE, 'file'=>$_FILES , 'raw'=>$GLOBALS["HTTP_RAW_POST_DATA"]);
if(isset($_GET['json'])){
    echo json_encode($r);
}else{
    var_dump($r);
}
#include 'func/debuging.php';
#include 'class/curl.class.php';

