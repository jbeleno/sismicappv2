<?php
/**
 * This file handles with user reports model, it allows the user report
 * errors and questions about the app.
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */


class Report_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

	/**
     * Function name: add
     *
     * Description: Insert a new report.
     *
     * Parameters:
     * - $device_token <Integer>: it's the device identifier
     * - $latitude <Float>: it's the latitude coordinate of the device
     * - $longitude <Float>: it's the longitude coordinate of the device
     * - $city <String>: it's the city name where the device stands
     * - $region <String>: it's the region name where the device stands
     * - $country <String>: it's the country name where the device stands
     *
     * Return: an array with the request status
     **/
    public function add($device_token, $latitude, 
    					$longitude, $city, $region, $country){

    	$date = date("Y-m-d H:i:s");

        $this->db->select('HEX(device_id) AS device_id');
        $this->db->where('device_token', $device_token);
        $device_query = $this->db->get('device', 1, 0);
        $device_id = $device_token;
        if($device_query->num_rows() == 1){
            $device_id = $device_query->row()->device_id;
        }

    	$data = array(
			'report_lat'=> $latitude,
			'report_lng' => $longitude,
            'report_ip' => $this->input->ip_address(),
            'report_city' => $city,
            'report_region' => $region,
            'report_country' => $country,
			'report_date' => $date
		);

        // Handling the UUID as identifier
        $this->db->set('report_id', "unhex(replace(uuid(),'-',''))", FALSE);
        $this->db->set('report_id_device', "UNHEX('".$device_id."')", FALSE);

		$this->db->insert('report', $data);

		return array( "status"=>"OK" );
    }
}

/* End of file Report_model.php */
/* Location: ./application/models/Report_model.php */