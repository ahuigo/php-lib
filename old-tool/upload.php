<?php
use sinacloud\sae\Storage as Storage;

$stor = new SaeStorage();
$domain = 'hilo';
$fileDataName = $_FILES["file"]["name"];
$fileDataName = 'a.txt';
if(isset($_GET['file']) || isset($_POST['file'])){
	var_dump(array($_GET, $_POST));
}
if(isset($_FILES["file"]["tmp_name"])) {
	if(empty($_FILES['file']['tmp_name'])){
		die('Try again!');
	}
	var_dump($_FILES);
	//添加图片上传到STORAGE
	//$dumpdata = file_get_contents($_FILES["file"]["tmp_name"]);
	//$dowLoadUrl = $stor->write($domain,$fileDataName,$dumpdata);//用write就行了
	$stor->upload( $domain,$fileDataName,$_FILES['file']['tmp_name']);   
	$url = $stor->getUrl($domain,$fileDataName);//如果上传图片的处理地址
	echo "上传的文件:\n";
	echo($url);
}else{
//	echo $stor->read($domain, $fileDataName);
}
die;
/******************************************************************************
 *
 *
 **/
$domain = 'hilo';
$fileDataName = $_FILES["file"]["name"];
$fileDataName = 'a.txt';
if(isset($_GET['file']) || isset($_POST['file'])){
	var_dump(array($_GET, $_POST));
}
if(isset($_FILES["file"]["tmp_name"])) {
	if(empty($_FILES['file']['tmp_name'])){
		die('Try again!');
	}
	//添加图片上传到STORAGE
	$s = new Storage();
	// 创建一个Bucket test
	$s->putBucket("test");
	$s->putObjectFile($_FILES['file']['tmp_name'], "test", $fileDataName);
	//$url = $stor->getUrl($domain,$fileDataName);//如果上传图片的处理地址
	// 获取test这个Bucket中的Object对象列表
	$info = $s->getBucket("test");
	echo "上传的文件:\n";
	var_dump($info);
}else{
//	echo $stor->read($domain, $fileDataName);
// 从test这个Bucket读取Object 1.txt，输出为此次请求的详细信息，包括状态码和1.txt的内容等
	var_dump($s->getObject("test", $fileDataName));
}

die;
