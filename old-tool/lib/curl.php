<?php
/**
 * @author hilojack
 * @desc Refer to : http://github.com/hilojack/php-curl
 */
class curl {

	private $ch;
	private $_option;
	private $_error;
	private $_errno;
	private $_codeInfo ;
	private	$debug = true;
	public $curlInfo = array();
	static private $instance;

	private function __construct() {
	}
	public function __get($name){
		return $this->$name;
	}
	public function debug($debug = true){
		$this->debug = $debug;
		return $this;
	}
	/**
	 *  
	 */
	public function init() {
		$this->_ch = curl_init();
		$this->_option = array(
			CURLOPT_TIMEOUT => 3600,
			CURLOPT_CONNECTTIMEOUT => 50,
			//CURLOPT_HEADER => false, //default false(output do not include header)
			CURLOPT_RETURNTRANSFER => true, //1. default false: output to stdout. 2. true: return result(only when CURLOPT_FILE is empty)
			//CURLOPT_NOBODY => 0, //default: false/0, if set true/1, the server will not response body(only when METHOD is GET)
			//CURLOPT_FOLLOWLOCATION => 1, //default:1 重定向到哪儿我们就去哪儿 
			CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.19 (KHTML, like Gecko) Ubuntu/12.04 Chromium/18.0.1025.168 Chrome/18.0.1025.168 Safari/535.19',
			CURLOPT_REFERER => 'http://g.cn',
		);
	}

	/**
	 * Refer to: http://php.net/manual/zh/function.curl-setopt.php
	 */
	protected function _setOpts($opts) {
		/**
			CURLOPT_HEADER => false, //default false(output do not include header)
			CURLOPT_USERPWD = 'user:passwd';
			CURLOPT_HTTPAUTH = $opt;
		 */
		$this->_option = $opts + $this->_option;
		return $this;
	}

	protected function _setHeader($headers) {
		/**
		 * 
		  $headers[]= 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727) ';
		  $headers[]= 'Host: weibo.cn';
		  $headers[]= 'Connection: Keep-Alive ';
		  $headers[]= "Content-length: ". strlen($data);
		  $headers[]= 'SOAPAction: "/soap/action/query"'; 
		  $headers[]= "Content-Type: text/xml";
		  $headers[]= 'Range: Bytes=0-177'; //断点传送 0-177 bytes
		 */
		$this->_option[CURLOPT_HTTPHEADER] = $headers;
		return $this;
	}

	protected function _setCookie($opts) {
		$opts or $opts = array(
			CURLOPT_COOKIE => 'a=1&b=2', //or Via Headers[] = 'Cookie: a=1;b=2'
			CURLOPT_COOKIEFILE => 'cookieFile.txt', //send
			CURLOPT_COOKIEJAR => 'cookieJar.txt', //store 
		);
		return $this->_setOpts($opts);
	}

	protected function _setSSL($user, $passwd) {
		$opts = array(
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_USERPWD => "$user:$passwd",
		);
		return $this->_setOpts($opts);
	}
	protected function _setFile($file){
		$this->_option[CURLOPT_FILE] = $file;//设置输出文件的位置，值是一个资源类型(比如 fopen('a.dat', 'w'))，默认为STDOUT (浏览器)。
	}

	protected function _setProxy($opts) {
		$opts or $opts = array(
			CURLOPT_HTTPPROXYTUNNEL => 1,
			CURLOPT_PROXY => 'ahui.com:1080',
			CURLOPT_PROXYUSERPWD => 'user:passwd',
		);
		return $this->_setOpts($opts);
	}

	protected function _setUrl($url){
		$this->_option[CURLOPT_URL] = $url;
	}

	/**
	 * @param type $url
	 * @param type $method
	 * @param type $params
	 * @return type
	 */
	function request($url, $method = 'get', $params = array(), $headers = array(), $opts = array()) {
		$this->init();
		$this->_setUrl($url);
		$this->_setHeader($headers);
		$this->_setOpts($opts);
		$method = strtolower($method);
		$this->_setMethod($method);
		if ( in_array($method ,[ 'post' ,  'put'])) {
			$this->_option[CURLOPT_POSTFIELDS] = $params;
		} else {
			$this->_setParamsGet($params);
		}
		curl_setopt_array($this->_ch, $this->_option);
		
		$rtn = curl_exec($this->_ch);
		$this->_error = curl_error($this->_ch);
		$this->_errno = curl_errno($this->_ch);
		$this->_codeInfo = curl_getinfo($this->_ch);
		$this->debug && $this->setCurlExecInfo();
		
		curl_close($this->_ch);//否则以前init 的option 会被保留
		$this->_option = array();
		return $rtn;
	}
	/**
	 *
	 */
	protected function _setMethod($method = 'get') {
		$this->_option[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
		$this->_option[CURLOPT_POST] = $method === 'post';
	}
	
	private function setCurlExecInfo() {
		$this->curlInfo['error'] = curl_error($this->_ch);
		$this->curlInfo['errno'] = curl_errno($this->_ch);
		$this->curlInfo['cmd'] = " curl ";
		if ( !empty($this->_option[CURLOPT_HTTPHEADER]) ) {
			foreach ( $this->_option[CURLOPT_HTTPHEADER] as $value )
			{
				$this->curlInfo['cmd'] .= "-H '{$value}' ";
			}
		}
		if ( !empty($this->_option[CURLOPT_POSTFIELDS]) ) {
			$params = $this->_option[CURLOPT_POSTFIELDS];
			$param = '';
			if(is_array($params)){
				array_walk($params, function($v, $k) use(&$param){
					$param .= "$k = ". urlencode($v);	
				});
			}else{
				$param = $params;
			}
			$param = substr($param, 0, 500);
			$this->curlInfo['cmd'].=" -d '{$param}' ";
		}
		$this->curlInfo['cmd'] .= " '{$this->_codeInfo['url']}' ";
		$this->curlInfo['http_code'] = $this->_codeInfo['http_code'];
		$this->curlInfo['total_time'] = $this->_codeInfo['total_time'];
		$this->curlInfo['connect_time'] = $this->_codeInfo['connect_time'];
		$this->curlInfo['pretransfer_time'] = $this->_codeInfo['pretransfer_time'];
		$this->curlInfo['size_download'] = $this->_codeInfo['size_download'];
		$this->curlInfo['speed_download'] = $this->_codeInfo['speed_download'];
	}
	public function getCurlInfo(){
		return $this->curlInfo;
	}
	public function getCurlCmd(){
		return $this->curlInfo['cmd'];
	}
	
	protected function _setParamsGet($data) {
		$_serverUrl = &$this->_option[CURLOPT_URL];
		$params = is_array($data) ? http_build_query($data) : $data;
		$_serverUrl .= strpos($_serverUrl, '?') ? '&' : '?';
		$_serverUrl .= $params;
	}

	protected function _setCookieFile($file) {
		$this->_option[CURLOPT_COOKIEJAR] = $file;//save cookie file
		$this->_option[CURLOPT_COOKIEFILE] = $file;//send cookie file
		return $this;
	}
	protected function _enableHttps(){
		$this->_option[CURLOPT_SSL_VERIFYPEER] = false;
		return $this;
	}

	/**
	 * 
	 * @return curl instance
	 */
	static function instance() {
		if(self::$instance === null){
			self::$instance = new self();
		}
		return self::$instance;
	}

}
