<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class RolePegawai extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('RolePegawai_model', 'role'); 
    }

    public function index_get() {
    	$id = $this->get('id');

    	if ($id)
    		$role = $this->role->get($id);
    	else
        	$role = $this->role->get(null);
        
        // response error data not found
        if (!$role) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $role
        ], REST_Controller::HTTP_OK);
    }

}


?>