<?php


class DetailPemesanan_model extends CI_Model
{
    private $TABLE = 'detail_pemesanan';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function delete($nomorPO) {
        $this->db->delete($this->TABLE, ['nomor_po' => $nomorPO]);
        return $this->db->affected_rows();
    }

    public function get($nomorPO) {
        if (!$nomorPO)
            return null;

        return $this->db->get_where($this->TABLE, ['nomor_po' => $nomorPO])->result_array();;
    }

}

?>