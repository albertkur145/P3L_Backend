<?php


class Pemesanan_model extends CI_Model
{
    private $TABLE = 'pemesanan';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function getAll($nomorPO) {
        if ($nomorPO)
        	return $this->db->order_by('nomor_po', 'ASC')->like('nomor_po', $nomorPO)->get_where($this->TABLE, ['deleted_at' => NULL, 'status !=' => 'Selesai'])->result_array();

       return $this->db->order_by('nomor_po', 'ASC')->get_where($this->TABLE, ['deleted_at' => NULL, 'status !=' => 'Selesai'])->result_array();
    }

    public function getLog($nomorPO) {
        if ($nomorPO)
            return $this->db->order_by('nomor_po', 'ASC')->like('nomor_po', $nomorPO)->get($this->TABLE)->result_array();

        return $this->db->order_by('nomor_po', 'ASC')->get($this->TABLE)->result_array();
    }

    public function getByNomorPO($nomorPO) {
        /* select data yang diperlukan */
        $this->db->select('
            p.id id_p,
            p.nomor_po nomor_po_p,
            p.tanggal_pesan tanggal_pesan_p,
            p.tanggal_masuk tanggal_masuk_p,
            p.supplier_id supplier_id_p,
            p.created_at created_at_p,
            p.updated_at updated_at_p,
            p.deleted_at deleted_at_p,
            p.status status_p,
            dp.produk_id produk_id_dp,
            dp.satuan satuan_dp,
            dp.jumlah jumlah_dp,
            prod.nama nama_prod,
            s.id id_s,
            s.nama nama_s,
            s.alamat alamat_s,
            s.kota kota_s,
            s.no_hp no_hp_s
        ');

        /* dari table apa dan join kemana */
        $this->db->from($this->TABLE . ' p');
        $this->db->join('detail_pemesanan dp', 'p.nomor_po = dp.nomor_po');
        $this->db->join('produk prod', 'prod.id = dp.produk_id');
        $this->db->join('supplier s', 's.id = p.supplier_id');
        $this->db->where('p.nomor_po', $nomorPO);

        // get data dan tampung di variabel
        $data = $this->db->get()->result_array();

        if (!$data)
            return null;

        // 1 pemesanan, bisa ada banyak produk (one to many), maka butuh looping
        $produk = [];
        foreach ($data as $value) {
            // tampung data produk di variabel temp
            $temp = [
                'produk_id' => $value['produk_id_dp'],
                'nama' => $value['nama_prod'],
                'satuan' => $value['satuan_dp'],
                'jumlah' => $value['jumlah_dp']
            ];

            array_push($produk, $temp);
        }

        // susun keluaran response
        return $response = [
            [
                'id' => $data[0]['id_p'],
                'nomor_po' => $data[0]['nomor_po_p'],
                'tanggal_pesan' => $data[0]['tanggal_pesan_p'],
                'tanggal_masuk' => $data[0]['tanggal_masuk_p'],
                'created_at' => $data[0]['created_at_p'],
                'updated_at' => $data[0]['updated_at_p'],
                'deleted_at' => $data[0]['deleted_at_p'],
                'status' => $data[0]['status_p'],
                'supplier' => [
                    'id' => $data[0]['id_s'],
                    'nama' => $data[0]['nama_s'],
                    'alamat' => $data[0]['alamat_s'],
                    'kota' => $data[0]['kota_s'],
                    'no_hp' => $data[0]['no_hp_s'],
                ],
                'detail_pemesanan' => $produk,
            ]
        ];
    }

    public function checkPemesananIsExist($nomorPO) {
        if ($nomorPO) {
            return $this->db->get_where($this->TABLE, 
            [
                'nomor_po' => $nomorPO,
                'deleted_at' => null
            ])->result_array();
        }

        return null;
    }

    public function updatePemesanan($nomorPO, $data) {
        $this->db->where(['nomor_po' => $nomorPO, 'deleted_at' => NULL, 'status' => 'Dipesan']);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function pemesananComplete($nomorPO, $data) {
        $this->db->where(['nomor_po' => $nomorPO, 'deleted_at' => NULL, 'status' => 'Dicetak']);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function getLastID() {
        $last = $this->db->select_max('id')->get($this->TABLE)->result_array();

        if ($last[0]['id'] == null)
            $last = 0;
        else
            $last = $last[0]['id'];

        return $last;
    }

    public function getPaging($page) {
        $start = ($page * 10) - 9;
        $data = $this->db->order_by('nomor_po', 'ASC')->get_where($this->TABLE, ['deleted_at' => null, 'status !=' => 'Selesai'])->result_array();

        return array_slice($data, $start - 1, 10);
    }

    public function countData() {
        return $this->db->where('deleted_at', null)->count_all_results($this->TABLE);
    }

}

?>