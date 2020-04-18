<?php


class PembayaranLayanan_model extends CI_Model
{
    private $TABLE = 'pembayaran_layanan';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get($noTransaksi) {
        if ($noTransaksi)
            return $this->db->order_by('no_transaksi', 'ASC')->get_where($this->TABLE, ['no_transaksi' => $noTransaksi])->result_array();

        return $this->db->order_by('no_transaksi', 'ASC')->get($this->TABLE)->result_array();
    }

    public function getPayment($noTransaksi) {
        // select data yang diperlukan
        $this->db->select('
            dt.layanan_id,
            l.harga,
            c.is_member
        ');

        /* dari table apa dan join kemana */
        $this->db->from('transaksi_layanan t');
        $this->db->join('detail_transaksi_layanan dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('layanan l', 'l.id = dt.layanan_id');
        $this->db->join('customer c', 'c.id = t.customer_id');
        $this->db->where(['t.no_transaksi' => $noTransaksi, 't.status' => 'Tidak selesai', 't.deleted_at' => NULL]);

        // get data dan tampung di variabel
        $data = $this->db->get()->result_array();

        // hitung subtotal dan get data layanan
        $layanan = [];
        $subtotal = 0;
        foreach ($data as $value) {
            $subtotal += $value['harga'];
            array_push($layanan, ['id' => $value['layanan_id']]);
        }

        // cek member atau bukan
        $diskon = 0;
        if ($data[0]['is_member'] == 1)
            $diskon = 0.15;

        // hitung total bayar
        $totalBayar = ceil($subtotal - ($subtotal * $diskon));

        return $response = [
            'sub_total' => $subtotal,
            'diskon' => ($subtotal * $diskon),
            'total' => $totalBayar,
            'layanan' => $layanan
        ];
    }
}