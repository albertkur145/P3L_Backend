<?php


class DetailTransaksiLayanan_model extends CI_Model
{
    private $TABLE = 'detail_transaksi_layanan';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get($noTransaksi) {
        if (!$noTransaksi)
            return null;

        return $this->db->get_where($this->TABLE, ['no_transaksi' => $noTransaksi])->result_array();
    }

    public function delete($noTransaksi) {
        $this->db->delete($this->TABLE, ['no_transaksi' => $noTransaksi]);
        return $this->db->affected_rows();
    }
}

?>