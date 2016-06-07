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
     * Function name: new
     *
     * Description: Insert a new report.
     *
     * Parameters:
     * - $seism_id <Integer>: it's the seism identifier
     * - $device_token <Integer>: it's the device identifier
     * - $latitude <Float>: it's the latitude coordinate of the device
     * - $longitude <Float>: it's the longitude coordinate of the device
     * - $intensity <Integer>: it's the seism intensity 
     *						   (low: 0, medium: 1, strong: 2, very strong: 3)
     * - $place <Integer>: it's the user location 
     *					   (Inside a building/house: 0, In the street: 1, Outdoor: 2)
     * - $activity <Integer>: it's the user activity 
     *						  (Not movement: 0, movement: 1)
     *
     * Return: an array with the request status
     **/
    public function new($seism_id, $device_token, $latitude, 
    					$longitude, $intensity, $place, $activity){
    	$date = date("Y-m-d H:i:s");

    	$this->db->select('device_id');
    	$this->db->where('device_token', $device_token);
    	$device_query = $this->db->get('device', 1, 0);
    	$device_id = NULL;

    	if($device_query.num_rows() == 1){
    		$device_id = $device_query->row()->device_id;
    	}

    	$data = array(
			'report_id_push' => $seism_id,
			'report_id_device' => $device_id,
			'report_lat'=> $latitude,
			'report_lng' => $longitude,
			'report_intensity' => $intensity,
			'report_place' => $place,
			'report_activity' => $activity,
            'report_ip' => $this->input->ip_address(),
			'report_date' => $date
		);

		$this->db->insert('report', $data);

		return array( "status"=>"OK" );
    }
}

/* End of file Report_model.php */
/* Location: ./application/models/Report_model.php */