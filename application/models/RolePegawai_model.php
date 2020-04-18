<?php


class RolePegawai_model extends CI_Model
{
    private $TABLE = 'role_pegawai';

    public function get($id) {
    	if ($id)
    		return $this->db->get_where($this->TABLE, ['id' => $id])->result_array();

        return $this->db->get($this->TABLE)->result_array();
    }

}

?>