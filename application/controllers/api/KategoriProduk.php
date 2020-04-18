<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class KategoriProduk extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('KategoriProduk_model', 'kategori'); 
    }

    public function index_get() {
    	$id = $this->get('id');

    	if ($id)
        	$kategori = $this->kategori->get($id);
        else
        	$kategori = $this->kategori->get(null);
        
        // response error data not found
        if (!$kategori) {
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
            'data' => $kategori
        ], REST_Controller::HTTP_OK);
    }

}


?>