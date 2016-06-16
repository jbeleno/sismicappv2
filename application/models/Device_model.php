<?php
/**
 * This file handles with device model, it's basically a CRUD for devices
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Device_model extends CI_Model {

	public function __construct(){
        parent::__construct();
    }
    

    /**
     * Function name: add
     *
     * Description: Insert new device data in the database and return the
     *              device token to keep it secret.
     *
     * Parameters:
     * - $push_id <String>: it's the token used by Android/iOS to send push notifications
     * - $latitude <Float>: it's the latitude coordinate of the device
     * - $longitude <Float>: it's the longitude coordinate of the device
     * - $model <String>: it's the device model
     * - $platform <String>: it's the device platform iOS or Android
     * - $version <String>: it's the SO version of the software
     *
     * Return: an array with the request status and the device
     *         identifier
     **/
    public function add($push_id, $latitude, $longitude, 
                        $model, $platform, $version){
        $date = date("Y-m-d H:i:s");
        $ip = $this->input->ip_address();

        $this->db->where('device_push_id', $push_id);
        $n_devices = $this->db->count_all_results('my_table');

        // If there's more than a device with the same push_id, then
        // the devices with are updated to not receive notifications
        // and a new device is created with different session token
        if($n_devices > 0){
            $this->db->set('device_notifications', 0);
            $this->db->where('device_push_id', $push_id);
            $this->db->update('device');
        }

        $data = array(
            'device_push_id' => $push_id,
            'device_token' => md5(microtime().$ip), // Char(32) format
            'device_lat'=> $latitude,
            'device_lng' => $longitude,
            'device_model' => $model,
            'device_plataform' => $plataform,
            'device_version' => $version,
            'device_date_registration' => $date,
            'device_magnitude' => 4.0,
            'device_range' => 1800,
            'device_notifications' => 1,
            'device_last_ip' => $ip,
            'device_last_date_login' => $date,
            'device_status' => 1
        );

        $this->db->insert('device', $data);

        return  array(
                    "status"=>"OK",
                    'device_token' => $data['device_token']
                );
    }


    /**
     * Function name: loadSettings
     *
     * Description: Retrieve the device settings according to the device token
     *
     * Parameters:
     * - $device_token <String>: it's the user token assigned for sismicapp as identifier
     *
     * Return: an array with the request status and settings data
     **/
    public function loadSettings($device_token){
        $this->db->select('magnitude, range, device_notifications');
        $this->db->where('device_token', $device_token);
        $settings_query = $this->db->get('device', 1, 0);

        if($settings_query->num_rows() == 1){
            $settings = $settings_query->row();

            return  array( 
	                    "status" => "OK",
	                    "settings" => $settings
	                );

        }

        return  array( 
                    "status" => "BAD",
                    "msg" => " ¡Ups! hay problemas cargando las configuraciones"
                );
    }


    /**
     * Function name: updateSettings
     *
     * Description: Update the device settings to receive notifications under
     *              some conditions
     *
     * Parameters:
     * - $device_token <String>: it's the user token assigned for sismicapp as identifier
     * - $magnitude <Float>: it's bottom limit of the seism magnitude in moment magnitude  
     *                       scale to send notifications
     * - $range <Integer>: it's the maximum distance in kilometers to receive notifications
     * - $notifications <Boolean>: it's the value to know if the device is allowed to 
     *                             receive notifications
     *
     * Return: an array with the request status
     **/
    public function updateSettings($device_token, $magnitude, $range, $notifications){
        $date = date("Y-m-d H:i:s");

        $data = array(
            'device_magnitude' => $magnitude,
            'device_range' => $range,
            'device_notifications' => $notifications
        );

        // Update device settings
        $this->db->where('device_token', $device_token);
        $this->db->limit(1);
        $this->db->update('device', $data_device);

        return array( "status"=>"OK" );
    }

}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */