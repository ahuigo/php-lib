<?php
/**
 * 得到唯一识别码
 * 修改自Twitter的Snowflake算法
 * 此算法支持分布式自增ID
 * @author cwsky@163.com
 */

abstract class Uniq
{
	//初始时间码,开站设定一次,可用以下语句得到
	//floor(microtime(true) * 1000);
	private static $twepoch = 1380553425155;

	//数据中心,10表示可以有1023个数据中心
	private static $dataCenterIDBits = 10;

	//数据中心ID最大值
	//private static $maxDatacenterID = -1 ^ (-1 << $datacenterIDBits);

	//机器标识符,15表示可以有32767台机器
	private static $workerIDBits = 15;

	//机器ID最大值
	//private static $maxWorkerId = -1 ^ (-1 << $workerIDBits);

	//毫秒内自增位
	private static $sequenceBits = 12;

	//机器ID偏左移12位
	//private static $workerIDShift = self::$sequenceBits;
	private static $workerIDShift = 12;

	//数据中心ID左移17位
	//private $dataCenterIDShift = $sequenceBits + $workerIDBits;

	//时间毫秒左移22位
	//private $timestampLeftShift = $sequenceBits + $workerIDBits + $datacenterIDBits;
	//private $sequenceMask = -1 ^ (-1 << $sequenceBits);

	private static $lastTimestamp = -1;

	private static $sequence = 0;

	public static function id($dataCenterID, $workerID)
	{
		//数据中心ID最大值
		$maxDataCenterID = -1 ^ (-1 << self::$dataCenterIDBits);

		//机器ID最大值
		$maxWorkerID = -1 ^ (-1 << self::$workerIDBits);

		//数据中心ID左移17位
		$dataCenterIDShift = self::$sequenceBits + self::$workerIDBits;

		//时间毫秒左移22位
		$timestampLeftShift = self::$sequenceBits + self::$workerIDBits + self::$dataCenterIDBits;
		$sequenceMask = -1 ^ (-1 << self::$sequenceBits);

		//检查workerID完整性
		if($workerID > $maxWorkerID || $workerID < 0)
		{
			return 0;
		}

		//检查数据中心完整性
		if($dataCenterID > $maxDataCenterID || $dataCenterID < 0)
		{
			return 0;
		}

		//echo sprintf("worker starting. timestamp left shift %d, datacenter id bits %d, maxDatacenterID:%d, worker id bits %d, maxWorkerId:%d, sequence bits %d, workerid %d", $timestampLeftShift, $datacenterIDBits, $maxDatacenterID, $workerIDBits, $maxWorkerId, $sequenceBits, $workerID);

		$timestamp = self::timeGen();

		if($timestamp < self::$lastTimestamp)
		{
			return 0;
		}

		if (self::$lastTimestamp == $timestamp)
		{
			//当前毫秒内，则+1
			self::$sequence = (self::$sequence + 1) & $sequenceMask;
			if(self::$sequence == 0)
			{
				//当前毫秒内计数满了，则等待下一秒
				$timestamp = self::tilNextMillis(self::$lastTimestamp);
			}
		}
		else
		{
			self::$sequence = 0;
		}

		self::$lastTimestamp = $timestamp;

		$id = (($timestamp - self::$twepoch << $timestampLeftShift)) | ($dataCenterID << $dataCenterIDShift) | ($workerID << self::$workerIDShift) | (self::$sequence);

		return $id;
	}

	public static function timeGen()
	{
		return floor(microtime(true) * 1000);
	}

	public static function tilNextMillis($lastTimestamp)
	{
		$timestamp = self::timeGen();

		while($timestamp <= $lastTimestamp)
		{
			$timestamp = self::timeGen();
		}

		return $timestamp;
	}
}
?>