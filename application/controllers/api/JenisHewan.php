<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class JenisHewan extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('JenisHewan_model', 'jenis'); 
    }

    public function create_post() {
        // get req body
        $nama = $this->post('nama');

        // set data
        $data = [
            'nama' => $nama,
            'created_at' => date("Y-m-d H:i:s")
        ];

        // response error params not found
        if (!$nama) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response success
        if ($this->jenis->save($data) > 0) {
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success save data'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_get() {
        // get params
        $id = $this->get('id');
        $nama = $this->get('nama');

        // if nama/id != null, then get by nama or id.... else get all
        if ($id)
            $jenis = $this->jenis->get($id);
        else if ($nama) 
            $jenis = $this->jenis->getByNama($nama);
        else
            $jenis = $this->jenis->get(null);
        
        // response error data not found
        if (!$jenis) {
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
            'data' => $jenis
        ], REST_Controller::HTTP_OK);
    }

    public function log_get() {
        // get params
        $nama = $this->get('nama');

        if ($nama) 
            $jenis = $this->jenis->getLog($nama);
        else
            $jenis = $this->jenis->getLog(null);

        // response error data not found
        if (!$jenis) {
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
            'data' => $jenis
        ], REST_Controller::HTTP_OK);
    }

    public function paging_get() {
        // get params
        $page = $this->get('page');

        if (!$page) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $jenis = $this->jenis->getPaging($page);
        
        // response error data not found
        if (!$jenis) {
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
            'amount' => $this->jenis->countData(),
            'data' => $jenis
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get req body
        $id = $this->post('id');
        $nama = $this->post('nama');

        // set data
        $data = [
            'nama' => $nama,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        // response error params not found
        if (!$id || !$nama) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error id not found
        if ($this->jenis->update($data, $id) <= 0) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'ID not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'message' => 'Success update data'
        ], REST_Controller::HTTP_OK);
    }

    public function delete_post() {
        // get req body
        $id = (int) $this->post('id');
        
        // response error params not found
        if (!$id) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error id not found
        if ($this->jenis->delete($id) <= 0) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'ID not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'message' => 'Success delete'
        ], REST_Controller::HTTP_OK);
    }

}


?>