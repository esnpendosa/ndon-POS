<?php
// $connectdb sudah tersedia dari setting.php via index.php — tidak perlu koneksi ulang

// Query untuk mengambil data barang dari tabel barang
$sql_barang = "SELECT harga_beli, harga_jual, stok FROM barang";
$row_barang = $connectdb->prepare($sql_barang);
$row_barang->execute();
$data_barang = $row_barang->fetchAll(PDO::FETCH_OBJ);

// Inisialisasi variabel untuk total perhitungan
$totalHargaBeliStok = 0;
$totalHargaJualStok = 0;

// Menghitung total harga_beli * stok dan harga_jual * stok
foreach ($data_barang as $barang) {
    // Total harga_beli * stok
    $totalHargaBeliStok += $barang->harga_beli * $barang->stok;

    // Total harga_jual * stok
    $totalHargaJualStok += $barang->harga_jual * $barang->stok;
}


// Selanjutnya, kode lain untuk menghitung penjualan, pembelian, dan operasional
// Query untuk menghitung total penjualan
$sql_penjualan = "SELECT jual, qty FROM penjualan_detail";
$row_penjualan = $connectdb->prepare($sql_penjualan);
$row_penjualan->execute();
$penjualan = $row_penjualan->fetchAll(PDO::FETCH_OBJ);

$totalPenjualan = 0;
foreach ($penjualan as $val) {
    $totalPenjualan += $val->jual * $val->qty;
}

// Query untuk menghitung total pembelian
$sql_pembelian = "SELECT beli, qty FROM pembelian_detail";
$row_pembelian = $connectdb->prepare($sql_pembelian);
$row_pembelian->execute();
$pembelian = $row_pembelian->fetchAll(PDO::FETCH_OBJ);

$totalPembelian = 0;
foreach ($pembelian as $val) {
    $totalPembelian += $val->beli * $val->qty;
}

// Query untuk menghitung operasional
$sql_operasional = "SELECT harga_operasional, status_operasional FROM operasional";
$row_operasional = $connectdb->prepare($sql_operasional);
$row_operasional->execute();
$operasional = $row_operasional->fetchAll(PDO::FETCH_OBJ);

$operasionalMasuk = 0;
$operasionalKeluar = 0;
foreach ($operasional as $val) {
    if ($val->status_operasional == "Pemasukan") {
        $operasionalMasuk += $val->harga_operasional;
    } else {
        $operasionalKeluar += $val->harga_operasional;
    }
}

// Laba kotor
$labaKotor = $totalPenjualan - $totalPembelian;

// Laba bersih
$labaBersih = $labaKotor + $operasionalMasuk - $operasionalKeluar;


// Role pengguna
$role = $_SESSION['role'] ?? 'guest'; // Contoh: 'admin', 'kasir', 'gudang'

// Query tren harian (Detail per Bulan dengan Filter)
$selected_month = !empty($_POST['bln_trx']) ? $_POST['bln_trx'] : date('m');
$selected_year = !empty($_POST['thn_trx']) ? $_POST['thn_trx'] : date('Y');
$num_days = date('t', strtotime("$selected_year-$selected_month-01"));

$labels_harian = [];
$data_restok_harian = [];
$data_jual_harian = [];

for ($d = 1; $d <= $num_days; $d++) {
    $day_str = sprintf('%02d', $d);
    $date = "$selected_year-$selected_month-$day_str";
    $labels_harian[] = $d; // Angka tanggal sebagai label
    
    // Query restok (pembelian)
    $sql_restok = "SELECT SUM(qty) as total FROM pembelian_detail WHERE tgl_input = ?";
    $stmt_restok = $connectdb->prepare($sql_restok);
    $stmt_restok->execute([$date]);
    $res_restok = $stmt_restok->fetch(PDO::FETCH_OBJ);
    $data_restok_harian[] = (int)($res_restok->total ?? 0);
    
    // Query penjualan
    $sql_jual = "SELECT SUM(qty) as total FROM penjualan_detail WHERE tgl_input = ?";
    $stmt_jual = $connectdb->prepare($sql_jual);
    $stmt_jual->execute([$date]);
    $res_jual = $stmt_jual->fetch(PDO::FETCH_OBJ);
    $data_jual_harian[] = (int)($res_jual->total ?? 0);
}

