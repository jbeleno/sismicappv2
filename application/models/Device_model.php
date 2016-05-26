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
     * Function name: new
     *
     * Description: Insert new device data in the database and return the
     *				device token to keep it secret.
     *
     * Parameters:
     * - $push_id: it's the token used by Android/iOS to send push notifications
     * - $latitude: it's the latitude coordinate of the device
     * - $longitude: it's the longitude coordinate of the device
     * - $model: it's the device model
     * - $platform: it's the device platform iOS or Android
     * - $version: it's the SO version of the software
     *
     * Return: an array in JSON format with the request status and the device
     * 		   identifier
     **/
    public function new($push_id, $latitude, $longitude, 
    					$model, $platform, $version){
    	$date = date("Y-m-d H:i:s");

    	$data = array(
			'device_push_id' => $push_id,
			'device_token' => sha1($date.$push_id),
			'device_lat'=> $latitude,
			'device_lng' => $longitude,
			'device_model' => $model,
			'device_plataform' => $plataform,
			'device_version' => $version,
			'device_date_registration' => $date,
			'device_magnitude' => 4.0,
			'device_range' => 1800,
			'device_notifications' => 1,
			'device_last_ip' => $this->input->ip_address(),
			'device_last_date_login' => $date
		);

		$this->db->insert('device', $data);

		return json_encode(
			array(
				"status"=>"OK",
				'ID' => $data['token']
			)
		);
    }

}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */