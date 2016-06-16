<?php
/**
 * This file handles with seismic functions that has access to the database
 * data and allows the devices performs read, update and write operations on the
 * 'seism' table
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */


class Seism_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Function name: all
     *
     * Description: Retrieve information from the last 20 seisms in Colombia, 
     * 				it saves a log about the device that is consulting the data
     *				and shows a banner inviting the device to rate the app.
     *
     * Parameters:
     * - $device_token <String> (Optional): it's the user token assigned for
     *                                      sismicapp as identifier
     * - $source <String>: it's the medium that use the device to access the data,  
     *			           by default it's "App"
     *
     * Return: an array of data that could belong to seism information or banner
     **/
    public function all($device_token = NULL, $source = "App"){
        $this->load->helper('general');
        $this->load->helper('log');

    	$logs = array();
    	$data = array();

    	$this->db->select('seism_id, seism_magnitude, seism_magnitude_richter, seism_epicenter, seism_date');
    	$this->db->order_by('seism_id', 'desc');

    	$seisms_query = $this->db->get('seism', 20, 0);

        // Getting the device identifier
        $this->db->select('device_id');
        $this->db->where('device_token', $device_token);
        $device_query = $this->db->get('device', 1, 0);
        $idDevice = NULL;

        if($devices_query->num_rows() == 1){
            $idDevice = $device_query->row()->device_id;
        }

    	foreach ($seisms_query->result() as $seism) {
    		$dateAgo = convertDateToXTimeAgo($seism->seism_date);

    		$seism->seism_date = $dateAgo;
    		$seism->seism_type = 'seism';

    		$data[] = $seism;

    		$logs[] = array(
    			($idDevice != "")? $idDevice : "NA",
    			$seism->seism_id,
    			$this->input->ip_address(),
    			$source,
    			date('Y-m-d H:i:s')
    		);
    	}

        // TO DO: Logistic of saving in database logs
    	saveLogArray($logs, 'temp/impressions.txt', $sep = ",");

    	return  array(
        			'status' => 'OK',
        			'posts' => $data
        		);
    }

    /**
     * Function name: detail
     *
     * Description: Retrieve information from a seism using an identifier as
     *              parameter.
     *
     * Parameters:
     * - $device_token <String> (Optional): it's the user token assigned for
     *                                      sismicapp as identifier
     * - $idSeism <Integer>: it's the seism identificator in the database
     *
     * Return: an array of the seism selected or an error 
     **/
    public function detail($device_token = NULL, $idSeism = NULL){
        $this->db->select('seism_lat, seism_lng, seism_epicenter, seism_date, seism_depth, seism_magnitude, seism_magnitude_richter');
        $this->db->where('seism_id', $idSeism);

        $seism_query = $this->db->get('seism', 1, 0);

        if($seism_query->num_rows() == 1){

            // Getting the device identifier
            $this->db->select('device_id');
            $this->db->where('device_token', $device_token);
            $device_query = $this->db->get('device', 1, 0);
            $idDevice = NULL;

            if($devices_query->num_rows() == 1){
                $idDevice = $device_query->row()->device_id;
            }
            
            // TO DO: Logistic of saving in database logs

            return  array(
                        'status' => 'OK',
                        'seism' => $seism_query->row()
                    );
        }else{
            return  array(
                        'status' => 'BAD',
                        'msg' => '¡Ups! al parecer no tenemos datos del sismo que estás buscando'
                    );
            );
        }
    }


    /**
     * Function name: scrapeInternational
     *
     * Description: Scrape RSNC website to index new international seism 
     *              information in our database, this is based just in the seism
     *				activity that affect directly Colombia
     *
     * Parameters: IT DOESN'T HAVE PARAMETERS
     *
     * Return: NOTHING
     **/
    public function scrapeInternational(){
    	require(APPPATH.'third_party/simple_html_dom.php');

    	$this->db->select('setting_value');
		$this->db->where('setting_name', 'intenational_seism_id');
		$seism = $this->db->get('setting', 1, 0);
		$id_intenational_seism = $seism->row()->setting_value + 1;

		$html = file_get_html(URL_RSNC_INTERNATIONAL);
		$control = 0;

		if ($html && !empty($html)) {
			$text = "";
			$title = $html->find('h2[class=contentheading]',0);
			$content_type1 = $html->find('div[class=article-content]',0);
			$content_type2 = $html->find('dt',0);

			// There 2 types of content written in natural language so it's
			// necessary to identify which is for later use in getting specific
			// data about the international seism
			if($content_type2 != NULL){
				$text = $contenidos2;
			}else if($content_type1 != NULL){
				if($content_type1->find('p',0) != NULL){
					$text = $content_type1->find('p',0);
				}
			}

			if($text != "" && $title != null){
				$IS_COLOMBIA_AFFECTED = strpos($title, "(Sentido en Colombia)");

				if($IS_COLOMBIA_AFFECTED !== FALSE){
					// Cleaning the text from HTML tags
					$textClean = strip_tags($text);

					// Getting the seism date
					$detection_date = date("Y-m-d H:i:s");
					$date_needle = "20";
					$UTC_needle = "UTC";
					$date_position = strpos($textClean, $date_needle);
					$UTC_position = strpos($textClean, $UTC_needle);
					$date_size = $UTC_position + strlen($UTC_needle) - $date_position;
					$date = strtotime(substr($textClean, $date_position, $date_size));

					// Getting the seism magnitude
					$M_needle = "magnitud M ";
					$Mw_needle = "magnitud Mw ";
					$M_position = strpos($textClean, $M_needle);
					$Mw_position = strpos($textClean, $Mw_needle);
					$magnitude = 0;

					if($Mw_position !== FALSE){
						$magnitude = substr($textClean, $Mw_postion + strlen($Mw_needle),4);
					}else if ($M_position !== FALSE) {
						$magnitude = substr($textClean, $M_position + strlen($M_needle),4);
					}

					// Getting the seism depth
					$depth_needle = "profundidad de ";
					$Km_needle = " Km";
					$km_needle = " km";
					$depth_position = strpos($textClean, $depth_needle);
					$Km_position = strpos($textClean, $Km_needle);
					$km_position = strpos($textClean, $km_needle);
					$depth = 0;

					if($depth_position !== FALSE){
						$kilometer_position = 0;

						if ($Km_position !== FALSE)
							$kilometer_position = $Km_position;
						else if ($km_position !== FALSE)
							$kilometer_position = $km_position;

						$str_length = $kilometer_position - ($depth_position + strlen($depth_needle));
						$depth = substr($textClean, $depth_position + strlen($depth_needle), $str_length);
						$depth = trim($depth);
					}


					// Getting the location
					$location_needle = "Según";
					$location_position = strrpos($textClean, $location_needle);
					$temporal_text = ($location_position !== false)? substr($textClean, 0, $location_position) : $textClean;
					$temporal_text = trim($temporal_text);
					$needle_space = " ";
					$space_position = strrpos($temporal_text, $needle_space);

					$str_country = substr($temporal_text, $space_position);
					$str_country = trim($str_country);
					$str_country = substr($str_country, 0, strlen($str_country) - 1);
					// The last line is to remove the character comma or period (IDK)


					// Google maps to get location coordinates
					// But is better adjust the exact location by hand
					$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$str_country.'&sensor=false');
			        $output= json_decode($geocode);
			        $latitude = $output->results[0]->geometry->location->lat;
			        $longitude = $output->results[0]->geometry->location->lng;


			        // Arraging the seism data
			        $data = array(
						'seism_date' => date("Y-m-d H:i:s", $date),
						'seism_magnitude' => $magnitude,
						'seism_magnitude_richter' => $magnitude,
						'seism_depth' => $depht,
						'seism_epicenter' => strtoupper($str_country),
						'seism_lat' => $latitude,
						'seism_lng' => $longitude,
						'seism_country' => 'Colombia',
						'seism_notificated' => 0,
						'seism_detection_date' => $detection_date
					);

			        // Execute the code below as a transaction
			        $this->db->trans_start();

				        // Insert the seism dat in the database
				        $this->db->insert('seism', $data);

				        // Updating the seism identifier as global variable
				        $this->db->set('setting_value', $id_intenational_seism);
						$this->db->where('setting_name', 'id_international_seism');
						$this->db->limit(1);
						$this->db->update('setting');

					$this->db->trans_complete();
				}
			}
		}
    }


    /**
     * Function name: scrapeLocal
     *
     * Description: Scrape RSNC website to index new seism information in our
     *              database, this is based just in the seism ocurred in Colombia.
     *
     * Parameters: IT DOESN'T HAVE PARAMETERS
     *
     * Return: NOTHING
     **/
    public function scrapeLocal(){
        require(APPPATH.'third_party/simple_html_dom.php');

        $html = file_get_html(URL_RSNC_LOCAL);
        $control = 0;
        $data = array();
        $detectionDate = date("Y-m-d H:i:s");

        if (!empty($html)) {
            $seism = array(
                'seism_notificated' => 0,
                'seism_detection_date' => $detectionDate
            );

            foreach ($html->find('td') as $seismHTML) {
                $control++;

                // The first 10 td are headers of the data
                if($control > 10){
                    // Convert the date to the format I'm handling in the db 
                    if(($control % 10) == 3) {
                        $utc = strtotime($seismHTML->innertext.' UTC');
                        $str_date = date("Y-m-d H:i:s", $utc);
                        
                        $seism['seism_date'] = $str_date;
                    }

                    if(($control % 10) == 4) 
                        $seism['seism_lat'] = $seismHTML->innertext;

                    if(($control % 10) == 5) 
                        $seism['seism_lng'] = $seismHTML->innertext;

                    if(($control % 10) == 6) 
                        $seism['seism_depth'] = $seismHTML->innertext;

                    if(($control % 10) == 7) 
                        $seism['seism_magnitude_richter'] = $seismHTML->innertext;

                    if(($control % 10) == 8) 
                        $seism['seism_magnitude'] = $seismHTML->innertext;

                    if(($control % 10) == 9) 
                        $seism['seism_epicenter'] = $seismHTML->innertext;

                    if(($control % 10) == 0){

                        // Trying to find whether or not the seism exist in the db
                        $this->db->where('seism_date', $seism['seism_date']);
                        $n_seisms = $this->db->count_all_results('seism'); 

                        // If not exist stack it to data, else break the cycle
                        if($n_seisms == 0)
                            $data[] = $seism;
                        else
                            break;

                        // Re-start the seism information
                        $seism = array(
                            'seism_notificated' => 'NO',
                            'seism_country' => 'Colombia',
                            'seism_detection_date' => $detectionDate
                        );
                    }
                }
            }

            // Select the optimal way to insert data based on the size of the data
            if(count($data) == 1)
                $this->db->insert('seism', $data[0]);
            else if (count($data) > 1)
                $this->db->insert_batch('seism', $data);
        }
    }


    /**
     * Function name: spreadTheVoice
     *
     * Description: Scan all the seisms that had not been sent and send a push 
     *              notifications to some devices according to their settings
     *              and publish a tweet with the new seism data.
     *
     * Parameters: IT DOESN'T HAVE PARAMETERS
     *
     * Return: NOTHING
     **/
    public function spreadTheVoice(){
        $this->load->helper('social');
        $this->load->helper('notifications');

        $this->db->select('seism_id, seism_date, seism_epicenter, seism_depth, seism_magnitude_richter, seism_magnitude, seism_lat, seism_lng');
        $this->db->where('seism_notificated', 0);

        $seism_query = $this->db->get('seism', 5, 0);

        if($seism_query->num_rows() > 0){
            foreach ($seism_query->result() as $seism) {
                $date = new DateTime($seism->seism_date);

                if($seism->seism_magnitude_richter > 3 && $seism->seism_magnitude > 0)
                    $magnitude = $seism->seism_magnitude.' Mw';
                else
                    $magnitude = $seism->seism_magnitude_richter.' Ml';

                $tw_msg = 'Sismo de '.$magnitude.' tuvo lugar hoy a las '.
                          $date->format('h:i a').' con epicentro cercano a '.
                          $seism->seism_epicenter.' y profundidad de '.
                          $seism->seism_depth.' Km.';

                writeTweet($tw_msg);

                $devices_query = $this->db->query(
                    'SELECT'.
                    'device_platform, device_push_id, device_range, ('.
                        '6371 * acos ('.
                              'cos ( radians('.$seism->seism_lat.'))'.
                              '* cos( radians( device_lat ) )'.
                              '* cos( radians( device_lng ) - radians('.$seism->seism_lng.'))'.
                              '+ sin ( radians('.$seism->seism_lat.'))'.
                              '* sin( radians( device_lat ) )'.
                            ')'.
                        ') AS distance'.
                    'FROM device'.
                    'WHERE device_notifications = 1'.
                    'AND (magnitud >= '.$seism->seism_magnitude.
                         'OR '.
                         'magnitud >= '.$seism->seism_magnitude_richter.')'.
                    'HAVING distance > device_range'.
                    'ORDER BY distance'
                );

                if($devices_query->num_rows() > 0){
                    $message = array(
                        'title' => 'Sismo Detectado',
                        'message' => 'Magnitud('.$magnitude.') '.$seism->seism_epicenter
                    );

                    $counter = 0;
                    $tokens = array();

                    foreach ($devices_query->result() as $device) {
                        $tokens[] = $device->device_push_id;
                        $counter++;

                        if($counter == 1000){
                            sendAndroidNotification($message, $tokens);
                            $tokens = array();
                            $counter = 0;
                        }
                    }

                    // TO DO: Receive the notification result and verify if the
                    // 		  notification was sent to update the device status 
                    sendAndroidNotification($message, $tokens);
                    $tokens = array();
                    $counter = 0;
                }

                $this->db->where('seism_id', $seism->seism_id);
                $this->db->limit(1);
                $this->db->update('seism', array('seism_notificated' => 1));
            }
        }
    }
}