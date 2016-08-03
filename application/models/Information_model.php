<?php
/**
 * This file handles with information model, this allows the administrator 
 * send informations to users throught sismicapp about general informations 
 * this was thought for social seism detection, but it could be extended
 * to other applications
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */

class Information_model extends CI_Model {

	public function __construct(){
        parent::__construct();
    }

    /**
     * Function name: detail
     *
     * Description: Retrieve information using an identifier as parameter.
     *
     * Parameters:
     * - $device_token <String> (Optional): it's the user token assigned for
     *                                      sismicapp as identifier
     * - $idInformation <Binary[16]>: it's the information identificator in 
     *								  the database
     *
     * Return: an array with data from a single information
     **/
    public function detail($device_token = NULL, $idInformation = NULL){
        $this->load->helper('general');
        
        if(ctype_alnum($idInformation)){
        	$this->db->select('information_title, information_content');
   	        $this->db->where('information_id', "UNHEX('".$idInformation."')", FALSE);
	        $information_query = $this->db->get('information', 1, 0);
	        if($information_query->num_rows() == 1){

	            // Getting the device identifier
	            $this->db->select('HEX(device_id) AS device_id');
	            $this->db->where('device_token', $device_token);
	            $device_query = $this->db->get('device', 1, 0);
	            $idDevice = NULL;
	            if($device_query->num_rows() == 1){
	                $idDevice = $device_query->row()->device_id;
	            }
	            
	            // TO DO: Logistic of saving in database logs

	            $information = $information_query->row();

                $data_information = array(
                    'id' => $idInformation,
                    'title' => $information->information_title,
                    'content' => $information_content
                );

	            return  array(
	                        'status' => 'OK',
	                        'information' => $data_information
	                    );
	        }else{
	            return  array(
	                        'status' => 'BAD',
	                        'msg' => '¡Ups! al parecer no tenemos la información que estás buscando'
	                    );
	        }
	    }else{
	    	return  array(
	                        'status' => 'BAD',
	                        'msg' => '¡Ups! al parecer no tenemos la información que estás buscando'
	                    );
	    }
    }

}

/* End of file Information_model.php */
/* Location: ./application/models/Information_model.php */