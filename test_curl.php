<?php
include "lib/curl.php";

$curl = curl::instance();
$rtn = $curl->disableJson()->request('http://0:8080/header.php', 'post', ['a'=>1]);
var_dump($rtn);


