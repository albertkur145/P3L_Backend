<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';


class Laporan extends REST_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        
        $this->load->model('Laporan_model', 'laporan');
        $this->load->library('pdfgenerator');
    }

    public function pendapatanbulanan_get() {
        // get params
        $bulan = $this->get('bulan');
        $tahun = $this->get('tahun');

        // response error params not found
        if (!$bulan || !$tahun) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $pendapatanProduk = $this->laporan->pendapatanBulananProduk($bulan, $tahun);
        $pendapatanLayanan= $this->laporan->pendapatanBulananLayanan($bulan, $tahun);

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['pendapatan_produk'] = $pendapatanProduk;
        $data['pendapatan_layanan'] = $pendapatanLayanan;
        $data['tahun'] = $tahun;
        $data['bulan'] = $this->getBulan($bulan);

        // load view convert to string
        $html = $this->load->view('laporan_pendapatan_bulanan', $data, true);

        // show and print pdf
        $fileName = 'Laporan Pendapatan Bulanan';
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    public function pendapatantahunan_get() {
        // get params
        $tahun = $this->get('tahun');

        // response error params not found
        if (!$tahun) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $pendapatanProduk = $this->laporan->pendapatanTahunanProduk($tahun);
        $pendapatanLayanan = $this->laporan->pendapatanTahunanLayanan($tahun);

        $totalPendapatan = 0;
        foreach ($pendapatanProduk as $key => $value) {
            $totalPendapatan += $value['pendapatan'];
        }

        foreach ($pendapatanLayanan as $key => $value) {
            $totalPendapatan += $value['pendapatan'];
        }

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['tahun'] = $tahun;
        $data['pendapatan_produk'] = $pendapatanProduk;
        $data['pendapatan_layanan'] = $pendapatanLayanan;
        $data['total_pendapatan'] = $totalPendapatan;

        // load view convert to string
        $html = $this->load->view('laporan_pendapatan_tahunan', $data, true);

        // show and print pdf   
        $fileName = 'Laporan Pendapatan Tahunan';
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    public function pengadaanbulanan_get() {
        // get params
        $bulan = $this->get('bulan');
        $tahun = $this->get('tahun');

        // response error params not found
        if (!$bulan || !$tahun) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $pengadaanProduk = $this->laporan->pengadaanBulananProduk($bulan, $tahun);

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['pengadaan_produk'] = $pengadaanProduk;
        $data['tahun'] = $tahun;
        $data['bulan'] = $this->getBulan($bulan);

        // load view convert to string
        $html = $this->load->view('laporan_pengadaan_bulanan', $data, true);

        // show and print pdf
        $fileName = 'Laporan Pengadaan Bulanan';
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    public function pengadaantahunan_get() {
        // get params
        $tahun = $this->get('tahun');

        // response error params not found
        if (!$tahun) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $pengadaanProduk = $this->laporan->pengadaanTahunanProduk($tahun);

        if ($pengadaanProduk != 0) {
            $totalPengeluaran = 0;
            foreach ($pengadaanProduk as $key => $value) {
                $totalPengeluaran += $value['pengeluaran'];
            }
        }

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['pengadaan_produk'] = $pengadaanProduk;
        $data['tahun'] = $tahun;

        if ($pengadaanProduk != 0)
            $data['total_pengeluaran'] = $totalPengeluaran;

        // load view convert to string
        $html = $this->load->view('laporan_pengadaan_tahunan', $data, true);

        // show and print pdf
        $fileName = 'Laporan Pengadaan Tahunan';
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    public function produkterlaris_get() {
        // get params
        $tahun = $this->get('tahun');

        // response error params not found
        if (!$tahun) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10)
                $produk['0' . $i] = $this->laporan->produkTerlarisBulanan('0'.$i, $tahun);
            else 
                $produk[$i] = $this->laporan->produkTerlarisBulanan($i, $tahun);
        }

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['produk'] = $produk;
        $data['tahun'] = $tahun;

        // load view convert to string
        $html = $this->load->view('laporan_produk_terlaris', $data, true);

        // show and print pdf
        $fileName = 'Laporan Produk Terlaris';
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    public function layananterlaris_get() {
        // get params
        $tahun = $this->get('tahun');

        // response error params not found
        if (!$tahun) {
            $this->response([
                'code' => 400,
                'status' => FALSE,
                'message' => 'Params not found!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10)
                $layanan['0' . $i] = $this->laporan->layananTerlarisBulanan('0'.$i, $tahun);
            else 
                $layanan[$i] = $this->laporan->layananTerlarisBulanan($i, $tahun);
        }

        // set data for view
        $data['printed'] =  $this->format_tanggal(date('Y-m-d'));
        $data['layanan'] = $layanan;
        $data['tahun'] = $tahun;

        // load view convert to string
        $html = $this->load->view('laporan_layanan_terlaris', $data, true);

        // show and print pdf
        $fileName = 'Laporan Layanan Terlaris';
        $this->pdfgenerator->generate($html, $fileName, true, 'A4', 'portrait', 0);
    }

    function format_tanggal($tanggal) {
        $bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }

    function getBulan($param) {
        $bulan = array (1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

        return $bulan[(int)$param];
    }

}

?>