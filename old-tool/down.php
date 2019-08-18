<?php
$u = $_GET['u'];
header('Content-Type: img/gif');
echo file_get_contents($u);
