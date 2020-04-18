<?php


class Pegawai_model extends CI_Model
{
    private $TABLE = 'pegawai';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get($id) {
        if ($id) 
            return $this->db->get_where($this->TABLE, ['deleted_at' => null, 'id' => $id])->result_array();

        return $this->db->order_by('nama', 'ASC')->get_where($this->TABLE, ['deleted_at' => null])->result_array();
    }

    public function getByIDLog($id) {
        if ($id)
            return $this->db->get_where($this->TABLE, ['id' => $id])->result_array();

        return null;
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

    public function getLog($nama) {
        if ($nama)
           	return $this->db->order_by('nama', 'ASC')->like('nama', $nama)->get($this->TABLE)->result_array();

         return $this->db->order_by('nama', 'ASC')->get($this->TABLE)->result_array();
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

    public function getUsername($username) {
        return $this->db->get_where($this->TABLE, ['username' => $username])->result_array();
    }

    public function validateLogin($username, $password) {
        $user = $this->db->get_where($this->TABLE, ['username' => $username, 'deleted_at' => null])->result_array();

        if (!$user)
            return 0;
        else if (!password_verify($password, $user[0]['password']))
            return 0;
        
        return $user;
    }

}


?>