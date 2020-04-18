<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Pegawai extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('Pegawai_model', 'pegawai'); 
        $this->load->model('RolePegawai_model', 'role'); 
    }

    public function create_post() {
        // get req body
        $nama = $this->post('nama');
        $alamat = $this->post('alamat');
        $tanggal_lahir = $this->post('tanggal_lahir');
        $no_hp = $this->post('no_hp');
        $username = $this->post('username');
        $password = $this->post('password');
        $role_id = $this->post('role_id');

        // set data
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'tanggal_lahir' => $tanggal_lahir,
            'no_hp' => $no_hp,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role_id' => $role_id,
            'created_at' => date("Y-m-d H:i:s")
        ];

        // response error params not found
        if (!$nama || !$alamat || !$tanggal_lahir || !$no_hp || !$username || !$password || !$role_id) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error same username
        if ($this->validateSameUsernamePost($username)) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Username already exist!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response success
        if ($this->pegawai->save($data) > 0) {
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
            $pegawai = $this->pegawai->get($id);
        else if ($nama)
            $pegawai = $this->pegawai->getByNama($nama);
        else
            $pegawai = $this->pegawai->get(null);
        
        // response error data not found
        if (!$pegawai) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get role pegawai
        for ($i = 0; $i < count($pegawai); $i++) {
            $role = $this->role->get($pegawai[$i]['role_id']);
            $pegawai[$i]['role_name'] = $role[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $pegawai
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

        $pegawai = $this->pegawai->getPaging($page);
        
        // response error data not found
        if (!$pegawai) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get role pegawai
        for ($i = 0; $i < count($pegawai); $i++) {
            $role = $this->role->get($pegawai[$i]['role_id']);
            $pegawai[$i]['role_name'] = $role[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'amount' => $this->pegawai->countData(),
            'data' => $pegawai
        ], REST_Controller::HTTP_OK);
    }

    public function log_get() {
        // get params
        $nama = $this->get('nama');

        if ($nama) 
            $pegawai = $this->pegawai->getLog($nama);
        else
            $pegawai = $this->pegawai->getLog(null);

        // response error data not found
        if (!$pegawai) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get role pegawai
        for ($i = 0; $i < count($pegawai); $i++) {
            $role = $this->role->get($pegawai[$i]['role_id']);
            $pegawai[$i]['role_name'] = $role[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $pegawai
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get req body
        $id = $this->post('id');
        $nama = $this->post('nama');
        $alamat = $this->post('alamat');
        $tanggal_lahir = $this->post('tanggal_lahir');
        $no_hp = $this->post('no_hp');
        $username = $this->post('username');
        $role_id = $this->post('role_id');

        // set data
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'tanggal_lahir' => $tanggal_lahir,
            'no_hp' => $no_hp,
            'username' => $username,
            'role_id' => $role_id,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        // response error params not found
        if (!$id || !$nama || !$alamat || !$tanggal_lahir || !$no_hp || !$username || !$role_id) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error same username
        if ($this->validateSameUsernamePut($username, $id)) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Username already exist!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error id not found
        if ($this->pegawai->update($data, $id) <= 0) {
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
        if ($this->pegawai->delete($id) <= 0) {
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

    public function validateSameUsernamePost($username) {
        if ($this->pegawai->getUsername($username)) 
            	return 1;

        return 0;
    }

    public function validateSameUsernamePut($username, $id) {
    	$pegawai = $this->pegawai->getUsername($username);

        if ($pegawai) {
        	if ($pegawai[0]['id'] != $id)
            	return 1;
        }
        return 0;
    }

    public function login_post() {
        // get req body
        $username = $this->post('username');
        $password = $this->post('password');

        // get user and validate
        $user = $this->pegawai->validateLogin($username, $password);

        // validate failed
        if ($user === 0) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Invalid username or password!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // get role pegawai
        $role = $this->role->get($user[0]['role_id']);
        $user[0]['role_name'] = $role[0]['nama'];

        // set response
        $user = [
            'id' => $user[0]['id'],
            'nama' => $user[0]['nama'],
            'alamat' => $user[0]['alamat'],
            'tanggal_lahir' => $user[0]['tanggal_lahir'],
            'no_hp' => $user[0]['no_hp'],
            'username' => $user[0]['username'],
            'role_id' => $user[0]['role_id'],
            'role_name' => $user[0]['role_name'],
        ];

        // return response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $user
        ], REST_Controller::HTTP_OK);
    }

}


?>