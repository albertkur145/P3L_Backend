<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class PembayaranProduk extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('TransaksiProduk_model', 'transaksi'); 
        $this->load->model('PembayaranProduk_model', 'pembayaran'); 
    }

    public function index_post() {
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

        // get data subtotal, diskon, and total (payment)
        $temp = $this->pembayaran->getPayment($noTransaksi);
        $dataBayar = [
            'sub_total' => $temp['sub_total'],
            'diskon' => $temp['diskon'],
            'total' => $temp['total'],
            'no_transaksi' => $noTransaksi,
            'created_at' => date("Y-m-d H:i:s")
        ];

        // set data for update transaksi
        $dataTransaksi = [
            'kasir_id' => $pegawai,
            'status' => 'Selesai'
        ];

        if ($this->pembayaran->save($dataBayar) > 0 && $this->transaksi->updatePaymentSuccess($noTransaksi, $dataTransaksi) > 0) {
            $this->response([
                'code' => 200,
                'status' => TRUE,
                'message' => 'Payment success'
            ], REST_Controller::HTTP_OK);
        }
    }

    public function index_get() {
        // get params
        $noTransaksi = $this->get('no_transaksi');

        if ($noTransaksi)
            $pembayaran = $this->pembayaran->get($noTransaksi);
        else
            $pembayaran = $this->pembayaran->get(null);

        if (!$pembayaran) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Data not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $this->response([
            'code' => 200,
            'status' => TRUE,
            'data' => $pembayaran
        ], REST_Controller::HTTP_NOT_FOUND);
    }

    public function nota_get() {
        $this->load->library('pdfgenerator');

        // get params
        $noTransaksi = $this->get('no_transaksi');

        $transaksi = $this->transaksi->getByNoTransaction($noTransaksi);

        // response error transaksi not found
        if (!$transaksi) {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Transaksi not found!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // response error transaksi not yet paid
        if ($transaksi[0]['status'] != 'Selesai') {
            $this->response([
                'code' => 404,
                'status' => FALSE,
                'message' => 'Transaksi must be paid!'
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        // set data for view
        $data['transaksi'] = $transaksi[0];
        $data['tanggal'] = $this->format_tanggal(explode(' ', $transaksi[0]['tanggal'])[0]);
        $waktu = explode(':', explode(' ', $transaksi[0]['tanggal'])[1]);
        $data['waktu'] = $waktu[0] . ':' . $waktu[1];

        // load view convert to string
        $html = $this->load->view('nota_produk', $data, true);

        // show and print pdf
        $fileName = 'Nota Lunas ' . $transaksi[0]['no_transaksi'];
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 1);
    }

    function format_tanggal($tanggal) {
        $bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }

}