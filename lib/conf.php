<?php
/**
 * Created by PhpStorm.
 * User: hilojack
 * Date: 13/8/2016
 * Time: 11:02 PM
 */
class Lib_Conf {
    /**
     * 按需加载配置, 用于局部加载
     * @param $name
     * @return mixed
     */
    static function load($name){
        static $cache = array();
        if(!isset($cache[$name])){
            $cache[$name] = require(ROOT . '/conf/' . $name . ".php");
        }
        return $cache[$name];
    }

}

