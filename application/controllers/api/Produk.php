<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Produk extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

        $this->load->model('Produk_model', 'produk');
        $this->load->model('KategoriProduk_model', 'kategori');
    }

    public function create_post() {
        // get req body
        $nama = htmlspecialchars($this->post('nama'));
        $stock = (int) htmlspecialchars($this->post('stock'));
        $harga = (int) htmlspecialchars($this->post('harga'));
        $kategori = (int) htmlspecialchars($this->post('kategori'));
        if (isset($_FILES['image']))
            $file = $_FILES['image'];
        else
            $file = null;

        // validation req body is not null
        if (!$nama || !$stock || !$harga || !$kategori || !$file) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // make sure upload image is success before store to database
        $upload = $this->uploadImage($file);
        if ($upload === null) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Error! Please try again'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // set data
        $data = [
            'nama' => $nama,
            'stock' => $stock,
            'harga' => $harga,
            'link_gambar' => base_url() . $upload,
            'kategori_id' => $kategori
        ];

        // success save response
        $data['created_at'] = date("Y-m-d H:i:s");
        if ($this->produk->save($data) > 0) {
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success save data'
            ], REST_Controller::HTTP_OK);
        }

        // error save response
        $this->response([
            'code' => 400,
            'status' => FALSE,
            'message' => 'Error! Please try again'
        ], REST_Controller::HTTP_BAD_REQUEST);
    }

    public function index_get() {
        // get params
        $id = $this->get('id');
        $nama = $this->get('nama');

        // if nama/id != null, then get by nama or id.... else get all
        if ($id)
            $produk = $this->produk->get($id);
        else if ($nama)
            $produk = $this->produk->getByNama($nama);
        else
            $produk = $this->produk->get(null);
        
        // response error get data null
        if (!$produk) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get kategori produk
        for ($i = 0; $i < count($produk); $i++) {
            $kategori = $this->kategori->get($produk[$i]['kategori_id']);
            $produk[$i]['kategori_name'] = $kategori[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $produk
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

        $produk = $this->produk->getPaging($page);
        
        // response error data not found
        if (!$produk) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get kategori produk
        for ($i = 0; $i < count($produk); $i++) {
            $kategori = $this->kategori->get($produk[$i]['kategori_id']);
            $produk[$i]['kategori_name'] = $kategori[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'amount' => $this->produk->countData(),
            'data' => $produk
        ], REST_Controller::HTTP_OK);
    }

    public function log_get() {
        // get params
        $nama = $this->get('nama');

        if ($nama) 
            $produk = $this->produk->getLog($nama);
        else
            $produk = $this->produk->getLog(null);

        // response error data not found
        if (!$produk) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get kategori produk
        for ($i = 0; $i < count($produk); $i++) {
            $kategori = $this->kategori->get($produk[$i]['kategori_id']);
            $produk[$i]['kategori_name'] = $kategori[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $produk
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get req body
        $id = htmlspecialchars($this->post('id'));
        $nama = htmlspecialchars($this->post('nama'));
        $stock = (int) htmlspecialchars($this->post('stock'));
        $harga = (int) htmlspecialchars($this->post('harga'));
        $kategori = (int) htmlspecialchars($this->post('kategori'));
        if (isset($_FILES['image']))
            $file = $_FILES['image'];
        else
            $file = null;

        // validation req body is not null
        if (!$id || !$nama || !$stock || !$harga || !$kategori || !$file) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // make sure upload image is success before store to database
        $upload = $this->uploadImage($file);
        if ($upload === null) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Error! Please try again'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // set data
        $data = [
            'nama' => $nama,
            'stock' => $stock,
            'harga' => $harga,
            'link_gambar' => base_url() . $upload,
            'kategori_id' => $kategori
        ];

        // success update response
        $data['updated_at'] = date("Y-m-d H:i:s");
        if ($this->produk->update($data, $id) > 0) {
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success update data'
            ], REST_Controller::HTTP_OK);
        } 

        // error update response
        $this->response([
            'code' => 404,
            'status' => FALSE,
            'message' => 'ID not found!'
        ], REST_Controller::HTTP_NOT_FOUND);
    }

    public function delete_post() {
        // get req body
        $id = (int) $this->post('id');
        
        // response error if id not exist
        if (!$id) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error if data unsuccess delete
        if ($this->produk->delete($id) <= 0) {
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

    public function uploadImage($file) {
        // get extension, generate random image name
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $imageName = rand() . '.' . $ext;
        $dir = 'uploads/' . $imageName;

        // move file and return directory / path
        if (move_uploaded_file($file["tmp_name"], $dir)) 
            return $dir;
        
        return null;
    }

    public function notifikasi_get() {
        $produk = $this->produk->getNotifikasi();
        
        // response error get data null
        if (!$produk) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get kategori produk
        for ($i = 0; $i < count($produk); $i++) {
            $kategori = $this->kategori->get($produk[$i]['kategori_id']);
            $produk[$i]['kategori_name'] = $kategori[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $produk
        ], REST_Controller::HTTP_OK);
    }

    public function sorting_get() {
        // get params
        $stock = $this->get('stock');
        $harga = $this->get('harga');

        // response error params not found
        if (!$stock || !$harga) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $produk = $this->produk->sorting($stock, $harga);

        // response error data not found
        if (!$produk) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get kategori produk
        for ($i = 0; $i < count($produk); $i++) {
            $kategori = $this->kategori->get($produk[$i]['kategori_id']);
            $produk[$i]['kategori_name'] = $kategori[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $produk
        ], REST_Controller::HTTP_OK);
    }

}


?>