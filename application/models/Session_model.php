<?php
/**
 * This file handles with device session model, mainly is used to track the user
 * behavior in sismicapp, including dates and locations
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Session_model extends CI_Model {

	public function __construct(){
        parent::__construct();
    }

	/**
     * Function name: new
     *
     * Description: Update the device location, ip, date and create a new session
     *              record to track the device usage.
     *
     * Parameters:
     * - $device_token <String>: it's the user token assigned for sismicapp as identifier
     * - $latitude <Float>: it's the latitude coordinate of the device
     * - $longitude <Float>: it's the longitude coordinate of the device
     *
     * Return: an array in JSON format with the request status
     **/
    public function new($device_token, $latitude, $longitude){

        $date = date("Y-m-d H:i:s");

        $data_device = array();
        $data_session = array();

        $data_session['session_ip'] = $data_device['device_last_ip'] = $this->input->ip_address();
        $data_session['session_date'] = $data_device['device_last_date_login'] = $date;
        $data_device['device_status'] = 1;

        if ($latitude != NULL && $latitude != "" && $latitude != 0 &&
            $longitude != NULL && $longitude != "" && $longitude != 0){
            $data_session['session_lat'] = $data_device['device_lat'] = $latitude;
            $data_session['session_lng'] = $data_device['device_lng'] = $longitude;
        }

        $this->db->select('device_id');
        $this->db->where('device_token', $device_token);
        $device_query = $this->db->get('device', 1, 0);
        if($device_query->num_rows() == 1){
            $device_id = $device_query->row()->device_id;
            $data_session['session_id_device'] = $device_id;
        }

        // New session information
        $this->db->insert('session', $data_session);

        // Update device data
        $this->db->where('device_token', $device_token);
        $this->db->limit(1);
        $this->db->update('device', $data_device);

        return json_encode( array( "status"=>"OK" ) );
    }

}

/* End of file Session_model.php */
/* Location: ./application/models/Session_model.php */