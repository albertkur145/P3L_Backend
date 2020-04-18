<?php


class Produk_model extends CI_Model
{
    private $TABLE = 'produk';

    public function save($data) {
        $this->db->insert($this->TABLE, $data);
        return $this->db->affected_rows();
    }

    public function get($id) {
        if ($id) 
            return $this->db->get_where($this->TABLE, ['deleted_at' => null, 'id' => $id])->result_array();

        return $this->db->order_by('nama', 'ASC')->get_where($this->TABLE, ['deleted_at' => null])->result_array();
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

    public function checkStockProduct($id, $jumlahBeli) {
        $produk = $this->db->get_where($this->TABLE, ['id' => $id, 'deleted_at' => null])->result_array();
        $produk = $produk[0];

        // cek stock akhir jika jadi beli, apakah minus atau tidak
        $stockAkhir = $produk['stock'] - $jumlahBeli;

        if ($stockAkhir >= 0)
            return true;

        return null;
    }

    public function reduceStock($id, $jumlahBeli) {
        // get produk
        $produk = $this->db->get_where($this->TABLE, ['id' => $id, 'deleted_at' => null])->result_array();
        $produk = $produk[0];

        // reduce stock
        $stockAkhir = $produk['stock'] - $jumlahBeli;

        $this->db->set('stock', $stockAkhir);
        $this->db->where(['id' => $id, 'deleted_at' => NULL]);
        $this->db->update($this->TABLE);

        return $this->db->affected_rows();
    }

    public function addStock($noTransaksi, $id) {
        // get produk
        $produk = $this->db->get_where($this->TABLE, ['id' => $id, 'deleted_at' => null])->result_array();
        $produk = $produk[0];

        // get jumlah beli produknya
        $detailTransaksi = $this->db
                            ->get_where('detail_transaksi_produk', ['no_transaksi' => $noTransaksi, 'produk_id' => $id])
                            ->result_array();
        $detailTransaksi = $detailTransaksi[0];

        // add stock
        $stockAkhir = $produk['stock'] + $detailTransaksi['jumlah'];

        $this->db->set('stock', $stockAkhir);
        $this->db->where(['id' => $id, 'deleted_at' => NULL]);
        $this->db->update($this->TABLE);

        return $this->db->affected_rows();
    }

    public function updateStock($noTransaksi, $produkID, $jumlahBeli) {
        // get produk
        $produk = $this->db->get_where($this->TABLE, ['id' => $produkID, 'deleted_at' => null])->result_array();
        $produk = $produk[0];

        /* cari selisih antara jumlah beli awal dan jumlah beli terbaru
        jika minus, maka stok produk++
        jika plus, maka stok produk-- */
        $detailTransaksi = $this->db
                            ->get_where('detail_transaksi_produk', ['no_transaksi' => $noTransaksi, 'produk_id' => $produkID])
                            ->result_array();
        $detailTransaksi = $detailTransaksi[0];

        if ($jumlahBeli > $detailTransaksi['jumlah']) 
            $stockAkhir = $produk['stock'] - ($jumlahBeli - $detailTransaksi['jumlah']);
        else
            $stockAkhir = $produk['stock'] + ($detailTransaksi['jumlah'] - $jumlahBeli);

        $this->db->set('stock', $stockAkhir);
        $this->db->where(['id' => $produkID, 'deleted_at' => NULL]);
        $this->db->update($this->TABLE);

        return $this->db->affected_rows();
    }

    public function checkDifferencePlus($noTransaksi, $produkID, $jumlahBeli) {
        // get produk
        $produk = $this->db->get_where($this->TABLE, ['id' => $produkID, 'deleted_at' => null])->result_array();
        $produk = $produk[0];

        $detailTransaksi = $this->db
                            ->get_where('detail_transaksi_produk', ['no_transaksi' => $noTransaksi, 'produk_id' => $produkID])
                            ->result_array();
        $detailTransaksi = $detailTransaksi[0];

        if ($jumlahBeli > $detailTransaksi['jumlah']) {
            $stockAkhir = $produk['stock'] - ($jumlahBeli - $detailTransaksi['jumlah']);

            // cek stock
            if ($stockAkhir >= 0)
                return true;

            return null;
        }

        return true;
    }

    public function addStockPemesanan($id, $jumlahPesan) {
        // get produk
        $produk = $this->db->get_where($this->TABLE, ['id' => $id, 'deleted_at' => null])->result_array();
        $produk = $produk[0];

        // add stock
        $stockAkhir = $produk['stock'] + $jumlahPesan;

        $this->db->set('stock', $stockAkhir);
        $this->db->where(['id' => $id, 'deleted_at' => NULL]);
        $this->db->update($this->TABLE);

        return $this->db->affected_rows();
    }

    public function getNotifikasi() {
        return $this->db->order_by('nama', 'ASC')->get_where($this->TABLE, ['deleted_at' => null, 'stock <=' => 10])->result_array();
    }

    public function sorting($stock, $harga) {
        if ($stock && $harga) {
            return $this->db->get_where($this->TABLE, 
                ['deleted_at' => null, 
                'stock >=' => $stock,
                'harga >=' => $harga])
            ->result_array();
        }

        return null;
    }

}


?>