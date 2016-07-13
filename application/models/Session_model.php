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
     * Function name: add
     *
     * Description: Update the device location, ip, date and create a new session
     *              record to track the device usage.
     *
     * Parameters:
     * - $device_token <String>: it's the user token assigned for sismicapp as identifier
     * - $latitude <Float>: it's the latitude coordinate of the device
     * - $longitude <Float>: it's the longitude coordinate of the device
     * - $city <String>: it's the city name where the device stands
     * - $region <String>: it's the region name where the device stands
     * - $country <String>: it's the country name where the device stands
     * - $app_version <String>: it's the app version that the user has
     *
     * Return: an array with the request status
     **/
    public function add($device_token, $latitude, $longitude, $city, $region, $country, $app_version){

        $date = date("Y-m-d H:i:s");

        $data_device = array();
        $data_session = array();

        $data_session['session_app_version'] = $data_device['device_app_version'] = $app_version;
        $data_session['session_city'] = $data_device['device_city'] = $city;
        $data_session['session_region'] = $data_device['device_region'] = $region;
        $data_session['session_country'] = $data_device['device_country'] = $country;
        $data_session['session_ip'] = $data_device['device_last_ip'] = $this->input->ip_address();
        $data_session['session_date'] = $data_device['device_last_date_login'] = $date;
        $data_device['device_status'] = 1;

        if ($latitude != NULL && $latitude != "" && $latitude != 0 &&
            $longitude != NULL && $longitude != "" && $longitude != 0){
            $data_session['session_lat'] = $data_device['device_lat'] = $latitude;
            $data_session['session_lng'] = $data_device['device_lng'] = $longitude;
        }

        $this->db->select('HEX(device_id) AS device_id');
        $this->db->where('device_token', $device_token);
        $device_query = $this->db->get('device', 1, 0);
        $device_id = NULL;
        if($device_query->num_rows() == 1){
            $device_id = $device_query->row()->device_id;
        }

        // Handling the UUID as identifier
        $this->db->set('session_id', "unhex(replace(uuid(),'-',''))", FALSE);
        $this->db->set('session_id_device', "UNHEX('".$device_id."')", FALSE);

        // New session information
        $this->db->insert('session', $data_session);

        // Update device data
        $this->db->where('device_token', $device_token);
        $this->db->limit(1);
        $this->db->update('device', $data_device);

        return array( "status"=>"OK" );
    }

}

/* End of file Session_model.php */
/* Location: ./application/models/Session_model.php */