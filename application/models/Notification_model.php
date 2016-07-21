<?php
/**
 * This file handles with push notifications in sismicapp
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Notification_model extends CI_Model {

	/**
     * Function name: all
     *
     * Description: Retrieve information from the last 15 notifications for 
     *				that device.
     *
     * Parameters:
     * - $device_token <String>: it's the user token assigned for sismicapp as 
     *							 identifier
     * - $limit <Integer> (Optional): it's the number of notifications shown per
     *								  device
     *
     * Return: an array of notifications for that user
     **/
	public function all($device_token, $limit = 15){

		// Load helpers
		$this->load->helper('general');

		// Getting the device identifier
        $this->db->select('HEX(device_id) AS device_id');
        $this->db->where('device_token', $device_token);
        $device_query = $this->db->get('device', 1, 0);
        $idDevice = NULL;
        if($device_query->num_rows() == 1){
            $idDevice = $device_query->row()->device_id;
        }

        if($idDevice){
        	// Getting the notifications from the device with the identifier provided
        	$this->db->select('notification_content_id, notification_type, notification_content, notification_read_status, notification_date');
			$this->db->where('notification_device_id', "UNHEX('".$idDevice."')", FALSE);
			$notifications_query = $this->db->get('notification', $limit, 0);

			if($notifications_query->num_rows() > 0){
				
				$notifications = array();
				foreach ($notifications_query->result() as $notification) {
					$notifications[] = array(
						'id' => $notification->notification_content_id,
						'type' => $notification->notification_type,
						'content' => $notification->notification_content,
						'status' =>  $notification->notification_read_status,
						'time' => convertDateToXTimeAgo($notification->notification_date)
					);
				}

				return array(
					'status' => 'OK',
					'notifications' => $notificacions
				);

			}else{
				return array(
	        		'status' => 'OK',
	        		'msg' => '¡Ups! parece que no existen notificaciones para ti'
	        	);
			}
        }else{
        	return array(
        		'status' => 'OK',
        		'msg' => '¡Ups! parece que no existen notificaciones para ti'
        	);
        }

	}

}

/* End of file Notification_model.php */
/* Location: ./application/models/Notification_model.php */