<?php
namespace Hilo;
class Time{
    /**
     * 获取可读时间
     * @param $timeStamp
     * @return bool|string
     */
    static function getReadableTime($timeStamp) {
        if ((10 != strlen($timeStamp)) || !is_numeric($timeStamp)) {
            $timeStamp = strtotime($timeStamp);
        }
        $timeSpan = ceil((time() - $timeStamp) / 60);
        if ($timeSpan < 59 ) {
            return (($timeSpan <= 0) ? 1 : $timeSpan) . '分钟前';
        } elseif (date('Ymd') == date('Ymd', $timeStamp)) {
            return '今天 ' . date('H:i', $timeStamp);
        } elseif (date('Y') == date('Y', $timeStamp)) {
            return date('m-d H:i', $timeStamp);
        } else {
            return date("Y-m-d H:i", $timeStamp);
        }
    }
}
