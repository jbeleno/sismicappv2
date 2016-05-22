<?php
/**
 * This file handles with seismic functions that has access to the database
 * data and allows the user performs read, update and write operations on the
 * 'sismos' table
 *
 * @author Juan Sebastián Beleño Díaz <jsbeleno@gmail.com>
 * @link http://www.sismicapp.com
 * @version 2.0
 * @since File available since Release 2.0
 */


class Seism_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }
    
    /**
	 * Function name: all
	 *
	 * Description: Retrieve information from the last 20 seisms in Colombia, 
	 * 				it saves a log about the user that is consulting the data
	 *				and shows a banner inviting the user to rate the app.
	 *
	 * Parameters:
	 * - $idUser (optional): it's the user identification, it's optional
	 *						 because it still has some problems in register of
	 *						 new users due to Phonegap plugins incompatibility
	 * - $source: it's the medium that use the user to access the data, by 
	 *			  default it's "App"
	 *
	 * Return: an array in  JSON format of data that could belong to seism 
	 *		   information or banner
	 **/
    public function all($idUser = NULL, $source = "App"){
    	$logs = array();
    	$data = array();

    	$this->db->select('id, magnitud, magnitud_richter, epicentro, fecha');
    	$this->db->order_by('id', 'desc');

    	$seisms_query = $this->db->get('sismos', 20, 0);

    	foreach ($seisms_query->result() as $seism) {
    		$dateAgo = convertDateToXTimeAgo($seism->fecha);

    		$seism->fecha = $dateAgo;
    		$seism->tipo = 'sismo';

    		$data[] = $seism;

    		$logs[] = array(
    			($idUser != "")? $idUser : "NA",
    			$seism->id,
    			$this->input->ip_address(),
    			$source,
    			date('Y-m-d H:i:s')
    		);
    	}

    	$user_threshold_new_version = 745;
    	if($idUser > $user_threshold_new_version){
    		$data[] = array(
    			'tipo' => 'banner',
    			'banner' => $this->load->view('banners/ratingapp', NULL, TRUE)
    		);
    	}

    	saveLogArray($logs, 'temp/impressions.txt', $sep = ",");

    	print json_encode(
    		array(
    			'status' => 'OK',
    			'posts' => $data
    		);
    	);
    }

    /**
     * Function name: detail
     *
     * Description: Retrieve information from a seism using an identifier as
     *              parameter.
     *
     * Parameters:
     * - $idSeism: it's the seism identificator in the database
     *
     * Return: an array in JSON format of the seism selected or an error in
     *         in JSON format
     **/
    public function detail($idSeism = NULL){
        $this->db->select('latitud, longitud, epicentro, fecha, profundidad, magnitud, magnitud_richter');
        $this->db->where('id', $idSeism);

        $seism_query = $this->db->get('sismos', 1, 0);

        if($seism_query->num_rows() == 1){
            print json_encode(
                array(
                    'status' => 'OK',
                    'seism' => $seism_query->row()
                );
            );
        }else{
            print json_encode(
                array(
                    'status' => 'BAD',
                    'msg' => '¡Ups! al parecer no tenemos datos del sismo que estás buscando'
                );
            );
        }
    }
}