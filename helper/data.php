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
}

if(!empty(getGet('aksi') == 'barang')) {
    $query  = "SELECT barang_kategori.nama_kategori, barang.* 
                FROM barang 
                LEFT JOIN barang_kategori ON barang.id_kategori=barang_kategori.id";
    $search = array('nama_kategori','nama_barang','id_barang','merk','satuan_barang','harga_beli','harga_jual','stok');
    $where  = null;
    if(!empty(getGet('stok', true))) {
        $isWhere = " stok <= 5 ";
    } else {
        $isWhere = null;
    }
    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

if(!empty(getGet('aksi') == 'nota-jual')) {
    $query = "SELECT users.name, pelanggan.nama_pelanggan, penjualan.* 
                FROM penjualan 
                LEFT JOIN users 
                ON penjualan.id_member = users.id 
                LEFT JOIN pelanggan 
                ON penjualan.id_pelanggan=pelanggan.id";
    $search = array('no_trx','name','nama_pelanggan');
    if(!empty(getGet('id_pelanggan', true))) {
        $where  = array('id_pelanggan' => getGet('id_pelanggan'));
    } else {
        $where  = null;
    }
    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " penjualan.periode = '".$periode."' ";
    } elseif(!empty(getGet('hari', true))) {
        $tgla = getGet('tgla', true);
        $tglb = getGet('tglb', true);
        $isWhere = " penjualan.tanggal_input BETWEEN '$tgla' AND '$tglb' ";
    } else {
        $isWhere = " penjualan.periode = '".date('Y-m')."' ";
    }
    if($_SESSION['codekop_session']['akses']== 5) {
        $isWhere .= " AND penjualan.id_member = ".$_SESSION['codekop_session']['id'];
    }


    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

// if(!empty(getGet('aksi') == 'nota-jual-produk')){
//     $query = "SELECT * FROM penjualan_detail";
//     $search = array('no_trx','nama_barang');
//     $where  = null;
//     if(!empty(getGet('thn'])){
//         $periode = getGet('thn',true).'-'.getGet('bln',true);
//         $isWhere = " penjualan_detail.periode = '".$periode."' ";
//     }elseif(!empty(getGet('hari',true))){
//         $tgla = getGet('tgla',true);
//         $tglb = getGet('tglb',true);
//         $isWhere = " penjualan_detail.tgl_input BETWEEN '$tgla' AND '$tglb' ";
//     }else{
//         $isWhere = " penjualan_detail.periode = '".date('Y-m')."' ";
//     }
//     echo get_tables_query($connectdb,$query,$search,$where,$isWhere);
// }

if(!empty(getGet('aksi') == 'nota-jual-produk')) {
    $query = "SELECT barang_kategori.nama_kategori, 
                barang.id_kategori, 
                penjualan_detail.* 
                FROM penjualan_detail LEFT JOIN barang ON penjualan_detail.id_barang=barang.id 
                LEFT JOIN barang_kategori ON barang.id_kategori=barang_kategori.id";
    $search = array('no_trx','penjualan_detail.nama_barang');

    if(!empty(getGet('kategori', true))) {
        if(getGet('kategori', true) !== 'All') {
            $where  = array('barang.id_kategori' => getGet('kategori', true));
        } else {
            $where  = null;
        }
    } else {
        $where  = null;
    }

    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " penjualan_detail.periode = '".$periode."' ";
    } elseif(!empty(getGet('hari', true))) {
        $tgla = getGet('tgla');
        $tglb = getGet('tglb');
        $isWhere = " penjualan_detail.tgl_input BETWEEN '$tgla' AND '$tglb' ";
    } else {
        $isWhere = " penjualan_detail.periode = '".date('Y-m')."' ";
    }
    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

if(!empty(getGet('aksi') == 'nota-beli')) {
    $query = "SELECT users.name, pembelian.* 
                FROM pembelian 
                LEFT JOIN users 
                ON pembelian.id_member = users.id";
    $search = array('no_trx','name');
    $where  = null;
    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " pembelian.periode = '".$periode."' ";
    } elseif(!empty(getGet('hari', true))) {
        $tgla = getGet('tgla');
        $tglb = getGet('tglb');
        $isWhere = " pembelian.tanggal_input BETWEEN '$tgla' AND '$tglb' ";
    } else {
        $isWhere = " pembelian.periode = '".date('Y-m')."' ";
    }
    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

if(!empty(getGet('aksi') == 'nota-beli-produk')) {
    $query = "SELECT * FROM pembelian_detail";
    $search = array('no_trx','nama_barang');
    $where  = null;
    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " pembelian_detail.periode = '".$periode."' ";
    } elseif(!empty(getGet('hari', true))) {
        $tgla = getGet('tgla');
        $tglb = getGet('tglb');
        $isWhere = " pembelian_detail.tgl_input BETWEEN '$tgla' AND '$tglb' ";
    } else {
        $isWhere = " pembelian_detail.periode = '".date('Y-m')."' ";
    }
    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

if(!empty(getGet('aksi') == 'stok')) {
    $query = "SELECT barang_kategori.nama_kategori, barang.* 
                FROM barang 
                LEFT JOIN barang_kategori ON barang.id_kategori=barang_kategori.id";
    $search = array('nama_kategori','nama_barang','id_barang');
    $where = null;
    $isWhere = null;
    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

if(!empty(getGet('aksi') == 'stok_masuk')) {
    $query = "SELECT SUM(qty) AS qty FROM pembelian_detail";
    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " pembelian_detail.periode = '".$periode."' ";
    } else {
        $isWhere = " pembelian_detail.periode = '".date('Y-m')."' ";
    }

    $sql = $query.' WHERE '.$isWhere.' AND idb = ?';
    $row = $connectdb->prepare($sql);
    $row->execute(array(getPost('id_barang')));
    $qty = $row->fetch();
    $qt = $qty['qty'] ?? 0;
    echo json_encode(['qty' => $qt]);
}

if(!empty(getGet('aksi') == 'riwayat_stok')) {
    $query = "SELECT * FROM (
                SELECT
                    pembelian_detail.id AS id,
                    pembelian_detail.no_trx AS referensi,
                    pembelian_detail.idb AS idb,
                    pembelian_detail.nama_barang AS nama_barang,
                    'Masuk' AS jenis,
                    pembelian_detail.qty AS qty,
                    'Pembelian Barang' AS keterangan,
                    pembelian_detail.tgl_input AS tgl_input,
                    pembelian_detail.periode AS periode,
                    users.name AS nama_user,
                    pembelian_detail.created_at AS created_at
                FROM pembelian_detail
                LEFT JOIN users ON pembelian_detail.id_member = users.id

                UNION ALL

                SELECT
                    penjualan_detail.id AS id,
                    penjualan_detail.no_trx AS referensi,
                    penjualan_detail.idb AS idb,
                    penjualan_detail.nama_barang AS nama_barang,
                    'Keluar' AS jenis,
                    penjualan_detail.qty AS qty,
                    'Penjualan Barang' AS keterangan,
                    penjualan_detail.tgl_input AS tgl_input,
                    penjualan_detail.periode AS periode,
                    users.name AS nama_user,
                    penjualan_detail.created_at AS created_at
                FROM penjualan_detail
                LEFT JOIN users ON penjualan_detail.id_member = users.id

                UNION ALL

                SELECT
                    stok_manual.id AS id,
                    CONCAT('ADJ-', stok_manual.id) AS referensi,
                    barang.id_barang AS idb,
                    barang.nama_barang AS nama_barang,
                    (CASE WHEN stok_manual.jenis = 'masuk' THEN 'Masuk' ELSE 'Keluar' END) AS jenis,
                    stok_manual.qty AS qty,
                    CONCAT('Penyesuaian Manual', IF(stok_manual.keterangan IS NOT NULL AND stok_manual.keterangan != '', CONCAT(' - ', stok_manual.keterangan), '')) AS keterangan,
                    stok_manual.tgl_input AS tgl_input,
                    stok_manual.periode AS periode,
                    users.name AS nama_user,
                    stok_manual.created_at AS created_at
                FROM stok_manual
                LEFT JOIN barang ON stok_manual.id_barang = barang.id
                LEFT JOIN users ON stok_manual.id_member = users.id
            ) AS riwayat";
    $search = array('nama_barang', 'idb', 'referensi', 'nama_user');
    $where  = null;

    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " periode = '".$periode."' ";
    } elseif(!empty(getGet('hari', true))) {
        $tgla = getGet('tgla', true);
        $tglb = getGet('tglb', true);
        $isWhere = " tgl_input BETWEEN '$tgla' AND '$tglb' ";
    } else {
        $isWhere = " periode = '".date('Y-m')."' ";
    }

    if(!empty(getGet('jenis', true)) && getGet('jenis', true) !== 'semua') {
        $jenisFilter = (getGet('jenis', true) === 'masuk') ? 'Masuk' : 'Keluar';
        $isWhere .= " AND jenis = '".$jenisFilter."' ";
    }

    echo get_tables_query($connectdb, $query, $search, $where, $isWhere);
}

if(!empty(getGet('aksi') == 'stok_keluar')) {
    $query = "SELECT SUM(qty) AS qty FROM penjualan_detail";
    if(!empty(getGet('thn', true))) {
        $periode = getGet('thn', true).'-'.getGet('bln', true);
        $isWhere = " penjualan_detail.periode = '".$periode."' ";
    } else {
        $isWhere = " penjualan_detail.periode = '".date('Y-m')."' ";
    }

    $sql = $query.' WHERE '.$isWhere.' AND idb = ?';
    $row = $connectdb->prepare($sql);
    $row->execute(array(getPost('id_barang')));
    $qty = $row->fetch();
    $qt = $qty['qty'] ?? 0;
    echo json_encode(['qty' => $qt]);
}
