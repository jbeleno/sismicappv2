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
 * - $latitudeFrom <Float>: it's the latitude parameter of the starting point
 * - $longitudeFrom <Float>: it's the longitude parameter of the starting point
 * - $latitudeTo <Float>: it's the latitude parameter of the final point
 * - $longitudeTo <Float>: it's the longitude parameter of the final point
 * - $earthRadius <Integer> (optional): it's the earth "radius"
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
 * Function name: classifySeismDepth
 *
 * Description: This function takes the seism depth and classify it as
 *				"poca profundidad", "profundidad media" or "mucha profundidad"
 *
 * Parameters:
 * -$depth <String>: The seism depth in kilometers
 *
 * Return: A classification according to the seism depth
 **/
if(!function_exists('classifySeismDepth')){
	function classifySeismDepth($depth)
	{
		$classification = "profundidad indeterminada";

		if($depth < 60){
			$classification = "poca profundidad";
		}else if($depth > 60 && $depth < 300){
			$classification = "profundidad media";
		}else{
			$classification = "mucha profundidad";
		}

		return $classification;
	}
}

/**
 * Function name: convertDateTimeToDate
 *
 * Description: This function takes a datetime value and convert it in
 *				a date
 *
 * Parameters:
 * -$str_date <String>: a date in String format
 *
 * Return: A date
 **/
if(!function_exists('convertDateTimeToDate')){
	function convertDateTimeToDate($str_date)
	{
		$date = DateTime::createFromFormat('Y-m-d H:i:s', $str_date)->getTimestamp();
		return date("d/m/Y",$date);
	}
}

/**
 * Function name: convertDateToText
 *
 * Description: This function takes a date in the format Y-m-d H:i:s and 
 *				convert it into a human readable string
 *
 * Parameters:
 * -$str_date <String>: a date in String format
 *
 * Return: A text with a human readable representation of the date
 **/
if(!function_exists('convertDateToText')){
	function convertDateToText($str_date)
	{
		$days = array(
			"domingo",
			"lunes",
			"martes",
			"miércoles",
			"jueves",
			"viernes",
			"sábado"
		);

		$months = array(
			'enero',
			'febrero',
			'marzo',
			'abril',
			'mayo',
			'junio',
			'julio',
			'agosto',
			'septiembre',
			'octubre',
			'noviembre',
			'diciembre'
		);

		$date = DateTime::createFromFormat('Y-m-d H:i:s', $str_date)->getTimestamp();

		$day_of_the_week = date("w",$date);
		$hour = date("H",$date);
		$day = date("d",$date);
		$month = intval(date("m",$date))-1;
		$year = date("Y",$date);

		$part_of_the_day = "";

		if($hour >= 0 && $hour < 12){
			$part_of_the_day = "mañana";
		}else if($hour >= 12 && $hour < 6){
			$part_of_the_day = "tarde";
		}else{
			$part_of_the_day = "noche";
		}

		return  $days[$day_of_the_week]." en la ".$part_of_the_day
				." (".$day." de ".$months[$month]." de ".$year.")";
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
 * - $str_date <String>: a date in String format
 *
 * Return: The date in a format of X (unit time) ago, where "unit time" could
 *		   take values of seconds, minutes, months and years
 **/
if(!function_exists('convertDateToXTimeAgo'))
{
	function convertDateToXTimeAgo($str_date){

		$dateAgo ="Hace unos segundos";

		$date = DateTime::createFromFormat('Y-m-d H:i:s', $str_date);

		$seconds = strtotime('now') - $date->getTimestamp();
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


/**
 * Function name: convertHourTo12
 *
 * Description: This function takes a date and shows the hours in a 
 *				12 hours format
 *
 * Parameters:
 * -$str_date <String>: a date in String format
 *
 * Return: An hour in 12 hours format
 **/
if(!function_exists('convertHourTo12')){
	function convertHourTo12($str_date)
	{
		$date = DateTime::createFromFormat('Y-m-d H:i:s', $str_date)->getTimestamp();
		return date("h:i a",$date);
	}
}


/**
 * Function name: selectMagnitude
 *
 * Description: This function takes the seism magnitude in richter scale and
 *				in the moment magnitude scale and selects one to show to the 
 *				user
 *
 * Parameters:
 * - $mag_richter <Float>: it's the seism magnitude in richter scale
 * - $mag_mms <Float>: it's the seism magnitude in the moment magnitude scale
 * -  $limit <Float>: it's the magnitude limit to select one or another scale
 *
 * Return: An string with the selected seism magnitude
 **/
if(!function_exists('selectMagnitude')){
	function selectMagnitude($mag_richter, $mag_mms, $limit = 4.0)
	{
		$units = 'Richter';
		$magnitude = $mag_richter;
		if($mag_richter >= 4.0 && $mag_mms >= 4.0){
			$magnitude = $mag_mms;
			$units = 'magnitud de momento';
		}

		return array($magnitude, $units);
	}
}