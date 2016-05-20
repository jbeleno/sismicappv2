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