<?php
/**
 * This file handles with the feedback controller, so this is what is returned
 * when someone make a request to the services
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Feedback extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('feedback_model');
	}

	public function new()
	{
		$device_token = $this->input->post('device_token');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$msg = $this->input->post('msg');

		print json_encode($this->feedback_model->new($device_token, $latitude, $longitude, $msg));
	}

}

/* End of file Feedback.php */
/* Location: ./application/controllers/Feedback.php */