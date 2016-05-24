<?php
/**
 * This file handles with seismic functions that has access to the database
 * data and allows the user performs read, update and write operations on the
 * 'sismos' table
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
     * 				it saves a log about the user that is consulting the data
     *				and shows a banner inviting the user to rate the app.
     *
     * Parameters:
     * - $idUser (optional): it's the user identification, it's optional
     *						 because it still has some problems in register of
     *						 new users due to Phonegap plugins incompatibility
     * - $source: it's the medium that use the user to access the data, by 
     *			  default it's "App"
     *
     * Return: an array in  JSON format of data that could belong to seism 
     *		   information or banner
     **/
    public function all($idUser = NULL, $source = "App"){
    	$logs = array();
    	$data = array();

    	$this->db->select('id, magnitud, magnitud_richter, epicentro, fecha');
    	$this->db->order_by('id', 'desc');

    	$seisms_query = $this->db->get('sismos', 20, 0);

    	foreach ($seisms_query->result() as $seism) {
    		$dateAgo = convertDateToXTimeAgo($seism->fecha);

    		$seism->fecha = $dateAgo;
    		$seism->tipo = 'sismo';

    		$data[] = $seism;

    		$logs[] = array(
    			($idUser != "")? $idUser : "NA",
    			$seism->id,
    			$this->input->ip_address(),
    			$source,
    			date('Y-m-d H:i:s')
    		);
    	}

    	// This threshold is about the first user who registered the 
    	// app version that allows banners without exploding
    	$user_threshold_new_version = 745;
    	if($idUser > $user_threshold_new_version){
    		$data[] = array(
    			'tipo' => 'banner',
    			'banner' => $this->load->view('banners/ratingapp', NULL, TRUE)
    		);
    	}

    	saveLogArray($logs, 'temp/impressions.txt', $sep = ",");

    	print json_encode(
    		array(
    			'status' => 'OK',
    			'posts' => $data
    		);
    	);
    }

    /**
     * Function name: detail
     *
     * Description: Retrieve information from a seism using an identifier as
     *              parameter.
     *
     * Parameters:
     * - $idSeism: it's the seism identificator in the database
     *
     * Return: an array in JSON format of the seism selected or an error in
     *         in JSON format
     **/
    public function detail($idSeism = NULL){
        $this->db->select('latitud, longitud, epicentro, fecha, profundidad, magnitud, magnitud_richter');
        $this->db->where('id', $idSeism);

        $seism_query = $this->db->get('sismos', 1, 0);

        if($seism_query->num_rows() == 1){
            print json_encode(
                array(
                    'status' => 'OK',
                    'seism' => $seism_query->row()
                );
            );
        }else{
            print json_encode(
                array(
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

    	$this->db->select('valor');
		$this->db->where('nombre', 'id_sismo_internacional');
		$seism = $this->db->get('variables_globales', 1, 0);
		$id_intenational_seism = $seism->row()->valor + 1;

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
						'fecha' => date("Y-m-d H:i:s", $date),
						'magnitud' => $magnitude,
						'magnitud_richter' => $magnitude,
						'profundidad' => $depht,
						'epicentro' => strtoupper($str_country),
						'latitud' => $latitude,
						'longitud' => $longitude,
						'push' => 'NO',
						'fecha_deteccion' => $detection_date
					);

			        // Execute the code below as a transaction
			        $this->db->trans_start();

				        // Insert the seism dat in the database
				        $this->db->insert('sismos', $data);

				        // Updating the seism identifier as global variable
				        $this->db->set('valor', $id_intenational_seism);
						$this->db->where('nombre', 'id_sismo_internacional');
						$this->db->limit(1);
						$this->db->update('variables_globales');

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
                'push' => 'NO',
                'fecha_deteccion' => $detectionDate
            );

            foreach ($html->find('td') as $seismHTML) {
                $control++;

                // The first 10 td are headers of the data
                if($control > 10){
                    // Convert the date to the format I'm handling in the db 
                    if(($control % 10) == 3) {
                        $utc = strtotime($seismHTML->innertext.' UTC');
                        $str_date = date("Y-m-d H:i:s", $utc);
                        
                        $seism['fecha'] = $str_date;
                    }

                    if(($control % 10) == 4) 
                        $seism['latitud'] = $seismHTML->innertext;

                    if(($control % 10) == 5) 
                        $seism['longitud'] = $seismHTML->innertext;

                    if(($control % 10) == 6) 
                        $seism['profundidad'] = $seismHTML->innertext;

                    if(($control % 10) == 7) 
                        $seism['magnitud_richter'] = $seismHTML->innertext;

                    if(($control % 10) == 8) 
                        $seism['magnitud'] = $seismHTML->innertext;

                    if(($control % 10) == 9) 
                        $seism['epicentro'] = $seismHTML->innertext;

                    if(($control % 10) == 0){

                        // Trying to find whether or not the seism exist in the db
                        $this->db->where('fecha', $seism['fecha']);
                        $n_seisms = $this->db->count_all_results('sismos'); 

                        // If not exist stack it to data, else break the cycle
                        if($n_seisms == 0)
                            $data[] = $seism;
                        else
                            break;

                        // Re-start the seism information
                        $seism = array(
                            'push' => 'NO',
                            'fecha_deteccion' => $detectionDate
                        );
                    }
                }
            }

            // Select the optimal way to insert data based on the size of the data
            if(count($data) == 1)
                $this->db->insert('sismos', $data[0]);
            else if (count($data) > 1)
                $this->db->insert_batch('sismos', $data);
        }
    }
}