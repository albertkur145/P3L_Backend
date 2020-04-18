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
            padding: 0.625rem 1rem;
            font-size: 0.9375rem;
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
            <h3 class="title">LAPORAN PENGADAAN PRODUK TAHUNAN</h3>
            <p>Tahun : <?= $tahun ?></p>
        </div>

        <div class="laporan">
            <table>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Jumlah Pengeluaran</th>
                </tr>

                <?php if ($pengadaan_produk != 0) { ?>
                    <tr>
                        <td>1</td>
                        <td>Januari</td>
                        <td>Rp. <?= number_format($pengadaan_produk['01']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>Februari</td>
                        <td>Rp. <?= number_format($pengadaan_produk['02']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>Maret</td>
                        <td>Rp. <?= number_format($pengadaan_produk['03']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td>April</td>
                        <td>Rp. <?= number_format($pengadaan_produk['04']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td>Mei</td>
                        <td>Rp. <?= number_format($pengadaan_produk['05']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td>Juni</td>
                        <td>Rp. <?= number_format($pengadaan_produk['06']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>7</td>
                        <td>Juli</td>
                        <td>Rp. <?= number_format($pengadaan_produk['07']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td>Agustus</td>
                        <td>Rp. <?= number_format($pengadaan_produk['08']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>9</td>
                        <td>September</td>
                        <td>Rp. <?= number_format($pengadaan_produk['09']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>10</td>
                        <td>Oktober</td>
                        <td>Rp. <?= number_format($pengadaan_produk['10']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>11</td>
                        <td>November</td>
                        <td>Rp. <?= number_format($pengadaan_produk['11']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>

                    <tr>
                        <td>12</td>
                        <td>Desember</td>
                        <td>Rp. <?= number_format($pengadaan_produk['12']['pengeluaran'], 0, ',', '.'); ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td colspan=3 style="text-align: center;">Tidak ada pengadaan produk di tahun ini.</td>
                    </tr>
                <?php } ?>
            </table>

            <?php if ($pengadaan_produk != 0) { ?>
                <p class="total">Total <span>Rp. <?= number_format($total_pengeluaran, 0, ',', '.'); ?></span></p>
            <?php } ?>
        </div>

        <p class="printed-out">Dicetak tanggal <?= $printed; ?></p>
    </div>
</body>
</html>