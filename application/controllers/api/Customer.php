<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Customer extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('Customer_model', 'customer'); 
        $this->load->model('Pegawai_model', 'pegawai'); 
    }

    public function create_post() {
        // get req body
        $nama = $this->post('nama');
        $alamat = $this->post('alamat');
        $tanggalLahir = $this->post('tanggal_lahir');
        $noHP = $this->post('no_hp');
        $isMember = $this->post('is_member');
        $pegawai = $this->post('pegawai_id');

        // set data
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'tanggal_lahir' => $tanggalLahir,
            'no_hp' => $noHP,
            'is_member' => $isMember,
            'created_at' => date("Y-m-d H:i:s"),
            'pegawai_id' => $pegawai
        ];

        // response error params not found
        if (!$nama || !$alamat || !$tanggalLahir || !$noHP || !$isMember || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response success
        if ($this->customer->save($data) > 0) {
            $id = $this->customer->lastID();
            $customer = $this->customer->get($id);

            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success save data',
                'data' => $customer
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_get() {
        // get params
        $id = $this->get('id');
        $nama = $this->get('nama');

        // if nama/id != null, then get by nama or id.... else get all
        if ($id)
            $customer = $this->customer->get($id);
        else if ($nama) 
            $customer = $this->customer->getByNama($nama);
        else
            $customer = $this->customer->get(null);
        
        // response error data not found
        if (!$customer) {
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
            'data' => $customer
        ], REST_Controller::HTTP_OK);
    }

    public function member_get() {
        // get params
        $id = $this->get('id');
        $nama = $this->get('nama');

        // if nama/id != null, then get by nama or id.... else get all
        if ($id)
            $customer = $this->customer->getAllMember($id);
        else if ($nama) 
            $customer = $this->customer->getAllMemberByNama($nama);
        else
            $customer = $this->customer->getAllMember(null);
        
        // response error data not found
        if (!$customer) {
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
            'data' => $customer
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

        $customer = $this->customer->getPaging($page);
        
        // response error data not found
        if (!$customer) {
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
            'amount' => $this->customer->countData(),
            'data' => $customer
        ], REST_Controller::HTTP_OK);
    }

    public function log_get() {
        // get params
        $nama = $this->get('nama');

        if ($nama) 
            $customer = $this->customer->getLog($nama);
        else
            $customer = $this->customer->getLog(null);

        // response error data not found
        if (!$customer) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get pegawai name
        for ($i = 0; $i < count($customer); $i++) {
            $pegawai = $this->pegawai->getByIDLog($customer[$i]['pegawai_id']);
            $customer[$i]['pegawai_name'] = $pegawai[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $customer
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get req body
        $id = $this->post('id');
        $nama = $this->post('nama');
        $alamat = $this->post('alamat');
        $tanggalLahir = $this->post('tanggal_lahir');
        $noHP = $this->post('no_hp');
        $pegawai = $this->post('pegawai_id');

        // set data
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'tanggal_lahir' => $tanggalLahir,
            'no_hp' => $noHP,
            'pegawai_id' => $pegawai,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        // response error params not found
        if (!$id || !$nama || !$alamat || !$tanggalLahir || !$noHP || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error id not found
        if ($this->customer->update($data, $id) <= 0) {
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
        $pegawai = (int) $this->post('pegawai_id');
        
        // response error params not found
        if (!$id || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error id not found
        if ($this->customer->delete($id, $pegawai) <= 0) {
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