$bulan_names = array(
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", 
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", 
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
);
$nama_bulan_dipilih = $bulan_names[$selected_month];
?>

<!-- Page content -->
<div class="row">
    <div class="col-sm-12">
        <?php 
            $sql="SELECT * FROM barang WHERE stok <= 5";
            $row = $connectdb->prepare($sql);
            $row->execute();
            $r = $row->rowCount();
            if ($r > 0) {
                echo "
                <div class='alert alert-warning'>
                    <span class='glyphicon glyphicon-info-sign'></span> Ada <span style='color:red'>$r</span> barang yang Stok tersisa sudah kurang dari 5 items. silahkan pesan lagi !!
                    <span class='float-right'><a href='".$baseURL."barang/index.php?stok=yes' class='text-dark'>Cek Barang <i class='fa fa-angle-right'></i></a></span>
                </div>
                ";    
            }
        ?>
    </div>
    
    <!-- Baris 1: Info Toko, Jam, Tanggal, Total Penjualan -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="info-box mb-2">
            <span class="info-box-icon bg-primary elevation-1">
                <i class="fas fa-shopping-cart"></i>
            </span>
            <div class="info-box-content" style="min-width:0;">
                <span class="info-box-text font-weight-bold text-truncate d-block"><?= $toko->nama_toko;?></span>
                <span class="info-box-text text-truncate d-block"><?= $toko->tlp;?></span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="info-box mb-2">
            <span class="info-box-icon bg-purple elevation-1"><i class="far fa-clock"></i></span>
            <div class="info-box-content" style="min-width:0;">
                <span class="info-box-text">
                    <?php 
                        $a = date("H");
                        if ($a >= 6 && $a <= 11) {
                            echo "Selamat Pagi";
                        } else if ($a > 11 && $a <= 14) {
                            echo "Selamat Siang";
                        } else if ($a > 14 && $a <= 18) {
                            echo "Selamat Sore";
                        } else { 
                            echo "Selamat Malam";
                        }
                    ?>
                </span>
                <span class="info-box-number">
                    <span id="jam"></span>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="info-box mb-2">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="info-box-content" style="min-width:0;">
                <span class="info-box-text">Tanggal</span>
                <span class="info-box-number" style="font-size:1rem;">
                    <?php 
                        function tgl_indo($tanggal) {
                            $bulan = array (
                                1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            );
                            $pecahkan = explode('-', $tanggal);
                            return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
                        }
                        echo tgl_indo(date('Y-m-d'));
                    ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Total Penjualan -->
    <?php if ($_SESSION['codekop_session']['akses'] == 1) { ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="info-box mb-2">
                <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content" style="min-width:0;">
                    <span class="info-box-text">Total Penjualan</span>
                    <span class="info-box-number" style="font-size:0.85rem; word-break:break-all;"><?= getRupiah($totalPenjualan ?? 0, 'Rp'); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
    
    <!-- Baris 2: Asset Pembelian, Asset Penjualan, Laba Kotor, Laba Bersih -->
    <?php if ($_SESSION['codekop_session']['akses'] == 1) { ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="info-box mb-2">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-dollar-sign"></i>
                </span>
                <div class="info-box-content" style="min-width:0;">
                    <span class="info-box-text">Asset Pembelian</span>
                    <span class="info-box-number" style="font-size:0.85rem; word-break:break-all;">
                        <?php echo getRupiah($totalHargaBeliStok ?? 0, 'Rp'); ?>
                    </span>
                </div>
            </div>
        </div>
    <?php } ?>
    
    <!-- Asset Penjualan -->
    <?php if ($_SESSION['codekop_session']['akses'] == 1) { ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="info-box mb-2">
                <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
                <div class="info-box-content" style="min-width:0;">
                    <span class="info-box-text">Asset Penjualan</span>
                    <span class="info-box-number" style="font-size:0.85rem; word-break:break-all;"><?= getRupiah($totalHargaJualStok ?? 0, 'Rp'); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Laba Kotor -->
    <?php if ($_SESSION['codekop_session']['akses'] == 1) { ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="info-box mb-2">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content" style="min-width:0;">
                    <span class="info-box-text">Laba Kotor</span>
                    <span class="info-box-number" style="font-size:0.85rem; word-break:break-all;"><?= getRupiah($labaKotor ?? 0, 'Rp'); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Laba Bersih -->
    <?php if ($_SESSION['codekop_session']['akses'] == 1) { ?>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="info-box mb-2">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-hand-holding-usd"></i></span>
                <div class="info-box-content" style="min-width:0;">
                    <span class="info-box-text">Laba Bersih</span>
                    <span class="info-box-number" style="font-size:0.85rem; word-break:break-all;"><?= getRupiah($labaBersih ?? 0, 'Rp'); ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
</div> <!-- /.row (Close first layout row to prevent grid bugs) -->

<?php if (!empty($_SESSION['codekop_session']['akses'] == 1)) {?>
<?php if(!empty($_POST['thn'])){ $thn = $_POST['thn'];  }else{ $thn = date('Y'); }?>
<div class="row">
    <div class="col-lg-12">
        <div class="card mt-2">
            <div class="card-header bg-primary border-0 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <h5 class="card-title m-0"><i class="far fa-chart-bar mr-1"></i> Grafik Penjualan & Pembelian Tahun <?= $thn;?></h5>
                <div class="card-tools">
                    <form method="post" action="<?= $baseURL.'index.php'?>" class="form-inline m-0">
                        <div class="form-group mb-0 mr-2">
                            <select name="thn" class="form-control form-control-sm">
                                <option value="">- Pilih Tahun -</option>
                                <?php
                                    $thn_skr = date('Y');
                                    for ($x = $thn_skr; $x >= 2021; $x--){
                                ?>
                                <option value="<?= $x;?>" <?php if($thn == $x){?> selected <?php }?>>
                                    <?= $x;?></option>
                                <?php }?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                            <i class="fa fa-search"></i>
                        </button>
                        <a href="<?= $baseURL.'index.php'?>" class="btn btn-success btn-sm">
                            <i class="fa fa-sync"></i>
                        </a>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="line-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card mt-2">
            <div class="card-header bg-primary border-0 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <h5 class="card-title m-0"><i class="far fa-chart-bar mr-1"></i> Grafik Kategori Barang</h5>
                <div class="card-tools">
                    <form method="post" action="<?= $baseURL.'index.php'?>" class="form-inline m-0">
                        <input type="hidden" name="kategori" value="yes">
                        <div class="form-group mb-0 mr-2">
                            <select name="bln" class="form-control form-control-sm">
                                <option value="">- Pilih Bulan -</option>
                                <?php
                                    $bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
                                    $jlh_bln=count($bulan);
                                    $bln1 = array('01','02','03','04','05','06','07','08','09','10','11','12');
                                    for($c=0; $c<$jlh_bln; $c+=1){
                                        if(!empty($_POST['kategori'])){
                                            if($_POST['bln'] == $bln1[$c]){
                                                echo"<option value='$bln1[$c]' selected> $bulan[$c] </option>";
                                            }else{
                                                echo"<option value='$bln1[$c]'> $bulan[$c] </option>";
                                            }
                                        }else{
                                            if(date('m') == $bln1[$c]){
                                                echo"<option value='$bln1[$c]' selected> $bulan[$c] </option>";
                                            }else{
                                                echo"<option value='$bln1[$c]'> $bulan[$c] </option>";
                                            }
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-0 mr-2">
                            <select name="thn" class="form-control form-control-sm">
                                <option value="">- Pilih Tahun -</option>
                                <?php
                                    $thn_skr = date('Y');
                                    for ($x = $thn_skr; $x >= 2021; $x--){
                                ?>
                                <option value="<?= $x;?>" <?php if($thn == $x){?> selected <?php }?>>
                                    <?= $x;?></option>
                                <?php }?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                            <i class="fa fa-search"></i>
                        </button>
                        <a href="<?= $baseURL.'index.php'?>" class="btn btn-success btn-sm">
                            <i class="fa fa-sync"></i>
                        </a>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="line-chart-2"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card mt-2">
            <div class="card-header bg-primary border-0 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <h5 class="card-title m-0"><i class="fas fa-chart-line mr-1"></i> Tren Transaksi Harian - <?= $nama_bulan_dipilih;?> <?= $selected_year;?></h5>
                <div class="card-tools">
                    <form method="post" action="<?= $baseURL.'index.php'?>" class="form-inline m-0">
                        <div class="form-group mb-0 mr-2">
                            <select name="bln_trx" class="form-control form-control-sm">
                                <option value="">- Pilih Bulan -</option>
                                <?php
                                    $bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
                                    $bln1 = array('01','02','03','04','05','06','07','08','09','10','11','12');
                                    for($c=0; $c<12; $c++){
                                        $selected = ($selected_month == $bln1[$c]) ? 'selected' : '';
                                        echo "<option value='{$bln1[$c]}' {$selected}>{$bulan[$c]}</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-0 mr-2">
                            <select name="thn_trx" class="form-control form-control-sm">
                                <option value="">- Pilih Tahun -</option>
                                <?php
                                    $thn_skr = date('Y');
                                    for ($x = $thn_skr; $x >= 2021; $x--){
                                        $selected = ($selected_year == $x) ? 'selected' : '';
                                        echo "<option value='{$x}' {$selected}>{$x}</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                            <i class="fa fa-search"></i>
                        </button>
                        <a href="<?= $baseURL.'index.php'?>" class="btn btn-success btn-sm">
                            <i class="fa fa-sync"></i>
                        </a>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="daily-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var linechartCanvas = document.getElementById('line-chart');
var linechartCtx = linechartCanvas.getContext('2d');

var gradientTerjual = linechartCtx.createLinearGradient(0, 0, 0, 300);
gradientTerjual.addColorStop(0, 'rgba(0, 180, 147, 0.35)');
gradientTerjual.addColorStop(1, 'rgba(0, 180, 147, 0.00)');

var gradientBeli = linechartCtx.createLinearGradient(0, 0, 0, 300);
gradientBeli.addColorStop(0, 'rgba(6, 182, 212, 0.35)');
gradientBeli.addColorStop(1, 'rgba(6, 182, 212, 0.00)');

var chart = new Chart(linechartCanvas, {
    type: 'line',
    data: {
        labels: [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
        ],
        datasets: [{
                label: "Stok Terjual",
                data: [
                    <?php 
                            // php mencari produk
                            for($n=1; $n<=12; $n++){
                                if($n > 9) {
                                    $period = $thn.'-'.$n;
                                }else{
                                    $period = $thn.'-'.'0'.$n;
                                }
                                $sql = "SELECT SUM(jumlah) as jml FROM penjualan 
                                        WHERE penjualan.periode = ? ORDER BY id DESC";
                                $row = $connectdb->prepare($sql);
                                $row->execute(array($period));
                                $gr = $row->fetch(PDO::FETCH_OBJ);
                        ?>
                    <?= $gr->jml ?? 0;?>,
                    <?php } ?>
                ],
                borderColor: 'rgba(0, 180, 147, 1)',
                backgroundColor: gradientTerjual,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: 'rgba(0, 180, 147, 1)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                lineTension: 0.35
            },
            {
                label: "Stok Pembelian",
                data: [
                    <?php 
                            // php mencari produk
                            for($n=1; $n<=12; $n++){
                                if($n > 9) {
                                    $period = $thn.'-'.$n;
                                }else{
                                    $period = $thn.'-'.'0'.$n;
                                }
                                $sql = "SELECT SUM(jumlah) as jml FROM pembelian 
                                        WHERE pembelian.periode = ? ORDER BY id DESC";
                                $row = $connectdb->prepare($sql);
                                $row->execute(array($period));
                                $gr = $row->fetch(PDO::FETCH_OBJ);
                        ?>
                    <?= $gr->jml ?? 0;?>,
                    <?php } ?>
                ],
                borderColor: 'rgba(6, 182, 212, 1)',
                backgroundColor: gradientBeli,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: 'rgba(6, 182, 212, 1)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                lineTension: 0.35
            },
        ],
    },
    plugins: [{
        afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;
            ctx.save();
            ctx.font = "bold 9px 'Inter', sans-serif";
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            
            chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function(element, index) {
                        var data = dataset.data[index];
                        if (data > 0) {
                            ctx.fillStyle = dataset.borderColor;
                            ctx.fillText(data, element._model.x, element._model.y - 8);
                        }
                    });
                }
            });
            ctx.restore();
        }
    }],
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'top',
            labels: {
                boxWidth: 10,
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                fontColor: '#64748b',
                padding: 15
            }
        },
        tooltips: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleFontFamily: "'Plus Jakarta Sans', sans-serif",
            bodyFontFamily: "'Inter', sans-serif",
            bodySpacing: 6,
            xPadding: 12,
            yPadding: 12,
            cornerRadius: 8
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false
                },
                ticks: {
                    fontFamily: "'Inter', sans-serif",
                    fontColor: '#94a3b8'
                }
            }],
            yAxes: [{
                gridLines: {
                    color: '#f1f5f9',
                    zeroLineColor: '#f1f5f9',
                    borderDash: [4, 4]
                },
                ticks: {
                    beginAtZero: true,
                    fontFamily: "'Inter', sans-serif",
                    fontColor: '#94a3b8',
                    padding: 8
                }
            }]
        }
    },
});
</script>
<script>
var linechart2Canvas = document.getElementById('line-chart-2');
var linechart2Ctx = linechart2Canvas.getContext('2d');

