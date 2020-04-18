<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class TransaksiProduk extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('TransaksiProduk_model', 'transaksi'); 
        $this->load->model('DetailTransaksiProduk_model', 'detailTransaksi'); 
        $this->load->model('Produk_model', 'produk'); 
    }

    public function create_post() {
        // get params
        $produk = $this->post('produk');
        $cs = $this->post('cs_id');
        $customer = $this->post('customer_id');

        // response error params not found
        if (!$produk || !$cs || !$customer) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error params produk incomplete
        if ($produk) {
            foreach ($produk as $value) {
                if (!isset($value['produk_id']) || !isset($value['jumlah'])) {
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

        // response error product out of stock
        foreach ($produk as $value) {
            $stock = $this->produk->checkStockProduct($value['produk_id'], $value['jumlah']);

            if (!$stock) {
                $this->response([
                    'code' => 400,
                    'status' => FALSE,
                    'message' => 'Product is out of stock!'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }

        // validation success then get last id
        $lastID = $this->transaksi->getLastID();

        // set 2 digit (last id + 1)
        if ($lastID < 10) {                 // untuk yang dibawah < 10 => 1,2,3,....,9
            $lastID += 1;                   // ditambah 1 karena last id sudah dipakai
            $lastID = '0' . $lastID;        // ditambah 0 di depan supaya 2 digit, misal : 01, 02, 03, ... 09
        } else 
            $lastID += 1;                   // cukup langsung +1 karena diatas 10

        // set data
        $data = [
            'no_transaksi' => 'PR-' . date('d') . date('m') . date('y') . '-' . $lastID,
            'tanggal' => date("Y-m-d H:i:s"),
            'customer_id' => $customer,
            'cs_id' => $cs,
            'created_at' => date("Y-m-d H:i:s"),
            'pegawai_id' => $cs,
            'status' => 'Tidak selesai'
        ];

        // save data
        if ($this->transaksi->save($data) > 0) {
            $transaksi = $this->transaksi->checkTransaksiIsExist('PR-' . date('d') . date('m') . date('y') . '-' . $lastID);

            // insert to detail transaksi, di looping sebanyak jumlah array / produk yang dikirim lewat params
            foreach ($produk as $value) {
                $detail = [
                    'no_transaksi' => $transaksi[0]['no_transaksi'],
                    'produk_id' => $value['produk_id'],
                    'jumlah' => $value['jumlah']
                ];
                $this->detailTransaksi->save($detail);
                $this->produk->reduceStock($value['produk_id'], $value['jumlah']);
            }

            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success save transaction',
                'data' => $transaksi
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_get() {
        // get params
        $noTransaksi = $this->get('no_transaksi');

        if (!$noTransaksi) 
            $transaksi = $this->transaksi->getAll();
        else
            $transaksi = $this->transaksi->getByNoTransaction($noTransaksi);

        // response error data not found
        if (!$transaksi) {
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
            'data' => $transaksi
        ], REST_Controller::HTTP_OK);
    }

    public function complete_get() {
    	// get params
        $noTransaksi = $this->get('no_transaksi');

        if ($noTransaksi)
        	$transaksi = $this->transaksi->getAllComplete($noTransaksi);
        else
        	$transaksi = $this->transaksi->getAllComplete(null);

        // response error data not found
        if (!$transaksi) {
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
            'data' => $transaksi
        ], REST_Controller::HTTP_OK);
    }

    public function uncomplete_get() {
    	// get params
        $noTransaksi = $this->get('no_transaksi');

        if ($noTransaksi)
        	$transaksi = $this->transaksi->getAllUncomplete($noTransaksi);
        else
        	$transaksi = $this->transaksi->getAllUncomplete(null);

        // response error data not found
        if (!$transaksi) {
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
            'data' => $transaksi
        ], REST_Controller::HTTP_OK);
    }

    public function completecanceled_get() {
    	// get params
        $noTransaksi = $this->get('no_transaksi');

        if ($noTransaksi)
        	$transaksi = $this->transaksi->getAllCompleteOrCanceled($noTransaksi);
        else
        	$transaksi = $this->transaksi->getAllCompleteOrCanceled(null);

        // response error data not found
        if (!$transaksi) {
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
            'data' => $transaksi
        ], REST_Controller::HTTP_OK);
    }

    public function update_post() {
        // get params
        $noTransaksi = $this->post('no_transaksi');
        $produk = $this->post('produk');
        $pegawai = $this->post('pegawai_id');

        // response error params not found
        if (!$noTransaksi || !$produk || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error params produk incomplete
        if ($produk) {
            foreach ($produk as $value) {
                if (!isset($value['produk_id']) || !isset($value['jumlah'])) {
                    $this->response([
                        'code' => 400,
                        'status' => FALSE,
                        'message' => 'Params not found!'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        }

        // make sure transaksi is exist
        $transaksi = $this->transaksi->checkTransaksiIsExist($noTransaksi);

        // response error transaksi not found
        if (!$transaksi) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Transaction not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
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

        // response error product out of stock
        foreach ($produk as $value) {
            $stock = $this->produk->checkStockProduct($value['produk_id'], $value['jumlah']);

            if (!$stock) {
                $this->response([
                    'code' => 400,
                    'status' => FALSE,
                    'message' => 'Product is out of stock!'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }

        // status sbg penanda, produk ini sudah di update stock atau belum
        // karena harus cari selisih utk id product yg sama
        // 0 = belum, 1 = sudah
        foreach($produk as $key => $value) {
            $produk[$key]['status'] = 0;
        }

        $detailProduk = $this->detailTransaksi->get($noTransaksi);

        // status detail produk sbg penanda, jika ada produk yang tidak jadi di beli
        // maka stock harus dibalikin ke data produk
        // 0 = tidak jadi beli, 1 = product id sama maka update stock
        foreach ($detailProduk as $index => $value) {
            $detailProduk[$index]['status'] = 0;
        }

        // cari detail produk yang sama, cari selisih lalu update stock
        foreach ($detailProduk as $key => $detail) {
            foreach ($produk as $index => $prod) {
                // jika ada produk yang sama, maka update stock
                if ($prod['produk_id'] == $detail['produk_id']) {
                    $produk[$index]['status'] = 1;
                    $detailProduk[$key]['status'] = 1;
                    $this->produk->updateStock($noTransaksi, $prod['produk_id'], $prod['jumlah']);
                }
            }
        }

        // retur stock, produk yang tidak jadi dibeli user
        foreach ($detailProduk as $value) {
            if ($value['status'] == 0)
                $this->produk->addStock($noTransaksi, $value['produk_id']);
        }

        // delete = replace detail transaksi
        if ($this->detailTransaksi->delete($noTransaksi) > 0) {
            // insert to detail transaksi, di looping sebanyak jumlah array / produk yang dikirim lewat params
            foreach ($produk as $value) {
                $detail = [
                    'no_transaksi' => $transaksi[0]['no_transaksi'],
                    'produk_id' => $value['produk_id'],
                    'jumlah' => $value['jumlah']
                ];

                $this->detailTransaksi->save($detail);

                if ($value['status'] == 0)
                    $this->produk->reduceStock($value['produk_id'], $value['jumlah']);
            }

            $data = [
                'updated_at' => date("Y-m-d H:i:s"),
                'pegawai_id' => $pegawai
            ];
            $this->transaksi->updateTransaction($noTransaksi, $data);

            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success update transaction ' . $noTransaksi
            ], REST_Controller::HTTP_OK);
        }
    }

    public function delete_post() {
        // get params
        $noTransaksi = $this->post('no_transaksi');
        $pegawai = $this->post('pegawai_id');

        // response error params not found
        if (!$noTransaksi || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // make sure transaksi is exist
        $transaksi = $this->transaksi->checkTransaksiIsExist($noTransaksi);

        // response error transaksi not found
        if (!$transaksi) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Transaction not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // set data
        $data = [
            'pegawai_id' => $pegawai,
            'deleted_at' => date("Y-m-d H:i:s"),
            'status' => 'Dibatalkan'
        ];

        if ($this->transaksi->cancelTransaction($noTransaksi, $data) > 0) {

            // kembalikan stock produk ke semula
            $detail = $this->detailTransaksi->get($noTransaksi);

            foreach ($detail as $value) {
                $this->produk->addStock($noTransaksi, $value['produk_id']);
            }

            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success canceled transaction'
            ], REST_Controller::HTTP_OK);
        }
    }
}

?>