<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cetak Nota Lunas</title>
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

        #app .nota {
            padding: 0 2.25rem;
        }

        #app .nota h2 {
            text-align: center;
            font-size: 1.3125rem;
        }

        #app .nota .info {
            font-size: 1.0625rem;
        }

        #app .nota .customer-pegawai {
            display: flex;
            font-size: 1.0625rem;
        }

        #app .nota .customer-pegawai .customer {
            text-align: left;
        }

        #app .nota .customer-pegawai .pegawai {
            text-align: right;
        }

        #app .nota .layanan {
            margin-top: -5.5rem;
        }

        #app .nota .layanan .title {
            font-size: 1.25rem;
            padding: 1rem;
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            text-align: center;
        }

        #app .nota .layanan table {
            border-collapse: collapse;
            width: 100%;
        }

        #app .nota .layanan table td,
        #app .nota .layanan table th {
            border: 1px solid #222;
            padding: 0.75rem 1rem;
            text-align: center;
        }

        #app .nota .layanan .pembayaran {
            margin-top: 0.5rem;
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

        <div class="nota">
            <h2>NOTA LUNAS</h2>

            <div class="info">
                <p style="text-align: right"><?= $tanggal . ' ' . $waktu; ?></p>
                <p style="text-align: left"><?= $transaksi['no_transaksi']; ?></p>
            </div>

            <div class="customer-pegawai">
                <div class="customer">
                    <p>Member  : <?= $transaksi['customer']['nama']; ?> (Hunter - Anjing)</p>
                    <p>Telepon : <?= $transaksi['customer']['no_hp']; ?></p>
                </div>

                <div class="pegawai">
                    <p>CS : <?= $transaksi['cs']; ?></p>
                    <p>Kasir : <?= $transaksi['kasir']; ?></p>
                </div>
            </div>

            <div class="layanan">
                <p class="title">Jasa Layanan</p>

                <table>
                    <tr>
                        <th>No</th>
                        <th>Nama Jasa</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>

                    <?php foreach ($transaksi['layanan'] as $index => $value) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td style="text-align: left;"><?= $value['nama'] ?></td>
                            <td>1</td>
                            <td>Rp. <?= number_format($value['harga'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php } ?>
                </table>

                <div class="pembayaran">
                    <p>Sub Total : Rp. <?= number_format($transaksi['pembayaran']['sub_total'], 0, ',', '.'); ?></p>
                    <p>Diskon : Rp. <?= number_format($transaksi['pembayaran']['diskon'], 0, ',', '.'); ?></p>
                    <p style="font-weight: 600;">TOTAL : Rp. <?= number_format($transaksi['pembayaran']['total'], 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>