<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cetak Pemesanan</title>
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

        #app .nota .date {
            text-align: right;
        }

        #app .nota .date h3 {
            font-size: 1rem;
            font-weight: 600;
        }

        #app .nota .supplier {
            display: inline-block;
            text-align: left;
            border: 0.0625rem dashed #000;
            padding: 0.75rem;
            margin-top: -1rem;
        }

        #app .nota .supplier p {
            margin: 0.5rem;
        }

        #app .nota .produk {
            margin-top: 0.5rem;
        }

        #app .nota .produk p {
            font-size: 1.125rem;
        }

        #app .nota .produk table {
            border-collapse: collapse;
            width: 100%;
        }

        #app .nota .produk table td,
        #app .nota .produk table th {
            border: 1px solid #222;
            padding: 0.75rem 1rem;
        }

        #app .nota .produk .printed-out {
            text-align: right;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
        }

        #app .nota .produk .printed-out p {
            font-size: 1rem;
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
            <h2>SURAT PEMESANAN</h2>

            <div class="date">
                <h3>No : <?= $pemesanan['nomor_po']; ?></h3>
                <h3>Tanggal : <?= $tanggal_pesan; ?></h3>
            </div>

            <div class="supplier">
                <p style="margin-top: 0;">Kepada Yth :</p>
                <p><?= $pemesanan['supplier']['nama']; ?></p>
                <p><?= $pemesanan['supplier']['kota']; ?></p>
                <p style="margin-bottom: 0;"><?= $pemesanan['supplier']['no_hp']; ?></p>
            </div>

            <div class="produk">
                <p>Mohon disediakan produk-produk berikut ini :</p>

                <table>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Satuan</th>
                        <th>Jumlah</th>
                    </tr>

                    <?php foreach ($pemesanan['detail_pemesanan'] as $index => $value) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $value['nama'] ?></td>
                            <td><?= $value['satuan'] ?></td>
                            <td><?= $value['jumlah'] ?></td>
                        </tr>
                    <?php } ?>
                </table>

                <div class="printed-out">
                    <p>Dicetak tanggal <?= $printed ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>