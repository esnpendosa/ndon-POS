<?php if (empty($_SESSION['codekop_session']) || !in_array($_SESSION['codekop_session']['akses'], [1, 6])) { redirect($baseURL); }?>
<?php
    $bulan_tes = array(
        '01' => "Januari", '02' => "Februari", '03' => "Maret", '04' => "April",
        '05' => "Mei", '06' => "Juni", '07' => "Juli", '08' => "Agustus",
        '09' => "September", '10' => "Oktober", '11' => "November", '12' => "Desember"
    );
?>
<!-- Page content -->
<div class="row">
    <div class="col-sm-12">
        <?php if (!empty(flashdata())) { echo flashdata(); }?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-history mr-1"></i> Cari Riwayat Per Bulan</h3>
            </div>
            <div class="card-body">
                <form method="post" action="index.php?cari=ok">
                    <div class="row">
                        <div class="col-sm-3">
                            <select name="bln" class="form-control">
                                <option selected="selected">Bulan</option>
                                <?php
                                    $bulan = array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
                                    $bln1 = array('01','02','03','04','05','06','07','08','09','10','11','12');
                                    for ($c = 0; $c < count($bulan); $c += 1) {
                                        if (!empty(getGet('cari', true))) {
                                            $sel = (getPost('bln', true) == $bln1[$c]) ? 'selected' : '';
                                        } else {
                                            $sel = (date('m') == $bln1[$c]) ? 'selected' : '';
                                        }
                                        echo "<option value='$bln1[$c]' $sel> $bulan[$c] </option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <?php
                                $now = date('Y');
                                echo "<select name='thn' class='form-control'>";
                                echo '<option>Tahun</option>';
                                for ($a = 2021; $a <= $now; $a++) {
                                    if (!empty(getGet('cari', true))) {
                                        $sel = (getPost('thn', true) == $a) ? 'selected' : '';
                                    } else {
                                        $sel = (date('Y') == $a) ? 'selected' : '';
                                    }
                                    echo "<option value='$a' $sel>$a</option>";
                                }
                                echo "</select>";
                            ?>
                        </div>
                        <div class="col-sm-3">
                            <select name="jenis" class="form-control">
                                <option value="semua">Semua Jenis</option>
                                <option value="masuk" <?= (getPost('jenis', true) == 'masuk') ? 'selected' : '';?>>Stok Masuk</option>
                                <option value="keluar" <?= (getPost('jenis', true) == 'keluar') ? 'selected' : '';?>>Stok Keluar</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary btn-flat">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="index.php" class="btn btn-success btn-flat">
                                    <i class="fas fa-sync"></i> Refresh</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-calendar mr-1"></i> Cari Riwayat Per Tanggal</h3>
            </div>
            <div class="card-body">
                <form method="get" action="index.php">
                    <?php
                        if (!empty(getGet('hari', true))) {
                            $tgla = getGet('tgla', true);
                            $tglb = getGet('tglb', true);
                        } else {
                            $tgla = "";
                            $tglb = "";
                        }
                    ?>
                    <input type="hidden" name="hari" value="yes">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Tanggal Awal</label>
                                <input type="date" value="<?= $tgla;?>" class="form-control w-100" name="tgla">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" value="<?= $tglb;?>" class="form-control w-100" name="tglb">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Jenis</label>
                                <select name="jenis" class="form-control">
                                    <option value="semua">Semua Jenis</option>
                                    <option value="masuk" <?= (getGet('jenis', true) == 'masuk') ? 'selected' : '';?>>Stok Masuk</option>
                                    <option value="keluar" <?= (getGet('jenis', true) == 'keluar') ? 'selected' : '';?>>Stok Keluar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="btn-group mt-4 pt-2" role="group">
                                <button class="btn btn-primary btn-flat">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="index.php" class="btn btn-success btn-flat">
                                    <i class="fas fa-sync"></i> Refresh</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <?php if (!empty(getGet('cari', true))) {?>
                        Riwayat Stok - <?= $bulan_tes[getPost('bln', true)] ?? '';?> <?= getPost('thn', true);?>
                    <?php } elseif (!empty(getGet('hari', true))) {?>
                        Riwayat Stok - <?= $tgla;?> s/d <?= $tglb;?>
                    <?php } else {?>
                        Riwayat Stok - <?= $bulan_tes[date('m')];?> <?= date('Y');?>
                    <?php }?>
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered w-100" id="table-artikel-query">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jenis</th>
                                <th>Qty</th>
                                <th>Keterangan</th>
                                <th>Referensi</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var tabel = null;
$(document).ready(function() {
    tabel = $('#table-artikel-query').DataTable({
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "ordering": true,
        "order": [
            [1, 'DESC']
        ],
        "ajax": {
            <?php if (!empty(getPost('thn', true))) {?>
                "url": "<?= $baseURL.'helper/data.php?aksi=riwayat_stok&thn='.getPost('thn', true).'&bln='.getPost('bln', true).'&jenis='.getPost('jenis', true);?>",
            <?php } elseif (!empty(getGet('hari', true))) {?>
                "url": "<?= $baseURL.'helper/data.php?aksi=riwayat_stok&hari=yes&tgla='.getGet('tgla', true).'&tglb='.getGet('tglb', true).'&jenis='.getGet('jenis', true);?>",
            <?php } else {?>
                "url": "<?= $baseURL.'helper/data.php?aksi=riwayat_stok';?>",
            <?php }?>
            "type": "POST"
        },
        "deferRender": true,
        "aLengthMenu": [
            [10, 25, 50],
            [10, 25, 50]
        ],
        "columns": [{
                "data": 'created_at',
                "sortable": false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { "data": "created_at" },
            { "data": "idb" },
            { "data": "nama_barang" },
            {
                "data": "jenis",
                render: function(data) {
                    if (data === 'Masuk') {
                        return `<span class="badge badge-success">Masuk</span>`;
                    }
                    return `<span class="badge badge-danger">Keluar</span>`;
                }
            },
            { "data": "qty" },
            { "data": "keterangan" },
            { "data": "referensi" },
            { "data": "nama_user" },
        ],
    });
});
</script>
