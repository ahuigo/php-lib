<?php
die;
use sinacloud\sae\Storage as Storage;
$stor = new Storage();
$domain = 'hilo';
$bucketName = 'hilo';
$fileName = 'log.txt';
$filePath = "saestor://$domain".'/log.txt';

if(isset($_POST['clean']) || isset($_GET['clean'])){
	file_put_contents($filePath, " ");
	die('clean!');
}

if(!empty($_POST['str'])){
	$str = file_get_contents($filePath) . $_POST['str']."\n";
	file_put_contents($filePath, $str);
	$url = $stor->getUrl($bucketName,$fileName);
	echo $url;
}

/*
$file = 'log.txt';
$port = $_SERVER['SERVER_PORT'];
if(isset($_GET['clean'])){
	unlink($file);
	die('clean!');
}
if(isset($_POST['str'])){
	$str = "{$_POST['str']}\n";
	$fp = fopen($file, 'a+');
	fwrite($fp, $str);
	echo "http://{$_SERVER['SERVER_ADDR']}:$port/$file";
}
 */