var gradientBarTerjual = linechart2Ctx.createLinearGradient(0, 0, 400, 0);
gradientBarTerjual.addColorStop(0, 'rgba(0, 180, 147, 0.35)');
gradientBarTerjual.addColorStop(1, 'rgba(0, 180, 147, 0.03)');

var gradientBarBeli = linechart2Ctx.createLinearGradient(0, 0, 400, 0);
gradientBarBeli.addColorStop(0, 'rgba(6, 182, 212, 0.35)');
gradientBarBeli.addColorStop(1, 'rgba(6, 182, 212, 0.03)');

var chart = new Chart(linechart2Canvas, {
    type: 'horizontalBar',
    data: {
        labels: [
            <?php 
                $sqls = "SELECT * FROM barang_kategori";
                $rows = $connectdb->prepare($sqls);
                $rows->execute();
                $kategori = $rows->fetchAll(PDO::FETCH_OBJ);
                foreach($kategori as $r){
                    echo '"'.$r->nama_kategori.'",';
                }
            ?>
        ],
        datasets: [{
                label: "Stok Terjual",
                data: [
                    <?php 

                if(!empty($_POST['kategori']) && !empty($_POST['bln'])){
                    $periode_detail = $thn.'-'.$_POST['bln'];
                }else{
                    $periode_detail = date('Y-m');
                }
                foreach($kategori as $r){
                    $sql1= "SELECT SUM(penjualan_detail.qty) as jml, barang.id_kategori FROM penjualan_detail 
                            LEFT JOIN barang ON penjualan_detail.id_barang= barang.id  
                            WHERE penjualan_detail.periode = ? AND  barang.id_kategori = ?";
                    $row1 = $connectdb->prepare($sql1);
                    $row1->execute(array($periode_detail, $r->id));
                    $pdj = $row1->fetch(PDO::FETCH_OBJ);
                ?>
                    <?= $pdj->jml ?? 0;?>,
                    <?php } ?>
                ],
                borderColor: 'rgba(0, 180, 147, 1)',
                backgroundColor: gradientBarTerjual,
                hoverBackgroundColor: 'rgba(0, 180, 147, 0.45)',
                hoverBorderColor: 'rgba(0, 180, 147, 1)',
                borderWidth: 2,
            },
            {
                label: "Stok Pembelian",
                data: [
                    <?php 

                if(!empty($_POST['kategori']) && !empty($_POST['bln'])){
                    $periode_detail = $thn.'-'.$_POST['bln'];
                }else{
                    $periode_detail = date('Y-m');
                }
                foreach($kategori as $r){
                    $sql1= "SELECT SUM(pembelian_detail.qty) as jml, barang.id_kategori FROM pembelian_detail 
                            LEFT JOIN barang ON pembelian_detail.id_barang= barang.id  
                            WHERE pembelian_detail.periode = ? AND  barang.id_kategori = ?";
                    $row1 = $connectdb->prepare($sql1);
                    $row1->execute(array($periode_detail, $r->id));
                    $pdj = $row1->fetch(PDO::FETCH_OBJ);
                ?>
                    <?= $pdj->jml ?? 0;?>,
                    <?php } ?>
                ],
                borderColor: 'rgba(6, 182, 212, 1)',
                backgroundColor: gradientBarBeli,
                hoverBackgroundColor: 'rgba(6, 182, 212, 0.45)',
                hoverBorderColor: 'rgba(6, 182, 212, 1)',
                borderWidth: 2,
            },
        ],
    },
    plugins: [{
        afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;
            ctx.save();
            ctx.font = "bold 9px 'Inter', sans-serif";
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';
            
            chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function(element, index) {
                        var data = dataset.data[index];
                        if (data > 0) {
                            ctx.fillStyle = dataset.borderColor;
                            ctx.fillText(data, element._model.x + 6, element._model.y);
                        }
                    });
                }
            });
            ctx.restore();
        }
    }],
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'top',
            labels: {
                boxWidth: 10,
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                fontColor: '#64748b',
                padding: 15
            }
        },
        tooltips: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleFontFamily: "'Plus Jakarta Sans', sans-serif",
            bodyFontFamily: "'Inter', sans-serif",
            bodySpacing: 6,
            xPadding: 12,
            yPadding: 12,
            cornerRadius: 8
        },
        scales: {
            xAxes: [{
                gridLines: {
                    color: '#f1f5f9',
                    zeroLineColor: '#f1f5f9',
                    borderDash: [4, 4]
                },
                ticks: {
                    beginAtZero: true,
                    fontFamily: "'Inter', sans-serif",
                    fontColor: '#94a3b8',
                    padding: 8
                }
            }],
            yAxes: [{
                gridLines: {
                    display: false
                },
                ticks: {
                    fontFamily: "'Inter', sans-serif",
                    fontColor: '#94a3b8',
                    padding: 8
                },
                barPercentage: 0.6,
                categoryPercentage: 0.5
            }]
        }
    },
});
</script>
<script>
var dailyChartCanvas = document.getElementById('daily-chart');
var dailyChartCtx = dailyChartCanvas.getContext('2d');

