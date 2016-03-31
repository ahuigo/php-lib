<?php
class Base{
    /**
     * @param $key
     * @param null $default
     * @return array|null
     */
    function get($key, $default = null) {
        if (is_array($key)) {
            $rtn = [];
            foreach ($key as $k) {
                $rtn[$k] = $this->params[$k];
            }
            return $rtn;
        } else {
            return isset($this->params[$key]) ? $this->params[$key] : $default;
        }
    }


    /**
     * 检查csrf漏洞
     * @param bool|true $checkGet
     */
    function checkCSRF($checkGet = true){
        $error = 'CSRF';
        $refer = $_SERVER['HTTP_REFERER'];
        if($refer && parse_url($refer, PHP_URL_HOST) === explode(':', $_SERVER['HTTP_HOST'])[0]){
            if($checkGet && $_SERVER['REQUEST_METHOD'] !== 'GET' ){
                $error = false;
            }else{
                $error = false;
            }
        }
        if($error){
            Response::error($error);
        }
    }


    /**
     * @return \Smarty
     */
    public function smarty() {
        static $smarty = null;
        if ($smarty === null) {
            require_once(APPPATH . "/smarty/libs/Smarty.class.php");
            $smarty = new \Smarty();
            $smarty->template_dir = SMARTY_ROOT . 'templates';
            $smarty->compile_dir = SMARTY_ROOT . 'templates_c';
            $smarty->config_dir = SMARTY_ROOT . 'config';
            $smarty->cache_dir = SMARTY_ROOT . 'cache';
            if(isset($_SERVER['DEBUG'])){
                $smarty->caching = false;
            }
        }
        return $smarty;
    }

    /**
     * @param $url
     * @param string $msg
     */
    public function validUrl($url, $msg = ''){
       if(!preg_match('#^http(s)?://#', $url) && !preg_match('#^//#', $url) ){
           Response::error($msg);
       }
    }

    /**
     * @param $key
     * @param null $value
     */
    public function assign($key, $value = null){
        if(is_string($key) ){
            $this->smarty()->assign($key, $value);
        }else{
            foreach($key as $k => $v){
                $this->smarty()->assign($k, $v);
            }
        }
    }

    /**
     * @param $arr
     * @param $error
     */
    function notEmpty($arr, $error) {
        if ($arr != array_filter($arr)) {
            Response::error($error);
        }
    }
}
