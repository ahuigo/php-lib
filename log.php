<?php

class Log {
    static $logRootDir = '';
    static $logDir = '.';

    public static function setLogRootDir($rootDir){
        self::$logRootDir = $rootDir;
    }
    /**
     * @param string $dir
     * @example
     * @return static
     */
    public static function setLogDir($dir){
        if(empty(self::$logRootDir)){
            self::$logRootDir = defined('LOG_PATH')?  LOG_PATH: './log';
        }
        if($dir{0} === '/'){
            self::$logDir = $dir;
        }else{
            self::$logDir =rtrim(self::$logRootDir, '/') . "/" . trim($dir, '/');
        }
    }
    /**
     * @param $dir
     * @param $arguments
     * @return static
     */
    public static function __callStatic($dir, $arguments) {
        self::setLogDir($dir);
        call_user_func_array([__CLASS__,'writeLog' ], $arguments);
    }

    /**
     * @param $filename
     * @param $msg
     */
    private static function writeLog($filename, $msg, $dateSuffix = false){
        static $fhs = array();
        if($dateSuffix){
            $filename = "$filename-". date('Y-m-d').".log";
        }
        $file = self::$logDir . "/$filename";

        if(is_array($msg)){
            $str = '';
            foreach($msg as $k => $v){
                if(!is_string($v)){
                    $v = var_export($v, true);
                }
                $str .= "$k=$v||";
            }
            $msg = $str;
        }

        if(!isset($fhs[$file])){
            if(!self::mkdir(dirname($file))){
               return false;
            }
            $fhs[$file] = fopen($file, 'a');
        }
        $fh = $fhs[$file];
        fwrite($fh, "$msg\n");
        return true;
    }

    /**
     * @param $dir
     */
    static function mkdir($dir){
        $rtn = true;
        if(!is_dir($dir)){
            $rtn = mkdir($dir, 0777, true);
        }
        return $rtn;
    }
}
