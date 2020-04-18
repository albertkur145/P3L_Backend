<?php


class DetailTransaksiProduk_model extends CI_Model
{
    private $TABLE = 'detail_transaksi_produk';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get($noTransaksi) {
        if (!$noTransaksi)
            return null;

        return $this->db->get_where($this->TABLE, ['no_transaksi' => $noTransaksi])->result_array();
    }

    public function update($noTransaksi, $produkID, $data) {
        $this->db->where(['no_transaksi' => $noTransaksi, 'produk_id' => $produkID]);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function delete($noTransaksi) {
        $this->db->delete($this->TABLE, ['no_transaksi' => $noTransaksi]);
        return $this->db->affected_rows();
    }

    public function deleteProduk($noTransaksi, $produkID) {
        $this->db->delete($this->TABLE, ['no_transaksi' => $noTransaksi, 'produk_id' => $produkID]);
        return $this->db->affected_rows();
    }

    public function checkProdukID($produkID, $noTransaksi) {
        return $this->db->get_where($this->TABLE, ['produk_id' => $produkID , 'no_transaksi' => $noTransaksi])->result_array();
    }
}

?>