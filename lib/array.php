<?php
/**
 * 不会得到7/3 : 3 3 1
 * 而会得到7/3 : 3 2 2
 **/
function partition(Array $list, $p) {
    $listlen = count($list);
    $partlen = floor($listlen / $p);
    echo "$partlen\n";
    $partrem = $listlen % $p;
    echo "$partrem\n";
    $partition = array();
    $mark = 0;
    for($px = 0; $px < $p; $px ++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice($list, $mark, $incr);
        $mark += $incr;
    }
    return $partition;
}
