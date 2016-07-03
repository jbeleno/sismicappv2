<?php
/**
 * This file handles with the device controller, so this is what is returned
 * when someone make a request to the services
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Device extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('device_model');
	}

	public function add()
	{
		$app_version = $this->input->post('app_version');
		$model = $this->input->post('model');
		$platform = $this->input->post('platform');
		$version = $this->input->post('version');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->device_model->add($model, $platform, $version, app_version)));
	}

	public function load_settings()
	{
		$device_token = $this->input->post('device_token');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->device_model->loadSettings($device_token)));
	}

	public function update_push_key()
	{
		$device_token = $this->input->post('device_token');
		$push_key = $this->input->post('push_key');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->device_model->updatePushKey($device_token, $push_key)));
	}


	public function update_settings()
	{
		$device_token = $this->input->post('device_token');
		$magnitude = $this->input->post('magnitude');
		$range = $this->input->post('range');
		$notifications = $this->input->post('notifications');

		$this->output
	         ->set_content_type('application/json')
	         ->set_output(json_encode($this->device_model->updateSettings($device_token, $magnitude, $range, $notifications)));
	}
}

/* End of file Device.php */
/* Location: ./application/controllers/Device.php */