<?php
/**
 * This file handles with the notification controller, so this is what is returned
 * when someone make a request to the services
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */
class Notification extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('notification_model');
	}

	public function all()
	{
		$device_token = $this->input->post('device_token');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->notification_model->all($device_token)));
	}

}

/* End of file Notification.php */
/* Location: ./application/controllers/Notification.php */