var gradientDailyJual = dailyChartCtx.createLinearGradient(0, 0, 0, 300);
gradientDailyJual.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
gradientDailyJual.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

var gradientDailyRestok = dailyChartCtx.createLinearGradient(0, 0, 0, 300);
gradientDailyRestok.addColorStop(0, 'rgba(245, 158, 11, 0.3)');
gradientDailyRestok.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

var dailyChart = new Chart(dailyChartCanvas, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels_harian); ?>,
        datasets: [{
                label: "Qty Terjual",
                data: <?php echo json_encode($data_jual_harian); ?>,
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: gradientDailyJual,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: 'rgba(16, 185, 129, 1)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                lineTension: 0.35
            },
            {
                label: "Qty Restok (Beli)",
                data: <?php echo json_encode($data_restok_harian); ?>,
                borderColor: 'rgba(245, 158, 11, 1)',
                backgroundColor: gradientDailyRestok,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: 'rgba(245, 158, 11, 1)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                lineTension: 0.35
            }
        ]
    },
    plugins: [{
        afterDatasetsDraw: function(chart) {
            var ctx = chart.ctx;
            ctx.save();
            ctx.font = "bold 9px 'Inter', sans-serif";
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            
            chart.data.datasets.forEach(function(dataset, i) {
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function(element, index) {
                        var data = dataset.data[index];
                        if (data > 0) {
                            ctx.fillStyle = dataset.borderColor;
                            ctx.fillText(data, element._model.x, element._model.y - 8);
                        }
                    });
                }
            });
            ctx.restore();
        }
    }],
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'top',
            labels: {
                boxWidth: 10,
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                fontColor: '#64748b',
                padding: 15
            }
        },
        tooltips: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleFontFamily: "'Plus Jakarta Sans', sans-serif",
            bodyFontFamily: "'Inter', sans-serif",
            bodySpacing: 6,
            xPadding: 12,
            yPadding: 12,
            cornerRadius: 8
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false
                },
                ticks: {
                    fontFamily: "'Inter', sans-serif",
                    fontColor: '#94a3b8'
                }
            }],
            yAxes: [{
                gridLines: {
                    color: '#f1f5f9',
                    zeroLineColor: '#f1f5f9',
                    borderDash: [4, 4]
                },
                ticks: {
                    beginAtZero: true,
                    fontFamily: "'Inter', sans-serif",
                    fontColor: '#94a3b8',
                    padding: 8
                }
            }]
        }
    }
});
</script>
<?php }?>