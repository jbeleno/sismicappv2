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
 * Function name: saveLogArray
 *
 * Description: This function takes a matrix of data, iterate over each vector,
 *              split the information in CSV format and store each piece of data
 *              as a new line of the specified file.
 *
 * Parameters:
 * - $data: it's an array of data
 * - $fileName: it's the file name where we want to store the information
 * - $sep (optional): it's an option to select which character we want to 
 *                    separate our data
 *
 * Return: NULL
 **/
if(!function_exists('saveLogArray')){
    function saveLogArray($data, $fileName, $sep = ","){
        $handle = fopen($file, "a");
        foreach ($datos as $impresion) {
            fputcsv($handle, $impresion, $sep);
        }
        fclose($handle);
    }
}


/**
 * Function name: saveLogLine
 *
 * Description: This function takes a vector of data, split it in CSV format
 *              and store it in a new line of the specified file.
 *
 * Parameters:
 * - $data: it's an array of data
 * - $fileName: it's the file name where we want to store the information
 * - $sep (optional): it's an option to select which character we want to 
 *                    separate our data
 *
 * Return: NULL
 **/
if(!function_exists('saveLogLine')){
    function saveLogLine($data, $fileName, $sep = ","){
        $handle = fopen($file, "a");
        fputcsv($handle, $data, $sep);
        fclose($handle);
    }
}