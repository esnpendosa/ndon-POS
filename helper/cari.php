<?php 
session_start();
include '../setting.php';
include '../helper.php';
if(!empty($_SESSION['codekop_session'])) {
    $uid =  (int)$_SESSION['codekop_session']['id'];
    $sql_users = "SELECT * FROM users WHERE id = ?";
    $row_users = $connectdb->prepare($sql_users);
    $row_users->execute(array($uid));
    $users = $row_users->fetch(PDO::FETCH_OBJ);
} else {
    redirect($baseURL.'login.php');
    exit;
}
if(!empty(getPost('keyword', true))) {
    $cari = preg_replace('/\s+/', '', getPost('keyword', true));
    $no =1;
    $sql = "SELECT barang_kategori.nama_kategori, barang.* 
            FROM barang 
            LEFT JOIN barang_kategori 
            ON barang.id_kategori=barang_kategori.id 
            WHERE id_barang = ? 
            ORDER BY barang.nama_barang ASC";
    $row = $connectdb->prepare($sql);
    $row->execute(array($cari));
    $hasil = $row->fetch(PDO::FETCH_OBJ);
    if(!empty($hasil)) {
        if(getGet('sortir') == 'beli') {
            $cekbarang =  "SELECT * FROM keranjang_beli WHERE id_barang = ? AND id_member = ?";
            $rowq = $connectdb->prepare($cekbarang);
            $rowq->execute(array($hasil->id, $_SESSION["codekop_session"]["id"]));
            $jml = $rowq->rowCount();
            if ($jml > 0) {
                $sqlw = "UPDATE keranjang_beli SET jumlah= jumlah + ? WHERE id_barang = ? AND id_member = ?";
                $roww = $connectdb->prepare($sqlw);
                $roww->execute(array(1,$hasil->id, $_SESSION["codekop_session"]["id"]));
            } else {
                $data[] =  $hasil->id;
                $data[] =  $_SESSION['codekop_session']['id'];
                $data[] =  $hasil->nama_barang;
                $data[] =  1;
                $data[] =  $hasil->harga_beli;
                $data[] =  $hasil->harga_jual;
                $data[] =  date('Y-m-d');

                $sqlk = "INSERT INTO keranjang_beli (id_barang,id_member,nama_barang,jumlah,beli,jual,tanggal_input ) VALUES (?,?,?,?,?,?,?)";
                $rowk = $connectdb->prepare($sqlk);
                $rowk->execute($data);
            }
            set_flashdata("Berhasil", "tambah barang ke keranjang !", "success");
        } else {
            if($hasil->stok > 0) {

                $cekbarang =  "SELECT * FROM keranjang WHERE id_barang = ? AND id_member = ?";
                $rowq = $connectdb->prepare($cekbarang);
                $rowq->execute(array($hasil->id, $_SESSION["codekop_session"]["id"]));
                $jml = $rowq->rowCount();
                if ($jml > 0) {
                    $item_lama = $rowq->fetch(PDO::FETCH_OBJ);
                    $new_qty = (int)$item_lama->jumlah + 1;
                    $harga_jual = (float)$hasil->harga_jual;
                    if (!empty($hasil->min_grosir) && (int)$hasil->min_grosir > 0 && $new_qty >= (int)$hasil->min_grosir && (float)$hasil->harga_grosir > 0) {
                        $harga_jual = (float)$hasil->harga_grosir;
                    }
                    $sqlw = "UPDATE keranjang SET jumlah= ?, jual = ? WHERE id_barang = ? AND id_member = ?";
                    $roww = $connectdb->prepare($sqlw);
                    $roww->execute(array($new_qty, $harga_jual, $hasil->id, $_SESSION["codekop_session"]["id"]));
                } else {
                    $qty = 1;
                    $harga_jual = (float)$hasil->harga_jual;
                    if (!empty($hasil->min_grosir) && (int)$hasil->min_grosir > 0 && $qty >= (int)$hasil->min_grosir && (float)$hasil->harga_grosir > 0) {
                        $harga_jual = (float)$hasil->harga_grosir;
                    }
                    $data = [];
                    $data[] =  $hasil->id;
                    $data[] =  $_SESSION['codekop_session']['id'];
                    $data[] =  $hasil->nama_barang;
                    $data[] =  0;
                    $data[] =  $qty;
                    $data[] =  $hasil->harga_beli;
                    $data[] =  $harga_jual;
                    $data[] =  date('Y-m-d');

                    $sqlk = "INSERT INTO keranjang (id_barang,id_member,nama_barang,diskon,jumlah,beli,jual,tanggal_input ) VALUES (?,?,?,?,?,?,?,?)";
                    $rowk = $connectdb->prepare($sqlk);
                    $rowk->execute($data);
                }
                set_flashdata("Berhasil", "tambah barang ke keranjang !", "success");
            } else {
                set_flashdata("Gagal", "qty barang kurang dari stok barang !", "danger");
            }
        }
    }else{
        set_flashdata("Gagal","Barang tidak ditemukan !","danger");
    }
    redirect("index.php");
}