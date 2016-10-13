<?php
class Response {

    static $finish = false;
    /**
     * 中断浏览器的响应
     */
    static function finish(){
        self::$finish = true;
        fastcgi_finish_request();
    }

    static function json($rtn, $error = '', $errno = 0, $die = true) {
        if(self::$finish){
            die;
        }
        header('Content-Type: application/json');
        $rtn = (array)$rtn + array('errno'=>$errno, 'errmsg'=>$error);
        if(version_compare(PHP_VERSION, '5.4')>0){
            $json = json_encode($rtn, JSON_UNESCAPED_UNICODE);
        }else{
            $json = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
                return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
            }, json_encode($rtn));
        }
        if($json === false){
            $json = json_encode($rtn, JSON_PARTIAL_OUTPUT_ON_ERROR);
        }
        echo $json;
        $die && die;
    }

    static function error($error = '', $errno = -1) {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_ACCEPT'] === 'application/json'){
            self::json(array(), $error, $errno);
        }else{
            $html = "$error".'<br>';
            echo $html;
        }
        die;
    }


    /**
     * 判断是否为ajax 请求
     * @return bool
     */
    static function isAjax(){
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_ACCEPT'] === 'application/json');
    }
    /**
     * @param $url
     */
    static function redirect($url){
        header('Location: '. $url);
        die;
    }
}
