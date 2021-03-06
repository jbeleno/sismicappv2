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

	public function add()
	{
		$device_token = $this->input->post('device_token');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$msg = $this->input->post('msg');
		$city = $this->input->post('city');
		$region = $this->input->post('region');
		$country = $this->input->post('country');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->feedback_model->add($device_token, $latitude, $longitude, $msg, $city, $region, $country)));
	}

}

/* End of file Feedback.php */
/* Location: ./application/controllers/Feedback.php */