<?php
/**
 * This file handles with impressions and details in seismic logs in files 
 * for this application for later inserts in MySQL database
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */


/**
 * Function name: calculateDistance
 *
 * Description: This function takes two geospatial points (latitude, longitude)
 *				and calculate their distance, this code was taken from 
 *				http://stackoverflow.com/a/10054282 using the Vincenty formula.
 *
 * Parameters:
 * - $latitudeFrom: it's the latitude parameter of the starting point
 * - $longitudeFrom: it's the longitude parameter of the starting point
 * - $latitudeTo: it's the latitude parameter of the final point
 * - $longitudeTo: it's the longitude parameter of the final point
 * - $earthRadius (optional): it's the earth "radius"
 *
 * Return: The distance in kilometers between the two given points
 **/
if(!function_exists('calculateDistance')){
	function calculateDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$lonDelta = $lonTo - $lonFrom;
		$a = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

		$angle = atan2(sqrt($a), $b);
		return $angle * $earthRadius/1000;
	}
}


/**
 * Function name: convertDateToXTimeAgo
 *
 * Description: This function takes a date in String format, convert it in date
 *				format and calculates the String representing the time X (unit time)
 *				ago, but this String is in spanish.
 *
 * Parameters:
 * - $str_date: a date in String format
 *
 * Return: The date in a format of X (unit time) ago, where "unit time" could
 *		   take values of seconds, minutes, months and years
 **/
if(!function_exists('convertDateToXTimeAgo'))
{
	function convertDateToXTimeAgo($str_date){

		$dateAgo ="Hace unos segundos";

		$date = strtotime($str_fecha);

		$seconds = strtotime('now') - $date;
		$minutes = floor($seconds/60);
		$hours = floor($seconds/60/60);
		$days = floor($seconds/60/60/24);
		$months = floor($seconds/60/60/24/30);
		$years = floor($seconds/60/60/24/30/12);

		if($minutes == 1){
			$dateAgo = 'Hace 1 minuto';
		}else if($minutes < 60 && $minutes > 1){
			$dateAgo = 'Hace '.$minutes.' minutos';
		}

		if($hours == 1){
			$dateAgo = 'Hace 1 hora';
		}else if($hours < 24 && $hours > 1){
			$dateAgo = 'Hace '.$hours.' horas';
		}

		if($days == 1){
			$dateAgo = 'Hace 1 día';
		}else if($days < 30 && $days > 1){
			$dateAgo = 'Hace '.$days.' días';
		}

		if($months == 1 && $months >0){
			$dateAgo = 'Hace 1 mes';
		}else if($months < 12 && $months >1){
			$dateAgo = 'Hace '.$months.' meses';
		}

		if($years == 1 && $years >0){
			$dateAgo = 'Hace 1 año';
		}else if($years >1){
			$dateAgo = 'Hace '.$years.' años';
		}
		return $dateAgo;
	}
}