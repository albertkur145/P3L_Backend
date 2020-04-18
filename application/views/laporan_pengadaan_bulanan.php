<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laporan Pengadaan</title>
    <style>
        #app {
            padding: 1rem 1.25rem;
            border: 1px solid #444;
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        }

        #app .top {
            display: flex;
        }

        #app .top .left {
            text-align: left;
        }

        #app .top .left img {
            width: 19.5rem;
        }

        #app .top .right {
            text-align: right;
        }

        #app .top .right h2 {
            font-size: 1.625rem;
        }

        #app hr {
            border-bottom: 1px solid #000;
            margin-top: -10rem;
        }

        #app .content {
            margin-bottom: 1.5rem;
        }

        #app .content .title {
            text-align: center;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }

        #app .laporan table {
            border-collapse: collapse;
            width: 100%;
        }

        #app .laporan table td,
        #app .laporan table th {
            border: 1px solid #222;
            padding: 0.75rem 1rem;
        }

        #app .laporan .total {
            text-align: right;
            font-size: 1.125rem;
            font-weight: 600;
        }

        #app .laporan .total span {
            margin-left: 0.375rem;
        }

        #app .printed-out {
            margin-top: 4rem;
            margin-bottom: 0;
            text-align: right;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="top">
            <div class="left">
                <img src="<?= $_SERVER['DOCUMENT_ROOT'] . '/kouvee/assets/img/logo.png' ?>">
            </div>
            
            <div class="right">
                <h2>Kouvee Pet Shop</h2>
                <p>Jl. Moses Gatotkaca No. 22 Yogyakarta 55281</p>
                <p>Telp. (0274) 357735</p>
                <p>http://www.sayanghewan.com</p>
            </div>
        </div>

        <hr>

        <div class="content">
            <h3 class="title">LAPORAN PENGADAAN PRODUK BULANAN</h3>
            <p>Bulan : <?= $bulan ?></p>
            <p>Tahun : <?= $tahun ?></p>
        </div>

        <div class="laporan">
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Jumlah Pengeluaran</th>
                </tr>

                <?php if($pengadaan_produk != 0) { ?>
                    <?php foreach ($pengadaan_produk['rincian'] as $index => $value) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td style="text-align: left;"><?= $value['nama_produk'] ?></td>
                            <td>Rp. <?= number_format($value['pengeluaran'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="3" style="text-align: center;">Tidak ada pengadaan produk di bulan ini.</td></tr>
                <?php } ?>
            </table>

            <?php if($pengadaan_produk != 0) { ?>
                <p class="total">Total <span>Rp. <?= number_format($pengadaan_produk['total'], 0, ',', '.'); ?></span></p>
            <?php } ?>
        </div>

        <p class="printed-out">Dicetak tanggal <?= $printed; ?></p>
    </div>
</body>
</html>