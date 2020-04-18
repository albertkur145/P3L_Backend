<?php


class Laporan_model extends CI_Model
{
    public function pendapatanBulananProduk($bulan, $tahun) {
        $this->db->select('
            t.no_transaksi,
            t.tanggal,
            dt.produk_id produk_id_dt,
            dt.jumlah jumlah_dt,
            p.id id_p,
            p.nama nama_p,
            p.harga harga_p'
        );

        $this->db->from('transaksi_produk t');
        $this->db->join('detail_transaksi_produk dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('produk p', 'p.id = dt.produk_id');
        $this->db->where([
            't.status' => 'Selesai',
            't.deleted_at' => null,
            'MONTH(t.tanggal)' => $bulan,
            'YEAR(t.tanggal)' => $tahun
        ]);

        $transaksi = $this->db->get()->result_array();
        $products = [['id' => '-',
                    'nama' => '-',
                    'harga' => '-',
                    'jumlah_beli' => '-']];

        if (!$transaksi)
            return 0;

        foreach ($transaksi as $value) {
            if($key = array_search($value['id_p'], array_column($products, 'id'), true))
                $products[$key]['jumlah_beli'] += $value['jumlah_dt'];
            else {
                array_push($products, [
                    'id' => $value['id_p'],
                    'nama' => $value['nama_p'],
                    'harga' => $value['harga_p'],
                    'jumlah_beli' => $value['jumlah_dt']
                ]);
            }
        }

        $results = [];
        $total = 0;
        foreach ($products as $index => $value) {
            if ($index != 0) {
                array_push($results, [
                    'nama_produk' => $value['nama'],
                    'pendapatan' => $value['harga'] * $value['jumlah_beli']
                ]);
                $total += $value['harga'] * $value['jumlah_beli'];
            }
        }

        return [
            'total' => $total,
            'rincian' => $results
        ];
    }

    public function pendapatanBulananLayanan($bulan, $tahun) {
        $this->db->select('
            t.no_transaksi,
            t.tanggal,
            dt.layanan_id layanan_id_dt,
            l.id id_l,
            l.nama nama_l,
            l.harga harga_l'
        );

        $this->db->from('transaksi_layanan t');
        $this->db->join('detail_transaksi_layanan dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('layanan l', 'l.id = dt.layanan_id');
        $this->db->where([
            't.status' => 'Selesai',
            't.deleted_at' => null,
            'MONTH(t.tanggal)' => $bulan,
            'YEAR(t.tanggal)' => $tahun
        ]);

        $transaksi = $this->db->get()->result_array();
        $layanan = [['id' => '-',
                    'nama' => '-',
                    'harga' => '-',
                    'jumlah_beli' => '-']];

        if (!$transaksi)
            return 0;

        foreach ($transaksi as $value) {
            if($key = array_search($value['id_l'], array_column($layanan, 'id'), true))
                $layanan[$key]['jumlah_beli'] += 1;
            else {
                array_push($layanan, [
                    'id' => $value['id_l'],
                    'nama' => $value['nama_l'],
                    'harga' => $value['harga_l'],
                    'jumlah_beli' => 1
                ]);
            }
        }

        $results = [];
        $total = 0;
        foreach ($layanan as $key => $value) {
            if ($key != 0) {
                array_push($results, [
                    'nama_layanan' => $value['nama'],
                    'pendapatan' => $value['harga'] * $value['jumlah_beli']
                ]);
                $total += $value['harga'] * $value['jumlah_beli'];
            }
        }

        return [
            'total' => $total,
            'rincian' => $results
        ];
    }

    public function pendapatanTahunanProduk($tahun) {
        $this->db->select('
            t.no_transaksi,
            t.tanggal,
            dt.produk_id produk_id_dt,
            dt.jumlah jumlah_dt,
            p.id id_p,
            p.harga harga_p'
        );

        $this->db->from('transaksi_produk t');
        $this->db->join('detail_transaksi_produk dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('produk p', 'p.id = dt.produk_id');
        $this->db->where([
            't.status' => 'Selesai',
            't.deleted_at' => null,
            'YEAR(t.tanggal)' => $tahun
        ]);

        $transaksi = $this->db->get()->result_array();
        $products = [['bulan' => '-', 'pendapatan' => '-']];

        if (!$transaksi) {
            $temp = [];
            for ($i = 1; $i <= 12; $i++) { 
                if ($i < 10) {
                    $temp['0' . $i] = [
                        'bulan' => '0' . $i,
                        'pendapatan' => 0
                    ];
                } else {
                    $temp[$i] = [
                        'bulan' => $i,
                        'pendapatan' => 0
                    ];
                }
            }

            return $temp;
        }

        foreach ($transaksi as $value) {
            $bln = date_format(new DateTime($value['tanggal']), 'm');

            if($key = array_search($bln, array_column($products, 'bulan'), true))
                $products[$key]['pendapatan'] += $value['harga_p'] * $value['jumlah_dt'];
            else {
                array_push($products, [
                    'bulan' => $bln,
                    'pendapatan' => $value['harga_p'] * $value['jumlah_dt']
                ]);
            }
        }

        $results = [];
        for ($i = 1; $i <= 12; $i++) { 
            if ($key = array_search($i, array_column($products, 'bulan'))) {
                $results[$products[$key]['bulan']] = [
                    'bulan' => $products[$key]['bulan'],
                    'pendapatan' => $products[$key]['pendapatan']
                ];
            } else {
                if ($i < 10) {
                    $results['0' . $i] = [
                        'bulan' => '0' . $i,
                        'pendapatan' => 0
                    ];
                } else {
                    $results[$i] = [
                        'bulan' => $i,
                        'pendapatan' => 0
                    ];
                }
            }
        }

        return $results;
    }

    public function pendapatanTahunanLayanan($tahun) {
        $this->db->select('
            t.no_transaksi,
            t.tanggal,
            dt.layanan_id layanan_id_dt,
            l.id id_l,
            l.nama nama_l,
            l.harga harga_l'
        );

        $this->db->from('transaksi_layanan t');
        $this->db->join('detail_transaksi_layanan dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('layanan l', 'l.id = dt.layanan_id');
        $this->db->where([
            't.status' => 'Selesai',
            't.deleted_at' => null,
            'YEAR(t.tanggal)' => $tahun
        ]);

        $transaksi = $this->db->get()->result_array();
        $layanan = [['bulan' => '-', 'pendapatan' => '-']];

        if (!$transaksi) {
            $temp = [];
            for ($i = 1; $i <= 12; $i++) { 
                if ($i < 10) {
                    $temp['0' . $i] = [
                        'bulan' => '0' . $i,
                        'pendapatan' => 0
                    ];
                } else {
                    $temp[$i] = [
                        'bulan' => $i,
                        'pendapatan' => 0
                    ];
                }
            }

            return $temp;
        }

        foreach ($transaksi as $value) {
            $bln = date_format(new DateTime($value['tanggal']), 'm');

            if($key = array_search($bln, array_column($layanan, 'bulan'), true))
                $layanan[$key]['pendapatan'] += $value['harga_l'];
            else {
                array_push($layanan, [
                    'bulan' => $bln,
                    'pendapatan' => $value['harga_l']
                ]);
            }
        }

        $results = [];
        for ($i = 1; $i <= 12; $i++) { 
            if ($key = array_search($i, array_column($layanan, 'bulan'))) {
                $results[$layanan[$key]['bulan']] = [
                    'bulan' => $layanan[$key]['bulan'],
                    'pendapatan' => $layanan[$key]['pendapatan']
                ];
            } else {
                if ($i < 10) {
                    $results['0' . $i] = [
                        'bulan' => '0' . $i,
                        'pendapatan' => 0
                    ];
                } else {
                    $results[$i] = [
                        'bulan' => $i,
                        'pendapatan' => 0
                    ];
                }
            }
        }

        return $results;
    }

    public function pengadaanBulananProduk($bulan, $tahun) {
        $this->db->select('
            p.nomor_po,
            p.tanggal_pesan,
            dp.produk_id,
            dp.jumlah,
            prod.id id_p,
            prod.nama nama_p,
            prod.harga harga_p
        ');

        $this->db->from('pemesanan p');
        $this->db->join('detail_pemesanan dp', 'p.nomor_po = dp.nomor_po');
        $this->db->join('produk prod', 'prod.id = dp.produk_id');
        $this->db->where([
            'p.status' => 'Selesai',
            'p.deleted_at' => null,
            'MONTH(p.tanggal_pesan)' => $bulan,
            'YEAR(p.tanggal_pesan)' => $tahun
        ]);

        $data = $this->db->get()->result_array();
        $products = [['id' => '-',
                    'nama' => '-',
                    'harga' => '-',
                    'jumlah_beli' => '-']];

        if (!$data)
            return 0;

        foreach ($data as $value) {
            if($key = array_search($value['id_p'], array_column($products, 'id'), true))
                $products[$key]['jumlah_pesan'] += $value['jumlah'];
            else {
                array_push($products, [
                    'id' => $value['id_p'],
                    'nama' => $value['nama_p'],
                    'harga' => $value['harga_p'],
                    'jumlah_pesan' => $value['jumlah']
                ]);
            }
        }

        $results = [];
        $total = 0;
        foreach ($products as $index => $value) {
            if ($index != 0) {
                array_push($results, [
                    'nama_produk' => $value['nama'],
                    'pengeluaran' => $value['harga'] * $value['jumlah_pesan']
                ]);
                $total += $value['harga'] * $value['jumlah_pesan'];
            }
        }

        return [
            'total' => $total,
            'rincian' => $results
        ];
    }

    public function pengadaanTahunanProduk($tahun) {
        $this->db->select('
            p.nomor_po,
            p.tanggal_pesan,
            dp.produk_id,
            dp.jumlah,
            prod.id id_p,
            prod.nama nama_p,
            prod.harga harga_p
        ');

        $this->db->from('pemesanan p');
        $this->db->join('detail_pemesanan dp', 'p.nomor_po = dp.nomor_po');
        $this->db->join('produk prod', 'prod.id = dp.produk_id');
        $this->db->where([
            'p.status' => 'Selesai',
            'p.deleted_at' => null,
            'YEAR(p.tanggal_pesan)' => $tahun
        ]);

        $data = $this->db->get()->result_array();
        $products = [['bulan' => '-', 'pengeluaran' => '-']];

        if (!$data)
            return 0;

        foreach ($data as $value) {
            $bln = date_format(new DateTime($value['tanggal_pesan']), 'm');

            if($key = array_search($bln, array_column($products, 'bulan'), true))
                $products[$key]['pengeluaran'] += $value['harga_p'] * $value['jumlah'];
            else {
                array_push($products, [
                    'bulan' => $bln,
                    'pengeluaran' => $value['harga_p'] * $value['jumlah']
                ]);
            }
        }

        $results = [];
        for ($i = 1; $i <= 12; $i++) { 
            if ($key = array_search($i, array_column($products, 'bulan'))) {
                $results[$products[$key]['bulan']] = [
                    'bulan' => $products[$key]['bulan'],
                    'pengeluaran' => $products[$key]['pengeluaran']
                ];
            } else {
                if ($i < 10) {
                    $results['0' . $i] = [
                        'bulan' => '0' . $i,
                        'pengeluaran' => 0
                    ];
                } else {
                    $results[$i] = [
                        'bulan' => $i,
                        'pengeluaran' => 0
                    ];
                }
            }
        }

        return $results;
    }

    public function produkTerlarisBulanan($bulan, $tahun) {
        $this->db->select('
            t.no_transaksi,
            t.tanggal,
            dt.produk_id produk_id_dt,
            dt.jumlah jumlah_dt,
            p.id id_p,
            p.nama nama_p'
        );

        $this->db->from('transaksi_produk t');
        $this->db->join('detail_transaksi_produk dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('produk p', 'p.id = dt.produk_id');
        $this->db->where([
            't.status' => 'Selesai',
            't.deleted_at' => null,
            'MONTH(t.tanggal)' => $bulan,
            'YEAR(t.tanggal)' => $tahun
        ]);

        $transaksi = $this->db->get()->result_array();
        $products = [['bulan' => '-', 'id' => '-', 'nama' => '-', 'jumlah_penjualan' => '-']];

        if (!$transaksi) {
            $temp = ['bulan' => $bulan, 'id' => '-', 'nama' => '-', 'jumlah_penjualan' => 0];
            return $temp;
        }

        foreach ($transaksi as $value) {
            if($key = array_search($value['id_p'], array_column($products, 'id'), true))
                $products[$key]['jumlah_penjualan'] += $value['jumlah_dt'];
            else {
                array_push($products, [
                    'bulan' => $bulan,
                    'id' => $value['id_p'],
                    'nama' => $value['nama_p'],
                    'jumlah_penjualan' => $value['jumlah_dt']
                ]);
            }
        }

        $jmlhPenjualan = array_column($products, 'jumlah_penjualan');
        $index = array_search(max($jmlhPenjualan), $jmlhPenjualan);

        return $products[$index];
    }

    public function layananTerlarisBulanan($bulan, $tahun) {
        $this->db->select('
            t.no_transaksi,
            t.tanggal,
            dt.layanan_id layanan_id_dt,
            l.id id_l,
            l.nama nama_l'
        );

        $this->db->from('transaksi_layanan t');
        $this->db->join('detail_transaksi_layanan dt', 't.no_transaksi = dt.no_transaksi');
        $this->db->join('layanan l', 'l.id = dt.layanan_id');
        $this->db->where([
            't.status' => 'Selesai',
            't.deleted_at' => null,
            'MONTH(t.tanggal)' => $bulan,
            'YEAR(t.tanggal)' => $tahun
        ]);

        $transaksi = $this->db->get()->result_array();
        $layanan = [['bulan' => '-', 'id' => '-', 'nama' => '-', 'jumlah_penjualan' => '-']];

        if (!$transaksi) {
            $temp = ['bulan' => $bulan, 'id' => '-', 'nama' => '-', 'jumlah_penjualan' => 0];
            return $temp;
        }

        foreach ($transaksi as $value) {
            if($key = array_search($value['id_l'], array_column($layanan, 'id'), true))
                $layanan[$key]['jumlah_penjualan'] += 1;
            else {
                array_push($layanan, [
                    'bulan' => $bulan,
                    'id' => $value['id_l'],
                    'nama' => $value['nama_l'],
                    'jumlah_penjualan' => 1
                ]);
            }
        }


        $jmlhPenjualan = array_column($layanan, 'jumlah_penjualan');
        $index = array_search(max($jmlhPenjualan), $jmlhPenjualan);

        return $layanan[$index];
    }

}
