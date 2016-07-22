<?php
/**
 * Created by PhpStorm.
 * User: hilojack
 * Date: 13/11/15
 * Time: 1:02 AM
 */

class File {

    /**
     * @param $dir
     */
    static function mkdir($dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                Response::error('Failed to create dir:' . $dir);
            }
        }
    }

    static function set($k, $v){
        $file = DATADIR.'/kv/'. $k;
        self::mkdir(dirname($file));
        return file_put_contents($file, $v);
    }
    static function get($k){
        $file = DATADIR.'/kv/' . $k;
        if(file_exists($file)){
            return file_get_contents($file);
        }
    }
    static function countLine($file) {
        //return (int)`wc -l $file`;
        $f = fopen($file, 'rb');
        $lines = 0;
        while (fgets($f)) {
            $lines++;
        }
        fclose($f);
        return $lines;
    }

    /**
     * @param $file
     * @param $start_line_pos
     * @param int $num
     * @return array
     */
    static function getFileLines($file, $start_line_pos, $num = 1){
        static $fhs;
        if(!isset($fhs[$file])){
            $fhs[$file] = new \SplFileObject($file);
        }
        $fh = $fhs[$file];
        $fh->seek($start_line_pos);

        $lines = array();
        while(( $line = $fh->current()) && $num-- > 0){
            if(!empty($line)){
                $lines[] = $line;
            }
            $fh->next();
        }
        return $lines;
    }

    /**
     * @param $file
     * @param $line_pos
     * @return mixed
     */
    static function getFileLine($file, $line_pos){
        list($line) = self::getFileLines($file, $line_pos);
        return $line;
    }
    /**
     * @param $tid
     * @param $maxProcessNum 每个任务的最大进程数
     * @return bool
     */
    static function getTaskLock($name , $tid, $maxProcessNum = 1) {
        static $fp;
        if($fp === null){
            $pid = mt_rand(1, $maxProcessNum);
            $lockfile = rtrim(DATAPATH, '/') . "/lock/$name/$tid-$pid";
            self::mkdir(dirname($lockfile));
            $fp = fopen($lockfile, 'w');
        }
        $lock = flock($fp, LOCK_EX | LOCK_NB);
        return $lock;
    }

    /**
     * @param $str
     * @return mixed
     */
    static function dataAsFile($str){
        static $temps = array();
        $temp = tmpfile();
        $temps[] = $temp;
        fwrite($temp, $str, strlen($str));
        $fileInfo = stream_get_meta_data($temp);
        $file = $fileInfo['uri'];
        return $file;
    }

    /**
     * 将字符转成utf8
     * @param $str
     * @return string
     */
    static function utf8($str){
        if(json_encode($str) === false){
            $str = iconv('gbk', 'utf8', $str);
        }
        return $str;
    }


    /**
     * 追加文件内容
     * @param $file
     * @param $str
     */
    static function append($file, $str){
        static $files = array();
        if(!isset($files[$file])){
            self::mkdir(dirname($file));
            $files[$file] = fopen($file, 'a');
        }
        $fp = $files[$file];
        return fwrite($fp, $str);
    }

    /**
     * @param $file
     * @return bool
     */
    static function rm($file){
        if(is_file($file)){
           return unlink($file);
        }
        return true;
    }
    /**
     * @param $dir
     * @param string $pattern
     * @param bool $recursive
     * @return RecursiveDirectoryIterator|RecursiveIteratorIterator|RegexIterator
     */
    static function iteratorDir($dir, $pattern = '', $recursive = false){
        $Iterator = new RecursiveDirectoryIterator($dir);
        if($recursive){
            $Iterator = new RecursiveIteratorIterator($Iterator);
        }
        if($pattern){
            $Iterator = new RegexIterator($Iterator, $pattern, RegexIterator::GET_MATCH); // It matches against (string)$fileobj
        }
        return $Iterator;
    }

}
