<?php
class Params{
    /**
     * @param $key
     * @param null $default
     * @param bool $checkEmpty
     * @return null
     */
    static function get($key, $default = null, $checkEmpty = false){
        static $request;
        $request === null && $request = $_GET+$_POST;
        if($checkEmpty){
            $v = !empty($request[$key]) ? $request[$key] : $default;
        }else{
            $v = isset($request[$key]) ? $request[$key] : $default;
        }
        return $v;
    }
    static function notEmpty($value, $desc){
       if(empty($value) && $desc){
           Response::error($desc, -1);
       }
    }
}
