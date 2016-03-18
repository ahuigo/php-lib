<?php
require_once APPPATH. '/src/lib/Thrift/ClassLoader/ThriftClassLoader.php';
use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__)) . '/genphp';
require_once $GEN_DIR . '/NotifyCenter.php';
require_once $GEN_DIR . '/Types.php';
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

$loader = new ThriftClassLoader ();
$loader->registerNamespace('Thrift', APPPATH. '/src/lib');
$loader->register();
