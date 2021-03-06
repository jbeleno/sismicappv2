<?php
/**
 * This file handles with the seism controller, so this is what is returned
 * when someone make a request to the services
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Seism extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('seism_model');
	}

	public function all()
	{
		$device_token = $this->input->post('device_token');
		$source = $this->input->post('source');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->seism_model->all($device_token, $source)));
	}

	public function detail()
	{
		$device_token = $this->input->post('device_token');
		$seism_id = $this->input->post('seism_id');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->seism_model->detail($device_token, $seism_id)));
	}

	public function scrape_international()
	{
		$ip = $this->input->ip_address();

		if($ip == ELASTIC_IP){
			$this->seism_model->scrapeInternational();

			$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode(array('status' => 'OK')));
		}else{
			$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode(
								array(
									'status' => 'BAD',
									'msg' => 'No tienes permisos para acceder a este servicio.'
								)
							));
		}
	}

	public function scrape_local()
	{
		$ip = $this->input->ip_address();

		if($ip == ELASTIC_IP){
			$this->seism_model->scrapeLocal();

			$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode(array('status' => 'OK')));
		}else{
			$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode(
								array(
									'status' => 'BAD',
									'msg' => 'No tienes permisos para acceder a este servicio.'
								)
							));
		}
	}

	public function spread()
	{
		$ip = $this->input->ip_address();

		if($ip == ELASTIC_IP){
			$this->seism_model->spreadTheVoice();

			$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode(array('status' => 'OK')));
		}else{
			$this->output
	         	 ->set_content_type('application/json')
	         	 ->set_output(json_encode(
								array(
									'status' => 'BAD',
									'msg' => 'No tienes permisos para acceder a este servicio.'
								)
							));
		}
	}

}

/* End of file Seism.php */
/* Location: ./application/controllers/Seism.php */