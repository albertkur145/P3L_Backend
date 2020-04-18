<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class TransaksiLayanan extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('TransaksiLayanan_model', 'transaksi'); 
        $this->load->model('DetailTransaksiLayanan_model', 'detailTransaksi'); 
        $this->load->model('Layanan_model', 'layanan'); 
        $this->load->model('Hewan_model', 'hewan'); 
        $this->load->model('Customer_model', 'customer'); 
    }

    public function create_post() {
        // get params
        $layanan = $this->post('layanan');
        $cs = $this->post('cs_id');
        $customer = $this->post('customer_id');
        $hewan = $this->post('hewan_id');

        // response error params not found
        if (!$layanan || !$cs || !$customer || !$hewan) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error params layanan incomplete
        if ($layanan) {
            foreach ($layanan as $value) {
                if (!isset($value['layanan_id'])) {
                    $this->response([
                        'code' => 400,
                        'status' => FALSE,
                        'message' => 'Params not found!'
                    ], REST_Controller::HTTP_BAD_REQUEST);
                }
            }
        }

        // response error layanan is not exist
        foreach ($layanan as $value) {
            $lay = $this->layanan->get($value['layanan_id']);

            if (!$lay) {
                $this->response([
                    'code' => 404,
                    'status' => FALSE,
                    'message' => 'Layanan not found!'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }

        // response error hewan not found
        $dataHewan = $this->hewan->get($hewan);
        if (!$dataHewan) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Hewan not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
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
            'no_transaksi' => 'LY-' . date('d') . date('m') . date('y') . '-' . $lastID,
            'tanggal' => date("Y-m-d H:i:s"),
            'customer_id' => $customer,
            'cs_id' => $cs,
            'created_at' => date("Y-m-d H:i:s"),
            'pegawai_id' => $cs,
            'status' => 'Tidak selesai'
        ];

        // save data
        if ($this->transaksi->save($data) > 0) {
            $transaksi = $this->transaksi->checkTransaksiIsExist('LY-' . date('d') . date('m') . date('y') . '-' . $lastID);

            // insert to detail transaksi, di looping sebanyak jumlah array yang dikirim lewat params
            foreach ($layanan as $value) {
                $detail = [
                    'no_transaksi' => $transaksi[0]['no_transaksi'],
                    'hewan_id' => $hewan,
                    'layanan_id' => $value['layanan_id'],
                ];
                $this->detailTransaksi->save($detail);
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
        $layanan = $this->post('layanan');
        $pegawai = $this->post('pegawai_id');

        // response error params not found
        if (!$noTransaksi || !$layanan || !$pegawai) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // response error params layanan incomplete
        if ($layanan) {
            foreach ($layanan as $value) {
                if (!isset($value['layanan_id'])) {
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

        // response error layanan is not exist
        foreach ($layanan as $value) {
            $lay = $this->layanan->get($value['layanan_id']);

            if (!$lay) {
                $this->response([
                    'code' => 404,
                    'status' => FALSE,
                    'message' => 'Layanan not found!'
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }

        $detailLayanan = $this->detailTransaksi->get($noTransaksi);

        // delete = replace detail transaksi
        if ($this->detailTransaksi->delete($noTransaksi) > 0) {
            // insert to detail transaksi, di looping sebanyak jumlah array / layanan yang dikirim lewat params
            foreach ($layanan as $value) {
                $detail = [
                    'no_transaksi' => $transaksi[0]['no_transaksi'],
                    'hewan_id' => $detailLayanan[0]['hewan_id'],
                    'layanan_id' => $value['layanan_id']
                ];

                $this->detailTransaksi->save($detail);
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
            // response success
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success canceled transaction'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function reminder_post() {
        $idUser = $this->post('id_user');

        // response error params not found
        if (!$idUser) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // make sure customer is exist
        $customer = $this->customer->getWithoutDeleted($idUser);

        // response error customer not found
        if (!$customer) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Customer not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $customer = $customer[0];

        $userkey = 'e478148ffa7e';
        $passkey = 'qpph9zcz1r';
        $telepon = $customer['no_hp'];
        $message = 'Hai ' . $customer['nama'] . ', jasa layanan kamu sudah siap. Jangan lupa diambil ya. Dari Kouvee Pet Shop';
        $url = 'https://gsm.zenziva.net/api/sendsms/';
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
            'userkey' => $userkey,
            'passkey' => $passkey,
            'nohp' => $telepon,
            'pesan' => $message
        ));
        $results = json_decode(curl_exec($curlHandle), true);

        if ($results) {
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Success reminder customer'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            $this->response([
                'code' => 500,
                'status' => FALSE,
                'message' => 'Error! Try Again.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
        curl_close($curlHandle);
    }

}