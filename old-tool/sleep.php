<?php
$t = isset($_GET['t']) ? $_GET['t'] : 1;
echo date('r') . " ";
sleep($t);
echo date('r');
