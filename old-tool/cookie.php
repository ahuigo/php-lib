<?php
$v = isset($_COOKIE['key']) ? $_COOKIE['key']:0;
setcookie('key', $v+1);
setcookie('name', 'ahui');
setcookie('age', '100', 0, "/", '.sinaapp.com');

echo "Your cookie is:\n";
foreach($_COOKIE as $k=>$v){
    echo "$k=>$v\n";
}
echo "key:".$v."\n";
