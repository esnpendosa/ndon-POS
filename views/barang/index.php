<!-- Page content -->
<div class="row">
    <div class="col-sm-12">
        <?php 
            if (!empty(flashdata())) {
                echo flashdata();
            }
            $sql=" select * from barang where stok <= 5";
            $row = $connectdb -> prepare($sql);
            $row -> execute();
            $r = $row -> rowCount();
            if ($r > 0) {
                echo "
                    <div class='alert alert-warning'>
                        <span class='glyphicon glyphicon-info-sign'></span> Ada <span style='color:red'>$r</span> barang yang Stok tersisa sudah kurang dari 5 items. silahkan pesan lagi !!
                        <span class='float-right'><a href='".$baseURL."barang/index.php?stok=yes' class='text-dark'>Cek Barang <i class='fa fa-angle-right'></i></a></span>
                    </div>
                    ";
            }
        ?>
        <div class="btn-group" role="group" aria-label="Basic example">
            <?php if (!empty(in_array($_SESSION['codekop_session']['akses'], [1, 6]))) {?>
            <a href="tambah.php" class="btn btn-primary btn-md" role="button">
                <i class="fa fa-plus mr-1"></i>Add Barang</a>
            <a href="import.php" class="btn btn-info btn-md" role="button">
                <i class="fa fa-plus mr-1"></i>Import Barang (Excel)</a>
            <?php }?>
            <button type="button" class="btn btn-danger btn-flat"><a href="index.php?stok=yes" class="text-white"><i
                        class="fa fa-times mr-1"></i> Stok Limit</a></button>
            <button type="button" class="btn btn-success btn-flat"><a href="index.php" class="text-white"><i
                        class="fas fa-sync mr-1"></i> Refresh</a></button>
            <a href="<?= $baseURL;?>stok/index.php" class="btn btn-secondary btn-flat">
                <i class="fa fa-history mr-1"></i> Riwayat Stok</a>
        </div>

        <!-- Modal Sesuaikan Stok -->
        <div class="modal fade" id="modelIdStok" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="proses.php?aksi=stok">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fa fa-boxes mr-1"></i> Sesuaikan Stok</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="stok_id">
                            <div class="form-group">
                                <label>Barang</label>
                                <input type="text" class="form-control" id="stok_nama" readonly>
                            </div>
                            <div class="form-group">
                                <label>Stok Saat Ini</label>
                                <input type="text" class="form-control" id="stok_saat_ini" readonly>
                            </div>
                            <div class="form-group">
                                <label>Jenis Penyesuaian</label>
                                <div>
                                    <input type="radio" name="jenis_stok" id="jenis_masuk" value="masuk" checked>&nbsp;
                                    <label for="jenis_masuk" class="mr-4">Tambah (Masuk)</label>
                                    <input type="radio" name="jenis_stok" id="jenis_keluar" value="keluar">&nbsp;
                                    <label for="jenis_keluar">Kurangi (Keluar)</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" min="1" step="any" class="form-control" required name="qty_stok">
                            </div>
                            <div class="form-group">
                                <label>Keterangan <small class="text-danger">* Opsional, mis. Stok Opname / Barang Rusak / Barang Hilang</small></label>
                                <textarea class="form-control" name="keterangan_stok"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br><br>
        <div class="card">
            <!-- Card header -->
            <div class="card-header">
                <h4 class="mb-0">Data Barang</h4>
            </div>
            <div class="card-body p-2">
                <!-- Light table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered w-100" id="table-artikel-query">
                        <thead>
                           <tr>
                                <th>No</th>
                                <th>ID Barang</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <?php if (!empty(in_array($_SESSION['codekop_session']['akses'], [1]))) { ?>
                                <th>Harga beli</th>
                                <?php }?>
                                <th>Harga jual</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <?php if (!empty(in_array($_SESSION['codekop_session']['akses'], [1, 6]))) { ?>
                                <th style="width:8%">Aksi</th>
                                <?php }?>
                            </tr>
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
function bukaModalStok(id, nama, stok) {
    $('#stok_id').val(id);
    $('#stok_nama').val(nama);
    $('#stok_saat_ini').val(stok);
    $('#modelIdStok').modal('show');
}
var tabel = null;
$(document).ready(function() {
    tabel = $('#table-artikel-query').DataTable({
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "ordering": true, // Set true agar bisa di sorting
        "order": [
            [0, 'DESC']
        ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
        "ajax": {
            <?php if (isset($_GET['stok'])) {?> "url": "<?= $baseURL.'/helper/data.php?aksi=barang&stok=yes';?>", // URL file untuk proses select datanya
            <?php } else {?> "url": "<?= $baseURL.'/helper/data.php?aksi=barang';?>", // URL file untuk proses select datanya
            <?php }?> "type": "POST"
        },
        "deferRender": true,
        "aLengthMenu": [
            [10, 25, 50],
            [10, 25, 50]
        ], // Combobox Limit
        "columns": [{
                "data": 'id',
                "sortable": false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                "data": "id_barang"
            },
            {
                "data": "gambar",
                "render": function(data, type, row, meta) {
                    if (row.gambar !== null) {
                        return `<center>
                                        <a href="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" data-toggle="lightbox" 
                                            data-title="${row.nama_barang}" data-gallery="gallery">
                                            <img src="<?= $baseURL;?>assets/uploads/barang/${row.gambar}" 
                                                alt="${row.nama_barang}" class="img-fluid" width="80"/>
                                        </a>
                                    </center>`;
                    } else {
                        return `<center>
                                        <a href="<?= $baseURL;?>assets/dist/img/no-image.jpg" data-toggle="lightbox" 
                                            data-title="${row.nama_barang}" data-gallery="gallery">
                                            <img src="<?= $baseURL;?>assets/dist/img/no-image.jpg" 
                                                alt="${row.nama_barang}" class="img-fluid" width="80"/>
                                        </a>
                                    </center>`;
                    }
                }
            },
            {
                "data": "nama_barang"
            },
            {
                "data": "nama_kategori"
            },
            {
                "data": "merk"
            },
            <?php if (!empty(in_array($_SESSION['codekop_session']['akses'], [1]))) {?> {
                data: 'harga_beli',
                render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp')
            },
            <?php }?> {
                data: 'harga_jual',
                render: function(data, type, row, meta) {
                    var out = 'Rp' + Number(row.harga_jual || 0).toLocaleString('id-ID');
                    if (row.min_grosir > 0 && Number(row.harga_grosir || 0) > 0) {
                        out += `<div class="text-success" style="font-size: 11px; margin-top: 2px;">
                                    <i class="fas fa-tags"></i> Grosir &ge; ${row.min_grosir}: Rp${Number(row.harga_grosir).toLocaleString('id-ID')}
                                </div>`;
                    }
                    return out;
                }
            },
            {
                "data": "satuan_barang"
            },
            {
                "data": "stok"
            },
            <?php if (!empty(in_array($_SESSION['codekop_session']['akses'], [1, 6]))) { ?> {
                "data": "id",
                "render": function(data, type, row, meta) {
                    var btnClass = row.stok > 5 ? "btn-sm" : "btn-sm mt-2";
                    return `<a href="edit.php?id=${row.id}"
                                    class="btn btn-primary ${btnClass}"><i class="fa fa-edit"></i></a>
                                <button type="button" onclick="bukaModalStok(${row.id}, '${row.nama_barang.replace(/'/g, "\\'")}', ${row.stok})"
                                    class="btn btn-warning ${btnClass}" title="Sesuaikan Stok"><i class="fa fa-boxes"></i></button>
                                <a href="proses.php?aksi=delete&id=${row.id}" onclick="javascript:return confirm('Data ingin dihapus ?');"
                                    class="btn btn-danger ${btnClass}"><i class="fa fa-trash"></i></a>`;
                }
            },
            <?php }?>
        ],
    });
});
</script>