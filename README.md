# CURL Usage

	include "curl.php";
	$res = curl::instance()->request($url, 'post', $params = array('var'=>'value'));
	echo $res;

## Download file
Download file from ftp

	$outfile = fopen('out.txt', 'wb');
	$opts = [
		'CURLOPT_FILE'=>$outfile,
		'CURLOPT_USERPWD'=> 'user:passwd',
		'CURLOPT_RETURNTRANSFER'=>1,//Not work when CURLOPT_FILE is true
	];
	echo curl::instance()->request('ftp://ip/a.txt', 'get', [], [], $opts);//1 when success



## Upload file

### Upload file via ftp

	$fp = fopen($file = 'a.txt', 'r');
	$opts = [
		CURLOPT_INFILE=>$fp,
		CURLOPT_UPLOAD=>1,
		CURLOPT_INFILESIZE=> filesize($file),
		CURLOPT_USERPWD=> 'user:passwd',
	];
	$ch = curl::instance()->debug();
	echo $res = $ch->request($url = 'ftp://ip/data/b.txt', 'get', [], [], $opts);

### Upload via POST

If php >= 5.5.0

	include "curl.php";
	$url = 'http://localhost:8000/up.php?haha=1';
	//method 1: $cfile = '@img/a.png';//unsafe
	//Process-oriented
	$cfile = curl_file_create('img/a.png','image/jpeg','pic');
	//Object-Oriented
	$cfile = new CURLFile('img/a.png','image/jpeg','pic');
	$res  = curl::instance()->request($url, 'file', $params= array('var'=> 'value', 'pic'=>$cfile));
	var_dump($res);//Return true when success

If php < 5.5.0

	$file = 'img/a.png';
	$basename = basename($file);
	$boundary = uniqid('prefix');
	$header = array(
		//'Expect: 100-continue',
		'Content-Type: multipart/form-data; boundary='. $boundary,
	);

	$params = [];
	//file
	$data = file_get_contents($file);
	$params[] = implode("\r\n", array(
		"--$boundary",
		"Content-Disposition: form-data; name=\"pic\"; filename=\"a.png\"",
		"Content-Type: image/png",
		"",
		"$data",
	));

	//var-value
	$params[] = implode("\r\n", array(
		"--$boundary",
		"Content-Disposition: form-data; name=\"var\"",
		"",
		"value",
	));

	//end
	$params[] = "--$boundary--\r\n";//It seems unnecessary in my test.
	$url = 'http://localhost:8000/up.php';
	$res = curl::instance()->request($url, 'post', implode("\r\n",$params), $header);
	var_dump($res);

It's equal to the curl shell cmd below：

	curl 'http://localhost:8000/up.php' -H 'Content-Type: multipart/form-data; boundary=W' -d $'--W\r\nContent-Disposition: form-data; name="pic"; filename="a.png"\r\nContent-Type: image/png\r\n\r\ndata\r\n--W\r\nContent-Disposition: form-data; name="var"\r\n\r\nvalue\r\n--W--\r\n'
	curl 'http://localhost:8000/up.php'  -F 'pic=@img/a.png'

## debug

	$ch = curl::instance();
	$ch->debug();
	$ch->request('http://baidu.com');
	var_dump($ch->getCurlCmd());

## SSL
SSLv2 and SSLv3 have high Vulnerability. Please use TLSv1 or higher version.

	$ch->setOpts([CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1]);

### Not Verify SSL

	$ch->setOpts([CURLOPT_SSL_VERIFYPEER => false]);

## Asynchronous


# debuging

	debuging('dtrace');
