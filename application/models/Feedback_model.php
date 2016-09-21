<?php
/**
 * This file handles with feedback model, this allows the user to send 
 * feedback to sismicapp about problems with the app and things that 
 * could be better
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Feedback_model extends CI_Model {

	public function __construct(){
        parent::__construct();
    }

    /**
     * Function name: add
     *
     * Description: Insert new feedback information.
     *
     * Parameters:
     * - $device_token <Integer>: it's the device identifier
     * - $latitude <Float>: it's the latitude coordinate of the device
     * - $longitude <Float>: it's the longitude coordinate of the device
     * - $msg <String>: it's a message content about the feedback
     * - $city <String>: it's the city name where the device stands
     * - $region <String>: it's the region name where the device stands
     * - $country <String>: it's the country name where the device stands
     *
     * Return: an array with the request status
     **/
    public function add($device_token, $latitude, $longitude, $msg, 
    					$city, $region, $country){
    	$date = date("Y-m-d H:i:s");

    	$this->db->select('HEX(device_id) AS device_id');
    	$this->db->where('device_token', $device_token);
    	$device_query = $this->db->get('device', 1, 0);
    	$device_id = NULL;

    	if($device_query->num_rows() == 1){
    		$device_id = $device_query->row()->device_id;
    	}

    	$data = array(
			'feedback_lat'=> $latitude,
			'feedback_lng' => $longitude,
			'feedback_msg' => $msg,
			'feedback_ip' => $this->input->ip_address(),
			'feedback_city' => $city,
            'feedback_region' => $region,
            'feedback_country' => $country,
			'feedback_date' => $date
		);

        // Handling the UUID as identifier
        $this->db->set('feedback_id', "unhex(replace(uuid(),'-',''))", FALSE);
        $this->db->set('feedback_id_device', "UNHEX('".$device_id."')", FALSE);

		$this->db->insert('feedback', $data);

		return array( "status"=>"OK" );
    }

}

/* End of file Feedback_model.php */
/* Location: ./application/models/Feedback_model.php */