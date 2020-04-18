<?php


class TransaksiProduk_model extends CI_Model
{
    private $TABLE = 'transaksi_produk';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function getAll() {
        return $this->db->get($this->TABLE)->result_array();
    }

    public function getAllUncomplete($noTransaksi) {
        if ($noTransaksi)
            return $this->db->like('no_transaksi', $noTransaksi)->get_where($this->TABLE, ['status' => 'Tidak selesai'])->result_array();

        return $this->db->get_where($this->TABLE, ['status' => 'Tidak selesai'])->result_array();
    }

    public function getAllComplete($noTransaksi) {
        if ($noTransaksi)
            return $this->db->like('no_transaksi', $noTransaksi)->get_where($this->TABLE, ['status' => 'Selesai'])->result_array();

        return $this->db->get_where($this->TABLE, ['status' => 'Selesai'])->result_array();
    }

    public function getAllCompleteOrCanceled($noTransaksi) {
        $this->db->where('status !=', 'Tidak selesai');
        if ($noTransaksi)
            return $this->db->like('no_transaksi', $noTransaksi)->get($this->TABLE)->result_array();

        return $this->db->get($this->TABLE)->result_array();
    }

    public function getByNoTransaction($noTransaksi) {
        /* select data yang diperlukan
        t -> transaksi, dt -> detail_transaksi, c -> customer, pgw_c -> pegawai di customer
        p -> produk, pgw_t -> pegawai di transaksi, cs -> pegawai (CS) di transaksi */
        $this->db->select('
        t.id id_t,
        t.no_transaksi,
        t.tanggal tanggal_t,
        t.customer_id customer_id_t,
        t.cs_id cs_id_t,
        t.kasir_id kasir_id_t, 
        t.created_at created_at_t,
        t.updated_at updated_at_t,
        t.deleted_at deleted_at_t, 
        t.pegawai_id pegawai_id_t, 
        t.status status_t, 
        dt.produk_id produk_id_dt,
        dt.jumlah jumlah_dt,
        c.id id_c,
        c.nama nama_c,
        c.alamat alamat_c,
        c.tanggal_lahir tanggal_lahir_c,
        c.no_hp no_hp_c,
        c.is_member is_member_c, 
        c.created_at created_at_c, 
        c.updated_at updated_at_c, 
        c.deleted_at deleted_at_c, 
        c.pegawai_id pegawai_c,
        pgw_c.id id_pegawai_customer,
        pgw_c.nama nama_pegawai_customer,
        p.id id_p,
        p.nama nama_p,
        p.harga harga_p,
        pgw_t.id id_pgw_t,
        pgw_t.nama nama_pgw_t,
        cs.id id_cs,
        cs.nama nama_cs');

        /* dari table apa dan join kemana
        (pgw_t => pegawai di table transaksi)
        (pgw_c => pegawai di table customer) */
        $this->db->from($this->TABLE . ' t');
        $this->db->join('detail_transaksi_produk dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('customer c', 'c.id = t.customer_id');
        $this->db->join('produk p', 'p.id = dt.produk_id');
        $this->db->join('pegawai pgw_t', 'pgw_t.id = t.pegawai_id');
        $this->db->join('pegawai cs', 'cs.id = t.cs_id');
        $this->db->join('pegawai pgw_c', 'pgw_c.id = c.pegawai_id');
        $this->db->where('t.no_transaksi', $noTransaksi);

        // get data dan tampung di variabel
        $data = $this->db->get()->result_array();

        if (!$data)
            return null;

        // 1 transaksi, bisa ada banyak produk/layanan (one to many), maka butuh looping
        $produk = [];
        $subtotal = 0;
        foreach ($data as $value) {
            // tampung data produk di variabel temp
            $temp = [
                'id' => $value['produk_id_dt'],
                'nama' => $value['nama_p'],
                'harga' => $value['harga_p'],
                'jumlah_beli' => $value['jumlah_dt']
            ];

            // ambil data bayar (subtotal)
            $subtotal += ($value['harga_p'] * $value['jumlah_dt']);
            array_push($produk, $temp);
        }

        /* get pegawai terakhir yang melakukan aksi */
        if ($value['pegawai_id_t']) {
            $last_pegawai = $this->db->get_where('pegawai', ['id' => $value['pegawai_id_t']])->result_array();
            $last_pegawai = $last_pegawai[0]['nama'];
        }

        /* cek kasir_id, karena jika blm bayar maka kasir id masih null
        jika sudah bayar, get nama kasir tsb */
        if ($data[0]['kasir_id_t']) {
            $kasir_name = $this->db->get_where('pegawai', ['id' => $data[0]['kasir_id_t']])->result_array();
            $kasir_name = $kasir_name[0]['nama'];
        } else
            $kasir_name = null;


        // cek apakah member atau bukan untuk set diskon
        $diskon = 0;
        if ($data[0]['is_member_c'] == 1)
            $diskon = 0.15;

        // hitung total bayar
        $totalBayar = ceil($subtotal - ($subtotal * $diskon));

        // susun keluaran response
        return $response = [
            [
                'id' => $data[0]['id_t'],
                'no_transaksi' => $data[0]['no_transaksi'],
                'tanggal' => $data[0]['tanggal_t'],
                'created_at' => $data[0]['created_at_t'],
                'updated_at' => $data[0]['updated_at_t'],
                'deleted_at' => $data[0]['deleted_at_t'],
                'status' => $data[0]['status_t'],
                'cs' => $data[0]['nama_cs'],
                'kasir' => $kasir_name,
                'last_action_by' => $last_pegawai,
                'produk' => $produk,
                'pembayaran' => [
                    'sub_total' => $subtotal,
                    'diskon' => $subtotal * $diskon,
                    'total' => $totalBayar
                ],
                'customer' => [
                    'id' => $data[0]['id_c'],
                    'nama' => $data[0]['nama_c'],
                    'alamat' => $data[0]['alamat_c'],
                    'tanggal_lahir' => $data[0]['tanggal_lahir_c'],
                    'no_hp' => $data[0]['no_hp_c'],
                    'is_member' => $data[0]['is_member_c'],
                    'created_at' => $data[0]['created_at_c'],
                    'updated_at_c' => $data[0]['updated_at_c'],
                    'deleted_at' => $data[0]['deleted_at_c'],
                    'pegawai' => $data[0]['nama_pegawai_customer'],
                ]
            ]
        ];
    }

    public function updateTransaction($noTransaksi, $data) {
        $this->db->where(['no_transaksi' => $noTransaksi, 'deleted_at' => NULL, 'status' => 'Tidak selesai']);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function cancelTransaction($noTransaksi, $data) {
        $this->db->where(['no_transaksi' => $noTransaksi, 'deleted_at' => NULL, 'status' => 'Tidak selesai']);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function updatePaymentSuccess($noTransaksi, $data) {
        $this->db->where(['no_transaksi' => $noTransaksi, 'deleted_at' => NULL, 'status' => 'Tidak selesai']);
        $this->db->update($this->TABLE, $data);

        return $this->db->affected_rows();
    }

    public function checkTransaksiIsExist($noTransaksi) {
        if ($noTransaksi) {
            return $this->db->get_where($this->TABLE, 
            [
                'no_transaksi' => $noTransaksi,
                'deleted_at' => null,
                'status' => 'Tidak selesai'
            ])->result_array();
        }

        return null;
    }

    public function getLastID() {
        $last = $this->db->select_max('id')->get($this->TABLE)->result_array();

        if ($last[0]['id'] == null)
            $last = 0;
        else
            $last = $last[0]['id'];

        return $last;
    }
}

?>