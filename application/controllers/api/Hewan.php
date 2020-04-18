<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Hewan extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('Hewan_model', 'hewan');
        $this->load->model('JenisHewan_model', 'jenis');
        $this->load->model('UkuranHewan_model', 'ukuran');
        $this->load->model('Pegawai_model', 'pegawai');
    }

    public function create_post() {
        // get req body
        $nama = $this->post('nama');
        $tanggalLahir = $this->post('tanggal_lahir');
        $ukuran = $this->post('ukuran_id');
        $jenis = $this->post('jenis_id');
        $pegawai = $this->post('pegawai_id');

        // set data
        $data = [
            'nama' => $nama,
            'tanggal_lahir' => $tanggalLahir,
            'ukuran_id' => $ukuran,
            'jenis_id' => $jenis,
            'created_at' => date("Y-m-d H:i:s"),
            'pegawai_id' => $pegawai
        ];

        // response error params not found
        if (!$nama || !$tanggalLahir || !$ukuran || !$jenis || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response success
        if ($this->hewan->save($data) > 0) {
            $id = $this->hewan->lastID();
            $hewan = $this->hewan->get($id);
            $jenis = $this->jenis->get($hewan[0]['jenis_id']);
            $ukuran = $this->ukuran->get($hewan[0]['ukuran_id']);

            $hewan[0]['jenis_hewan'] = $jenis[0]['nama'];
            $hewan[0]['ukuran_hewan'] = $ukuran[0]['nama'];

            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success save data',
                'data' => $hewan
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_get() {
        // get params
        $id = $this->get('id');
        $nama = $this->get('nama');

        // if nama/id != null, then get by nama or id.... else get all
        if ($id)
            $hewan = $this->hewan->get($id);
        else if ($nama) 
            $hewan = $this->hewan->getByNama($nama);
        else
            $hewan = $this->hewan->get(null);
        
        // response error data not found
        if (!$hewan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get jenis dan ukuran hewan
        for ($i = 0; $i < count($hewan); $i++) {
            $jenis = $this->jenis->getJenis($hewan[$i]['jenis_id']);
            $ukuran = $this->ukuran->getUkuran($hewan[$i]['ukuran_id']);
            $hewan[$i]['jenis_hewan'] = $jenis[0]['nama'];
            $hewan[$i]['ukuran_hewan'] = $ukuran[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $hewan
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

        $hewan = $this->hewan->getPaging($page);
        
        // response error data not found
        if (!$hewan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get jenis dan ukuran hewan
        for ($i = 0; $i < count($hewan); $i++) {
            $jenis = $this->jenis->getJenis($hewan[$i]['jenis_id']);
            $ukuran = $this->ukuran->getUkuran($hewan[$i]['ukuran_id']);
            $hewan[$i]['jenis_hewan'] = $jenis[0]['nama'];
            $hewan[$i]['ukuran_hewan'] = $ukuran[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'amount' => $this->hewan->countData(),
            'data' => $hewan
        ], REST_Controller::HTTP_OK);
    }

    public function log_get() {
        // get params
        $nama = $this->get('nama');

        if ($nama) 
            $hewan = $this->hewan->getLog($nama);
        else
            $hewan = $this->hewan->getLog(null);

        // response error data not found
        if (!$hewan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get jenis dan ukuran hewan
        for ($i = 0; $i < count($hewan); $i++) {
            $jenis = $this->jenis->getJenis($hewan[$i]['jenis_id']);
            $ukuran = $this->ukuran->getUkuran($hewan[$i]['ukuran_id']);
            $hewan[$i]['jenis_hewan'] = $jenis[0]['nama'];
            $hewan[$i]['ukuran_hewan'] = $ukuran[0]['nama'];
        }

        // get pegawai name
        for ($i = 0; $i < count($hewan); $i++) {
            $pegawai = $this->pegawai->getByIDLog($hewan[$i]['pegawai_id']);
            $hewan[$i]['pegawai_name'] = $pegawai[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $hewan
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get req body
        $id = $this->post('id');
        $nama = $this->post('nama');
        $tanggalLahir = $this->post('tanggal_lahir');
        $ukuran = $this->post('ukuran_id');
        $jenis = $this->post('jenis_id');
        $pegawai = $this->post('pegawai_id');

        // set data
        $data = [
            'nama' => $nama,
            'tanggal_lahir' => $tanggalLahir,
            'ukuran_id' => $ukuran,
            'jenis_id' => $jenis,
            'pegawai_id' => $pegawai,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        // response error params not found
        if (!$id || !$nama || !$tanggalLahir || !$ukuran || !$jenis || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error id not found
        if ($this->hewan->update($data, $id) <= 0) {
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
        if ($this->hewan->delete($id, $pegawai) <= 0) {
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