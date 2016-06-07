<?php
/**
 * This file handles with the session controller, so this is what is returned
 * when someone make a request to the services
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Session extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('session_model');
	}

	public function add()
	{
		$device_token = $this->input->post('device_token');
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');

		$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode($this->session_model->add($device_token, $latitude, $longitude)));
	}

}

/* End of file Session.php */
/* Location: ./application/controllers/Session.php */