<?php


class Fcm_model extends CI_Model
{
    private $TABLE = 'fcm_info';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get() {
        return $this->db->get($this->TABLE)->result_array();
    }

}