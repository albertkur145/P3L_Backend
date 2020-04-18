<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Pemesanan extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

        $this->load->model('Pemesanan_model', 'pemesanan'); 
        $this->load->model('DetailPemesanan_model', 'detailPemesanan'); 
        $this->load->model('Produk_model', 'produk'); 
        $this->load->model('Supplier_model', 'supplier'); 
    }

    public function create_post() {
        // get params
        $supplier = $this->post('supplier_id');
        $produk = $this->post('produk');

        // response error params not found
        if (!$supplier || !$produk) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error params produk incomplete
        if ($produk) {
            foreach ($produk as $value) {
                if (!isset($value['produk_id']) || !isset($value['satuan']) || !isset($value['jumlah'])) {
                    $this->response([
                        'code' => 400,
                        'status' => FALSE,
                        'message' => 'Params not found!'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        }

        // response error product is not exist
        foreach ($produk as $value) {
            $prod = $this->produk->get($value['produk_id']);

            if (!$prod) {
                $this->response([
                    'code' => 404,
                    'status' => FALSE,
                    'message' => 'Product not found!'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }

        // validation success then get last id
        $lastID = $this->pemesanan->getLastID();

        // set 2 digit (last id + 1)
        if ($lastID < 10) {                 // untuk yang dibawah < 10 => 1,2,3,....,9
            $lastID += 1;                   // ditambah 1 karena last id sudah dipakai
            $lastID = '0' . $lastID;        // ditambah 0 di depan supaya 2 digit, misal : 01, 02, 03, ... 09
        } else 
            $lastID += 1;                   // cukup langsung +1 karena diatas 10

        // set data
        $data = [
            'nomor_po' => 'PO-' . date('Y') . '-' .date('m') . '-' . date('d') . '-' . $lastID,
            'tanggal_pesan' => date("Y-m-d H:i:s"),
            'supplier_id' => $supplier,
            'created_at' => date("Y-m-d H:i:s"),
            'status' => 'Dipesan'
        ];

        // save data
        if ($this->pemesanan->save($data) > 0) {
            $pemesanan = $this->pemesanan->checkPemesananIsExist('PO-' . date('Y') . '-' .date('m') . '-' . date('d') . '-' . $lastID);

            // insert to detail pemesanan, di looping sebanyak jumlah array / produk yang dikirim lewat params
            foreach ($produk as $value) {
                $detail = [
                    'nomor_po' => $pemesanan[0]['nomor_po'],
                    'produk_id' => $value['produk_id'],
                    'satuan' => $value['satuan'],
                    'jumlah' => $value['jumlah']
                ];
                $this->detailPemesanan->save($detail);
            }

            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success save pemesanan',
                'data' => $pemesanan
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_get() {
        // get params
        $nomorPO = $this->get('nomor_po');

        if (!$nomorPO) 
            $pemesanan = $this->pemesanan->getAll(null);
        else
            $pemesanan = $this->pemesanan->getAll($nomorPO);

        // response error data not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get supplier name
        for ($i = 0; $i < count($pemesanan); $i++) {
            $supplier = $this->supplier->get($pemesanan[$i]['supplier_id']);
            $pemesanan[$i]['supplier_name'] = $supplier[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $pemesanan
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

        $pemesanan = $this->pemesanan->getPaging($page);
        
        // response error data not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get supplier name
        for ($i = 0; $i < count($pemesanan); $i++) {
            $supplier = $this->supplier->get($pemesanan[$i]['supplier_id']);
            $pemesanan[$i]['supplier_name'] = $supplier[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'amount' => $this->pemesanan->countData(),
            'data' => $pemesanan
        ], REST_Controller::HTTP_OK);
    }

    public function log_get() {
        // get params
        $nomorPO = $this->get('nomor_po');

        if ($nomorPO) 
            $pemesanan = $this->pemesanan->getLog($nomorPO);
        else
            $pemesanan = $this->pemesanan->getLog(null);

        // response error data not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // get supplier name
        for ($i = 0; $i < count($pemesanan); $i++) {
            $supplier = $this->supplier->get($pemesanan[$i]['supplier_id']);
            $pemesanan[$i]['supplier_name'] = $supplier[0]['nama'];
        }

        // response success
        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $pemesanan
        ], REST_Controller::HTTP_OK);
    }

    public function detail_get() {
        // get params
        $nomorPO = $this->get('nomor_po');

        // response error params not found
        if (!$nomorPO) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $pemesanan = $this->pemesanan->getByNomorPO($nomorPO);

        // response error data not found
        if (!$pemesanan) {
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
            'data' => $pemesanan
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get params
        $nomorPO = $this->post('nomor_po');
        $supplier = $this->post('supplier_id');
        $produk = $this->post('produk');

        // response error params not found
        if (!$nomorPO || !$supplier || !$produk) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error params produk incomplete
        if ($produk) {
            foreach ($produk as $value) {
                if (!isset($value['produk_id']) || !isset($value['satuan']) || !isset($value['jumlah'])) {
                    $this->response([
                        'code' => 400,
                        'status' => FALSE,
                        'message' => 'Params not found!'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        }

        // response error product is not exist
        foreach ($produk as $value) {
            $prod = $this->produk->get($value['produk_id']);

            if (!$prod) {
                $this->response([
                    'code' => 404,
                    'status' => FALSE,
                    'message' => 'Product not found!'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }

        // make sure nomor po is exist
        $pemesanan = $this->pemesanan->checkPemesananIsExist($nomorPO);

        // response error pemesanan not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Pemesanan not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response error pemesanan status is dicetak / dibatalkan
        if ($pemesanan[0]['status'] != 'Dipesan') {
            $this->response([
                'code' => 409,
                'status' => FALSE,
                'message' => 'Status pemesanan is '. $pemesanan[0]['status'] .'. Cannot be changed!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if ($this->detailPemesanan->delete($nomorPO) > 0) {
            // insert to detail pemesanan, di looping sebanyak jumlah array / produk yang dikirim lewat params
            foreach ($produk as $value) {
                $detail = [
                    'nomor_po' => $pemesanan[0]['nomor_po'],
                    'produk_id' => $value['produk_id'],
                    'satuan' => $value['satuan'],
                    'jumlah' => $value['jumlah']
                ];
                $this->detailPemesanan->save($detail);
            }

            $this->pemesanan->updatePemesanan($pemesanan[0]['nomor_po'], ['updated_at' => date("Y-m-d H:i:s"), 'supplier_id' => $supplier]);

            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success update pemesanan',
                'data' => $pemesanan
            ], REST_Controller::HTTP_OK);
        }
    }

    public function delete_post() {
        // get params
        $nomorPO = $this->post('nomor_po');

        // response error params not found
        if (!$nomorPO) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // make sure nomor po is exist
        $pemesanan = $this->pemesanan->checkPemesananIsExist($nomorPO);

        // response error pemesanan not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Pemesanan not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response error pemesanan status is dicetak / selesai / dibatalkan
        if ($pemesanan[0]['status'] != 'Dipesan') {
            $this->response([
                'code' => 409,
                'status' => FALSE,
                'message' => 'Status pemesanan is '. $pemesanan[0]['status'] .'. Cannot be changed!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = [
            'deleted_at' =>  date("Y-m-d H:i:s"),
            'status' => 'Dibatalkan'
        ];

        if ($this->pemesanan->updatePemesanan($nomorPO, $data) > 0) {
            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success cancel pemesanan'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function ordercame_post() {
        // get params
        $nomorPO = $this->post('nomor_po');

        // response error params not found
        if (!$nomorPO) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // make sure nomor po is exist
        $pemesanan = $this->pemesanan->checkPemesananIsExist($nomorPO);

        // response error pemesanan not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Pemesanan not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response error pemesanan status is dipesan / selesai / dibatalkan
        if ($pemesanan[0]['status'] != 'Dicetak') {
            $this->response([
                'code' => 411,
                'status' => FALSE,
                'message' => 'Pemesanan shall be dicetak!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = [
            'tanggal_masuk' => date("Y-m-d H:i:s"),
            'status' => 'Selesai'
        ];

        if ($this->pemesanan->pemesananComplete($nomorPO, $data) > 0) {
            $detailPemesanan = $this->detailPemesanan->get($nomorPO);

            foreach ($detailPemesanan as $value) {
                $this->produk->addStockPemesanan($value['produk_id'], $value['jumlah']);
            }

            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success update product stock'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function printOrder_get() {
        $this->load->library('pdfgenerator');

        // get params
        $nomorPO = $this->get('nomor_po');        

        $pemesanan = $this->pemesanan->getByNomorPO($nomorPO);

        // response error pemesanan not found
        if (!$pemesanan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Pemesanan not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // update status
        $this->pemesanan->updatePemesanan($pemesanan[0]['nomor_po'], ['status' => 'Dicetak']);

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['tanggal_pesan'] =  $this->format_tanggal(date("Y-m-d", strtotime($pemesanan[0]['tanggal_pesan'])));
        $data['pemesanan'] = $pemesanan[0];

        // load view convert to string
        $html = $this->load->view('print_order', $data, true);

        // show and print pdf
        $fileName = 'Pemesanan ' . $this->format_tanggal(date("Y-m-d", strtotime($pemesanan[0]['tanggal_pesan'])));
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    function format_tanggal($tanggal) {
        $bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }

}