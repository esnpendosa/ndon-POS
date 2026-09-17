<?php
    session_start();
    include '../../setting.php';
    include '../../helper.php';
    if(!empty($_SESSION['codekop_session'])) {
        $uid =  (int)$_SESSION['codekop_session']['id'];
        $sql_users = "SELECT * FROM users WHERE id = ?";
        $row_users = $connectdb->prepare($sql_users);
        $row_users->execute(array($uid));
        $users = $row_users->fetch(PDO::FETCH_OBJ);
    } else {
        redirect($baseURL.'login.php');
    }

    $id =  getGet("no", true);
    $sql = "SELECT users.name, pelanggan.nama_pelanggan, penjualan.* 
                FROM penjualan 
                LEFT JOIN users 
                ON penjualan.id_member = users.id 
                LEFT JOIN pelanggan 
                ON penjualan.id_pelanggan=pelanggan.id
                WHERE penjualan.no_trx = ?";
    $row = $connectdb->prepare($sql);
    $row->execute(array($id));
    $edit = $row->fetch(PDO::FETCH_OBJ);
    if(empty($edit)) {
        redirect($baseURL.'index.php');
    }

    $sql_toko =  "SELECT * FROM toko WHERE id = 1";
    $row_toko = $connectdb->prepare($sql_toko);
    $row_toko->execute();
    $toko = $row_toko->fetch(PDO::FETCH_OBJ);

    // Ambil detail transaksi
    $sql_items = "SELECT * FROM penjualan_detail WHERE no_trx = ? ORDER BY id ASC";
    $row_items = $connectdb->prepare($sql_items);
    $row_items->execute(array($id));
    $hasil = $row_items->fetchAll(PDO::FETCH_OBJ);

    $diskon = 0;
    foreach($hasil as $r) {
        $diskon += (float)$r->diskon;
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Struk - <?= htmlspecialchars($edit->no_trx); ?></title>
    <style>
    html {
        font-family: monospace, 'Courier New', Courier, sans-serif;
        font-size: 8pt;
        line-height: 10pt !important;
        background: #fff;
    }
    body {
        margin: 0;
        padding: 5px;
        color: #000;
    }
    p {
        margin: 2px 0;
    }
    table {
        width: 100%;
        margin: 0;
        font-size: 8pt;
        line-height: 10pt;
        border-collapse: collapse;
    }
    tr td {
        padding-top: 2px;
        padding-bottom: 2px;
    }
    .right {
        text-align: right;
    }
    center {
        margin: 0;
    }
    .doted {
        border-bottom: 1px dashed #333;
        width: 100%;
        margin-top: 3px;
        margin-bottom: 3px;
    }
    @media print {
        body {
            padding: 0;
            margin: 0;
        }
    }
    </style>
</head>
<body onload="window.print()">
    <section>
        <center>
            <b style="font-size: 10pt;"><?= htmlspecialchars($toko->nama_toko);?></b><br>
            <?= htmlspecialchars($toko->alamat_toko);?><br>
            Telp. <?= htmlspecialchars($toko->tlp);?>
        </center>
        <div class="doted"></div>
        <table>
            <tr>
                <td style="width: 25%;">TRX</td>
                <td style="width: 5%;">:</td>
                <td><?= htmlspecialchars($edit->no_trx);?></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td><?= htmlspecialchars($edit->name);?></td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td><?= htmlspecialchars($edit->nama_pelanggan ?? '-');?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?= htmlspecialchars($edit->created_at ?? '-');?></td>
            </tr>
        </table>
        <div class="doted"></div>
        <table>
            <tr>
                <td><b>Item</b></td>
                <td class="right"><b>Total</b></td>
            </tr>
            <?php foreach($hasil as $r): ?>
            <tr>
                <td colspan="2" style="padding-top: 2px;"><?= htmlspecialchars($r->nama_barang);?></td>
            </tr>
            <tr>
                <td><?= getRupiah($r->jual);?> x <?=$r->qty;?> <?= ($r->diskon > 0 ? ' (Disc: '.getRupiah($r->diskon,'Rp').')' : ''); ?></td>
                <td class="right"><?= getRupiah($r->total,'Rp');?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="doted"></div>
        <table>
            <tr>
                <td>Metode Bayar</td>
                <td>:</td>
                <td class="right"><?= ucwords($edit->metode_bayar ?? 'Tunai') ?></td>
            </tr>
            <?php if ($diskon > 0): ?>
            <tr>
                <td>Total Diskon</td>
                <td>:</td>
                <td class="right"><?= getRupiah($diskon,'Rp');?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><b>Total Bayar</b></td>
                <td>:</td>
                <td class="right"><b><?= getRupiah($edit->total ?? 0,'Rp');?></b></td>
            </tr>
            <tr>
                <td>Dibayar</td>
                <td>:</td>
                <td class="right"><?= getRupiah($edit->bayar ?? 0,'Rp');?></td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td>:</td>
                <td class="right"><b><?= getRupiah(($edit->bayar - $edit->total) ?? 0,'Rp');?></b></td>
            </tr>
        </table>
        <div class="doted"></div>
        <div style="font-size: 7pt; text-align: center; margin: 3px 0;">
            Catatan: Barang yang telah dibeli tidak dapat ditukar/dikembalikan.
        </div>
        <div class="doted"></div>
        <center style="margin-top: 4px;">
            <b>TERIMA KASIH<br>ATAS KUNJUNGAN ANDA</b>
        </center>
    </section>
</body>
</html>