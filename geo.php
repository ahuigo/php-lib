<?php
class Geo{
	const  EARTH_RADIUS = 6378.137; //km
	static function rad($d)   {
		return $d * M_PI / 180.0;
	}

	/**
	 * 根据两点间经纬度坐标（double值），计算两点间距离，单位为km
	 */
	public static function GetDistance($lat1, $lng1, $lat2, $lng2) {
		$radLat1 = self::rad($lat1);
		$radLat2 = self::rad($lat2);
		$a = $radLat1 - $radLat2;
		$b = self::rad($lng1) - self::rad($lng2);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
		$s = $s * self::EARTH_RADIUS;
		$s = round($s * 10000) / 10000;
		return $s;
	}
}
echo Geo::GetDistance(0.52, 117.17, 21.23,115.12);
