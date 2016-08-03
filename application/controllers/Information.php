<?php
/**
 * This file handles with the information controller, so this is what is returned
 * when someone make a request to the services
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */
class Information extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('information_model');
	}

	public function detail()
	{
		$device_token = $this->input->post('device_token');
		$information_id = $this->input->post('information_id');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->information_model->detail($device_token, $information_id)));
	}

}

/* End of file Information.php */
/* Location: ./application/controllers/Information.php */