<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laporan Layanan Terlaris</title>
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
            <h3 class="title">LAPORAN LAYANAN TERLARIS</h3>
            <p>Tahun : <?= $tahun ?></p>
        </div>

        <div class="laporan">
            <table>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Nama Layanan</th>
                    <th style="text-align: center;">Jumlah Penjualan</th>
                </tr>

                <tr>
                    <td>1</td>
                    <td>Januari</td>
                    <td><?= $layanan['01']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['01']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>Februari</td>
                    <td><?= $layanan['02']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['02']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>3</td>
                    <td>Maret</td>
                    <td><?= $layanan['03']['nama'];; ?></td>
                    <td style="text-align: center;"><?= $layanan['03']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>4</td>
                    <td>April</td>
                    <td><?= $layanan['04']['nama'];; ?></td>
                    <td style="text-align: center;"><?= $layanan['04']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>5</td>
                    <td>Mei</td>
                    <td><?= $layanan['05']['nama'];; ?></td>
                    <td style="text-align: center;"><?= $layanan['05']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>6</td>
                    <td>Juni</td>
                    <td><?= $layanan['06']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['06']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>7</td>
                    <td>Juli</td>
                    <td><?= $layanan['07']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['07']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>8</td>
                    <td>Agustus</td>
                    <td><?= $layanan['08']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['08']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>9</td>
                    <td>September</td>
                    <td><?= $layanan['09']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['09']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>10</td>
                    <td>Oktober</td>
                    <td><?= $layanan['10']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['10']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>11</td>
                    <td>November</td>
                    <td><?= $layanan['11']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['11']['jumlah_penjualan']; ?></td>
                </tr>

                <tr>
                    <td>12</td>
                    <td>Desember</td>
                    <td><?= $layanan['12']['nama']; ?></td>
                    <td style="text-align: center;"><?= $layanan['12']['jumlah_penjualan']; ?></td>
                </tr>
            </table>
        </div>

        <p class="printed-out">Dicetak tanggal <?= $printed; ?></p>
    </div>
</body>
</html>