<?php


class JenisHewan_model extends CI_Model
{
    private $TABLE = 'jenis_hewan';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get($id) {
        if ($id) 
            return $this->db->get_where($this->TABLE, ['deleted_at' => null, 'id' => $id])->result_array();

        return $this->db->order_by('nama', 'ASC')->get_where($this->TABLE, ['deleted_at' => null])->result_array();
    }

    public function getJenis($id) {
        if ($id) 
            return $this->db->get_where($this->TABLE, ['id' => $id])->result_array();

        return 0;
    }

    public function getLog($nama) {
        if ($nama)
            return $this->db->order_by('nama', 'ASC')->like('nama', $nama)->get($this->TABLE)->result_array();

        return $this->db->order_by('nama', 'ASC')->get($this->TABLE)->result_array();
    }

    public function getPaging($page) {
        $start = ($page * 10) - 9;
        $data = $this->db->order_by('nama', 'ASC')->get_where($this->TABLE, ['deleted_at' => null])->result_array();

        return array_slice($data, $start - 1, 10);
    }

    public function countData() {
        return $this->db->where('deleted_at', null)->count_all_results($this->TABLE);
    }

    public function getByNama($nama) {
        if ($nama)
            return $this->db->order_by('nama', 'ASC')->like('nama', $nama)->get_where($this->TABLE, ['deleted_at' => null])->result_array();
        
        return null;
    }

    public function update($data, $id) {
        $this->db->where(['id' => $id, 'deleted_at' => NULL]);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function delete($id) {
        $this->db->set('deleted_at', date("Y-m-d H:i:s"));
        $this->db->where(['id' => $id, 'deleted_at' => null]);
        $this->db->update($this->TABLE);

        return $this->db->affected_rows();
    }

}


?>